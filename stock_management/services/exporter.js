const common = require('../lib/common');
var Sequelize = require("sequelize");
const {Transaction} = require('../models/orm');
const logs = require('../lib/log');
const config = require('../config');
const db = require('../lib/db');
let contract = require('../models/contract');
let optionABI = require('../abi/stockOption.js');
let shareABI = require('../abi/stockShare.js');

var exporter = function(web3){
    var self = this;
    self.Transaction = Transaction;
    self.web3 = web3;

    self.processLog = function(log, type) {
        logs.debug("log: "+JSON.stringify(log, null, 2));
        self.web3.eth.getBlock(log.blockNumber, false, function(err, block) {
            if (err) {
              logs.error("Error retrieving block information for log:", err);
              return;
            }
            logs.debug("type: "+type);
            var subtype;
            let data = {
                from : null,
                to : null,
                value : null,
            };

            if (log.event === "Transfer"){
                subtype = 1;
                data.from = log.returnValues.from;
                data.to = log.returnValues.to;
                data.value = log.returnValues.value;
            } else if (log.event === "Approval"){
                data.from = log.returnValues.owner;
                data.to = log.returnValues.spender;
                data.value = log.returnValues.value;
                subtype = 6;
            } else if (log.event === "Destroy"){
                data.from = log.returnValues.account;
                data.to = config.stockContract.ownerAddress;
                data.value = log.returnValues.value;
                subtype=2;
            } else if (log.event === "Distribute"){
                data.to = log.returnValues.account;
                data.from = config.stockContract.ownerAddress;
                data.value = log.returnValues.value;
                subtype=3;
            } else if (log.event === "Unlock"){
                data.from = log.returnValues.account;
                data.to = log.returnValues.account;
                data.value = log.returnValues.value;
                subtype=4;
            } else if (log.event === "ExchangeShare"){
                data.from = log.returnValues.account;
                data.to = null;
                data.value = log.returnValues.value;
                subtype=5;
            } else {
              return;
            }


            self.Transaction.findOrCreate({
              where:{
                  txHash: log.transactionHash,
                  from : data.from.toLowerCase(),
                  to : data.to.toLowerCase()
              },
              defaults:{
                  from : data.from.toLowerCase(),
                  to : data.to.toLowerCase(),
                  value : data.value,
                  txHash : log.transactionHash,
                  type : type,
                  subtype : subtype,
                  time : block.timestamp*1000
              }
            }).spread((transaction,created) => {

            });
        });
    };


    let listenBlocks = async function() {

        while (1) {
            let blockNumber = await self.web3.eth.getBlockNumber();
            let blockNumberInDB = (await db.getRows("SELECT value FROM configs WHERE name = 'ethPublicBN'"))[0].value;
            if (blockNumber > blockNumberInDB) {
                logs.debug("[Reading Ether] Searching for transactions within blocks " + blockNumberInDB + " and " + blockNumber);
                let start = 0;
                if(blockNumberInDB-5>=0) start = blockNumberInDB-5;

                let addresses = await contract.getContractAddress();
                addresses.data.records.forEach(function(item){
                    if (item.symbol === 'NSS'){
                        let shareContract = new self.web3.eth.Contract(shareABI, item.contract_address);
                        shareContract.getPastEvents("allEvents", {
                            fromBlock: start,
                            toBlock: blockNumber
                        }, function (error, events) {
                            //console.log(events);
                            for(event of events){
                                self.processLog(event,  item.period);
                            }
                        });
                    } else {
                        let optionContract = new self.web3.eth.Contract(optionABI, item.contract_address);
                        optionContract.getPastEvents("allEvents", {
                            fromBlock: start,
                            toBlock: blockNumber
                        }, function (error, events) {
                            //console.log(events);
                            for(event of events){
                                self.processLog(event,  item.period);
                            }
                        });
                    }
                });
                await db.query("update configs set value=? WHERE name = 'ethPublicBN' and value < ?", [blockNumber, blockNumber]);
            }
            logs.info("Reached chain head: " + blockNumber);

            listenDistribute();
            listenUnlock();
            
            await common.sleep(10000);
        }
    };


    let listenDistribute = async function(){
        let records = await db.getRows("SELECT `txHash` FROM `option_expected_batch` WHERE `status`=0 and `txHash` is not null");

        records.forEach(async function(item){
            var receipt = await self.web3.eth.getTransactionReceipt(item.txHash);

            if (receipt.status){
                await db.query("update option_expected_batch set status=1 where txHash=?", [item.txHash]);
            }
        });
    }

    let listenUnlock = async function(){
        await db.query("update option_unlock_remind t1 set status=1 where exists(select 1 from transactions t2 where t1.txHash=t2.txHash) and t1.status=0", []);
    }

    listenBlocks();
    console.log("Exporter initialized, waiting for new events...");
};

module.exports = exporter;
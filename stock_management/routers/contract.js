var url = require('url');
let contract = require('../models/contract');

let web3 = require('../lib/web3');
let solc = require('solc')
let fs = require('fs');
let config = require('../config');

let register = (router) => {
    //写入已发布合约
    router.post('/contract/new', async function (req, res) {
        try {
            // let contract_address = req.body.contract_address;
            // let re = /0x[0-9a-f]{40}/g;
            // if(!re.test(contract_address)) {
            //     throw new Error("Contract address is not right!");
            // }
            let symbol = req.body.symbol;
            let supply = req.body.supply;
            let open_time = new Date(Date.parse(req.body.open_time))/1000;
            let expire_time = new Date(Date.parse(req.body.expire_time))/1000;
            let price = req.body.price;
            let period = req.body.period;

            let contract_address = await deploy(symbol,supply,open_time,expire_time,price);
            console.log("contract_address:",contract_address);

            res.end(JSON.stringify(await contract.insertContract(symbol, supply, contract_address, price, open_time, expire_time, period)))
        } catch (e) {
            res.end(JSON.stringify({status:1,error:e.message}))
        }
    });

    router.get('/contract/getTokenTransactions', async function (req, res) {
        try {
            let gets = url.parse(req.url, true).query;

            let parameter = gets.parameter || null;
            let subtype = gets.type || null;
            let type = gets.period;
            let offset = gets.offset;
            let limit = gets.limit;

            res.end(JSON.stringify(await contract.getTransactions(parameter, subtype, type, offset, limit)))
        } catch (e) {
            res.end(JSON.stringify({status:1,error:e.message}))
        }
    });

    router.get('/contract/getContractAddress', async function (req, res) {
        try {
            let gets = url.parse(req.url, true).query;

            let symbol = gets.symbol;
            let offset = gets.offset;
            let limit = gets.limit;

            res.end(JSON.stringify(await contract.getContractAddress(symbol, offset, limit)))
        } catch (e) {
            res.end(JSON.stringify({status:1,error:e.message}))
        }
    });

    router.get('/contract/getContractById', async function (req, res) {
        try {
            let gets = url.parse(req.url, true).query;
            let id = gets.id;

            res.end(JSON.stringify(await contract.getContractById(id)))
        } catch (e) {
            res.end(JSON.stringify({status:1,error:e.message}))
        }
    });

    router.get('/contract/getAddresses', async function (req, res) {
        try {
            res.end(JSON.stringify(await contract.getAddress()))
        } catch (e) {
            res.end(JSON.stringify({status:1,error:e.message}))
        }
    });

    router.get('/contract/getAddressesInternal', async function (req, res) {
        try {
            res.end(JSON.stringify(await contract.getAddress()))
        } catch (e) {
            res.end(JSON.stringify({status:1,error:e.message}))
        }
    });

    router.get('/contract/getContractTime', async function (req, res) {
        try {
            let gets = url.parse(req.url, true).query;

            let symbol = gets.symbol;

            res.end(JSON.stringify({status:0,data:await contract.getContractTime(symbol)}))
        } catch (e) {
            res.end(JSON.stringify({status:1,error:e.message}))
        }
    });


    
    const contractCode = fs.readFileSync(__dirname+'/../contract/StockOptionToken.sol').toString();
    const compileCode = solc.compile({sources: {main: contractCode}}, 1);
    const abi = JSON.parse(compileCode.contracts['main:StockOptionToken'].interface);
    const byteCode = '0x'+compileCode.contracts['main:StockOptionToken'].bytecode;
    const gasEstimate = compileCode.contracts['main:StockOptionToken'].gasEstimate;
    var tokenContract = new web3.eth.Contract(abi, {data: byteCode});
    

    let deploy = async(symbol,supply,open,expire,price) => {
        return new Promise((resolve, reject) => {
            tokenContract.deploy({arguments:[symbol,supply,open,expire,config.stockContract.address,price]})
                    .send({from:config.stockContract.ownerAddress,gas:3455117})
                    .on('transactionHash', function(transactionHash){
                        console.log("deploy transaction hash: ", transactionHash)
                    })
                    .on('receipt', function(receipt){
                        console.log("deploy receipt: ", receipt)
                        resolve(receipt.contractAddress);
                    })
                    .on('confirmation', function(confirmationNum, receipt){
                        console.log("got confirmations number: ", confirmationNum)
                    })
                    .then(async function(myContactInstance){
                        console.log("deployed successfully.")
                    })
                    .catch(err => {
                        console.log("Error: failed to deploy, detail:", err)
                    });
        })
    }


};
module.exports = {register};
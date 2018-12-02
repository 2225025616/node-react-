let url = require('url');
let option = require('../models/stockOption');
const log = require('../lib/log');

let register = (router) => {

    router.post('/option/transfer', async function (req, res) {
        try {
            let from = req.body.from;
            let to = req.body.to;
            let amount = req.body.amount;
            let txHash = req.body.txHash;
            let period = req.body.period;
            res.end(JSON.stringify(await option.transfer(from, to, amount, txHash, period)))
        } catch (e) {
            res.end(JSON.stringify({status:1,error:e.message}))
        }
    });

    router.post('/option/exchangeShare', async function (req, res) {
        try {
            let account = req.body.account;
            let amount = req.body.amount;
            let txHash = req.body.txHash;
            let period = req.body.period;
            res.end(JSON.stringify(await option.exchangeShare(account, amount, txHash, period)))
        } catch (e) {
            res.end(JSON.stringify({status:1,error:e.message}))
        }
    });

    router.post('/option/distributeOption', async function (req, res) {
        try {
            let addresses = req.body.addresses;
            let amount = req.body.amount;
            let txHash = req.body.txHash;
            let period = req.body.period;
            res.end(JSON.stringify(await option.distributeOption(addresses, amount, txHash,period)))
        } catch (e) {
            res.end(JSON.stringify({status:1,error:e.message}))
        }
    });

    router.post('/option/unlockOption', async function (req, res) {
        try {
            let period = req.body.period;
            //let symbol = req.body.symbol;
            let addresses = req.body.addresses;
            //let ratio = req.body.ratio;
            let amounts = req.body.amounts;
            let txHash = req.body.txHash;
            res.end(JSON.stringify(await option.unlockOption(addresses,amounts,txHash,period)))
        } catch (e) {
            res.end(JSON.stringify({status:1,error:e.message}))
        }
    });

    router.post('/option/updateOptionLockedRatio', async function (req, res) {
        try {
            let symbol = req.body.symbol;
            let ratio = req.body.ratio;
            res.end(JSON.stringify(await option.updateOptionLockedRatio(symbol,ratio)))
        } catch (e) {
            res.end(JSON.stringify({status:1,error:e.message}))
        }
    });

    router.post('/option/reclaimOption', async function (req, res) {
        try {
            let addresses = req.body.addresses;
            let amounts = req.body.amounts;
            let txHash = req.body.txHash;
            let period = req.body.period;
            res.end(JSON.stringify(await option.destroyOption(addresses, amounts, txHash, period)))
        } catch (e) {
            res.end(JSON.stringify({status:1,error:e.message}))
        }
    });

    router.get('/option/lockedRatio', async function (req, res) {
        try {
            let gets = url.parse(req.url, true).query;
            let symbol = gets.symbol;
            res.end(JSON.stringify(await option.lockedRatio(symbol)))
        } catch (e) {
            res.end(JSON.stringify({status:1,error:e.message}))
        }
    });

    router.get('/option/getExpectedBatch', async function (req, res) {
        try {
            let gets = url.parse(req.url, true).query;
            let optionID = gets.id;
            res.end(JSON.stringify(await option.getExpectedBatch(optionID)))
        } catch (e) {
            res.end(JSON.stringify({status:1,error:e.message}))
        }
    });

    router.get('/option/getExpectedBatchById', async function (req, res) {
        try {
            let gets = url.parse(req.url, true).query;
            let id = gets.id;
            res.end(JSON.stringify(await option.getExpectedBatchById(id)))
        } catch (e) {
            res.end(JSON.stringify({status:1,error:e.message}))
        }
    });

    router.get('/option/getExpectedList', async function (req, res) {
        try {
            let gets = url.parse(req.url, true).query;
            let batchID = gets.id;
            
            res.end(JSON.stringify(await option.getExpectedList(batchID)))
        } catch (e) {
            res.end(JSON.stringify({status:1,error:e.message}))
        }
    });

    router.post('/option/addExpectedBatch', async function (req, res) {
        try {
            let optionID = req.body.option_id;
            let name = req.body.name;
            res.end(JSON.stringify(await option.addExpectedBatch(optionID, name)))
        } catch (e) {
            res.end(JSON.stringify({status:1,error:e.message}))
        }
    });

    router.post('/option/modifyExpectedBatchStatus', async function (req, res) {
        try {
            let id = req.body.id;
            let txHash = req.body.txHash;
            res.end(JSON.stringify(await option.modifyExpectedBatchTxHash(txHash,id)))
        } catch (e) {
            res.end(JSON.stringify({status:1,error:e.message}))
        }
    });

    router.post('/option/modifyExpectedBatch', async function (req, res) {
        try {
            let id = req.body.id;
            let name = req.body.name;
            res.end(JSON.stringify(await option.modifyExpectedBatch(id,name)))
        } catch (e) {
            res.end(JSON.stringify({status:1,error:e.message}))
        }
    });

    router.post('/option/addExpectedList', async function (req, res) {
        try {
            let userIDs = req.body.user_id;
            let batchID = req.body.batch_id;
            res.end(JSON.stringify(await option.addExpectedList(userIDs,batchID)))
        } catch (e) {
            res.end(JSON.stringify({status:1,error:e.message}))
        }
    });

    router.post('/option/delExpectedList', async function (req, res) {
        try {
            let id = req.body.id;
            res.end(JSON.stringify(await option.delExpectedList(id)))
        } catch (e) {
            res.end(JSON.stringify({status:1,error:e.message}))
        }
    });

    router.post('/option/modifyExpectedList', async function (req, res) {
        try {
            let id = req.body.id;
            let value = req.body.value;
            res.end(JSON.stringify(await option.modifyExpectedList(id,value)))
        } catch (e) {
            res.end(JSON.stringify({status:1,error:e.message}))
        }
    });


    router.post('/option/unlockRemind', async function (req, res) {
        try {
            let id = req.body.option_id;
            let dates = req.body.date;
            let values = req.body.value;

            res.end(JSON.stringify(await option.unlockRemind(id,dates,values)));
        } catch (e) {
            res.end(JSON.stringify({status:1,error:e.message}))
        }
    });

    router.get('/option/getUnlockRemind', async function (req, res) {
        try {
            let gets = url.parse(req.url, true).query;
            let id = gets.option_id;
            
            res.end(JSON.stringify(await option.getUnlockRemind(id)))
        } catch (e) {
            res.end(JSON.stringify({status:1,error:e.message}))
        }
    });

    router.post('/option/updateUnlockRemind', async function (req, res) {
        try {
            let id = req.body.id;
            let txHash = req.body.txHash;

            res.end(JSON.stringify(await option.updateUnlockRemind(id,txHash)));
        } catch (e) {
            res.end(JSON.stringify({status:1,error:e.message}))
        }
    });
};
module.exports = {register};

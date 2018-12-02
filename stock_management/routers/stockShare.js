let share = require('../models/stockShare');


let register = (router) => {

    router.post('/share/transfer', async function (req, res) {
        try {
            let from = req.body.from;
            let txHash = req.body.txHash;
            let to = req.body.to;
            let amount = req.body.amount;
            res.end(JSON.stringify(await share.transfer(from, to, amount, txHash)));
        } catch (e) {
            res.end(JSON.stringify({status:1,error:e.message}))
        }
    });
};
module.exports = {register};

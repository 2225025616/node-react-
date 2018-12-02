const config = require("../config");
//const log = require('../lib/log');
const db = require('../lib/db');

let transfer = async (from, to, amount, txHash) => {
    let record = db.query("insert into transactions(`fromAddress`,`toAddress`,`amount`, `transHash`,`type`,`subtype`) values (?,?,?,?,?,?)",[from,to,amount,txHash,0,1]);
    if (record < 1) {
        return {
            status: 2001,
            error: 'db: share transactions insert error,transHash = ' + txHash + ' fromAddress = ' + from + ', toAddress = ' + to,
        }
    }
    return {
        status: 0,
        data: true
    }
};

module.exports = {
    transfer,
};
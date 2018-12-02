const config = require('../config');
const db = require('../lib/db');
//const BigNumber = require('bignumber.js');
const log = require('../lib/log');

function timeConverter(UNIX_timestamp){
    let a = new Date(UNIX_timestamp);
    //let months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    let year = a.getFullYear();
    //let month = months[a.getMonth()];
    let month = a.getMonth();
    let date = a.getDate();
    let hour = a.getHours();
    let min = a.getMinutes();
    let sec = a.getSeconds();
    return year + '-' + month + '-' + date + ' ' + hour + ':' + min + ':' + sec ;
}

//转账交易
let transfer = async (from, to, value, txHash, period) => {
    //let type = parseInt(symbol.substring(symbol.length-1, symbol.length));
    let record = await db.query("insert into transactions(`from`,`to`,`value`, `txHash`,`type`,`subtype`,`time`) values (?,?,?,?,?,?,FROM_UNIXTIME(?))",[from,to,value,txHash,period,1,Math.round((new Date()).getTime() / 1000)]);
    if (record < 1) {
        return {
            status: 2001,
            error: 'db: option transactions insert error,transHash = ' + txHash + ' fromAddress = ' + from + ', toAddress = ' + to,
        }
    }
    return {
        status: 0,
        data: true
    }
};

let exchangeShare = async (account, amount, txHash, period) => {
    let record1 = await db.query("insert into transactions(`from`,`to`,`value`, `txHash`,`type`,`subtype`,`time`) values (?,?,?,?,?,?,FROM_UNIXTIME(?))",[account,0,amount,txHash,period,5,Math.round((new Date()).getTime() / 1000)]);
    let record2 = await db.query("insert into transactions(`from`,`to`,`value`, `txHash`,`type`,`subtype`,`time`) values (?,?,?,?,?,?,FROM_UNIXTIME(?))",[0,account,amount,txHash,0,5,Math.round((new Date()).getTime() / 1000)]);
    if (record1 + record2 < 2) {
        return {
            status: 2001,
            error: 'db: option exchange  insert error,transHash = ' + txHash + ' address = ' + account,
        }
    }
    return {
        status: 0,
        data: true
    }
};

//发行期权
let distributeOption = async (addresses, amount, txHash,period) => {
    let values=[];
    let time=timeConverter(Math.round((new Date()).getTime()));
    for(let i=0; i<addresses.length; i++){
        values.push([config.stockContract.ownerAddress,addresses[i],amount,txHash,period,3,time]);
    }
    //log.debug(JSON.stringify([values],null,2));
    let record = await db.query("insert into transactions (`from`,`to`,`value`, `txHash`,`type`,`subtype`,`time`) values ?", [values]);
    if (record < 1) {
        return {
            status: 2001,
            error: 'db:distribute transactions insert error,transHash = ' + txHash + ', period = ' + period,
        }
    }

    return {
        status: 0,
        data: true
    }
};

//解锁期权
let unlockOption = async (addresses,amounts,txHash,period) => {
    //let unlocked = (await db.getRows("SELECT `freeze` FROM `stock_option` WHERE `symbol` = ?", [symbol]))[0].freeze;
    //if(unlocked - ratio > 100) throw new Error("ratio is too large");
    let values=[];
    let time=timeConverter(Math.round((new Date()).getTime()));
    for(let i=0; i<addresses.length; i++){
        values.push([addresses[i],addresses[i],amounts[i],txHash,period,4,time]);
    }
    //log.debug(JSON.stringify([values],null,2));
    let record = await db.query("insert into transactions(`from`,`to`,`value`, `txHash`,`type`,`subtype`,`time`) values ?", [values]);
    //db.query("UPDATE `stock_option` SET `freeze` = ? WHERE `symbol` = ?", [unlocked-ratio, symbol]);
    if (record < 1) {
        return {
            status: 2001,
            error: 'db:unlock transactions insert error,transHash = ' + txHash + ', period = ' + period,
        }
    }
    return {
        status:0,
        data: true
    }
};

let updateOptionLockedRatio = async (symbol,ratio) => {
    let unlocked = (await db.getRows("SELECT `freeze` FROM `stock_option` WHERE `symbol` = ?", [symbol]))[0].freeze;
    if(unlocked - ratio > 100) throw new Error("ratio is too large");
    //let record = await db.query("insert into transactions(`from`,`to`,`value`, `txHash`,`type`,`subtype`,`time`) values (?,?,?,?,?,?,FROM_UNIXTIME(?))", [address,address,amount,txHash,period,4,Math.round((new Date()).getTime() / 1000)]);
    let record = await db.query("UPDATE `stock_option` SET `freeze` = ? WHERE `symbol` = ?", [unlocked-ratio, symbol]);
    if (record < 1) {
        return {
            status: 2001,
            error: 'db:Locked Ratio in DB update error,symbol = ' + symbol + ', ratio = ' + ratio,
        }
    }
    return {
        status:0,
        data: true
    }
};

//回收期权
let destroyOption = async (addresses, amounts, txHash, period) => {
    let values=[];
    let time=timeConverter(Math.round((new Date()).getTime()));
    for(let i=0; i<addresses.length; i++){
        values.push([addresses[i],config.stockContract.ownerAddress,amounts[i],txHash,period,2,time]);
    }
    let record = await db.query("insert into transactions(`from`,`to`,`value`, `txHash`,`type`,`subtype`,`time`) values ?", [values]);
    if (record < 1) {
        return {
            status: 2001,
            error: 'db:reclaim transactions insert error,transHash = ' + txHash + ', period = ' + period,
        }
    }
    return {
        status:0,
        data: true
    }
};

let lockedRatio = async (symbol) => {
    //log.debug('symbol: '+symbol);
    let lockedRatio = await db.getRows("SELECT `freeze` FROM `stock_option` WHERE `symbol` = ?", [symbol]);
    //log.debug('lockedRatio: '+lockedRatio);
    if(lockedRatio.length === 0){
        return {
            status:1,
            error: "cannot find symbol"
        }
    }
    return {
        status:0,
        lockedRatio: lockedRatio[0].freeze
    }
};

//预发放批次
let getExpectedBatch = async (id) => {
    let dataList = await db.getRows("select t1.id,t1.name,t1.status,t1.txHash,t1.createdAt,ifnull(t2.total,0) total from option_expected_batch t1"+ 
    " left join (select batch_id,sum(value) total from option_expected_list group by batch_id) t2 "+
    " on t1.id = t2.batch_id where t1.option_id=? order by t1.createdAt desc",[id]);

    return {
        status:0,
        data: dataList
    }
}

//预发放名单
let getExpectedTotal = async (batchId) => {
    let total = await db.getRows("select sum(t1.`value`) value from `option_expected_list` t1  where t1.batch_id=?",[batchId]);
    return total;
}


let getExpectedList = async (batchId) => {
    let dataList = await db.getRows("select t1.id,t1.user_id,ifnull(t1.`value`,0) value,t2.name,t2.phone_number,t2.id_card,t2.address from `option_expected_list` t1"+
    " left join `user` t2 on t1.user_id = t2.id where t1.batch_id=?",[batchId]);
    let total = await getExpectedTotal(batchId);

    return {
        status:0,
        data: dataList,
        total: total
    }
}

//创建批次
let addExpectedBatch = async(optionID,name) => {
    let record = await db.query("insert into option_expected_batch(`option_id`,`name`) values (?,?)", [optionID,name]);
    if (record < 1) {
        return {
            status: 2002,
            error: 'add expected batch faild',
        }
    }
    return {
        status:0,
        data: true
    }
}

let getExpectedBatchById = async(id) => {
    let row = await db.getRows("select t1.*,t2.contract_address from option_expected_batch t1 left join stock_option t2 on t1.option_id=t2.id where t1.id=?",[id]);
    return {
        status:0,
        data: row
    }
}

//修改发放批次状态
let modifyExpectedBatchStatus = async(id) => {
    let record = await db.query("update option_expected_batch set status=1 where id = ? and status=0", [id]);
    if (record < 1) {
        return {
            status: 2003,
            error: 'update expected batch status faild',
        }
    }
    return {
        status:0,
        data: true
    }
}

let modifyExpectedBatchTxHash = async(txHash,id) => {
    let record = await db.query("update option_expected_batch set txHash=? where id = ? and status=0", [txHash,id]);
    if (record < 1) {
        return {
            status: 2003,
            error: 'update expected batch txHash faild',
        }
    }
    return {
        status:0,
        data: true
    }
}

//修改发放批次状态
let modifyExpectedBatch = async(id,name) => {
    let record = await db.query("update option_expected_batch set name=? where id = ?", [name,id]);
    if (record < 1) {
        return {
            status: 2004,
            error: 'add expected batch faild',
        }
    }
    return {
        status:0,
        data: true
    }
}

//添加预发放名单
let addExpectedList = async(userIDs,batchID) => {
    try{

        for (let i =0;i<userIDs.length;i++){
            console.log('userID:',userIDs[i])
            await db.query("insert into option_expected_list(`user_id`,`batch_id`) values (?,?)", [userIDs[i],batchID]);
        }

        return {
            status:0,
            data: true
        }
    } catch (e) {
        return {
            status:1,
            error: 'user already exists the name`s list '
        }
    }
}

//从预发放名单中删除
let delExpectedList = async(id) => {
    let record = await db.query("delete from option_expected_list where id = ?", [id]);
    if (record < 1) {
        return {
            status: 2006,
            error: 'delete expected name`s list faild',
        }
    }
    return {
        status:0,
        data: true
    }
}

let modifyExpectedList = async(id,value) => {
    let record = await db.query("update option_expected_list set value=? where id = ?", [value,id]);
    if (record < 1) {
        return {
            status: 2007,
            error: 'modify expected name`s list faild',
        }
    }
    return {
        status:0,
        data: true
    }
}

let unlockRemind = async(id,dates,values) => {
    if (typeof dates === "object" && dates.length != values.length){
        return {
            status:1,
            error: 'Not available paramter'
        }
    }

    if (typeof dates === "object"){
        for(let i = 0; i < dates.length ; i++){
            await db.query("insert into option_unlock_remind(option_id,datetime,value) values(?,?,?)", [id,dates[i],values[i]]);
        }
    } else {
        await db.query("insert into option_unlock_remind(option_id,datetime,value) values(?,?,?)", [id,dates,values]);
    }

    return {
        status:0,
        data: true
    }
    
}

let getUnlockRemind = async(id) => {
    let rows = await db.getRows("select t1.* from  option_unlock_remind t1 where t1.option_id=? order by t1.datetime asc",[id]);
    return {
        status:0,
        data: rows
    }
}

let updateUnlockRemind = async(id,txHash) => {
    let record = await db.query("update option_unlock_remind set txHash=? where id=?", [txHash,id]);
    return {
        status:0,
        data: true
    }
}


module.exports = {
    transfer, 
    exchangeShare, 
    distributeOption, 
    unlockOption, 
    destroyOption, 
    lockedRatio, 
    updateOptionLockedRatio,
    getExpectedBatch,
    getExpectedList,
    getExpectedTotal,
    addExpectedBatch,
    modifyExpectedBatchStatus,
    modifyExpectedBatchTxHash,
    modifyExpectedBatch,
    addExpectedList,
    delExpectedList,
    modifyExpectedList,
    getExpectedBatchById,
    unlockRemind,
    getUnlockRemind,
    updateUnlockRemind
};
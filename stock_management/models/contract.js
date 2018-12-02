const db = require('../lib/db');
// const share = require('./stockShare');
const log = require('../lib/log');
const {User,Transaction} = require('./orm');
const Sequelize = require('sequelize');
const Op = Sequelize.prototype.Op;

let getContractAddress = async (symbol, offset, limit) => {
    let rows;
    let rowsCount;
    if (symbol === "" || symbol === undefined || symbol === null){
        rowsCount = (await db.getRows("SELECT COUNT(*) AS count FROM stock_option", []))[0].count;
        if(limit === undefined || limit === null) limit = rowsCount; offset = 0;
        rows = await db.getRows("SELECT t1.*,ifnull(t2.total,0) total FROM stock_option t1 left join (select type,sum(value) total from transactions where subtype=3 and type >0 group by type) t2 on t1.period=t2.type LIMIT ?,?", [JSON.parse(offset), JSON.parse(limit)]);
    } else {
        rowsCount = (await db.getRows("SELECT COUNT(*) AS count FROM stock_option WHERE symbol = ?", [symbol]))[0].count;
        if(limit === undefined || limit === null) limit = rowsCount; offset = 0;
        rows = await db.getRows("SELECT t1.*,ifnull(t2.total,0) total FROM stock_option t1 left join (select type,sum(value) total from transactions where subtype=3 and type >0 group by type) t2 on t1.period=t2.type WHERE t1.symbol = ? LIMIT ?,?", [symbol, JSON.parse(offset), JSON.parse(limit)]);
    }
    if (rows === undefined || rows.length === 0) {
        throw new Error("symbol not exist:"+symbol);
    }
    return {
        status: 0,
        data: {
            totalCount: rowsCount,
            page: Math.ceil(offset / limit) + 1,
            totalPage: Math.ceil(rowsCount / limit),
            records: rows
        }
    };
};


let getContractById = async(id) => {
    let res = await db.getRows("SELECT t1.*,t2.total FROM stock_option t1 left join (select type,sum(value) total from transactions where subtype=3 and type >0 group by type) t2 on t1.period=t2.type where t1.id=?", [id]);
    return {
        status: 0,
        data: res
    };
}

let getAddress = async () => {

    let res = await db.getRows("SELECT `contract_address`, `period` FROM stock_option WHERE `period` > 0", []);

    // let data = [];
    // for(let i=0; i<res.length; i++){
    //     data.push(res[i].contract_address)
    // }
    return {
        status: 0,
        data: res
    };
};

let getContractTime = async (symbol) => {
    let rows;
    if (symbol === "" || symbol === undefined || symbol === null){
        rows = await db.getRows("SELECT `symbol`, `open_time`, `expire_time` FROM stock_option", []);
    } else {
        rows = await db.getRows("SELECT `symbol`, `open_time`, `expire_time` FROM stock_option WHERE symbol = ?", [symbol]);
    }
    if (rows === undefined || rows.length === 0) {
        throw new Error("symbol not exist:"+symbol);
    }
    return rows;
};

//在数据库中写入已发布合约
async function insertContract(symbol, supply, contract_address, price, open_time, expire_time, period){
    let rows = await db.query("INSERT INTO stock_option (`symbol`,`supply`,`contract_address`,`price`,`open_time`,`expire_time`,`createdAt`, `period`) VALUES (?,?,?,?,FROM_UNIXTIME(?),FROM_UNIXTIME(?),FROM_UNIXTIME(?),?)", [symbol, supply, contract_address, price, open_time, expire_time, Math.round((new Date()).getTime() / 1000), period]);
    if (rows !== 1) {
        return {
            status:1,
            error:'INSERT into DB error',
        };
    }
    return {
        status: 0,
        data: true
    }
}

//期权开放，更新db中的合约状态为1
async function openOption(contractAddress){
    let rows = await db.query("UPDATE stock_option SET status = 1 WHERE contract_address = ?", [contractAddress]);
    if (rows !== 1) {
        return {
            status:1,
            error:'UPDATE DB error, more than one or zero contract with the contract address: '+contractAddress,
        };
    }
    return {
        status: 0,
        data: contractAddress+" opened"
    };
}


let getCount = async(params) => {

	// let params = {
    //     where : {}
    // };
	
	// if(!(type === null || type === undefined)){
    //     params['where']['type'] = type;
    // }

    // if(!(subtype === null || subtype === undefined)){
    //     params['where']['subtype'] = subtype;
    // }

	return new Promise((resolve, reject) =>{
		Transaction.count(params).then(count => {
			resolve({
		        status: 0,
		        data: count
		    })
		});
	});
};

async function getTransactions(parameter, subtype, type, offset, limit){

    let params = {
        where : {}
    };

    params['include'] = [{
        model : User,
        as: 'fromUser'
    },{
        model : User,
        as : 'toUser'
    }];

    if(!(parameter === null || parameter === undefined)){
        params = {
            where : {
                [Op.or] : [
                    {from : parameter},
                    {to : parameter},
                    {txHash : parameter}
                ]
            }
        };
    }

    if(!(type === null || type === undefined)){
        params['where']['type'] = type;
    }

    if(!(subtype === null || subtype === undefined)){
        params['where']['subtype'] = subtype;
    }

    let rowNumber = (await getCount(params)).data;

    if(!(limit === undefined && limit === null)) {
        params['offset'] = Number(offset);
        params['limit'] = Number(limit);
	}

    params['order'] = [
        ['time','DESC']
    ];

    let results = await Transaction.findAll(params);

    return {
        status: 0,
        data: {
            totalCount: rowNumber,
            page: Math.ceil(offset / limit) + 1,
            totalPage: Math.ceil(rowNumber / limit),
            records: results
        }
    };
}

module.exports = {insertContract, openOption, getContractAddress, getContractTime, getTransactions, getAddress,getContractById};

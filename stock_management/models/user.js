const config = require('../config');
const Sequelize = require('sequelize');
const log = require('../lib/log');
const sd = require('silly-datetime');
const Op = Sequelize.prototype.Op;
const xlsx = require("node-xlsx");
const db = require('../lib/db');
const crypto = require('../lib/crypto');
const {User,Admin} = require('./orm');
// let user = User.sync({ force: false});

//分页查询 
let getUserList = async(parameter,offset,limit) => {
    let count = (await getCount(parameter)).data;
    let params = {};

    if(limit === undefined || limit === null) {
        params['offset'] = 0;
        params['limit'] = count;
    } else {
        params['offset'] = offset;
        params['limit'] = limit;
	}

	if (parameter){
		params['where'] = {
			status: 0,
			[Op.or]:[
				{
					name:{
						[Op.like]:'%'+parameter+'%'
					}
				},
				{phone_number: parameter},
				{id_card: parameter},
				{address: parameter},
				{department: parameter}
			]
		}
	} else{
		params['where'] = {status: 0};
	}
    let data = await User.findAll(params);

	return {
		status: 0,
		data: {
            records: data,
            totalCount: count,
            page: Math.ceil(offset / limit) + 1,
            totalPage: Math.ceil(count / limit)
        }
	}
};

let getUserAccounts = async(symbol) => {
	
	let data = await db.getRows("SELECT `address` from user t1 where exists (select 1 from option_expected_list t2 where t1.id=t2.user_id and t2.batch_id in (select id from option_expected_batch where option_id=(select id from stock_option where symbol=?)))", [symbol]);

    return {
        status: 0,
        data: data,
    }
};

//查询总量
let getCount = async(parameter) => {

	let params = {};
	
	if (parameter){
		params['where'] = {
			status: 0,
			[Op.or]:[
				{
					name:{
						[Op.like]:'%'+parameter+'%'
					}
				},
				{phone_number: parameter},
				{id_card: parameter},
				{address: parameter},
				{department: parameter}
			]
		}
	} else{
		params['where'] = {status: 0};
	}

	return new Promise((resolve, reject) =>{
		User.count(params).then(count => {
			resolve({
		        status: 0,
		        data: count
		    })
		});
	});
};


//增加
let addUser = async(name,phone_number,id_card,address,department) => {
	return new Promise((resolve, reject) =>{
		User.findOrCreate({
			where:{
				[Op.or]:[
					{phone_number: phone_number},
					{id_card: id_card},
					{address: address}
				]
			},
			defaults:{
				name: name,
			    phone_number: phone_number,
			    id_card:id_card,
				address:address,
				department:department
			}	
		}).spread((user,created) => {
			 if (created){
			 	resolve({
			        status: 0,
			        data: true
			    })
			 } else {
			 	reject({
			        status: 1001,
			        error: 'user already exists'
			    })
			 }
		});
	});
}

let registerAdmin = async(name,phone_number,id_card,username,pw) => {
	return new Promise((resolve, reject) =>{
        let hashes = crypto.saltHashPassword(pw, username);
		Admin.findOrCreate({
			where:{
				[Op.or]:[
					{phone_number: phone_number},
					{id_card: id_card},
					{username: username}
				]
			},
			defaults:{
				name: name,
			    phone_number: phone_number,
			    id_card:id_card,
                username: username,
				pw:hashes.passwordHash,
				salt:hashes.nSalt,
				token:hashes.tokenHash
			}
		}).spread((admin,created) => {
			 if (created){
			 	resolve({
			        status: 0,
			        data: true
			    })
			 } else {
			 	reject({
			        status: 1001,
			        error: 'user already exists'
			    })
			 }
		});
	});
}

//修改
let modUser = async(id,name,phone_number,id_card,address,department) => {
	return new Promise((resolve, reject) =>{
		User.findOne({where:{id:id}}).then(user => {
			user.name = name;
			user.phone_number = phone_number;
			user.id_card = id_card;
			user.address = address;
			user.department = department;
			user.updatedAt = sd.format(new Date(), 'YYYY-MM-DD HH:mm:ss');
			user.save().then(() => {
				resolve({
			        status: 0,
			        data: true
			    })
			}).catch(error => {
				reject({
			        status: 1002,
			        error: error
			    })
			});
		});
	});
};

let changePw = async (id, pw, newPassword) => {
    // let adminRow = await db.getRows('SELECT * FROM `admin` WHERE `id` = ?', [id]);
    // if (adminRow.length === 0){
    //     return{
    //         status: 1,
    //         error: "id doesn't exist"
    //     };
    // }
    // let admin = adminRow[0];
    // if (admin.pw !== crypto.sha512(pw, admin.salt).passwordHash){
    //     return{
    //         status: 2,
    //         error: "Password incorrect"
    //     };
    // }
    // let newHashes = crypto.saltHashPassword(newPassword, username);

	return new Promise((resolve, reject) =>{
        Admin.findOne({where:{id:id}}).then(admin => {
            if (admin.pw !== crypto.sha512(pw, admin.salt).passwordHash){
                return{
                    status: 2,
                    error: "Password incorrect"
                };
            }
            let newHashes = crypto.saltHashPassword(newPassword, admin.username);
            admin.pw = newHashes.passwordHash;
            admin.salt = newHashes.nSalt;
            admin.token = newHashes.tokenHash;
            admin.updatedAt = sd.format(new Date(), 'YYYY-MM-DD HH:mm:ss');
            admin.save().then(() => {
				resolve({
			        status: 0,
			        data: {
			        	token: newHashes.tokenHash
					}
			    })
			}).catch(error => {
				reject({
			        status: 1002,
			        error: error
			    })
			});
		});
	});
};

let login = async (username, password) => {
    //crypto.saltHashPassword(password, username);
	let adminRow = await db.getRows('SELECT * FROM `admin` WHERE `username` = ?', [username]);
	if (adminRow.length === 0){
		return{
			status: 1,
			error: "Username doesn't exist"
		};
	}
	let admin = adminRow[0];
	if (admin.pw !== crypto.sha512(password, admin.salt).passwordHash){
		return{
			status: 2,
			error: "Password incorrect"
		};
	}
    let timeNow = Math.floor(Date.now() / 1000);
	admin.token = timeNow +'-'+ admin.token + '-' + crypto.sha512(admin.salt, timeNow.toString()).passwordHash;
	delete admin.pw;
    delete admin.salt;
    delete admin.username;
	return {
        status: 0,
        data: admin
	};
};

//伪删除
let delUser = async(id) => {

	return new Promise((resolve, reject) =>{
		User.findOne({where:{id:id}}).then(user => {
			user.update({
				status:1,
				updatedAt:sd.format(new Date(), 'YYYY-MM-DD HH:mm:ss')
			}).then(() => {
				resolve({
			        status: 0,
			        data: true
			    })
			}).catch(error => {
				reject({
			        status: 1003,
			        error: error
			    })
			})
		});
	});

}

//单个查询 
let getUser = async(id,address) => {
	return User.findOne({
		where:{
			[Op.or]:[
				{id: id},
				{address: address}
			]
		}
	});
}

let getName = async(address) => {
	let res = await User.findOne({
		where:{
            //address: address,
			[Op.or]:[
				{address: address}
			]
		},
        attributes: ['name']
	});
	if (res !== null) return {
		status: 0,
		data: res.name
	}; else return{
		status: 2,
		error: "no such address"
	}
}

//导入用户
let importUsers = async(filename) => {
	
	try {
		let xlsxData = xlsx.parse(filename); 

		if (xlsxData.length == 0){
			throw new Error('file is not available');
		}


		for(let key in xlsxData[0].data){

			if (key != 0){
				let item = xlsxData[0].data[key];
                let re = /0x[0-9a-f]{40}/g;
                if(!re.test(item[3].toLowerCase())) {
                    throw new Error("address is not right! " + item[3] +", at row "+ key);
                }
				await addUser(item[0],item[1],item[2],item[3].toLowerCase(),item[4]);
			}
		}

	} catch(err){
		throw err;
	}

	return {status: 0 , data: true };
}

module.exports = {
    getUserList,
    addUser,
    modUser,
    delUser,
    getUser,
    getName,
    getCount,
    importUsers,
	login,
    getUserAccounts,
    changePw,
    registerAdmin
};
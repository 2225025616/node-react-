const log = require('../lib/log');
const {getUserList,addUser,modUser,delUser,getUser,getName,getCount,importUsers,login,getUserAccounts,changePw,registerAdmin} = require('../models/user');
const url = require('url');
const formidable = require('formidable');

let register = (router) => {

	router.post('/user/create', async function(req, res){
        try{
            // let obj = url.parse(req.url, true).query;
            let name = req.body.name;
            let phone_number = req.body.phone_number;
            let id_card = req.body.id_card;
            let address = req.body.address.toLowerCase() || null;
            let department = req.body.department;

            if (address === null) throw new Error("Please provide a valid address");
            address = address.toLowerCase();
            let re = /0x[0-9a-f]{40}/g;
            if(!re.test(address)) {
                throw new Error("address is not right!");
            }
            res.end(JSON.stringify(await addUser(name,phone_number,id_card,address,department)));
        }
        catch (e) {
            res.end(JSON.stringify({status:1, error:e.message}))
        }
    });

    router.post('/user/modify', async function(req, res){
        try{
            // let obj = url.parse(req.url, true).query;
            let id = req.body.id;
            let name = req.body.name;
            let phone_number = req.body.phone_number;
            let id_card = req.body.id_card;
            let address = req.body.address.toLowerCase() || null;
            let department = req.body.department;
            if (address === null) throw new Error("Please provide a valid address");
            address = address.toLowerCase();
            let re = /0x[0-9a-f]{40}/g;
            if(!re.test(address)) {
                throw new Error("address is not right!");
            }
            res.end(JSON.stringify(await modUser(id,name,phone_number,id_card,address,department)));
        }
        catch (e) {
            res.end(JSON.stringify({status:1, error:e.message}))
        }
    });

    router.get('/user/getList', async function(req, res){
        try{
            let obj = url.parse(req.url, true).query;
            let parameter = obj.parameter;
            let offset = parseInt(obj.offset) || null;
            let limit = parseInt(obj.limit) || null;
            res.end(JSON.stringify(await getUserList(parameter,offset,limit)));
        }
        catch (e) {
            res.end(JSON.stringify({status:1, error:e.message}))
        }
    });

    router.get('/user/getAccounts', async function(req, res){
        try{
            res.end(JSON.stringify(await getUserAccounts()));
        }
        catch (e) {
            res.end(JSON.stringify({status:1, error:e.message}))
        }
    });

    router.get('/user/getAccountsInternal', async function(req, res){
        try{
            let obj = url.parse(req.url, true).query;
            let symbol = obj.symbol;
            res.end(JSON.stringify(await getUserAccounts(symbol)));
        }
        catch (e) {
            res.end(JSON.stringify({status:1, error:e.message}))
        }
    });

    router.get('/user/getCount', async function(req, res){
        try{
            let obj = url.parse(req.url, true).query;
            let parameter = obj.parameter;
            res.end(JSON.stringify(await getCount(parameter)));
        }
        catch (e) {
            res.end(JSON.stringify({status:1, error:e.message}))
        }
    });

    router.get('/user/login', async function(req, res){
        try{
            //let session = req.session;
            let obj = url.parse(req.url, true).query;
            let userName = obj.userName;
            let password = obj.password;
            let pw = "";
            pw += password;
            let result = await login(userName, pw);
            // if(result.status === 0){
            //     req.session.regenerate(function(err) {
            //         if(err){
            //             res.end(JSON.stringify({status:1, error:"登录失败"}));
            //         }
            //         req.session.loginUser = result.data;
            //         res.end(JSON.stringify(result));
            //     });
            // } else {
            //     res.end(JSON.stringify(result));
            // }
            res.writeHead(200, {'token': result.data.token});
            res.end(JSON.stringify(result));
        }
        catch (e) {
            res.end(JSON.stringify({status:1, error:e.message}))
        }
    });

    router.post('/user/changePw', async function(req, res){
        try{
            //let session = req.session;
            let id = req.body.id;
            let password = req.body.password;
            let newPassword = req.body.newPassword;
            let pw = "";
            pw += password;
            let result = await changePw(id, pw, newPassword);
            res.end(JSON.stringify(result));
        }
        catch (e) {
            res.end(JSON.stringify({status:1, error:e.message}))
        }
    });

    router.post('/user/registerAdmin', async function(req, res){
        try{
            let name = req.body.name;
            let phone_number = req.body.phone_number;
            let id_card = req.body.id_card;
            let username = req.body.username;
            let pw = req.body.password;
            res.end(JSON.stringify(await registerAdmin(name,phone_number,id_card,username,pw)));
        }
        catch (e) {
            res.end(JSON.stringify({status:1, error:e.message}))
        }
    });


    router.post('/user/del', async function(req, res){
        try{
            // let obj = url.parse(req.url, true).query;
            let id = req.body.id;
            res.end(JSON.stringify(await delUser(id)));
        }
        catch (e) {
            res.end(JSON.stringify({status:1, error:e.message}))
        }
    });


    router.get('/user/getUser', async function(req, res){
        try{
            let obj = url.parse(req.url, true).query;
            let id = obj.id;
            let address = obj.address || null;
            if (address !== null)    address = address.toLowerCase();

            res.end(JSON.stringify(await getUser(id,address)));
        }
        catch (e) {
            res.end(JSON.stringify({status:1, error:e.message}))
        }
    });

    router.get('/user/getName', async function(req, res){
        try{
            let obj = url.parse(req.url, true).query;
            let address = obj.address.toLowerCase();

            res.end(JSON.stringify(await getName(address)));
        }
        catch (e) {
            res.end(JSON.stringify({status:1, error:e.message}))
        }
    });

    //上传文件属性名为file
    router.post('/user/importUsers', async function(req, res){
        try{
            let form = new formidable.IncomingForm(); 
            form.encoding = 'utf-8';        //设置编辑
            form.uploadDir = 'upload/';     //设置上传目录
            form.keepExtensions = true;     //保留后缀
            form.maxFieldsSize = 5 * 1024 * 1024;   //文件大小
            console.log('heeeee')

            await form.parse(req,function(err,fields,files){

                if(err){
                    console.log(err);
                }

                res.end(JSON.stringify(importUsers(files.file.path))); 
            });
            
        }
        catch (e) {
            res.end(JSON.stringify({status:1, error:e.message}))
        }
    });


}

module.exports = {register};
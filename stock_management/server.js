let finalhandler = require('finalhandler');
let http = require('http');
let router = require('./lib/router');
let config = require('./config');
const log = require('./lib/log');
let fs = require("fs");
let web3 = require('./lib/web3');

let requireDir = (path, subDir) => {
    let pa = fs.readdirSync(path);
    pa.forEach(file => {
        let info = fs.statSync(path + "/" + file);
        if (info.isDirectory()) {
            requireDir(path + "/" + file, file);
        } else if (file.match(/\.js$/) !== null) {
            log.info("load router: " + path + "/" + file);
            require(path + "/" + file).register(router, subDir);
        }
    })
};

requireDir(__dirname + '/routers');

let server = http.createServer(function (req, res) {
    res.setHeader('Access-Control-Allow-Headers', 'X-Requested-With, Authorization, Content-type,Accept,X-Access-Token,X-Key');
    res.setHeader('Access-Control-Allow-Origin', '*');
    res.setHeader('Content-Type', 'text/plain; charset=utf-8');
    router(req, res, finalhandler(req, res))
});

if (server.listen(config.listen.port, config.listen.host)) {
    log.info(`listening ${config.listen.host}:${config.listen.port}`);
}

var exporterService = require('./services/exporter.js');

async function sysTransaction(){
    // let web3 = new Web3();
    // web3.setProvider(new Web3.providers.HttpProvider(config.gethServer));
    new exporterService(web3);
}

if (config.listenBlocks) sysTransaction();
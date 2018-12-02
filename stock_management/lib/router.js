var express = require('express');
var path = require('path');
let Router = require('router');
let router = Router({});
const bodyParser = require('body-parser');
const db = require('../lib/db');
const config = require('../config');
const crypto = require('../lib/crypto');
//const log = require('../lib/log');

var routes = require('./index');
var api = require('./api');
var app = express();
var tempToken = "";
var tempSalt = "";

app.set('views', path.join(__dirname, 'views'));
app.set('view engine', 'ejs');

router.use(bodyParser.json()); // support json encoded bodies
router.use(bodyParser.urlencoded({ extended: true }));
app.use(express.static(path.join(__dirname, 'public')));

router.get('/', function (req, res) {
    res.end('Hello World!')
});

//验证用户登陆token
router.use(async function (req, res, next) {
    //绕过特定api
    if(req.originalUrl.startsWith('/user/login') ||
        //req.originalUrl.startsWith('/option/distributeOption') ||
        req.originalUrl.startsWith('/option/lockedRatio') ||
        //req.originalUrl.startsWith('/option/unlockOption') ||
        //req.originalUrl.startsWith('/option/reclaimOption') ||
        req.originalUrl.startsWith('/contract/getAddressesInternal') ||
        req.originalUrl.startsWith('/user/getAccountsInternal') ||

        req.originalUrl.startsWith('/option/transfer') ||
        'OPTIONS' === req.method) return next();
    //console.log('headers:', JSON.stringify(req.headers, null, 2));
    //console.log('Authorization:', req.headers.authorization);
    if(req.headers.authorization === null || req.headers.authorization === undefined) return res.end(JSON.stringify({status:401, data:"Please login first and include token in request header"}));
    let tokenArray = req.headers.authorization.split('-');
    if(tokenArray.length !== 3) return res.end(JSON.stringify({status:401, data:"invalid token, please login first"}));
    let token = tokenArray[1];
    let timeToken = parseInt(tokenArray[0], 10);
    let timeHash = tokenArray[2];
    //console.log('timeToken:', timeToken);
    let timeNow = Math.floor(Date.now() / 1000);

    if((timeNow - timeToken) > config.loginTimeout) return res.end(JSON.stringify({status:401, data:"log in timeout, please login again."}));

    if(tempToken === token && timeHash === crypto.sha512(tempSalt, tokenArray[0]).passwordHash) return next();

    let rows = await db.getRows('SELECT token, salt FROM admin WHERE token =?', [token]);
    if(rows.length === 0 || timeHash !== crypto.sha512(rows[0].salt, tokenArray[0]).passwordHash) return res.end(JSON.stringify({status:401, data:"invalid token, please login first"}));
    console.log("temp token changed");

    tempToken = rows[0].token;
    tempSalt = rows[0].salt;
    next();
});

app.use('/', routes);
app.use('/api', api);

// catch 404 and forward to error handler
app.use(function(req, res, next) {
  var err = new Error('Not Found');
  err.status = 404;
  next(err);
});
// development error handler
// will print stacktrace
if (app.get('env') === 'development') {
  app.use(function(err, req, res, next) {
    res.status(err.status || 500);
    res.render('error', {
      message: err.message,
      error: err
    });
  });
}
// production error handler
// no stacktraces leaked to user
app.use(function(err, req, res, next) {
  res.status(err.status || 500);
  res.render('error', {
    message: err.message,
    error: {}
  });
});
module.exports = router;

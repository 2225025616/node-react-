var express = require('express');
var router = express.Router();
var fs = require('fs');

router.options('*', function (req, res, next) {
    next();
});

module.exports = router;

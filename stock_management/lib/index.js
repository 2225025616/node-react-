var express = require('express');
var router = express.Router();

/* GET admin page. */
router.get('/admin', function(req, res, next) {
    res.render('admin', {})
});

module.exports = router;
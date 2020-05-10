// Applying authentication to specific endpoints
var express = require('express');
var router = express.Router();
const path = require('path')

// Protecting /users path
router.use('/', function (req, res, next) {
    let isLoggedIn = req.session.is_loggedin
    let allowedRoutes = ['/login', '/oauth-callback', '/logout']
    // decode token
    console.log(`isLoggedIn: ${isLoggedIn}`)
    if (isLoggedIn) {
        next();
    } else {
        if (allowedRoutes.indexOf(req.path) > -1) {
            next();
        } else {
            res.sendFile(path.join(__dirname + '/../views/logged-out.html'));
        }
    }
});

module.exports = router;
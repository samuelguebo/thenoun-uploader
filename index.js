const settings = require('./settings.js')
const express = require('express')
const path = require('path')
const WikiOAuth = require('./auth.js')
const fetch = require('node-fetch')
const bodyParser = require('body-parser')
const session = require('express-session');
const app = express()
const router = express.Router();


app.use(express.static('static'))
app.use(bodyParser.urlencoded({
    extended: false
}))
app.use(session({
    secret: settings.OAUTH_KEY,
    saveUninitialized: false,
    resave: false,
    cookie: {
        maxAge: 1000 * 60 * 15
    }
}));

app.use(bodyParser.json())
app.use(router)

router.get('/', function (req, res) {
    res.sendFile(path.join(__dirname + '/index.html'));

})

router.get('/login', function (req, res) {
    new WikiOAuth().authenticate()
        .then(auth => {
            if (auth.oauth_callback_confirmed == 'true') {
                // redirect 
                let permissionURI = `${settings.OAUTH_MWURI}/wiki/Special:OAuth/authorize`
                permissionURI += `?oauth_consumer_key=${settings.OAUTH_KEY}`
                permissionURI += `&oauth_token=${auth.oauth_token}`
                req.session.req_token = auth.oauth_token
                req.session.req_secret = auth.oauth_token_secret
                //console.log(`redirecting to ${permissionURI}`)
                res.redirect(permissionURI + '\n')
            } else {
                console.log(auth.error)
            }
        })

})

router.get('/oauth-callback', function (req, res) {
    let params = req.query
    req.session.oauth_verifier = params.oauth_verifier;

    let baseURI = `http://${req.headers.host}`

    // obtain access token
    new WikiOAuth().getToken(req)
        .then(auth => {
            req.session.access_token = auth.oauth_token
            req.session.access_token_secret = auth.oauth_token_secret
            console.log(req.session)
            return auth
        })
        // get CSRF Token
        .then(auth => {
            new WikiOAuth().getCrsfToken(req)
                .then(token => {
                    console.log(token)
                }).catch(error => {
                    console.log(`Error: ${error}`)
                })
        })
        .then(data => res.send(`data params: ${JSON.stringify(data)}`))

})


app.listen(5000)
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
    let wikiOauth = new WikiOAuth();
    wikiOauth.authenticate()
        .then(auth => {
            if (auth.oauth_callback_confirmed == 'true') {
                // update session
                req.session.req_token = auth.oauth_token
                req.session.req_secret = auth.oauth_token_secret
                // redirect
                wikiOauth.grantAccessRedirect(req, res)
            } else {
                console.log(`on /login: ${auth.error}`)
            }
        })

})

router.get('/oauth-callback', function (req, res) {
    let params = req.query
    req.session.oauth_verifier = params.oauth_verifier;
    let wikiOauth = new WikiOAuth();

    // get access token
    wikiOauth.getAccessToken(req)
        .then(auth => {
            //console.log(auth)
            req.session.access_token = auth.oauth_token
            req.session.access_token_secret = auth.oauth_token_secret
            //console.log(req.session)
            return auth
        })

        // get CSRF Token
        .then(data => {
            wikiOauth.getCrsfToken(req)
                .then(token => {
                    res.send(token)
                })

                .catch(error => res.send('An error occured during authentication. Please try again'))

        })

})

app.listen(5000)
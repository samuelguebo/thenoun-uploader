const settings = require('./settings.js')
const express = require('express')
const bodyParser = require('body-parser')
const session = require('express-session');
const app = express()

app.use(express.static('public'))
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

app.use(require('./app/controllers/auth-middleware'));
app.use(require('./app/controllers/home'));
app.use(require('./app/controllers/auth'));

app.listen(5000)
const express = require('express')
const path = require('path')
const app = express()
const router = express.Router();

router.get('/', function (req, res) {
    res.sendFile(path.join(__dirname + '/index.html'));

})

app.use(express.static('static'))
app.use(router)

app.listen(8081, () => {
    console.log('App listening on port 8082!')
})
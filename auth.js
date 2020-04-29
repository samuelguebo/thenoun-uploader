const settings = require('./settings.js')
const crypto = require('crypto')
const request = require('request');
const OAuth = require('oauth-1.0a')
const qs = require('querystring')

class WikiOAuth {
    authenticate = () => {
        return new Promise((res, err) => {
            let oauth = this.getOauth()
            let data = {}
            let apiURI = `${settings.OAUTH_MWURI}/w/index.php?title=Special:OAuth/initiate&oauth_callback=oob`

            request.post(apiURI, {
                form: oauth.authorize({
                    url: apiURI,
                    method: 'POST',
                }),
            }, (error, response, body) => {

                data = qs.parse(body)
                // console.log(`Data: ${JSON.stringify(data)}`)
                res(data)
            }).on('error', error => {
                error: error
            })

        })

    }

    getToken = (req) => {
        // console.log(req.session)
        return new Promise((res, err) => {
            let oauth = this.getOauth()
            let data = {}

            let apiURI = `${settings.OAUTH_MWURI}/wiki/Special:OAuth/token?`
            apiURI += `oauth_verifier=${req.session.oauth_verifier}`


            let token = {
                key: req.session.req_token,
                secret: req.session.req_secret
            }

            request.post(apiURI, {
                form: oauth.authorize({
                    url: apiURI,
                    method: 'POST',
                }, token),
            }, (error, response, body) => {
                data = qs.parse(body)

                res(data)
            }).on('error', error => {
                console.log(error)
                error: error
            })

        }).catch(error => console.log(`error: ${error}`))

    }

    // Initiate an Oauth object
    getOauth = () => OAuth({
        consumer: {
            key: settings.OAUTH_KEY,
            secret: settings.OAUTH_SECRET,
        },
        signature_method: 'HMAC-SHA1',
        hash_function(base_string, key) {
            return crypto
                .createHmac('sha1', key)
                .update(base_string)
                .digest('base64')
        },
    })

    // Generate CSRF token
    getCrsfToken = (req) => {
        console.log(req.session)

        return new Promise((res, err) => {
            let oauth = this.getOauth()
            let data = {}

            let apiURI = `${settings.OAUTH_MWURI}/w/api.php`
            apiURI += '?action=query&meta=tokens'
            apiURI += '&type=csrf&format=json'

            let oauth_signature = oauth.authorize({
                url: apiURI,
                method: 'GET'
            })

            console.log(req.session)
            /**
             * headers': {
                'Authorization': 'OAuth oauth_consumer_key="79ea52b320da444132d4ee4d28fd3810",oauth_token="6f9e329e7be0aae5b5228b5e53661b6e",oauth_signature_method="HMAC-SHA1",oauth_timestamp="1587963155",oauth_nonce="fTXoFO9ordi",oauth_version="1.0",oauth_signature="dgT1N4l%2BuqcNs%2F2PRTWoPb1gqvQ%3D"

             */



            let headers = oauth.toHeader(oauth_signature)
            console.log(headers)

            request.get(apiURI, {
                url: apiURI,
                method: 'GET',
                form: oauth_signature,

            }, (error, response, body) => {

                data = qs.parse(body)
                console.log(data)
                if (typeof data['*'] !== 'undefined') {
                    res(data['*']['batchcomplete']['query']['tokens']['csrftoken'])
                } else {
                    console.error('Error while fetching CSRF token')
                }
            }).on('error', error => {
                error: error
            })

        })
    }
}

module.exports = WikiOAuth
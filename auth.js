/**
 *
 */
const settings = require('./settings.js')
const urllib = require('url')
const oauth = require('oauth-lite')
const fetch = require('node-fetch')

class WikiOAuth {
    authenticate = () => {
        return new Promise((res, err) => {
            let state = this.getState()
            let apiURI = `${settings.OAUTH_MWURI}/w/index.php?title=Special:OAuth/initiate&oauth_callback=oob`

            oauth.fetchRequestToken(state, apiURI, null, (err, params) => {
                    res(params)
                })
                .on('error', error => {
                    error: `on authenticate(): ${error}`
                })
        })

    }

    getAccessToken = (req) => {

        let state = this.getState()
        let apiURI = `${settings.OAUTH_MWURI}/wiki/Special:OAuth/token?`
        apiURI += `oauth_verifier=${req.session.oauth_verifier}`

        // add token details
        state.oauth_token = req.session.req_token
        state.oauth_token_secret = req.session.req_secret

        let options = urllib.parse(apiURI, true);
        options.url = apiURI
        options.method = 'GET'
        options.headers = {
            'Authorization': oauth.makeAuthorizationHeader(state, options)
        }

        return fetch(apiURI, options)
            .then(data => data.text())
            .then(result => {
                // update session
                req.session.access_token = result.oauth_token
                req.session.access_token_secret = result.oauth_token_secret
                return Object.fromEntries(new URLSearchParams(result));
            })
            .catch(error => `error on getAccessToken(): ${error}`)

    }

    // Generate CSRF token
    getCrsfToken = (req) => {
        let state = this.getState()

        let apiURI = `${settings.OAUTH_MWURI}/w/api.php`
        apiURI += '?action=query&meta=tokens'
        apiURI += '&type=csrf&format=json'

        // add token details
        state.oauth_token = req.session.access_token
        state.oauth_token_secret = req.session.access_token_secret

        let options = urllib.parse(apiURI, true);
        options.url = apiURI
        options.method = 'GET'
        options.headers = {
            'Authorization': oauth.makeAuthorizationHeader(state, options)
        }

        return fetch(apiURI, options)
            .then(data => data.json())
            .then(result => {
                return result.query.tokens.csrftoken
            })
            .catch(error => `error on getCrsfToken(): ${error}`)

    }

    grantAccessRedirect = (req, res) => {
        // redirect 
        let permissionURI = `${settings.OAUTH_MWURI}/wiki/Special:OAuth/authorize`
        permissionURI += `?oauth_consumer_key=${settings.OAUTH_KEY}`
        permissionURI += `&oauth_token=${req.session.req_token}`

        //console.log(`redirecting to ${permissionURI}`)
        res.redirect(permissionURI + '\n')
    }
    // Initiate an Oauth object
    getState = () => {
        return {
            oauth_consumer_key: settings.OAUTH_KEY,
            oauth_consumer_secret: settings.OAUTH_SECRET,
        }
    }
}

module.exports = WikiOAuth
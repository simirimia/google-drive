# Simplified Google Drive API access
Simplified Google Drive API access based on google/apiclient

## How to run the examples
* set up the Google client secret and download it as JSON file (see below id you need instructions for that)
* move it to the examples/config folder
* CLI version
    * rename it to app_secret_cli.json
    * open terminal, go to the examples/cli folder
        * run php index.php 
        * open the URL
        * enter the verification code
        * now you should see a (partial) list of the content of your drive
        * run it again, now you should see the same list without the hassle of managing the verification code
* HTTP version
    * rename it to app_secret_http.json
    * set up a web server with examples/http as doc root
    * 
    

## How to get the client secret from google
* go to https://console.developers.google.com
* create new or open existing project
* got to API Manager, enable Google Drive API
* go to Credentials and create new OAuth Client ID
* if it is the first time, you need to setup the consent screen
    *  enter product name and an optional URL
* CLI version
    * In the list with application types, choose other and give it a name
* HTTP version
    * In the list with application types, choose Web Application and give it a name
    * In the field Authorized redirect URIs enter:
        * http://< your (local) web server url >/< OAuth2 callback handler >
            * to run the examples (given you have a local system gd-test.app set up):
            * http://gd-test.app/redirecttarget.php  
* Confirm the popup with client id and secret -> you don't need to remember those now
* Instead, download the whole credentials as json from the list with all client IDs
* move the file to any destination you like an make sure you give the correct path to the service



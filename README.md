Bouncer
=======

This project is a proof of concept and not a polished product. Its intended use case was to build a low cost captive portal for WiFi setups using Aerohive access points. It currently has the following features:

* Landing page with login and self-registration functionality
* User self-registration using name, personal email address and mobile phone number (optional)
* Users must also specify a sponsor email address from a list of approved sponsors
* Sponsor notification via email to accept or reject registrations
* Sending of confirmation email and optionally a short message (SMS) containing the user credentials
* Network login with personal email address and password
* 12 hours MAC address caching of the users devices to allow for seamless reconnects
* Ability to limit the amount of devices per account

## Running Bouncer
You will need a Linux server running [Docker](https://www.docker.com/) and [Docker Compose](https://docs.docker.com/compose/). Clone this repo and rename the `.env.sample` file to `.env`. Change the values for the variables to match your environment most of them should be self explanatory.

* `DATABASE_NAME` name of the database used by Freeradius
* `DATABASE_USER` username for the radius database
* `DATABASE_PASS` password for the radius database
* `RADIUS_CLIENTS` Radius shared secret in the form of shared_secret@subnet/mask_bits
* `RADIUS_AUTH_PORT` Radius auth port
* `RADIUS_ACCT_PORT` Radius acct port
* `VIRTUAL_HOST` External hostname as used by [LetsEncrypt companion container for nginx-proxy](https://github.com/JrCs/docker-letsencrypt-nginx-proxy-companion)
* `LETSENCRYPT_EMAIL` Mail address External hostname as used by [LetsEncrypt companion container for nginx-proxy](https://github.com/JrCs/docker-letsencrypt-nginx-proxy-companion)
* `ENDPOINT_LIMIT` Limits how many devices the user can us to simultaniously connect to the Wifi
* `SPONSORCC` Specify whether the sponsor should be in CC when sending confirmation email to use can be either TRUE or FALSE
* `UAM_SHARED_SECRET` A 1-128 characters long random string used to secure user passwords during login
* `CI_ENCRYPTION_KEY` CodeIgniter Encryption Key
* `SMTP_HOST` SMTP server name
* `SMTP_PORT` SMTP server port
* `SMTP_USER` SMTP username
* `SMTP_PASS` SMTP password
* `SMTP_CRYPTO` SMTP Encryption `tls` or `ssl`
* `SIPGATE_SMS_ID` see chapter SMS integration below
* `SIPGATE_ACCESS_TOKEN` see chapter SMS integration below

Now you can start Bouncer using the `docker-compose up` command. Please note Bouncers web interface is not meant to be reachable directly over the internet instead it relies on a reverse proxy as provided by the [LetsEncrypt companion container for nginx-proxy](https://github.com/JrCs/docker-letsencrypt-nginx-proxy-companion). This will also take care of the retrieval and renewal of the SSL certicate by using [Let's Encrypt](https://letsencrypt.org/) a free, automated, and open Certificate Authority. It is even possible to extend the functionality of the nginx-proxy to also load balance the UDP connections to the Radius containers.

A few settings are not available via the `.env` variables but need to be changed in order to make Bouncer work properly:
* approved sponsor list in the register function of the Portal library
* sender email address and name in the register function of the Portal library
* sender email address and name in the _send_approval_mail function of the Sponsor controller

## Configure your Wifi - Aerohive style
If you're reading this you probably already have some experience with Aerohive access points and HiveManager. So I will not provide a detailed guide how to set it all up but I will point out the most critical settings.

### First step - Setup the Captive Web Portal (CWP)
You can find it in HiveManager under CONFIGURATION >> AUTHENTICATION >> Captive Web Portals. Create a new entry and apply the following settings:

* Name (Exact value is not important some random name will do)
* Registration Type: External Authentication
* Captive Web Portal Login Page Settings
  * Authentication Method: CHAP
  * Login URL: https://your-server-name/aerohive
  * Password Encryption: UAM with Shared Secret
  * Shared Secret: enter the same random key as in the Bouncer configuration
* Captive Web Portal Success Page Settings
  * Show the success page after a successful login: checkbox enabled
  * Use the automatically generated web pages
  * After successful login: Redirect to initially requested page
* Captive Web Portal Failure Page Settings
  * Show the failure page after an unsuccessful login attempt: checkbox enabled
  * Use the automatically generated web pages
  * After a failed login: Redirect to the login page
* Captive Web Portal Language Support
  * Choose the supported and default languages for the Aerohive internal web pages please note: Bouncer is currently only available in english!

### Second step - Configure the AAA Client Settings
You can find it in HiveManager under CONFIGURATION >> AUTHENTICATION >> AAA Client Settings. Create a new entry and apply the following settings:

* Name (Exact value is not important some random name will do)
* IP Address/Domain Name of your server running Bouncer
* Server Type: Auth
* Shared Secret: enter the same shared secret as in the Bouncer configuration please note: This is and should be different from the UAM shared secret above!
* Authentication Port: 1812

### Third step - Define the SSID
You can find it in HiveManager under CONFIGURATION >> SSIDS. Create a new entry and apply the following settings:

* Profile Name
* SSID
* SSID Access Security
  * Open
  * Enable Captive Web Portal: checkbox enabled
  * Enable MAC authentication: checkbox enabled
    * Authentication Protocol: PAP
* Optional Settings
  * Advanced
    * User Profile Application Sequence: MAC Authentication - Captive Web Portal - SSID

### Fourth Step - Configure the User Profile
This is where you define your guest VLAN ID. It is also a good idea to implement a suitable IP Firewall Policy and bandwidth limiting. How to do this is not in the scope of this document.

### Fifth Step - Supplemental CLI
Bouncers MAC caching feature relies on the MAC authentication which we enabled earlier in the SSID settings but if the users MAC address is unknown we want to redirect the users to the Captive Web Portal. In order to do this we have to apply a CLI command to the configuration of the access points. For that to work we have to enable supplemental CLI in the settings of the HiveManager. This can be found under HOME >> HIVEMANAGER SETTINGS >> Enable Supplemental CLI. If this is set to Yes you should get a new option under CONFIGURATION >> COMMON OBJECTS. This option is called CLI Supplement in there create a new entry and apply the following command:

```
security-object <SSID-profile-name> security additional-auth-method mac-based-auth fallback-to-ecwp
```

Replace &lt;SSID-profile-name&gt; with the Profile Name of your actual SSID as defined in Step 3.

### Last Step - put everything together in your Network Policy
Select the newly created SSID and apply the Captive Portal, AAA client (aka RADIUS) and the User profile. Don't forget to apply the CLI supplement under Additional settings >> Service Settings >> Supplemental CLI! Now save your network policy and apply it to your access points using a full configuration upload.

## SMS integration
By default it is possible to use the SMS gateway provided by [Sipgate](https://www.sipgate.de). With slight modifications to the sponsor controller it is also possible to use the Free and Open Source SMS Gateway [playSMS](https://playsms.org/).

To use the [Sipgate REST API](https://api.sipgate.com/v2/doc) it is recommended to use OAuth2 to create an access token. But if you already have a sipgate account it is also possible to use the API directly. This is particularly useful to determine the correct value for the smsId.

First you have to find out what your userId is.

```
curl --request GET --user username:password --url https://api.sipgate.com/v2/users
{"items":[{"id":"<userId>","firstname":"John","lastname":"Doe","email":"jdoe@gmail.com","defaultDevice":"e0","busyOnBusy":false,"admin":true}]}
```

Now that you know your userId you can query the sms endpoint to get your smsId.

```
curl --request GET --user username:password --url https://api.sipgate.com/v2/<userId>/sms
{"items":[{"id":"<smsId>","alias":"SMS","callerId":"sipgate"}]}
```

It is also possible to send out a test SMS via curl.

```
curl --request POST --user username:password --header "Content-Type: application/json" --data '{"smsId":"<smsId>", "recipient":"+4915123456790", "message":"This is only a test"}' --url https://api.sipgate.com/v2/sessions/sms
```

Edit the .env file and uncomment the line containing the SIPGATE_SMS_ID and enter your smsId.
Have a look at the [Sipgate documentation](https://developer.sipgate.io/rest-api/authentication/#oauth2) to see how to generate the access token and put it also in the .env file as value for the SIPGATE_ACCESS_TOKEN variable.
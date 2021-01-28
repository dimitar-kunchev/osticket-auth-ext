# External authentication over REST plugin for OSTicket

This provides the option to authenticate staff members with external service over a simple REST call

Configure the address to post credentials to and build your own backed processor to accept them.

Request is sent as JSON `{"email": "...", "pass": "..."}`. Note that the plugin sends in the fields whatever was provided in the login form.  
Response should be 200 OK `{"email": "...", "name": "..."}`. Any non-200 response will be treated as access denied.

The plugin can be configured (via the OSTicket plugin configuration page) to create staff members if their credentials are accepted but they
are missing from the local database. You can setup which deparment and what level they should be assigned to.

# Building

php -dphar.readonly=0 -f make.php

# Installing

Copy to the include/plugins directory of your OSTicket install. Open the web panel and install the plugin.    
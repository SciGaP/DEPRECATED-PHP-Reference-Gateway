PHP-Reference-Gateway
=====================

A gateway developed in PHP as a reference implementation for the Airavata API. The concepts in the gateway are inspired by the Ultrascan gateway.

Installation
-----------------
To run your own instance of the PHP Reference Gateway, clone this repository to a directory on your webserver.  These files should be owned by the same user/group that owns the httpd processes. See the httpd.conf for these settings. 

You will find some configuration constants at the top of utilities.php. You must set these constants to appropriate values. Note also that you will need to create the directory you specify in EXPERIMENT_DATA_ROOT.  The owner of the httpd process must have read/write access to this directory. 

In order to enable login with XSEDE credentials, you will need to generate an OAuth key and modify /resources/oa4mp/oauth-properties.ini.

Be aware that you may also need to update the upload_max_filesize and post_max_size values in your webserver's php.ini file in order to upload large input files.

If everything is set up correctly, you should be able to load login.php in a browser and either log in or create an account.

User Store
-----------------
There are two available user stores that may be enabled: a simple XML database, and a WS02 Identity Server. You can configure which version to use by setting the value of the constant USER_STORE near the top of utilities.php. Note that the WS02 Identity Server requires the PHP SOAP and OpenSSL extensions, so you may need to enable them on your webserver. Depending on your operating system, you may also need to edit the cafile-path within wsis_config.ini. You may add your own user store by implementing the id_utilities.php interface.

PHP-Reference-Gateway
=====================

A gateway developed in PHP as a reference implementation for the Airavata API. The concepts in the gateway are inspired by the Ultrascan gateway. 
Installation
-----------------
To run your own instance of the PHP Reference Gateway, clone this repository to a directory on your webserver. Load login.php in a browser and either log in or create an account.

User Store
-----------------
There are two available user stores that may be enabled: a simple XML database, and a WS02 Identity Server. You can configure which version to use by setting the value of the constant USER_STORE near the top of utilities.php. Note that the WS02 Identity Server requires the PHP SOAP and OpenSSL extensions, so you may need to enable them on your webserver. Depending on your operating system, you may also need to edit the cafile-path within wsis_config.ini. You may add your own user store by implementing the id_utilities.php interface.

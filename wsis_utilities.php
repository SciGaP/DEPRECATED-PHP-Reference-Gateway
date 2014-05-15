<?php

require_once 'id_utilities.php';

$GLOBALS['WSIS_ROOT'] = './lib/WSIS/';
require_once $GLOBALS['WSIS_ROOT'] . 'WSISClient.php';

/**
 * Utilities for ID management with a WSO2 IS 4.6.0
 */

class WSISUtilities implements IdUtilities{

    const WSIS_CONFIG_PATH = 'wsis_config.ini';

    /**
     * wso2 IS client
     * 
     * @var WSISClient
     * @access private
     */
    private $wsis_client;

    /**
     * Connect to the identity store.
     * @return mixed|void
     */
    public function connect() {     
        $wsis_config = null;

        try {
            if (file_exists(self::WSIS_CONFIG_PATH)) {
                $wsis_config = parse_ini_file(self::WSIS_CONFIG_PATH);
            } else {
                throw new Exception("Error: Cannot open wsis_config.xml file!");
            }

            if (!$wsis_config) {
                throw new Exception('Error: Unable to read wsis_config.xml!');
            }
            
            if(substr($wsis_config['service-url'], -1) !== "/"){
                $wsis_config['service-url'] = $wsis_config['service-url'] . "/";
            }
            
            if(!substr($wsis_config['cafile-path'], 0) !== "/"){
                $wsis_config['cafile-path'] = "/" . $wsis_config['cafile-path'];
            }
            $wsis_config['cafile-path'] = ROOT_DIR . $wsis_config['cafile-path'];            
            
            $this->wsis_client = new WSISClient(
                    $wsis_config['admin-username'],
                    $wsis_config['admin-password'],
                    $wsis_config['server'],
                    $wsis_config['service-url'],
                    $wsis_config['cafile-path'],
                    $wsis_config['verify-peer'],
                    $wsis_config['allow-self-signed']
            );            
        } catch (Exception $e) {
            throw new Exception('Unable to instantiate Identity Server client. Try editing the cafile-path within wsis_config.ini.', 0, NULL);
        }
    }

    /**
     * Return true if the given username exists in the identity server.
     * @param $username
     * @return bool
     */
    public function username_exists($username) {
        try{
            return $this->wsis_client->username_exists($username);
        } catch (Exception $ex) {
            throw new Exception("Unable to check whether username exists", 0, NULL);
        }
        
    }

    /**
     * authenticate a given user
     * @param $username
     * @param $password
     * @return boolean
     */
    public function authenticate($username, $password) {
        try{
            return $this->wsis_client->authenticate($username, $password);
        } catch (Exception $ex) {
            throw new Exception("Unable to authenticate user", 0, NULL);
        }        
    }

    /**
     * Add a new user to the identity server.
     * @param $username
     * @param $password
     * @return void
     */
    public function add_user($username, $password) {
        try{
            $this->wsis_client->addUser($username, $password);
        } catch (Exception $ex) {
            throw new Exception("Unable to add new user", 0, NULL);
        }        
    }
}

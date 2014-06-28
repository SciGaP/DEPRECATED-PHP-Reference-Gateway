<?php
/**
 * Basic Airavata UserAPI utility functions
 */
/**
 * import Thrift and Airavata
 */
$GLOBALS['THRIFT_ROOT'] = './lib/Thrift/';
require_once $GLOBALS['THRIFT_ROOT'] . 'Transport/TTransport.php';
require_once $GLOBALS['THRIFT_ROOT'] . 'Transport/TSocket.php';
require_once $GLOBALS['THRIFT_ROOT'] . 'Protocol/TProtocol.php';
require_once $GLOBALS['THRIFT_ROOT'] . 'Protocol/TBinaryProtocol.php';
require_once $GLOBALS['THRIFT_ROOT'] . 'Exception/TException.php';
require_once $GLOBALS['THRIFT_ROOT'] . 'Exception/TApplicationException.php';
require_once $GLOBALS['THRIFT_ROOT'] . 'Exception/TProtocolException.php';
require_once $GLOBALS['THRIFT_ROOT'] . 'Base/TBase.php';
require_once $GLOBALS['THRIFT_ROOT'] . 'Type/TType.php';
require_once $GLOBALS['THRIFT_ROOT'] . 'Type/TMessageType.php';
require_once $GLOBALS['THRIFT_ROOT'] . 'Factory/TStringFuncFactory.php';
require_once $GLOBALS['THRIFT_ROOT'] . 'StringFunc/TStringFunc.php';
require_once $GLOBALS['THRIFT_ROOT'] . 'StringFunc/Core.php';

$GLOBALS['AIRAVATA_ROOT'] = './lib/Airavata/';
require_once $GLOBALS['AIRAVATA_ROOT'] . 'UserAPI/UserAPI.php';
require_once $GLOBALS['AIRAVATA_ROOT'] . 'UserAPI/Models/Types.php';
require_once $GLOBALS['AIRAVATA_ROOT'] . 'UserAPI/Error/Types.php';
require_once $GLOBALS['AIRAVATA_ROOT'] . 'UserAPI/Types.php';

require_once './lib/UserAPIClientFactory.php';
require_once './id_utilities.php';
require_once './wsis_utilities.php';

use \Airavata\UserAPI\UserAPIClient;
use \Airavata\UserAPI\UserAPIClientFactory;
use \Airavata\UserAPI\Models\UserProfile;

/**
 * Utilities for ID management with Airavata UserAPI
 */

class UserAPIUtilities implements IdUtilities{

    const USER_API_CONFIG_PATH = 'userapi_config.ini';

    /**
     * UserAPI client
     *
     * @var UserAPIClient
     * @access private
     */
    private $userapi_client;


    /**
     * UserAPI client factory
     *
     * @var UserAPIClientFactory
     * @access private
     */
    private $userapi_client_factory;

    /**
     * Connect to the identity store.
     * @return mixed|void
     */
    public function connect() {
        try {
            $properties = array();
            $this->userapi_client_factory = new UserAPIClientFactory($properties);
            $this->userapi_client = $this->userapi_client_factory->getUserAPIClient();

            if(!isset($_SESSION['USER_API_TOKEN'])){
                $_SESSION['USER_API_TOKEN'] = $this->userapi_client->adminLogin("admin@phprg.scigap.org","phprg9067@min");
            }

        } catch (Exception $ex) {
            throw new Exception('Unable to instantiate UserAPI client.', 0, NULL);
        }
    }

    /**
     * Return true if the given username exists in the identity server.
     * @param $username
     * @return bool
     */
    public function username_exists($username) {
        try{
            return $this->userapi_client->checkUsernameExists($username,$_SESSION['USER_API_TOKEN']);
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
            return $this->userapi_client->authenticateUser($username, $password, $_SESSION['USER_API_TOKEN']);
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
    public function add_user($username, $password, $first_name, $last_name, $email, $organization) {
        try{
            $profile = new UserProfile();
            $profile->firstName = $first_name;
            $profile->lastName = $last_name;
            $profile->emailAddress = $email;
            $profile->organization = $organization;

            $this->userapi_client->createNewUser($username, $password, $profile, $_SESSION['USER_API_TOKEN']);
        } catch (Exception $ex) {
            var_dump($ex);
            throw new Exception("Unable to add new user", 0, NULL);
        }
    }
}

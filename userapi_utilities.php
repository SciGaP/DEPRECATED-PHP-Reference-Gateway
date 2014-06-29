<?php
/**
 * Basic Airavata UserAPI utility functions
 */
/**
 * Import Thrift and Airavata
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
    public function add_user($username, $password, $first_name, $last_name, $email, $organization,
                             $address, $country,$telephone, $mobile, $im, $url) {
        try{
            $profile = new UserProfile();
            $profile->firstName = $first_name;
            $profile->lastName = $last_name;
            $profile->emailAddress = $email;
            $profile->organization = $organization;
            $profile->address = $address;
            $profile->country = $country;
            $profile->telephone = $telephone;
            $profile->mobile = $mobile;
            $profile->im = $im;
            $profile->url = $url;

            $this->userapi_client->createNewUser($username, $password, $profile, $_SESSION['USER_API_TOKEN']);
        } catch (Exception $ex) {
            throw new Exception("Unable to add new user", 0, NULL);
        }
    }

    /**
     * Get the user profile
     * @param $username
     * @return mixed|void
     */
    public function get_user_profile($username)
    {
        try{
            $profile_obj = $this->userapi_client->getUserProfile($username, $_SESSION['USER_API_TOKEN']);
            $profile_arr = array();
            $profile_arr['first_name'] = $profile_obj->firstName;
            $profile_arr['last_name'] = $profile_obj->lastName;
            $profile_arr['email_address'] = $profile_obj->emailAddress;
            $profile_arr['organization'] = $profile_obj->organization;
            $profile_arr['address'] = $profile_obj->address;
            $profile_arr['country'] = $profile_obj->country;
            $profile_arr['telephone'] = $profile_obj->telephone;
            $profile_arr['mobile'] = $profile_obj->mobile;
            $profile_arr['im'] = $profile_obj->im;
            $profile_arr['url'] = $profile_obj->url;
            return $profile_arr;
        } catch (Exception $ex) {
            throw new Exception("Unable to get user profile", 0, NULL);
        }
    }

    /**
     * Update the user profile
     *
     * @param $username
     * @param $first_name
     * @param $last_name
     * @param $email
     * @param $organization
     * @param $address
     * @param $country
     * @param $telephone
     * @param $mobile
     * @param $im
     * @param $url
     * @return mixed
     */
    public function update_user_profile($username, $first_name, $last_name, $email, $organization, $address,
                                        $country, $telephone, $mobile, $im, $url)
    {
        try{
            $profile = new UserProfile();
            $profile->firstName = $first_name;
            $profile->lastName = $last_name;
            $profile->emailAddress = $email;
            $profile->organization = $organization;
            $profile->address = $address;
            $profile->country = $country;
            $profile->telephone = $telephone;
            $profile->mobile = $mobile;
            $profile->im = $im;
            $profile->url = $url;
            $this->userapi_client->updateUserProfile($username, $profile, $_SESSION['USER_API_TOKEN']);
        } catch (Exception $ex) {
            throw new Exception("Unable to update user profile", 0, NULL);
        }
    }

    /**
     * Function to update user password
     *
     * @param $username
     * @param $current_password
     * @param $new_password
     * @return mixed
     */
    public function change_password($username, $current_password, $new_password)
    {
        try{
            $this->userapi_client->updateUserPassword($username, $new_password, $current_password, $_SESSION['USER_API_TOKEN']);
        } catch (Exception $ex) {
            throw new Exception("Unable to update user password", 0, NULL);
        }
    }
}

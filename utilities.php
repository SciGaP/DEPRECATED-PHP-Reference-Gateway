<?php
/**
 * Basic utility functions
 */

include 'xml_id_utilities.php';

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
require_once $GLOBALS['AIRAVATA_ROOT'] . 'API/Airavata.php';
require_once $GLOBALS['AIRAVATA_ROOT'] . 'Model/Workspace/Experiment/Types.php';
require_once $GLOBALS['AIRAVATA_ROOT'] . 'Model/Workspace/Types.php';
require_once $GLOBALS['AIRAVATA_ROOT'] . 'API/Error/Types.php';

require_once './lib/AiravataClientFactory.php';

use Airavata\API\AiravataClient;
use Airavata\API\Error\InvalidRequestException;
use Airavata\API\Error\AiravataClientException;
use Airavata\API\Error\AiravataSystemException;
use Airavata\API\Error\ExperimentNotFoundException;
use Thrift\Protocol\TBinaryProtocol;
use Thrift\Transport\TSocket;




/**
 * Print success message
 * @param $message
 */
function print_success_message($message)
{
    echo '<div class="alert alert-success">' . $message . '</div>';
}

/**
 * Print error message
 * @param $message
 */
function print_error_message($message)
{
    echo '<div class="alert alert-danger">' . $message . '</div>';
}

/**
 * Redirect to the given url
 * @param $url
 */
function redirect($url)
{
    echo '<meta http-equiv="Refresh" content="1; URL=' . $url . '">';
}

/**
 * Return true if the form has been submitted
 * @return bool
 */
function form_submitted()
{
    return isset($_POST['Submit']);
}

/**
 * Compare the submitted credentials with those stored in the database
 * @param $username
 * @param $passwordHash
 * @return bool
 */
function id_matches_db($username, $passwordHash)
{
    global $idStore;

    return $passwordHash == $idStore->get_password($username);
}


/**
 * Store user details in session variables
 * @param $username
 * @param $passwordHash
 */
function store_id_in_session($username, $passwordHash)
{
    $_SESSION['username'] = $username;
    $_SESSION['password_hash'] = $passwordHash;
}

/**
 * Return true if the user details are stored in the session
 * @return bool
 */
function id_in_session()
{
    return isset($_SESSION['username']) && isset($_SESSION['password_hash']);
}

/**
 * Verify that the user details stored in the session
 * match those in the database. If not, redirect to login.
 */
function verify_login()
{
    if (id_in_session())
    {
        if (id_matches_db($_SESSION['username'], $_SESSION['password_hash']))
        {
            return; // login verified - do nothing
        }
        else
        {
            unset($_SESSION['username']);
            unset($_SESSION['password_hash']);

            print_error_message('User is not logged in!');
            redirect('login.php');
        }
    }
    else
    {
        print_error_message('User is not logged in!');
        redirect('login.php');
    }
}

/**
 * Connect to the ID store
 */
function connect_to_id_store()
{
    global $idStore;

    $idStore = new XmlIdUtilities();
    $idStore->connect();
}

/**
 * Return an Airavata client
 * @return AiravataClient
 */
function get_airavata_client()
{
    /*
    $airavataClientFactory = new \Airavata\Client\AiravataClientFactory(array('airavataServerHost' => "gw111.iu.xsede.org", 'airavataServerPort' => "8930"));

    return $airavataClientFactory->getAiravataClient();
    */

    $transport = new TSocket('gw111.iu.xsede.org', 8930);
    $transport->setRecvTimeout(5000);

    $protocol = new TBinaryProtocol($transport);
    $transport->open();

    return new AiravataClient($protocol);

}


/**
 * Launch the experiment with the given ID
 * @param $expId
 */
function launch_experiment($expId)
{
    global $airavataclient;

    try
    {
        $airavataclient->launchExperiment($expId, 'airavataToken');

        print_success_message("Experiment launched!");
    }
    catch (InvalidRequestException $ire)
    {
        print_error_message('InvalidRequestException!<br><br>' . $ire->getMessage());
    }
    catch (ExperimentNotFoundException $enf)
    {
        print_error_message('ExperimentNotFoundException!<br><br>' . $enf->getMessage());
    }
    catch (AiravataClientException $ace)
    {
        print_error_message('AiravataClientException!<br><br>' . $ace->getMessage());
    }
    catch (AiravataSystemException $ase)
    {
        print_error_message('AiravataSystemException!<br><br>' . $ase->getMessage());
    }
}
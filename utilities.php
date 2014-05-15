<?php
/**
 * Basic utility functions
 */

/**
 * Choose a user store
 */
//const USER_STORE = 'XML';
const USER_STORE = 'WS02';

if (USER_STORE == 'WS02')
{
    require_once 'wsis_utilities.php'; // WS02 Identity Server
}
else
{
    require_once 'xml_id_utilities.php'; // XML user database
}

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
use Thrift\Exception\TTransportException;
use Airavata\Model\Workspace\Experiment\ComputationalResourceScheduling;
use Airavata\Model\Workspace\Experiment\DataObjectType;
use Airavata\Model\Workspace\Experiment\DataType;
use Airavata\Model\Workspace\Experiment\UserConfigurationData;
use Airavata\Model\Workspace\Experiment\Experiment;








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
 * @param $password
 * @return bool
 */
function id_matches_db($username, $password)
{
    global $idStore;

    if($idStore->authenticate($username, $password))
    {
        return true;
    }else{
        return false;
    }
}


/**
 * Store username in session variables
 * @param $username
 */
function store_id_in_session($username)
{
    $_SESSION['username'] = $username;
    $_SESSION['loggedin'] = true;
}

/**
 * Return true if the username stored in the session
 * @return bool
 */
function id_in_session()
{
    return isset($_SESSION['username']) && isset($_SESSION['loggedin']);
}

/**
 * Verify user is already logged in. If not, redirect to login.
 */
function verify_login()
{
    if (id_in_session())
    {
        return;
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

    if (USER_STORE == 'WS02')
    {
        $idStore = new WSISUtilities(); // WS02 Identity Server
    }
    else
    {
        $idStore = new XmlIdUtilities(); // XML user database
    }


    try
    {
        $idStore->connect();
    }
    catch (Exception $e)
    {
        print_error_message($e->getMessage());
    }

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

/**
 * Get all projects owned by the given user
 * @param $username
 * @return null
 */
function get_all_user_projects($username)
{
    global $airavataclient;
    $userProjects = null;

    try
    {
        $userProjects = $airavataclient->getAllUserProjects($username);
    }
    catch (InvalidRequestException $ire)
    {
        print_error_message('InvalidRequestException: ' . $ire->getMessage(). '\n');
    }
    catch (AiravataClientException $ace)
    {
        print_error_message('Airavata System Exception: ' . $ace->getMessage().'\n');
    }
    catch (AiravataSystemException $ase)
    {
        print_error_message('Airavata System Exception: ' . $ase->getMessage().'\n');
    }

    return $userProjects;
}

/**
 * Get the experiment with the given ID
 * @param $expId
 * @return null
 */
function get_experiment($expId)
{
    global $airavataclient;

    try
    {
        return $airavataclient->getExperiment($expId);
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
    catch (TTransportException $tte)
    {
        print_error_message('TTransportException!<br><br>' . $tte->getMessage());
    }
    catch (Exception $e)
    {
        print_error_message('Exception!<br><br>' . $e->getMessage());
    }

}

/**
 * Get the project with the given ID
 * @param $projectId
 * @return null
 */
function get_project($projectId)
{
    global $airavataclient;

    try
    {
        return $airavataclient->getProject($projectId);
    }
    catch (InvalidRequestException $ire)
    {
        print_error_message('InvalidRequestException!<br><br>' . $ire->getMessage());
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


/**
 * Create and configure a new Experiment
 * @return Experiment
 */
function assemble_experiment()
{
    $scheduling = new ComputationalResourceScheduling();
    $scheduling->totalCPUCount = $_POST['cpu-count'];
    $scheduling->nodeCount = $_POST['node-count'];
    $scheduling->numberOfThreads = $_POST['threads'];
    $scheduling->queueName = 'normal';
    $scheduling->wallTimeLimit = $_POST['wall-time'];
    $scheduling->totalPhysicalMemory = $_POST['memory'];
    $scheduling->resourceHostId = $_POST['compute-resource'];

    switch ($_POST['compute-resource'])
    {
        case 'trestles.sdsc.edu':
            $scheduling->ComputationalProjectAccount = 'sds128';
            break;
        case 'stampede.tacc.xsede.org':
            $scheduling->ComputationalProjectAccount = 'TG-STA110014S';
            break;
        default:
            $scheduling->ComputationalProjectAccount = 'admin';
    }


    $userConfigData = new UserConfigurationData();
    $userConfigData->computationalResourceScheduling = $scheduling;
    $userConfigData->overrideManualScheduledParams = 0;
    $userConfigData->airavataAutoSchedule = 0;


    $experimentInput = new DataObjectType();
    $experimentInput->key = 'echo_input';
    $experimentInput->value = 'echo_output=Hello World';
    $experimentInput->type = DataType::STRING;
    $experimentInputs = array($experimentInput);


    $experimentOutput1 = new DataObjectType();
    $experimentOutput1->key = 'echo_output';
    $experimentOutput1->value = '';
    $experimentOutput1->type = DataType::STRING;

    $experimentOutput2 = new DataObjectType();
    $experimentOutput2->key = 'stdout';
    $experimentOutput2->value = '';
    $experimentOutput2->type = DataType::STRING;

    $experimentOutput3 = new DataObjectType();
    $experimentOutput3->key = 'stderr';
    $experimentOutput3->value = '';
    $experimentOutput3->type = DataType::STRING;

    $experimentOutputs = array($experimentOutput1, $experimentOutput2, $experimentOutput3);


    $experiment = new Experiment();

    // required
    $experiment->projectID = $_POST['project'];
    $experiment->userName = $_SESSION['username'];
    $experiment->name = $_POST['experiment-name'];

    // optional
    $experiment->description = $_POST['experiment-description'];
    $experiment->applicationId = $_POST['application'];
    $experiment->userConfigurationData = $userConfigData;
    $experiment->experimentInputs = $experimentInputs;

    $experiment->experimentOutputs = $experimentOutputs;



    return $experiment;
}


/**
 * Update the experiment with the given ID
 * @param $expId
 * @param $updatedExperiment
 */
function update_experiment($expId, $updatedExperiment)
{
    global $airavataclient;

    try
    {
        $airavataclient->updateExperiment($expId, $updatedExperiment);

        print_success_message('Experiment updated!');
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


/**
 * Clone the experiment with the given ID
 * @param $expId
 */
function clone_experiment($expId)
{
    global $airavataclient;

    try
    {
        //create new experiment to receive the clone
        $experiment = $airavataclient->getExperiment($expId);
        $experiment->name .= time();

        $airavataclient->cloneExperiment($expId, $experiment);

        print_success_message("Experiment cloned!");
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
    catch (TTransportException $tte)
    {
        print_error_message('TTransportException!<br><br>' . $tte->getMessage());
    }
}

/**
 * Cancel the experiment with the given ID
 * @param $expId
 */
function cancel_experiment($expId)
{
    global $airavataclient;

    try
    {
        $airavataclient->terminateExperiment($expId);

        print_success_message("Experiment canceled!");
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
    catch (TTransportException $tte)
    {
        print_error_message('TTransportException!<br><br>' . $tte->getMessage());
    }
    catch (Exception $e)
    {
        print_error_message('Exception!<br><br>' . $e->getMessage());
    }
}


/**
 * Create a select input and populate it with project options from the database
 */
function create_project_select($projectId = null, $editable = true)
{
    echo '<select class="form-control" name="project" id="project" required>';

    $userProjects = get_all_user_projects($_SESSION['username']);

    foreach ($userProjects as $project)
    {
        if ($project->projectID == $projectId)
        {
            $selected = 'selected';
        }
        else
        {
            $selected = '';
        }

        $disabled = !$editable;

        echo '<option value="' . $project->projectID . '" ' . $selected . ' ' . $disabled . '>' . $project->name . '</option>';
    }

    echo '</select>';
}

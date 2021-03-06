<?php
/**
 * Basic utility functions
 */

define('ROOT_DIR', __DIR__);

/**
 * Define configuration constants
 */
const AIRAVATA_SERVER = 'gw111.iu.xsede.org';
//const AIRAVATA_SERVER = 'gw127.iu.xsede.org';
//const AIRAVATA_SERVER = 'gw56.iu.xsede.org'; //Mirror
//const AIRAVATA_PORT = 8930; //development
const AIRAVATA_PORT = 9930; //production
const AIRAVATA_TIMEOUT = 50000;
const EXPERIMENT_DATA_ROOT = '../experimentData/';

$sshUser = 'root';
$hostName =  $_SERVER['SERVER_NAME'];
$pathConstant = 'file://'.$sshUser.'@'.$hostName.'://var/www/experimentData/';

const EXPERIMENT_DATA_ROOT_ABSOLUTE = '/var/www/experimentData/';

//const EXPERIMENT_DATA_ROOT_ABSOLUTE = 'C:/wamp/www/experimentData/';

//const USER_STORE = 'WSO2','XML','USER_API';
const USER_STORE = 'WSO2';


$req_url = 'https://gw111.iu.xsede.org:8443/credential-store/acs-start-servlet';
$gatewayName = 'PHP-Reference-Gateway';
$email = 'admin@gw120.iu.xsede.org';
$tokenFilePath = 'tokens.xml';
$tokenFile = null;
$experimentPath = null;

date_default_timezone_set('UTC');

/**
 * Import user store utilities
 */
switch (USER_STORE)
{
    case 'WSO2':
        require_once 'wsis_utilities.php'; // WS02 Identity Server
        break;
    case 'XML':
        require_once 'xml_id_utilities.php'; // XML user database
        break;
    case 'USER_API':
        require_once 'userapi_utilities.php'; // Airavata UserAPI
        break;
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
require_once $GLOBALS['AIRAVATA_ROOT'] . 'API/AppCatalog/ApplicationCatalogAPI.php';
require_once $GLOBALS['AIRAVATA_ROOT'] . 'Model/AppCatalog/ComputeResource/Types.php';
require_once $GLOBALS['AIRAVATA_ROOT'] . 'Model/AppCatalog/AppInterface/Types.php';
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
use Airavata\Model\Workspace\Experiment\UserConfigurationData;
use Airavata\Model\Workspace\Experiment\AdvancedOutputDataHandling;
use Airavata\Model\Workspace\Experiment\Experiment;
use Airavata\Model\AppCatalog\AppInterface\DataType;








/**
 * Print success message
 * @param $message
 */
function print_success_message($message)
{
    echo '<div class="alert alert-success">' . $message . '</div>';
}

/**
 * Print warning message
 * @param $message
 */
function print_warning_message($message)
{
    echo '<div class="alert alert-warning">' . $message . '</div>';
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
 * Print info message
 * @param $message
 */
function print_info_message($message)
{
    echo '<div class="alert alert-info">' . $message . '</div>';
}

/**
 * Redirect to the given url
 * @param $url
 */
function redirect($url)
{
    echo '<meta http-equiv="Refresh" content="0; URL=' . $url . '">';
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
        redirect('index.php');
    }
}

/**
 * Connect to the ID store
 */
function connect_to_id_store()
{
    global $idStore;

    switch (USER_STORE)
    {
        case 'WSO2':
            $idStore = new WSISUtilities(); // WS02 Identity Server
            break;
        case 'XML':
            $idStore = new XmlIdUtilities(); // XML user database
            break;
        case 'USER_API':
            $idStore = new UserAPIUtilities(); // Airavata UserAPI
            break;
    }

    try
    {
        $idStore->connect();
    }
    catch (Exception $e)
    {
        print_error_message('<p>Error connecting to ID store.
            Please try again later or report a bug using the link in the Help menu</p>' .
            '<p>' . $e->getMessage() . '</p>');
    }
}

/**
 * Return an Airavata client
 * @return AiravataClient
 */
function get_airavata_client()
{
    try
    {
        $transport = new TSocket(AIRAVATA_SERVER, AIRAVATA_PORT);
        $transport->setRecvTimeout(AIRAVATA_TIMEOUT);
        $transport->setSendTimeout(AIRAVATA_TIMEOUT);

        $protocol = new TBinaryProtocol($transport);
        $transport->open();

        $client = new AiravataClient($protocol);
    }
    catch (Exception $e)
    {
        print_error_message('<p>There was a problem connecting to Airavata.
            Please try again later or submit a bug report using the link in the Help menu.</p>' .
            '<p>' . $e->getMessage() . '</p>');
    }


    return $client;

}


/**
 * Launch the experiment with the given ID
 * @param $expId
 */
function launch_experiment($expId)
{
    global $airavataclient;
    global $tokenFilePath;
    global $tokenFile;

    try
    {
        /* temporarily using hard-coded token
        open_tokens_file($tokenFilePath);

        $communityToken = $tokenFile->tokenId;


        $token = isset($_SESSION['tokenId'])? $_SESSION['tokenId'] : $communityToken;

        $airavataclient->launchExperiment($expId, $token);

        $tokenString = isset($_SESSION['tokenId'])? 'personal' : 'community';

        print_success_message('Experiment launched using ' . $tokenString . ' allocation!');
        */

        $hardCodedToken = '2c308fa9-99f8-4baa-92e4-d062e311483c';
        $airavataclient->launchExperiment($expId, $hardCodedToken);

        //print_success_message('Experiment launched!');
        print_success_message("<p>Experiment launched!</p>" .
            '<p>You will be redirected to the summary page shortly, or you can
            <a href="experiment_summary.php?expId=' . $expId . '">go directly</a> to the experiment summary page.</p>');
        redirect('experiment_summary.php?expId=' . $expId);
    }
    catch (InvalidRequestException $ire)
    {
        print_error_message('<p>There was a problem launching the experiment.
            Please try again later or submit a bug report using the link in the Help menu.</p>' .
            '<p>InvalidRequestException: ' . $ire->getMessage() . '</p>');
    }
    catch (ExperimentNotFoundException $enf)
    {
        print_error_message('<p>There was a problem launching the experiment.
            Please try again later or submit a bug report using the link in the Help menu.</p>' .
            '<p>ExperimentNotFoundException: ' . $enf->getMessage() . '</p>');
    }
    catch (AiravataClientException $ace)
    {
        print_error_message('<p>There was a problem launching the experiment.
            Please try again later or submit a bug report using the link in the Help menu.</p>' .
            '<p>AiravataClientException: ' . $ace->getMessage() . '</p>');
    }
    catch (AiravataSystemException $ase)
    {
        print_error_message('<p>There was a problem launching the experiment.
            Please try again later or submit a bug report using the link in the Help menu.</p>' .
            '<p>AiravataSystemException: ' . $ase->getMessage() . '</p>');
    }
    catch (Exception $e)
    {
        print_error_message('<p>There was a problem launching the experiment.
            Please try again later or submit a bug report using the link in the Help menu.</p>' .
            '<p>Exception: ' . $e->getMessage() . '</p>');
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
        print_error_message('<p>There was a problem getting the user\'s projects.
            Please try again later or submit a bug report using the link in the Help menu.</p>' .
            '<p>InvalidRequestException: ' . $ire->getMessage(). '</p>');
    }
    catch (AiravataClientException $ace)
    {
        print_error_message('<p>There was a problem getting the user\'s projects.
            Please try again later or submit a bug report using the link in the Help menu.</p>' .
            '<p>Airavata Client Exception: ' . $ace->getMessage(). '</p>');
    }
    catch (AiravataSystemException $ase)
    {
        if ($ase->airavataErrorType == 2) // 2 = INTERNAL_ERROR
        {
            print_warning_message('<p>You must create a project before you can create an experiment.
                Click <a href="create_project.php">here</a> to create a project.</p>');
        }
        else
        {
            print_error_message('<p>There was a problem getting the user\'s projects.
            Please try again later or submit a bug report using the link in the Help menu.</p>' .
            '<p>AiravataSystemException: ' . $ase->getMessage() . '</p>');
        }
    }

    return $userProjects;
}


/**
 * Get all available applications
 * @return null
 */
function get_all_applications()
{
    global $airavataclient;
    $applications = null;

    try
    {
        $applications = $airavataclient->getAllApplicationInterfaceNames();
    }
    catch (InvalidRequestException $ire)
    {
        print_error_message('<p>There was a problem getting all applications.
            Please try again later or submit a bug report using the link in the Help menu.</p>' .
            '<p>InvalidRequestException: ' . $ire->getMessage() . '</p>');
    }
    catch (AiravataClientException $ace)
    {
        print_error_message('<p>There was a problem getting all applications.
            Please try again later or submit a bug report using the link in the Help menu.</p>' .
            '<p>Airavata Client Exception: ' . $ace->getMessage() . '</p>');
    }
    catch (AiravataSystemException $ase)
    {
        print_error_message('<p>There was a problem getting all applications.
            Please try again later or submit a bug report using the link in the Help menu.</p>' .
            '<p>Airavata System Exception: ' . $ase->getMessage() . '</p>');
    }

    return $applications;
}


/**
 * Get the interface for the application with the given ID
 * @param $id
 * @return null
 */
function get_application_interface($id)
{
    global $airavataclient;
    $applicationInterface = null;

    try
    {
        $applicationInterface = $airavataclient->getApplicationInterface($id);
    }
    catch (InvalidRequestException $ire)
    {
        print_error_message('<p>There was a problem getting the application interface.
            Please try again later or submit a bug report using the link in the Help menu.</p>' .
            '<p>InvalidRequestException: ' . $ire->getMessage(). '</p>');
    }
    catch (AiravataClientException $ace)
    {
        print_error_message('<p>There was a problem getting the application interface.
            Please try again later or submit a bug report using the link in the Help menu.</p>' .
            '<p>Airavata Client Exception: ' . $ace->getMessage() . '</p>');
    }
    catch (AiravataSystemException $ase)
    {
        print_error_message('<p>There was a problem getting the application interface.
            Please try again later or submit a bug report using the link in the Help menu.</p>' .
            '<p>Airavata System Exception: ' . $ase->getMessage() . '</p>');
    }

    return $applicationInterface;
}


/**
 * Get a list of compute resources available for the given application ID
 * @param $id
 * @return null
 */
function get_available_app_interface_compute_resources($id)
{
    global $airavataclient;
    $computeResources = null;

    try
    {
        $computeResources = $airavataclient->getAvailableAppInterfaceComputeResources($id);
    }
    catch (InvalidRequestException $ire)
    {
        print_error_message('<p>There was a problem getting compute resources.
            Please try again later or submit a bug report using the link in the Help menu.</p>' .
            '<p>InvalidRequestException: ' . $ire->getMessage() . '</p>');
    }
    catch (AiravataClientException $ace)
    {
        print_error_message('<p>There was a problem getting compute resources.
            Please try again later or submit a bug report using the link in the Help menu.</p>' .
            '<p>Airavata Client Exception: ' . $ace->getMessage() . '</p>');
    }
    catch (AiravataSystemException $ase)
    {
        print_error_message('<p>There was a problem getting compute resources.
            Please try again later or submit a bug report using the link in the Help menu.</p>' .
            '<p>Airavata System Exception: ' . $ase->getMessage() . '</p>');
    }

    return $computeResources;
}


/**
 * Get the ComputeResourceDescription with the given ID
 * @param $id
 * @return null
 */
function get_compute_resource($id)
{
    global $airavataclient;
    $computeResource = null;

    try
    {
        $computeResource = $airavataclient->getComputeResource($id);
    }
    catch (InvalidRequestException $ire)
    {
        print_error_message('<p>There was a problem getting the compute resource.
            Please try again later or submit a bug report using the link in the Help menu.</p>' .
            '<p>InvalidRequestException: ' . $ire->getMessage() . '</p>');
    }
    catch (AiravataClientException $ace)
    {
        print_error_message('<p>There was a problem getting the compute resource.
            Please try again later or submit a bug report using the link in the Help menu.</p>' .
            '<p>Airavata Client Exception: ' . $ace->getMessage() . '</p>');
    }
    catch (AiravataSystemException $ase)
    {
        print_error_message('<p>There was a problem getting the compute resource.
            Please try again later or submit a bug report using the link in the Help menu.</p>' .
            '<p>Airavata System Exception: ' . $ase->getMessage() . '</p>');
    }

    return $computeResource;
}


/**
 * List the experiment's input files
 * @param $experiment
 */
function list_input_files($experiment)
{
    $applicationInputs = get_application_inputs($experiment->applicationId);

    $experimentInputs = $experiment->experimentInputs;



    foreach ($experimentInputs as $input)
    {
        $matchingAppInput = null;
        foreach($applicationInputs as $applicationInput)
        {
            if ($input->key == $applicationInput->name)
            {
                $matchingAppInput = $applicationInput;
            }
        }
        //var_dump($matchingAppInput);

        if ($matchingAppInput->type == DataType::URI)
        {
            $explode = explode('/', $input->value);
            //echo '<p><a href="' . $input->value . '">' . $input->key . '</a></p>';
            echo '<p><a target="_blank"
                        href="' . EXPERIMENT_DATA_ROOT . $explode[sizeof($explode)-2] . '/' . $explode[sizeof($explode)-1] . '">' .
                $input->key . '
                <span class="glyphicon glyphicon-new-window"></span></a></p>';
            //echo $input->value . '<br>';
            //echo str_replace(EXPERIMENT_DATA_ROOT_ABSOLUTE, EXPERIMENT_DATA_ROOT, $input->value) . '<br>';
            //echo dirname($input->value) . '<br>';


            //var_dump($explode);
            //echo sizeof($explode) . '<br>';
            //echo EXPERIMENT_DATA_ROOT . $explode[sizeof($explode)-2] . '/' . $explode[sizeof($explode)-1] . '<br>';
        }
        elseif ($matchingAppInput->type == DataType::STRING)
        {
            //$valueExplode = explode('=', $input->value);
            echo '<p>' . $input->key . ': ' . $input->value . '</p>';
        }
    }
}


/**
 * Get a list of the inputs for the application with the given ID
 * @param $id
 * @return null
 */
function get_application_inputs($id)
{
    global $airavataclient;
    $inputs = null;

    try
    {
        $inputs = $airavataclient->getApplicationInputs($id);
    }
    catch (InvalidRequestException $ire)
    {
        print_error_message('<p>There was a problem getting application inputs.
            Please try again later or submit a bug report using the link in the Help menu.</p>' .
            '<p>InvalidRequestException: ' . $ire->getMessage() . '</p>');
    }
    catch (AiravataClientException $ace)
    {
        print_error_message('<p>There was a problem getting application inputs.
            Please try again later or submit a bug report using the link in the Help menu.</p>' .
            '<p>Airavata Client Exception: ' . $ace->getMessage() . '</p>');
    }
    catch (AiravataSystemException $ase)
    {
        print_error_message('<p>There was a problem getting application inputs.
            Please try again later or submit a bug report using the link in the Help menu.</p>' .
            '<p>Airavata System Exception: ' . $ase->getMessage() . '</p>');
    }

    return $inputs;
}


/**
 * Get a list of the outputs for the application with the given ID
 * @param $id
 * @return null
 */
function get_application_outputs($id)
{
    global $airavataclient;
    $outputs = null;

    try
    {
        $outputs = $airavataclient->getApplicationOutputs($id);
    }
    catch (InvalidRequestException $ire)
    {
        print_error_message('<p>There was a problem getting application outputs.
            Please try again later or submit a bug report using the link in the Help menu.</p>' .
            '<p>InvalidRequestException: ' . $ire->getMessage() . '</p>');
    }
    catch (AiravataClientException $ace)
    {
        print_error_message('<p>There was a problem getting application outputs.
            Please try again later or submit a bug report using the link in the Help menu.</p>' .
            '<p>Airavata Client Exception: ' . $ace->getMessage() . '</p>');
    }
    catch (AiravataSystemException $ase)
    {
        print_error_message('<p>There was a problem getting application outputs.
            Please try again later or submit a bug report using the link in the Help menu.</p>' .
            '<p>Airavata System Exception: ' . $ase->getMessage() . '</p>');
    }

    return $outputs;
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
        print_error_message('<p>There was a problem getting the experiment.
            Please try again later or submit a bug report using the link in the Help menu.</p>' .
            '<p>InvalidRequestException: ' . $ire->getMessage() . '</p>');
    }
    catch (ExperimentNotFoundException $enf)
    {
        print_error_message('<p>There was a problem getting the experiment.
            Please try again later or submit a bug report using the link in the Help menu.</p>' .
            '<p>ExperimentNotFoundException: ' . $enf->getMessage() . '</p>');
    }
    catch (AiravataClientException $ace)
    {
        print_error_message('<p>There was a problem getting the experiment.
            Please try again later or submit a bug report using the link in the Help menu.</p>' .
            '<p>AiravataClientException: ' . $ace->getMessage() . '</p>');
    }
    catch (AiravataSystemException $ase)
    {
        print_error_message('<p>There was a problem getting the experiment.
            Please try again later or submit a bug report using the link in the Help menu.</p>' .
            '<p>AiravataSystemException: ' . $ase->getMessage() . '</p>');
    }
    catch (TTransportException $tte)
    {
        print_error_message('<p>There was a problem getting the experiment.
            Please try again later or submit a bug report using the link in the Help menu.</p>' .
            '<p>TTransportException: ' . $tte->getMessage() . '</p>');
    }
    catch (Exception $e)
    {
        print_error_message('<p>There was a problem getting the experiment.
            Please try again later or submit a bug report using the link in the Help menu.</p>' .
            '<p>Exception: ' . $e->getMessage() . '</p>');
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
        print_error_message('<p>There was a problem getting the project.
            Please try again later or submit a bug report using the link in the Help menu.</p>' .
            '<p>InvalidRequestException: ' . $ire->getMessage() . '</p>');
    }
    catch (AiravataClientException $ace)
    {
        print_error_message('<p>There was a problem getting the project.
            Please try again later or submit a bug report using the link in the Help menu.</p>' .
            '<p>AiravataClientException: ' . $ace->getMessage() . '</p>');
    }
    catch (AiravataSystemException $ase)
    {
        print_error_message('<p>There was a problem getting the project.
            Please try again later or submit a bug report using the link in the Help menu.</p>' .
            '<p>AiravataSystemException!<br><br>' . $ase->getMessage() . '</p>');
    }

}


/**
 * Create and configure a new Experiment
 * @return Experiment
 */
function assemble_experiment()
{
    //$experimentAssemblySuccessful = true; // errors will set this to false
    //$experimentPath = EXPERIMENT_DATA_ROOT;
    $experimentInputs = array();
    //$experimentOutputs = array();

    $scheduling = new ComputationalResourceScheduling();
    $scheduling->totalCPUCount = $_POST['cpu-count'];
    $scheduling->nodeCount = $_POST['node-count'];
    //$scheduling->numberOfThreads = $_POST['threads'];
    $scheduling->queueName = 'normal';
    $scheduling->wallTimeLimit = $_POST['wall-time'];
    //$scheduling->totalPhysicalMemory = $_POST['memory'];
    $scheduling->resourceHostId = $_POST['compute-resource'];



    $userConfigData = new UserConfigurationData();
    $userConfigData->computationalResourceScheduling = $scheduling;



    $applicationInputs = get_application_inputs($_POST['application']);
    $experimentInputs = process_inputs($applicationInputs, $experimentInputs);
    //var_dump($experimentInputs);

    global $experimentPath, $pathConstant;
    if($experimentPath != null){
        $advHandling = new AdvancedOutputDataHandling();
        //echo($experimentPath);
        $advHandling->outputDataDir = str_replace(EXPERIMENT_DATA_ROOT, $pathConstant , $experimentPath);
        $userConfigData->advanceOutputDataHandling = $advHandling;
    }





    /*
    $applicationOutputs = get_application_outputs($_POST['application']);
    $experimentOutputs = array();

    foreach ($applicationOutputs as $applicationOutput)
    {
        $experimentOutput = new DataObjectType();
        $experimentOutput->key = $applicationOutput->name;
        $experimentOutput->type = $applicationOutput->type;
        $experimentOutput->value = '';

        $experimentOutputs[] = $experimentOutput;
    }
    */










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

    //$experiment->experimentOutputs = $experimentOutputs;


    if ($experimentInputs)
    {
        return $experiment;
    }
}

/**
 * @param $applicationInputs
 * @param $experimentInputs
 * @internal param $environmentPath
 * @return array
 */
function process_inputs($applicationInputs, $experimentInputs)
{
    global $experimentPath;
    $experimentAssemblySuccessful = true;
    $newExperimentInputs = array();

    //var_dump($_FILES);

    if (sizeof($_FILES) > 0)
    {
        if (file_upload_successful())
        {
            // construct unique path
            do
            {
                $experimentPath = EXPERIMENT_DATA_ROOT . str_replace(' ', '', $_SESSION['username']) . md5(rand() * time()) . '/';
            }
            while (is_dir($experimentPath)); // if dir already exists, try again

            // create upload directory
            if (!mkdir($experimentPath))
            {
                print_error_message('<p>Error creating upload directory!
                    Please try again later or report a bug using the link in the Help menu.</p>');
                $experimentAssemblySuccessful = false;
            }
        }
        else
        {
            $experimentAssemblySuccessful = false;
        }
    }

    foreach ($applicationInputs as $applicationInput)
    {
        $experimentInput = new DataObjectType();
        $experimentInput->key = $applicationInput->name;
        $experimentInput->metaData = $applicationInput->metaData;


        //$experimentInput->type = $applicationInput->type;
        //$experimentInput->type = DataType::STRING;


        if(($applicationInput->type == DataType::STRING) ||
            ($applicationInput->type == DataType::INTEGER) ||
            ($applicationInput->type == DataType::FLOAT))
        {
            if (isset($_POST[$applicationInput->name]) && (trim($_POST[$applicationInput->name]) != ''))
            {
                $experimentInput->value = $_POST[$applicationInput->name];
            }
            else // use previous value
            {
                $index = -1;
                for ($i = 0; $i < sizeof($experimentInputs); $i++)
                {
                    if ($experimentInputs[$i]->key == $applicationInput->name)
                    {
                        $index = $i;
                    }
                }

                if ($index >= 0)
                {
                    $experimentInput->value = $experimentInputs[$index]->value;
                }
            }
        }
        elseif ($applicationInput->type == DataType::URI)
        {
            //var_dump($_FILES[$applicationInput->name]->name);
            if ($_FILES[$applicationInput->name]['name'])
            {
                $file = $_FILES[$applicationInput->name];


                //
                // move file to experiment data directory
                //
                $filePath = $experimentPath . $file['name'];

                // check if file already exists
                if (is_file($filePath))
                {
                    unlink($filePath);

                    print_warning_message('Uploaded file already exists! Overwriting...');
                }

                $moveFile = move_uploaded_file($file['tmp_name'], $filePath);

                if ($moveFile)
                {
                    print_success_message('Upload: ' . $file['name'] . '<br>' .
                        'Type: ' . $file['type'] . '<br>' .
                        'Size: ' . ($file['size']/1024) . ' kB');//<br>' .
                        //'Stored in: ' . $experimentPath . $file['name']);
                }
                else
                {
                    print_error_message('<p>Error moving uploaded file ' . $file['name'] . '!
                    Please try again later or report a bug using the link in the Help menu.</p>');
                    $experimentAssemblySuccessful = false;
                }

                global $pathConstant;

                $experimentInput->value = str_replace(EXPERIMENT_DATA_ROOT, $pathConstant , $filePath);
            }
            else
            {
                $index = -1;
                for ($i = 0; $i < sizeof($experimentInputs); $i++)
                {
                    if ($experimentInputs[$i]->key == $applicationInput->name)
                    {
                        $index = $i;
                    }
                }

                if ($index >= 0)
                {
                    $experimentInput->value = $experimentInputs[$index]->value;
                }
            }

        }
        else
        {
            print_error_message('I cannot accept this input type yet!');
        }







        //$experimentInputs[] = $experimentInput;
        /*
        $index = -1;
        for ($i = 0; $i < sizeof($experimentInputs); $i++)
        {
            if ($experimentInputs[$i]->key == $experimentInput->key)
            {
                $index = $i;
            }
        }

        if ($index >= 0)
        {
            unset($experimentInputs[$index]);
        }
        */
        //$experimentInputs[] = $experimentInput;





        $newExperimentInputs[] = $experimentInput;


    }

    if ($experimentAssemblySuccessful)
    {
        return $newExperimentInputs;
    }
    else
    {
        return false;
    }

}


/**
 * Check the uploaded files for errors
 */
function file_upload_successful()
{
    $uploadSuccessful = true;

    foreach ($_FILES as $file)
    {
        //var_dump($file);
        if($file['name'])
        {
            if ($file['error'] > 0)
            {
                $uploadSuccessful = false;
                print_error_message('<p>Error uploading file ' . $file['name'] . ' !
                    Please try again later or report a bug using the link in the Help menu.');
            }/*
            elseif ($file['type'] != 'text/plain')
            {
                $uploadSuccessful = false;
                print_error_message('Uploaded file ' . $file['name'] . ' type not supported!');
            }
            elseif (($file['size'] / 1024) > 20)
            {
                $uploadSuccessful = false;
                print_error_message('Uploaded file ' . $file['name'] . ' must be smaller than 10 MB!');
            }*/
        }


    }

    return $uploadSuccessful;
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

        print_success_message("<p>Experiment updated!</p>" .
            '<p>Click
            <a href="experiment_summary.php?expId=' . $expId . '">here</a> to visit the experiment summary page.</p>');
    }
    catch (InvalidRequestException $ire)
    {
        print_error_message('<p>There was a problem updating the experiment.
            Please try again later or submit a bug report using the link in the Help menu.</p>' .
            '<p>InvalidRequestException: ' . $ire->getMessage() . '</p>');
    }
    catch (ExperimentNotFoundException $enf)
    {
        print_error_message('<p>There was a problem updating the experiment.
            Please try again later or submit a bug report using the link in the Help menu.</p>' .
            '<p>ExperimentNotFoundException: ' . $enf->getMessage() . '</p>');
    }
    catch (AiravataClientException $ace)
    {
        print_error_message('<p>There was a problem updating the experiment.
            Please try again later or submit a bug report using the link in the Help menu.</p>' .
            '<p>AiravataClientException: ' . $ace->getMessage() . '</p>');
    }
    catch (AiravataSystemException $ase)
    {
        print_error_message('<p>There was a problem updating the experiment.
            Please try again later or submit a bug report using the link in the Help menu.</p>' .
            '<p>AiravataSystemException: ' . $ase->getMessage() . '</p>');
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

        $cloneId = $airavataclient->cloneExperiment($expId, 'Clone of ' . $experiment->name);

        print_success_message("<p>Experiment cloned!</p>" .
            '<p>You will be redirected to the edit page shortly, or you can
            <a href="edit_experiment.php?expId=' . $cloneId . '">go directly</a> to the edit experiment page.</p>');
        redirect('edit_experiment.php?expId=' . $cloneId);
    }
    catch (InvalidRequestException $ire)
    {
        print_error_message('<p>There was a problem cloning the experiment.
            Please try again later or submit a bug report using the link in the Help menu.</p>' .
            '<p>InvalidRequestException: ' . $ire->getMessage() . '</p>');
    }
    catch (ExperimentNotFoundException $enf)
    {
        print_error_message('<p>There was a problem cloning the experiment.
            Please try again later or submit a bug report using the link in the Help menu.</p>' .
            '<p>ExperimentNotFoundException: ' . $enf->getMessage() . '</p>');
    }
    catch (AiravataClientException $ace)
    {
        print_error_message('<p>There was a problem cloning the experiment.
            Please try again later or submit a bug report using the link in the Help menu.</p>' .
            '<p>AiravataClientException: ' . $ace->getMessage() . '</p>');
    }
    catch (AiravataSystemException $ase)
    {
        print_error_message('<p>There was a problem cloning the experiment.
            Please try again later or submit a bug report using the link in the Help menu.</p>' .
            '<p>AiravataSystemException: ' . $ase->getMessage() . '</p>');
    }
    catch (TTransportException $tte)
    {
        print_error_message('<p>There was a problem cloning the experiment.
            Please try again later or submit a bug report using the link in the Help menu.</p>' .
            '<p>TTransportException: ' . $tte->getMessage() . '</p>');
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
        print_error_message('<p>There was a problem canceling the experiment.
            Please try again later or submit a bug report using the link in the Help menu.</p>' .
            '<p>InvalidRequestException: ' . $ire->getMessage() . '</p>');
    }
    catch (ExperimentNotFoundException $enf)
    {
        print_error_message('<p>There was a problem canceling the experiment.
            Please try again later or submit a bug report using the link in the Help menu.</p>' .
            '<p>ExperimentNotFoundException: ' . $enf->getMessage() . '</p>');
    }
    catch (AiravataClientException $ace)
    {
        print_error_message('<p>There was a problem canceling the experiment.
            Please try again later or submit a bug report using the link in the Help menu.</p>' .
            '<p>AiravataClientException: ' . $ace->getMessage() . '</p>');
    }
    catch (AiravataSystemException $ase)
    {
        print_error_message('<p>There was a problem canceling the experiment.
            Please try again later or submit a bug report using the link in the Help menu.</p>' .
            '<p>AiravataSystemException: ' . $ase->getMessage() . '</p>');
    }
    catch (TTransportException $tte)
    {
        print_error_message('<p>There was a problem canceling the experiment.
            Please try again later or submit a bug report using the link in the Help menu.</p>' .
            '<p>TTransportException: ' . $tte->getMessage() . '</p>');
    }
    catch (Exception $e)
    {
        print_error_message('<p>There was a problem canceling the experiment.
            Please try again later or submit a bug report using the link in the Help menu.</p>' .
            '<p>Exception: ' . $e->getMessage() . '</p>');
    }
}


/**
 * Create a select input and populate it with project options from the database
 */
function create_project_select($projectId = null, $editable = true)
{
    $editable? $disabled = '' : $disabled = 'disabled';
    $userProjects = get_all_user_projects($_SESSION['username']);

    if (sizeof($userProjects) > 0)
    {
        echo '<select class="form-control" name="project" id="project" required ' . $disabled . '>';

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

            echo '<option value="' . $project->projectID . '" ' . $selected . '>' . $project->name . '</option>';
        }

        echo '</select>';
    }
}


/**
 * Create a select input and populate it with applications options
 * @param null $id
 * @param bool $editable
 */
function create_application_select($id = null, $editable = true)
{
    $disabled = $editable? '' : 'disabled';

    $applicationIds = get_all_applications();

    echo '<select class="form-control" name="application" id="application" required ' . $disabled . '>';

    foreach ($applicationIds as $applicationId => $applicationName)
    {
        $selected = ($applicationId == $id) ? 'selected' : '';

        echo '<option value="' . $applicationId . '" ' . $selected . '>' . $applicationName . '</option>';
    }

    echo '</select>';
}


/**
 * Create a select input and populate it with compute resources
 * available for the given application ID
 * @param $applicationId
 * @param $resourceHostId
 */
function create_compute_resources_select($applicationId, $resourceHostId)
{
    $computeResources = get_available_app_interface_compute_resources($applicationId);

    echo '<select class="form-control" name="compute-resource" id="compute-resource">';

    foreach ($computeResources as $id => $name)
    {
        $selected = ($resourceHostId == $id)? ' selected' : '';

        echo '<option value="' . $id . '"' . $selected . '>' .
                $name . '</option>';

    }

    echo '</select>';
}


/**
 * Create form inputs to accept the inputs to the given application
 * @param $id
 * @param $isRequired
 * @internal param $required
 */
function create_inputs($id, $isRequired)
{
    $inputs = get_application_inputs($id);

    $required = $isRequired? ' required' : '';

    foreach ($inputs as $input)
    {
        /*
        echo '<p>DataType::STRING = ' . \Airavata\Model\AppCatalog\AppInterface\DataType::STRING . '</p>';
        echo '<p>DataType::INTEGER = ' . \Airavata\Model\AppCatalog\AppInterface\DataType::INTEGER . '</p>';
        echo '<p>DataType::FLOAT = ' . \Airavata\Model\AppCatalog\AppInterface\DataType::FLOAT . '</p>';
        echo '<p>DataType::URI = ' . \Airavata\Model\AppCatalog\AppInterface\DataType::URI . '</p>';

        echo '<p>$input->type = ' . $input->type . '</p>';
        */

        switch ($input->type)
        {
            case DataType::STRING:
                echo '<div class="form-group">
                    <label for="experiment-input">' . $input->name . '</label>
                    <input type="text" class="form-control" name="' . $input->name .
                    '" id="' . $input->name .
                    '" placeholder="' . $input->userFriendlyDescription . '"' . $required . '>
                    </div>';
                break;
            case DataType::INTEGER:
            case DataType::FLOAT:
                echo '<div class="form-group">
                    <label for="experiment-input">' . $input->name . '</label>
                    <input type="number" class="form-control" name="' . $input->name .
                    '" id="' . $input->name .
                    '" placeholder="' . $input->userFriendlyDescription . '"' . $required . '>
                    </div>';
                break;
            case DataType::URI:
                echo '<div class="form-group">
                    <label for="experiment-input">' . $input->name . '</label>
                    <input type="file" class="" name="' . $input->name .
                    '" id="' . $input->name . '" ' . $required . '>
                    <p class="help-block">' . $input->userFriendlyDescription . '</p>
                    </div>';
                break;
            default:
                print_error_message('Input data type not supported!
                    Please file a bug report using the link in the Help menu.');
                break;
        }
    }
}


/**
 * Create navigation bar
 * Used for all pages
 */
function create_nav_bar()
{
    $menus = array
    (
        'Project' => array
        (
            array('label' => 'Create Project', 'url' => 'create_project.php'),
            array('label' => 'Search Projects', 'url' => 'search_projects.php')
        ),
        'Experiment' => array
        (
            array('label' => 'Create Experiment', 'url' => 'create_experiment.php'),
            array('label' => 'Search Experiments', 'url' => 'search_experiments.php')
        ),
        'Help' => array
        (
            array('label' => 'Report Issue', 'url' => '#'),
            array('label' => 'Request Feature', 'url' => '#')
        )
    );

    $selfExplode = explode('/', $_SERVER['PHP_SELF']);



    // nav bar and left-aligned content

    echo '<nav class="navbar navbar-default navbar-static-top" role="navigation">
            <div class="container-fluid">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                       <span class="sr-only">Toggle navigation</span>
                       <span class="icon-bar"></span>
                       <span class="icon-bar"></span>
                       <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="index.php" title="PHP Gateway with Airavata">PGA</a>
                </div>

                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav">';


    foreach ($menus as $label => $options)
    {
        isset($_SESSION['loggedin']) && $_SESSION['loggedin']? $disabled = '' : $disabled = ' class="disabled"';

        echo '<li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">' . $label . '<span class="caret"></span></a>
                <ul class="dropdown-menu" role="menu">';

        foreach ($options as $option)
        {
            $id = strtolower(str_replace(' ', '-', $option['label']));

            $option['url'] == $selfExplode[2]? $active = ' class="active"' : $active = '';

            echo '<li' . $active . $disabled . '><a href="' . $option['url'] . '" id=' . $id . '>' . $option['label'] . '</a></li>';
        }

        echo '</ul>
        </li>';
    }


    echo '</ul>

        <ul class="nav navbar-nav navbar-right">';





    // right-aligned content

    if (isset($_SESSION['username']))
    {
        (USER_STORE === "USER_API" && !isset($_SESSION['excede_login'])) ? $link = "user_profile.php" : $link = "index.php";
        echo '<li><a href="' . $link . '"><span class="glyphicon glyphicon-user"></span> ' . $_SESSION['username'] . '</a></li>';
    }

    if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'])
    {
        echo '<li><a href="logout.php"><span class="glyphicon glyphicon-log-out"></span> Log out</a></li>';
    }
    elseif ($selfExplode[2] == 'login.php')
    {
        echo '<li><a href="create_account.php"><span class="glyphicon glyphicon-user"></span> Create account</a></li>';
    }
    elseif ($selfExplode[2] == 'create_account.php')
    {
        echo '<li><a href="login.php"><span class="glyphicon glyphicon-log-in"></span> Log in</a></li>';
    }
    elseif ($selfExplode[2] == 'index.php')
    {
        echo '<li><a href="create_account.php"><span class="glyphicon glyphicon-user"></span> Create account</a></li>';
        echo '<li><a href="login.php"><span class="glyphicon glyphicon-log-in"></span> Log in</a></li>';
    }


    echo    '</ul>
    </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
    </nav>';
}

/**
* Add attributes to the HTTP header.
*/
function create_http_header()
{
   header( 'Cache-Control: no-store, no-cache, must-revalidate' );
   header( 'Cache-Control: post-check=0, pre-check=0', false );
   header( 'Pragma: no-cache' );
}

/**
 * Create head tag
 * Used for all pages
 */
function create_html_head() 
{
    echo'
        <head>
            <title>PHP Reference Gateway</title>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <link rel="icon" href="resources/assets/favicon.ico" type="image/x-icon">
            <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
            <script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>

            <!-- Jira Issue Collector - Report Issue -->
            <script type="text/javascript"
                    src="https://gateways.atlassian.net/s/31280375aecc888d5140f63e1dc78a93-T/en_USmlc07/6328/46/1.4.13/_/download/batch/com.atlassian.jira.collector.plugin.jira-issue-collector-plugin:issuecollector/com.atlassian.jira.collector.plugin.jira-issue-collector-plugin:issuecollector.js?locale=en-US&collectorId=b1572922"></script>

            <!-- Jira Issue Collector - Request Feature -->
            <script type="text/javascript"
                src="https://gateways.atlassian.net/s/31280375aecc888d5140f63e1dc78a93-T/en_USmlc07/6328/46/1.4.13/_/download/batch/com.atlassian.jira.collector.plugin.jira-issue-collector-plugin:issuecollector/com.atlassian.jira.collector.plugin.jira-issue-collector-plugin:issuecollector.js?locale=en-US&collectorId=674243b0"></script>


            <script type="text/javascript">
                window.ATL_JQ_PAGE_PROPS = $.extend(window.ATL_JQ_PAGE_PROPS, {
                    "b1572922":
                    {
                        "triggerFunction": function(showCollectorDialog) {
                            //Requries that jQuery is available!
                            jQuery("#report-issue").click(function(e) {
                                e.preventDefault();
                                showCollectorDialog();
                            });
                        }
                    },
                    "674243b0":
                    {
                        "triggerFunction": function(showCollectorDialog) {
                            //Requries that jQuery is available!
                            jQuery("#request-feature").click(function(e) {
                                e.preventDefault();
                                showCollectorDialog();
                            });
                        }
                    }
                });
            </script>

        </head>
    ';
}


/**
 * Open the XML file containing the community token
 * @param $tokenFilePath
 * @throws Exception
 */
function open_tokens_file($tokenFilePath)
{
    global $tokenFile;

    if (file_exists($tokenFilePath))
    {
        $tokenFile = simplexml_load_file($tokenFilePath);
    }
    else
    {
        throw new Exception('Error: Cannot connect to tokens database!');
    }


    if (!$tokenFile)
    {
        throw new Exception('Error: Cannot open tokens database!');
    }
}

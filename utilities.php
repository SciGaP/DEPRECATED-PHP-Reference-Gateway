<?php
/**
 * Basic utility functions
 */

define('ROOT_DIR', __DIR__);

/**
 * Define configuration constants
 */
const AIRAVATA_SERVER = 'gw111.iu.xsede.org';
const AIRAVATA_PORT = 8930;
const AIRAVATA_TIMEOUT = 20000;
const EXPERIMENT_DATA_ROOT = '../experimentData/';
const EXPERIMENT_DATA_ROOT_ABSOLUTE = '/var/www/experimentData/';

//const USER_STORE = 'XML';
const USER_STORE = 'WSO2';



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

    switch (USER_STORE)
    {
        case 'WSO2':
            $idStore = new WSISUtilities(); // WS02 Identity Server
            break;
        case 'XML':
            $idStore = new XmlIdUtilities(); // XML user database
            break;
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

    $transport = new TSocket(AIRAVATA_SERVER, AIRAVATA_PORT);
    $transport->setRecvTimeout(AIRAVATA_TIMEOUT);

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
    $uploadSuccessful = true; // errors will set this to false
    $experimentInputs = array();
    $experimentOutputs = array();

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
        case 'lonestar.tacc.utexas.edu':
            $scheduling->ComputationalProjectAccount = 'TG-STA110014S';
            break;
        default:
            $scheduling->ComputationalProjectAccount = 'admin';
    }


    $userConfigData = new UserConfigurationData();
    $userConfigData->computationalResourceScheduling = $scheduling;
    $userConfigData->overrideManualScheduledParams = 0;
    $userConfigData->airavataAutoSchedule = 0;










    if ($_POST['application'] == 'WRF')
    {
        foreach ($_FILES as $file)
        {
            if ($file['error'] > 0)
            {
                $uploadSuccessful = false;
                print_error_message('Error uploading file ' . $file['name'] . ' !');
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








        if ($uploadSuccessful)
        {
            // construct unique path
            do
            {
                $experimentPath = EXPERIMENT_DATA_ROOT . $_POST['experiment-name'] . md5(rand() * time()) . '/';
            }
            while (is_dir($experimentPath)); // if dir already exists, try again

            // create new directory
            // move file to new directory, overwriting old versions if necessary
            if (mkdir($experimentPath))
            {
                foreach ($_FILES as $file)
                {
                    $filePath = $experimentPath . $file['name'];

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
                            'Size: ' . ($file['size']/1024) . ' kB<br>' .
                            'Stored in: ' . $experimentPath . $file['name']);
                    }
                    else
                    {
                        print_error_message('Error moving uploaded file ' . $file['name'] . '!');
                    }



                    // wrf
                    $experimentInput = new DataObjectType();

                    if ($file == $_FILES['namelist'])
                    {
                        $experimentInput->key = 'WRF_Namelist';
                        //$experimentInput->value = '/home/airavata/wrf/namelist.input';
                    }
                    elseif ($file == $_FILES['model-init'])
                    {
                        $experimentInput->key = 'WRF_Input_File';
                        //$experimentInput->value = '/home/airavata/wrf/wrfinput_d01';
                    }
                    elseif ($file == $_FILES['bounds'])
                    {
                        $experimentInput->key = 'WRF_Boundary_File';
                        //$experimentInput->value = '/home/airavata/wrf/wrfbdy_d01';
                    }

                    //echo $filePath . '<br>';
                    //echo 'realpath: ' . realpath($filePath) . '<br>';
                    //echo 'str_replace: ' . str_replace(EXPERIMENT_DATA_ROOT, EXPERIMENT_DATA_ROOT_ABSOLUTE, $filePath) . '<br>';
                    //echo str_replace(dirname(realpath($filePath)), EXPERIMENT_DATA_ROOT, realpath($filePath)) . '<br>';

                    //$experimentInput->value = $filePath;
                    $experimentInput->value = str_replace(EXPERIMENT_DATA_ROOT, EXPERIMENT_DATA_ROOT_ABSOLUTE, $filePath);
                    $experimentInput->type = DataType::URI;
                    $experimentInputs[] = $experimentInput; // push into array

                    //Configuring WRF Outputs
                    $experimentOutput1 = new DataObjectType();
                    $experimentOutput1->key = 'WRF_Output';
                    $experimentOutput1->value = '';
                    $experimentOutput1->type = DataType::URI;

                    $experimentOutput2 = new DataObjectType();
                    $experimentOutput2->key = 'WRF_Execution_Log';
                    $experimentOutput2->value = '';
                    $experimentOutput2->type = DataType::URI;

                    $experimentOutputs = array($experimentOutput1, $experimentOutput2);
                }
            }
            else
            {
                print_error_message('Error creating upload directory!');
            }


        }
    }
    else // echo
    {
        //Echo Inputs
        $experimentInput = new DataObjectType();
        $experimentInput->key = 'echo_input';
        $experimentInput->value = 'echo_output=' . $_POST['experiment-input'];
        $experimentInput->type = DataType::STRING;
        $experimentInputs = array($experimentInput);

        //Echo Outputs
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
    }




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


    if ($uploadSuccessful)
    {
        return $experiment;
    }
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

        $airavataclient->cloneExperiment($expId, $experiment->name .= time());

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
    $editable? $disabled = '' : $disabled = 'disabled';

    echo '<select class="form-control" name="project" id="project" required ' . $disabled . '>';

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

        echo '<option value="' . $project->projectID . '" ' . $selected . '>' . $project->name . '</option>';
    }

    echo '</select>';
}

/**
 * Create navigation bar
 * Used for all pages
 */
function create_nav_bar()
{
    $labels = array('Create project',
        'Create experiment',
        'Browse',
        'Search experiments',
        'Search projects');
    $urls = array('create_project.php',
        'create_experiment.php',
        'browse_experiments.php',
        'search_experiments.php',
        'search_projects.php');

    $selfExplode = explode('/', $_SERVER['PHP_SELF']);

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
                    <a class="navbar-brand" href="home.php">PHP Reference Gateway</a>
                </div>

                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav">';

    for ($i = 0; $i < sizeof($labels); $i++)
    {
        $urls[$i] == $selfExplode[2]? $active = ' class="active"' : $active = '';


        isset($_SESSION['loggedin']) && $_SESSION['loggedin']? $disabled = '' : $disabled = ' class="disabled"';

        echo '<li' . $active . $disabled . '><a href="' . $urls[$i] . '">' . $labels[$i] . '</a></li>';
    }



    echo '</ul>

        <ul class="nav navbar-nav navbar-right">';




    if (isset($_SESSION['username']))
    {
        echo '<li><a href="home.php">' . $_SESSION['username'] . '</a></li>';
    }

    if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'])
    {
        echo '<li><a href="logout.php">Log out</a></li>';
    }
    elseif ($selfExplode[2] == 'login.php')
    {
        echo '<li><a href="create_account.php">Create account</a></li>';
    }
    elseif ($selfExplode[2] == 'create_account.php')
    {
        echo '<li><a href="login.php">Log in</a></li>';
    }


    echo    '</ul>
    </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
    </nav>';
}

/**
 * Create head tag
 * Used for all pages
 */
function create_head()
{
    echo'
        <head>
            <title>PHP Reference Gateway</title>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
            <script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
        </head>
    ';
}

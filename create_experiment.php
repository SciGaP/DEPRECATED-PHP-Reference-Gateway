<?php
require 'check_login.php';

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
require_once $GLOBALS['AIRAVATA_ROOT'] . 'API/Error/Types.php';

use Airavata\Model\Workspace\Experiment\ComputationalResourceScheduling;
use Airavata\Model\Workspace\Experiment\DataObjectType;
use Airavata\Model\Workspace\Experiment\UserConfigurationData;
use Thrift\Protocol\TBinaryProtocol;
use Thrift\Transport\TSocket;
use Airavata\API\AiravataClient;
use Airavata\Model\Workspace\Experiment\Experiment;
use Airavata\Model\Workspace\Experiment\ExperimentState;



//checking if the user is logged in
if($logged_in == false)//user not logged in, redirect him to the login page
{
    echo 'User not logged in!';
    echo '<meta http-equiv="Refresh" content="0; URL=login.php">';
}


$transport = new TSocket('gw111.iu.xsede.org', 8930);
$protocol = new TBinaryProtocol($transport);

$airavataclient = new AiravataClient($protocol);
$transport->open();

?>

<html>
<head>
    <title>Create Experiment</title>
</head>


<body>

<div><h1>Create Experiment</h1></div>


<ul id="nav">
    <li><a href="home.php">Home</a></li>
    <li><a href="create_experiment.php">Create experiment</a></li>
    <li><a href="manage_experiments.php">Manage experiments</a></li>
    <li><a href="logout.php">Log out</a></li>
</ul>




<?php
if (isset($_POST['clear']))
{
    echo '<div>Values cleared!</div>';
}
if (isset($_POST['save']) || isset($_POST['launch']))
{
    $experiment = new Experiment();
    $experiment->name = $_POST['experiment-name'];
    $experiment->description = $_POST['experiment-description'];
    $experiment->userName = $_SESSION['username'];
    $experiment->projectID = $_POST['project'];
    $experiment->applicationId = $_POST['application'];

    $experimentInputs = new DataObjectType();
    $experimentInputs->key = 'input';
    $experimentInputs->value = 'file:///home/airavata/input/hpcinput.tar';
    $experiment->experimentInputs = array($experimentInputs);

    $experimentOutput1 = new DataObjectType();
    $experimentOutput1->key = 'output';
    $experimentOutput1->value = '';

    $experimentOutput2 = new DataObjectType();
    $experimentOutput2->key = 'stdout';
    $experimentOutput2->value = '';

    $experimentOutput3 = new DataObjectType();
    $experimentOutput3->key = 'stderr';
    $experimentOutput3->value = '';

    $experiment->experimentOutputs = array($experimentOutput1, $experimentOutput2, $experimentOutput3);


    $scheduling = new ComputationalResourceScheduling();
    $scheduling->resourceHostId = 'gsissh-trestles';

    $userConfigData = new UserConfigurationData();
    $userConfigData->computationalResourceScheduling = $scheduling;
    $userConfigData->overrideManualScheduledParams = False;
    $userConfigData->airavataAutoSchedule = False;
    $experiment->userConfigurationData = $userConfigData;

    try
    {
        $expId = $airavataclient->createExperiment($experiment);
        if ($expId)
        {
            echo "<div>Experiment {$_POST['experiment-name']} created!</div>";
        }
        else
        {
            echo "<div>Error creating experiment {$_POST['experiment-name']}!</div>";
        }
    }
    catch (TException $texp)
    {
        print 'Exception: ' . $texp->getMessage()."\n";
    }
    catch (AiravataSystemException $ase)
    {
        print 'Airavata System Exception: ' . $ase->getMessage()."\n";
    }


    if (isset($_POST['launch']))
    {
        try
        {
            $airavataclient->launchExperiment($expId, "airavataToken");
            echo "<div>Launched experiment {$_POST['experiment-name']}</div>";
        }
        catch (TException $texp)
        {
            print 'Exception: ' . $texp->getMessage()."\n";
        }
        catch (AiravataSystemException $ase)
        {
            print 'Airavata System Exception: ' . $ase->getMessage()."\n";
        }
    }
}


$transport->close();
?>



<form action="<?php echo $_SERVER['PHP_SELF']?>" method="post">
    <div><label for="experiment-name">Experiment Name:</label><input type="text" name="experiment-name" id="experiment-name"></div>
    <div>
        <label for="project">Project:</label>
        <select name="project" id="project">
            <option value="project-1">Project 1</option>
            <option value="project-2">Project 2</option>
            <option value="project-3">Project 3</option>
        </select>
    </div>
    <div><label for="experiment-input">Experiment input:</label><input type="file" name="experiment-input" id="experiment-input"></div>
    <div>
        <label for="application">Application:</label>
        <select name="application" id="application">
            <option value="application-1">Application 1</option>
            <option value="application-2">Application 2</option>
            <option value="application-3">Application 3</option>
        </select>
    </div>

<!--<div><label for="compute-resource">Compute Resource:</label><input type="text" name="compute-resource" id="compute-resource"></div>-->
    <div>
        <label for="compute-resource">Compute Resource:</label>
        <select name="compute-resource" id="compute-resource">
            <option value="compute-resource-1">Compute Resource 1</option>
            <option value="compute-resource-2">Compute Resource 2</option>
            <option value="compute-resource-3">Compute Resource 3</option>
        </select>
    </div>




    <div><label for="cpu-count">CPU Count:</label><input type="text" name="cpu-count" id="cpu-count"></div>
    <div><label for="wall-time">Wall Time:</label><input type="text" name="wall-time" id="wall-time"></div>
    <div><label for="experiment-description">Experiment Description:</label><input type="text" name="experiment-description" id="experiment-description"></div>

    <input name="save" type="submit" value="Save">
    <input name="launch" type="submit" value="Save and Launch">
    <input name="clear" type="submit" value="Clear">
</form>


</body>
</html>


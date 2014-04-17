<?php
session_start();
include 'utilities.php';



use Airavata\Model\Workspace\Experiment\ComputationalResourceScheduling;
use Airavata\Model\Workspace\Experiment\DataObjectType;
use Airavata\Model\Workspace\Experiment\UserConfigurationData;
use Airavata\Model\Workspace\Experiment\Experiment;
use Airavata\API\Error\InvalidRequestException;
use Airavata\API\Error\AiravataClientException;
use Airavata\API\Error\AiravataSystemException;
use Airavata\API\Error\ExperimentNotFoundException;



connect_to_id_store();
verify_login();

$airavataclient = get_airavata_client();

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
    print_success_message('Values cleared!');
}
elseif (isset($_POST['save']) || isset($_POST['launch']))
{
    $experiment = assemble_experiment();

    try
    {
        $expId = $airavataclient->createExperiment($experiment);

        if ($expId)
        {
            print_success_message("Experiment {$_POST['experiment-name']} created!");
        }
        else
        {
            print_error_message("Error creating experiment {$_POST['experiment-name']}!");
        }
    }
    catch (InvalidRequestException $ire)
    {

    }
    catch (AiravataClientException $ace)
    {

    }
    catch (AiravataSystemException $ase)
    {

    }

    if (isset($_POST['launch']))
    {
        try
        {
            $airavataclient->launchExperiment($expId, "airavataToken");
            print_success_message("Launched experiment {$_POST['experiment-name']}!");
        }
        catch (InvalidRequestException $ire)
        {

        }
        catch (ExperimentNotFoundException $enf)
        {

        }
        catch (AiravataClientException $ace)
        {

        }
        catch (AiravataSystemException $ase)
        {

        }
    }
}

//$transport->close();

?>



<form action="<?php echo $_SERVER['PHP_SELF']?>" method="post">
    <div>
        <label for="experiment-name">Experiment Name:</label>
        <input type="text" name="experiment-name" id="experiment-name">
    </div>
    <!-- ultimately replace with results of getAllUserProjects() -->
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

    <div>
        <label for="compute-resource">Compute Resource:</label>
        <select name="compute-resource" id="compute-resource">
            <option value="compute-resource-1">Compute Resource 1</option>
            <option value="compute-resource-2">Compute Resource 2</option>
            <option value="compute-resource-3">Compute Resource 3</option>
        </select>
    </div>




    <div>
        <label for="cpu-count">CPU Count:</label>
        <input type="text" name="cpu-count" id="cpu-count">
    </div>
    <div>
        <label for="wall-time">Wall Time:</label>
        <input type="text" name="wall-time" id="wall-time">
    </div>
    <div>
        <label for="experiment-description">Experiment Description:</label>
        <input type="text" name="experiment-description" id="experiment-description">
    </div>

    <input name="save" type="submit" value="Save">
    <input name="launch" type="submit" value="Save and Launch">
    <input name="clear" type="submit" value="Clear">
</form>


</body>
</html>

<?php

function assemble_experiment()
{
    $experiment = new Experiment();

    // required
    $experiment->projectID = $_POST['project'];
    $experiment->userName = $_SESSION['username'];
    $experiment->name = $_POST['experiment-name'];

    // optional
    $experiment->description = $_POST['experiment-description'];
    $experiment->applicationId = $_POST['application'];

    $scheduling = new ComputationalResourceScheduling();
    $scheduling->resourceHostId = 'gsissh-trestles';

    $userConfigData = new UserConfigurationData();
    $userConfigData->computationalResourceScheduling = $scheduling;
    $userConfigData->overrideManualScheduledParams = False;
    $userConfigData->airavataAutoSchedule = False;
    $experiment->userConfigurationData = $userConfigData;

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


    return $experiment;
}
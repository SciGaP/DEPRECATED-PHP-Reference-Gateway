<?php
session_start();
include 'utilities.php';



use Airavata\Model\Workspace\Experiment\ComputationalResourceScheduling;
use Airavata\Model\Workspace\Experiment\DataObjectType;
use Airavata\Model\Workspace\Experiment\DataType;
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
    <title>PHP Reference Gateway</title>
    
    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
</head>


<body>
    
<nav class="navbar navbar-default navbar-static-top" role="navigation">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="#">PHP Reference Gateway</a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
        <li><a href="home.php">Home</a></li>
        <li class="active"><a href="create_experiment.php">Create experiment</a></li>
        <li><a href="manage_experiments.php">Manage experiments</a></li>    
      </ul>
      
      <ul class="nav navbar-nav navbar-right">
        <li><a href="logout.php">Log out</a></li>
      </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>


<div class="container">
    
<h3>Create a new experiment</h3>




<?php

if (isset($_POST['clear']))
{
    print_success_message('Values cleared!');
}
elseif (isset($_POST['save']) || isset($_POST['launch']))
{
    $expId = create_experiment();

    if (isset($_POST['launch']) && $expId)
    {
        launch_experiment('fdfsdfsdf');
    }
}

//$transport->close();

?>



<form action="<?php echo $_SERVER['PHP_SELF']?>" method="post" role="form">
    <div class="form-group">
        <label for="experiment-name">Experiment Name</label>
        <input type="text" class="form-control" name="experiment-name" id="experiment-name" placeholder="Enter experiment name" autofocus required>
    </div>
    <!-- ultimately replace with results of getAllUserProjects() -->
    <div class="form-group">
        <label for="project">Project</label>
        <?php create_project_select(); ?>
    </div>
    <div class="form-group bg-danger">
        <label for="experiment-input">Experiment input</label>
        <input type="file" name="experiment-input" id="experiment-input">
    </div>
    <div class="form-group">
        <label for="application">Application</label>
        <select class="form-control" name="application" id="application">
            <option value="SimpleEcho0">SimpleEcho0</option>
            <option value="SimpleEcho2">SimpleEcho2</option>
            <option value="SimpleEcho3">SimpleEcho3</option>
            <option value="SimpleEcho4">SimpleEcho4</option>
        </select>
    </div>
    <div class="form-group">
        <label for="compute-resource">Compute Resource</label>
        <select class="form-control" name="compute-resource" id="compute-resource">
            <option value="localhost">localhost</option>
            <option value="trestles.sdsc.edu">Trestles</option>
            <option value="stampede.tacc.xsede.org">Stampede</option>
            <option value="lonestar.tacc.utexas.edu">Lonestar</option>
        </select>
    </div>
    <div class="form-group">
        <label for="cpu-count">CPU Count</label>
        <input type="text" class="form-control" name="cpu-count" id="cpu-count" value="1">
    </div>
    <div class="form-group">
        <label for="wall-time">Wall Time</label>
        <input type="text" class="form-control" name="wall-time" id="wall-time" value="15">
    </div>
    <div class="form-group">
        <label for="experiment-description">Experiment Description</label>
        <textarea class="form-control" name="experiment-description" id="experiment-description" placeholder="Optional: Enter a short description of the experiment"></textarea>
    </div>

    <input name="save" type="submit" class="btn btn-primary" value="Save">
    <input name="launch" type="submit" class="btn btn-primary" value="Save and Launch">
    <input name="clear" type="submit" class="btn btn-default" value="Clear">
</form>

</div>
</body>
</html>

<?php

function create_experiment()
{
    global $airavataclient;

    $experiment = assemble_experiment();
    $expId = null;

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

    return $expId;
}

/**
 * Create and configure a new Experiment
 * @return Experiment
 */
function assemble_experiment()
{
    $scheduling = new ComputationalResourceScheduling();
    $scheduling->totalCPUCount = $_POST['cpu-count'];
    $scheduling->nodeCount = 1;
    $scheduling->numberOfThreads = 0;
    $scheduling->queueName = 'normal';
    $scheduling->wallTimeLimit = $_POST['wall-time'];
    $scheduling->jobStartTime = 0;
    $scheduling->totalPhysicalMemory = 0;
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
 * Create a select input and populate it with project options from the database
 */
function create_project_select()
{
    global $airavataclient;


    echo '<select class="form-control" name="project" id="project" required>';

    try
    {
        $userProjects = $airavataclient->getAllUserProjects($_SESSION['username']);

        foreach ($userProjects as $project)
        {
            echo '<option value="' . $project->projectID . '">' . $project->name . '</option>';
        }
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

    echo '</select>';
}

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

    if (isset($_POST['launch']))
    {
        try
        {
            $airavataclient->launchExperiment($expId, "airavataToken");
            print_success_message("Launched experiment {$_POST['experiment-name']}!");
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
}

//$transport->close();

?>



<form action="<?php echo $_SERVER['PHP_SELF']?>" method="post" role="form">
    <div class="form-group">
        <label for="experiment-name">Experiment Name</label>
        <input type="text" class="form-control" name="experiment-name" id="experiment-name" placeholder="Enter experiment name">
    </div>
    <!-- ultimately replace with results of getAllUserProjects() -->
    <div class="form-group bg-warning">
        <label for="project">Project</label>
        <select class="form-control" name="project" id="project">
            <option value="project-1">Project 1</option>
            <option value="project-2">Project 2</option>
            <option value="project-3">Project 3</option>
        </select>
    </div>
    <div class="form-group bg-danger">
        <label for="experiment-input">Experiment input</label>
        <input type="file" name="experiment-input" id="experiment-input">
    </div>
    <div class="form-group bg-warning">
        <label for="application">Application</label>
        <select class="form-control" name="application" id="application">
            <option value="application-1">Application 1</option>
            <option value="application-2">Application 2</option>
            <option value="application-3">Application 3</option>
        </select>
    </div>
    <div class="form-group bg-danger">
        <label for="compute-resource">Compute Resource</label>
        <select class="form-control" name="compute-resource" id="compute-resource">
            <option value="compute-resource-1">Compute Resource 1</option>
            <option value="compute-resource-2">Compute Resource 2</option>
            <option value="compute-resource-3">Compute Resource 3</option>
        </select>
    </div>
    <div class="form-group bg-danger">
        <label for="cpu-count">CPU Count</label>
        <input type="text"class="form-control" name="cpu-count" id="cpu-count">
    </div>
    <div class="form-group  bg-danger">
        <label for="wall-time">Wall Time</label>
        <input type="text" class="form-control" name="wall-time" id="wall-time">
    </div>
    <div class="form-group">
        <label for="experiment-description">Experiment Description</label>
        <textarea class="form-control" name="experiment-description" id="experiment-description"></textarea>
    </div>

    <input name="save" type="submit" class="btn btn-primary" value="Save">
    <input name="launch" type="submit" class="btn btn-primary" value="Save and Launch">
    <input name="clear" type="submit" class="btn btn-default" value="Clear">
</form>

</div>
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
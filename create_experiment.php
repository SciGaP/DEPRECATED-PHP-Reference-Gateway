<?php
session_start();
include 'utilities.php';




use Airavata\API\Error\InvalidRequestException;
use Airavata\API\Error\AiravataClientException;
use Airavata\API\Error\AiravataSystemException;



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
      <a class="navbar-brand" href="home.php">PHP Reference Gateway</a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
          <li><a href="create_project.php">Create project</a></li>
          <li class="active"><a href="create_experiment.php">Create experiment</a></li>
          <li><a href="browse_experiments.php">Browse experiments</a></li>
          <li><a href="search_experiments.php">Search experiments</a></li>
      </ul>
      
      <ul class="nav navbar-nav navbar-right">
          <li><a href="home.php"><?php echo $_SESSION['username']?></a></li>
          <li><a href="logout.php">Log out</a></li>
      </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>


<div class="container">
    
<h3>Create a new experiment</h3>




<?php

if (isset($_POST['save']) || isset($_POST['launch']))
{
    $expId = create_experiment();

    if (isset($_POST['launch']) && $expId)
    {
        launch_experiment($expId);
    }
}

//$transport->close();

?>



<form action="<?php echo $_SERVER['PHP_SELF']?>" method="post" role="form">
    <div class="form-group">
        <label for="experiment-name">Experiment Name</label>
        <input type="text" class="form-control" name="experiment-name" id="experiment-name" placeholder="Enter experiment name" autofocus required>
    </div>
    <div class="form-group">
        <label for="project">Project</label>
        <?php create_project_select(); ?>
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
    <div class="form-group bg-danger">
        <label for="experiment-input">Experiment input</label>
        <input type="file" name="experiment-input" id="experiment-input">
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
        <label for="node-count">Node Count</label>
        <input type="number" class="form-control" name="node-count" id="node-count" value="1">
    </div>
    <div class="form-group">
        <label for="cpu-count">Total CPU Count</label>
        <input type="number" class="form-control" name="cpu-count" id="cpu-count" value="1">
    </div>
    <div class="form-group">
        <label for="threads">Number of Threads</label>
        <input type="number" class="form-control" name="threads" id="threads" value="0">
    </div>
    <div class="form-group">
        <label for="wall-time">Wall Time Limit</label>
        <input type="number" class="form-control" name="wall-time" id="wall-time" value="15">
    </div>
    <div class="form-group">
        <label for="memory">Total Physical Memory</label>
        <input type="number" class="form-control" name="memory" id="memory" value="0">
    </div>
    <div class="form-group">
        <label for="experiment-description">Experiment Description</label>
        <textarea class="form-control" name="experiment-description" id="experiment-description" placeholder="Optional: Enter a short description of the experiment"></textarea>
    </div>

    <div class="btn-toolbar">
        <div class="btn-group">
            <input name="save" type="submit" class="btn btn-primary" value="Save">
            <input name="launch" type="submit" class="btn btn-primary" value="Save and launch">
        </div>
        <input name="clear" type="reset" class="btn btn-default" value="Reset values">
    </div>




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


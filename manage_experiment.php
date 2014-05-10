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
          <li><a href="create_experiment.php">Create experiment</a></li>
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
    
<h3>Manage experiment</h3>




<?php

$experiment = get_experiment($_GET['expId']);
$project = get_project($experiment->projectID);

if (isset($_POST['save']))
{
    $updatedExperiment = assemble_experiment(false);

    update_experiment($experiment->experimentID, $updatedExperiment);
}
elseif (isset($_POST['launch']))
{
    launch_experiment($experiment->experimentID);
}
elseif (isset($_POST['clone']))
{
    clone_experiment($experiment->experimentID);
}
elseif (isset($_POST['cancel']))
{
    cancel_experiment($experiment->experimentID);
}






//$transport->close();

?>



<form action="<?php echo $_SERVER['PHP_SELF'] . '?expId=' . $_GET['expId']?>" method="post" role="form">
    <div class="form-group">
        <label for="experiment-name">Experiment Name</label>
        <input type="text" class="form-control" name="experiment-name" id="experiment-name" value="<?php echo $experiment->name ?>">
    </div>
    <div class="form-group">
        <label for="project">Project</label>
        <input type="text" class="form-control" name="project" id="project" value="<?php echo $project->name ?>">
    </div>
    <div class="form-group">
        <label for="application">Application</label>
        <input type="text" class="form-control" name="application" id="application" value="<?php echo $experiment->applicationId ?>">
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
        <input type="text" class="form-control" name="node-count" id="node-count" value="1">
    </div>
    <div class="form-group">
        <label for="cpu-count">Total CPU Count</label>
        <input type="text" class="form-control" name="cpu-count" id="cpu-count" value="1">
    </div>
    <div class="form-group">
        <label for="threads">Number of Threads</label>
        <input type="text" class="form-control" name="threads" id="threads" value="0">
    </div>
    <div class="form-group">
        <label for="wall-time">Wall Time Limit</label>
        <input type="text" class="form-control" name="wall-time" id="wall-time" value="15">
    </div>
    <div class="form-group">
        <label for="memory">Total Physical Memory</label>
        <input type="text" class="form-control" name="memory" id="memory" value="0">
    </div>
    <div class="form-group">
        <label for="experiment-description">Experiment Description</label>
        <textarea class="form-control" name="experiment-description" id="experiment-description"><?php echo $experiment->description ?></textarea>
    </div>

    <div class="btn-toolbar">
        <div class="btn-group">
            <input name="save" type="submit" class="btn btn-primary" value="Save">
            <input name="launch" type="submit" class="btn btn-primary" value="Launch">
            <input name="clone" type="submit" class="btn btn-primary" value="Clone">
            <input name="cancel" type="submit" class="btn btn-warning" value="Cancel">
        </div>
        <input name="clear" type="reset" class="btn btn-default" value="Clear">
    </div>
</form>

</div>
</body>
</html>



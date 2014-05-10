<?php
session_start();
include 'utilities.php';



use Airavata\Model\Workspace\Project;
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
          <li class="active"><a href="create_project.php">Create project</a></li>
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
    
<h3>Create a new project</h3>




<?php

if (isset($_POST['clear']))
{
    print_success_message('Values cleared!');
}
elseif (isset($_POST['save']))
{
    create_project();
}

//$transport->close();

?>



<form action="<?php echo $_SERVER['PHP_SELF']?>" method="post" role="form">
    <div class="form-group">
        <label for="project-name">Project Name</label>
        <input type="text" class="form-control" name="project-name" id="project-name" placeholder="Enter project name" autofocus required>
    </div>

    <div class="form-group">
        <label for="project-description">Project Description</label>
        <textarea class="form-control" name="project-description" id="project-description" placeholder="Optional: Enter a short description of the project"></textarea>
    </div>

    <input name="save" type="submit" class="btn btn-primary" value="Save">
    <input name="clear" type="submit" class="btn btn-default" value="Clear">
</form>

</div>
</body>
</html>

<?php

function create_project()
{
    global $airavataclient;

    $project = new Project();
    $project->owner = $_SESSION['username'];
    $project->name = $_POST['project-name'];
    $project->description = $_POST['project-description'];
    $project->creationTime = time();


    $projectId = null;

    try
    {
        $projectId = $airavataclient->createProject($project, $project->owner);

        if ($projectId)
        {
            print_success_message("Project {$_POST['project-name']} created!");
        }
        else
        {
            print_error_message("Error creating project {$_POST['project-name']}!");
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

    return $projectId;
}

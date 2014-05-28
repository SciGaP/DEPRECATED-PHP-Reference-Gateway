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

<?php create_head(); ?>

<body>

<?php create_nav_bar(); ?>

<div class="container" style="max-width: 750px">
    
<h1>Create a new project</h1>



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
        $projectId = $airavataclient->createProject($project);

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

<?php
session_start();
include 'utilities.php';



use Airavata\Model\Workspace\Project;
use Airavata\API\Error\InvalidRequestException;
use Airavata\API\Error\AiravataClientException;
use Airavata\API\Error\AiravataSystemException;
use Airavata\API\Error\ProjectNotFoundException;



connect_to_id_store();
verify_login();

$airavataclient = get_airavata_client();





?>

<html>

<?php create_head(); ?>

<body>

<?php create_nav_bar(); ?>

<div class="container" style="max-width: 750px;">






    <?php

    $project = get_project($_GET['projId']);



    if (isset($_POST['save']))
    {
        $updatedProject = new Project();
        $updatedProject->owner = $_SESSION['username'];
        $updatedProject->name = $_POST['project-name'];
        $updatedProject->description = $_POST['project-description'];

        update_project($project->projectID, $updatedProject);

        $project = get_project($project->projectID);
    }






    //$transport->close();

    ?>


    <h1>Edit Project</h1>




    <form action="<?php echo $_SERVER['PHP_SELF'] . '?projId=' . $_GET['projId']?>" method="post" role="form">
        <div class="form-group">
            <label for="project-name">Project Name</label>
            <input type="text"
                   class="form-control"
                   name="project-name"
                   id="project-name"
                   value="<?php echo $project->name ?>">
        </div>
        <div class="form-group">
            <label for="project-description">Project Description</label>
            <textarea class="form-control"
                      name="project-description"
                      id="project-description"><?php echo $project->description; ?>
            </textarea>
        </div>

        <div class="btn-toolbar">
            <input name="save" type="submit" class="btn btn-primary" value="Save">
            <input name="clear" type="reset" class="btn btn-default" value="Reset values">
        </div>


    </form>


</div>
</body>
</html>


<?php

function update_project($projectId, $updatedProject)
{
    global $airavataclient;

    try
    {
        $airavataclient->updateProject($projectId, $updatedProject);

        print_success_message('Project updated! Click <a href="project_summary.php?projId=' . $projectId . '">here</a> to view the project summary.');
    }
    catch (InvalidRequestException $ire)
    {
        print_error_message('InvalidRequestException!<br><br>' . $ire->getMessage());
    }
    catch (ProjectNotFoundException $pnfe)
    {
        print_error_message('ProjectNotFoundException!<br><br>' . $pnfe->getMessage());
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



?>

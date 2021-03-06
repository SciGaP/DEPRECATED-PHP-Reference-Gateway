<?php
session_start();
include 'utilities.php';



use Airavata\Model\Workspace\Experiment\DataType;
use Airavata\Model\Workspace\Experiment\ExperimentState;
use Airavata\Model\Workspace\Experiment\JobState;


create_http_header();
connect_to_id_store();
verify_login();

$airavataclient = get_airavata_client();



?>

<html>

<?php create_html_head(); ?>

<body>

<?php create_nav_bar(); ?>

<div class="container" style="max-width: 750px;">
    





<?php

$experiment = get_experiment($_GET['expId']);
$project = get_project($experiment->projectID);



$experimentStatus = $experiment->experimentStatus;
$experimentState = $experimentStatus->experimentState;
$experimentStatusString = ExperimentState::$__names[$experimentState];
$experimentTimeOfStateChange = date('Y-m-d H:i:s', $experimentStatus->timeOfStateChange/1000); // divide by 1000 since timeOfStateChange is in ms
$experimentCreationTime = date('Y-m-d H:i:s', $experiment->creationTime/1000); // divide by 1000 since creationTime is in ms


$jobStatus = $airavataclient->getJobStatuses($experiment->experimentID);

if ($jobStatus)
{
    $jobName = array_keys($jobStatus);
    $jobState = JobState::$__names[$jobStatus[$jobName[0]]->jobState];
}
else
{
    $jobState = null;
}



$userConfigData = $experiment->userConfigurationData;
$scheduling = $userConfigData->computationalResourceScheduling;

$applicationInterface = get_application_interface($experiment->applicationId);
$computeResource = get_compute_resource($scheduling->resourceHostId);

//var_dump($experiment);



switch ($experimentStatusString)
{
    case 'CREATED':
    case 'VALIDATED':
    case 'SCHEDULED':
    case 'CANCELED':
    case 'FAILED':
        $editable = true;
        break;
    default:
        $editable = false;
        break;
}

switch ($experimentStatusString)
{
    case 'CREATED':
    case 'VALIDATED':
    case 'SCHEDULED':
    case 'LAUNCHED':
    case 'EXECUTING':
        $cancelable = true;
        break;
    default:
        $cancelable = false;
        break;
}



if (isset($_POST['save']))
{
    $updatedExperiment = apply_changes_to_experiment($experiment);

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


<h1>
    Experiment Summary
    <small><a href="experiment_summary.php?expId=<?php echo $experiment->experimentID ?>"
              title="Refresh"><span class="glyphicon glyphicon-refresh"></span></a></small>
</h1>


    <table class="table">
        <tr>
            <td><strong>Name</strong></td>
            <td><?php echo $experiment->name; ?></td>
        </tr>
        <tr>
            <td><strong>Description</strong></td>
            <td><?php echo $experiment->description; ?></td>
        </tr>
        <tr>
            <td><strong>Project</strong></td>
            <td><?php echo $project->name; ?></td>
        </tr>
        <tr>
            <td><strong>Application</strong></td>
            <td><?php echo $applicationInterface->applicationName; ?></td>
        </tr>
        <tr>
            <td><strong>Compute resource</strong></td>
            <td><?php echo $computeResource->hostName; ?></td>
        </tr>
        <tr>
            <td><strong>Experiment Status</strong></td>
            <td><?php echo $experimentStatusString; ?></td>
        </tr>
        <?php
        if ($jobState) echo '
        <tr>
            <td><strong>Job Status</strong></td>
            <td>' . $jobState . '</td>
        </tr>
        ';
        ?>
        <tr>
            <td><strong>Creation time</strong></td>
            <td><?php echo $experimentCreationTime; ?></td>
        </tr>
        <tr>
            <td><strong>Update time</strong></td>
            <td><?php echo $experimentTimeOfStateChange; ?></td>
        </tr>
        <tr>
            <td><strong>Inputs</strong></td>
            <td><?php list_input_files($experiment); ?></td>
        </tr>
        <tr>
            <td><strong>Outputs</strong></td>
            <td><?php if ($experimentStatusString == 'COMPLETED') list_output_files($experiment); ?></td>
        </tr>
    </table>

    <form action="<?php echo $_SERVER['PHP_SELF'] . '?expId=' . $_GET['expId']?>" method="post" role="form">
        <div class="btn-toolbar">
            <input name="launch"
                   type="submit"
                   class="btn btn-success"
                   value="Launch"
                   title="Launch the experiment" <?php if(!$editable) echo 'disabled'  ?>>
            <!--<input name="cancel" type="submit" class="btn btn-warning" value="Cancel" <?php //if(!$cancelable) echo 'disabled';  ?>>-->
            <input name="clone"
                   type="submit"
                   class="btn btn-primary"
                   value="Clone"
                   title="Create a clone of the experiment. Cloning is the only way to change an experiment's settings
                    after it has been launched.">
            <a href="edit_experiment.php?expId=<?php echo $experiment->experimentID; ?>"
               class="btn btn-default"
               role="button"
               title="Edit the experiment's settings" <?php if(!$editable) echo 'disabled'  ?>>
                <span class="glyphicon glyphicon-pencil"></span>
                Edit
            </a>
        </div>
    </form>

</div>
</body>
</html>



<?php



function list_output_files($experiment)
{
    $experimentOutputs = $experiment->experimentOutputs;
    //var_dump($experimentOutputs);

    foreach ($experimentOutputs as $output)
    {
        if ($output->type == DataType::URI)
        {
            //echo '<p>' . $output->key .  ': <a href="' . $output->value . '">' . $output->value . '</a></p>';
            echo '<p><a target="_blank"
                        href="' . str_replace(EXPERIMENT_DATA_ROOT_ABSOLUTE, EXPERIMENT_DATA_ROOT, $output->value) . '">' .
                        $output->key . ' <span class="glyphicon glyphicon-new-window"></span></a></p>';
        }
        elseif ($output->type == DataType::STRING)
        {
            echo '<p>' . $output->value . '</p>';
        }
    }
}

unset($_POST);

?>

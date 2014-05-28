<?php
session_start();
include 'utilities.php';



use Airavata\Model\Workspace\Experiment\DataType;
use Airavata\Model\Workspace\Experiment\ExperimentState;




connect_to_id_store();
verify_login();

$airavataclient = get_airavata_client();



$echoResources = array('localhost', 'trestles.sdsc.edu', 'stampede.tacc.xsede.org', 'lonestar.tacc.utexas.edu');
$wrfResources = array('trestles.sdsc.edu', 'stampede.tacc.xsede.org');

$appResources = array( 'SimpleEcho0' => $echoResources,
    'SimpleEcho2' => $echoResources,
    'SimpleEcho3' => $echoResources,
    'SimpleEcho4' => $echoResources,
    'WRF' => $wrfResources);

?>

<html>

<?php create_head(); ?>

<body>

<?php create_nav_bar(); ?>

<div class="container" style="max-width: 750px;">
    





<?php

$experiment = get_experiment($_GET['expId']);
$project = get_project($experiment->projectID);


$experimentStatus = $experiment->experimentStatus;
$experimentState = $experimentStatus->experimentState;
$experimentStatusString = ExperimentState::$__names[$experimentState];
$experimentTimeOfStateChange = $experimentStatus->timeOfStateChange;


$userConfigData = $experiment->userConfigurationData;
$scheduling = $userConfigData->computationalResourceScheduling;



//var_dump($experiment);



switch ($experimentStatusString)
{
    case 'CREATED':
    case 'VALIDATED':
    case 'SCHEDULED':
    case 'CANCELED':
    case 'FAILED':
    case 'UNKNOWN':
        $editable = true;
        break;
    default:
        $editable = false;
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


<h1>Experiment Summary</h1>


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
            <td><?php echo $experiment->applicationId; ?></td>
        </tr>
        <tr>
            <td><strong>Compute resource</strong></td>
            <td><?php echo $scheduling->resourceHostId; ?></td>
        </tr>
        <tr>
            <td><strong>Status</strong></td>
            <td><?php echo $experimentStatusString; ?></td>
        </tr>
        <tr>
            <td><strong>Update time</strong></td>
            <td><?php echo $experimentTimeOfStateChange; ?></td>
        </tr>
        <tr>
            <td><strong>Outputs</strong></td>
            <td><?php list_output_files($experiment); ?></td>
        </tr>
    </table>

    <form action="<?php echo $_SERVER['PHP_SELF'] . '?expId=' . $_GET['expId']?>" method="post" role="form">
        <div class="btn-toolbar">
            <input name="launch" type="submit" class="btn btn-primary" value="Launch" <?php if(!$editable) echo 'disabled'  ?>>
            <input name="cancel" type="submit" class="btn btn-warning" value="Cancel" <?php if($editable) echo 'disabled'  ?>>
            <input name="clone" type="submit" class="btn btn-primary" value="Clone">
            <a href="edit_experiment.php?expId=<?php echo $experiment->experimentID; ?>" class="btn btn-default" role="button" <?php if(!$editable) echo 'disabled'  ?>>
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
            echo '<p>' . $output->key .  ': <a href="' . $output->value . '">' . $output->value . '</a></p>';
        }
        elseif ($output->type == DataType::STRING)
        {
            echo '<p>' . $output->value . '</p>';
        }
    }
}

?>

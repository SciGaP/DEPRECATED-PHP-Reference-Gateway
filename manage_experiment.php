<?php
session_start();
include 'utilities.php';




use Airavata\Model\Workspace\Experiment\ExperimentState;



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

<?php create_nav_bar(); ?>

<div class="container">
    
<h3>Manage experiment</h3>




<?php

$experiment = get_experiment($_GET['expId']);
$project = get_project($experiment->projectID);


$experimentStatus = $experiment->experimentStatus;
$experimentState = $experimentStatus->experimentState;
$experimentStatusString = ExperimentState::$__names[$experimentState];


$userConfigData = $experiment->userConfigurationData;
$scheduling = $userConfigData->computationalResourceScheduling;



//var_dump($experiment);



switch ($experimentStatusString)
{
    case 'CREATED':
    case 'VALIDATED':
    case 'SCHEDULED':
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



<form action="<?php echo $_SERVER['PHP_SELF'] . '?expId=' . $_GET['expId']?>" method="post" role="form">
    <div class="form-group">
        <label for="experiment-name">Experiment Name</label>
        <input type="text" class="form-control" name="experiment-name" id="experiment-name" value="<?php echo $experiment->name ?>" <?php if(!$editable) echo 'disabled' ?>>
    </div>
    <div class="form-group">
        <label for="project">Project</label>
        <?php create_project_select($experiment->projectID, $editable); ?>
    </div>
    <div class="form-group">
        <label for="application">Application</label>
        <select class="form-control" name="application" id="application" <?php if(!$editable) echo 'disabled' ?>>
            <option value="SimpleEcho0" <?php if ($experiment->applicationId == 'SimpleEcho0') echo 'selected' ?>>SimpleEcho0</option>
            <option value="SimpleEcho2" <?php if ($experiment->applicationId == 'SimpleEcho2') echo 'selected' ?>>SimpleEcho2</option>
            <option value="SimpleEcho3" <?php if ($experiment->applicationId == 'SimpleEcho3') echo 'selected' ?>>SimpleEcho3</option>
            <option value="SimpleEcho4" <?php if ($experiment->applicationId == 'SimpleEcho4') echo 'selected' ?>>SimpleEcho4</option>
        </select>
    </div>
    <div class="form-group bg-danger">
        <label for="experiment-input">Experiment input</label>
        <input type="file" name="experiment-input" id="experiment-input" <?php if(!$editable) echo 'disabled' ?>>
    </div>

    <div class="form-group">
        <label for="compute-resource">Compute Resource</label>
        <select class="form-control" name="compute-resource" id="compute-resource" <?php if(!$editable) echo 'disabled' ?>>
            <option value="localhost" <?php if ($scheduling->resourceHostId == 'localhost') echo 'selected' ?>>localhost</option>
            <option value="trestles.sdsc.edu" <?php if ($scheduling->resourceHostId == 'trestles.sdsc.edu') echo 'selected' ?>>Trestles</option>
            <option value="stampede.tacc.xsede.org" <?php if ($scheduling->resourceHostId == 'stampede.tacc.xsede.org') echo 'selected' ?>>Stampede</option>
            <option value="lonestar.tacc.utexas.edu" <?php if ($scheduling->resourceHostId == 'lonestar.tacc.utexas.edu') echo 'selected' ?>>Lonestar</option>
        </select>
    </div>
    <div class="form-group">
        <label for="node-count">Node Count</label>
        <input type="number" class="form-control" name="node-count" id="node-count" value="<?php echo $scheduling->nodeCount ?>" <?php if(!$editable) echo 'disabled' ?>>
    </div>
    <div class="form-group">
        <label for="cpu-count">Total CPU Count</label>
        <input type="number" class="form-control" name="cpu-count" id="cpu-count" value="<?php echo $scheduling->totalCPUCount ?>" <?php if(!$editable) echo 'disabled' ?>>
    </div>
    <div class="form-group">
        <label for="threads">Number of Threads</label>
        <input type="number" class="form-control" name="threads" id="threads" value="<?php echo $scheduling->numberOfThreads ?>" <?php if(!$editable) echo 'disabled' ?>>
    </div>
    <div class="form-group">
        <label for="wall-time">Wall Time Limit</label>
        <input type="number" class="form-control" name="wall-time" id="wall-time" value="<?php echo $scheduling->wallTimeLimit ?>" <?php if(!$editable) echo 'disabled' ?>>
    </div>
    <div class="form-group">
        <label for="memory">Total Physical Memory</label>
        <input type="number" class="form-control" name="memory" id="memory" value="<?php echo $scheduling->totalPhysicalMemory ?>" <?php if(!$editable) echo 'disabled' ?>>
    </div>
    <div class="form-group">
        <label for="experiment-description">Experiment Description</label>
        <textarea class="form-control" name="experiment-description" id="experiment-description" <?php if(!$editable) echo 'disabled' ?>><?php echo $experiment->description ?></textarea>
    </div>
    <div class="form-group">
        <p><strong>Status: </strong><?php echo $experimentStatusString ?></p>
    </div>

    <div class="btn-toolbar">
        <div class="btn-group">
            <input name="save" type="submit" class="btn btn-primary" value="Save" <?php if(!$editable) echo 'disabled'  ?>>
            <input name="launch" type="submit" class="btn btn-primary" value="Launch" <?php if(!$editable) echo 'disabled'  ?>>
        </div>
        <input name="cancel" type="submit" class="btn btn-warning" value="Cancel" <?php if($editable) echo 'disabled'  ?>>
        <input name="clone" type="submit" class="btn btn-primary" value="Clone">
        <input name="clear" type="reset" class="btn btn-default" value="Reset values">
    </div>
</form>

</div>
</body>
</html>


<?php

function apply_changes_to_experiment($experiment)
{
    $experiment->name = $_POST['experiment-name'];
    $experiment->applicationId = $_POST['application'];

    $userConfigDataUpdated = $experiment->userConfigurationData;
    $schedulingUpdated = $userConfigDataUpdated->computationalResourceScheduling;

    $schedulingUpdated->resourceHostId = $_POST['compute-resource'];
    $schedulingUpdated->nodeCount = $_POST['node-count'];
    $schedulingUpdated->totalCPUCount = $_POST['cpu-count'];
    $schedulingUpdated->numberOfThreads = $_POST['threads'];
    $schedulingUpdated->wallTimeLimit = $_POST['wall-time'];
    $schedulingUpdated->totalPhysicalMemory = $_POST['memory'];

    $userConfigDataUpdated->computationalResourceScheduling = $schedulingUpdated;
    $experiment->userConfigurationData = $userConfigDataUpdated;

    return $experiment;
}

?>

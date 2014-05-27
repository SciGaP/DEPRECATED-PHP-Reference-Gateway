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
<head>
    <title>PHP Reference Gateway</title>
    
    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
</head>


<body>

<?php create_nav_bar(); ?>

<div class="container">
    





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

function apply_changes_to_experiment($experiment)
{
    $experiment->name = $_POST['experiment-name'];
    $experiment->description = $_POST['experiment-description'];
    //$experiment->applicationId = $_POST['application'];

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







    /*
    if ($_POST['application'] == 'WRF')
    {
        foreach ($_FILES as $file)
        {
            if ($file['error'] > 0)
            {
                $uploadSuccessful = false;
                print_error_message('Error uploading file ' . $file['name'] . ' !');
            }
            elseif ($file['type'] != 'text/plain')
            {
                $uploadSuccessful = false;
                print_error_message('Uploaded file ' . $file['name'] . ' type not supported!');
            }
            elseif (($file['size'] / 1024) > 20)
            {
                $uploadSuccessful = false;
                print_error_message('Uploaded file ' . $file['name'] . ' must be smaller than 20 kB!');
            }
        }


        if ($uploadSuccessful)
        {
            // construct unique path
            do
            {
                $experimentPath = EXPERIMENT_DATA_ROOT . $_POST['experiment-name'] . md5(rand() * time()) . '/';
            }
            while (is_dir($experimentPath)); // if dir already exists, try again

            // create new directory
            // move file to new directory, overwriting old versions if necessary
            if (mkdir($experimentPath))
            {
                foreach ($_FILES as $file)
                {
                    $filePath = $experimentPath . $file['name'];

                    if (is_file($filePath))
                    {
                        unlink($filePath);

                        print_warning_message('Uploaded file already exists! Overwriting...');
                    }

                    $moveFile = move_uploaded_file($file['tmp_name'], $filePath);

                    if ($moveFile)
                    {
                        print_success_message('Upload: ' . $file['name'] . '<br>' .
                            'Type: ' . $file['type'] . '<br>' .
                            'Size: ' . ($file['size']/1024) . ' kB<br>' .
                            'Stored in: ' . $experimentPath . $file['name']);
                    }
                    else
                    {
                        print_error_message('Error moving uploaded file ' . $file['name'] . '!');
                    }



                    // wrf
                    $experimentInput = new DataObjectType();
                    $experimentInput->key = $file['name'];
                    $experimentInput->value = $filePath;
                    $experimentInput->type = DataType::URI;
                    $experimentInputs[] = $experimentInput; // push into array
                }
            }
            else
            {
                print_error_message('Error creating upload directory!');
            }


        }
    }
    else
    {
        // echo
        $experimentInput = new DataObjectType();
        $experimentInput->key = 'echo_input';
        $experimentInput->value = $_POST['experiment-input'];
        $experimentInput->type = DataType::STRING;
        $experimentInputs = array($experimentInput);
    }
    */













    return $experiment;
}


function list_input_files($experiment)
{
    $experimentInputs = $experiment->experimentInputs;
    //var_dump($experimentInputs);

    foreach ($experimentInputs as $input)
    {
        if ($input->type == DataType::URI)
        {
            echo '<a href="' . $input->value . '">' . $input->value . '</a><br>';
        }
        elseif ($input->type == DataType::STRING)
        {
            echo '<p>' . $input->value . '</p>';
        }
    }
}


function list_output_files($experiment)
{
    $experimentOutputs = $experiment->experimentOutputs;
    var_dump($experimentOutputs);

    foreach ($experimentOutputs as $output)
    {
        if ($output->type == DataType::URI)
        {
            echo '<a href="' . $output->value . '">' . $output->value . '</a><br>';
        }
        elseif ($output->type == DataType::STRING)
        {
            echo '<p>' . $output->value . '</p>';
        }
    }
}

?>

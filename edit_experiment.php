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

$appResources = array( 'Echo' => $echoResources, 'WRF' => $wrfResources);

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



//var_dump($experiment->experimentInputs);



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

    //update_experiment($experiment->experimentID, $updatedExperiment);

    $experiment = get_experiment($_GET['expId']); // update local experiment variable
}






//$transport->close();

?>


<h1>Edit Experiment</h1>


<form action="<?php echo $_SERVER['PHP_SELF'] . '?expId=' . $_GET['expId']?>" method="post" role="form" enctype="multipart/form-data">
    <div class="form-group">
        <label for="experiment-name">Experiment Name</label>
        <input type="text"
               class="form-control"
               name="experiment-name"
               id="experiment-name"
               value="<?php echo $experiment->name ?>"
            <?php if(!$editable) echo 'disabled' ?>>
    </div>
    <div class="form-group">
        <label for="experiment-description">Experiment Description</label>
        <textarea class="form-control"
                  name="experiment-description"
                  id="experiment-description"
                <?php if(!$editable) echo 'disabled' ?>>
            <?php echo $experiment->description ?>
        </textarea>
    </div>
    <div class="form-group">
        <label for="project">Project</label>
        <?php create_project_select($experiment->projectID, $editable); ?>
    </div>
    <div class="form-group">
        <label for="application">Application</label>
        <select class="form-control" name="application" id="application" disabled>
            <option value="Echo" <?php if ($experiment->applicationId == 'Echo') echo 'selected' ?>>Echo</option>
            <option value="WRF" <?php if ($experiment->applicationId == 'WRF') echo 'selected' ?>>WRF</option>
        </select>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">Application configuration</div>
        <div class="panel-body">
            <label>Application input</label>
            <div class="well">
                <div class="form-group">
                    <p><strong>Current inputs</strong></p>
                    <?php list_input_files($experiment); ?>
                </div>
                <?php
                switch ($experiment->applicationId)
                {
                    case 'Echo':
                        echo '<div class="form-group">
                            <label class="sr-only" for="experiment-input">Text to echo</label>
                            <input type="text"
                                class="form-control"
                                name="experiment-input"
                                id="experiment-input"
                                placeholder="Text to echo">
                            </div>';
                        break;
                    case 'WRF':
                        echo '<div class="form-group">
                                <label for="namelist">Namelist</label>
                                <input type="file" name="namelist" id="namelist" disabled>
                            </div>
                            <div class="form-group">
                                <label for="model-init">Model initialization data</label>
                                <input type="file" name="model-init" id="model-init" disabled>
                            </div>
                            <div class="form-group">
                                <label for="bounds">Forecast lateral boundary conditions</label>
                                <input type="file" name="bounds" id="bounds" disabled>
                            </div>
                        ';
                        break;
                }
                ?>
            </div>

        <div class="form-group">
            <label for="compute-resource">Compute Resource</label>
            <select class="form-control" name="compute-resource" id="compute-resource">';
                <?php

                foreach ($appResources[$experiment->applicationId] as $resource)
                {
                    echo '<option value="' . $resource . '">' . $resource . '</option>';
                }

                ?>
            </select>
        </div>

    <div class="form-group">
        <label for="node-count">Node Count</label>
        <input type="number"
               class="form-control"
               name="node-count"
               id="node-count"
               value="<?php echo $scheduling->nodeCount ?>"
            <?php if(!$editable) echo 'disabled' ?>>
    </div>
    <div class="form-group">
        <label for="cpu-count">Total CPU Count</label>
        <input type="number"
               class="form-control"
               name="cpu-count"
               id="cpu-count"
               value="<?php echo $scheduling->totalCPUCount ?>"
            <?php if(!$editable) echo 'disabled' ?>>
    </div>
    <div class="form-group">
        <label for="threads">Number of Threads</label>
        <input type="number"
               class="form-control"
               name="threads"
               id="threads"
               value="<?php echo $scheduling->numberOfThreads ?>"
            <?php if(!$editable) echo 'disabled' ?>>
    </div>
    <div class="form-group">
        <label for="wall-time">Wall Time Limit</label>
        <div class="input-group">
            <input type="number"
                   class="form-control"
                   name="wall-time"
                   id="wall-time"
                   value="<?php echo $scheduling->wallTimeLimit ?>"
                <?php if(!$editable) echo 'disabled' ?>>
            <span class="input-group-addon">minutes</span>
        </div>
    </div>
    <div class="form-group">
        <label for="memory">Total Physical Memory</label>
        <div class="input-group">
            <input type="number"
                   class="form-control"
                   name="memory"
                   id="memory"
                   value="<?php echo $scheduling->totalPhysicalMemory ?>"
                <?php if(!$editable) echo 'disabled' ?>>
            <span class="input-group-addon">kB</span>
        </div>
    </div>

    </div>
    </div>

    <div class="btn-toolbar">
        <input name="save" type="submit" class="btn btn-primary" value="Save" <?php if(!$editable) echo 'disabled'  ?>>
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

    switch ($_POST['compute-resource'])
    {
        case 'trestles.sdsc.edu':
            $schedulingUpdated->ComputationalProjectAccount = 'sds128';
            break;
        case 'stampede.tacc.xsede.org':
        case 'lonestar.tacc.utexas.edu':
            $schedulingUpdated->ComputationalProjectAccount = 'TG-STA110014S';
            break;
        default:
            $schedulingUpdated->ComputationalProjectAccount = 'admin';
    }

    $userConfigDataUpdated->computationalResourceScheduling = $schedulingUpdated;
    $experiment->userConfigurationData = $userConfigDataUpdated;

    $experimentInputs = $experiment->experimentInputs;





    if ($experiment->applicationId == 'WRF')
    {
        /*
        if (sizeof($_FILES) > 0)
        {
            $uploadSuccessful = true; // changed to false if error

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
                // get upload path
                $path = $experimentInputs[0]->value;
                echo $path;
            }
        }
        */
    }
    else // echo
    {
        if ($_POST['experiment-input'])
        {
            foreach ($experimentInputs as $input)
            {
                if ($input->key = 'echo_input')
                {
                    $input->value = 'echo_output=' . $_POST['experiment-input'];
                }
            }

            $experiment->experimentInputs = $experimentInputs;
        }
    }






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
            echo '<p><a href="' . $input->value . '">' . $input->key . '</a></p>';
        }
        elseif ($input->type == DataType::STRING)
        {
            $valueExplode = explode('=', $input->value);
            echo '<p>' . $input->key . ': ' . $valueExplode[1] . '</p>';
        }
    }
}

?>

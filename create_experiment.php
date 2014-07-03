<?php
session_start();
include 'utilities.php';




use Airavata\API\Error\InvalidRequestException;
use Airavata\API\Error\AiravataClientException;
use Airavata\API\Error\AiravataSystemException;



connect_to_id_store();
verify_login();

$airavataclient = get_airavata_client();
$appCatClient = get_appcat_client();


$echoResources = array('localhost', 'trestles.sdsc.edu', 'lonestar.tacc.utexas.edu');
$wrfResources = array('trestles.sdsc.edu');

$appResources = array('Echo' => $echoResources, 'WRF' => $wrfResources);





















?>

<html>

<?php create_head(); ?>

<body>

<?php create_nav_bar(); ?>

<div class="container" style="max-width: 750px;">
    
<h1>Create a new experiment</h1>




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



<form action="<?php echo $_SERVER['PHP_SELF']?>" method="post" role="form" enctype="multipart/form-data">

    <?php

    if (isset($_POST['continue']))
    {
        $disabled = ' disabled';
        $experimentName = $_POST['experiment-name'];
        $experimentDescription = $_POST['experiment-description'] . ' ';
        $project = $_POST['project'];
        $application = $_POST['application'];

        $interface = $appCatClient->getApplicationInterface($_POST['application']);

        // ugly hack until app catalog is in place
        $echo = ($application == 'Echo')? ' selected' : '';
        $wrf = ($application == 'WRF')? ' selected' : '';

        echo '<input type="hidden" name="experiment-name" value="' . $experimentName . '">';
        echo '<input type="hidden" name="experiment-description" value="' . $experimentDescription . '">';
        echo '<input type="hidden" name="project" value="' . $project . '">';
        echo '<input type="hidden" name="application" value="' . $application . '">';
    }
    else
    {
        $disabled = '';
        $experimentName = '';
        $experimentDescription = '';
        $project = '';
        $application = '';

        $echo = '';
        $wrf = '';
    }


    echo '
    <div class="form-group">
        <label for="experiment-name">Experiment Name</label>
        <input type="text" class="form-control" name="experiment-name" id="experiment-name" placeholder="Enter experiment name" autofocus required' . $disabled . ' value="' . $experimentName . '">
    </div>
    <div class="form-group">
        <label for="experiment-description">Experiment Description</label>
        <textarea class="form-control" name="experiment-description" id="experiment-description" placeholder="Optional: Enter a short description of the experiment"' . $disabled . '>' . $experimentDescription . '</textarea>
    </div>
    <div class="form-group">
        <label for="project">Project</label>
    ';
        create_project_select($project, !$disabled);

    echo '
    </div>
    <div class="form-group">
        <label for="application">Application</label>';

        create_application_select($application, !$disabled);

    echo '</div>';



    if (!isset($_POST['continue']))
    {
        echo '<div class="btn-toolbar">
        <input name="continue" type="submit" class="btn btn-primary" value="Continue">
        <input name="clear" type="reset" class="btn btn-default" value="Reset values">
        </div>
        ';
    }
    else
    {
        echo '<div class="panel panel-default">
        <div class="panel-heading">Application configuration</div>
        <div class="panel-body">
        <label>Application input</label>
        <div class="well">
        ';










        $inputs = $interface->applicationInputs;

        foreach ($inputs as $input)
        {
            switch ($input-type)
            {
                case 'STRING':
                    echo '<div class="form-group">
                    <label class="sr-only" for="experiment-input">' . $input->name . '</label>
                    <input type="text" class="form-control" name="' . $input->name .
                        '" id="' . $input->name .
                        '" value="' . $input->value .
                        '" placeholder="' . $input->userFriendlyDescription . '" required>
                    </div>';
                    break;
                case 'INTEGER':
                case 'FLOAT':
                    echo '<div class="form-group">
                    <label class="sr-only" for="experiment-input">' . $input->name . '</label>
                    <input type="number" class="form-control" name="' . $input->name .
                        '" id="' . $input->name .
                        '" value="' . $input->value .
                        '" placeholder="' . $input->userFriendlyDescription . '" required>
                    </div>';
                    break;
                case 'URI':
                    echo '<div class="form-group">
                    <label class="sr-only" for="experiment-input">' . $input->name . '</label>
                    <input type="file" class="form-control" name="' . $input->name .
                        '" id="' . $input->name .
                        '" placeholder="' . $input->userFriendlyDescription . '" required>
                    </div>';
                    break;
            }
        }



















    echo '</div>
    <div class="form-group">
        <label for="compute-resource">Compute Resource</label>
        <select class="form-control" name="compute-resource" id="compute-resource">';




        foreach ($interface->applicationDeployments as $deployment)
        {
            echo '<option value="' . $deployment->computeResourceDescription->computeResourceId . '">' .
                $deployment->computeResourceDescription->hostName . '</option>';
        }



    echo '
        </select>
    </div>
    <div class="form-group">
        <label for="node-count">Node Count</label>
        <input type="number" class="form-control" name="node-count" id="node-count" value="1" min="1">
    </div>
    <div class="form-group">
        <label for="cpu-count">Total Core Count</label>
        <input type="number" class="form-control" name="cpu-count" id="cpu-count" value="1" min="1">
    </div>
    <div class="form-group">
        <label for="threads">Number of Threads</label>
        <input type="number" class="form-control" name="threads" id="threads" value="0" min="0">
    </div>
    <div class="form-group">
        <label for="wall-time">Wall Time Limit</label>
        <div class="input-group">
            <input type="number" class="form-control" name="wall-time" id="wall-time" value="15" min="0">
            <span class="input-group-addon">minutes</span>
        </div>
    </div>
    <div class="form-group">
        <label for="memory">Total Physical Memory</label>
        <div class="input-group">
            <input type="number" class="form-control" name="memory" id="memory" value="0" min="0">
            <span class="input-group-addon">kB</span>
        </div>
    </div>



    </div>
    </div>
    <!-- use <button> instead of <input> in order to match height of <a> in Firefox -->
    <div class="btn-toolbar">
        <div class="btn-group">
            <button name="save" type="submit" class="btn btn-primary" value="Save">Save</button>
            <button name="launch" type="submit" class="btn btn-primary" value="Save and launch">Save and launch</button>
        </div>
        <div class="btn-group">
            <button name="clear" type="reset" class="btn btn-default" value="Reset values">Reset application configuration</button>
            <a href="' . $_SERVER['PHP_SELF'] . '" class="btn btn-default" role="button">Start over</a>
        </div>
    </div>';
    }

    ?>







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
        if($experiment)
        {
            $expId = $airavataclient->createExperiment($experiment);
        }

        if ($expId)
        {
            print_success_message("Experiment {$_POST['experiment-name']} created!" .
                ' <a href="experiment_summary.php?expId=' . $expId . '">Go to experiment summary page</a>');
            //var_dump($airavataclient->getExperiment($expId));
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


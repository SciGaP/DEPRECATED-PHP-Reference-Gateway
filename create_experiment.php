<?php
session_start();
include 'utilities.php';




use Airavata\API\Error\InvalidRequestException;
use Airavata\API\Error\AiravataClientException;
use Airavata\API\Error\AiravataSystemException;



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
        $experimentDescription = $_POST['experiment-description'];
        $project = $_POST['project'];
        $application = $_POST['application'];

        // ugly hack until app catalog is in place
        $simpleEcho0 = ($application == 'SimpleEcho0')? ' selected' : '';
        $simpleEcho2 = ($application == 'SimpleEcho2')? ' selected' : '';
        $simpleEcho3 = ($application == 'SimpleEcho3')? ' selected' : '';
        $simpleEcho4 = ($application == 'SimpleEcho4')? ' selected' : '';
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
        $experimentDescription = ' ';
        $project = '';
        $application = '';

        $simpleEcho0 = '';
        $simpleEcho2 = '';
        $simpleEcho3 = '';
        $simpleEcho4 = '';
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
        <label for="application">Application</label>
        <select class="form-control" name="application" id="application"' . $disabled . '>
            <option value="SimpleEcho0"' . $simpleEcho0 . '>SimpleEcho0</option>
            <option value="SimpleEcho2"' . $simpleEcho2 . '>SimpleEcho2</option>
            <option value="SimpleEcho3"' . $simpleEcho3 . '>SimpleEcho3</option>
            <option value="SimpleEcho4"' . $simpleEcho4 . '>SimpleEcho4</option>
            <option value="WRF"' . $wrf . '>WRF</option>
        </select>
    </div>
    ';



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





        switch ($_POST['application'])
        {
            case 'SimpleEcho0':
            case 'SimpleEcho2':
            case 'SimpleEcho3':
            case 'SimpleEcho4':
                echo '<div class="form-group">
                    <label class="sr-only" for="experiment-input">Text to echo</label>
                    <input type="text" class="form-control" name="experiment-input" id="experiment-input" placeholder="Text to echo" required>
                    </div>';
                break;
            case 'WRF':
                echo '<div class="form-group">
                        <label for="namelist">Namelist</label>
                        <input type="file" name="namelist" id="namelist" required>
                    </div>
                    <div class="form-group">
                        <label for="model-init">Model initialization data</label>
                        <input type="file" name="model-init" id="model-init" required>
                    </div>
                    <div class="form-group">
                        <label for="bounds">Forecast lateral boundary conditions</label>
                        <input type="file" name="bounds" id="bounds" required>
                    </div>
                    ';
                break;
        }






    echo '</div>
    <div class="form-group">
        <label for="compute-resource">Compute Resource</label>
        <select class="form-control" name="compute-resource" id="compute-resource">';


            foreach ($appResources[$_POST['application']] as $resource)
            {
                echo '<option value="' . $resource . '">' . $resource . '</option>';
            }



    echo '
        </select>
    </div>
    <div class="form-group">
        <label for="node-count">Node Count</label>
        <input type="number" class="form-control" name="node-count" id="node-count" value="1">
    </div>
    <div class="form-group">
        <label for="cpu-count">Total CPU Count</label>
        <input type="number" class="form-control" name="cpu-count" id="cpu-count" value="1">
    </div>
    <div class="form-group">
        <label for="threads">Number of Threads</label>
        <input type="number" class="form-control" name="threads" id="threads" value="0">
    </div>
    <div class="form-group">
        <label for="wall-time">Wall Time Limit</label>
        <input type="number" class="form-control" name="wall-time" id="wall-time" value="15">
    </div>
    <div class="form-group">
        <label for="memory">Total Physical Memory</label>
        <input type="number" class="form-control" name="memory" id="memory" value="0">
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
            print_success_message("Experiment {$_POST['experiment-name']} created!");
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


<?php
session_start();
include 'utilities.php';

use Airavata\Model\Workspace\Experiment\ExperimentState;
use Airavata\API\Error\InvalidRequestException;
use Airavata\API\Error\AiravataClientException;
use Airavata\API\Error\AiravataSystemException;
use Airavata\API\Error\ExperimentNotFoundException;
use Thrift\Exception\TTransportException;



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
      <a class="navbar-brand" href="#">PHP Reference Gateway</a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
        <li><a href="home.php">Home</a></li>
        <li><a href="create_experiment.php">Create experiment</a></li>
        <li class="active"><a href="manage_experiments.php">Manage experiments</a></li>    
      </ul>
      
      <ul class="nav navbar-nav navbar-right">
        <li><a href="logout.php">Log out</a></li>
      </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>

    
<div class="container">
    
<h3>Search for Experiments</h3>

<form action="<?php echo $_SERVER['PHP_SELF']?>" method="post" role="form">
    <div class="form-group">
        <label for="search-key">Search Key</label>
        <select class="form-control" name="search-key" id="search-key">
            <?php

            // set up options for select input
            $values = array('experiment-name', 'project', 'resource', 'submitted-user', 'experiment-status');
            $labels = array('Experiment Name', 'Project', 'Resource', 'Submitted User', 'Experiment Status');
            $disabled = array('disabled', '', 'disabled', '', 'disabled');

            create_options($values, $labels, $disabled);

            ?>
        </select>
    </div>

    <div class="form-group">
        <label for="search-value">Value</label>
        <input type="search" class="form-control" name="search-value" id="search-value" 
               value="<?php if (isset($_POST['search-value'])) echo $_POST['search-value'] ?>">
    </div>

    <input name="search" type="submit" class="btn btn-primary" value="Search">
</form>





<?php

if (isset($_POST['search']) || isset($_POST['details']) || isset($_POST['launch']) || isset($_POST['clone']) || isset($_POST['end']))
{
    /**
     * get results
     */
    $experiments = get_search_results();


    /**
     * display results
     */
    echo '<div class="panel panel-default">';
                
    echo '<div class="panel-heading">';
    echo '<h3>Results</h3>';
    echo '</div>';
                
    echo '<div class="panel-body">';
    echo '<form action="' . $_SERVER['PHP_SELF'] . '" method="post" role="form">';

    create_results_radio_buttons($experiments);


    /**
     * display results or a message, depending on which button was pressed
     */
    if (isset($_POST['details']) and isset($_POST['experiment-id']))
    {
        $experiment = get_experiment($_POST['experiment-id']);

        $experimentStatusString = get_experiment_status($_POST['experiment-id']);


        echo '<div class="well">';
        echo "<p><strong>Experiment ID:</strong> {$experiment->name}</p>";
        echo "<p><strong>Experiment Status:</strong> {$experimentStatusString}</p>";
        echo '</div>';

    }

    if (isset($_POST['launch']) and isset($_POST['experiment-id']))
    {
        launch_experiment($_POST['experiment-id']);

        print_success_message("Experiment {$_POST['experiment-id']} launched!");
    }

    if (isset($_POST['clone']) and isset($_POST['experiment-id']))
    {
        clone_experiment($_POST['experiment-id']);

        print_success_message("Experiment {$_POST['experiment-id']} cloned!");
    }

    if (isset($_POST['end']) and isset($_POST['experiment-id']))
    {
        end_experiment($_POST['experiment-id']);

        print_success_message("Experiment {$_POST['experiment-id']} ended!");
    }


    /**
     * Display form submit buttons
     */

    echo '<div class="btn-toolbar">
        <div class="btn-group"> 
        <input name="details" type="submit" class="btn btn-info" value="Details">
        <input name="launch" type="submit" class="btn btn-primary" value="Launch">
        <input name="clone" type="submit" class="btn btn-primary" value="Clone">
        <input name="end" type="submit" class="btn btn-warning" value="End">
        </div>
        <input name="clear" type="submit" class="btn btn-default" value="Clear">
        </div>';

    echo '</form>';
    echo '</div>'; 
    echo '</div>';
}


//$transport->close();

?>


</div>

</body>
</html>



















<?php
/**
 * Utility Functions
 */


/**
 * Create options for the search key select input
 * @param $values
 * @param $labels
 * @param $disabled
 */
function create_options($values, $labels, $disabled)
{
    for ($i = 0; $i < sizeof($values); $i++)
    {
        $selected = '';

        // if option was previously selected, mark it as selected
        if (isset($_POST['search-key']))
        {
            if ($values[$i] == $_POST['search-key'])
            {
                $selected = 'selected';
            }
        }

        echo '<option value="' . $values[$i] . '" ' . $disabled[$i] . ' ' . $selected . '>' . $labels[$i] . '</option>';
    }
}

/**
 * Get results of the user's search
 * @return array|null
 */
function get_search_results()
{
    global $airavataclient;

    $experiments = array();

    try
    {
        switch ($_POST['search-key'])
        {
            case 'submitted-user':
                $experiments = $airavataclient->getAllUserExperiments($_POST['search-value']);
                break;
            case 'project':
                $experiments = $airavataclient->getAllExperimentsInProject($_POST['search-value']);
                break;
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
    catch (TTransportException $tte)
    {
        print_error_message('TTransportException!<br><br>' . $tte->getMessage());
    }

    return $experiments;
}

/**
 * Create radio buttons for the given set of experiments
 * @param $experiments
 */
function create_results_radio_buttons($experiments)
{
    $checked_array = [];

    for ($i = 0; $i < sizeof($experiments); $i++)
    {
        if (isset($_POST['experiment-id'])) // experiment previously selected
        {
            // filled in radio button for previously-selected experiment
            if($_POST['experiment-id'] == $experiments[$i]->experimentID)
            {
                $checked_array[] = 'checked';
            }
            else
            {
                $checked_array[] = '';
            }
        }
        else // no experiments selected
        {
            $checked_array[] = '';
        }

        echo '<div class="radio"><label><input type="radio" name="experiment-id" value="' . $experiments[$i]->experimentID . '" ' . $checked_array[$i] . '>' . $experiments[$i]->name . '</label></div>';
    }

    // include hidden inputs to populate previously-filled-in inputs
    echo '<input type="hidden" name="search-key" value="' . $_POST['search-key'] . '">';
    echo '<input type="hidden" name="search-value" value="' . $_POST['search-value'] . '">';
}

/**
 * Get the experiment with the given ID
 * @param $expId
 * @return null
 */
function get_experiment($expId)
{
    global $airavataclient;

    try
    {
        return $airavataclient->getExperiment($expId);
    }
    catch (InvalidRequestException $ire)
    {
        print_error_message('InvalidRequestException!<br><br>' . $ire->getMessage());
    }
    catch (ExperimentNotFoundException $enf)
    {
        print_error_message('ExperimentNotFoundException!<br><br>' . $enf->getMessage());
    }
    catch (AiravataClientException $ace)
    {
        print_error_message('AiravataClientException!<br><br>' . $ace->getMessage());
    }
    catch (AiravataSystemException $ase)
    {
        print_error_message('AiravataSystemException!<br><br>' . $ase->getMessage());
    }
    catch (TTransportException $tte)
    {
        print_error_message('TTransportException!<br><br>' . $tte->getMessage());
    }
    catch (Exception $e)
    {
        print_error_message('Exception!<br><br>' . $e->getMessage());
    }

}

/**
 * Get a string containing the given experiment's status
 * @param $expId
 * @return mixed
 */
function get_experiment_status($expId)
{
    global $airavataclient;

    try
    {
        $experimentStatus = $airavataclient->getExperimentStatus($expId);
    }
    catch (InvalidRequestException $ire)
    {
        print_error_message('InvalidRequestException!<br><br>' . $ire->getMessage());
    }
    catch (ExperimentNotFoundException $enf)
    {
        print_error_message('ExperimentNotFoundException!<br><br>' . $enf->getMessage());
    }
    catch (AiravataClientException $ace)
    {
        print_error_message('AiravataClientException!<br><br>' . $ace->getMessage());
    }
    catch (AiravataSystemException $ase)
    {
        print_error_message('AiravataSystemException!<br><br>' . $ase->getMessage());
    }
    catch (Exception $e)
    {
        print_error_message('Exception!<br><br>' . $e->getMessage());
    }

    return ExperimentState::$__names[$experimentStatus->experimentState];
}

/**
 * Launch the experiment with the given ID
 * @param $expId
 */
function launch_experiment($expId)
{
    global $airavataclient;

    try
    {
        $airavataclient->launchExperiment($expId, 'airavataToken');
    }
    catch (InvalidRequestException $ire)
    {
        print_error_message('InvalidRequestException!<br><br>' . $ire->getMessage());
    }
    catch (ExperimentNotFoundException $enf)
    {
        print_error_message('ExperimentNotFoundException!<br><br>' . $enf->getMessage());
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

/**
 * Clone the experiment with the given ID
 * @param $expId
 */
function clone_experiment($expId)
{
    global $airavataclient;

    try
    {
        //create new experiment to receive the clone
        $experiment = $airavataclient->getExperiment($expId);
        $experiment->name .= '-clone';


        $airavataclient->cloneExperiment($expId, $experiment);
    }
    catch (InvalidRequestException $ire)
    {
        print_error_message('InvalidRequestException!<br><br>' . $ire->getMessage());
    }
    catch (ExperimentNotFoundException $enf)
    {
        print_error_message('ExperimentNotFoundException!<br><br>' . $enf->getMessage());
    }
    catch (AiravataClientException $ace)
    {
        print_error_message('AiravataClientException!<br><br>' . $ace->getMessage());
    }
    catch (AiravataSystemException $ase)
    {
        print_error_message('AiravataSystemException!<br><br>' . $ase->getMessage());
    }
    catch (TTransportException $tte)
    {
        print_error_message('TTransportException!<br><br>' . $tte->getMessage());
    }
}

/**
 * End the experiment with the given ID
 * @param $expId
 */
function end_experiment($expId)
{
    global $airavataclient;

    try
    {
        $airavataclient->terminateExperiment($expId);
    }
    catch (InvalidRequestException $ire)
    {
        print_error_message('InvalidRequestException!<br><br>' . $ire->getMessage());
    }
    catch (ExperimentNotFoundException $enf)
    {
        print_error_message('ExperimentNotFoundException!<br><br>' . $enf->getMessage());
    }
    catch (AiravataClientException $ace)
    {
        print_error_message('AiravataClientException!<br><br>' . $ace->getMessage());
    }
    catch (AiravataSystemException $ase)
    {
        print_error_message('AiravataSystemException!<br><br>' . $ase->getMessage());
    }
    catch (TTransportException $tte)
    {
        print_error_message('TTransportException!<br><br>' . $tte->getMessage());
    }
    catch (Exception $e)
    {
        print_error_message('Exception!<br><br>' . $e->getMessage());
    }
}

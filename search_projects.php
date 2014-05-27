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

    <?php create_nav_bar(); ?>

    <div class="container">

        <h1>Search for Projects</h1>

        <form action="<?php echo $_SERVER['PHP_SELF']?>" method="post" class="form-inline" role="form">
            <div class="form-group">
                <label for="search-key">Search</label>
                <select class="form-control" name="search-key" id="search-key">
                    <option value="project-name">Project Name</option>
                    <option value="project-description">Project description</option>
                </select>
            </div>

            <div class="form-group">
                <label for="search-value">for</label>
                <input type="search" class="form-control" name="search-value" id="search-value" placeholder="value" required
                       value="<?php if (isset($_POST['search-value'])) echo $_POST['search-value'] ?>">
            </div>

            <input name="search" type="submit" class="btn btn-primary" value="Search">
        </form>





        <?php

        if (isset($_POST['search']) ||
            isset($_POST['details']) ||
            isset($_POST['launch']) ||
            isset($_POST['clone']) ||
            isset($_POST['cancel']))
        {
            /**
             * get results
             */
            $projects = get_search_results();

            /**
             * display results
             */
            if (sizeof($projects) == 0)
            {
                print_warning_message('No results found. Please try again.');
            }
            else
            {
                foreach ($projects as $project)
                {
                    echo '<div class="panel panel-default">';

                    echo '<div class="panel-heading">';
                    echo "<h3>$project->name</h3>";
                    echo "<p>$project->description</p>";
                    echo '</div>';

                    $experiments = get_experiments_in_project($project->projectID);

                    echo '<table class="table">';

                    echo '<tr>';

                    echo '<th>Name</th>';
                    echo '<th>Application</th>';
                    echo '<th>Status</th>';
                    echo '<th>Details</th>';

                    echo '</tr>';

                    foreach ($experiments as $experiment)
                    {
                        $experimentStatus = $experiment->experimentStatus;
                        $experimentState = $experimentStatus->experimentState;
                        $experimentStatusString = ExperimentState::$__names[$experimentState];
                        $experimentTimeOfStateChange = $experimentStatus->timeOfStateChange;


                        echo '<tr>';

                        echo '<td>';


                        switch ($experimentStatusString)
                        {
                            case 'SCHEDULED':
                            case 'LAUNCHED':
                            case 'EXECUTING':
                            case 'CANCELING':
                            case 'COMPLETED':
                                echo $experiment->name;
                                break;
                            default:
                                echo '$experiment->name<a href="edit_experiment.php?expId=' . $experiment->experimentID . '">
                <span class="glyphicon glyphicon-pencil"></span></a>';
                                break;
                        }



                        echo '</td>';

                        echo "<td>$experiment->applicationId</td>";



                        //echo '<td>' . $experimentStatusString . ' at ' . date("Y-m-d H:i:s", $experimentTimeOfStateChange) . '</td>';
                        echo '<td>' . $experimentStatusString . '</td>';

                        echo '<td><a href="experiment_summary.php?expId=' . $experiment->experimentID . '">Details</a></td>';

                        echo '</tr>';
                    }

                    echo '</table>';
                    echo '</div>';
                }
            }

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

    $projects = array();

    try
    {
        switch ($_POST['search-key'])
        {
            case 'project-name':
                $projects = $airavataclient->searchProjectsByProjectName($_SESSION['username'], $_POST['search-value']);
                break;
            case 'project-description':
                $projects = $airavataclient->searchProjectsByProjectDesc($_SESSION['username'], $_POST['search-value']);
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

    return $projects;
}

/**
 * Create radio buttons for the given set of experiments
 * @param $experiments
 */
function create_results_radio_buttons($experiments)
{
    $checked_array = array();

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
 * Get experiments in project
 * @param $projectId
 * @return array|null
 */
function get_experiments_in_project($projectId)
{
    global $airavataclient;

    $experiments = array();

    try
    {
        $experiments = $airavataclient->getAllExperimentsInProject($projectId);
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


<?php
session_start();
include 'utilities.php';

use Airavata\Model\Workspace\Experiment\ExperimentState;
use Airavata\API\Error\InvalidRequestException;
use Airavata\API\Error\AiravataClientException;
use Airavata\API\Error\AiravataSystemException;
use Airavata\API\Error\ExperimentNotFoundException;
use Thrift\Exception\TTransportException;

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
    
<h1>Browse Experiments</h1>


<?php

$userProjects = get_all_user_projects($_SESSION['username']);

foreach ($userProjects as $project)
{
    echo '<div class="panel panel-default">';

    echo '<div class="panel-heading">';
    echo "<h3>$project->name</h3>";
    echo "<p>$project->description</p>";
    echo '</div>';

    $experiments = get_experiments_in_project($project->projectID);

    echo '<div class="table-responsive">';
    echo '<table class="table">';

    echo '<tr>';

    echo '<th>Name</th>';
    echo '<th>Application</th>';
    echo '<th>Time</th>';
    echo '<th>Status</th>';

    echo '</tr>';

    foreach ($experiments as $experiment)
    {
        $experimentStatus = $experiment->experimentStatus;
        $experimentState = $experimentStatus->experimentState;
        $experimentStatusString = ExperimentState::$__names[$experimentState];
        $experimentTimeOfStateChange = date('Y-m-d H:i:s', $experimentStatus->timeOfStateChange/1000);// divide by 1000 since timeOfStateChange is in ms


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
                echo $experiment->name .
                    ' <a href="edit_experiment.php?expId=' .
                    $experiment->experimentID .
                    '" title="Edit"><span class="glyphicon glyphicon-pencil"></span></a>';
                break;
        }



        echo '</td>';

        echo "<td>$experiment->applicationId</td>";



        echo '<td>' . $experimentTimeOfStateChange . '</td>';


        switch ($experimentStatusString)
        {
            case 'CANCELING':
            case 'CANCELED':
            case 'UNKNOWN':
                $textClass = 'text-warning';
                break;
            case 'FAILED':
                $textClass = 'text-danger';
                break;
            case 'COMPLETED':
                $textClass = 'text-success';
                break;
            default:
                $textClass = 'text-info';
                break;
        }


        echo '<td><a class="' . $textClass .
            '" href="experiment_summary.php?expId=' . $experiment->experimentID .
            '">' . $experimentStatusString .
            '</a></td>';

        echo '</tr>';
    }

    echo '</table>';
    echo '</div>';
    echo '</div>';
}

?>

</div>

</body>
</html>



















<?php
/**
 * Utility Functions
 */


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



unset($_POST);

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

<?php create_head(); ?>

<body>

<?php create_nav_bar(); ?>
    
<div class="container" style="max-width: 750px;">
    
<h1>Search for Experiments</h1>

<form action="<?php echo $_SERVER['PHP_SELF']?>" method="post" class="form-inline" role="form">
    <div class="form-group">
        <label for="search-key">Search by</label>
        <select class="form-control" name="search-key" id="search-key">
            <?php

            // set up options for select input
            $values = array('experiment-name', 'experiment-description', 'application');
            $labels = array('Experiment Name', 'Experiment Description', 'Application');
            $disabled = array('', '', '');

            create_options($values, $labels, $disabled);

            ?>
        </select>
    </div>

    <div class="form-group">
        <label for="search-value">for</label>
        <input type="search" class="form-control" name="search-value" id="search-value" placeholder="value" required
               value="<?php if (isset($_POST['search-value'])) echo $_POST['search-value'] ?>">
    </div>

    <input name="search" type="submit" class="btn btn-primary" value="Search">
    <p class="help-block">You can use a * as a wildcard character. Tip: search for * alone to retrieve all of your experiments.</p>
</form>





<?php

if (isset($_POST['search']))
{
    /**
     * get results
     */
    $experiments = get_search_results();
    //var_dump($experiments[0]);

    if (sizeof($experiments) == 0)
    {
        print_warning_message('No results found. Please try again.');
    }
    else
    {
        echo '
            <div class="table-responsive">
            <table class="table">
                <tr>
                    <th>Name</th>
                    <th>Application</th>
                    <th>Time</th>
                    <th>Status</th>
                </tr>
        ';


        foreach ($experiments as $experiment)
        {
            $experimentStatus = $experiment->experimentStatus;
            $experimentState = $experimentStatus->experimentState;
            $experimentStatusString = ExperimentState::$__names[$experimentState];
            $applicationInterface = get_application_interface($experiment->applicationId);
            //var_dump($experiment);

            switch ($experimentStatusString)
            {
                case 'SCHEDULED':
                case 'LAUNCHED':
                case 'EXECUTING':
                case 'CANCELING':
                case 'COMPLETED':
                    $nameText = $experiment->name;
                    $experimentTime = date('Y-m-d H:i:s', $experimentStatus->timeOfStateChange/1000);// divide by 1000 since timeOfStateChange is in ms
                    break;
                default:
                    $nameText = $experiment->name .
                        ' <a href="edit_experiment.php?expId=' .
                        $experiment->experimentID .
                        '" title="Edit"><span class="glyphicon glyphicon-pencil"></span></a>';
                    $experimentTime = date('Y-m-d H:i:s', $experiment->creationTime/1000);// divide by 1000 since timeOfStateChange is in ms
                    break;
            }




            echo '<tr>';

            echo '<td>' . $nameText . '</td>';

            echo "<td>$applicationInterface->applicationName</td>";

            echo '<td>' . $experimentTime . '</td>';


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


            echo '<td><a class="' .
                $textClass .
                '" href="experiment_summary.php?expId=' .
                $experiment->experimentID .
                '">' .
                $experimentStatusString .
                '</a></td>';

            echo '</tr>';
        }

        echo '
            </table>
            </div>
            ';
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

    $experiments = array();

    try
    {
        switch ($_POST['search-key'])
        {
            case 'experiment-name':
                $experiments = $airavataclient->searchExperimentsByName($_SESSION['username'], $_POST['search-value']);
                break;
            case 'experiment-description':
                $experiments = $airavataclient->searchExperimentsByDesc($_SESSION['username'], $_POST['search-value']);
                break;
            case 'application':
                $experiments = $airavataclient->searchExperimentsByApplication($_SESSION['username'], $_POST['search-value']);
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
        if ($ase->airavataErrorType == 2) // 2 = INTERNAL_ERROR
        {
            print_info_message('<p>You have not created any experiments yet, so no results will be returned!</p>
                                <p>Click <a href="create_experiment.php">here</a> to create an experiment, or
                                <a href="create_project.php">here</a> to create a new project.</p>');
        }
        else
        {
            print_error_message('There was a problem with Airavata. Please try again later or report a bug using the link in the Help menu.');
            //print_error_message('AiravataSystemException!<br><br>' . $ase->airavataErrorType . ': ' . $ase->getMessage());
        }
    }
    catch (TTransportException $tte)
    {
        print_error_message('TTransportException!<br><br>' . $tte->getMessage());
    }

    return $experiments;
}

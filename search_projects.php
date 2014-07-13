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

        <h1>Search for Projects</h1>

        <form action="<?php echo $_SERVER['PHP_SELF']?>" method="post" class="form-inline" role="form">
            <div class="form-group">
                <label for="search-key">Search by</label>
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
            <p class="help-block">You can use * as a wildcard character. Tip: search for * alone to retrieve all of your projects.</p>
        </form>





        <?php

        if (isset($_POST['search']))
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
                echo '<div class="table-responsive">';
                echo '<table class="table">';

                echo '<tr>';

                echo '<th>Name</th>';
                echo '<th>Creation Time</th>';
                echo '<th>Experiments</th>';

                echo '</tr>';

                foreach ($projects as $project)
                {
                    echo '<tr>';
                    echo '<td>' . $project->name . ' <a href="edit_project.php?projId=' .
                        $project->projectID .
                        '" title="Edit"><span class="glyphicon glyphicon-pencil"></span></a></td>';
                    echo '<td>' . date('Y-m-d H:i:s', $project->creationTime/1000) . '</td>';
                    echo '<td><a href="project_summary.php?projId=' . $project->projectID . '">View</a></td>';
                    echo '</tr>';
                }

                echo '</table>';
                echo '</div>';
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
        if ($ase->airavataErrorType == 2) // 2 = INTERNAL_ERROR
        {
            print_info_message('<p>You have not created any projects yet, so no results will be returned!</p>
                                <p>Click <a href="create_project.php">here</a> to create a new project.</p>');
        }
        else
        {
            print_error_message('There was a problem with Airavata. Please try again later, or report a bug using the link in the Help menu.');
            //print_error_message('AiravataSystemException!<br><br>' . $ase->airavataErrorType . ': ' . $ase->getMessage());
        }
    }
    catch (TTransportException $tte)
    {
        print_error_message('TTransportException!<br><br>' . $tte->getMessage());
    }

    return $projects;
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

unset($_POST);

<?php
require 'check_login.php';

/**
 * import Thrift and Airavata
 */
$GLOBALS['THRIFT_ROOT'] = './lib/Thrift/';
require_once $GLOBALS['THRIFT_ROOT'] . 'Transport/TTransport.php';
require_once $GLOBALS['THRIFT_ROOT'] . 'Transport/TSocket.php';
require_once $GLOBALS['THRIFT_ROOT'] . 'Protocol/TProtocol.php';
require_once $GLOBALS['THRIFT_ROOT'] . 'Protocol/TBinaryProtocol.php';
require_once $GLOBALS['THRIFT_ROOT'] . 'Exception/TException.php';
require_once $GLOBALS['THRIFT_ROOT'] . 'Exception/TApplicationException.php';
require_once $GLOBALS['THRIFT_ROOT'] . 'Exception/TProtocolException.php';
require_once $GLOBALS['THRIFT_ROOT'] . 'Base/TBase.php';
require_once $GLOBALS['THRIFT_ROOT'] . 'Type/TType.php';
require_once $GLOBALS['THRIFT_ROOT'] . 'Type/TMessageType.php';
require_once $GLOBALS['THRIFT_ROOT'] . 'Factory/TStringFuncFactory.php';
require_once $GLOBALS['THRIFT_ROOT'] . 'StringFunc/TStringFunc.php';
require_once $GLOBALS['THRIFT_ROOT'] . 'StringFunc/Core.php';

$GLOBALS['AIRAVATA_ROOT'] = './lib/Airavata/';
require_once $GLOBALS['AIRAVATA_ROOT'] . 'API/Airavata.php';
require_once $GLOBALS['AIRAVATA_ROOT'] . 'Model/Workspace/Experiment/Types.php';
require_once $GLOBALS['AIRAVATA_ROOT'] . 'API/Error/Types.php';

use Airavata\Model\Workspace\Experiment\ComputationalResourceScheduling;
use Airavata\Model\Workspace\Experiment\DataObjectType;
use Airavata\Model\Workspace\Experiment\UserConfigurationData;
use Thrift\Protocol\TBinaryProtocol;
use Thrift\Transport\TSocket;
use Airavata\API\AiravataClient;
use Airavata\Model\Workspace\Experiment\Experiment;
use Airavata\Model\Workspace\Experiment\ExperimentState;



//checking if the user is logged in
if($logged_in == false)//user not logged in, redirect him to the login page
{
    echo "User not logged in!";
    echo "<meta http-equiv='Refresh' content='0; URL=login.php'>";
}


$transport = new TSocket('gw111.iu.xsede.org', 8930);
$protocol = new TBinaryProtocol($transport);

$airavataclient = new AiravataClient($protocol);
$transport->open();
?>

<html>
<head>
    <title>Manage Experiments</title>
</head>


<body>

<div><h1>Manage Experiments</h1></div>


<ul id="nav">
    <li><a href="home.php">Home</a></li>
    <li><a href="create_experiment.php">Create experiment</a></li>
    <li><a href="manage_experiments.php">Manage experiments</a></li>
    <li><a href="logout.php">Log out</a></li>
</ul>


<h3>Search for Experiments</h3>

<form action="<?php echo $_SERVER['PHP_SELF']?>" method="post">
    <div>
        <label for="search-key">Search Key:</label>
        <select name="search-key" id="search-key">
            <?php
            $values = array("experiment-name", "project", "resource", "submitted-user", "experiment-status");
            $labels = array("Experiment Name", "Project", "Resource", "Submitted User", "Experiment Status");
            $disabled = array("disabled", "", "disabled", "", "disabled");

            for ($i = 0; $i < sizeof($values); $i++)
            {
                $selected = "";

                if (isset($_POST['search-key']))
                {
                    if ($values[$i] == $_POST['search-key'])
                    {
                        $selected = "selected";
                    }
                }

                echo '<option value="' . $values[$i] . '" ' . $disabled[$i] . ' ' . $selected . '>' . $labels[$i] . '</option>';
            }
            ?>
        </select>
    </div>

    <div>
        <label for="search-value">Value:</label>
        <input type="search" name="search-value" id="search-value" value="<?php if (isset($_POST['search-value'])) echo $_POST['search-value'] ?>">
    </div>

    <input name="search" type="submit" value="Search">
</form>



<?php
if (isset($_POST['search']) || isset($_POST['details']) || isset($_POST['launch']) || isset($_POST['clone']) || isset($_POST['end']))
{
    try
    {
        switch ($_POST['search-key'])
        {
            case "submitted-user":
                $userExperiments = $airavataclient->getAllUserExperiments($_POST["search-value"]);
                break;
            case "project":
                $userExperiments = $airavataclient->getAllExperimentsInProject($_POST["search-value"]);
                break;
        }
    }
    catch (TException $texp)
    {
        print 'Exception: ' . $texp->getMessage()."\n";
    }
    catch (AiravataSystemException $ase)
    {
        print 'Airavata System Exception: ' . $ase->getMessage()."\n";
    }





    echo "<h3>Results</h3>";

    echo '<form action="' . $_SERVER['PHP_SELF'] . '" method="post">';

    $checked_array = [];

    for ($i = 0; $i < sizeof($userExperiments); $i++)
    {
        if (isset($_POST['experiment-id']))
        {
            if($_POST['experiment-id'] == $userExperiments[$i]->experimentID)
            {
                $checked_array[] = "checked";
            }
            else
            {
                $checked_array[] = "";
            }
        }
        else
        {
            $checked_array[] = "";
        }

        echo '<div><label><input type="radio" name="experiment-id" value="' . $userExperiments[$i]->experimentID . '" ' . $checked_array[$i] . '>' . $userExperiments[$i]->name . '</label></div>';
    }


    echo '<input type="hidden" name="search-key" value="' . $_POST['search-key'] . '">';
    echo '<input type="hidden" name="search-value" value="' . $_POST['search-value'] . '">';

    if (isset($_POST['details']) and isset($_POST['experiment-id']))
    {
        try
        {
            $experimentStatus = $airavataclient->getExperimentStatus($_POST['experiment-id']);

            $experimentStatusString = ExperimentState::$__names[$experimentStatus->experimentState];
        }
        catch (TException $texp)
        {
            print 'Exception: ' . $texp->getMessage()."\n";
        }
        catch (AiravataSystemException $ase)
        {
            print 'Airavata System Exception: ' . $ase->getMessage()."\n";
        }


        echo '<div>';
        echo '<p>Experiment ID: ' . $_POST['experiment-id'] . '</p>';
        echo '<p>Experiment Status: ' . $experimentStatusString . '</p>';
        echo '</div>';

    }
    if (isset($_POST['launch']) and isset($_POST['experiment-id']))
    {
        echo "<div>Experiment " . $_POST['experiment-id'] . " launched!</div>";

        try
        {
            $airavataclient->launchExperiment($_POST['experiment-id'], "airavataToken");
        }
        catch (TException $texp)
        {
            print 'Exception: ' . $texp->getMessage()."\n";
        }
        catch (AiravataSystemException $ase)
        {
            print 'Airavata System Exception: ' . $ase->getMessage()."\n";
        }
    }
    if (isset($_POST['clone']) and isset($_POST['experiment-id']))
    {
        echo "<div>Experiment " . $_POST['experiment-id'] . " cloned!</div>";

        try
        {
            //create new experiment to receive the clone


            //$airavataclient->cloneExperiment($_POST['experiment-id'], $updatedExperiment);
        }
        catch (TException $texp)
        {
            print 'Exception: ' . $texp->getMessage()."\n";
        }
        catch (AiravataSystemException $ase)
        {
            print 'Airavata System Exception: ' . $ase->getMessage()."\n";
        }
    }
    if (isset($_POST['end']) and isset($_POST['experiment-id']))
    {
        echo "<div>Experiment " . $_POST['experiment-id'] . " ended!</div>";

        try
        {
            $airavataclient->terminateExperiment($_POST['experiment-id']);
        }
        catch (TException $texp)
        {
            print 'Exception: ' . $texp->getMessage()."\n";
        }
        catch (AiravataSystemException $ase)
        {
            print 'Airavata System Exception: ' . $ase->getMessage()."\n";
        }
    }

    echo '<input name="details" type="submit" value="Details">
        <input name="launch" type="submit" value="Launch">
        <input name="clone" type="submit" value="Clone">
        <input name="end" type="submit" value="End">
        <input name="clear" type="submit" value="Clear">';

    echo '</form>';
}


$transport->close();
?>










</body>
</html>


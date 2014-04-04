<?php
require 'check_login.php';


//checking if the user is logged in
if($logged_in == false)//user not logged in, redirect him to the login page
{
    echo "User not logged in!";
    echo "<meta http-equiv='Refresh' content='0; URL=login.php'>";
}
else //user logged in
{

}
?>

<html>
<head>
    <title>Manage Experiments</title>
</head>


<body>

<h1>Manage Experiments</h1>

<div id="nav">
    <a href="home.php">Home</a>
    <a href="create_experiment.php">Create experiment</a>
    <a href="manage_experiments.php">Manage experiments</a>
    <a href="logout.php">Log out</a>
</div>




<?php

if (isset($_POST['launch']))
{
    echo "Experiment " . $_POST['experiment-id'] . " launched!";
}
if (isset($_POST['clone']))
{
    echo "Experiment " . $_POST['experiment-id'] . " cloned!";
}
if (isset($_POST['end']))
{
    echo "Experiment " . $_POST['experiment-id'] . " ended!";
}
if (isset($_POST['clear']))
{
    echo "Values cleared!";
}


?>



<form action="<?php echo $_SERVER['PHP_SELF']?>" method="post">
    <label for="search-key">Search Key:</label>
    <select name="search-key">
        <option value="experiment-name">Experiment Name</option>
        <option value="project">Project</option>
        <option value="resource">Resource</option>
        <option value="submitted-user">Submitted User</option>
        <option value="experiment-status">Experiment Status</option>
    </select>
    <label for="search-value">Value:</label>
    <input type="search" name="search-value" value="<?php if (isset($_POST['search-value'])) echo $_POST['search-value'] ?>">

    <input name="search" type="submit" value="Search">
</form>

<form action="<?php echo $_SERVER['PHP_SELF']?>" method="post">

<?php
if (isset($_POST['search']) || isset($_POST['details']))
{
    $checked_array = [];

    for ($i = 0; $i < 3; $i++)
    {
        if (isset($_POST['details']) && $_POST['experiment-id'] == $i)
        {
            $checked_array[] = "checked";
        }
        else
        {
            $checked_array[] = "";
        }
    }



    echo "Results :";

    echo '<input type="radio" name="experiment-id" value="0" ' . $checked_array[0] . '>Experiment 1
        <input type="radio" name="experiment-id" value="1" ' . $checked_array[1] . '>Experiment 2
        <input type="radio" name="experiment-id" value="2" ' . $checked_array[2] . '>Experiment 3
        <input type="hidden" name="search-value" value="' . $_POST['search-value'] . '"><br>';

    echo '<input name="details" type="submit" value="Details">';

    if (isset($_POST['details']))
    {
        echo $_POST['experiment-id'];
    }

    echo '<input name="launch" type="submit" value="Launch">
        <input name="clone" type="submit" value="Clone">
        <input name="end" type="submit" value="End">
        <input name="clear" type="submit" value="Clear">';
}
?>
</form>









</body>
</html>


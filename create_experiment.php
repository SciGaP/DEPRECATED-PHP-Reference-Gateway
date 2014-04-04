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
    <title>Create Experiment</title>
</head>


<body>

<h1>Create Experiment</h1>

<div id="nav">
    <a href="home.php">Home</a>
    <a href="create_experiment.php">Create experiment</a>
    <a href="manage_experiments.php">Manage experiments</a>
    <a href="logout.php">Log out</a>
</div>




<?php

if (isset($_POST['save']))
{
    echo "Experiment " . $_POST['experiment-name'] . " created!";
}
if (isset($_POST['clear']))
{
    echo "Values cleared!";
}
if (isset($_POST['launch']))
{
    echo "Experiment " . $_POST['experiment-name'] . " saved and launched!";
}

?>



<form action="<?php echo $_SERVER['PHP_SELF']?>" method="post">
    <label for="experiment-name">Experiment Name:</label><input type="text" name="experiment-name">
    <label for="project">Project:</label><input type="text" name="project">
    <label for="experiment-input">Experiment input:</label><input type="text" name="experiment-input">
    <label for="application">Application:</label><input type="text" name="application">
    <label for="compute-resource">Compute Resource:</label><input type="text" name="compute-resource">
    <label for="cpu-count">CPU Count:</label><input type="text" name="cpu-count">
    <label for="wall-time">Wall Time:</label><input type="text" name="wall-time">
    <label for="experiment-description">Experiment Description:</label><input type="text" name="experiment-description">
    <input name="save" type="submit" value="Save">
    <input name="clear" type="submit" value="Clear">
    <input name="launch" type="submit" value="Save and Launch">
</form>


</body>
</html>


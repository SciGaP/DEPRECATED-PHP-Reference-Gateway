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
    <title>Home</title>
</head>


<body>

<h1>Home</h1>

<div id="nav">
    <a href="home.php">Home</a>
    <a href="create_experiment.php">Create experiment</a>
    <a href="manage_experiments.php">Manage experiments</a>
    <a href="logout.php">Log out</a>
</div>

</body>
</html>


<?php
require 'check_login.php';


//checking if the user is logged in
if($logged_in == false)//user not logged in, redirect him to the login page
{
    echo "User not logged in!";
    echo "<meta http-equiv='Refresh' content='0; URL=login.php'>";
}
?>

<html>
<head>
    <title>Home</title>
</head>


<body>

<div><h1>Home</h1></div>

<ul id="nav">
    <li><a href="home.php">Home</a></li>
    <li><a href="create_experiment.php">Create experiment</a></li>
    <li><a href="manage_experiments.php">Manage experiments</a></li>
    <li><a href="logout.php">Log out</a></li>
</ul>

</body>
</html>


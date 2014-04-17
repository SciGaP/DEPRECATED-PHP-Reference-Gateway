<?php
/**
 * A user's homepage
 */
session_start();
include 'utilities.php';

connect_to_id_store();
verify_login();

?>

<html>
<head>
    <title>Home</title>
</head>


<body>

<div>
    <h1>Home</h1>
</div>

<ul id="nav">
    <li><a href="home.php">Home</a></li>
    <li><a href="create_experiment.php">Create experiment</a></li>
    <li><a href="manage_experiments.php">Manage experiments</a></li>
    <li><a href="logout.php">Log out</a></li>
</ul>

</body>
</html>


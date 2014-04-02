<?php
//include "db_utilities.php";
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

<a href="logout.php">Log out</a>

</body>
</html>


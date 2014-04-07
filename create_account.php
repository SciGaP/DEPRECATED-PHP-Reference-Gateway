<?php
include "db_utilities.php";

$db = connect_to_db();
?>

<html>
<head>
    <title>Create Account</title>
</head>


<body>

<div><h1>Create Account</h1></div>


<?php

if (isset($_POST['Submit']))
{
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (username_in_db($username))
    {
        echo "<div>This username is already in use. Please select another.</div>";
    }
    else
    {
        db_add_user($username,$password);
        echo "<div>New user created!</div>";
        echo "<meta http-equiv='Refresh' content='1; URL=login.php'>";
    }
}

?>





<form action="<?php echo $_SERVER['PHP_SELF']?>" method="post">
    <div><label for="username">Username:</label><input type="text" name="username" id="username"></div>
    <div><label for="password">Password:</label><input type="password" name="password" id="password"></div>
    <input name="Submit" type="submit" value="Submit">
</form>


</body>
</html>
<?php
include "db_utilities.php";

$db = connect_to_db();
?>

<html>
<head>
    <title>Create Account</title>
</head>


<body>

<?php

if (isset($_POST['Submit']))
{
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (username_in_db($username))
    {
        echo "This username is already in use. Please select another.";
    }
    else
    {
        db_add_user($username,$password);
        echo "New user created!";
        echo "<meta http-equiv='Refresh' content='1; URL=login.php'>";
    }
}

?>





<form action="<?php echo $_SERVER['PHP_SELF']?>" method="post">
    <label for="username">Username:</label><input type="text" name="username">
    <label for="password">Password:</label><input type="password" name="password">
    <input name="Submit" type="submit" value="Submit">
</form>


</body>
</html>
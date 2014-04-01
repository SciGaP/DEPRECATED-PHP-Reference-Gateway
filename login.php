<?php
include "db_utilities.php";

$db = connect_to_db();
?>

<html>
<head>
    <title>Login</title>
</head>


<body>

<?php

if (isset($_POST['Submit']))
{
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (username_in_db($username))
    {
        $db_password = db_get_password($username);

        if ($password == $db_password)
        {
            // redirect to home page
            echo "<meta http-equiv='Refresh' content='1; URL=home.php'>";
        }
        elseif ($db_password == "Duplicate users in database!")
        {
            echo "Duplicate users in database!";
        }
        else
        {
            echo "Invalid username or password. Please try again.";
        }
    }
    else
    {
        echo "Invalid username or password. Please try again.";
    }
}

?>





<form action="<?php echo $_SERVER['PHP_SELF']?>" method="post">
    <label for="username">Username:</label><input type="text" name="username">
    <label for="password">Password:</label><input type="password" name="password">
    <input name="Submit" type="submit" value="Submit">
</form>

<a href="create_account.php">Create an account</a>

</body>
</html>
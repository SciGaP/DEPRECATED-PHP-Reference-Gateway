<?php
session_start();
include "db_utilities.php";


$db = connect_to_db();
?>

<html>
<head>
    <title>Login</title>
</head>


<body>

<div><h1>Login</h1></div>


<?php

if (isset($_POST['Submit']))
{
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (username_in_db($username))
    {
        $db_password_hash = db_get_password($username);

        if (md5($password) == $db_password_hash)
        {
            //set session variables
            $_SESSION['username'] = $username;
            $_SESSION['password_hash'] = md5($password);

            // redirect to home page
            echo "<div>Login successful!</div>";
            echo "<meta http-equiv='Refresh' content='1; URL=home.php'>";
        }
        elseif ($db_password_hash == "Duplicate users in database!")
        {
            echo "<div>Duplicate users in database!</div>";
        }
        else
        {
            echo "<div>Invalid username or password. Please try again.</div>";
        }
    }
    else
    {
        echo "<div>Invalid username or password. Please try again.</div>";
    }
}

?>





<form action="<?php echo $_SERVER['PHP_SELF']?>" method="post">
    <div><label for="username">Username:</label><input type="text" name="username"></div>
    <div><label for="password">Password:</label><input type="password" name="password"></div>
    <input name="Submit" type="submit" value="Submit">
</form>

<div><a href="create_account.php">Create an account</a></div>

</body>
</html>
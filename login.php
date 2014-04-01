<?php
/**********************************************************************
 *  Name: login.php	                                                 *
 *  Description: This is the main login page.                         *
 *  The PHP section performs a check on the submitted username        *
 *  and password and accordingly displays either an error message     *
 *  or redirects to the appropriate member/admin home page            *
 *********************************************************************/

function username_in_db($username)
{
    if ($username == "user")
    {
        return true;
    }
    else
    {
        return false;
    }
}

function db_retrieve_password($username)
{
    return "password";
}


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

    // is username in database?
    if (username_in_db($username))
    {
        $db_password = db_retrieve_password($username);

        if ($password == $db_password)
        {
            echo "<meta http-equiv='Refresh' content='1; URL=home.php'>";
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
    <label for="username">Username: </label><input type="text" name="username">
    <label for="password">Password: </label><input type="password" name="password">
    <input name="Submit" type="submit" value="Submit">
</form>

<a href="">Create an account</a>

</body>
</html>


<?php
session_start();
include 'xml_id_utilities.php';

$idStore = new XmlIdUtilities();
$idStore->connect();
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

    if ($idStore->username_exists($username))
    {
        $passwordHash = $idStore->get_password($username);

        if (md5($password) == $passwordHash)
        {
            //set session variables
            $_SESSION['username'] = $username;
            $_SESSION['password_hash'] = md5($password);

            // redirect to home page
            echo '<div>Login successful!</div>';
            echo '<meta http-equiv="Refresh" content="1; URL=home.php">';
        }
        elseif ($passwordHash == 'Duplicate users in database!')
        {
            echo '<div>Duplicate users in database!</div>';
        }
        else
        {
            echo '<div>Invalid username or password. Please try again.</div>';
        }
    }
    else
    {
        echo '<div>Invalid username or password. Please try again.</div>';
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
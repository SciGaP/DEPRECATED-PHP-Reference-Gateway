<?php
/**
 * Allow users to log in or create a new account
 */
session_start();
include 'utilities.php';

connect_to_id_store();
?>

<html>
<head>
    <title>Login</title>
</head>


<body>

<div>
    <h1>Login</h1>
</div>





<?php

if (form_submitted())
{
    $username = $_POST['username'];
    $passwordHash = md5($_POST['password']);

    if (id_matches_db($username, $passwordHash))
    {
        store_id_in_session($username, $passwordHash);
        print_success_message('Login successful!');
        redirect('home.php');
    }
    else
    {
        print_error_message('Invalid username or password. Please try again.');
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

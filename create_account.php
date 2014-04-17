<?php
/**
 * Allow users to create a new user account
 */
include 'utilities.php';

connect_to_id_store();
?>

<html>
<head>
    <title>Create Account</title>
</head>


<body>

<div>
    <h1>Create Account</h1>
</div>





<?php

if (form_submitted())
{
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($idStore->username_exists($username))
    {
        print_error_message('The username you entered is already in use. Please select another.');
    }
    else
    {
        $idStore->add_user($username,$password);
        print_success_message('New user created!');
        redirect('login.php');
    }
}

?>





<form action="<?php echo $_SERVER['PHP_SELF']?>" method="post">
    <div><label for="username">Username:</label><input type="text" name="username" id="username"></div>
    <div><label for="password">Password:</label><input type="password" name="password" id="password"></div>
    <input name="Submit" type="submit" value="Submit">
</form>

<div><a href="login.php">Go to login</a></div>


</body>
</html>
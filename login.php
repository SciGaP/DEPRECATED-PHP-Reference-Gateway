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
    <title>PHP Reference Gateway</title>
  
    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
</head>


<body>

<?php create_nav_bar(); ?>
    
<div class="container">

    <h3>Login</h3>





<?php

if (form_submitted())
{
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (id_matches_db($username, $password))
    {
        store_id_in_session($username);
        print_success_message('Login successful!');
        redirect('home.php');
    }
    else
    {
        print_error_message('Invalid username or password. Please try again.');
    }
}

?>


    <form action="<?php echo $_SERVER['PHP_SELF']?>" method="post" role="form">
        <div class="form-group">
            <label class="sr-only" for="username">Username</label>
            <input type="text" class="form-control" name="username" placeholder="Username" autofocus required>
        </div>
        <div class="form-group">
            <label class="sr-only" for="password">Password</label>
            <input type="password" class="form-control"  name="password" placeholder="Password" required>
        </div>
        <input name="Submit" type="submit" class="btn btn-primary" value="Submit">
    </form>




<!--<a href="create_account.php">Create an account</a>-->
</div>
</body>
</html>

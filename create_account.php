<?php
/**
 * Allow users to create a new user account
 */
include 'utilities.php';

connect_to_id_store();
?>

<html>

<?php create_head(); ?>

<body>

<?php create_nav_bar(); ?>

<div class="container" style="max-width: 330px;">
    
    <h3>Create a new account</h3>




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



 



    <form action="<?php echo $_SERVER['PHP_SELF']?>" method="post" role="form">
        <div class="form-group">
            <label class="sr-only" for="username">Username</label>
            <input type="text" class="form-control" name="username" id="username" placeholder="Username" autofocus required>
        </div>
        <div class="form-group">
            <label class="sr-only" for="password">Password</label>
            <input type="password" class="form-control" name="password" id="password" placeholder="Password" required>
        </div>
        <input name="Submit" type="submit" class="btn btn-primary btn-block" value="Create">
    </form>

    <!--<a href="login.php">Go to login</a>-->

</div>
</body>
</html>
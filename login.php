<?php
/**
 * Allow users to log in or create a new account
 */
session_start();
include 'utilities.php';

connect_to_id_store();
?>

    <html>

    <?php create_head(); ?>

    <body>

    <?php create_nav_bar(); ?>

    <div class="container" style="max-width: 330px;">

        <h3>
            Login
            <small>
                <small> (Not registered? <a href="create_account.php">Create account</a>)</small>
            </small>
        </h3>

        <?php

        if (form_submitted()) {
            $username = $_POST['username'];
            $password = $_POST['password'];
            try {
                if (id_matches_db($username, $password)) {
                    store_id_in_session($username);
                    print_success_message('Login successful! You will be redirected to your home page shortly.');
                    redirect('index.php');
                } else {
                    print_error_message('Invalid username or password. Please try again.');
                }
            } catch (Exception $ex) {
                print_error_message('Invalid username or password. Please try again.');
            }
        }

        ?>


        <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" role="form">
            <div class="form-group">
                <label class="sr-only" for="username">Username</label>
                <input type="text" class="form-control" name="username" placeholder="Username" autofocus required>
            </div>
            <div class="form-group">
                <label class="sr-only" for="password">Password</label>
                <input type="password" class="form-control" name="password" placeholder="Password" required>
            </div>
            <input name="Submit" type="submit" class="btn btn-primary btn-block" value="Sign in">
        </form>
    </div>
    </body>
    </html>

<?php

unset($_POST);

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

<div class="container" style="width: 500px">
    <div class="page-header">
        <h3>Create New Account
            <small>
                <small> (Already registered? <a href="login.php">Log in</a>)</small>
            </small>
        </h3>
    </div>
    <?php

    if (form_submitted() && isset($_POST['username']) && isset($_POST['password']) && isset($_POST['confirm_password'])) {
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        $email = $_POST['email'];
        $organization = $_POST['organization'];

        if ($idStore->username_exists($username)) {
            print_error_message('The username you entered is already in use. Please select another.');
        } else if (strlen($username) < 3) {
            print_error_message('Username should be more than three characters long!');
        } else if ($password != $confirm_password) {
            print_error_message('The passwords that you entered do not match!');
        } else {
            $idStore->add_user($username, $password, $first_name, $last_name, $email, $organization);
            print_success_message('New user created!');
            redirect('login.php');
        }
    }
    ?>

    <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" role="form">
        <div class="form-group required"><label class="control-label">Username</label>

            <div><input class="form-control" id="username" minlength="3" maxlength="30" name="username"
                        placeholder="Username" required="required" title="" type="text"/></div>
        </div>
        <div class="form-group required"><label class="control-label">E-mail</label>

            <div><input class="form-control" id="email" name="email" placeholder="E-mail"
                        required="required" title="" type="email"/></div>
        </div>
        <div class="form-group required"><label class="control-label">Password</label>

            <div><input class="form-control" id="password" name="password" placeholder="Password"
                        required="required" title="" type="password"/></div>
        </div>
        <div class="form-group required"><label class="control-label">Password (again)</label>

            <div><input class="form-control" id="confirm_password" name="confirm_password"
                        placeholder="Password (again)" required="required" title="" type="password"/>
            </div>
        </div>
        <div class="form-group required"><label class="control-label">First Name</label>

            <div><input class="form-control" id="first_name" maxlength="30" name="first_name"
                        placeholder="First Name" required="required" title="" type="text"/></div>
        </div>
        <div class="form-group required"><label class="control-label">Last Name</label>

            <div><input class="form-control" id="last_name" maxlength="30" name="last_name"
                        placeholder="Last Name" required="required" title="" type="text"/></div>
        </div>
        <div class="form-group required"><label class="control-label">Organization</label>

            <div><input class="form-control" id="organization" name="organization"
                        placeholder="Organization" required="required" title="" type="text"/>
            </div>
        </div>
        <br/>
        <input name="Submit" type="submit" class="btn btn-primary btn-block" value="Create">
    </form>

    <style media="screen" type="text/css">
        .form-group.required .control-label:after {
            content: " *";
            color: red;
        }
    </style>
    <br/><br/><br/>
</div>
</body>
</html>
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
        $address = $_POST['address'];
        $country = $_POST['country'];
        $telephone = $_POST['telephone'];
        $mobile = $_POST['mobile'];
        $im = $_POST['im'];
        $url = $_POST['url'];

        if ($idStore->username_exists($username)) {
            print_error_message('The username you entered is already in use. Please select another.');
        } else if (strlen($username) < 3) {
            print_error_message('Username should be more than three characters long!');
        } else if ($password != $confirm_password) {
            print_error_message('The passwords that you entered do not match!');
        }elseif(!isset($first_name)){
            print_error_message('First name is required.');
        }elseif(!isset($last_name)){
            print_error_message('Last name is required.');
        }elseif(!isset($email)){
            print_error_message('Email address is required.');
        }else{
            $idStore->add_user($username, $password, $first_name, $last_name, $email, $organization,
            $address, $country,$telephone, $mobile, $im, $url);
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
        <div class="form-group required"><label class="control-label">Password</label>

            <div><input class="form-control" id="password" name="password" placeholder="Password"
                        required="required" title="" type="password"/></div>
        </div>
        <div class="form-group required"><label class="control-label">Password (again)</label>

            <div><input class="form-control" id="confirm_password" name="confirm_password"
                        placeholder="Password (again)" required="required" title="" type="password"/>
            </div>
        </div>
        <div class="form-group required"><label class="control-label">E-mail</label>

            <div><input class="form-control" id="email" name="email" placeholder="E-mail"
                        required="required" title="" type="email"/></div>
        </div>
        <div class="form-group required"><label class="control-label">First Name</label>

            <div><input class="form-control" id="first_name" maxlength="30" name="first_name"
                        placeholder="First Name" required="required" title="" type="text"/></div>
        </div>
        <div class="form-group required"><label class="control-label">Last Name</label>

            <div><input class="form-control" id="last_name" maxlength="30" name="last_name"
                        placeholder="Last Name" required="required" title="" type="text"/></div>
        </div>
        <div class="form-group"><label class="control-label">Organization</label>

            <div><input class="form-control" id="organization" name="organization"
                        placeholder="Organization" title="" type="text"/>
            </div>
        </div>
        <div class="form-group"><label class="control-label">Address</label>

            <div><input class="form-control" id="address" name="address"
                        placeholder="Address" title="" type="text"/>
            </div>
        </div>
        <div class="form-group"><label class="control-label">Country</label>

            <div><input class="form-control" id="country" name="country"
                        placeholder="Country" title="" type="text"/>
            </div>
        </div>
        <div class="form-group"><label class="control-label">Telephone</label>

            <div><input class="form-control" id="telephone" name="telephone"
                        placeholder="Telephone" title="" type="tel"/>
            </div>
        </div>
        <div class="form-group"><label class="control-label">Mobile</label>

            <div><input class="form-control" id="mobile" name="mobile"
                        placeholder="Mobile" title="" type="tel"/>
            </div>
        </div>
        <div class="form-group"><label class="control-label">IM</label>

            <div><input class="form-control" id="im" name="im"
                        placeholder="IM" title="" type="text"/>
            </div>
        </div>
        <div class="form-group"><label class="control-label">URL</label>

            <div><input class="form-control" id="url" name="url"
                        placeholder="URL" title="" type="text"/>
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

<?php
unset($_POST);

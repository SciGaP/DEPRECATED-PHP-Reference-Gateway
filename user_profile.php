<?php
session_start();
include 'utilities.php';
connect_to_id_store();
verify_login();

?>
<html>

<?php create_head(); ?>

<body>

<?php create_nav_bar(); ?>

<div class="container" style="width: 500px">
    <div class="page-header">
        <h3>Change password</h3>
    </div>

    <?php

    if (form_submitted() && isset($_POST['Password_Update'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $new_password_again = $_POST['new_password_again'];

        if(!isset($current_password)){
            print_error_message('Existing password is required.');
        }elseif(!isset($new_password)){
            print_error_message('New password is required.');
        }elseif(!isset($new_password_again)){
            print_error_message('Retype new password again.');
        }elseif($new_password_again !== $new_password){
            print_error_message('Passwords does not match.');
        }else{
            $idStore->change_password($_SESSION['username'],$current_password, $new_password);
            print_success_message('User profile updated successfully');
        }
    }

    $profile = $idStore->get_user_profile($_SESSION['username']);

    ?>

    <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" role="form">
        <div class="form-group required"><label class="control-label">Current password</label>

            <div><input class="form-control" id="current_password" name="current_password" placeholder="Current password"
                        required="required" title="" type="password"/></div>
        </div>
        <div class="form-group required"><label class="control-label">New Password</label>

            <div><input class="form-control" id="new_password" maxlength="30" name="new_password"
                        placeholder="New Password" required="required" title="" type="password"/></div>
        </div>
        <div class="form-group required"><label class="control-label">New password (again)</label>

            <div><input class="form-control" id="new_password_again" maxlength="30" name="new_password_again"
                        placeholder="New password (again)" required="required" title="" type="password"/></div>
        </div>
        <br/>
        <input name="Submit" type="submit" class="btn btn-primary btn-block" value="Save">
        <input name="Password_Update" type="hidden" value="Password_Update">
    </form>
    <br/><br/>


    <div class="page-header">
        <h3>Update user profile</h3>
    </div>
    <?php

    if (form_submitted() && isset($_POST['Profile_Update'])) {
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $email = $_POST['email'];
        $organization = $_POST['organization'];
        $address = $_POST['address'];
        $country = $_POST['country'];
        $telephone = $_POST['telephone'];
        $mobile = $_POST['mobile'];
        $im = $_POST['im'];
        $url = $_POST['url'];

        if(!isset($first_name)){
            print_error_message('First name is required.');
        }elseif(!isset($last_name)){
            print_error_message('Last name is required.');
        }elseif(!isset($email)){
            print_error_message('Email address is required.');
        }else{
            $idStore->update_user_profile($_SESSION['username'], $first_name, $last_name, $email, $organization,
                $address, $country,$telephone, $mobile, $im, $url);
            print_success_message('User profile updated successfully');
        }
    }

    $profile = $idStore->get_user_profile($_SESSION['username']);

    ?>

    <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" role="form">
        <div class="form-group required"><label class="control-label">E-mail</label>

            <div><input class="form-control" id="email" name="email" placeholder="E-mail"
                        required="required" title="" type="email" value="<?php echo $profile['email_address'] ?>"/></div>
        </div>
        <div class="form-group required"><label class="control-label">First Name</label>

            <div><input class="form-control" id="first_name" maxlength="30" name="first_name"
                        placeholder="First Name" required="required" title="" type="text" value="<?php echo $profile['first_name'] ?>"/></div>
        </div>
        <div class="form-group required"><label class="control-label">Last Name</label>

            <div><input class="form-control" id="last_name" maxlength="30" name="last_name"
                        placeholder="Last Name" required="required" title="" type="text" value="<?php echo $profile['last_name'] ?>"/></div>
        </div>
        <div class="form-group"><label class="control-label">Organization</label>

            <div><input class="form-control" id="organization" name="organization"
                        placeholder="Organization" title="" type="text" value="<?php echo $profile['organization'] ?>"/>
            </div>
        </div>
        <div class="form-group"><label class="control-label">Address</label>

            <div><input class="form-control" id="address" name="address"
                        placeholder="Address" title="" type="text" value="<?php echo $profile['address'] ?>"/>
            </div>
        </div>
        <div class="form-group"><label class="control-label">Country</label>

            <div><input class="form-control" id="country" name="country"
                        placeholder="Country" title="" type="text" value="<?php echo $profile['country'] ?>"/>
            </div>
        </div>
        <div class="form-group"><label class="control-label">Telephone</label>

            <div><input class="form-control" id="telephone" name="telephone"
                        placeholder="Telephone" title="" type="tel" value="<?php echo $profile['telephone'] ?>"/>
            </div>
        </div>
        <div class="form-group"><label class="control-label">Mobile</label>

            <div><input class="form-control" id="mobile" name="mobile"
                        placeholder="Mobile" title="" type="tel" value="<?php echo $profile['mobile'] ?>"/>
            </div>
        </div>
        <div class="form-group"><label class="control-label">IM</label>

            <div><input class="form-control" id="im" name="im"
                        placeholder="IM" title="" type="text" value="<?php echo $profile['im'] ?>"/>
            </div>
        </div>
        <div class="form-group"><label class="control-label">URL</label>

            <div><input class="form-control" id="url" name="url"
                        placeholder="URL" title="" type="text" value="<?php echo $profile['url'] ?>"/>
            </div>
        </div>
        <br/>
        <input name="Submit" type="submit" class="btn btn-primary btn-block" value="Save">
        <input name="Profile_Update" type="hidden" value="Profile_Update">
    </form>

    <style media="screen" type="text/css">
        .form-group.required .control-label:after {
            content: " *";
            color: red;
        }
    </style>
    <br/><br/><br/>

<?php

unset($_POST);

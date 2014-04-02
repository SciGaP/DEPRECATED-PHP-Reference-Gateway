<?php
session_start();
include "db_utilities.php";

$db = connect_to_db();

//sets logged_in as false if any of the session variables are not set
if (!isset($_SESSION['username']) || !isset($_SESSION['password_hash']))
{
    echo "session variables not set!";
    $logged_in = false;
    return;
}
else
{
    $db_password_hash = db_get_password($_SESSION['username']);

    //db_retrieve_password() returns negative integers when there is an error
    if($db_password_hash == -1 || $db_password_hash == -2)
    {
        $logged_in = false;
        unset($_SESSION['username']);
        unset($_SESSION['password_hash']);
    }
    else //valid contact id
    {
        //$db_password_hash=md5($db_password);

        if($db_password_hash==$_SESSION['password_hash'])//correct password hash stored
        {
            $logged_in = true;
        }
        else //incorrect password hash stored
        {
            $logged_in = false;
            unset($_SESSION['username']);
            unset($_SESSION['password_hash']);
        }
    }
}



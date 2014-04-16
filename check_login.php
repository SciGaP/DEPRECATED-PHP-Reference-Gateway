<?php
session_start();
include 'xml_id_utilities.php';

$idStore = new XmlIdUtilities();
$idStore->connect();

//sets logged_in as false if any of the session variables are not set
if (!isset($_SESSION['username']) || !isset($_SESSION['password_hash']))
{
    echo 'session variables not set!';
    $logged_in = false;
    return;
}
else
{
    $passwordHash = $idStore->get_password($_SESSION['username']);

    //get_password() returns negative integers when there is an error
    if($passwordHash == -1 || $passwordHash == -2)
    {
        $logged_in = false;
        unset($_SESSION['username']);
        unset($_SESSION['password_hash']);
    }
    else //valid contact id
    {
        if($passwordHash==$_SESSION['password_hash'])//correct password hash stored
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



<?php
session_start();

//kill session variables
unset($_SESSION['username']);
unset($_SESSION['password_hash']);
unset($_POST);
$_SESSION = array(); // reset session array
session_destroy();   // destroy session.
header('Location: index.php');
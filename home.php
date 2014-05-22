<?php
/**
 * A user's homepage
 */
session_start();
include 'utilities.php';

connect_to_id_store();
verify_login();

?>

<html>
<head>
    <title>PHP Reference Gateway</title>
    
    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
</head>


<body>

<?php create_nav_bar(); ?>
    
<div class="container">
    
    <div class="jumbotron">
        <h1 class="text-center">Welcome to the PHP Reference Gateway!</h1>
    </div>

</div>
</body>
</html>


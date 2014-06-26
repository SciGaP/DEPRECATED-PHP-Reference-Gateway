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

<?php create_head(); ?>

<body>

<?php create_nav_bar(); ?>
    
<div class="container">
    
    <div class="jumbotron">
        <h1 class="text-center">Welcome to the PHP Reference Gateway!</h1>
    </div>

    <?php
        $req_url = 'https://gw111.iu.xsede.org:8443/credential-store/acs-start-servlet';
        $gatewayName = 'PHP-Reference-Gateway';
        $email = 'admin@gw120.iu.xsede.org';
        $username = $_SESSION['username'];

        if(!isset($_GET['tokenId']) && !isset($_SESSION['tokenId']))
        {
            header('Location: ' . $req_url . '?gatewayName=' . $gatewayName . '&email=' . $email . '&portalUserName=' . $username);

            echo '<p>no token</p>';
        }
        else if(isset($_GET['tokenId']))
        {
            $_SESSION['tokenId'] = $_GET['tokenId'];

            echo '<p>token = ' . $_SESSION['tokenId'] . '</p>';
        }
    ?>

</div>
</body>
</html>


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



        if($username == 'admin1') // temporary hard-coded admin user. will replace with admin role in future
        {
            try
            {
                open_tokens_file($tokenFilePath);
            }
            catch (Exception $e)
            {
                print_error_message($e->getMessage());
            }


            if(isset($_GET['tokenId']))
            {
                try
                {
                    write_new_token($_GET['tokenId']);

                    print_success_message('Received new XSEDE token ' . $tokenFile->tokenId .
                        '! Click <a href="' . $req_url .
                        '?gatewayName=' . $gatewayName .
                        '&email=' . $email .
                        '&portalUserName=' . $username .
                        '">here</a> to fetch a new token.');
                }
                catch (Exception $e)
                {
                    print_error_message($e->getMessage());
                }
            }
            else
            {
                print_info_message('Community token currently set to ' . $tokenFile->tokenId .
                    '. Click <a href="' . $req_url .
                    '?gatewayName=' . $gatewayName .
                    '&email=' . $email .
                    '&portalUserName=' . $username .
                    '">here</a> to fetch a new token.');
            }
        }
        else // standard user
        {
            if (isset($_SESSION['tokenId']))
            {
                print_info_message('XSEDE token currently active.
                    All experiments launched during this session will use your personal allocation.');
            }
            elseif(!isset($_GET['tokenId']) && !isset($_SESSION['tokenId']))
            {
                print_info_message('Currently using community allocation. Click <a href="' .
                    $req_url .
                    '?gatewayName=' . $gatewayName .
                    '&email=' . $email .
                    '&portalUserName=' . $username .
                    '">here</a> to use your personal allocation for this session.');
            }
            elseif(isset($_GET['tokenId']))
            {
                $_SESSION['tokenId'] = $_GET['tokenId'];

                print_success_message('Received XSEDE token!' .
                    '<br>All experiments launched during this session will use your personal allocation.');
            }
        }


    ?>

</div>
</body>
</html>

<?php



/**
 * Write the new token to the XML file
 * @param $tokenId
 */
function write_new_token($tokenId)
{
    global $tokenFile;
    global $tokenFilePath;

    // write new tokenId to tokens file
    $tokenFile->tokenId = $tokenId;

    //Format XML to save indented tree rather than one line
    $dom = new DOMDocument('1.0');
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput = true;
    $dom->loadXML($tokenFile->asXML());
    $dom->save($tokenFilePath);
}

?>

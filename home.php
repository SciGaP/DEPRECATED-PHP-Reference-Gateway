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
















            echo '<script type="text/javascript"
                src="https://gateways.atlassian.net/s/31280375aecc888d5140f63e1dc78a93-T/en_USmlc07/6328/46/1.4.13/_/download/batch/com.atlassian.jira.collector.plugin.jira-issue-collector-plugin:issuecollector/com.atlassian.jira.collector.plugin.jira-issue-collector-plugin:issuecollector.js?locale=en-US&collectorId=39f1242f"></script>';

            echo '<script type="text/javascript">
                    window.ATL_JQ_PAGE_PROPS = $.extend(window.ATL_JQ_PAGE_PROPS, {


                    // ==== custom trigger function ====
                    triggerFunction : function( showCollectorDialog ) {
                        $("#feedback-button").on( "click", function(e) {
                            e.preventDefault();
                            showCollectorDialog();
                        });

                        // add any other custom triggers for the issue collector here
                    }

                });

                </script>';

            echo '<a href="#" id="feedback-button" class="btn btn-primary btn-large">Report feedback</a>';









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

<?php
/**
 * A user's homepage
 */
session_start();
include 'utilities.php';

connect_to_id_store();

?>

<html>

<?php create_head(); ?>

<body>

<?php create_nav_bar(); ?>

<div class="jumbotron">
    <div class="container">

        <?php

        if (id_in_session())
        {
            $columnClass = 'col-md-4';

            echo '<h1>Welcome, ' . $_SESSION['username'] . '!</h1>';

            if($_SESSION['username'] == 'admin1') // temporary hard-coded admin user. will replace with admin role in future
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
                            '&portalUserName=' . $_SESSION['username'] .
                            '">here</a> to fetch a new token.');
                    }
                    catch (Exception $e)
                    {
                        print_error_message($e->getMessage());
                    }
                }
                else
                {
                    echo '<p><small>Community token currently set to ' . $tokenFile->tokenId .
                        '. Click <a href="' . $req_url .
                        '?gatewayName=' . $gatewayName .
                        '&email=' . $email .
                        '&portalUserName=' . $_SESSION['username'] .
                        '">here</a> to fetch a new token.</small></p>';
                }
            }
            else // standard user
            {
                if (isset($_SESSION['tokenId']))
                {
                    echo '<p><small>XSEDE token currently active.
                    All experiments launched during this session will use your personal allocation.</small></p>';
                }
                elseif(!isset($_GET['tokenId']) && !isset($_SESSION['tokenId']))
                {
                    echo '<p><small>Currently using community allocation. Click <a href="' .
                        $req_url .
                        '?gatewayName=' . $gatewayName .
                        '&email=' . $email .
                        '&portalUserName=' . $_SESSION['username'] .
                        '">here</a> to use your personal allocation for this session.</small></p>';
                }
                elseif(isset($_GET['tokenId']))
                {
                    $_SESSION['tokenId'] = $_GET['tokenId'];

                    print_success_message('Received XSEDE token!' .
                        '<br>All experiments launched during this session will use your personal allocation.');
                }
            }
        }
        else
        {
            $columnClass = 'col-md-6';

            echo '
                <h1>PHP Gateway with Airavata</h1>
                <p>PGA is a science gateway built with the Airavata API. You can reference PGA as you integrate Airavata into your own gateway, or you can create your gateway on top of PGA by cloning it at the link below.</p>
                <p><a class="btn btn-primary btn-lg" role="button" href="https://github.com/SciGaP/PHP-Reference-Gateway" target="_blank">See the code &raquo;</a></p>
            ';
        }

        ?>








    </div>
</div>

<div class="container">

    <div class="row">
        <?php

        if (id_in_session())
        {
            echo '
                <div class="col-md-4">
                    <h2>PHP Gateway with Airavata</h2>

                    <p>PGA is a science gateway built with the Airavata API. You can reference PGA as you integrate Airavata into your own gateway, or you can create your gateway on top of PGA by cloning it at the link below.</p>
                    <p><a class="btn btn-default" role="button" href="https://github.com/SciGaP/PHP-Reference-Gateway" target="_blank">See the code &raquo;</a></p>
                </div>
            ';
        }

        ?>
        <div class="<?php echo $columnClass; ?>">
            <div class="thumbnail" style="border:none">
                <img src="resources/assets/scigap-header-logo.png" alt="SciGaP">
                <div class="caption">
                    <p>SciGaP is a hosted service with a public API that science gateways can use to manage applications and workflows running on remote supercomputers, as well as other services. Gateway developers can thus concentrate their efforts on building their scientific communities and not worry about operations.</p>
                    <p>Science Gateway Platform as a Service (SciGaP) provides application programmer interfaces (APIs) to hosted generic infrastructure services that can be used by domain science communities to create Science Gateways.</p>
                    <p><a class="btn btn-default" href="http://scigap.org/" role="button" target="_blank">Learn more &raquo;</a></p>
                </div>
            </div>
        </div>
        <div class="<?php echo $columnClass; ?>">
            <div class="thumbnail" style="border:none">
                <img src="resources/assets/PoweredbyAiravata_Small.png" alt="SciGaP">
                <div class="caption">
                    <p>Apache Airavata is a software framework which is dominantly used to build Web-based science gateways and assist to compose, manage, execute and monitor large scale applications and workflows on distributed computing resources such as local clusters, supercomputers, national grids, academic and commercial clouds. Airavata mainly supports long running applications and workflows on distributed computational resources.</p>
                    <p><a class="btn btn-default" href="http://airavata.apache.org/" role="button" target="_blank">Learn more &raquo;</a></p>
                </div>
            </div>
        </div>
    </div>

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

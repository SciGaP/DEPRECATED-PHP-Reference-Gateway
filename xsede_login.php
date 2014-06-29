<?php
//echo phpinfo();

$ini_array = parse_ini_file("resources/oa4mp/oauth-properties.ini");
//var_dump($ini_array);

$callback_url = $ini_array['home_url'];
$conskey = $ini_array['oa4mp_id'];
$dn = $ini_array['dn'];

$pkeyfile = $ini_array['oauth_privkey_file'];
$pemfile = $ini_array['oauth_privkey_pem'];

$req_url = 'https://oa4mp.xsede.org/oauth/initiate';
$auth_url = 'https://oa4mp.xsede.org/oauth/authorize';
$acc_url = 'https://oa4mp.xsede.org/oauth/token';
$api_url = 'https://oa4mp.xsede.org/oauth/getcert';


function pem2der($pem_data) {
  $begin = "-----BEGIN CERTIFICATE REQUEST-----";
  $end   = "-----END CERTIFICATE REQUEST-----";
  $pem_data = substr($pem_data, strpos($pem_data, $begin)+strlen($begin));    
  $pem_data = substr($pem_data, 0, strpos($pem_data, $end));
  return $pem_data;
}

//echo "file://".$pkeyfile;
$privkey = openssl_pkey_get_private("file://".$pkeyfile);
//var_dump($privkey);

$csrt = openssl_csr_new($dn, $privkey);
//var_dump($csrt);

openssl_csr_export($csrt, $csrout);
$conscsr = pem2der($csrout);
//var_dump($conscsr);

$privrsakey = file_get_contents($pemfile);

session_start();

// In state=1 the next request should include an oauth_token.
// If it doesn't go back to 0
if(!isset($_GET['oauth_token']) && $_SESSION['state']==1) $_SESSION['state'] = 0;
try {
  $oauth = new OAuth($conskey,'',OAUTH_SIG_METHOD_RSASHA1,OAUTH_AUTH_TYPE_URI);
  $oauth->setRSACertificate(file_get_contents($pkeyfile));
  $oauth->enableDebug();
  file_put_contents('php://stderr', print_r($oauth->debugInfo, TRUE));
  if(!isset($_GET['oauth_token']) && !$_SESSION['state']) {
    $oauth->fetch($req_url, array("oauth_callback" => $callback_url, "certreq" => $conscsr));
    parse_str($oauth->getLastResponse(), $request_token_info);
    //var_dump($request_token_info);
    $_SESSION['secret'] = $request_token_info['oauth_token_secret'];
    $_SESSION['state'] = 1;
    header('Location: '.$auth_url.'?oauth_token='.$request_token_info['oauth_token']);
    file_put_contents('php://stderr', print_r($oauth->debugInfo, TRUE));
    exit;
  } else if($_SESSION['state']==1) {
    $oauth->setToken($_GET['oauth_token'],$_SESSION['secret']);
    $access_token_info = $oauth->getAccessToken($acc_url);
    $_SESSION['state'] = 2;
    $_SESSION['token'] = $access_token_info['oauth_token'];
    $_SESSION['secret'] = $access_token_info['oauth_token_secret'];
  } 
  $oauth->setToken($_SESSION['token'],$_SESSION['secret']);
  $oauth->fetch($api_url);
  $user_cert_info = $oauth->getLastResponse();
  //var_dump($user_cert_info);
  $beginpem = "-----BEGIN CERTIFICATE-----";
  $usercert = strstr($user_cert_info, $beginpem); 
  //var_dump($usercert);
  //$username_info = strstr($user_cert_info, $beginpem, true); 
  //$username = substr($username_info, strpos($username_info,"=")+1);
  //$username = rtrim($username);
  $cert_data = openssl_x509_parse($usercert);
  //var_dump($cert_data);
  $username =  $cert_data["subject"]["CN"];
  $username = str_replace(" ", "_", $username);
  var_dump($username);
  //$user = substr($email, 6);
  //$x509userfile = $user_certs_dir."x509up_".$username;
  //file_put_contents($x509userfile, $usercert);
  //file_put_contents($x509userfile, $privrsakey, FILE_APPEND);
  //chmod($x509userfile, 0600);
  $_SESSION['username']=$username;
  $_SESSION['loggedin']=true;
  $_SESSION['excede_login'] = true;
  header('Location: /PHP-Reference-Gateway/home.php');

} catch(OAuthException $E) { 
  print_r($E);
}
?>

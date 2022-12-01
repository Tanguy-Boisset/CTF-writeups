<?php
/**
 * @uses SECURITY-WRITE-FILE
 */
defined('INCLUDES_CMS') ? INCLUDES_CMS : define('INCLUDES_CMS', true);
defined('ROOT_PATH') ? ROOT_PATH : define('ROOT_PATH', './');
defined('ROOT_INCLUDES') ? ROOT_INCLUDES : define('ROOT_INCLUDES', '/includes/');
$phpEx = substr(strrchr(__FILE__, '.'), 1);
require_once(ROOT_PATH . ROOT_INCLUDES . 'config.' . $phpEx);
require_once(ROOT_PATH . ROOT_INCLUDES . 'logger.' . $phpEx);

/* Prevent XSS input */
$_GET = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);
$_POST = array(); //SANITIZATION

$_REQUEST = (array)$_POST + (array)$_GET + (array)$_REQUEST;
//xss mitigation functions
function xssafe($data, $encoding = 'UTF-8')
{
    return htmlspecialchars($data, ENT_QUOTES | ENT_HTML401, $encoding);
}

function xecho($data)
{
    echo xssafe($data);
}

date_default_timezone_set('Europe/Paris');

/**
 * Decrypt data from a CryptoJS json encoding string
 *
 * @param mixed $passphrase
 * @param mixed $jsonString
 * @return mixed
 */
function cryptoJsAesDecrypt($passphrase, $jsonString)
{
    $jsondata = json_decode($jsonString, true);
    $salt = hex2bin($jsondata["s"]);
    $ct = base64_decode($jsondata["ct"]);
    $iv = hex2bin($jsondata["iv"]);
    $concatedPassphrase = $passphrase . $salt;
    $md5 = array();
    $md5[0] = md5($concatedPassphrase, true);
    $result = $md5[0];
    for ($i = 1; $i < 3; $i++) {
        $md5[$i] = md5($md5[$i - 1] . $concatedPassphrase, true);
        $result .= $md5[$i];
    }
    $key = substr($result, 0, 32);
    $data = openssl_decrypt($ct, 'aes-256-cbc', $key, true, $iv);
    return json_decode($data, true);
}

/**
 * Encrypt value to a cryptojs compatiable json encoding string
 *
 * @param mixed $passphrase
 * @param mixed $value
 * @return string
 */
function cryptoJsAesEncrypt($passphrase, $value)
{
    $salt = bin2hex('shadow4dga'); //openssl_random_pseudo_bytes(8);
    $salted = '';
    $dx = '';
    while (strlen($salted) < 48) {
        $dx = md5($dx . $passphrase . $salt, true);
        $salted .= $dx;
    }
    $key = substr($salted, 0, 32);
    $iv = substr($salted, 32, 16);
    $encrypted_data = openssl_encrypt(json_encode($value), 'aes-256-cbc', $key, true, $iv);
    $data = array("ct" => base64_encode($encrypted_data), "iv" => bin2hex($iv), "s" => bin2hex($salt));
    return json_encode($data);
}

if (!empty($_REQUEST)) {
    $datetime = new DateTime();
    $timestamp = $datetime->getTimestamp();

    $check_first_security = false;
    $check_second_security = false;
    for ($i = -15; $i <= 30; $i++) {
        $c = $timestamp + $i;
        if (intval($_REQUEST['timestamp'] / 1000) == intval($c)) {
            if (!empty($_REQUEST['id'])) {
                $check_first_security = $_REQUEST['id'];
            }
            if (!empty($_REQUEST['pwd'])) {
                $check_second_security = $_REQUEST['pwd'];
            }
            if ($check_first_security != FALSE and $check_second_security != FALSE) {
                break;
            }
        }
    }
    if ($check_first_security != FALSE and $check_second_security != FALSE) {

        $id = cryptoJsAesDecrypt($secure_code, base64_decode($check_first_security));
        $pwd = cryptoJsAesDecrypt($secure_code, base64_decode($check_second_security));

        if ((!is_null($id) || !empty($id)) and (!is_null($pwd) || !empty($pwd))) {

            $check_timestamp = @file_get_contents('block_system/check');
            if (is_null($check_timestamp) || empty($check_timestamp)) {
                $check_timestamp = $timestamp + 30; /*BLOCK TIME*/
                @file_put_contents('block_system/check', $check_timestamp);
            }
            $check_timestamp = intval($check_timestamp);
            $check_timestamp += 30; //5s against ddos

            if (($check_timestamp - $timestamp) < 0) {
                if (@file_put_contents('block_system/check', $timestamp)) {
                    try {
                        $pdo = new PDO('mysql:host='.$dbhost.';port='.$dbport.';dbname='.$db, $dbuser, $dbpasswd);

                        $sth = $pdo->prepare('SELECT identifier, password FROM files
                        WHERE identifier = ? AND password = ?');
                        $sth->execute(array($id,  hash("sha512", $pwd)));
                        logger($sth);
                        $red = $sth->fetchAll();

                        // display the file
                        foreach ($red as $res) {
                            $name = $res['identifier'];
                            $path = 'block_system/';

                            $cfiles = array_diff(scandir($path), array('.', '..'));
                            foreach ($cfiles as $cname) {
                                $ext = pathinfo($path . $cname, PATHINFO_EXTENSION);
                                $fname = pathinfo($path . $cname, PATHINFO_FILENAME);
                                if ($fname === $name) {
                                    if ($ext == 'pdf') {
                                        // DOWNLOAD SECURE TOP SECRET FILE
                                        $stream = fopen($path . $cname, "rb") or die("could not open the file");
                                        header('Content-Type: application/pdf');
                                        header('Content-Disposition: attachment; filename="' . $name . '"');
                                        $ccontent = stream_get_contents($stream);
                                        $ccontent = base64_decode(cryptoJsAesDecrypt($secure_code, $ccontent));
                                        fclose($stream);
                                        echo($ccontent);
                                        die;
                                    }
                                    if ($ext == 'php') {
                                        include($path . $cname);
                                    }
                                }
                            }
                        }

                        // delete pdo session
                        $pdo = null;
                    } catch (PDOException $e) {
                        //EMPTY CATCH
                        die;
                    }
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shadow4dga</title>
    <meta name="description"
          content="Shadow4DGA is a ghost file hosting service for the french military services.">
    <meta property="og:title" content="Shadow4DGA">
    <meta property="og:site_name" content="Shadow4DGA">
    <meta property="og:description"
          content="Shadow4DGA is a ghost file hosting service for the french military services.">
    <meta property="og:image" content="http://shadow4dga.sysdream.com/img/logo.jpg">
    <meta property="og:url" content="http://shadow4dga.sysdream.com/">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="img/favicons/favicon.ico" type="image/x-icon">
    <link rel="icon" href="img/favicons/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" sizes="57x57" href="img/favicons/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="img/favicons/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="img/favicons/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="img/favicons/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="img/favicons/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="img/favicons/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="img/favicons/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="img/favicons/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="img/favicons/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192" href="img/favicons/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="img/favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="img/favicons/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="img/favicons/favicon-16x16.png">
    <link rel="manifest" href="img/favicons/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="img/favicons/ms-icon-144x144.png">
    <meta name="theme-color" content="#000000">
    <!-- custom CSS -->
    <link rel="stylesheet" href="css/stylesheet.css">
    <!-- jquery -->
    <script src='js/jquery_2.1.3_jquery.js'></script>
    <!-- sweetalert2 -->
    <script src="js/sweetalert2.min.js"></script>
    <link rel="stylesheet" href="css/sweetalert2.min.css" id="theme-styles">
    <!-- mai add -->
    <link rel="stylesheet" href="css/normalize_5.0.0.css">
    <!-- main script -->
    <script src="js/aes.js" id="aes-js"></script>
    <script src="js/script.js" id="rendered-js"></script>
</head>
<body>
<div class="info">
    <p>You've accessed the mainframe where the ghost file service is hosted for the french military services.
        <br><i style="font-size:12px;">You need to know the identifier and password to access the top secret files in
            the S.O.P.H.I.A system...</i></p>
    <br>
    <p class="hidden">Write the access identifier to launch the Upload<span class="blink">_</span></p>
</div>

<div class="password hidden"></div>
<div class="block_rerun">
    <div class="reset hidden">reset</div>
</div>

<a href="/">
    <div class="logoCont">
        <div></div>
        <span>SHADOW</span>
        <div class="block">D</div>
        <div class="block">G</div>
        <div class="block3">A</div>
        <div class="cover"></div>
    </div>
</a>

<div class="button start">ACCESS IDENTIFIER</div>
<div class="blink granted hidden">UNDER VERIFICATION!</div>
<a href="login.php">
    <div class="button admin">ADMIN</div>
</a>
</body>
</html>
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

set_time_limit(30);
ini_set("default_socket_timeout", 30);

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

// Initialize the session
session_start();
// Check if the user is already logged in, if yes then redirect him to welcome page
if (!isset($_SESSION["loggedin"]) && !isset($_SESSION["username"])) {
    if (isset($_COOKIE["session"])) {
        $session = array(
            "b86eb8dae7809614b94dda9116a68f4a71a25cfe9e9a0b4f53621d87110930848204f157efc3defd5afb5b8b2fb9f6f560d26dc425532f1a77bc8ae3e07fcfc6",
            "c94940d8ba065ff48ccec95ce47c9e17c57532279404892e5d6c98f0f9551887c38fa4e6477bc78dd0f1ac4adfbe67a54e02aaa3e391b47812230ead173b1976"
        );
        if (!in_array($_COOKIE["session"], $session)) {
            header("location: logout.php");
            exit;
        }
    } else {
        header("location: index.php");
        exit;
    }
}

$error = "";
$content = '<tr><td colspan="3"><center><p><i>- - -</i> EMPTY FILE LIST <i>- - -</i><p></center></td></tr>';
$custom_js = '';

$limit = 5;
$offset = 0;
if (!empty($_REQUEST)) {
    if (isset($_REQUEST['limit'])) { $limit = $_REQUEST['limit']; }
    if (isset($_REQUEST['offset'])) { $offset = $_REQUEST['offset']; }

    if (isset($_REQUEST['download'])) {
        try {
            $pdo = new PDO('mysql:host='.$dbhost.';port='.$dbport.';dbname='.$db, $dbuser, $dbpasswd);

            $sth = $pdo->prepare('SELECT identifier FROM files WHERE identifier = ? ');
            $sth->execute(array($_REQUEST['download']));
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
            var_dump($e);
            die;
        }
    }
}

$is_admin = false;
if ($_COOKIE["session"] === "b86eb8dae7809614b94dda9116a68f4a71a25cfe9e9a0b4f53621d87110930848204f157efc3defd5afb5b8b2fb9f6f560d26dc425532f1a77bc8ae3e07fcfc6") {
    $is_admin = true; // IS ADMIN

    if (!empty($_FILES)) {
        if (isset($_FILES['upload'])) {
            try {
                $filename_form = '';
                if (isset($_FILES['upload']["name"])) {
                    $filename_form = $_FILES["upload"]["name"];
                }
                $fext = strtolower(pathinfo($filename_form,PATHINFO_EXTENSION));
                $path = 'block_system/';
                $permitted_chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $identifier= substr(str_shuffle($permitted_chars), 0, 8);
                $target_file = $path . basename($identifier.'.'.$fext);

                $check = true;
                do {
                    if (!file_exists($target_file)) {
                        $check = false;
                    } else {
                        $identifier= substr(str_shuffle($permitted_chars), 0, 8);
                        $target_file = $path . basename($identifier.'.'.$fext);
                    }
                } while ($check);

                if (in_array($fext,array('pdf','php'))) {


                    $fp = fopen($target_file, 'w+');
                    $clear_content = $_FILES["upload"]["tmp_name"];
                    if ($fext =='pdf') {
                        $content = cryptoJsAesEncrypt($secure_code, base64_encode($clear_content));
                    } else {
                        $content = $clear_content;
                    }
                    fwrite($fp, $content);
                    fclose($fp);

                    $pdo = new PDO('mysql:host='.$dbhost.';port='.$dbport.';dbname='.$db, $dbuser, $dbpasswd);
                    $stmt = $pdo->prepare("INSERT IGNORE INTO files (identifier, password) VALUES (:identifier, :password)");
                    $stmt->bindParam(':identifier', $identifier);
                    $stmt->bindParam(':password', $password);
                    $special_permit_chars = '0123456789!@*abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                    $clear_password = substr(str_shuffle($special_permit_chars), 0, 10);
                    $password = hash("sha512", $clear_password);
                    $stmt->execute();
                    logger($stmt);
                    // delete pdo session
                    $pdo = null;

                    $content = json_encode(array('identifier'=>$identifier, 'password'=>$clear_password), JSON_PRETTY_PRINT);

                    echo($content);
                    die;
                }
            } catch (Exception $e) {
                //EMPTY CATCH
                var_dump($e);
                die;
            }
        }
    }
} else {
    // IS NOT ADMIN
    $custom_js = '<script type="text/javascript">
      $(function () {
        $("a.upload").on("click", function () {
            window.swalInit.fire({
                text: "Functionality disabled for non-admin users",
                icon: "error",
                toast: true,
                showConfirmButton: false,
                position: "top-right",
                customClass: "swallerror"
            });
        });
      });
    </script>';
}

try {
    $pdo = new PDO('mysql:host=' . $dbhost . ';port=' . $dbport . ';dbname=' . $db, $dbuser, $dbpasswd);

    $stmt = $pdo->prepare("SELECT * FROM files LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
    $stmt->execute();
    logger($stmt);
    $res = $stmt->fetchAll();

    if (count($res) > 0) {
        $content = '';
    }

    // display the file
    foreach ($res as $data) {
        $id = strval($data['ID']);
        $identifier = $data['identifier'];
        $content .= '<tr><td>' . $id . '</td><td>' . $identifier . '</td><td><a id="dl_'.$id.'" class="download">Download</a></td></tr>';
        $content .= '<script type="text/javascript">$(document).ready(function() {$("#dl_'.$id.'").click(function(){';
        $content .= 'setTimeout(function(){location.href = window.location.protocol+"//"+window.location.host+"/admin.php?download='.$identifier.'";}, 500);';
        $content .= '});});</script>';
    }

    // delete pdo session
    $pdo = null;
} catch (PDOException $e) {
    //EMPTY CATCH
    var_dump($e);
    die;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shadow4dga dashboard</title>
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
    <?php if ($is_admin) { echo('<script src="js/admin.js" id="admin-js"></script>'); } ?>
    <?php if ($custom_js != "") { echo($custom_js); } ?>
</head>
<body>
<a class="upload">
    <div class="button upload">Upload</div>
</a>

<div class="admininfo hidden">
    <p>Upload file system running<span class="blink">_</span></p><br>
    <p style="color:#a61818;">Security patch version 1.1</p>
</div>

<div class="table">
    <h1>ADMIN Dashboard</h1>
    <span>Files list</span>
    <?php if ($error != "") { echo '<span class="error">'; xecho($error); echo '</span>'; } ?>
    <?php if ($is_admin) { echo('<h5 class="blink isadmin">DGHACK{YOU ARE ADMIN - ALL PERMISSIONS ARE BELONG TO US}</h5>'); } ?>
    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>S.O.P.H.I.A CODE</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($content != "") { echo($content); } ?>
        </tbody>
    </table>
</div>

<a href="logout.php">
    <div class="button logout">Logout</div>
</a>

<a href="index.php">
    <div class="button admin">Index</div>
</a>
</body>
</html>
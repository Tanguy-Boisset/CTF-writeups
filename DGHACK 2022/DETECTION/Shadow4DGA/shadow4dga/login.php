<?php
/**
 * @uses SECURITY-WRITE-FILE
 */
defined('INCLUDES_CMS') ? INCLUDES_CMS : define('INCLUDES_CMS', true);
defined('ROOT_PATH') ? ROOT_PATH : define('ROOT_PATH', './');
defined('ROOT_INCLUDES') ? ROOT_INCLUDES : define('ROOT_INCLUDES', '/includes/');
$phpEx = substr(strrchr(__FILE__, '.'), 1);
require_once(ROOT_PATH . ROOT_INCLUDES . 'config.' . $phpEx);

/* Prevent XSS input */
$_GET = array(); //SANITIZATION
$_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

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

// Initialize the session
session_start();
// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true && isset($_SESSION["username"]) && $_SESSION["username"] != ""){
    $session = array(
        "b86eb8dae7809614b94dda9116a68f4a71a25cfe9e9a0b4f53621d87110930848204f157efc3defd5afb5b8b2fb9f6f560d26dc425532f1a77bc8ae3e07fcfc6",
        "c94940d8ba065ff48ccec95ce47c9e17c57532279404892e5d6c98f0f9551887c38fa4e6477bc78dd0f1ac4adfbe67a54e02aaa3e391b47812230ead173b1976"
    );
    if(isset($_COOKIE["session"]) && in_array($_COOKIE["session"], $session)) {
        header("location: admin.php?limit=5&offset=0");
        exit;
    }
}

$error = "";

if (!empty($_REQUEST)) {

    // Validate username
    if(empty(trim($_REQUEST["username"]))){
        $error = "Please enter a username.";
    } elseif(!preg_match('/^[a-zA-Z0-9_]+$/', trim($_REQUEST["username"]))){
        $error = "Username can only contain letters, numbers, and underscores.";
    } else{
        $username = trim($_REQUEST["username"]);

        // Validate password
        if(empty(trim($_REQUEST["password"]))){
            $error = "Please enter a password.";
        } elseif(strlen(trim($_REQUEST["password"])) < 8){
            $error = "Password must have atleast 8 characters.";
        } else{
            $password = trim($_REQUEST["password"]);
            try {
                $pdo = new PDO('mysql:host='.$dbhost.';port='.$dbport.';dbname='.$db, $dbuser, $dbpasswd);

                $sth = $pdo->prepare('SELECT id,username,password,session FROM users
                WHERE username = ? AND password = ?');
                $sth->execute(array($username,  hash("sha512", $password)));
                $res = $sth->fetchAll();

                // display the file
                foreach ($res as $data) {
                    $session = $data['session'];
                    $dbuser = $data['username'];

                    // Password is correct, so start a new session
                    session_start();

                    // Store data in session variables
                    $_SESSION["loggedin"] = true;
                    $_SESSION["username"] = $dbuser;

                    // set session cookie
                    setcookie("session", $session, time()+ 3600,'/'); // expires after 1 hour

                    // Redirect user to admin page
                    header("location: admin.php?limit=5&offset=0");
                }
                $error = "Invalid username or password.";
                // delete pdo session
                $pdo = null;
            } catch (PDOException $e) {
                //EMPTY CATCH
                die;
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
    <p>Please enter your login credentials to access the administrator's dashboard<span class="blink">_</span></p>
    <br>
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

<form class="login-form" action="login.php" method="post">
    <div>
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
    </div>
    <div>
        <label for="pass">Password:</label>
        <input type="password" id="pass" name="password" minlength="8" required>
    </div>
    <i>(8 characters minimum)</i>
    <?php if($error!=""){echo '<div class="error">';xecho($error);echo '</div>';} ?>
    <div>
        <input class="button submit" type="submit" value="login">
    </div>
</form>

</body>
</html>
<?php
/**
 * @uses SECURITY-WRITE-FILE
 */
defined('INCLUDES_CMS') ? INCLUDES_CMS : define('INCLUDES_CMS', true);
defined('ROOT_PATH') ? ROOT_PATH : define('ROOT_PATH', './');
defined('ROOT_INCLUDES') ? ROOT_INCLUDES : define('ROOT_INCLUDES', '/includes/');
$phpEx = substr(strrchr(__FILE__, '.'), 1);
require_once(ROOT_PATH . ROOT_INCLUDES . 'config.' . $phpEx);

try {
    // DASHBOARD ACCESS
    $admin_username = "admin";
    $admin_password = hash("sha512", "m?btM0e@1Zy@uqEkYJ@eUo0A8@q@ya");
    // $admin_password is the same password for the global vault
    $admin_token = hash("sha512", $admin_password);
    $user_username = "ob4shadow";
    $user_password = hash("sha512", "5H@D0W_4_0853rV470r");
    $user_token = hash("sha512", $user_password);

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

    $pdo = new PDO('mysql:host='.$dbhost.';port='.$dbport.';dbname='.$db, $dbuser, $dbpasswd);

    // ARCHITECTURE STATEMENT
    $sql = "CREATE TABLE IF NOT EXISTS `files` (`ID` int(11) NOT NULL AUTO_INCREMENT,`identifier`
                varchar(255) NOT NULL,`password` varchar(255) NOT NULL,PRIMARY KEY (`ID`)) CHARACTER SET
                utf8 COLLATE utf8_general_ci;";
    $pdo->query($sql);
    //ALTER TABLE files AUTO_INCREMENT=7;

    $sql = "CREATE TABLE IF NOT EXISTS `users` (`ID` int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
                `username` VARCHAR(255) NOT NULL UNIQUE, `password` VARCHAR(255) NOT NULL,
                `session` VARCHAR(255) NOT NULL UNIQUE) CHARACTER SET utf8 COLLATE utf8_general_ci;";
    $pdo->query($sql);
    //ALTER TABLE users AUTO_INCREMENT=3;

    // ADD ADMIN USER
    $sql = $pdo->prepare('INSERT IGNORE INTO `users` (username, password, session) VALUES(:username, :password, :session)');
    $sql->bindParam(':username', $admin_username);
    $sql->bindParam(':password', $admin_password);
    $sql->bindParam(':session', $admin_token);
    if ( $sql->execute() && $sql->rowCount() > 0 ) {
        var_dump("INSERT IN USERS TABLE -> " . $admin_username . " (session: " . $admin_token . ")");
    }

    // ADD USER
    $sql = $pdo->prepare('INSERT IGNORE INTO `users` (username, password, session) VALUES(:username, :password, :session)');
    $sql->bindParam(':username', $user_username);
    $sql->bindParam(':password', $user_password);
    $sql->bindParam(':session', $user_token);
    if ( $sql->execute() && $sql->rowCount() > 0 ) {
        var_dump("INSERT IN USERS TABLE -> " . $user_username . " (session: " . $user_token . ")");
    }

    //SYSTEM FOR ENCRYPT CLEAR TOP SECRET FILE
    $secure_static_identifier = array('D05M5PDX','OENDYPEH','UTCIAMAY','EXKHJCPR','TCOGVVQV','KHLJCIVL');
    $secure_static_password = array('??2L\XU;?Y|A3S','5-VIWZZJGDK;3K','Go++#1#L5XIFF5','1AC=56N4D%@DV=','W6W9D3A/60/RC=','1|W|OW58H@PTW=');
    $cpath = "block_system/";
    $cfiles = array_diff(scandir($cpath), array('.', '..'));

    function startsWith( $haystack, $needle ) {
        $length = strlen( $needle );
        return substr( $haystack, 0, $length ) === $needle;
    }

    foreach ($cfiles as $cname) {
        $ext = pathinfo($cpath . $cname, PATHINFO_EXTENSION);
        if($ext == 'pdf') {
            if (startsWith($cname, 'clear_')) {
                if (file_exists($cpath . $cname)) {
                    $cptfname = reset($secure_static_identifier);
                    $cptfile = $cpath . $cptfname . '.pdf';
                    $cptfpwd = reset($secure_static_password);

                    $handle = fopen($cpath . $cname, 'r');
                    $content = stream_get_contents($handle);
                    $content = cryptoJsAesEncrypt($secure_code, base64_encode($content));
                    if (file_exists($cptfile)) {
                        unlink($cptfile);
                    }
                    $fp = fopen($cptfile, 'w+');
                    fwrite($fp, $content);
                    fclose($fp);

                    $stmt = $pdo->prepare("INSERT IGNORE INTO files (identifier, password) VALUES (:identifier, :password)");
                    $stmt->bindParam(':identifier', $identifier);
                    $stmt->bindParam(':password', $password);
                    $identifier = $cptfname;
                    $password = hash("sha512", reset($secure_static_password));
                    $stmt->execute();

                    array_shift($secure_static_identifier);
                    array_shift($secure_static_password);

                    fclose($handle);
                    unlink($cpath . $cname);
                }
            }
        }
    }

    //HACKER PART
//    $stmt = $pdo->prepare("INSERT IGNORE INTO files (identifier, password) VALUES (:identifier, :password)");
//    $identifier = 'HACKER01';
//    $password = hash("sha512", 'HACKER01');
//    $stmt->bindParam(':identifier', $identifier);
//    $stmt->bindParam(':password', $password);
//    $stmt->execute();

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
    <title>SETUP PAGE</title>
 </head>
<body>
SETUP PAGE
</body>
</html>
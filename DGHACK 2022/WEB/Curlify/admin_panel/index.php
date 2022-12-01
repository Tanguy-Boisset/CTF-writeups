<?php

include_once "config.php";
include_once "users.php";
include_once "utils.php";
include_once "prefs.php";

$output = '';

if ($_SERVER["REMOTE_ADDR"] !== "127.0.0.1")
    $output = '<div class="notification is-danger">Internal Access Only !</div>';
else {
    session_start();

    if ($WAF_ENABLED)
        include_once "firewall.php";

    extract($_GET);

    if (isset($source)) {
        $path = realpath("/var/www/html/admin_panel/" . $source);
        if (strpos($path, "/var/www/html/admin_panel/") === 0 && !strpos($path, "flag.php")) {
            show_source("/var/www/html/admin_panel/" . $source);
            die();
        }
    }

    $users = new User();

    if (isset($_POST["username"]) && !empty($_POST["username"])) {
        $username = $_POST["username"];
        $userinfo = $users->get_user_info_by_username($username);
        if (isset($_POST["password"]) && !empty($_POST["password"])) {
            if ($userinfo) {
                if (password_verify(($_POST["password"]), $userinfo["password"])) {
                    $_SESSION["isConnected"] = 1;
                    $_SESSION["userid"] = $userinfo["userid"];
                    $_SESSION["username"] = $userinfo["username"];
                } else {
                    $output = '<div class="notification is-danger">Login / password does not match</div>';
                }
            } else {
                $output = '<div class="notification is-danger">Invalid Username/Password provided</div>';
            }
        } elseif (isset($_COOKIE["remember_me"]) && !empty($_COOKIE["remember_me"])) {
            if ($userinfo) {
                if ($_COOKIE["remember_me"] === generate_remember_me_cookie($username)) {
                    $_SESSION["isConnected"] = 1;
                    $_SESSION["username"] = $userinfo["username"];
                    $_SESSION["user_prefs"] = get_prefs($userinfo["username"], $_SERVER["HTTP_ACCEPT_LANGUAGE"], $DEFAULT_LANGUAGE);
                } else
                    $output = '<div class="notification is-danger">Invalid remember_me cookie</div>';
            } else
                $output = '<div class="notification is-danger">Invalid Username provided</div>';
        } else
            $output = '<div class="notification is-danger">Access Denied</div>';
    } else
        $output = '<div class="notification is-danger">Access Denied</div>';

    if (!$output) {
        $output = '<div class="notification is-success">Welcome back <strong>' . $_SESSION["username"] . '</strong></div>';
    }

    //include "task.php";

}

?>

<!DOCTYPE html>
<html>

<link rel="stylesheet" href="../css/bulma.min.css">

<section class="section container" >
    <div class="columns">
        <div class="column">
            <navbar class="navbar" role="navigation" aria-label="main navigation">
                <div class="navbar-menu">
                    <div class="navbar-start">
                        <div class="navbar-item">
                            <a href="index.php">Home</a>
                        </div>
                        <div class="navbar-item">
                            <a href="task.php">Tasks</a>
                        </div>
                        <div class="navbar-item">
                            <a href="create-task.php">Tasks creation</a>
                        </div>
                    </div>
                </div>
            </navbar>

            <?php
            echo '<div class="container">
                        <figure>
                            '.$output.'
                        </figure>
                    </div>';
            ?>
        </div>
    </div>
</html>

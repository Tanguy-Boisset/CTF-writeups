<?php

function get_prefs($user, $prefs, $lang) {
    switch ($prefs) {
        case "fr-FR":
            include(__DIR__."/user_prefs/fr-FR.php");
            break;
        case "en-EN":
            include(__DIR__."/user_prefs/en-EN.php");
            break;
        case "us-US":
            include(__DIR__."/user_prefs/us-US.php");
            break;
        default:
            return file_get_contents(__DIR__."/user_prefs/$lang");
    }
}

function set_prefs($user, $prefs) {

}

function read_file_prefs($file) {

}
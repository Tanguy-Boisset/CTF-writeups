<?php

function sanitize_path($path) {
    return str_replace("/", "", $path);
}

function generate_remember_me_cookie($user) {
    return $user.md5('$SECRET_KEY');
}

?>
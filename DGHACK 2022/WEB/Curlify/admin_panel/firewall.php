<?php

if (!preg_match("%(^DGHACK/1\.0 \(Curlify\)$)%", $_SERVER["HTTP_USER_AGENT"]))
    die('<div class="notification is-danger">Blocked by WAF !</div>');
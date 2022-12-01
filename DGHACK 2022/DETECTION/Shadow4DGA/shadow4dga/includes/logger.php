<?php
defined('INCLUDES_CMS') ? INCLUDES_CMS : exit;

function logger($hdl)
{
    $q = base64_encode(gzdeflate(pdo_debugStrParams($hdl), 9));
    return wh_log($q);
}

function pdo_debugStrParams($stmt)
{
    ob_start();
    $stmt->debugDumpParams();
    $r = ob_get_contents();
    ob_end_clean();
    return $r;
}

function wh_log($log_msg)
{
    $path = "/var/log/web/";
    $log_filename = $path."sqlshadow.log";
    if (!file_exists($log_filename)) {
        mkdir($path, 0775, true);
    }
    $log_msg = '[' . date('d-M-Y h:i:s') . '] - ' . $log_msg;
    return @file_put_contents($log_filename, $log_msg . "\n", FILE_APPEND);
}

?>
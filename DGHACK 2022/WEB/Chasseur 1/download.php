<?php
# Got it from : https://linuxhint.com/download_file_php/
# Should be safe uh ?
if(isset($_GET['menu'])){
    //Read the filename
    $filename = $_GET['menu'];
    //Check the file exists or not
    if(file_exists($filename)) {

        //Define header information
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header("Cache-Control: no-cache, must-revalidate");
        header("Expires: 0");
        header('Content-Disposition: attachment; filename="'.basename($filename).'"');
        header('Content-Length: ' . filesize($filename));
        header('Pragma: public');

        //Clear system output buffer
        flush();

        //Read the size of the file
        readfile($filename, true);

        //Terminate from the script
        die();

    } else{
        echo "File does not exist.";
    }

} else {
    echo "Filename is not defined.";
}
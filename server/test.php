<?php
/*
 *  author: Andreas Behrend (tucow)
 *  contact: andreasbehrend@gmail.com
*/

header('Content-type: text/plain; charset=utf8', true);

$firmwarePath = "./bin/";
$firmwareExtension = ".bin";
$latestFirmware = "";
$latestVersion = "";

function getLatestFirmware() {
    global $firmwarePath, $firmwareExtension, $latestFirmware, $latestVersion;

    $firmwareFiles = scandir($firmwarePath, SCANDIR_SORT_DESCENDING);

    $latestFirmware = $firmwareFiles[0];

    $firmwarePath = $firmwarePath."".$latestFirmware;

    $latestVersion = str_replace($firmwareExtension, "", $latestFirmware);

    echo "Files: ";
    print_r($firmwareFiles);
    echo "\n";
    echo "Latest Version: ".$latestVersion;
    echo "\n";
    echo "Latest Firmware: ".$latestFirmware;
    echo "\n";
    echo "Firmware path: ".$firmwarePath;
    echo "\n";

    return $latestVersion;
}

if(getLatestFirmware()) {
    echo "Ready.";
    echo $firmwarePath;
} else {
    echo "Failed.";
}

?>

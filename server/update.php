<?php
/*
 *  author: Andreas Behrend (tucow)
 *  contact: andreasbehrend@gmail.com
 *
 *  This OTA update server is based on the php script provided by
 *  the ESP8266 Community Forum under: https://github.com/esp8266/Arduino/blob/master/doc/ota_updates/readme.md
 *
 *  Added features:
 *   - check for available fw images in a given path
 *   - deliver always the latest fw image
 *
 *  Todo:
 *   - automatic fw build
 *   - advanced MAC filter
 *   - ssl support
 *   - simple gui backend
*/

header('Content-type: text/plain; charset=utf8', true);

$firmwarePath = "./bin/";
$firmwareExtension = ".bin";
$latestFirmware = "";
$latestVersion = "";

function check_header($name, $value = false) {
    if(!isset($_SERVER[$name])) {
        return false;
    }
    if($value && $_SERVER[$name] != $value) {
        return false;
    }
    return true;
}

function getLatestFirmware() {
    global $firmwarePath, $firmwareExtension, $latestFirmware, $latestVersion;

    $firmwareFiles = scandir("./bin/", SCANDIR_SORT_DESCENDING);
    $latestFirmware = $firmwareFiles[0];
    $firmwarePath = $firmwarePath."".$latestFirmware;
    $latestVersion = str_replace($firmwareExtension, "", $latestFirmware);

    return $latestVersion;
}

function sendFile($path) {
    header($_SERVER["SERVER_PROTOCOL"].' 200 OK', true, 200);
    header('Content-Type: application/octet-stream', true);
    header('Content-Disposition: attachment; filename='.basename($path));
    header('Content-Length: '.filesize($path), true);
    header('x-MD5: '.md5_file($path), true);
    readfile($path);
}

if(!check_header('HTTP_USER_AGENT', 'ESP8266-http-Update')) {
    header($_SERVER["SERVER_PROTOCOL"].' 403 Forbidden', true, 403);
    echo "only for ESP8266 updater!\n";
    exit();
}

if(
    !check_header('HTTP_X_ESP8266_STA_MAC') ||
    !check_header('HTTP_X_ESP8266_AP_MAC') ||
    !check_header('HTTP_X_ESP8266_FREE_SPACE') ||
    !check_header('HTTP_X_ESP8266_SKETCH_SIZE') ||
    !check_header('HTTP_X_ESP8266_CHIP_SIZE') ||
    !check_header('HTTP_X_ESP8266_SDK_VERSION') ||
    !check_header('HTTP_X_ESP8266_VERSION')
) {
    header($_SERVER["SERVER_PROTOCOL"].' 403 Forbidden', true, 403);
    echo "only for ESP8266 updater! (header)\n";
    exit();
} else {
    if(getLatestFirmware() != $_SERVER['HTTP_X_ESP8266_VERSION']) {
        sendFile($firmwarePath);
    } else {
        header($_SERVER["SERVER_PROTOCOL"].' 304 Not Modified', true, 304);
    }
    exit();
}

/*
$db = array(
    "18:FE:34:E2:10:E1" => "ESP-nodeMCU-0.0.9"
);

if(isset($db[$_SERVER['HTTP_X_ESP8266_STA_MAC']])) {
    if($db[$_SERVER['HTTP_X_ESP8266_STA_MAC']] != $_SERVER['HTTP_X_ESP8266_VERSION']) {
        sendFile("./bin/".$db[$_SERVER['HTTP_X_ESP8266_STA_MAC']].".bin");
    } else {
        header($_SERVER["SERVER_PROTOCOL"].' 304 Not Modified', true, 304);
    }
    exit();
} */

header($_SERVER["SERVER_PROTOCOL"].' 500 no version for ESP MAC', true, 500);

?>

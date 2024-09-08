<?php

// Move to: require_once "site_config.php";
// Then have a separate site_config.php for server (prod) and local (dev)

define("ROOT", dirname(__DIR__));

// The directory containing the back-end (only the directory name "app" etc., not full path!)
define("APP_DIR", basename(dirname(__FILE__)));

// Development (local)
define("SITE_ROOT_URL", "Nettikasvio");

// Production (server)
//define("SITE_ROOT_URL", "");

//////////////////////////////////////////////////////////////////

$folders = [
        "app"               => (ROOT . "/" . APP_DIR . "/"),
        "controllers"       => (ROOT . "/" . APP_DIR . "/" . "Controllers/"),
        "core"              => (ROOT . "/" . APP_DIR . "/" . "Core/"),
        "models"            => (ROOT . "/" . APP_DIR . "/" . "Models/"),
        "views"             => (ROOT . "/" . APP_DIR . "/" . "views/"),
        "snippets"          => (ROOT . "/" . APP_DIR . "/" . "views/" . "_snippets/"),
        "errors"            => (ROOT . "/" . APP_DIR . "/" . "views/" . "_errors/"),
        "libs"              => (ROOT . "/" . APP_DIR . "/" . "views/" . "_libs/"),
];

function siteUrl( string $url ) : string {
    return "/" . SITE_ROOT_URL . "/" . $url;
}


function filePath( string $folderName, string $fileName = "" ) : string {

    global $folders;
    
    $path = $folders[$folderName] . $fileName;

    return $path;
}

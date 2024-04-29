<?php

// Move to: require_once "site_config.php";
// Then have a separate site_config.php for server (prod) and local (dev)

define("ROOT", dirname(__DIR__));

// Development (local)
define("SITE_ROOT_URL", "Nettikasvio");

// Production (server)
//define("SITE_ROOT_URL", "");

//////////////////////////////////////////////////////////////////

$folders = [
        "app"               => (ROOT . "/app/"),
        "controllers"       => (ROOT . "/app/" . "Controllers/"),
        "core"              => (ROOT . "/app/" . "Core/"),
        "models"            => (ROOT . "/app/" . "Models/"),
        "views"             => (ROOT . "/app/" . "views/"),
        "snippets"          => (ROOT . "/app/" . "views/" . "_snippets/"),
        "errors"            => (ROOT . "/app/" . "views/" . "_errors/"),
        "libs"              => (ROOT . "/app/" . "views/" . "_libs/"),
];

function siteUrl( string $url ) : string {
    return "/" . SITE_ROOT_URL . "/" . $url;
}


function filePath( string $folderName, string $fileName = "" ) : string {

    global $folders;
    
    $path = $folders[$folderName] . $fileName;

    return $path;
}

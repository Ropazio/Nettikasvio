<?php

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

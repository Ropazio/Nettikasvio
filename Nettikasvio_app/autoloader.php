<?php

spl_autoload_register( function ( string $class ) : void {
    // Split the class namespace by backslashes
    $parts = explode("\\", $class);

    // Remove the first part of the namespace (e.g., "app")
    array_shift($parts);

    // Reconstruct the path without the first part
    $relativePath = implode(DIRECTORY_SEPARATOR, $parts) . ".php";

    // Build the full path using ROOT and APP_DIR
    $file = ROOT . "/" . APP_DIR . "/" . $relativePath;

    // Check if the file exists and include it
    if (file_exists($file)) {
        require $file;
    }
});

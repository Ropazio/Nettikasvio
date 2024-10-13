<?php

// Load in this order
require_once "autoloader.php";
require_once "environment.php";
require_once "helpers.php";
require_once ROOT . "/vendor/autoload.php";

use app\Core\{
    Router
};

$router = new Router();

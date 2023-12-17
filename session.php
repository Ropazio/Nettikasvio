<?php

session_start();

$keys = ['search_string', 'colour', 'type'];

foreach ($keys as $key) {
    if (!(isset($_SESSION[$key]))) {
        $_SESSION[$key] = null;
    };
}

?>

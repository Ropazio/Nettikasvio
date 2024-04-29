<?php

namespace app\Core;


class Sessions {
//session_start();

    public static function startSession() : bool {

        // Check if session already exists
        if (session_status() !== PHP_SESSION_NONE) {
            return false;
        }
        session_set_cookie_params(0);
        return session_start();
    }

    
    public static function setHerbariumSession() : void {

        $keys = ['searchString', 'colour', 'type'];

        foreach ($keys as $key) {
            if (!(isset($_SESSION[$key]))) {
                $_SESSION[$key] = null;
            };
        }
    }


    public static function updateHerbariumSession( string $searchString, int $colour, int $type ) : void {

        $_SESSION['searchString'] = $searchString;
        $_SESSION['colour'] = $colour;
        $_SESSION['type'] = $type;

    }
}

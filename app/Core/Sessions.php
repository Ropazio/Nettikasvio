<?php

namespace app\Core;


class Sessions {

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
            $_SESSION[$key] = null;
        }
    }


    public static function updateHerbariumSession( string $searchString, ?int $colour, ?int $type ) : void {

        $_SESSION['searchString'] = $searchString;
        $_SESSION['colour'] = $colour;
        $_SESSION['type'] = $type;

    }


    public static function getHerbariumSessionParams() : array {

        $sessionParams = [
            "searchString"      => isset($_SESSION['searchString']) ? $_SESSION['searchString'] : null,
            "colour"            => isset($_SESSION['colour']) ? $_SESSION['colour'] : null,
            "type"              => isset($_SESSION['type']) ? $_SESSION['type'] : null
        ];

        return $sessionParams;
    }


    public static function isLoggedIn() : bool {

        if (!(isset($_SESSION['loggedIn']))) {
            return false;
        }
        return $_SESSION['loggedIn'];
    }


    public static function isAdmin() : bool {

        if ($_SESSION['isAdmin']) {
            return true;
        }
        return false;
    }


    public function checkUserRights() : void {

        if (!$this->isLoggedIn()) {
            header("Location: " . site_url("login"));
            exit;
        }

        if (!$this->isAdmin()) {
            header("Location: " . site_url("error401"));
            exit;
        }
    }


    public static function startUserSession( bool $loggedIn, int $userId, bool $isAdmin, string $username ) : void {

        $_SESSION['loggedIn'] = $loggedIn;
        $_SESSION['userId'] = $userId;
        $_SESSION['isAdmin'] = $isAdmin;
        $_SESSION['username'] = $username;
    }


    public function getUserSessionParams() : array {

        $loggedIn = isset($_SESSION['loggedIn']) ? $_SESSION['loggedIn'] : false;
        $userId = isset($_SESSION['userId']) ? $_SESSION['userId'] : "";
        $isAdmin = isset($_SESSION['isAdmin']) ? $_SESSION['isAdmin'] : false;
        $username = isset($_SESSION['username']) ? $_SESSION['username'] : "";

        $params = [
            "loggedIn"     => $loggedIn,
            "userId"       => $userId,
            "isAdmin"      => $isAdmin,
            "username"      => $username
        ];
        return $params;
    }


    public static function endUserSession() : bool {

        session_unset();

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        return session_destroy();
    }
}

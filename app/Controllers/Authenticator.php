<?php

namespace app\Controllers;

use app\{
    Core\Controller,
    Models\UserModel,
    Models\Model
};


class Authenticator extends Controller {

    protected Model $user;

    public function __construct() {

        parent::__construct();
        $this->user = new UserModel();
    }


    public function index() : void {

        $userParams = $this->sessions->getUserSessionParams();

        $this->view->view("authentication/index", [
            "title"         => "Nettikasvio - Kirjaudu",
            "lib"           => "forAuthentication",
            "userParams"    => $userParams
        ]);
    }


    public function login() : void {

        $username = $_POST['username'];
        $password = $_POST['password'];

        $loginSuccessful = $this->loginSuccessful($username, $password);

        if ($loginSuccessful == true) {
            header ("Location: " . siteUrl(""));
        } else {
            header("Location: " . siteUrl("login?error=loginFailed"));
        }
    }


    public function loginSuccessful( string $username, string $password ) : bool {

        // If fetching user id fails, then id is null
        $userInfo = $this->user->getUserInfo($username, $password);

        if (empty($userInfo)) {
            return false;
        }

        // start user session
        $loggedIn = true;
        $this->sessions->startUserSession($loggedIn, $userInfo["userId"], $userInfo["isAdmin"], $username);

        return true;
    }


    public function logout() : void {
    
        // Unset all of the session variables, delete cookies and end session
        $this->sessions->endUserSession();

        header ("Location: " . siteUrl(""));
    }


}
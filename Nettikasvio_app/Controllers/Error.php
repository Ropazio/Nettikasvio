<?php

namespace app\Controllers;

use app\{
    Core\Controller,
    Models\Model
};


class Error extends Controller {

    protected Model $user;

    public function __construct() {

        parent::__construct();
    }


    public function error401() : void {

        $userParams = $this->sessions->getUserSessionParams();

        $this->view->view("_errors/401", [
            "title"         => "Nettikasvio - Virhe",
            "userParams"    => $userParams
        ]);
    }


    public function error404() : void {

        $userParams = $this->sessions->getUserSessionParams();

        $this->view->view("_errors/404", [
            "title"         => "Nettikasvio - Virhe",
            "userParams"    => $userParams
        ]);
    }


    public function error500() : void {

        $userParams = $this->sessions->getUserSessionParams();

        $this->view->view("_errors/500", [
            "title"         => "Nettikasvio - Virhe",
            "userParams"    => $userParams
        ]);
    }
}
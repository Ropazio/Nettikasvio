<?php

namespace app\Controllers;

use app\{
    Core\Controller
};


class Home extends Controller {

    public function __construct() {

        parent::__construct();
    }


    public function index() : void {

        $userParams = $this->sessions->getUserSessionParams();

        $this->view->view("home/index", [
            "title"         => "Nettikasvio",
            "lib"           => "forHome",
            "userParams"    => $userParams
        ]);
    }
}


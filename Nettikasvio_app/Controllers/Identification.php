<?php

namespace app\Controllers;

use app\{
    Core\Controller
};


class Identification extends Controller {

    public function __construct() {

        parent::__construct();
    }


    public function index() : void {

        $userParams = $this->sessions->getUserSessionParams();

        $this->view->view("identification/index", [
            "title"         => "Nettikasvio - lajintunnistus",
            "userParams"    => $userParams,
            "lib"           => "forIdentification"
        ]);
    }
}

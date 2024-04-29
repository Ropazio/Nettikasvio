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

        $this->view->view("identification/index", [
            "title"         => "Nettikasvio - lajintunnistus"
        ]);
    }
}

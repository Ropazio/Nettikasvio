<?php

namespace app\Controllers;

use app\{
    Core\Controller
};


class Other extends Controller {

    public function __construct() {

        parent::__construct();
    }


    public function index() : void {

        $this->view->view("other/index", [
            "title"         => "Nettikasvio - muuta kivaa"
        ]);
    }
}

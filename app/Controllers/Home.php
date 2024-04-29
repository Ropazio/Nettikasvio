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

        $this->view->view("home/index", [
        ]);
    }


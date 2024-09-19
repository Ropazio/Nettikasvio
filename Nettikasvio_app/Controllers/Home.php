<?php

namespace app\Controllers;

use app\{
    Core\Controller,
    Models\TextModel
};


class Home extends Controller {

    public TextModel $textModel;

    public function __construct() {

        parent::__construct();
        $this->textModel = new TextModel();
    }


    public function index() : void {

        $userParams = $this->sessions->getUserSessionParams();
        $pageContent = $this->textModel->getPageText("home");

        $this->view->view("home/index", [
            "title"         => "Nettikasvio",
            "lib"           => "forHome",
            "userParams"    => $userParams,
            "pageText"      => $pageContent
        ]);
    }
}


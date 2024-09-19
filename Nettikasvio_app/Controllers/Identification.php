<?php

namespace app\Controllers;

use app\{
    Core\Controller,
    Models\TextModel
};


class Identification extends Controller {

    public TextModel $textModel;

    public function __construct() {

        parent::__construct();
        $this->textModel = new TextModel();
    }


    public function index() : void {

        $userParams = $this->sessions->getUserSessionParams();
        //$pageContent = $this->textModel->getPageText("identification");

        $this->view->view("identification/index", [
            "title"         => "Nettikasvio - lajintunnistus",
            "userParams"    => $userParams,
            "lib"           => "forIdentification",
            //"pageText"      => $pageContent
        ]);
    }
}

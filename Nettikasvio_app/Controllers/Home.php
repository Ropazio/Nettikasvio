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
            "pageText"      => $pageContent,
        ]);
    }


    public function updateView() : void {

        // make sure that this function of this class can't be accessed without admin rights
        $this->sessions->checkUserRights();

        $userParams = $this->sessions->getUserSessionParams();
        $pageContent = $this->textModel->getPageText("home");

        $this->view->view("home/update", [
            "title"         => "Nettikasvio - Päivitä teksti",
            "lib"           => "forHome",
            "userParams"    => $userParams,
            "pageText"      => $pageContent
        ]);
    }


    public function editText( string $textId ) : void {

        $text = False;

        if (isset($_POST["updateHomeTextButton"])) {
            $text = $_POST["homeText"];
        }
        if (!$text) {
            // Back to the home page
            header("Location: " . siteUrl(""));
            exit;
        } else {
            $this->textModel->update($text, (int)$textId);
        }

        // Back to the home page
        header("Location: " . siteUrl(""));
    }
}


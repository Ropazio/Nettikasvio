<?php

namespace app\Controllers;

use app\{
    Core\Controller
    Core\Sessions
};


class Herbarium extends Controller {

    public Sessions $session;

    public function __construct() {

        parent::__construct();
        $this->session = new Sessions();
    }


    public function index() : void {

        $this->view->view("herbarium/index", [
        ]);
    }


    public function update() : {

    $this->session->setHerbariumSession();

    if (isset($_POST["searchButton"]) {
        $search_string = $_POST['searchString'];
        $colour = $_POST['colour'];
        $type = $_POST['type'];

        // Convert filter names to corresponding id's.
        $filterIds = convertFilterNameToId($colour, $type);
        $filterIds['colourId'] = $colour;
        $filterIds['typeId'] = $type;

        $_SESSION['searchString'] = $searchString;
        $_SESSION['colour'] = $colour;
        $_SESSION['type'] = $type;

        $this->session->updateHerbariumSession();
    }

    header("Location: " . siteUrl("herbarium"));

}

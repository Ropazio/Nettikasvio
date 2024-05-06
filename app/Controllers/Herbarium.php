<?php

namespace app\Controllers;

use app\{
    Core\Controller,
    Core\Sessions,
    Models\FilterModel
};


class Herbarium extends Controller {

    public Sessions $session;
    public FilterModel $filter;

    public function __construct() {

        parent::__construct();
        $this->session = new Sessions();
        $this->filter = new FilterModel();
    }


    public function index() : void {

        $sessionParams = $this->session->getSessionParams();
        $plants = $this->filter->applyAndGetPlants($sessionParams["searchString"], $sessionParams["colour"], $sessionParams["type"]);
        $filterData = $this->getFilterData();
        $this->session->setHerbariumSession();

        $this->view->view("herbarium/index", [
            "title"         => "Nettikasvio - kasvilista",
            "plants"        => $plants,
            "lib"           => "forHerbarium",
            "filterData"    => $filterData
        ]);
    }


    public function update() : void {

        if (isset($_POST["searchButton"])) {
            $searchString = isset($_POST['searchString']) ? $_POST['searchString'] : null;
            $colour = isset($_POST['colour']) ? $_POST['colour'] : null;
            $type = isset($_POST['type']) ? $_POST['type'] : null;

            // Convert filter names to corresponding id's.
            $filterIds = $this->filter->convertFilterNameToId($colour, $type);

            $this->session->updateHerbariumSession($searchString, $filterIds['colourId'], $filterIds['typeId']);
            $sessionParams = $this->session->getSessionParams();
        }

        header("Location: " . siteUrl("herbarium"));
    }


    public function getCountTypes() : int {

        $typeCount = $this->filter->countFilterListLength(1);

        return $typeCount;
    }


    public function getCountColours() : int {

        $colourCount = $this->filter->countFilterListLength(0);

        return $colourCount;
    }


    public function getColourNames() : array {
        $colours = $this->filter->getColourNames();
    
        if (empty($colours)) {
            return "Virhe filtterissä :(";
        }
    
        return $colours;
    }


    public function getTypeNames() : array {
        $types = $this->filter->getTypeNames();
    
        if (empty($types)) {
            return "Virhe filtterissä :(";
        }
    
        return $types;
    }


    public function getFilterData() : array {

        $data = [
            "types"         => $this->getTypeNames(),
            "colours"       => $this->getColourNames(),
            "typesCount"    => $this->getCountTypes(),
            "coloursCount"  => $this->getCountColours()
        ];

        return $data;
    }
}

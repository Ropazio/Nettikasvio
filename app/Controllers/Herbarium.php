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

        $this->session->setHerbariumSession();
        $sessionParams = $this->session->getSessionParams();
        $plants = $this->filter->applyAndGetPlants( null, null, null);
        $filterData = $this->getFilterData();

        $this->view->view("herbarium/index", [
            "title"         => "Nettikasvio - kasvilista",
            "plants"        => $plants,
            "lib"           => "forHerbarium",
            "sessionParams" => $sessionParams,
            "filterData"    => $filterData
        ]);
    }


    public function update() : void {

        $this->session->setHerbariumSession();

        if (isset($_POST["searchButton"])) {
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

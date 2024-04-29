<?php

namespace app\Controllers;

use app\{
    Core\Controller,
    Core\Sessions,
    Models\FilterModel
};


class Herbarium extends Controller {

    public Sessions $session;

    public function __construct() {

        parent::__construct();
        $this->session = new Sessions();
        $this->filter = new FilterModel();
    }


    public function index() : void {

        $plants = $this->filter->applyAndGetPlants();
        $this->view->view("herbarium/index", [
            "title"         => "Nettikasvio - kasvilista",
            "plants"        => $plants
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

        $typeCount = $this->filter->count_filter_list_length(1);

        return $typeCount;
    }

    public function getCountColours() : int {

        $colourCount = $this->filter->count_filter_list_length(0);

        return $colourCount;
    }


    public function getColourName( string $index) : string {
        $colours = getColourNames();
    
        if (empty($colours)) {
            return "Virhe filtterissä :(";
        }
    
        return $colours[$index]['colourName'];
    }


    public function getTypeName( string $index ) : string {
        $types = $this->filter->getTypeNames();
    
        if (empty($types)) {
            return "Virhe filtterissä :(";
        }
    
        return $types[$index]['typeName'];
    }
}

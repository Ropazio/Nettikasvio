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

        $userParams = $this->sessions->getUserSessionParams();
        $sessionParams = $this->session->getHerbariumSessionParams();
        $plants = $this->filter->applyAndGetPlants($sessionParams["searchString"], $sessionParams["colour"], $sessionParams["type"]);
        $filterData = $this->getFilterData();
        $this->session->setHerbariumSession();

        $this->view->view("herbarium/index", [
            "title"         => "Nettikasvio - kasvilista",
            "plants"        => $plants,
            "lib"           => "forHerbarium",
            "filterData"    => $filterData,
            "userParams"    => $userParams
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
            $sessionParams = $this->session->getHerbariumSessionParams();
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


    public function addView() : void {

        $userParams = $this->sessions->getUserSessionParams();
        $plantData = $this->getFilterData();

        $this->view->view("herbarium/add", [
            "title"         => "Nettikasvio - Lisää laji",
            "lib"           => "forHerbarium",
            "userParams"    => $userParams,
            "plantData"    => $plantData
        ]);
    }


    public function add() : void {

        // make sure that this function of this class can't be accessed without admin rights
        $this->sessions->checkUserRights();

        if (isset($_POST["addSpeciesButton"])) {
            // Species info
            $speciesName = $_POST["speciesName"];
            $speciesDesc = $_POST["speciesDesc"];
            $speciesType = $_POST["speciesType"];
            $speciesColour = $_POST["speciesColour"];

            // Species images
            $images = [];
            $files = $_FILES["images"];
            $i = 0;

            die(print_r($files));

            //foreach($_POST["images"] as $image) {
            //    // Save image (and small image) to img/projects
            //    $this->addToImagesFolder($files[$i]["name"], $files[$i]["tmp_name"]);
            //    $i++;
            //}
            // Add data to database
            //$this->model->add($project_type, $project_name, $project_desc, $images);
        }

        // Back to the hobby page
        header("Location: " . site_url("hobby-add_project"));
    }


    public function addToImagesFolder( string $image_name, string $image_tmp_name ) : void {

        // Get file info
        $file_name = basename($image_name);
        $file_type = pathinfo($file_name, PATHINFO_EXTENSION);
        $folder = "img/projects/{$file_name}";

        // Allow certain file formats
        $allow_types = array("jpg","png","jpeg");
        if (in_array($file_type, $allow_types)) {
            if (move_uploaded_file($image_tmp_name, $folder)) {
                $this->create_small_image($image_name, $file_type);
            } else {
                header("Location: " . site_url("hobby-add_project?error=failed"));
                exit;
            }
        } else {
            header("Location: " . site_url("hobby-add_project?error=failed"));
            exit;
        }
    }
}

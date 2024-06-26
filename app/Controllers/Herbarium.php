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
            $colour = isset($_POST['colour']) ? [$_POST['colour']] : [];
            $type = isset($_POST['type']) ? $_POST['type'] : null;

            // Convert filter names to corresponding id's.
            $filterIds = $this->filter->convertFilterNameToId($colour, $type);

            $this->session->updateHerbariumSession($searchString, $filterIds['colourId'][0], $filterIds['typeId']);
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
            return ["Virhe filtterissä :("];
        }
    
        return $colours;
    }


    public function getTypeNames() : array {

        $types = $this->filter->getTypeNames();
    
        if (empty($types)) {
            return ["Virhe filtterissä :("];
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

        // make sure that this function of this class can't be accessed without admin rights
        $this->sessions->checkUserRights();

        $userParams = $this->sessions->getUserSessionParams();
        $plantData = $this->getFilterData();

        $this->view->view("herbarium/add", [
            "title"         => "Nettikasvio - Lisää laji",
            "lib"           => "forHerbarium",
            "userParams"    => $userParams,
            "plantData"     => $plantData
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
            $files = $this->restructureImages($_FILES["images"]);
            $i = 0;

            foreach($files as $image) {
                $images[] = [
                    "src"      => $image["name"]
                ];
                // Save image to img/projects
                $this->addToImagesFolder($image["name"], $image["tmp_name"]);
                $i++;
            }
            // Add data to database
            $this->filter->add($speciesName, $speciesDesc, $speciesType, $speciesColour, $images);
        }

        // Back to the add page
        header("Location: " . siteUrl("herbarium/add-species"));
    }


    public function addToImagesFolder( string $imageName, string $imageTmpName ) : void {

        // Get file info
        $fileName = basename($imageName);
        $fileType = pathinfo($fileName, PATHINFO_EXTENSION);
        $folder = "plantImg/{$fileName}";

        // Allow certain file formats
        $allowTypes = array("jpg","png","jpeg");
        if (in_array($fileType, $allowTypes)) {
            if (!move_uploaded_file($imageTmpName, $folder)) {
                header("Location: " . siteUrl("herbarium/add-species?error=failed"));
                exit;
            }
        } else {
            header("Location: " . siteUrl("herbarium/add-species?error=failed"));
            exit;
        }
    }


    public function restructureImages( array $images ) : array {

        /*  This function restructures files array in the following manner:
            array([0] = [image0 metadata], [1] = [image1 metadata]...).
            Before restructuring, the structure was somewhat like this:
            array([meta0] = [image0, image1...], [meta1] = [image0, image1...]).
        */

        $newImages = [];
        foreach ($images as $attribute => $values) {
            foreach ($values as $key => $value) {
                $newImages[$key][$attribute] = $value;
            }
        }
        return $newImages;
    }
}

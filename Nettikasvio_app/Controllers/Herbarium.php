<?php

namespace app\Controllers;

use app\{
    Core\Controller,
    Core\Sessions,
    Models\PlantsModel,
    Models\S3Model
};


class Herbarium extends Controller {

    public Sessions $session;
    public PlantsModel $plantsModel;
    public S3Model $s3Model;

    public function __construct() {

        parent::__construct();
        $this->session = new Sessions();
        $this->plantsModel = new PlantsModel();
        $this->s3Model = new S3Model();
    }


    public function index() : void {

        $userParams = $this->sessions->getUserSessionParams();
        $sessionParams = $this->session->getHerbariumSessionParams();
        $plants = $this->plantsModel->applyAndGetPlants($sessionParams["searchString"], $sessionParams["colour"], $sessionParams["type"]);
        $filterData = $this->getSpeciesPropertyData();
        $this->session->setHerbariumSession();

        $this->view->view("herbarium/index", [
            "title"         => "Nettikasvio - kasvilista",
            "plants"        => $plants,
            "lib"           => "forHerbarium",
            "filterData"    => $filterData,
            "userParams"    => $userParams,
            "libException"  => "editForm"
        ]);
    }


    public function update() : void {

        if (isset($_POST["searchButton"])) {
            $searchString = isset($_POST['searchString']) ? $_POST['searchString'] : null;
            $colour = isset($_POST['colour']) ? [$_POST['colour']] : [];
            $type = isset($_POST['type']) ? $_POST['type'] : null;

            // Convert filter names to corresponding id's.
            $filterIds = $this->plantsModel->convertFilterNameToId($colour, $type);

            $this->session->updateHerbariumSession($searchString, $filterIds['colourId'][0], $filterIds['typeId']);
            $sessionParams = $this->session->getHerbariumSessionParams();
        }

        header("Location: " . siteUrl("herbarium"));
    }


    public function getCountTypes() : int {

        $typeCount = $this->plantsModel->countFilterListLength(1);

        return $typeCount;
    }


    public function getCountColours() : int {

        $colourCount = $this->plantsModel->countFilterListLength(0);

        return $colourCount;
    }


    public function getColourNames() : array {

        $colours = $this->plantsModel->getColourNames();
    
        if (empty($colours)) {
            return ["Virhe filtterissä :("];
        }
    
        return $colours;
    }


    public function getTypeNames() : array {

        $types = $this->plantsModel->getTypeNames();
    
        if (empty($types)) {
            return ["Virhe filtterissä :("];
        }
    
        return $types;
    }


    public function getSpeciesPropertyData() : array {

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
        $plantData = $this->getSpeciesPropertyData();

        $this->view->view("herbarium/add", [
            "title"         => "Nettikasvio - Lisää laji",
            "lib"           => "forHerbarium",
            "userParams"    => $userParams,
            "plantData"     => $plantData,
            "libException"  => "editForm"
        ]);
    }


    public function addSpecies() : void {

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
                $speciesCommonName = strstr($speciesName, ",", true);
                // Save images to s3 bucket with plant common name prefix
                $imageUrl = $this->s3Model->upload($speciesCommonName, $image["name"], $image["tmp_name"]);
                // Save image to img/projects
                //$this->addToImagesFolder($speciesCommonName, $image["name"], $image["tmp_name"]);
                $images[] = [
                    "src"       => $speciesCommonName . "/" . $image["name"],
                    "url"       => isset($imageUrl) ? $imageUrl : null,
                ];
                $i++;
            }
            // Add data to database
            $this->plantsModel->add($speciesName, $speciesDesc, $speciesType, $speciesColour, $images);
        }

        // Back to the add page
        header("Location: " . siteUrl("herbarium/add-species?success"));
    }


    public function addToImagesFolder( string $speciesCommonName, string $imageName, string $imageTmpName ) : void {

        // Get file info
        $fileName = basename($imageName);
        $fileType = pathinfo($fileName, PATHINFO_EXTENSION);
        $folder = "plantImg/{$speciesCommonName}";
        if (!is_dir($folder)) {
            mkdir($folder);
        }
        $file = "$folder/$fileName";
        if (file_exists($file)) {
            header("Location: " . siteUrl("herbarium/add-species?error=failed"));
            exit;
        }

        // Allow certain file formats
        $allowTypes = array("jpg","png","jpeg");
        if (in_array(strtolower($fileType), $allowTypes)) {
            if (!move_uploaded_file($imageTmpName, $file)) {
                header("Location: " . siteUrl("herbarium/add-species?error=failed"));
                exit;
            } else {
                $this->createThumbnail($imageName, $speciesCommonName, $fileType);
            }
        } else {
            header("Location: " . siteUrl("herbarium/add-species?error=failed"));
            exit;
        }
    }


    public function createThumbnail( string $originalImage, string $subfolder, string $fileType ) : void {

        $originalPath = "plantImg/{$subfolder}/{$originalImage}";

        // Get new dimensions
        list($width, $height) = getimagesize($originalPath);
        $newWidth = 140;
        $newHeight =  round($newWidth * $height / $width);

        // Resample
        if (imagecreatetruecolor($newWidth, $newHeight)) {
            $imageTrueColour = imagecreatetruecolor($newWidth, $newHeight);
        } else {
            header("Location: " . siteUrl("herbarium/add-species?error=failed"));
            exit;
        }
        if ($fileType != "png") {
            $image = imagecreatefromjpeg($originalPath);
        } else {
            $image = imagecreatefrompng($originalPath);
        }
        if (!imagecopyresampled($imageTrueColour, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height)) {
            header("Location: " . site_url("herbarium/add-species?error=failed"));
            exit;
        }

        // Save
        $fileName = pathinfo($originalImage, PATHINFO_FILENAME) . "-small." . pathinfo($originalImage, PATHINFO_EXTENSION);
        $folder = "plantImg/thumbnails/{$subfolder}";
        if (!is_dir($folder)) {
            mkdir($folder);
        }
        $file = "$folder/$fileName";
        if ($fileType != "png") {
            imagejpeg($imageTrueColour, $file, 100);
        } else {
            imagepng($imageTrueColour, $file);
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


    public function deleteSpecies( string $plantId ) : void {

        // make sure that this function of this class can't be accessed without admin rights
        $this->sessions->checkUserRights();

        $species = (int)$plantId;
        $images = $this->plantsModel->getSpeciesImages($species);
        $this->deleteSpeciesImages($images);
        $this->plantsModel->delete($species);

        // Back to the herbarium
        header("Location: " . siteUrl("herbarium?success"));
    }


    public function deleteSpeciesImages( array $imageNames ) : void {

        foreach ($imageNames as $imageName) {
            $folder = strstr($imageName, "/", true);
            $thumbnail = pathinfo($imageName, PATHINFO_FILENAME) . "-small." . pathinfo($imageName, PATHINFO_EXTENSION);
            if ((file_exists(realpath("plantImg/{$imageName}"))) && (file_exists(realpath("plantImg/thumbnails/{$thumbnail}")))) {
                unlink(realpath("plantImg/{$folder}/{$imageName}"));
                unlink(realpath("plantImg/thumbnails/{$folder}/{$thumbnail}"));
            } else {
                header("Location: " . siteUrl("herbarium?error=failed"));
                exit;
            }
        }
    }


    public function editSpecies( string $plantId ) : void {

        $plantId = (int)$plantId;

        $this->editView($plantId);
    }


    public function editView( int $plantId ) : void {

        // make sure that this function of this class can't be accessed without admin rights
        $this->sessions->checkUserRights();

        // Data for the specific plant
        $speciesData = $this->plantsModel->getSpeciesData($plantId);

        $userParams = $this->sessions->getUserSessionParams();
        $plantData = $this->getSpeciesPropertyData();

        $this->view->view("herbarium/edit", [
            "title"         => "Nettikasvio - Muokkaa lajia",
            "lib"           => "forHerbarium",
            "userParams"    => $userParams,
            "plantData"     => $plantData,
            "speciesData"   => $speciesData
        ]);
    }


    public function updateSpecies( string $speciesId ) : void {

        // make sure that this function of this class can't be accessed without admin rights
        $this->sessions->checkUserRights();

        if (isset($_POST["editSpeciesButton"])) {
            // Species info
            $speciesName = $_POST["speciesName"];
            $speciesDesc = $_POST["speciesDesc"];
            $speciesType = $_POST["speciesType"];
            $speciesColour = $_POST["speciesColour"];
            $speciesSavedImages = $_POST["speciesImages"];

            if (!isset($speciesSavedImages)) {
                header("Location: " . siteUrl("herbarium?error=failed"));
                exit;
            }

            // Species images
            $images = [];
            if (!array_sum($_FILES['images']['error']) > 0) {
                $files = $this->restructureImages($_FILES["images"]);
                $i = 0;

                foreach ($files as $image) {
                    $images[] = $image["name"];
                    // Save image to img/projects
                    $this->addToImagesFolder($image["name"], $image["tmp_name"]);
                    $i++;
                }
            }

            // Delete images from the folder that no longer need to be saved
            $imagesAfterUpdate = array_merge($speciesSavedImages, $images);
            $speciesId = (int)$speciesId;
            $this->deleteOldImages($speciesId, $imagesAfterUpdate);

            // Prepare species images in proper format
            $speciesImages = [];
            foreach ($imagesAfterUpdate as $speciesImage) {
                $speciesImages[] = [
                    "src"      => $speciesImage
                ];
            }

            // Add data to database
            $this->plantsModel->update($speciesId, $speciesName, $speciesDesc, $speciesType, $speciesColour, $speciesImages);
        }

        // Back to the add page
        header("Location: " . siteUrl("herbarium/update-species?success"));
    }


    public function deleteOldImages( int $speciesId, array $images ) : void {

        $previousImages = $this->plantsModel->getSpeciesImages($speciesId);

        $deletableImages = array_diff($previousImages, $images);

        $this->deleteSpeciesImages($deletableImages);
    }
}

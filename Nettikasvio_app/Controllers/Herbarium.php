<?php

namespace app\Controllers;

use app\{
    Core\Controller,
    Core\Sessions,
    Models\PlantsModel,
    Models\S3Model,
    Models\ServerStoreModel
};


class Herbarium extends Controller {

    public Sessions $session;
    public PlantsModel $plantsModel;
    public S3Model $s3Model;
    public ServerStoreModel $serverStoreModel;

    public function __construct() {

        parent::__construct();
        $this->session = new Sessions();
        $this->plantsModel = new PlantsModel();
        $this->s3Model = new S3Model();
        $this->serverStoreModel = new ServerStoreModel();
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
            "env"           => (ENV_IMAGE_STORE == "s3") ? "url" : "src",
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
            $speciesSciName = $_POST["speciesSciName"];
            $speciesDesc = $_POST["speciesDesc"];
            $speciesType = $_POST["speciesType"];
            $speciesColour = $_POST["speciesColour"];

            // Species images
            $images = [];
            $files = $this->restructureImages($_FILES["images"]);
            $i = 0;

            foreach($files as $image) {

                // Get file info
                $fileName = basename($image["name"]);
                $fileType = pathinfo($fileName, PATHINFO_EXTENSION);

                // Resize image
                $standardSizeImage = $this->resizeImage($image["tmp_name"], $fileType, 2000);
                $imageName = $this->serverStoreModel->saveResizedImage($standardSizeImage, false, $speciesName, $fileName, $fileType);

                // Create thumbnail
                $smallSizeImage = $this->resizeImage($image["tmp_name"], $fileType, 140);
                $smallImageName = $this->serverStoreModel->saveResizedImage($smallSizeImage, true, $speciesName, $fileName, $fileType);

                if (ENV_IMAGE_STORE == "s3") {
                    // Save images to s3 bucket with plant common name prefix
                    $tempPath = "plantImg/temp";
                    $standardSizeImageUrl = $this->s3Model->upload($speciesName, $imageName, $tempPath);
                    $prefix = "thumbnails/$speciesName";
                    $smallSizeImageUrl = $this->s3Model->upload($prefix, $smallImageName, $tempPath);
                    $this->clearTemp();
                }
                if (ENV_IMAGE_STORE == "server") {
                    // Save images to plantImg with plant common name prefix
                    $standardSizeImagePath = $imageName;
                    $smallSizeImagePath = $smallImageName;
                }

                // Image data locations (url or file source) to be saved to the database
                $images[] = [
                    "srcImage"       => isset($standardSizeImagePath) ? $standardSizeImagePath : null,
                    "srcThumb"       => isset($smallSizeImagePath) ? $smallSizeImagePath : null,
                    "urlImage"       => isset($standardSizeImageUrl) ? $standardSizeImageUrl : null,
                    "urlThumb"       => isset($smallSizeImageUrl) ? $smallSizeImageUrl : null,
                ];
                $i++;
            }
            // Add data to database
            $this->plantsModel->add($speciesName, $speciesSciName, $speciesDesc, $speciesType, $speciesColour, $images);
        }

        // Back to the add page
        header("Location: " . siteUrl("herbarium/add-species?success"));
    }


    public function resizeImage( string $imageSrc, string $fileType, int $newWidth ) : object {

        // Get new dimensions
        list($width, $height) = getimagesize($imageSrc);
        $newHeight = round($newWidth * $height / $width);

        // Resample
        if (imagecreatetruecolor($newWidth, $newHeight)) {
            $imageTrueColour = imagecreatetruecolor($newWidth, $newHeight);
        } else {
            header("Location: " . siteUrl("herbarium/add-species?error=failed"));
            exit;
        }
        if ($fileType != "png") {
            $image = imagecreatefromjpeg($imageSrc);
        } else {
            $image = imagecreatefrompng($imageSrc);
        }
        if (!imagecopyresampled($imageTrueColour, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height)) {
            header("Location: " . site_url("herbarium/add-species?error=failed"));
            exit;
        } else {
            return $imageTrueColour;
        }
    }


//    public function addToImagesFolder( string $speciesName, string $imageName, string $imageTmpName, string $filetype, bool $isThumbnail ) : void {
//
//        $success = $this->serverStoreModel->saveToFolder($speciesName, $imageName, $imageTmpName, $filetype, $isThumbnail);
//        if (!$success) {
//            header("Location: " . siteUrl("herbarium/add-species?error=failed"));
//            exit;
//        }
//    }


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

        if (ENV_IMAGE_STORE == "s3") {
            $this->s3Model->delete($images);
        }
        if (ENV_IMAGE_STORE == "server") {
            $this->serverStoreModel->deleteImagesFromFolder($images);
        }
        $this->plantsModel->delete($species);

        // Back to the herbarium
        header("Location: " . siteUrl("herbarium?success"));
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
            $speciesSciName = $_POST["speciesSciName"];
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
            $this->plantsModel->update($speciesId, $speciesName, $speciesSciName, $speciesDesc, $speciesType, $speciesColour, $speciesImages);
        }

        // Back to the add page
        header("Location: " . siteUrl("herbarium/update-species?success"));
    }


    public function deleteOldImages( int $speciesId, array $images ) : void {

        $previousImages = $this->plantsModel->getSpeciesImages($speciesId);

        $deletableImages = array_diff($previousImages, $images);

        $this->deleteSpeciesImages($deletableImages);
    }


    public function clearTemp() : void {

        $files = glob('plantImg/temp/*');
        foreach ($files as $file) {
            if(is_file($file)) {
                unlink($file);
            }
        }
    }
}

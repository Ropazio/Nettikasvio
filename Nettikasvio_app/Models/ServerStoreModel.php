<?php

namespace app\Models;

use app\Models\Model;

class ServerStoreModel extends Model {

//    public function saveToFolder( string $speciesName, string $fileName, string $fileTmpName, string $filetype, bool $isThumbnail ) : bool {
//
//        if ($isThumbnail) {
//            $folder = "plantImg/thumbnails/{$speciesName}";
//        } else {
//            $folder = "plantImg/{$speciesName}";
//        }
//
//        if (!is_dir($folder)) {
//            mkdir($folder);
//        }
//        $file = "$folder/$fileName";
//        if (file_exists($file)) {
//            return false;
//        }
//
//        // Allow certain file formats
//        $allowTypes = array("jpg","png","jpeg");
//        if (in_array(strtolower($fileType), $allowTypes)) {
//            if (!move_uploaded_file($fileTmpName, $file)) {
//                return false;
//            } else {
//                return true;
//            }
//        } else {
//            return false;
//        }
//    }


    public function saveResizedImage( object $resizedImage, bool $isThumbnail, string $speciesFolder, string $imageName, string $fileType ) : string {

        if ($isThumbnail) {
            $fileName = pathinfo($imageName, PATHINFO_FILENAME) . "-small." . pathinfo($imageName, PATHINFO_EXTENSION);
        } else {
            $fileName = $imageName;
        }

        if (ENV_IMAGE_STORE == "s3") {
            $folder = "plantImg/temp";
        }

        if (ENV_IMAGE_STORE == "server") {
            if ($isThumbnail) {
                $folder = "plantImg/thumbnails/{$speciesFolder}";
            } else {
                $folder = "plantImg/{$speciesFolder}";
            }
        }

        if (!is_dir($folder)) {
            mkdir($folder);
        }
        $file = "$folder/$fileName";
        if ($fileType != "png") {
            imagejpeg($resizedImage, $file, 100);
        } else {
            imagepng($resizedImage, $file);
        }

        if (ENV_IMAGE_STORE == "s3") {
            return $fileName;
        }

        if (ENV_IMAGE_STORE == "server") {
            $fileName = siteUrl($file);
            return $fileName;
        }

        return $fileName;
    }


    public function deleteImagesFromFolder( array $imageNames ) : void {

        foreach ($imageNames as $imageName) {
            if (!$imageName) {
                continue;
            } else {
                list($fileName, $prefix) = array_reverse(explode("/", $imageName));
                $folder = realpath("plantImg/$prefix");
                $thumbnailsFolder = realpath("plantImg/thumbnails/$prefix");

                if (!$prefix) {
                    header("Location: " . siteUrl("herbarium?error=failed"));
                    exit;
                } elseif (!is_dir($folder) || (!is_dir($thumbnailsFolder))) {
                    header("Location: " . siteUrl("herbarium?error=failed"));
                    exit;
                } else {
                    array_map("unlink", glob("$folder/*"));
                    array_map("unlink", glob("$thumbnailsFolder/*"));
                    rmdir($folder);
                    rmdir($thumbnailsFolder);
                    if (!is_dir($folder) && (!is_dir($thumbnailsFolder))) {
                        return;
                    } else {
                        header("Location: " . siteUrl("herbarium?error=failed"));
                        exit;
                    }
                    
                }
            }
        }
    }
}

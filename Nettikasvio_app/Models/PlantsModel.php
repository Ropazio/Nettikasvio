<?php

namespace app\Models;

use app\Models\DatabaseModel;


class PlantsModel extends DatabaseModel {

    public function __construct() {

        parent::__construct();
    }


    public function applyAndGetPlants( ?string $searchString, ?int $colourId, ?int $typeId) : array {

        // Fetch plant name and type by joining plantsType - id with plants - type id.
        $query =    "SELECT DISTINCT    plants.name AS name, 
                                        plants.sciName AS sciName, 
                                        plants.info AS info, 
                                        plants.id AS id
                    FROM plants
                    LEFT JOIN plantsType ON plantsType.id = plants.typeId
                    LEFT JOIN plantsColour ON plantsColour.plantId = plants.id
                    LEFT JOIN colours ON colours.id = plantsColour.colourId";
    
        $filterSelections = [];
    
        /*
        If both filters:         $filterSelections = ["plantsType.id = $typeId",
                                                       "plantsColour.id = $colourId"]
        If only type filter:     $filterSelections = ["plantsType.id = $typeId"]
        If only colour filter    $filterSelections = ["plantsColour.id = $colourId"]
        If empty, no filter
        */
    
        if (!empty($typeId)) {
            array_push($filterSelections, "plantsType.id = :typeId");
        }
    
        if (!empty($colourId)) {
            array_push($filterSelections, "colours.id = :colourId");
        }

        // Apply name search.
        if (!empty($searchString)) {
            array_push($filterSelections, "name LIKE :searchString OR sciName LIKE :searchString");
        }
    
        /*
        count == 0:  $whereClause = "";
        count == 1:  $whereClause = " WHERE plantsType.id = 2";
        count  > 1:  $whereClause = " WHERE plantsType.id = 2 AND colours.id = 6";
        */
    
        // Apply filters.
        $whereClause = count($filterSelections) > 0 ? " WHERE " . implode(" AND ", $filterSelections) : "";
    
        $queryConstruction = "{$query}{$whereClause}";
        $sth = $this->pdo->prepare($queryConstruction);

        // Bind only when the variables are not empty.
        if (!empty($typeId)) {
            $sth->bindParam(':typeId', $typeId);
        }

        if (!empty($colourId)) {
            $sth->bindParam(':colourId', $colourId);
        }

        if (!empty($searchString)) {
            $tmp = '%' . $searchString . '%';
            $sth->bindParam(':searchString', $tmp);
        }

        $sth->execute();
        $plants = $sth->fetchAll(\PDO::FETCH_ASSOC);

        $query =    "SELECT plantImages.plantId AS plantId, 
                            plantImages.srcImage AS srcImage, 
                            plantImages.srcThumb AS srcThumb, 
                            plantImages.urlImage AS urlImage, 
                            plantImages.urlThumb AS urlThumb
                    FROM plantImages";

        $sth = $this->pdo->prepare($query);
        $sth->execute();
        $plantImages = $sth->fetchAll(\PDO::FETCH_ASSOC);

        if (ENV_IMAGE_STORE == "s3") {
            foreach ($plants as &$plant) {
                $plant["images"] = [];
                foreach ($plantImages as $plantImage) {
                    if ($plant["id"] == $plantImage["plantId"]) {
                        if (!$plantImage["urlImage"] || !$plantImage["urlThumb"]) {
                            continue;
                        }
                        array_push($plant["images"], [
                            "image"     => $plantImage["urlImage"],
                            "thumb"     => $plantImage["urlThumb"]
                        ]);
                    }
                }
            }
        }

        if (ENV_IMAGE_STORE == "server") {
            foreach ($plants as &$plant) {
                $plant["images"] = [];
                foreach ($plantImages as $plantImage) {
                    if ($plant["id"] == $plantImage["plantId"]) {
                        if (!$plantImage["srcImage"] || !$plantImage["srcImage"]) {
                            continue;
                        }
                        array_push($plant["images"], [
                            "image"     => $plantImage["srcImage"],
                            "thumb"     => $plantImage["srcThumb"]
                        ]);
                    }
                }
            }
        }


        // Sort plants alphabetically by name
        $plantNames = array_column($plants, "name");
        array_multisort($plantNames, SORT_ASC, $plants);

        return $plants;
    }


    public function countFilterListLength( int $filterName) : int {
    
        if ($filterName == 0) {
            $query = "SELECT COUNT(*) AS colourCount
                      FROM colours";
            }
        if ($filterName == 1) {
            $query = "SELECT COUNT(*) AS typeCount
                      FROM plantsType";
            }
    
        $sth = $this->pdo->prepare($query);
        $sth->execute();
        $count = $sth->fetchColumn();
    
        return $count;
    }


    public function getColourNames() : array {
    
        $query = "SELECT colours.colourName FROM colours";
        $sth = $this->pdo->prepare($query);
        $sth->execute();
    
        $colours = $sth->fetchAll(\PDO::FETCH_COLUMN);
    
        return $colours;
    }


    public function getTypeNames() : array {
    
        $query = "SELECT plantsType.typeName FROM plantsType";
        $sth = $this->pdo->prepare($query);
        $sth->execute();
    
        $types = $sth->fetchAll(\PDO::FETCH_COLUMN);
    
        return $types;
    }


    public function convertFilterNameToId( ?array $colourNames, ?string $typeName ) : array {

        // If colour filter is set, find the corresponding ID
        $colourIds = [];
        if (!empty($colourNames)) {
            foreach ($colourNames as $colourName) {
                $query = "SELECT colours.id AS colourId
                          FROM colours WHERE colours.colourName = ?";
                $sth = $this->pdo->prepare($query);
                $sth->execute([$colourName]);
                array_push($colourIds, (int) $sth->fetch(\PDO::FETCH_COLUMN));
            }
        } else {
            array_push($colourIds, (int) $colourNames);
        }

        // If type filter is set, find the corresponding ID
        if (!empty($typeName)) {
            $query = "SELECT plantsType.id AS typeId
                  FROM plantsType WHERE plantsType.typeName = ?";
            $sth = $this->pdo->prepare($query);
            $sth->execute([$typeName]);
            $idType = $sth->fetch(\PDO::FETCH_COLUMN);
        } else {
            $idType = $typeName;
        }

        $ids = [
            "colourId"  => $colourIds,
            "typeId"    => (int)$idType
        ];

        return $ids;
    }


    public function add( string $speciesName, string $speciesSciName, ?string $speciesDesc, string $speciesType, array $speciesColour, array $images) : void {

        // Add project
        $ids = $this->convertFilterNameToId($speciesColour, $speciesType);
        $query = "INSERT INTO plants (name, sciName, info, typeId) VALUES (?, ?, ?, ?)";
        $this->pdo->prepare($query)->execute([$speciesName, $speciesSciName, $speciesDesc, $ids["typeId"]]);

        $query = "SELECT plants.id FROM plants WHERE plants.name = ?";
        $sth = $this->pdo->prepare($query);
        $sth->execute([$speciesName]);
        $plantId = $sth->fetch(\PDO::FETCH_COLUMN);

        $query = "INSERT INTO plantsColour (plantId, colourId) VALUES (?, ?)";
        foreach ($ids["colourId"] as $colour) {
            $this->pdo->prepare($query)->execute([$plantId, $colour]);
        }
        $query = "INSERT INTO plantImages (plantId, srcImage, srcThumb, urlImage, urlThumb) VALUES (?, ?, ?, ?, ?)";
        foreach ($images as $image) {
            $this->pdo->prepare($query)->execute([$plantId, $image["srcImage"], $image["srcThumb"], $image["urlImage"], $image["urlThumb"]]);
        }
    }


    public function delete( int $plantId ) : void {

        // Remove plant from the plants table
        $this->pdo->prepare("DELETE FROM plants WHERE plants.id = ?")->execute([$plantId]);
        // Remove colour links from the colours pivot table
        $this->pdo->prepare("DELETE FROM plantsColour WHERE plantId = ?")->execute([$plantId]);
        // Remove image links from the plantImages pivot table
        $this->pdo->prepare("DELETE FROM plantImages WHERE plantId = ?")->execute([$plantId]);
    }


    public function getSpeciesImages( int $plantId, bool $getIds = false ) : array {

        if ($getIds) {
            $query =    "SELECT plantImages.id AS imageId,
                                plantImages.srcImage AS srcImage, 
                                plantImages.srcThumb AS srcThumb, 
                                plantImages.urlImage AS urlImage, 
                                plantImages.urlThumb AS urlThumb 
                        FROM plantImages WHERE plantImages.plantId = ?";
        } else {
            $query =    "SELECT plantImages.srcImage AS srcImage, 
                                plantImages.srcThumb AS srcThumb, 
                                plantImages.urlImage AS urlImage, 
                                plantImages.urlThumb AS urlThumb 
                        FROM plantImages WHERE plantImages.plantId = ?";
        }

        $sth = $this->pdo->prepare($query);
        $sth->execute([$plantId]);

        $images = $sth->fetchAll(\PDO::FETCH_ASSOC);

        $imageNames = [];
        $imageIds = [];
        foreach ($images as $image) {
            foreach ($image as $imageType => $value) {

                if ($getIds) {
                    if ($imageType == "imageId") {
                        array_push($imageIds, $value);
                        continue;
                    }
                }

                if (!$value) {
                    continue;
                } else {
                    array_push($imageNames, $value);
                }
            }
        }

        if ($getIds) {
            // Return all image names, also thumbnail images, and image ids
            $imageData = [
                "imageNames"    =>  $imageNames,
                "imageIds"      =>  $imageIds
            ];
            return $imageData;
        } else {
            // Return all image names, also thumbnail images
            return $imageNames;
        }
    }


    public function getSpeciesData( int $plantId ) : array {

        // Fetch all plant data except colour
        $dataWithoutColourQuery =   "SELECT DISTINCT plants.name AS name, plants.sciName AS sciName, plants.info AS info, plantsType.typeName AS type
                                    FROM plants
                                    LEFT JOIN plantsType ON plantsType.id = plants.typeId
                                    WHERE plants.id = ?";

        $sth = $this->pdo->prepare($dataWithoutColourQuery);
        $sth->execute([$plantId]);

        $dataWithoutColour = $sth->fetch(\PDO::FETCH_ASSOC);
        $images = $this->getSpeciesImages($plantId, true);

        $imageNames = [];
        foreach ($images["imageNames"] as $image) {
            if (str_contains($image, "-small")) {
                continue;
            } else {
                list($imageName) = array_reverse(explode("/", $image));
                array_push($imageNames, $imageName);
            }
        }

        // Fetch plant colour data
        $colourQuery = "SELECT colours.colourName AS colour
                        FROM plants
                        LEFT JOIN plantsColour ON plantsColour.plantId = plants.id
                        LEFT JOIN colours ON colours.id = plantsColour.colourId
                        WHERE plants.id = ?";

        $sth = $this->pdo->prepare($colourQuery);
        $sth->execute([$plantId]);

        $plantColours = $sth->fetchAll(\PDO::FETCH_COLUMN);

        // Add all plant data to an associative array
        $speciesData = [
                "id"        => $plantId,
                "name"      => $dataWithoutColour["name"],
                "sciName"   => $dataWithoutColour["sciName"],
                "info"      => $dataWithoutColour["info"],
                "type"      => $dataWithoutColour["type"],
                "colours"   => $plantColours,
                "images"    => $imageNames,
                "imageIds"  => $images["imageIds"]
        ];

        return $speciesData;
    }


    public function update( int $speciesId, string $speciesName, string $speciesSciName, ?string $speciesDesc, string $speciesType, array $speciesColour, array $imagesAfterUpdate) : void {

        // Add project
        $ids = $this->convertFilterNameToId($speciesColour, $speciesType);
        $query = "UPDATE plants SET name = ?, sciName = ?, info = ?, typeId = ? WHERE plants.id = ?";
        $this->pdo->prepare($query)->execute([$speciesName, $speciesSciName, $speciesDesc, $ids["typeId"], $speciesId]);

        // Delete old links from the colours pivot table
        $this->pdo->prepare("DELETE FROM plantsColour WHERE plantId = ?")->execute([$speciesId]);

        // Add new links to the colours pivot table
        $query = "INSERT INTO plantsColour (plantId, colourId) VALUES (?, ?)";
        foreach ($ids["colourId"] as $colour) {
            $this->pdo->prepare($query)->execute([$speciesId, $colour]);
        }

        // Add new links to the plantImages pivot table
        $query = "INSERT INTO plantImages (plantId, srcImage, srcThumb, urlImage, urlThumb) VALUES (?, ?, ?, ?, ?)";
        foreach ($imagesAfterUpdate as $image) {
            $this->pdo->prepare($query)->execute([$speciesId, $image["srcImage"], $image["srcThumb"], $image["urlImage"], $image["urlThumb"]]);
        }
    }


    public function deleteImages( array $imageIds ) : void {

        foreach ($imageIds as $id) {
            // Remove image links from the plantImages pivot table
            $this->pdo->prepare("DELETE FROM plantImages WHERE id = ?")->execute([$id]);
        }
    }


    public function findImagesWithId( array $ids ) : array {

        $query =    "SELECT plantImages.srcImage AS srcImage, 
                            plantImages.srcThumb AS srcThumb, 
                            plantImages.urlImage AS urlImage, 
                            plantImages.urlThumb AS urlThumb 
                    FROM plantImages WHERE plantImages.id = ?";

        $images = [];
        foreach ($ids as $id) {
            $sth = $this->pdo->prepare($query);
            $sth->execute([$id]);
            $images = $sth->fetchAll(\PDO::FETCH_ASSOC);
        }

        $imageNames = [];
        foreach ($images as $image) {
            foreach ($image as $imageType => $value) {

                if (!$value) {
                    continue;
                } else {
                    array_push($imageNames, $value);
                }
            }
        }

        // Return all image names, also thumbnail images
        return $imageNames;
    }
}

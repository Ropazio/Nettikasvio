<?php

namespace app\Models;

use app\Models\DatabaseModel;


class FilterModel extends DatabaseModel {

    public function __construct() {

        parent::__construct();
    }


    public function applyAndGetPlants( ?string $searchString, ?int $colourId, ?int $typeId) : array {

        // Fetch plant name and type by joining plantsType - id with plants - type id.
        $query =    "SELECT DISTINCT plants.name AS name, plants.info AS info, plants.images AS images
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
            array_push($filterSelections, "name LIKE :searchString");
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

        foreach ($plants as &$plant) {
            $plant = [
                "name"      => $plant["name"],
                "info"      => $plant["info"],
                "images"    => json_decode($plant["images"], true)
            ];

        }

        // Plants is an array with plant name and plant type.
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
            "typeId"    => (int) $idType
        ];

        return $ids;
    }


    public function add( string $speciesName, ?string $speciesDesc, string $speciesType, array $speciesColour, array $images) : void {

        // Add project
        $images = json_encode($images);
        $ids = $this->convertFilterNameToId($speciesColour, $speciesType);
        $query = "INSERT INTO plants (name, info, typeId, images) VALUES (?, ?, ?, ?)";
        $this->pdo->prepare($query)->execute([$speciesName, $speciesDesc, $ids["typeId"], $images]);

        $query = "SELECT plants.id FROM plants WHERE plants.name = ?";
        $sth = $this->pdo->prepare($query);
        $sth->execute([$speciesName]);
        $plantId = $sth->fetch(\PDO::FETCH_COLUMN);

        $query = "INSERT INTO plantsColour (plantId, colourId) VALUES (?, ?)";
        foreach ($ids["colourId"] as $colour) {
            $this->pdo->prepare($query)->execute([$plantId, $colour]);
        }
    }
}

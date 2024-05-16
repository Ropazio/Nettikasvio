<?php

namespace app\Models;

use app\Models\DatabaseModel;


class FilterModel extends DatabaseModel {

    public function __construct() {

        parent::__construct();
    }


    public function applyAndGetPlants( ?string $searchString, ?string $colourId, ?int $typeId) : array {

        // Fetch plant name and type by joining plantsType - id with plants - type id.
        $query =    "SELECT plants.name AS name, plants.info AS info, plants.images AS images
                    FROM plants
                    LEFT JOIN plantsType ON plantsType.id = plants.typeId
                    LEFT JOIN plantsColour ON plantsColour.id = plants.colourId";
    
        $filterSelections = [];
    
        /*
        If both filters:         $filterSelections = ["plantsType.typeId = $typeId",
                                                       "plantsColour.colourId = $colourId"]
        If only type filter:     $filterSelections = ["plantsType.typeId = $typeId"]
        If only colour filter    $filterSelections = ["plantsColour.colourId = $colourId"]
        If empty, no filter
        */
    
        if (!empty($typeId)) {
            array_push($filterSelections, "plantsType.id = :typeId");
        }
    
        if (!empty($colourId)) {
            array_push($filterSelections, "plantsColour.id = :colourId");
        }
    
        // Apply name search.
        if (!empty($searchString)) {
            array_push($filterSelections, "name LIKE :searchString");
        }
    
        /*
        count == 0:  $whereClause = "";
        count == 1:  $whereClause = " WHERE plants.typeId = 2";
        count  > 1:  $whereClause = " WHERE plants.typeId = 2 AND plants.colourId = 3";
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
                      FROM plantsColour";
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
    
        $query = "SELECT plantsColour.colourName FROM plantsColour";
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
                $query = "SELECT plantsColour.id AS colourId
                          FROM plantsColour WHERE plantsColour.colourName = ?";
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
        $ids["colourId"] = json_encode($ids["colourId"]);
        $query = "INSERT INTO plants (name, info, typeId, colourId, images) VALUES (?, ?, ?, ?, ?)";
        $this->pdo->prepare($query)->execute([$speciesName, $speciesDesc, $ids["typeId"], $ids["colourId"], $images]);
    }
}

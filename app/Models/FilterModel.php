<?php

namespace app\Models;

use app\Models\DatabaseModel;


class FilterModel extends DatabaseModel {

    public function __construct() {

        parent::__construct();
    }


    public function applyAndGetPlants( ?string $searchString, ?int $colourId, ?int $typeId) : array {

        // Fetch plant name and type by joining plantsType - id with plants - type id.
        $query =    "SELECT plants.name AS name, plants.info AS info, plants.image AS image
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
            array_push($filterSelections, "plantsType.typeName = :typeId");
        }
    
        if (!empty($colourId)) {
            array_push($filterSelections, "plantsColour.colourName = :colourId");
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
    
        $plants = $sth->fetchAll();
    
        // Plants is an array with plant name and plant type.
        return $plants;
    }


    public function countFilterListLength($filterName) : int {
    
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
    
        $colours = $sth->fetchAll();
    
        return $colours;
    }


    public function getTypeNames() : array {
    
        $query = "SELECT plantsType.typeName FROM plantsType";
        $sth = $this->pdo->prepare($query);
        $sth->execute();
    
        $types = $sth->fetchAll();
    
        return $types;
    }


    public function convertFilterNameToId( string $colourName, string $typeName ) : array {

        $query = "SELECT plantsColour.id AS colourId, plantsType.id AS typeId
                  FROM plantsColour, plantsType
                  WHERE plantsColour.colourName = '$colourName'
                  AND plantsType.typeName = '$typeName'";
    
        $sth = $this->pdo->prepare($query);
        $sth->execute();
    
        $ids = $sth->fetch();
    
        // Id's is a list that contains id['colourId'] and id['typeId'].
        return $ids;
    }
}

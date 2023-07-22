<?php

// Database connection

$dbConfig = [
    'name'          => 'nettikasvio',
    'user'          => 'root',
    'password'      => '',
    'options'       => []
];

$pdo = new PDO(
    'mysql:host=127.0.0.1;dbname='. $dbConfig['name'],
    $dbConfig['user'],
    $dbConfig['password'],
    $dbConfig['options']
);

// Open connection to database
$pdo->exec('SET NAMES utf8');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

///////////////////////////////////////////////////////////////////////////////


//function get_plants() {
//
//    global $pdo;
//
//    // Fetch plants
//    $query = "SELECT id, name FROM plants";
//    $sth = $pdo->prepare($query);
//    $sth->execute();
//
//    $plants = $sth->fetchAll();
//
//    foreach ($plants as &$plant) {
//
//        $plant = [
//            'id'        => $plant['id'],
//            'name'      => $plant['name'],
//            'colour_id' => $plant['colour_id'],
//            'type_id'	=> $plant['type_id']
//        ];
//    }
//
//    return $plants;
//}


function apply_filters_and_get_plants_list($colour_id, $type_id) {

	global $pdo;

	$query_plants_name_and_type = "SELECT plants.name, plants_type.type 
		    				      FROM plants 
								  LEFT JOIN plants_type ON plants_type.id = plants.type_id";
	
	$filter_selections = [];
	
	// If both filters:			$filter_selections = ["plants.type_id = $type_id",
	//			           				 			  "plants.colour_id = $colour_id"]
	// If only type filter:		$filter_selections = ["plants.type_id = $type_id"]
	// If only colour filter 	$filter_selections = ["plants.colour_id = $colour_id"]
	// If empty, no filter
	
	if (!empty($type_id)) {
		array_push($filter_selections, "plants.type_id = $type_id");
	}
	
	if (!empty($filter['colour'])) {
		array_push($filter_selections, "plants.colour_id = $colour_id");
	}
	
	$query_construction = "{$query_plants_name_and_type} WHERE {${implode("AND", $filter_selections)}}";
	
	
	$query = 	"SELECT
					plants.name AS plant_name,
					plants_type.type AS plant_type,
					plants_colours.colour_name AS plant_colour
				FROM plants 
				LEFT JOIN plants_type ON plants_type.id = plants.type_id
				WHERE plants.type_id = $type_id AND plants.colour_id = $colour_id";
	
	$sth = $pdo->prepare($query);
    $sth->execute();

    $plants = $sth->fetchAll();

    return $plants;
	
	//	if ($colour_id = NULL) {
	//		$query_plant_colour = "SELECT * FROM plants";
	//	}
	//	else {
	//		$query_plant_colour =  "SELECT plants.name, plants_colour.colour_name
	//								FROM plants 
	//								LEFT JOIN plants_colour ON plants_colour.id = plants.colour_id
	//								WHERE plants.colour_id = $colour_id";
	//	}
	//
	//	if ($type_id = NULL) {
	//		$query_plant_type = "SELECT * FROM plants";
	//	}
	//	else {
	//		$query_plant_type =    "SELECT plants.name, plants_type.type 
	//								FROM plants 
	//								LEFT JOIN plants_type ON plants_type.id = plants.type_id 
	//								WHERE plants.type_id = $type_id";
	//	}
	//
	//	$query_colour_and_type_intersect = $query_plant_colour . "INTERSECT" . $query_plant_type;
	}

function count_filter_list_length($filter_name) {

	global $pdo;
	
	if ($filter_name == 0) {
		$query = "SELECT COUNT(*) AS colour_count
		 		  FROM plants_colour";
		}
	if ($filter_name == 1) {
		$query = "SELECT COUNT(*) AS type_count
				  FROM plants_type";
		}
	
	$sth = $pdo->prepare($query);
    $sth->execute();
    $count = $sth->fetchColumn();

	return $count;
}

function get_colour_names_from_database() {

	global $pdo;

	$query = "SELECT plants_colour.colour_name FROM plants_colour";
	$sth = $pdo->prepare($query);
    $sth->execute();

    $colours = $sth->fetchAll();

    return $colours;
}

function get_type_names_from_database() {

	global $pdo;

	$query = "SELECT plants_type.type FROM plants_type";
	$sth = $pdo->prepare($query);
    $sth->execute();

    $types = $sth->fetchAll();

    return $types;
}

?>
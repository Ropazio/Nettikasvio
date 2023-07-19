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


function get_plants() {

    global $pdo;

    // Fetch plants
    $query = "SELECT * FROM plants";
    $sth = $pdo->prepare($query);
    $sth->execute();

    $plants = $sth->fetchAll();

    foreach ($plants as &$plant) {

        $plant = [
            'id'        => $plant['id'],
            'name'      => $plant['name'],
            'colour_id' => $plant['colour_id'],
            'type_id'	=> $plant['type_id']
        ];
    }

    return $plants;
}

function link_colour_and_type_to_id($colour_id, $type_id) {
	$query_plant_colour = "SELECT plants.name AS plant_name, 
						plants_colour.colour_name
						FROM plants 
						LEFT JOIN plants_colour ON plants_colour.id = colour_id 
						WHERE plants.id = $colour_id";

	$query_plant_type = "SELECT plants.name AS plant_name,
						plants_type.type 
						FROM plants 
						LEFT JOIN plants_type ON plants_type.id = type_id 
						WHERE plants.id = $type_id";
}

function filter_by_colour() {
	// code...
}

?>
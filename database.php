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

// Avaa tietokantayhteys
$pdo->exec('SET NAMES utf8');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

///////////////////////////////////////////////////////////////////////////////


function get_plants() {

    global $pdo;

    // Fetch plants
    $query = "SELECT * FROM plants;
    $sth = $pdo->prepare($query);
    $sth->execute();

    $plants = $sth->fetchAll();

    foreach ($plants as &$plant) {

        $plant = [
            'id'        => $plant['id'],
            'name'      => $name['name'],
            'colour'    => $colour['colour'],
        ];
    }

    return $plants;
}
}

function filter_by_colour() {
	// code...
}



?>
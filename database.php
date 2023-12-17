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
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////


function apply_filters_and_get_plants($search_string, $colour_id, $type_id) {

    global $pdo;

    // Fetch plant name and type by joining plants_type - id with plants - type id.
    $query_plants_name_and_type = "SELECT plants.name, plants_type.type_name
                                  FROM plants
                                  LEFT JOIN plants_type ON plants_type.id = plants.type_id
                                  LEFT JOIN plants_colour ON plants_colour.id = plants.colour_id";

    $filter_selections = [];

    // If both filters:         $filter_selections = ["plants_type.type_id = $type_id",
    //                                                "plants_colour.colour_id = $colour_id"]
    // If only type filter:     $filter_selections = ["plants_type.type_id = $type_id"]
    // If only colour filter    $filter_selections = ["plants_colour.colour_id = $colour_id"]
    // If empty, no filter

    if (!empty($type_id)) {
        array_push($filter_selections, "plants_type.type_name = '$type_id'");
    }

    //if (!empty($filter['colour'])) {
    if (!empty($colour_id)) {
        array_push($filter_selections, "plants_colour.colour_name = '$colour_id'");
    }

    // count == 0:  $where_clause = "";
    // count == 1:  $where_clause = " WHERE plants.type_id = 2";
    // count  > 1:  $where_clause = " WHERE plants.type_id = 2 AND plants.colour_id = 3";

    // Apply filters.
    $where_clause = count($filter_selections) > 0 ? " WHERE " . implode(" AND ", $filter_selections) : "";

    // Apply name search.
    if (!empty($search_string)) {
        $where_clause .= " AND name LIKE '%{$search_string}%'";
    }

    $query_construction = "{$query_plants_name_and_type}{$where_clause}";

    $sth = $pdo->prepare($query_construction);
    $sth->execute();

    $plants = $sth->fetchAll();

    // Plants is an array with plant name and plant type.
    return $plants;
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

    $query = "SELECT plants_type.type_name FROM plants_type";
    $sth = $pdo->prepare($query);
    $sth->execute();

    $types = $sth->fetchAll();

    return $types;
}

function convert_filter_name_to_id($colour_name, $type_name) {

    global $pdo;

    $query = "SELECT plants_colour.id AS colour_id, plants_type.id AS type_id
              FROM plants_colour, plants_type
              WHERE plants_colour.colour_name = '$colour_name'
              AND plants_type.type_name = '$type_name'";

    $sth = $pdo->prepare($query);
    $sth->execute();

    $ids = $sth->fetch();

    // Id's is a list that contains id['colour_id'] and id['type_id'].
    return $ids;
}

?>
<?php

require_once "database.php";


function get_header() {

    echo '<head>';
    echo    '<meta charset="utf-8">';
    echo    '<meta name="viewport" content="width=device-width, initial-scale=1">';
    echo    '<meta name="robots" content="noindex">';
    echo    '<meta name="googlebot" content="noindex">';
    echo    '<title>Nettikasvio</title>';
    echo    '<link rel="stylesheet" href="herbarium.css" />';
    echo '</head>';
}

function get_main_headline_box() {

    echo '<div class="main_headline_box">';
    echo    '<h1 class="main_headline">';
    echo        '<a class="link_main" href="/Nettikasvio/index.php">KASVIO</a>';
    echo    '</h1>';
    echo '</div>';
}

function get_navi() {

    echo '<div class="navi">';
    echo    '<table class="navi_table">';
    echo        '<tr>';
    echo            '<th class="table_headline headline_border">';
    echo                '<h2>';
    echo                    '<a class="link" href="/Nettikasvio/plant_list.php">Kasvilista</a>';
    echo                '</h2>';
    echo            '</th>';
    echo            '<th class="table_headline headline_border">';
    echo                '<h2>';
    echo                    '<a class="link" href="/Nettikasvio/identification.php">Lajintunnistus</a>';
    echo                '</h2>';
    echo            '</th>';
    echo            '<th class="table_headline">';
    echo                '<h2>';
    echo                    '<a class="link" href="/Nettikasvio/others.php">Muuta kivaa</a>';
    echo                '</h2>';
    echo            '</th>';
    echo        '</tr>';
    echo    '</table>';
    echo '</div>';
}

function print_plants_list($search_string, $colour_id, $type_id) {

    $rows = apply_filters_and_get_plants($search_string, $colour_id, $type_id);

    if (empty($rows)) {
        echo "<p>Ei kasveja.</p>";
    }
    else {
        foreach ($rows as $row) {
            get_plant_list($row);
        }

    }
}

function get_filter_lists_lengths() {

    $colour_and_type_list_lengths = [];

    $query_colour_length = count_filter_list_length(0);
    $query_type_length = count_filter_list_length(1);

    array_push($colour_and_type_list_lengths, $query_colour_length, $query_type_length);

    return $colour_and_type_list_lengths;
}

function get_colour_name($index) {
    $colours = get_colour_names_from_database();

    if (empty($colours)) {
        return "Virhe filtterissä :(";
    }

    return $colours[$index]['colour_name'];
}

function get_type_name($index) {
    $types = get_type_names_from_database();

    if (empty($types)) {
        return "Virhe filtterissä :(";
    }

    return $types[$index]['type_name'];
}

?>
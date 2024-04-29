<?php

require_once "database.php";

$colour_and_type_lengths = get_filter_lists_lengths();


function print_plants_list($search_string, $colour_id, $type_id) {

    $rows = apply_filters_and_get_plants($search_string, $colour_id, $type_id);

    if (empty($rows)) {
        echo '<p class="plant_list_column_2">Ei kasveja.</p>';
    }
    else {
        get_plant_list_headlines();
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
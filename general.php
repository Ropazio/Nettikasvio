<?php

require_once "database.php";

$colour_and_type_lengths = get_filter_lists_lengths();

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

    echo '<div id="main_headline_box">';
    echo    '<h1 id="main_headline">';
    echo        '<a id="link_main" href="/Nettikasvio/index.php">KASVIO</a>';
    echo    '</h1>';
    echo '</div>';
}

function get_navi() {

    echo '<div id="navi">';
    echo    '<div id="grid_navi">';
    echo        '<div class="navi_column_1 headline_border">';
    echo            '<h2>';
    echo                '<a class="link" href="/Nettikasvio/plant_list.php">Kasvilista</a>';
    echo            '</h2>';
    echo        '</div>';
    echo        '<div class="navi_column_2 headline_border">';
    echo            '<h2>';
    echo                '<a class="link" href="/Nettikasvio/identification.php">Lajintunnistus</a>';
    echo            '</h2>';
    echo        '</div>';
    echo        '<div class="navi_column_3">';
    echo            '<h2>';
    echo                '<a class="link" href="/Nettikasvio/others.php">Muuta kivaa</a>';
    echo            '</h2>';
    echo        '</div>';
    echo    '</div>';
    echo '</div>';
}

function get_filter() {

    echo '<div id="filter">';
    echo    '<div>';
    echo        '<form method="POST" action="search.php" id="grid_filter">';
    echo            '<div class="filter_column_1">';
                        add_text_search();
    echo            '</div>';
    echo            '<div class="filter_column_2">';
                        add_colour_filter();
    echo            '</div>';
    echo            '<div class="filter_column_3">';
                        add_type_filter();
    echo            '</div>';
    echo            '<div class="filter_column_4">';
                        add_submit_button();
    echo            '</div>';
    echo        '</form>';
    echo    '</div>';
    echo '</div>';
}

function get_plant_list_headlines() {

    echo '<div id="plant_list">';
    echo    '<div id="grid_plant_list">';
    echo        '<h4 class="plant_list_column_1">' . "Kasvin nimi" . '</h4>';
    echo        '<h4 class="plant_list_column_2">' . "Tiedot" . '</h4>';
    echo        '<h4 class="plant_list_column_3">' . "Havaintokuvat" . '</h4>';
    echo    '</div>';
    echo '</div>';
}

function get_plant_list($plant) {
    echo '<div id="plant_list">';
    echo    '<div id="grid_plant_list">';
    echo        '<div class="plant_list_column_1 headline_border">';
    echo            '<p>' . $plant['name'] . '</p>';
    echo        '</div>';
    echo        '<div class="plant_list_column_2 headline_border">';
    echo            '<p>' . $plant['info'] . '</p>';
    echo        '</div>';
    echo        '<div class="plant_list_column_3">';
    echo            '<p>' . $plant['image'] . '</p>';
    echo        '</div>';
    echo    '</div>';
    echo '</div>';
}

function add_colour_filter() {

    global $colour_and_type_lengths;

    echo                    '<select name="colour" class="filter_dropdown">';
                                // get colour filter list length and enumerate colours
                                $colour = null;
                                $selected_colour = $colour == $_SESSION['colour'];
    echo                        "<option value=\"{$type}\"{$selected_type} disabled hidden selected>" . "( Valitse kasvin väri )" . '</option>';
    echo                        "<option value=\"{$colour}\"{$selected_colour}>" . "ei valintaa" . '</option>';
                                for ($i = 0; $i < $colour_and_type_lengths[0]; $i++) {
                                    $colour = get_colour_name($i);
                                    $selected_colour = $colour == $_SESSION['colour'];
    echo                            "<option value=\"{$colour}\"{$selected_colour}>" . $colour . '</option>';
                                }
    echo                    '</select>';
}


function add_type_filter() {

    global $colour_and_type_lengths;

    echo                    '<select name="type" class="filter_dropdown">';
                                // get type filter list length and enumerate colours
                                $type = null;
                                $selected_type = $type == $_SESSION['type'];
    echo                        "<option value=\"{$type}\"{$selected_type} disabled hidden selected>" . "( Valitse kasvin tyyppi )" . '</option>';
    echo                        "<option value=\"{$type}\"{$selected_type}>" . "ei valintaa" . '</option>';
                                for ($i = 0; $i < $colour_and_type_lengths[1]; $i++) {
                                    $type = get_type_name($i);
                                    $selected_type = $type == $_SESSION['type'];
    echo                            "<option value=\"{$type}\"{$selected_type}>" . $type . '</option>';
                                }
    echo                    '</select>';
}


function add_text_search() {
    echo                    "<input type=\"text\" id=\"text_search\" name=\"search_string\" value=\"{$_SESSION['search_string']}\">";
}

function add_submit_button() {
    echo                '<input type="submit" id="search_button" value="Hae kasveja">';
}

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
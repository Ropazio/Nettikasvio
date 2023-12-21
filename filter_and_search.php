<?php
require_once "general.php";

$colour_and_type_lengths = get_filter_lists_lengths();

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
    echo                        "<option value=\"{$type}\"{$selected_type} disabled hidden selected>" . "Valitse kasvin väri" . '</option>';
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
    echo                        "<option value=\"{$type}\"{$selected_type} disabled hidden selected>" . "Valitse kasvin tyyppi" . '</option>';
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

?>

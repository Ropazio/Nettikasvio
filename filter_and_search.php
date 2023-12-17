<?php
require_once "general.php";

$colour_and_type_lengths = get_filter_lists_lengths();

function get_filter() {

    echo '<div class="filter">';
    echo    '<table class="filter_table">';
    echo        '<tr>';
    echo            '<form method="POST" action="search.php">';
    echo                '<th class="filter_table_search filter_headline_border">';
                            add_text_search();
    echo                '</th>';
    echo                '<th class=filter_dropdown_frame>';
                            add_colour_filter();
    echo                '</th>';
    echo                '<th class=filter_dropdown_frame>';
                            add_type_filter();
    echo                '</th>';
    echo                '<th class="filter_button_frame">';
    echo                    '<input type="submit" id="search_button" value="Hae kasveja">';
    echo                '</th>';
    echo            '</form>';
    echo        '</tr>';
    echo    '</table>';
    echo '</div>';
}

function get_plant_list($plant) {
    echo '<div class="plant_list">';
    echo    '<p>' . $plant['name'] . ' - ' . $plant['info'] . $plant['image'] . '</p>';
    echo '</div>';
}

function add_colour_filter() {

    global $colour_and_type_lengths;

    echo                    '<select name="colour" class="filter_button">';
                                // get colour filter list length and enumerate colours
                                $colour = null;
                                $selected_colour = $colour == $_SESSION['colour'] ? ' selected' : '';
    echo                        "<option value=\"{$colour}\"{$selected_colour}>" . "ei valintaa" . '</option>';
                                for ($i = 0; $i < $colour_and_type_lengths[0]; $i++) {
                                    $colour = get_colour_name($i);
                                    $selected_colour = $colour == $_SESSION['colour'] ? ' selected' : '';
    echo                            "<option value=\"{$colour}\"{$selected_colour}>" . $colour . '</option>';
                                }
    echo                    '</select>';
}


function add_type_filter() {

    global $colour_and_type_lengths;

    echo                    '<select name="type" class="filter_button">';
                                // get type filter list length and enumerate colours
                                $type = null;
                                $selected_type = $type == $_SESSION['type'] ? ' selected' : '';
    echo                        "<option value=\"{$type}\"{$selected_type}>" . "ei valintaa" . '</option>';
                                for ($i = 0; $i < $colour_and_type_lengths[1]; $i++) {
                                    $type = get_type_name($i);
                                    $selected_type = $type == $_SESSION['type'] ? ' selected' : '';
    echo                            "<option value=\"{$type}\"{$selected_type}>" . $type . '</option>';
                                }
    echo                    '</select>';
}


function add_text_search() {
    echo                    "<input type=\"text\" id=\"search\" name=\"search_string\" value=\"{$_SESSION['search_string']}\">";
}

?>

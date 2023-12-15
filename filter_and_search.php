<?php
require_once "general.php";

function get_filter() {

    $colour_and_type_lengths = get_filter_lists_lengths();

    echo '<div class="filter">';
    echo    '<table class="filter_table">';
    echo        '<tr>';
    echo            '<form action="search.php">';
    echo            '<th class="filter_table_search filter_headline_border">';
    echo                '<input type="text" id="search">';
    echo            '</th>';
    echo            '<th class=filter_dropdown_frame>';
    echo                '<select name="colours" class="filter_button">';
                                // get colour filter list length and enumerate colours
                                for ($i = 0; $i < $colour_and_type_lengths[0]; $i++) {
    echo                            '<option value="<?=get_colour_name($i); ?>">' . get_colour_name($i) . '</option>';
                                }
    echo                '</select>';
    echo            '</th>';
    echo            '<th class=filter_dropdown_frame>';
    echo                '<select name="types" class="filter_button">';
                                // get type filter list length and enumerate colours
                                for ($i = 0; $i < $colour_and_type_lengths[1]; $i++) {
    echo                            '<option value="<?=get_type_name($i); ?>">' . get_type_name($i) . '</option>';
                                }
    echo                '</select>';
    echo            '</th>';
    echo            '<th class="filter_button_frame">';
    echo                '<input type="submit" id="search_button" value="Hae kasveja">';
    echo            '</th>';
    echo            '</form>';
    echo        '</tr>';
    echo    '</table>';
    echo '</div>';
}

?>
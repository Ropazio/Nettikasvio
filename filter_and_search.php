<?php
require_once "general.php";


function get_filter() {

    echo '<div class="filter">';
    echo    '<table class="filter_table">';
    echo        '<tr>';
    echo            '<form action="search.php">';
    echo            '<th class="filter_table_search filter_headline_border">';
    echo                '<input type="text" id="search">';
    echo            '</th>';
    echo            '<th class=filter_table_dropdown>';
    echo                '<button type="button" class="dropdown" onclick="activate_dropdown()">Kukan väri';
    echo                    '<i id="turn_arrow" class="arrow_right"></i>';
    echo                '</button>';
    echo                '<div>';
    echo                    '<div class="dropdown_content" id="colour_dropdown"</a>';
                                $colour_and_type_lengths = get_filter_lists_lengths();

                                // get colour filter list length and enumerate colours
                                for ($i = 0; $colour_and_type_lengths[0]; $i++) {
    echo                                '<a class="colour_option" href="#">'                                  . get_colour_name($i) . '</a>';
                                }
    echo                    '</div>';
    echo                '</div>';
    echo            '</th>';
    echo            '<th class="filter_table_button">';
    echo                '<input type="submit" id="search_button">';
    echo            '</th>';
    echo            '</form>';
    echo        '</tr>';
    echo    '</table>';
    echo '</div>';
}
?>
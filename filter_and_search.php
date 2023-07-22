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
    echo            '<th class=filter_table_dropdown>';
    echo                '<button type="button" class="dropdown" onclick="activate_dropdown(0)">Väri:';
    echo                    '<i class="arrow_right"></i>';
    echo                '</button>';
    echo                '<div>';
    echo                    '<div class="filter_dropdown dropdown_content"</a>';
    echo                           '<a class="filter_option" href="#">kaka</a>';
                                // get colour filter list length and enumerate colours
                                for ($i = 0; $colour_and_type_lengths[0]; $i++) {
    echo                                '<a class="filter_option" href="#">'                                  . get_colour_name($i) . '</a>';
                                }
    echo                    '</div>';
    echo                '</div>';
    echo            '</th>';
    echo            '<th class=filter_table_dropdown>';
    echo                '<button type="button" class="dropdown" onclick="activate_dropdown(1)">Tyyppi:';
    echo                    '<i class="arrow_right"></i>';
    echo                '</button>';
    echo                '<div>';
    echo                    '<div class="filter_dropdown dropdown_content"</a>';

                                // get type filter list length and enumerate colours
                                for ($i = 0; $colour_and_type_lengths[1]; $i++) {
    echo                                '<a class="filter_option" href="#">'                                  . get_type_name($i) . '</a>';
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
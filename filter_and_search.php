<?php

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
    echo                        '<a class="colour_option" style="color:red" href="#">Punainen</a>';
    echo                        '<a class="colour_option" style="color:blue" href="#">Sininen</a>';
    echo                        '<a class="colour_option" style="color:yellow" href="#">Keltainen</a>';
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
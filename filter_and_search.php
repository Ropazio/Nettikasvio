<?php

function get_filter() {

	echo '<div class="filter">';
	echo 	'<table class="filter_table">';
	echo 		'<tr>';
	echo 			'<th class="filter_table_headline filter_headline_border">';
	echo 				'<form action="search.php">';
  	echo 					'<input type="text" id="search" name="fname">';
  	echo 					'<input type="submit" id="search_button" value="🔍">';
	echo 				'</form>';
	echo 			'</th>';
	echo 			'<th class="filter_table_headline">';
	echo 				'<h3>';
	echo 					'<button class="dropdown" onclick="activate_dropdown()">Kukan väri';
    echo 						'<i class="fa fa-caret-down"></i>';
  	echo 					'</button>';
  	echo 					'<div class="dropdown_content" id="colour_dropdown"</a>';
    echo 						'<a class="colour_option" href="#">Punainen</a>';
    echo 						'<a class="colour_option" href="#">Sininen</a>';
    echo 						'<a class="colour_option" href="#">Keltainen</a>';
  	echo 					'</div>';
	echo				'</h3>';
	echo 			'</th>';
	echo 		'</tr>';
	echo 	'</table>';
	echo '</div>';
}
?>
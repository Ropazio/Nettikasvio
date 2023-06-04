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
	echo 					'<label for="Kukan_väri">Kukan väri:</label>';
	echo 					'<input type="text" id="search" name="fname">';
	echo				'</h3>';
	echo 			'</th>';
	echo 		'</tr>';
	echo 	'</table>';
	echo '</div>';
}
?>
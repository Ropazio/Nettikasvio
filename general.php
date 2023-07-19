<?php

require_once "database.php";



function get_header() {

	echo '<head>';
	echo 	'<meta charset="utf-8">';
	echo 	'<meta name="viewport" content="width=device-width, initial-scale=1">';
	echo 	'<meta name="robots" content="noindex">';
	echo 	'<meta name="googlebot" content="noindex">';
	echo 	'<title>Nettikasvio</title>';
	echo 	'<link rel="stylesheet" href="herbarium.css" />';
	echo '</head>';
}

function get_main_headline_box() {

	echo '<div class="main_headline_box">';
	echo 	'<h1 class="main_headline">';
	echo 		'<a class="link_main" href="https://ropaz.dev/Nettikasvio/index.php">KASVIO</a>';
	echo 	'</h1>';
	echo '</div>';
}

function get_navi() {

	echo '<div class="navi">';
	echo 	'<table class="navi_table">';
	echo 		'<tr>';
	echo 			'<th class="table_headline headline_border">';
	echo 				'<h2>';
	echo 					'<a class="link" href="https://ropaz.dev/Nettikasvio/plant_list.php">Kasvilista</a>';
	echo 				'</h2>';
	echo 			'</th>';
	echo 			'<th class="table_headline headline_border">';
	echo 				'<h2>';
	echo 					'<a class="link" href="https://ropaz.dev/Nettikasvio/identification.php">Lajintunnistus</a>';
	echo 				'</h2>';
	echo 			'</th>';
	echo 			'<th class="table_headline">';
	echo 				'<h2>';
	echo 					'<a class="link" href="https://ropaz.dev/Nettikasvio/others.php">Muuta kivaa</a>';
	echo 				'</h2>';
	echo 			'</th>';
	echo 		'</tr>';
	echo 	'</table>';
	echo '</div>';
}

function print_plants_list() {

	$rows = get_plants();

	if (empty($rows)) {
		echo "<p>Ei kasveja.</p>";
	}
	else {
		foreach ($rows as $row) {
			echo "<p>" . $row['id'] . "</p>";
			echo "<p>" . $row['name'] . "</p>";
		}

	}
}

?>
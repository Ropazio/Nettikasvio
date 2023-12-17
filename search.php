<?php

require_once "session.php";
require_once "database.php";

$search_string = $_POST['search_string'];
$colour = $_POST['colour'];
$type = $_POST['type'];

// Convert filter names to corresponding id's.
$filter_ids = convert_filter_name_to_id($colour, $type);
$filter_ids['colour_id'] = $colour;
$filter_ids['type_id'] = $type;

$_SESSION['search_string'] = $search_string;
$_SESSION['colour'] = $colour;
$_SESSION['type'] = $type;
header("Location: plant_list.php");

?>
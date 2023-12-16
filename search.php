<?php

$colour = $_POST['colour'];
$type = $_POST['type'];

echo "<p>" . $colour . $type . "</p>";

header("Location: plant_list.php");

?>
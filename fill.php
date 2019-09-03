<?php
date_default_timezone_set('Europe/Prague');
ini_set ('max_execution_time', 0);
$link = mysqli_connect('localhost', 'root', 'root', 'NIS');

$start_date = "2017-12-30"; // jeden den před prvním požadovaným datumem
$fixdate = date_create_from_format("Y-m-d", $start_date);
$i = 0;

while ($totodatum != "2018-12-01") {
	$prirustek = "1 day";
	date_add($fixdate, date_interval_create_from_date_string($prirustek));
	$totodatum = date_format($fixdate, 'Y-m-d');

	$query16 = "INSERT INTO datumy (datum) VALUES ('$totodatum');";
	echo "$query16<br/>";
	$prikaz16 = mysqli_query($link, $query16);

	$i = $i + 1;
}


mysqli_close($link);
?>
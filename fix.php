<?php
date_default_timezone_set('Europe/Prague');
ini_set ('max_execution_time', 0);
$link = mysqli_connect('localhost', 'root', 'root', 'NIS');

$query5 = "SELECT idUdalost FROM udalosti WHERE humanID IS NULL;";
if ($result5 = mysqli_query($link, $query5)) {
	while ($row5 = mysqli_fetch_row($result5)) {
		$idUdalost = $row5[0];

		$query17 = "SELECT humanID FROM datove_vety WHERE idUdalost = '$idUdalost' ORDER BY datumVytvoreni LIMIT 1;";
		if ($result17 = mysqli_query($link, $query17)) {
			while ($row17 = mysqli_fetch_row($result17)) {
				$humanID = $row17[0];
			}
		}

		$query18 = "UPDATE udalosti SET humanID = '$humanID' WHERE idUdalost = '$idUdalost';";
		echo "$query18<br/>";
		$prikaz18 = mysqli_query($link, $query18);
	}
}

mysqli_close($link);
?>
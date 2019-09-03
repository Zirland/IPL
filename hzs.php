<?php
date_default_timezone_set('Europe/Prague');
ini_set ('max_execution_time', 0);
$link = mysqli_connect('localhost', 'root', 'root', 'NIS');

echo "<table>";
echo "<tr><th>ID</th><th>Start</th><th>Uložení</th><th>Vytěžení</th><th>Co se stalo</th><th>HZS</th><th>Čas HZS</th><th>PČR</th><th>Čas PČR</th><th>ZZS</th><th>Čas ZZS</th><th>Složka</th><th>Kraj</th><th>Typ</th><th>Podtyp</th></tr>";

//$query6 = "SELECT * FROM udalosti WHERE (typ = 'DON' OR podtyp IN ('LET', 'VOS', 'ZEL', 'ZRA', '001', '002', '003', '004', '005', 'A02', 'A03', 'A04', 'A05', 'A06', 'A07', 'A08', 'A09')) AND podtyp NOT IN ('POL', 'UKO', 'UVO') ORDER BY datumUlozeni;";
$query6 = "SELECT * FROM udalosti WHERE (typ = 'POZ' OR podtyp IN ('DOP', 'LES', 'NIB', 'ODP', 'POB', 'POD', 'POP', 'PPT', 'SHO', 'TRA', 'VYB', 'A17', 'A18', 'A19', 'A20', 'A21', 'A22', 'A23', 'A24', 'A25', 'A26', '094', '095')) AND podtyp NOT IN ('PLP') ORDER BY datumUlozeni;";


if ($result6 = mysqli_query($link, $query6)) {
	while ($row6 = mysqli_fetch_row($result6)) {
		$idUdalost = $row6[0];
		$casHovor = $row6[1];
		$datumUdalosti = $row6[2];
		$datumUlozeni = $row6[3];
		$popis = $row6[4];
		$hzs = $row6[5];
		$pcr = $row6[6];
		$zzs = $row6[7];
		$slozka = $row6[8];
		$kraj = $row6[9];
		$typ = $row6[10];
		$podtyp = $row6[11];
		$human_id = $row6[12];

		if (($casHovor != "" && $casHovor < $datumUdalosti) || $datumUdalosti == "") {
			$casStart = $casHovor;
		} else {
			$casStart = $datumUdalosti;
		}

		
		$casStartform = $casStart;
		if ($casStartform != '') {
			$casStartform = date("d.m.Y H:i:s", $casStartform);
		}

		$datumUlozeniform = $datumUlozeni;
		$datumUlozeniform = date("d.m.Y H:i:s", $datumUlozeniform);

		$hzsform = $hzs;
		if ($hzsform != '') {
			$hzsform = date("d.m.Y H:i:s", $hzsform);
		}

		$pcrform = $pcr;
		if ($pcrform != '') {
			$pcrform = date("d.m.Y H:i:s", $pcrform);
		}

		$zzsform = $zzs;
		if ($zzsform != '') {
			$zzsform = date("d.m.Y H:i:s", $zzsform);
		}

		$vytezeni = $datumUlozeni - $casStart;
		if ($hzs != "") {
			$casHZS = $hzs - $datumUlozeni;
		}
		if ($pcr != "") {
			$casPCR = $pcr - $datumUlozeni;
		}
		if ($zzs != "") {
			$casZZS = $zzs - $datumUlozeni;
		}

		echo "<tr><td>$human_id</td><td>$casStartform</td><td>$datumUlozeniform</td><td>$vytezeni</td><td>$popis</td><td>$hzsform</td><td>$casHZS</td><td>$pcrform</td><td>$casPCR</td><td>$zzsform</td><td>$casZZS</td><td>$slozka</td><td>$kraj</td><td>$typ</td><td>$podtyp</td></tr>";

		$vars = array_keys(get_defined_vars());
//		print_r($vars);
		$k = 0;
		foreach ($vars as $var) {
			if ($k > 8) {
				unset($$var);
			}
			$k ++;
		}
		unset ($vars, $k);

	}
}

echo "</table>";

mysqli_close($link);
?>



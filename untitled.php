<?php
date_default_timezone_set('Europe/Prague');
ini_set ('max_execution_time', 0);
$link = mysqli_connect('localhost', 'root', 'root', 'NIS');

echo "<table>";
echo "<tr><th>ID</th><th>Hovor</th><th>Vznik</th><th>Uložení</th><th>Co se stalo</th><th>HZS</th><th>PČR</th><th>ZZS</th><th>Složka</th><th>Kraj</th><th>Typ</th><th>Podtyp</th></tr>";

$query6 = "SELECT * FROM udalosti WHERE podtyp = 'VOS';";
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

		$casPrichoduHovoruform = $casHovor;
		if ($casPrichoduHovoruform != '') {
			$casPrichoduHovoruform = date("d.m.Y H:i:s", $casPrichoduHovoruform);
		}

		$datumUdalostiform = $datumUdalosti;
		$datumUdalostiform = date("d.m.Y H:i:s", $datumUdalostiform);

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

		echo "<tr><td>$idUdalost</td><td>$casPrichoduHovoruform</td><td>$datumUdalostiform</td><td>$datumUlozeniform</td><td>$popis</td><td>$hzsform</td><td>$pcrform</td><td>$zzsdform</td><td>$slozka</td><td>$kraj</td><td>$typUdalosti</td><td>$podtyp</td></tr>";

	}
}






$datum = $_GET["datum"];

$zacatek = mktime(0,0,0,9,$datum,2018);
$konec = mktime(0,0,0,9,$datum+1,2018);

echo "<table>";
echo "<tr><th>ID</th><th>Hovor</th><th>Vznik</th><th>Uložení</th><th>Co se stalo</th><th>HZS</th><th>PČR</th><th>ZZS</th><th>Složka</th><th>Kraj</th><th>Typ</th><th>Podtyp</th></tr>";

$query5 = "SELECT idUdalost FROM datove_vety WHERE datumUdalosti >= '$zacatek' AND datumUdalosti < '$konec' ORDER BY datumUdalosti;";
echo $query5;
if ($result5 = mysqli_query($link, $query5)) {
	while ($row5 = mysqli_fetch_row($result5)) {
		$idUdalost = $row5[0];

		$query10 = "SELECT casPrichoduHovoru FROM datove_vety WHERE idUdalost = '$idUdalost' AND casPrichoduHovoru IS NOT NULL ORDER BY datumVytvoreni LIMIT 1;";
		if ($result10 = mysqli_query($link, $query10)) {
			while ($row10 = mysqli_fetch_row($result10)) {
				$casPrichoduHovoru = $row10[0];
			}
		}

		$query17 = "SELECT datumUdalosti FROM datove_vety WHERE idUdalost = '$idUdalost' AND datumUdalosti IS NOT NULL ORDER BY datumVytvoreni LIMIT 1;";
		if ($result17 = mysqli_query($link, $query17)) {
			while ($row17 = mysqli_fetch_row($result17)) {
				$datumUdalosti = $row17[0];
			}
		}

		$query24 = "SELECT datumVytvoreni FROM datove_vety WHERE idUdalost = '$idUdalost' ORDER BY datumVytvoreni LIMIT 1;";
		if ($result24 = mysqli_query($link, $query24)) {
			while ($row24 = mysqli_fetch_row($result24)) {
				$datumUlozeni = $row24[0];
			}
		}

		$query31 = "SELECT popis FROM datove_vety WHERE idUdalost = '$idUdalost' AND popis IS NOT NULL AND popis != '' ORDER BY datumVytvoreni DESC LIMIT 1;";
		if ($result31 = mysqli_query($link, $query31)) {
			while ($row31 = mysqli_fetch_row($result31)) {
				$popis = $row31[0];
			}
		}

		$query38 = "SELECT datumVytvoreni FROM datove_vety WHERE idUdalost = '$idUdalost' AND dispecink LIKE 'H%' AND stav = 'TDO' ORDER BY datumVytvoreni LIMIT 1;";
		if ($result38 = mysqli_query($link, $query38)) {
			while ($row38 = mysqli_fetch_row($result38)) {
				$hzs = $row38[0];
			}
		}

		$query45 = "SELECT datumVytvoreni FROM datove_vety WHERE idUdalost = '$idUdalost' AND dispecink LIKE 'P%' AND stav = 'TDO' ORDER BY datumVytvoreni LIMIT 1;";
		if ($result45 = mysqli_query($link, $query45)) {
			while ($row45 = mysqli_fetch_row($result45)) {
				$pcr = $row45[0];
			}
		}

		$query52 = "SELECT datumVytvoreni FROM datove_vety WHERE idUdalost = '$idUdalost' AND dispecink LIKE 'Z%' AND stav = 'TDO' ORDER BY datumVytvoreni LIMIT 1;";
		if ($result52 = mysqli_query($link, $query52)) {
			while ($row52 = mysqli_fetch_row($result52)) {
				$zzs = $row52[0];
			}
		}

		$query59 = "SELECT odesilatel FROM datove_vety WHERE idUdalost = '$idUdalost' ORDER BY datumVytvoreni LIMIT 1;;";
		if ($result59 = mysqli_query($link, $query59)) {
			while ($row59 = mysqli_fetch_row($result59)) {
				$puvodce = $row59[0];
			}
		}

		$query66 = "SELECT typUdalosti FROM datove_vety WHERE idUdalost = '$idUdalost' AND typUdalosti IS NOT NULL AND typUdalosti != '' ORDER BY datumVytvoreni DESC LIMIT 1;";
		if ($result66 = mysqli_query($link, $query66)) {
			while ($row66 = mysqli_fetch_row($result66)) {
				$typUdalosti = $row66[0];
			}
		}

		$query73 = "SELECT podtypUdalosti FROM datove_vety WHERE idUdalost = '$idUdalost' AND podtypUdalosti IS NOT NULL AND podtypUdalosti != '' ORDER BY datumVytvoreni DESC LIMIT 1;";
		if ($result73 = mysqli_query($link, $query73)) {
			while ($row73 = mysqli_fetch_row($result73)) {
				$podtyp = $row73[0];
			}
		}

		$casPrichoduHovoruform = $casPrichoduHovoru;
		if ($casPrichoduHovoruform != '') {
			$casPrichoduHovoruform = date("d.m.Y H:i:s", $casPrichoduHovoruform);
		}

		$datumUdalostiform = $datumUdalosti;
		$datumUdalostiform = date("d.m.Y H:i:s", $datumUdalostiform);

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

		$slozka =  substr($puvodce, 0, 1);
		$kraj = substr($puvodce, -3);

//		echo "<tr><td>$idUdalost</td><td>$casPrichoduHovoruform</td><td>$datumUdalostiform</td><td>$datumUlozeniform</td><td>$popis</td><td>$hzsform</td><td>$pcrform</td><td>$zzsdform</td><td>$slozka</td><td>$kraj</td><td>$typUdalosti</td><td>$podtyp</td></tr>";

		$query107 = "INSERT INTO udalosti (idUdalost, casPrichoduHovoru, datumUdalosti, datumUlozeni, popis, hzs, pcr, zzs, slozka, kraj, typ, podtyp) VALUES ('$idUdalost','$casPrichoduHovoru','$datumUdalosti','$datumUlozeni','$popis','$hzs','$pcr','$zzs','$slozka','$kraj','$typUdalosti','$podtyp');";
		$prikaz107 = mysqli_query($link, $query107);

		$vars = array_keys(get_defined_vars());
//		print_r($vars);
		$k = 0;
		foreach ($vars as $var) {
			if ($k > 11) {
				unset($$var);
			}
			$k ++;
		}
		unset ($vars, $k);

	}
}

echo "</table>";

mysqli_close($link);

echo "Hotovo $datum";
$datum = $datum+1;
echo "<meta http-equiv=\"refresh\" content=\"5; url=list.php?datum=$datum\">";

?>



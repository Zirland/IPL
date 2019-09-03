<?php
ini_set ('max_execution_time', 0);

$pg_link = pg_connect("host=10.30.205.241 port=5432 dbname=nisizs_ipl user=jurbanek password=jupassword");
$link = mysqli_connect('localhost', 'root', 'root', 'NIS');

$query7 = "SELECT datum FROM datumy LIMIT 1;";
if ($result7 = mysqli_query($link, $query7)) {
	while ($row7 = mysqli_fetch_row($result7)) {
		$datum = $row7[0];
	}
}

$rok = substr($datum, 2, 2);
$mesic = substr($datum, 5, 2);
$table = "tpart_historie_dv_" . $rok . "_" . $mesic;

echo "Datum: $datum<br/>";

$query20 = "select zdroj from $table where (datum_vytvoreni at time zone 'utc')::date = '$datum' and typ_dv = 'DVUD' and prichozi=true order by vytvoreno LIMIT 10;";
echo "$query20<br/>";
if ($result20 = pg_query($pg_link, $query20)) {
	while ($row20 = pg_fetch_row($result20)) {
		$zdroj = $row20[0];

$xmlstr = <<<XML
$zdroj
XML;

		$xml = new SimpleXMLElement($xmlstr);
		$xml = dom_import_simplexml($xml);

		$raw_idDatovaVeta = $xml->getElementsByTagName('idDatovaVeta');
		$idDatovaVeta = $raw_idDatovaVeta->item(0)->nodeValue;

		$raw_datumVytvoreni = $xml->getElementsByTagName('datumVytvoreni');
		$datumVytvoreni = $raw_datumVytvoreni->item(0)->nodeValue;
		$datumVytvoreni = strtotime($datumVytvoreni);

		$vytvoreno = strtotime($vytvoreno);

		$raw_odesilatel = $xml->getElementsByTagName('odesilatel');
		for ($i = 0; $i < $raw_odesilatel->length; $i++) {
			$raw_odesilatel_kod = $raw_odesilatel->item(0)->getElementsByTagName('kod');
			$odesilatel = $raw_odesilatel_kod->item(0)->nodeValue;
		}

		$raw_idUdalost = $xml->getElementsByTagName('idUdalost');
		$idUdalost = $raw_idUdalost->item(0)->nodeValue;

		$raw_oznamovatel = $xml->getElementsByTagName('oznamovatel');
		for ($i = 0; $i < $raw_oznamovatel->length; $i++) {
			$raw_oznamovatel_jmeno = $raw_oznamovatel->item($i)->getElementsByTagName('oznamovatel');
			$oznamovatel = $raw_oznamovatel_jmeno->item(0)->nodeValue;

			$raw_oznamovatel_id = $raw_oznamovatel->item($i)->getElementsByTagName('idOznamovatel');
			$idOznamovatel = $raw_oznamovatel_id->item(0)->nodeValue;

			$raw_hovor = $raw_oznamovatel->item($i)->getElementsByTagName('hovor');
			for ($j = 0; $j < $raw_hovor->length; $j++) {

				$raw_hovor_volaneCislo = $raw_hovor->item($j)->getElementsByTagName('volaneCislo');
				$volaneCislo = $raw_hovor_volaneCislo->item(0)->nodeValue;

				$raw_hovor_volajiciCislo = $raw_hovor->item($j)->getElementsByTagName('volajiciCislo');
				$volajiciCislo = $raw_hovor_volajiciCislo->item(0)->nodeValue;

				$raw_hovor_casPrichodu = $raw_hovor->item($j)->getElementsByTagName('casPrichoduHovoru');
				$casPrichoduHovoru = $raw_hovor_casPrichodu->item(0)->nodeValue;
				$casPrichoduHovoru = strtotime($casPrichoduHovoru);

				$raw_hovor_casZvednuti = $raw_hovor->item($j)->getElementsByTagName('casZvednutiHovoru');
				$casZvednutiHovoru = $raw_hovor_casZvednuti->item(0)->nodeValue;
				$casZvednutiHovoru = strtotime($casZvednutiHovoru);

				$raw_hovor_smazat = $raw_hovor->item($j)->getElementsByTagName('smazat');
				$hovorSmazat = $raw_hovor_smazat->item(0)->nodeValue;

				$query81 = "INSERT INTO datove_vety (idDatovaVeta, datumVytvoreni, odesilatel, idUdalost, humanID, oznamovatel, idOznamovatel, volaneCislo, volajiciCislo, casPrichoduHovoru, casZvednutiHovoru, hovorSmazat) VALUES ('$idDatovaVeta', '$vytvoreno', '$odesilatel', '$idUdalost', '$human_id', '$oznamovatel', '$idOznamovatel', '$volaneCislo', '$volajiciCislo', '$casPrichoduHovoru', '$casZvednutiHovoru', '$hovorSmazat');";
				if (!mysqli_query($link,$query81)) {
					echo("Error description: " . mysqli_error($link)) . "<br/>";
				}
			}
		}

		$raw_soucinnost = $xml->getElementsByTagName('soucinnost');
		for ($i = 0; $i < $raw_soucinnost->length; $i++) {
			$raw_soucinnost_dispecinky = $raw_soucinnost->item($i)->getElementsByTagName('souciniciOr');
			$raw_soucinnost_dispecinky_kod = $raw_soucinnost_dispecinky->item(0)->getElementsByTagName('kod');
			$dispecink = $raw_soucinnost_dispecinky_kod->item(0)->nodeValue;
			$raw_soucinnost_stav = $raw_soucinnost->item($i)->getElementsByTagName('idStavOr');
			$stav = $raw_soucinnost_stav->item(0)->nodeValue;
			$raw_soucinnost_id = $raw_soucinnost->item($i)->getElementsByTagName('id');
			$soucinnost_id = $raw_soucinnost_id->item(0)->nodeValue;
			$raw_soucinnost_misto = $raw_soucinnost->item($i)->getElementsByTagName('idUrceniMistaT');
			$soucinnost_misto = $raw_soucinnost_misto->item(0)->nodeValue;

			$query99 = "INSERT INTO datove_vety (idDatovaVeta, datumVytvoreni, odesilatel, idUdalost, humanID, idSoucinnost, idSoucinnostMisto, dispecink, stav) VALUES ('$idDatovaVeta', '$vytvoreno', '$odesilatel', '$idUdalost', '$human_id','$soucinnost_id', '$soucinnost_misto', '$dispecink', '$stav');";
			if (!mysqli_query($link,$query99)) {
				echo("Error description: " . mysqli_error($link)) . "<br/>";
			}
		}

		$raw_poznamka = $xml->getElementsByTagName('poznamka');
		for ($i = 0; $i < $raw_poznamka->length; $i++) {
			$raw_poznamka_timestamp = $raw_poznamka->item($i)->getElementsByTagName('timestamp');
			$poznamka_timestamp = $raw_poznamka_timestamp->item(0)->nodeValue;
			$poznamka_timestamp = strtotime($poznamka_timestamp);

			$raw_poznamka_text = $raw_poznamka->item($i)->getElementsByTagName('text');
			$poznamka_text = $raw_poznamka_text->item(0)->nodeValue;

			$raw_poznamka_privatnost = $raw_poznamka->item($i)->getElementsByTagName('privatnost');
			$raw_poznamka_privatnost_kod = $raw_poznamka->item(0)->getElementsByTagName('kod');
			$poznamka_privatnost = $raw_poznamka_privatnost_kod->item(0)->nodeValue;

			$raw_poznamka_autorPoznamky = $raw_poznamka->item($i)->getElementsByTagName('autorPoznamky');
			$raw_poznamka_autor_or = $raw_poznamka_autorPoznamky->item(0)->getElementsByTagName('or');
			$raw_poznamka_autor_or_kod = $raw_poznamka_autor_or->item(0)->getElementsByTagName('kod');
			$poznamka_autor_kod = $raw_poznamka_autor_or_kod->item(0)->nodeValue;

			$raw_poznamka_autor_jmeno = $raw_poznamka_autorPoznamky->item(0)->getElementsByTagName('jmeno');
			$poznamka_autor_jmeno = $raw_poznamka_autor_jmeno->item(0)->nodeValue;

			$raw_poznamka_autor_prijmeni = $raw_poznamka_autorPoznamky->item(0)->getElementsByTagName('prijmeni');
			$poznamka_autor_prijmeni = $raw_poznamka_autor_prijmeni->item(0)->nodeValue;

			$poznamka_autor = $poznamka_autor_jmeno." ".$poznamka_autor_prijmeni;

			$query130 = "INSERT INTO datove_vety (idDatovaVeta, datumVytvoreni, odesilatel, idUdalost, humanID, poznamkaTimestamp, poznamkaText, poznamkaPrivatnost, poznamkaAutorOr, poznamkaAutor) VALUES ('$idDatovaVeta', '$vytvoreno', '$odesilatel', '$idUdalost', '$human_id','$poznamka_timestamp', '$poznamka_text', '$poznamka_privatnost', '$poznamka_autor_kod', '$poznamka_autor');";
			if (!mysqli_query($link,$query130)) {
				echo("Error description: " . mysqli_error($link)) . "<br/>";
			}
		}

		$raw_mistoUdalosti = $xml->getElementsByTagName('mistoUdalosti');
		for ($i = 0; $i < $raw_mistoUdalosti->length; $i++) {
			$raw_misto_id = $raw_mistoUdalosti->item($i)->getElementsByTagName('id');
			$idMisto = $raw_misto_id->item(0)->nodeValue;

			$raw_misto_idOznamovatel = $raw_mistoUdalosti->item($i)->getElementsByTagName('idOznamovatel');
			$mistoOznamovatel = $raw_misto_idOznamovatel->item(0)->nodeValue;

			$raw_misto_dopresneniMista = $raw_mistoUdalosti->item($i)->getElementsByTagName('dopresneniMista');
			$mistoDopresneni = $raw_misto_dopresneniMista->item(0)->nodeValue;

			$raw_misto_urcenoPro = $raw_mistoUdalosti->item($i)->getElementsByTagName('urcenoPro');
			$misto_urcenoPro = "";
			for ($j = 0; $j < $raw_misto_urcenoPro->length; $j++) {
				$raw_misto_urcenoPro_kod = $raw_misto_urcenoPro->item($j)->getElementsByTagName('kod');
				$mistoUrceno .= $raw_misto_urcenoPro_kod->item(0)->nodeValue."|";
			}

			$raw_misto_smazat = $raw_mistoUdalosti->item($i)->getElementsByTagName('smazat');
			$mistoSmazat = $raw_misto_smazat->item(0)->nodeValue;

			$query156 = "INSERT INTO datove_vety (idDatovaVeta, datumVytvoreni, odesilatel, idUdalost, idMisto, typMisto, mistoOznamovatel, mistoDopresneni, mistoUrceno, mistoSmazat) VALUES ('$idDatovaVeta', '$datumVytvoreni', '$odesilatel', '$idUdalost', '$idMisto', 'U', '$mistoOznamovatel', '$mistoDopresneni', '$mistoUrceno', '$mistoSmazat');";
			if (!mysqli_query($link,$query156)) {
				echo("Error description: " . mysqli_error($link)) . "<br/>";
			}

			$raw_misto_uzemi = $raw_mistoUdalosti->item($i)->getElementsByTagName('uzemi');
			$raw_misto_stat = $raw_misto_uzemi->item(0)->getElementsByTagName('stat');
			$misto_stat = $raw_misto_stat->item(0)->nodeValue;
			$raw_misto_kraj = $raw_misto_uzemi->item(0)->getElementsByTagName('kraj');
			$misto_kraj = $raw_misto_kraj->item(0)->nodeValue;
			$raw_misto_okres = $raw_misto_uzemi->item(0)->getElementsByTagName('okres');
			$misto_okres = $raw_misto_okres->item(0)->nodeValue;
			$raw_misto_obec = $raw_misto_uzemi->item(0)->getElementsByTagName('obec');
			$misto_obec = $raw_misto_obec->item(0)->nodeValue;
			$raw_misto_castObce = $raw_misto_uzemi->item(0)->getElementsByTagName('castObce');
			$misto_castObce = $raw_misto_castObce->item(0)->nodeValue;
			$raw_misto_zsjKod = $raw_misto_uzemi->item(0)->getElementsByTagName('zsjKod');
			$misto_zsjKod = $raw_misto_zsjKod->item(0)->nodeValue;
			$raw_misto_ulice = $raw_misto_uzemi->item(0)->getElementsByTagName('ulice');
			$misto_ulice = $raw_misto_ulice->item(0)->nodeValue;

			$query178 = "INSERT INTO mista (idMisto, stat, kraj, okres, obec, castObce, zsjKod, ulice) VALUES ('$idMisto','$misto_stat', '$misto_kraj', '$misto_okres', '$misto_obec', '$misto_castObce', '$misto_zsjKod', '$misto_ulice');";
			if (!mysqli_query($link,$query178)) {
				echo("Error description: " . mysqli_error($link)) . "<br/>";
			}

			$raw_misto_adresniMisto = $raw_mistoUdalosti->item($i)->getElementsByTagName('adresniMisto');
			$raw_misto_kodAdresy = $raw_misto_adresniMisto->item(0)->getElementsByTagName('kod');
			$misto_kodAdresy = $raw_misto_kodAdresy->item(0)->nodeValue;
			$raw_misto_kodObjektu = $raw_misto_adresniMisto->item(0)->getElementsByTagName('stavObjKod');
			$misto_kodObjektu = $raw_misto_kodObjektu->item(0)->nodeValue;
			$raw_misto_cisloDomovni = $raw_misto_adresniMisto->item(0)->getElementsByTagName('cisloDomovni');
			$misto_cisloDomovni = $raw_misto_cisloDomovni->item(0)->nodeValue;
			$raw_misto_cisloOrientacni = $raw_misto_adresniMisto->item(0)->getElementsByTagName('cisloOrientacni');
			$misto_cisloOrientacni = $raw_misto_cisloOrientacni->item(0)->nodeValue;
			$raw_misto_cisloOrientracniPismeno = $raw_misto_adresniMisto->item(0)->getElementsByTagName('cisloOrientacniPismeno');
			$misto_cisloOrientracniPismeno = $raw_misto_cisloOrientracniPismeno->item(0)->nodeValue;
			$raw_misto_typCislaKod = $raw_misto_adresniMisto->item(0)->getElementsByTagName('typCislaKod');
			$misto_typCislaKod = $raw_misto_typCislaKod->item(0)->nodeValue;
			$raw_misto_patro = $raw_misto_adresniMisto->item(0)->getElementsByTagName('patro');
			$misto_patro = $raw_misto_patro->item(0)->nodeValue;
			$raw_misto_cisloBytu = $raw_misto_adresniMisto->item(0)->getElementsByTagName('cisloBytu');
			$misto_cisloBytu = $raw_misto_cisloBytu->item(0)->nodeValue;
			$raw_misto_adresuSmazat = $raw_misto_adresniMisto->item(0)->getElementsByTagName('smazat');
			$misto_adresuSmazat = $raw_misto_adresuSmazat->item(0)->nodeValue;

			$query203 = "INSERT INTO mista (idMisto, kodAdresy, stavObjKod, cisloDomovni, cisloOrientacni, cisloOrientacniPismeno, typCislaKod, patro, cisloBytu, adresuSmazat) VALUES ('$idMisto', '$misto_kodAdresy', '$misto_kodObjektu', '$misto_cisloDomovni', '$misto_cisloOrientacni', '$misto_cisloOrientracniPismeno', '$misto_typCislaKod', '$misto_patro', '$misto_cisloBytu', '$misto_adresuSmazat');";
			if (!mysqli_query($link,$query203)) {
				echo("Error description: " . mysqli_error($link)) . "<br/>";
			}

			$raw_misto_polohaTop = $raw_mistoUdalosti->item($i)->getElementsByTagName('poloha');
			$raw_misto_poloha = $raw_misto_polohaTop->item(0)->getElementsByTagName('poloha');
			$raw_misto_polohaX = $raw_misto_poloha->item(0)->getElementsByTagName('x');
			$misto_polohaX = $raw_misto_polohaX->item(0)->nodeValue;
			$raw_misto_polohaY = $raw_misto_poloha->item(0)->getElementsByTagName('y');
			$misto_polohaY = $raw_misto_polohaY->item(0)->nodeValue;
			$raw_misto_polohaSRID = $raw_misto_poloha->item(0)->getElementsByTagName('srid');
			$misto_polohaSRID = $raw_misto_polohaSRID->item(0)->nodeValue;
			$raw_misto_urceniPolohy = $raw_misto_polohaTop->item(0)->getElementsByTagName('urceniPolohy');
			$misto_urceniPolohy = $raw_misto_urceniPolohy->item(0)->nodeValue;

			$query219 = "INSERT INTO mista (idMisto, polohaX, polohaY, polohaSRID, urceniPolohy) VALUES ('$idMisto', '$misto_polohaX', '$misto_polohaY', '$misto_polohaSRID', '$misto_urceniPolohy');";
			if (!mysqli_query($link,$query219)) {
				echo("Error description: " . mysqli_error($link)) . "<br/>";
			}

			$raw_misto_ZajmovyObjekt = $raw_mistoUdalosti->item($i)->getElementsByTagName('mistopisZajmovyObjekt');
			if ($raw_misto_ZajmovyObjekt->length > "0") {
				$raw_misto_objektID = $raw_misto_ZajmovyObjekt->item(0)->getElementsByTagName('id');
				$misto_objektID = $raw_misto_objektID->item(0)->nodeValue;
				$raw_misto_objektTyp = $raw_misto_ZajmovyObjekt->item(0)->getElementsByTagName('typZajmovyObjektKod');
				$misto_objektTyp = $raw_misto_objektTyp->item(0)->nodeValue;
				$raw_misto_objektNazev = $raw_misto_ZajmovyObjekt->item(0)->getElementsByTagName('typZajmovyObjektNazev');
				$misto_objektNazev = $raw_misto_objektNazev->item(0)->nodeValue;
				$raw_misto_objektSmazat = $raw_misto_ZajmovyObjekt->item(0)->getElementsByTagName('smazat');
				$misto_objektSmazat = $raw_misto_objektSmazat->item(0)->nodeValue;

				$query235 = "INSERT INTO mista (idMisto, objektID, objektTyp, objektNazev, objektSmazat) VALUES ('$idMisto', '$misto_objektID', '$misto_objektTyp', '$misto_objektNazev', '$misto_objektSmazat');";
				if (!mysqli_query($link,$query235)) {
					echo("Error description: " . mysqli_error($link)) . "<br/>";
				}
			}

			$raw_misto_LiniovySilnice = $raw_mistoUdalosti->item($i)->getElementsByTagName('mistopisLiniovySilnice');
			if ($raw_misto_LiniovySilnice->length > "0") {
				$raw_misto_silniceID = $raw_misto_LiniovySilnice->item(0)->getElementsByTagName('id');
				$misto_silniceID = $raw_misto_silniceID->item(0)->nodeValue;
				$raw_misto_silniceSmerID = $raw_misto_LiniovySilnice->item(0)->getElementsByTagName('smerId');
				$misto_silniceSmerID = $raw_misto_silniceSmerID->item(0)->nodeValue;
				$raw_misto_usekID = $raw_misto_LiniovySilnice->item(0)->getElementsByTagName('usekId');
				$misto_usekID = $raw_misto_usekID->item(0)->nodeValue;
				$raw_misto_usekName = $raw_misto_LiniovySilnice->item(0)->getElementsByTagName('usekName');
				$misto_usekName = $raw_misto_usekName->item(0)->nodeValue;
				$raw_misto_silniceKm = $raw_misto_LiniovySilnice->item(0)->getElementsByTagName('km');
				$misto_silniceKm = $raw_misto_silniceKm->item(0)->nodeValue;
				$raw_misto_silniceSmerName = $raw_misto_LiniovySilnice->item(0)->getElementsByTagName('smerName');
				$misto_silniceSmerName = $raw_misto_silniceSmerName->item(0)->nodeValue;
				$raw_misto_silniceSmazat = $raw_misto_LiniovySilnice->item(0)->getElementsByTagName('smazat');
				$misto_silniceSmazat = $raw_misto_silniceSmazat->item(0)->nodeValue;

				$query258 = "INSERT INTO mista (idMisto, silniceID, silniceSmerID, usekID, usekName, silniceKm, silniceSmerName, silniceSmazat) VALUES ('$idMisto', '$misto_silniceID', '$misto_silniceSmerID', '$misto_usekID', '$misto_usekName', '$misto_silniceKm', '$misto_silniceSmerName', '$misto_silniceSmazat');";
				if (!mysqli_query($link,$query258)) {
					echo("Error description: " . mysqli_error($link)) . "<br/>";
				}
			}

			$raw_misto_LiniovyZeleznice = $raw_mistoUdalosti->item($i)->getElementsByTagName('mistopisLiniovyZeleznice');
			if ($raw_misto_LiniovyZeleznice->length > "0") {
				$raw_misto_zelezniceID = $raw_misto_LiniovyZeleznice->item(0)->getElementsByTagName('id');
				$misto_zelezniceID = $raw_misto_zelezniceID->item(0)->nodeValue;
				$raw_misto_zelezniceSmerID = $raw_misto_LiniovyZeleznice->item(0)->getElementsByTagName('smerId');
				$misto_zelezniceSmerID = $raw_misto_zelezniceSmerID->item(0)->nodeValue;
				$raw_misto_zelezniceKm = $raw_misto_LiniovyZeleznice->item(0)->getElementsByTagName('km');
				$misto_zelezniceKm = $raw_misto_zelezniceKm->item(0)->nodeValue;
				$raw_misto_zelezniceSmerName = $raw_misto_LiniovyZeleznice->item(0)->getElementsByTagName('smerName');
				$misto_zelezniceSmerName = $raw_misto_zelezniceSmerName->item(0)->nodeValue;
				$raw_misto_zelezniceSmazat = $raw_misto_LiniovyZeleznice->item(0)->getElementsByTagName('smazat');
				$misto_zelezniceSmazat = $raw_misto_zelezniceSmazat->item(0)->nodeValue;

				$query277 = "INSERT INTO mista (idMisto, zelezniceID, zelezniceSmerID, zelezniceKm, zelezniceSmerName, zelezniceSmazat) VALUES ('$idMisto', '$misto_zelezniceID', '$misto_zelezniceSmerID', '$misto_zelezniceKm', '$misto_zelezniceSmerName', '$misto_zelezniceSmazat');";
				if (!mysqli_query($link,$query277)) {
					echo("Error description: " . mysqli_error($link)) . "<br/>";
				}
			}

			$raw_misto_LiniovyVodotec = $raw_mistoUdalosti->item($i)->getElementsByTagName('mistopisLiniovyVodotec');
			if ($raw_misto_LiniovyVodotec->length > "0") {
				$raw_misto_vodotecKm = $raw_misto_LiniovyVodotec->item(0)->getElementsByTagName('km');
				$misto_vodotecKm = $raw_misto_vodotecKm->item(0)->nodeValue;
				$raw_misto_vodotecID = $raw_misto_LiniovyVodotec->item(0)->getElementsByTagName('id');
				$misto_vodotecID = $raw_misto_vodotecID->item(0)->nodeValue;
				$raw_misto_vodotecBreh = $raw_misto_LiniovyVodotec->item(0)->getElementsByTagName('breh');
				$misto_vodotecBreh = $raw_misto_vodotecBreh->item(0)->nodeValue;
				$raw_misto_vodotecSmazat = $raw_misto_LiniovyVodotec->item(0)->getElementsByTagName('smazat');
				$misto_vodotecSmazat = $raw_misto_vodotecSmazat->item(0)->nodeValue;

				$query294 = "INSERT INTO mista (idMisto, vodotecID, vodotecBreh, vodotecKm, vodotecSmazat) VALUES ('$idMisto', '$misto_vodotecID', '$misto_vodotecBreh', '$misto_vodotecKm', '$misto_vodotecSmazat');";
				if (!mysqli_query($link,$query294)) {
					echo("Error description: " . mysqli_error($link)) . "<br/>";
				}
			}
		}

		$raw_mistoOznameni = $xml->getElementsByTagName('mistoOznameni');
		for ($i = 0; $i < $raw_mistoOznameni->length; $i++) {
			$raw_misto_id = $raw_mistoOznameni->item($i)->getElementsByTagName('id');
			$idMisto = $raw_misto_id->item(0)->nodeValue;

			$raw_misto_idOznamovatel = $raw_mistoOznameni->item($i)->getElementsByTagName('idOznamovatel');
			$mistoOznamovatel = $raw_misto_idOznamovatel->item(0)->nodeValue;

			$raw_misto_dopresneniMista = $raw_mistoOznameni->item($i)->getElementsByTagName('dopresneniMista');
			$mistoDopresneni = $raw_misto_dopresneniMista->item(0)->nodeValue;

			$raw_misto_urcenoPro = $raw_mistoOznameni->item($i)->getElementsByTagName('urcenoPro');
			$misto_urcenoPro = "";
			for ($j = 0; $j < $raw_misto_urcenoPro->length; $j++) {
				$raw_misto_urcenoPro_kod = $raw_misto_urcenoPro->item($j)->getElementsByTagName('kod');
				$mistoUrceno .= $raw_misto_urcenoPro_kod->item(0)->nodeValue."|";
			}

			$raw_misto_smazat = $raw_mistoOznameni->item($i)->getElementsByTagName('smazat');
			$mistoSmazat = $raw_misto_smazat->item(0)->nodeValue;

			$query322 = "INSERT INTO datove_vety (idDatovaVeta, datumVytvoreni, odesilatel, idUdalost, idMisto, typMisto, mistoOznamovatel, mistoDopresneni, mistoUrceno, mistoSmazat) VALUES ('$idDatovaVeta', '$datumVytvoreni', '$odesilatel', '$idUdalost', '$idMisto', 'O', '$mistoOznamovatel', '$mistoDopresneni', '$mistoUrceno', '$mistoSmazat');";
			if (!mysqli_query($link,$query322)) {
				echo("Error description: " . mysqli_error($link)) . "<br/>";
			}

			$raw_misto_uzemi = $raw_mistoOznameni->item($i)->getElementsByTagName('uzemi');
			$raw_misto_stat = $raw_misto_uzemi->item(0)->getElementsByTagName('stat');
			$misto_stat = $raw_misto_stat->item(0)->nodeValue;
			$raw_misto_kraj = $raw_misto_uzemi->item(0)->getElementsByTagName('kraj');
			$misto_kraj = $raw_misto_kraj->item(0)->nodeValue;
			$raw_misto_okres = $raw_misto_uzemi->item(0)->getElementsByTagName('okres');
			$misto_okres = $raw_misto_okres->item(0)->nodeValue;
			$raw_misto_obec = $raw_misto_uzemi->item(0)->getElementsByTagName('obec');
			$misto_obec = $raw_misto_obec->item(0)->nodeValue;
			$raw_misto_castObce = $raw_misto_uzemi->item(0)->getElementsByTagName('castObce');
			$misto_castObce = $raw_misto_castObce->item(0)->nodeValue;
			$raw_misto_zsjKod = $raw_misto_uzemi->item(0)->getElementsByTagName('zsjKod');
			$misto_zsjKod = $raw_misto_zsjKod->item(0)->nodeValue;
			$raw_misto_ulice = $raw_misto_uzemi->item(0)->getElementsByTagName('ulice');
			$misto_ulice = $raw_misto_ulice->item(0)->nodeValue;

			$query343 = "INSERT INTO mista (idMisto, stat, kraj, okres, obec, castObce, zsjKod, ulice) VALUES ('$idMisto','$misto_stat', '$misto_kraj', '$misto_okres', '$misto_obec', '$misto_castObce', '$misto_zsjKod', '$misto_ulice');";
			if (!mysqli_query($link,$query343)) {
				echo("Error description: " . mysqli_error($link)) . "<br/>";
			}

			$raw_misto_adresniMisto = $raw_mistoOznameni->item($i)->getElementsByTagName('adresniMisto');
			$raw_misto_kodAdresy = $raw_misto_adresniMisto->item(0)->getElementsByTagName('kod');
			$misto_kodAdresy = $raw_misto_kodAdresy->item(0)->nodeValue;
			$raw_misto_kodObjektu = $raw_misto_adresniMisto->item(0)->getElementsByTagName('stavObjKod');
			$misto_kodObjektu = $raw_misto_kodObjektu->item(0)->nodeValue;
			$raw_misto_cisloDomovni = $raw_misto_adresniMisto->item(0)->getElementsByTagName('cisloDomovni');
			$misto_cisloDomovni = $raw_misto_cisloDomovni->item(0)->nodeValue;
			$raw_misto_cisloOrientacni = $raw_misto_adresniMisto->item(0)->getElementsByTagName('cisloOrientacni');
			$misto_cisloOrientacni = $raw_misto_cisloOrientacni->item(0)->nodeValue;
			$raw_misto_cisloOrientracniPismeno = $raw_misto_adresniMisto->item(0)->getElementsByTagName('cisloOrientacniPismeno');
			$misto_cisloOrientracniPismeno = $raw_misto_cisloOrientracniPismeno->item(0)->nodeValue;
			$raw_misto_typCislaKod = $raw_misto_adresniMisto->item(0)->getElementsByTagName('typCislaKod');
			$misto_typCislaKod = $raw_misto_typCislaKod->item(0)->nodeValue;
			$raw_misto_patro = $raw_misto_adresniMisto->item(0)->getElementsByTagName('patro');
			$misto_patro = $raw_misto_patro->item(0)->nodeValue;
			$raw_misto_cisloBytu = $raw_misto_adresniMisto->item(0)->getElementsByTagName('cisloBytu');
			$misto_cisloBytu = $raw_misto_cisloBytu->item(0)->nodeValue;
			$raw_misto_adresuSmazat = $raw_misto_adresniMisto->item(0)->getElementsByTagName('smazat');
			$misto_adresuSmazat = $raw_misto_adresuSmazat->item(0)->nodeValue;

			$query368 = "INSERT INTO mista (idMisto, kodAdresy, stavObjKod, cisloDomovni, cisloOrientacni, cisloOrientacniPismeno, typCislaKod, patro, cisloBytu, adresuSmazat) VALUES ('$idMisto', '$misto_kodAdresy', '$misto_kodObjektu', '$misto_cisloDomovni', '$misto_cisloOrientacni', '$misto_cisloOrientracniPismeno', '$misto_typCislaKod', '$misto_patro', '$misto_cisloBytu', '$misto_adresuSmazat');";
			if (!mysqli_query($link,$query351)) {
				echo("Error description: " . mysqli_error($link)) . "<br/>";
			}

			$raw_misto_polohaTop = $raw_mistoOznameni->item($i)->getElementsByTagName('poloha');
			$raw_misto_poloha = $raw_misto_polohaTop->item(0)->getElementsByTagName('poloha');
			$raw_misto_polohaX = $raw_misto_poloha->item(0)->getElementsByTagName('x');
			$misto_polohaX = $raw_misto_polohaX->item(0)->nodeValue;
			$raw_misto_polohaY = $raw_misto_poloha->item(0)->getElementsByTagName('y');
			$misto_polohaY = $raw_misto_polohaY->item(0)->nodeValue;
			$raw_misto_polohaSRID = $raw_misto_poloha->item(0)->getElementsByTagName('srid');
			$misto_polohaSRID = $raw_misto_polohaSRID->item(0)->nodeValue;
			$raw_misto_urceniPolohy = $raw_misto_polohaTop->item(0)->getElementsByTagName('urceniPolohy');
			$misto_urceniPolohy = $raw_misto_urceniPolohy->item(0)->nodeValue;

			$query384 = "INSERT INTO mista (idMisto, polohaX, polohaY, polohaSRID, urceniPolohy) VALUES ('$idMisto', '$misto_polohaX', '$misto_polohaY', '$misto_polohaSRID', '$misto_urceniPolohy');";
			if (!mysqli_query($link,$query384)) {
				echo("Error description: " . mysqli_error($link)) . "<br/>";
			}

			$raw_misto_ZajmovyObjekt = $raw_mistoOznameni->item($i)->getElementsByTagName('mistopisZajmovyObjekt');
			if ($raw_misto_ZajmovyObjekt->length > "0") {
				$raw_misto_objektID = $raw_misto_ZajmovyObjekt->item(0)->getElementsByTagName('id');
				$misto_objektID = $raw_misto_objektID->item(0)->nodeValue;
				$raw_misto_objektTyp = $raw_misto_ZajmovyObjekt->item(0)->getElementsByTagName('typZajmovyObjektKod');
				$misto_objektTyp = $raw_misto_objektTyp->item(0)->nodeValue;
				$raw_misto_objektNazev = $raw_misto_ZajmovyObjekt->item(0)->getElementsByTagName('typZajmovyObjektNazev');
				$misto_objektNazev = $raw_misto_objektNazev->item(0)->nodeValue;
				$raw_misto_objektSmazat = $raw_misto_ZajmovyObjekt->item(0)->getElementsByTagName('smazat');
				$misto_objektSmazat = $raw_misto_objektSmazat->item(0)->nodeValue;

				$query400 = "INSERT INTO mista (idMisto, objektID, objektTyp, objektNazev, objektSmazat) VALUES ('$idMisto', '$misto_objektID', '$misto_objektTyp', '$misto_objektNazev', '$misto_objektSmazat');";
				if (!mysqli_query($link,$query400)) {
					echo("Error description: " . mysqli_error($link)) . "<br/>";
				}
			}

			$raw_misto_LiniovySilnice = $raw_mistoOznameni->item($i)->getElementsByTagName('mistopisLiniovySilnice');
			if ($raw_misto_LiniovySilnice->length > "0") {
				$raw_misto_silniceID = $raw_misto_LiniovySilnice->item(0)->getElementsByTagName('id');
				$misto_silniceID = $raw_misto_silniceID->item(0)->nodeValue;
				$raw_misto_silniceSmerID = $raw_misto_LiniovySilnice->item(0)->getElementsByTagName('smerId');
				$misto_silniceSmerID = $raw_misto_silniceSmerID->item(0)->nodeValue;
				$raw_misto_usekID = $raw_misto_LiniovySilnice->item(0)->getElementsByTagName('usekId');
				$misto_usekID = $raw_misto_usekID->item(0)->nodeValue;
				$raw_misto_usekName = $raw_misto_LiniovySilnice->item(0)->getElementsByTagName('usekName');
				$misto_usekName = $raw_misto_usekName->item(0)->nodeValue;
				$raw_misto_silniceKm = $raw_misto_LiniovySilnice->item(0)->getElementsByTagName('km');
				$misto_silniceKm = $raw_misto_silniceKm->item(0)->nodeValue;
				$raw_misto_silniceSmerName = $raw_misto_LiniovySilnice->item(0)->getElementsByTagName('smerName');
				$misto_silniceSmerName = $raw_misto_silniceSmerName->item(0)->nodeValue;
				$raw_misto_silniceSmazat = $raw_misto_LiniovySilnice->item(0)->getElementsByTagName('smazat');
				$misto_silniceSmazat = $raw_misto_silniceSmazat->item(0)->nodeValue;

				$query423 = "INSERT INTO mista (idMisto, silniceID, silniceSmerID, usekID, usekName, silniceKm, silniceSmerName, silniceSmazat) VALUES ('$idMisto', '$misto_silniceID', '$misto_silniceSmerID', '$misto_usekID', '$misto_usekName', '$misto_silniceKm', '$misto_silniceSmerName', '$misto_silniceSmazat');";
				if (!mysqli_query($link,$query423)) {
					echo("Error description: " . mysqli_error($link)) . "<br/>";
				}
			}

			$raw_misto_LiniovyZeleznice = $raw_mistoOznameni->item($i)->getElementsByTagName('mistopisLiniovyZeleznice');
			if ($raw_misto_LiniovyZeleznice->length > "0") {
				$raw_misto_zelezniceID = $raw_misto_LiniovyZeleznice->item(0)->getElementsByTagName('id');
				$misto_zelezniceID = $raw_misto_zelezniceID->item(0)->nodeValue;
				$raw_misto_zelezniceSmerID = $raw_misto_LiniovyZeleznice->item(0)->getElementsByTagName('smerId');
				$misto_zelezniceSmerID = $raw_misto_zelezniceSmerID->item(0)->nodeValue;
				$raw_misto_zelezniceKm = $raw_misto_LiniovyZeleznice->item(0)->getElementsByTagName('km');
				$misto_zelezniceKm = $raw_misto_zelezniceKm->item(0)->nodeValue;
				$raw_misto_zelezniceSmerName = $raw_misto_LiniovyZeleznice->item(0)->getElementsByTagName('smerName');
				$misto_zelezniceSmerName = $raw_misto_zelezniceSmerName->item(0)->nodeValue;
				$raw_misto_zelezniceSmazat = $raw_misto_LiniovyZeleznice->item(0)->getElementsByTagName('smazat');
				$misto_zelezniceSmazat = $raw_misto_zelezniceSmazat->item(0)->nodeValue;

				$query442 = "INSERT INTO mista (idMisto, zelezniceID, zelezniceSmerID, zelezniceKm, zelezniceSmerName, zelezniceSmazat) VALUES ('$idMisto', '$misto_zelezniceID', '$misto_zelezniceSmerID', '$misto_zelezniceKm', '$misto_zelezniceSmerName', '$misto_zelezniceSmazat');";
				if (!mysqli_query($link,$query442)) {
					echo("Error description: " . mysqli_error($link)) . "<br/>";
				}
			}

			$raw_misto_LiniovyVodotec = $raw_mistoUdalosti->item($i)->getElementsByTagName('mistopisLiniovyVodotec');
			if ($raw_misto_LiniovyVodotec->length > "0") {
				$raw_misto_vodotecKm = $raw_misto_LiniovyVodotec->item(0)->getElementsByTagName('km');
				$misto_vodotecKm = $raw_misto_vodotecKm->item(0)->nodeValue;
				$raw_misto_vodotecID = $raw_misto_LiniovyVodotec->item(0)->getElementsByTagName('id');
				$misto_vodotecID = $raw_misto_vodotecID->item(0)->nodeValue;
				$raw_misto_vodotecBreh = $raw_misto_LiniovyVodotec->item(0)->getElementsByTagName('breh');
				$misto_vodotecBreh = $raw_misto_vodotecBreh->item(0)->nodeValue;
				$raw_misto_vodotecSmazat = $raw_misto_LiniovyVodotec->item(0)->getElementsByTagName('smazat');
				$misto_vodotecSmazat = $raw_misto_vodotecSmazat->item(0)->nodeValue;

				$query459 = "INSERT INTO mista (idMisto, vodotecID, vodotecBreh, vodotecKm, vodotecSmazat) VALUES ('$idMisto', '$misto_vodotecID', '$misto_vodotecBreh', '$misto_vodotecKm', '$misto_vodotecSmazat');";
				if (!mysqli_query($link,$query459)) {
					echo("Error description: " . mysqli_error($link)) . "<br/>";
				}
			}
		}

		$raw_typUdalosti = $xml->getElementsByTagName('typUdalosti');
		$typUdalosti = $raw_typUdalosti->item(0)->nodeValue;

		$raw_podtyp = $xml->getElementsByTagName('podTypUdalosti');
		for ($i = 0; $i < $raw_podtyp->length; $i++) {
			$raw_podtyp_kod = $raw_podtyp->item($i)->getElementsByTagName('kod');
			$podtypUdalosti = $raw_podtyp_kod->item(0)->nodeValue;

			$raw_podtyp_slozka = $raw_podtyp->item($i)->getElementsByTagName('slozka');
			$raw_podtyp_slozka_kod = $raw_podtyp_slozka->item(0)->getElementsByTagName('kod');
			$podtyp_slozka_kod = $raw_podtyp_slozka_kod->item(0)->nodeValue;

			$raw_podtyp_smazat = $raw_podtyp->item($i)->getElementsByTagName('smazat');
			$podtyp_smazat = $raw_podtyp_smazat->item(0)->nodeValue;
		}

		$raw_datumUdalosti = $xml->getElementsByTagName('datumUdalosti');
		$datumUdalosti = $raw_datumUdalosti->item(0)->nodeValue;
		$datumUdalosti = strtotime($datumUdalosti);

		$raw_popis = $xml->getElementsByTagName('popis');
		$popis = $raw_popis->item(0)->nodeValue;

		$raw_nalehavostUdalosti = $xml->getElementsByTagName('nalehavostUdalosti');
		$nalehavostUdalosti = $raw_nalehavostUdalosti->item(0)->nodeValue; 

		$raw_stupenPoplachuUdalosti = $xml->getElementsByTagName('stupenPoplachuUdalosti');
		$stupenPoplachuUdalosti = $raw_stupenPoplachuUdalosti->item(0)->nodeValue;

		$raw_nutnostVyprosteni = $xml->getElementsByTagName('nutnostVyprosteni');
		$nutnostVyprosteni = $raw_nutnostVyprosteni->item(0)->nodeValue;

		$query498 = "INSERT INTO datove_vety (idDatovaVeta, datumVytvoreni, odesilatel, idUdalost, humanID, typUdalosti, podtypUdalosti, datumUdalosti, popis, nalehavostUdalosti, stupenPoplachuUdalosti, nutnostVyprosteni) VALUES ('$idDatovaVeta', '$vytvoreno', '$odesilatel', '$idUdalost', '$human_id','$typUdalosti', '$podtypUdalosti', '$datumUdalosti', '$popis', '$nalehavostUdalosti', '$stupenPoplachuUdalosti', '$nutnostVyprosteni');";
		if (!mysqli_query($link,$query498)) {
			echo("Error description: " . mysqli_error($link)) . "<br/>";
		}

		$vars = array_keys(get_defined_vars());
		$k = 0;
		foreach ($vars as $var) {
			if ($k > 23) {
				unset($$var);
			}
			$k ++;
		}
		unset ($vars, $k);
	}
}

mysqli_close($link);
pg_close($pg_link);

echo "Hotovo $datum";
//echo "<meta http-equiv=\"refresh\" content=\"5; url=scrape.php?den=$den\">";
?>
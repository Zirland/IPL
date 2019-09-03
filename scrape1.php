<?php
ini_set ('max_execution_time', 0);

$pg_link = pg_connect("host=10.30.221.241 port=5432 dbname=nisizs_ipl user=jurbanek password=jupassword");
$link = mysqli_connect('localhost', 'root', 'root', 'NIS');

/*$query5 = "select id, human_id, popis, datum_vzniku_udalosti, typ_udalosti, soucinnost_hzs, soucinnost_zzs, soucinnost_pcr from tdn_udalost where (datum_vzniku_udalosti at time zone 'utc')::date = '2018-08-31' order by datum_vzniku_udalosti;";
if ($result5 = pg_query($pg_link, $query5)) {
	while ($row5 = pg_fetch_row($result5)) {
		$id = $row5[0];
		$human_id = $row5[1];
		$popis = $row5[2];
		$datum_vzniku_udalosti = $row5[3];
		$typ_udalosti = $row5[4];
		$soucinnost_hzs = $row5[5];
		$soucinnost_zzs = $row5[6];
		$soucinnost_pcr = $row5[7];

		$vznik_udalosti = strtotime($datum_vzniku_udalosti);

		$query17 = "INSERT INTO udalosti (id, human_id, popis, datum_vzniku, typ_udalosti, HZS, ZZS, PCR) VALUES ('$id', '$human_id', '$popis', '$vznik_udalosti', '$typ_udalosti', '$soucinnost_hzs', '$soucinnost_zzs', '$soucinnost_pcr');";
		echo "$query17<br/>";
		$prikaz17 = mysqli_query($link, $query17);
	}
}
*/

$query500 = "select zdroj from tpart_historie_dv_18_10 where id_udalost in (select id from tdn_udalost where (datum_vzniku_udalosti at time zone 'utc')::date = '2018-10-01' order by datum_vzniku_udalosti) and typ_dv = 'DVUD' and prichozi=true order by datum_vytvoreni;";
if ($result500 = pg_query($pg_link, $query500)) {
	while ($row500 = pg_fetch_row($result500)) {
		$zdroj = $row500[0];

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

				$query99 = "INSERT INTO datove_vety (idDatovaVeta, datumVytvoreni, odesilatel, idUdalost, oznamovatel, idOznamovatel, volaneCislo, volajiciCislo, casPrichoduHovoru, casZvednutiHovoru, hovorSmazat) VALUES ('$idDatovaVeta', '$datumVytvoreni', '$odesilatel', '$idUdalost', '$oznamovatel', '$idOznamovatel', '$volaneCislo', '$volajiciCislo', '$casPrichoduHovoru', '$casZvednutiHovoru', '$hovorSmazat');";
//				echo "$query99<br/>";
				$prikaz99 = mysqli_query($link, $query99);
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

			$query118 = "INSERT INTO datove_vety (idDatovaVeta, datumVytvoreni, odesilatel, idUdalost, idSoucinnost, idSoucinnostMisto, dispecink, stav) VALUES ('$idDatovaVeta', '$datumVytvoreni', '$odesilatel', '$idUdalost', '$soucinnost_id', '$soucinnost_misto', '$dispecink', '$stav');";
//			echo "$query118<br/>";
			$prikaz118 = mysqli_query($link, $query118);
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

			$query149 = "INSERT INTO datove_vety (idDatovaVeta, datumVytvoreni, odesilatel, idUdalost, poznamkaTimestamp, poznamkaText, poznamkaPrivatnost, poznamkaAutorOr, poznamkaAutor) VALUES ('$idDatovaVeta', '$datumVytvoreni', '$odesilatel', '$idUdalost', '$poznamka_timestamp', '$poznamka_text', '$poznamka_privatnost', '$poznamka_autor_kod', '$poznamka_autor');";
//			echo "$query149<br/>";
			$prikaz149 = mysqli_query($link, $query149);

		}

/*		$raw_mistoUdalosti = $xml->getElementsByTagName('mistoUdalosti');
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

			$query160 = "INSERT INTO datove_vety (idDatovaVeta, datumVytvoreni, odesilatel, idUdalost, idMisto, typMisto, mistoOznamovatel, mistoDopresneni, mistoUrceno, mistoSmazat) VALUES ('$idDatovaVeta', '$datumVytvoreni', '$odesilatel', '$idUdalost', '$idMisto', 'U', '$mistoOznamovatel', '$mistoDopresneni', '$mistoUrceno', '$mistoSmazat');";
//			echo "$query160<br/>";
//			prikaz160 = $mysqli_query($link, $query160);
		}
/*
		mistoUdalosti

			stat
			kraj
			okres
			obec
			castObce
			zsjKod
			ulice

			adresnimisto\kod
			cisloDomovni
			typCislaKod

			poloha\x
			poloha\y
			poloha\srid
			urceniPolohy

			mistopisZajmovyObjekt\id
			typZajmovyObjektKod

			MistopisLiniovySilnice
				id
				smerId
				km


			MistopisLiniovyZeleznice
				id
				smerId
				km*/


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

		$query191 = "INSERT INTO datove_vety (idDatovaVeta, datumVytvoreni, odesilatel, idUdalost, typUdalosti, podtypUdalosti, datumUdalosti, popis, nalehavostUdalosti, stupenPoplachuUdalosti, nutnostVyprosteni) VALUES ('$idDatovaVeta', '$datumVytvoreni', '$odesilatel', '$idUdalost', '$typUdalosti', '$podtypUdalosti', '$datumUdalosti', '$popis', '$nalehavostUdalosti', '$stupenPoplachuUdalosti', '$nutnostVyprosteni');";
//		echo "$query191<br/>";
		$prikaz191 = mysqli_query($link, $query191);

		$vars = array_keys(get_defined_vars());
//		print_r($vars);
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

echo "Hotovo $den";
$den = $den+1;
//echo "<meta http-equiv=\"refresh\" content=\"5; url=scrape.php?den=$den\">";
?>
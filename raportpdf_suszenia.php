<?php
function filtruj($zmienna) {
	$data = trim($zmienna);
	//usuwa spacje, tagi
	$data = stripslashes($zmienna);
	//usuwa slashe
	$data = htmlspecialchars($zmienna);
	//zamienia tagi html na czytelne znaki aby w formularzu nie wpisać szkodliwego kodu
	return $zmienna;
}

if (isset($_POST['pdf'])) {

	/*Odbieramy dane z sesji*/
	$data_raportu = $_SESSION["data_raportu"];
	$kolejny_dzien = date('Y-m-d', strtotime($data_raportu . ' +1 day'));
	$asortyment_suszu = $_SESSION["asortyment_suszu"];
	$nr_suszarni = $_SESSION["nr_suszarni"];
}

if ($_POST['pdf2']) {

	/*Odbieramy dane z formularza*/
	$data_raportu = filtruj($_POST['data_raportu']);
	$kolejny_dzien = date('Y-m-d', strtotime($data_raportu . ' +1 day'));
	$ostatni_raport = filtruj($_POST['ostatni_raport']);
	$asortyment_suszu = filtruj($_POST['asortyment_suszu']);
	$nr_suszarni = filtruj($_POST['nr_suszarni']);
}

if ($_POST['wyslij']) {

	/*Odbieramy dane z formularza*/
	$data_raportu = filtruj($_POST['data_raportu']);
	$kolejny_dzien = date('Y-m-d', strtotime($data_raportu . ' +1 day'));
	$ostatni_raport = filtruj($_POST['ostatni_raport']);
	$asortyment_suszu = filtruj($_POST['asortyment_suszu']);
	$nr_suszarni = filtruj($_POST['nr_suszarni']);
	$email = filtruj($_POST['email']);
}

if (isset($_POST['pdf']) || isset($_POST['pdf2']) || isset($_POST['wyslij'])) {
	/* ��czymy si� z serwerem */
	require_once ('polaczenie_z_baza.php');

	/*Zmieniamy kodowanie znak�w z UTF-8 na Windows-1250 poniewa� klasa fpdf nie wspiera UTF-8
	co powoduje �e tekst z bazy danych nie pokazuje polskich znak�w */
	$asortyment_suszu2 = iconv('UTF-8', 'windows-1250', $asortyment_suszu);
	
	/*Przygotowujemy znak stopni celsjusza
	$stopien_kod=&#176;
	$stopien=iconv('UTF-8', 'windows-1252', html_entity_decode($stopien_kod));
	*/
	
	$raport = "";

	if ($ostatni_raport == "Ostatni_raport") {
		$rok = date("Y");
		if ($stmt = $mysqli -> prepare("SELECT MAX(Data) FROM `" . $asortyment_suszu . "` WHERE Data LIKE '%" . $rok . "%' AND Czas >=  STR_TO_DATE('08:00:00','%h:%i:%s') AND NrSuszarni=?")) {
			$stmt -> bind_param("s",$nr_suszarni);
			$stmt -> execute();
			$stmt -> bind_result($Max_data_raportu);
			$stmt -> store_result();
			if ($stmt -> fetch()) {
				$data_raportu = $Max_data_raportu;
				$kolejny_dzien = date('Y-m-d', strtotime($data_raportu . ' +1 day'));
			}
		}
	}

	/*ob_end_clean();
	 ini_set('display_errors', 1);
	 ini_set('display_startup_errors', 1);
	 error_reporting(E_ALL);*/

	require ('fpdf/fpdf.php');

	$pdf = new FPDF('L');
	$pdf -> SetMargins(25.4, 25.4, 25.4, 25.4);
	$pdf -> AliasNbPages();
	$pdf -> AddPage();

	$pdf -> AddFont('arial_ce', '', 'arial_ce.php');
	$pdf -> AddFont('arial_ce', 'I', 'arial_ce_i.php');
	$pdf -> AddFont('arial_ce', 'B', 'arial_ce_b.php');
	$pdf -> AddFont('arial_ce', 'BI', 'arial_ce_bi.php');

	$pdf -> SetFont('arial_ce', 'B', 16);
	$tekst = "Raport z procesu suszenia";
	iconv('UTF-8', 'iso-8859-2//TRANSLIT//IGNORE', $tekst);
	$pdf -> SetXY(50, 24);
	$pdf -> Cell(40, 10, "$tekst", '', 'C');
	$pdf -> Ln(30);

	/*Dane raportu*/
	$pdf -> SetFont('arial_ce', 'B', 10);
	$pdf -> SetXY(10, 40);
	$pdf -> Cell(15, 5, "Asortyment: $asortyment_suszu2");
	$pdf -> SetXY(80, 40);
	$pdf -> Cell(15, 5, "Data: $data_raportu - $kolejny_dzien");
	$pdf -> SetXY(160, 40);
	$pdf -> Cell(15, 5, "Nr suszarni: $nr_suszarni");

	$pdf -> SetFont('arial_ce', 'B', 9);
	$pdf -> SetXY(10, 50);
	$pdf -> Multicell(45, 5, "Godzina\nPr�dko�� Blanszownika\nTemperatura Blanszownika\nPr�dko�� Siatki nr 7\nPr�dko�� Siatki nr 6\nPr�dko�� Siatki nr 5\nPr�dko�� Siatki nr 4\nPr�dko�� Siatki nr 3\nPr�dko�� Siatki nr 2\nPr�dko�� Siatki nr 1\nCzas Suszenia\nTemperatura G�ra\nTemperatura D�\nWilgotno��\nOsoba odpowiedzialna.");
	$pdf -> SetX(55);

	//usuwamy specjalne znaki takie jak '," aby nie możnabyło wpisać ich z formularza do zapytania SQL
	$asortyment_suszu = $mysqli -> real_escape_string($asortyment_suszu);
	$data_raportu = $mysqli -> real_escape_string($data_raportu);
	$kolejny_dzien = $mysqli -> real_escape_string($kolejny_dzien);
	$nr_suszarni = $mysqli -> real_escape_string($nr_suszarni);

	/* Utworzenie zapytania*/
	$query = "SELECT Data,Czas,PredkoscBlanszownika,TemperaturaBlanszownika,PredkoscSiatkiNr7,PredkoscSiatkiNr6,PredkoscSiatkiNr5,PredkoscSiatkiNr4,PredkoscSiatkiNr3,PredkoscSiatkiNr2,PredkoscSiatkiNr1,TemperaturaGora,TemperaturaDol,CzasSuszenia,Wilgotnosc,WykonawcaPomiaru FROM `" . $asortyment_suszu . "`  WHERE Data='" . $data_raportu . "' AND Czas >=  STR_TO_DATE('08:00:00','%h:%i:%s') AND NrSuszarni='" . $nr_suszarni . "'
	 UNION ALL
	 SELECT Data,Czas,PredkoscBlanszownika,TemperaturaBlanszownika,PredkoscSiatkiNr7,PredkoscSiatkiNr6,PredkoscSiatkiNr5,PredkoscSiatkiNr4,PredkoscSiatkiNr3,PredkoscSiatkiNr2,PredkoscSiatkiNr1,TemperaturaGora,TemperaturaDol,CzasSuszenia,Wilgotnosc,WykonawcaPomiaru FROM `" . $asortyment_suszu . "` WHERE Data='" . $kolejny_dzien . "' AND Czas <=  STR_TO_DATE('06:00:00','%h:%i:%s') AND NrSuszarni='" . $nr_suszarni . "' ORDER BY Data, Czas ASC";

	/*Przesłanie zapytania do bazy*/
	$result = $mysqli -> query($query);
	/* Przetwarzanie wierszy wyniku zapytania */
	$num_rows = mysqli_num_rows($result);
	if ($num_rows > 0) {
		$raport = TRUE;
		while ($row = $result -> fetch_object()) {

			$Czas = $row -> Czas;
			$Czas = substr($Czas, 0, 5);
			$Predkosc_Blanszownika = $row -> PredkoscBlanszownika;
			$Temperatura_Blanszownika = $row -> TemperaturaBlanszownika;
			$V_Siatka7 = $row -> PredkoscSiatkiNr7;
			$V_Siatka6 = $row -> PredkoscSiatkiNr6;
			$V_Siatka5 = $row -> PredkoscSiatkiNr5;
			$V_Siatka4 = $row -> PredkoscSiatkiNr4;
			$V_Siatka3 = $row -> PredkoscSiatkiNr3;
			$V_Siatka2 = $row -> PredkoscSiatkiNr2;
			$V_Siatka1 = $row -> PredkoscSiatkiNr1;
			$Czas_Suszenia = $row -> CzasSuszenia;
			$Temp_Gorna = $row -> TemperaturaGora;
			$Temp_Dolna = $row -> TemperaturaDol;
			$Wilgotnosc = $row -> Wilgotnosc;
			$Odpowiedzialny = $row -> WykonawcaPomiaru;

			/*Wyświetlenie/Wypisanie wyników z bazy danych*/
			$pdf -> SetFont('arial_ce', '', 9);
			$x = $pdf -> GetX();
			$x1 = $x + 2;
			$pdf -> SetXY($x1, 50);
			$pdf -> Cell(15, 5, "$Czas");
			$pdf -> SetXY($x1, 55);
			$pdf -> Cell(15, 5, "$Predkosc_Blanszownika Hz");
			$pdf -> SetXY($x1, 60);
			$pdf -> Cell(15, 5, "$Temperatura_Blanszownika  *C");
			$pdf -> SetXY($x1, 65);
			$pdf -> Cell(15, 5, "$V_Siatka7 Hz");
			$pdf -> SetXY($x1, 70);
			$pdf -> Cell(15, 5, "$V_Siatka6 Hz");
			$pdf -> SetXY($x1, 75);
			$pdf -> Cell(15, 5, "$V_Siatka5 Hz");
			$pdf -> SetXY($x1, 80);
			$pdf -> Cell(15, 5, "$V_Siatka4 Hz");
			$pdf -> SetXY($x1, 85);
			$pdf -> Cell(15, 5, "$V_Siatka3 Hz");
			$pdf -> SetXY($x1, 90);
			$pdf -> Cell(15, 5, "$V_Siatka2 Hz");
			$pdf -> SetXY($x1, 95);
			$pdf -> Cell(15, 5, "$V_Siatka1 Hz");
			$pdf -> SetXY($x1, 100);
			$pdf -> Cell(15, 5, "$Czas_Suszenia min");
			$pdf -> SetXY($x1, 105);
			$pdf -> Cell(15, 5, "$Temp_Dolna *C");
			$pdf -> SetXY($x1, 110);
			$pdf -> Cell(15, 5, "$Temp_Gorna *C");
			$pdf -> SetXY($x1, 115);
			$pdf -> Cell(15, 5, "$Wilgotnosc %");
			
			/*Zmieniamy kodowanie znak�w z UTF-8 na Windows-1250 poniewa� klasa fpdf nie wspiera UTF-8
			co powoduje �e tekst z bazy danych nie pokazuje polskich znak�w */
			$Osoba = iconv('UTF-8', 'windows-1250', $Odpowiedzialny);
			$pdf -> SetXY($x1, 120);
			$pdf -> SetFont('arial_ce', '', 7);
			$pdf -> Cell(17, 5, "$Osoba");
		}

		//Wyci�gmy �redni� warto�� wilgotno�ci pocz�tkowej
					$Suma_Wilgotnosc1="";
					$Suma_Wilgotnosc2="";
					$Suma_Wilgotnosc="";
					$Ilosc_pomiarow1="";
					$Ilosc_pomiarow2="";
					$Ilosc_pomiarow="";
					$Srednia_Wilgotnosc="";
					$precision="";
		/* Utworzenie zapytania */
		$query = "SELECT SUM(Wilgotnosc) AS SumaWilg1 FROM `" . $asortyment_suszu . "` WHERE Data ='" . $data_raportu . "' AND Czas >=  STR_TO_DATE('08:00:00','%h:%i:%s') AND NrSuszarni='" . $nr_suszarni . "' AND Wilgotnosc > 0";
		/*Przesłanie zapytania do bazy*/
		$result = $mysqli -> query($query);
		/* Przetwarzanie wierszy wyniku zapytania */
		$num_rows = mysqli_num_rows($result);
		if ($num_rows > 0) {
			$row = $result -> fetch_object();
			$Suma_Wilgotnosc1 = $row -> SumaWilg1;
		}

		/* Utworzenie zapytania */
		$query = "SELECT COUNT(Wilgotnosc) AS IloscPom1 FROM `" . $asortyment_suszu . "` WHERE Data ='" . $data_raportu . "' AND Czas >=  STR_TO_DATE('08:00:00','%h:%i:%s') AND NrSuszarni='" . $nr_suszarni . "' AND Wilgotnosc > 0";
		/*Przesłanie zapytania do bazy*/
		$result = $mysqli -> query($query);
		/* Przetwarzanie wierszy wyniku zapytania */
		$num_rows = mysqli_num_rows($result);
		if ($num_rows > 0) {
			$row = $result -> fetch_object();
			$Ilosc_pomiarow1 = $row -> IloscPom1;
		}


		/* Utworzenie zapytania */
		$query = "SELECT SUM(Wilgotnosc) AS SumaWilg2 FROM `" . $asortyment_suszu . "` WHERE Data ='" . $kolejny_dzien . "' AND Czas <=  STR_TO_DATE('06:00:00','%h:%i:%s') AND NrSuszarni='" . $nr_suszarni . "' AND Wilgotnosc > 0";
		/*Przesłanie zapytania do bazy*/
		$result = $mysqli -> query($query);
		/* Przetwarzanie wierszy wyniku zapytania */
		$num_rows = mysqli_num_rows($result);
		if ($num_rows > 0) {
			$row = $result -> fetch_object();
			$Suma_Wilgotnosc2 = $row -> SumaWilg2;

		}

		/* Utworzenie zapytania */
		$query = "SELECT COUNT(Wilgotnosc) AS IloscPom2 FROM `" . $asortyment_suszu . "` WHERE Data ='" . $kolejny_dzien . "' AND Czas <=  STR_TO_DATE('06:00:00','%h:%i:%s') AND NrSuszarni='" . $nr_suszarni . "' AND Wilgotnosc > 0";
		/*Przesłanie zapytania do bazy*/
		$result = $mysqli -> query($query);
		/* Przetwarzanie wierszy wyniku zapytania */
		$num_rows = mysqli_num_rows($result);
		if ($num_rows > 0) {
			$row = $result -> fetch_object();
			$Ilosc_pomiarow2 = $row -> IloscPom2;

		}

		$Suma_Wilgotnosc=$Suma_Wilgotnosc1+$Suma_Wilgotnosc2;
		$Ilosc_pomiarow=$Ilosc_pomiarow1+$Ilosc_pomiarow2;

		$Srednia_Wilgotnosc=($Suma_Wilgotnosc/$Ilosc_pomiarow);
		$Srednia_Wilgotnosc=round($Srednia_Wilgotnosc,$precision=2);

		$pdf -> SetFont('arial_ce', 'B', 10);
		$pdf -> SetXY(10, 130);
		$pdf -> Cell(15, 5, "�rednia wilgotno��:");

		$pdf -> SetFont('arial_ce', '', 10);
		$pdf -> SetXY(45, 130);
		$pdf -> Cell(15, 5, "$Srednia_Wilgotnosc%");

		//Wyci�gmy info o ilosc suszu na I zmianie
		/* Utworzenie zapytania */
		$query = "SELECT IloscSuszuZmiana1 FROM `" . $asortyment_suszu . "` WHERE Data ='" . $data_raportu . "' AND NrSuszarni='" . $nr_suszarni . "' AND IloscSuszuZmiana1>0 ";
		/*Przesłanie zapytania do bazy*/
		$result = $mysqli -> query($query);
		/* Przetwarzanie wierszy wyniku zapytania */
		$num_rows = mysqli_num_rows($result);
		if ($num_rows > 0) {
			$row = $result -> fetch_object();
			$Ilosc_suszu1 = $row -> IloscSuszuZmiana1;
		}
		$pdf -> SetFont('arial_ce', 'B', 10);
		$pdf -> SetXY(60, 130);
		$pdf -> Cell(15, 5, "Ilo�� suszu I zmiana:");

		$pdf -> SetFont('arial_ce', '', 10);
		$pdf -> SetXY(98, 130);
		$pdf -> Cell(15, 5, "$Ilosc_suszu1 kg");

		//Wyci�gmy info o ilosc suszu na II zmianie
		/* Utworzenie zapytania */
		$query = "SELECT IloscSuszuZmiana2 FROM `" . $asortyment_suszu . "` WHERE Data ='" . $data_raportu . "' AND NrSuszarni='" . $nr_suszarni . "' AND IloscSuszuZmiana2>0";
		/*Przesłanie zapytania do bazy*/
		$result = $mysqli -> query($query);
		/* Przetwarzanie wierszy wyniku zapytania */
		$num_rows = mysqli_num_rows($result);
		if ($num_rows > 0) {
			$row = $result -> fetch_object();
			$Ilosc_suszu2 = $row -> IloscSuszuZmiana2;
		}
		$pdf -> SetFont('arial_ce', 'B', 10);
		$pdf -> SetXY(120, 130);
		$pdf -> Cell(15, 5, "Ilo�� suszu II zmiana:");

		$pdf -> SetFont('arial_ce', '', 10);
		$pdf -> SetXY(158, 130);
		$pdf -> Cell(15, 5, "$Ilosc_suszu2 kg");

		//Wyci�gmy info o ilosc suszu na III zmianie
		/* Utworzenie zapytania */
		$query = "SELECT IloscSuszuZmiana3 FROM `" . $asortyment_suszu . "` WHERE Data ='" . $data_raportu . "' AND NrSuszarni='" . $nr_suszarni . "' AND IloscSuszuZmiana3>0 ";
		/*Przesłanie zapytania do bazy*/
		$result = $mysqli -> query($query);
		/* Przetwarzanie wierszy wyniku zapytania */
		$num_rows = mysqli_num_rows($result);
		if ($num_rows > 0) {
			$row = $result -> fetch_object();
			$Ilosc_suszu3 = $row -> IloscSuszuZmiana3;
		}
		$pdf -> SetFont('arial_ce', 'B', 10);
		$pdf -> SetXY(180, 130);
		$pdf -> Cell(15, 5, "Ilo�� suszu III zmiana:");

		$pdf -> SetFont('arial_ce', '', 10);
		$pdf -> SetXY(218, 130);
		$pdf -> Cell(15, 5, "$Ilosc_suszu3 kg");

		//Wyci�gmy info o ca�kowitej ilosc suszu z danej suszarni
		/* Utworzenie zapytania */
		$query = "SELECT SUM(IloscSuszuZmiana1+IloscSuszuZmiana2+IloscSuszuZmiana3) AS IloscSuszu FROM `" . $asortyment_suszu . "` WHERE Data ='" . $data_raportu . "' AND NrSuszarni='" . $nr_suszarni . "' ";
		/*Przesłanie zapytania do bazy*/
		$result = $mysqli -> query($query);
		/* Przetwarzanie wierszy wyniku zapytania */
		$num_rows = mysqli_num_rows($result);
		if ($num_rows > 0) {
			$row = $result -> fetch_object();
			$Ilosc_suszu = $row -> IloscSuszu;
		}
		$pdf -> SetFont('arial_ce', 'B', 10);
		$pdf -> SetXY(240, 130);
		$pdf -> Cell(15, 5, "Ilo�� suszu:");

		$pdf -> SetFont('arial_ce', '', 10);
		$pdf -> SetXY(262, 130);
		$pdf -> Cell(15, 5, "$Ilosc_suszu kg");
		
		//Wyci�gmy info o ca�kowitej ilosc suszu ze wszystkich suszarni
		/* Utworzenie zapytania */
		$query = "SELECT CalkowitaIloscSuszu FROM `" . $asortyment_suszu . "` WHERE Data ='" . $data_raportu . "' AND CalkowitaIloscSuszu>0 ";
		/*Przesłanie zapytania do bazy*/
		$result = $mysqli -> query($query);
		/* Przetwarzanie wierszy wyniku zapytania */
		$num_rows = mysqli_num_rows($result);
		if ($num_rows > 0) {
			$row = $result -> fetch_object();
			$Ilosc_suszu = $row -> CalkowitaIloscSuszu;
		}
		$pdf -> SetFont('arial_ce', 'B', 10);
		$pdf -> SetXY(200.5, 137);
		$pdf -> Cell(15, 5, "Ilo�� suszu ze wszystkich suszarni:");

		$pdf -> SetFont('arial_ce', '', 10);
		$pdf -> SetXY(262, 137);
		$pdf -> Cell(15, 5, "$Ilosc_suszu kg");

		//Wyci�gmy info o ocenie suszu na I zmianie
		/* Utworzenie zapytania */
		$query = "SELECT OcenaTowaruZmiany1 FROM `" . $asortyment_suszu . "` WHERE Data ='" . $data_raportu . "' AND NrSuszarni='" . $nr_suszarni . "' AND CHAR_LENGTH(OcenaTowaruZmiany1)>0";
		/*Przesłanie zapytania do bazy*/
		$result = $mysqli -> query($query);
		/* Przetwarzanie wierszy wyniku zapytania */
		$num_rows = mysqli_num_rows($result);
		if ($num_rows > 0) {
			$row = $result -> fetch_object();
			$Ocena_suszu1 = $row -> OcenaTowaruZmiany1;
		}
		$pdf -> SetFont('arial_ce', 'B', 10);
		$pdf -> SetXY(10, 140);
		$pdf -> Cell(15, 5, "Ocena suszu I zmiana:");

		$pdf -> SetFont('arial_ce', '', 10);
		$pdf -> SetXY(50, 140);
		$pdf -> Cell(15, 5, "$Ocena_suszu1");

		//Wyci�gmy info o ocenie suszu na II zmianie
		/* Utworzenie zapytania */
		$query = "SELECT OcenaTowaruZmiany2 FROM `" . $asortyment_suszu . "` WHERE Data ='" . $data_raportu . "' AND NrSuszarni='" . $nr_suszarni . "' AND CHAR_LENGTH(OcenaTowaruZmiany2)>0";
		/*Przesłanie zapytania do bazy*/
		$result = $mysqli -> query($query);
		/* Przetwarzanie wierszy wyniku zapytania */
		$num_rows = mysqli_num_rows($result);
		if ($num_rows > 0) {
			$row = $result -> fetch_object();
			$Ocena_suszu2 = $row -> OcenaTowaruZmiany2;
		}
		$pdf -> SetFont('arial_ce', 'B', 10);
		$pdf -> SetXY(10, 145);
		$pdf -> Cell(15, 5, "Ocena suszu II zmiana:");

		$pdf -> SetFont('arial_ce', '', 10);
		$pdf -> SetXY(51, 145);
		$pdf -> Cell(15, 5, "$Ocena_suszu2");

		//Wyci�gmy info o ocenie suszu na III zmianie
		/* Utworzenie zapytania */
		$query = "SELECT OcenaTowaruZmiany3 FROM `" . $asortyment_suszu . "` WHERE Data ='" . $data_raportu . "' AND NrSuszarni='" . $nr_suszarni . "' AND CHAR_LENGTH(OcenaTowaruZmiany3)>0";
		/*Przesłanie zapytania do bazy*/
		$result = $mysqli -> query($query);
		/* Przetwarzanie wierszy wyniku zapytania */
		$num_rows = mysqli_num_rows($result);
		if ($num_rows > 0) {
			$row = $result -> fetch_object();
			$Ocena_suszu3 = $row -> OcenaTowaruZmiany3;
		}
		$pdf -> SetFont('arial_ce', 'B', 10);
		$pdf -> SetXY(10, 150);
		$pdf -> Cell(15, 5, "Ocena suszu III zmiana:");

		$pdf -> SetFont('arial_ce', '', 10);
		$pdf -> SetXY(52, 150);
		$pdf -> Cell(15, 5, "$Ocena_suszu3");

		//Wyci�gamy info o dostawcy
		/* Utworzenie zapytania */
		$query = "SELECT Dostawca FROM `" . $asortyment_suszu . "` WHERE Data ='" . $data_raportu . "' AND NrSuszarni='" . $nr_suszarni . "' AND CHAR_LENGTH(Dostawca)>0";
		/*Przesłanie zapytania do bazy*/
		$result = $mysqli -> query($query);
		/* Przetwarzanie wierszy wyniku zapytania */
		$num_rows = mysqli_num_rows($result);
		if ($num_rows > 0) {
			$row = $result -> fetch_object();
			$Dostawca = $row -> Dostawca;
		}
		$pdf -> SetFont('arial_ce', 'B', 10);
		$pdf -> SetXY(10, 160);
		$pdf -> Cell(15, 5, "Dostawca:");

		$pdf -> SetFont('arial_ce', '', 10);
		$pdf -> SetXY(29, 160);
		$pdf -> MultiCell(220, 5, "$Dostawca");

		//Wyci�gamy info o uwagach
		/* Utworzenie zapytania */
		$query = "SELECT Uwagi FROM `" . $asortyment_suszu . "` WHERE Data ='" . $data_raportu . "' AND NrSuszarni='" . $nr_suszarni . "' AND CHAR_LENGTH(Uwagi)>0";
		/*Przesłanie zapytania do bazy*/
		$result = $mysqli -> query($query);
		/* Przetwarzanie wierszy wyniku zapytania */
		$num_rows = mysqli_num_rows($result);
		if ($num_rows > 0) {
			$row = $result -> fetch_object();
			$Uwagi = $row -> Uwagi;
		}
		$pdf -> SetFont('arial_ce', 'B', 10);
		$pdf -> SetXY(10, 170);
		$pdf -> Cell(15, 5, "Uwagi:");

		$pdf -> SetFont('arial_ce', '', 10);
		$pdf -> SetXY(23, 170);
		$pdf -> Cell(15, 5, "$Uwagi");

		//Wyci�gamy zdj�cie
		/* Utworzenie zapytania */
		$query = "SELECT Zdjecia,OpisZdjecia FROM `" . $asortyment_suszu . "` WHERE Data='" . $data_raportu . "' AND NrSuszarni='" . $nr_suszarni . "' AND CHAR_LENGTH(Zdjecia)>0";
		/*Przesłanie zapytania do bazy*/
		$result = $mysqli -> query($query);
		/* Przetwarzanie wierszy wyniku zapytania */
		$num_rows = mysqli_num_rows($result);
		if ($num_rows > 0) {
			$row = $result -> fetch_object();
			$Zdjecie = $row -> Zdjecia;
			$Opis = $row -> OpisZdjecia;

			if (!empty($Zdjecie)) {

				$pdf -> AddPage();

				$pdf -> SetFont('arial_ce', 'B', 10);
				//$pdf->SetTextColor(0, 0, 0);
				$pdf -> SetXY(10, 30);
				$pdf -> Cell(15, 5, "Zdj�cia: ");

				$pdf -> Image("grafika/zdjecia_raporty_suszenia/$Zdjecie", 10, 40, 90, 60);

				$pdf -> SetFont('arial_ce', 'B', 10);
				$pdf -> SetXY(10, 170);
				$pdf -> Cell(15, 5, "Opis: ");

				$pdf -> SetFont('arial_ce', '', 10);
				$pdf -> SetXY(23, 170);
				$pdf -> Cell(15, 5, "$Opis");
			}
		}

	} else {

		$pdf -> SetFont('arial_ce', 'B', 24);
		$pdf -> SetXY(80, 80);
		$pdf -> Cell(50, 20, "Brak danych w bazie danych");

		$raport = FALSE;
		if (isset($_POST['wyslij']) && $raport == FALSE) {

			echo '<div class="alert alert-info"><strong>Info!</strong>&nbsp Nie wysłano raportu. Brak danych w bazie danych. </div>';
		}
	}

	//Jeśli nie wysyłamy raportu to wyswietlamy go
	if (!isset($_POST['wyslij'])) {
		$pdf -> Output();
	}

	//Wysyłanie raportu poprzez email
	if ($raport == TRUE && isset($_POST['wyslij']) && !$email == null && !$asortyment_suszu == null && !$data_raportu == null && !$nr_suszarni == null) {

		require_once ('PHPMailer/PHPMailerAutoload.php');
		# patch where is PHPMailer / ścieżka do PHPMailera

		$mail = new PHPMailer;
		$mail -> CharSet = "UTF-8";

		$mail -> IsSMTP();
		$mail -> Host = 'mailing.az.pl';
		# Gmail SMTP host
		$mail -> Port = 587;
		# Gmail SMTP port
		$mail -> SMTPAuth = true;
		# Enable SMTP authentication / Autoryzacja SMTP
		$mail -> Username = "formularz@web-control.pl";
		# Gmail username (e-mail) / Nazwa użytkownika
		$mail -> Password = "GqxQ4~w.pz";
		# Gmail password / Hasło użytkownika
		$mail -> SMTPSecure = 'ssl';

		$mail -> From = 'formularz@web-control.pl';
		# REM: Gmail put Your e-mail here
		$mail -> FromName = 'Suszarnia Warzyw Jaworski - Narzędzia Produkcyjne Online';
		# Sender name
		$mail -> SMTPAutoTLS = false;
		//wyłączenie TLS
		$mail -> SMTPSecure = '';
		//
		$mail -> AddAddress($email, $email);
		# # Recipient (e-mail address + name) / Odbiorca (adres e-mail i nazwa)

		$mail -> IsHTML(true);
		# Email @ HTML

		$mail -> Subject = 'Raport z procesu suszenia';
		$mail -> Body = "Witam.<br / ><br / >
						W załączniku znajduje się raport z procesu suszenia. <br / >
						Asortyment: $asortyment_suszu . Data: $data_raportu. <br / ><br / >
						Wiadomość wysłana z aplikacji sieciowej - Narzędzia Produkcyjne Online Suszarnia Warzyw Jaworski<br / >
						Proszę na nią nie odpowiadać.<br / ><br / >
						Administrator: Szymon Chomej. Email: schomej@jaworski.com.pl";
		$mail -> AltBody = 'Przepraszamy wystapił jakiś błąd tutaj powinna być treść wiadomości.';

		$doc = $pdf -> Output('S');
		$mail -> AddStringAttachment($doc, 'raport_suszenia.pdf', 'base64', 'application/pdf');

		if (!$mail -> Send()) {
			printf("<div class='alert alert-danger'><strong>Uwaga!</strong>&nbspBłąd podczas wysyłania wiadomości. Kod błędu: %s\n</div><br / ><br / >", $mail -> ErrorInfo);

			exit ;
		}

		echo '<div class="alert alert-success alert-dismissable fade in">
							<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
							<span class="glyphicon glyphicon-thumbs-up"></span>&nbsp;<strong>Sukces!</strong>&nbsp Raport zosta� wys�any. </div><br / >';

	} else {

		if ($raport == TRUE) {
			echo "<div class='alert alert-warning'><span class='glyphicon glyphicon-alert'></span>&nbsp;<strong>Uwaga!</strong>&nbsp Aby wys�a� raport podaj: Asortyment, Dat� oraz Email.</div>";

		}

	}

};

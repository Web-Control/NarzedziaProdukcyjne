<?php
function filtruj($zmienna) {
							$data = trim($zmienna);//usuwa spacje, tagi
							$data = stripslashes($zmienna);//usuwa slashe
							$data = htmlspecialchars($zmienna);//zamienia tagi html na czytelne znaki aby w formularzu nie wpisać szkodliwego kodu
							return $zmienna;
						}
if (isset($_POST['pdf'])) {

	/*Odbieramy dane z sesji*/
	$nr_raportu = $_SESSION["nr_raportu"];
	$asortyment = $_SESSION["asortyment"];
	$ostatni_raport = $_SESSION['ostatni_raport'];

}

if ($_POST['pdf2']) {


	/*Odbieramy dane z formularza*/
	 $nr_raportu = filtruj($_POST['nr_raportu']);
	 $asortyment = filtruj($_POST['asortyment']);
	 $ostatni_raport = filtruj($_POST['ostatni_raport']);
}

if ($_POST['wyslij']) {


	/*Odbieramy dane z formularza*/
	 $nr_raportu = filtruj($_POST['nr_raportu']);
	 $ostatni_raport = filtruj($_POST['ostatni_raport']);
	 $asortyment = filtruj($_POST['asortyment']);
	 $email = filtruj($_POST['email']);
}

$asortyment_czysty=substr($asortyment,0,-7);//Usuwamy tekst '_Steryl' z końca nazwy asortymentu, który jest w bazie danych

	/*Zmieniamy kodowanie znaków z UTF-8 na Windows-1250 ponieważ klasa fpdf nie wspiera UTF-8
	co powoduje że tekst z bazy danych nie pokazuje polskich znaków */
	$asortyment_czysty = iconv('UTF-8', 'windows-1250', $asortyment_czysty);

if (isset($_POST['pdf']) || isset($_POST['pdf2']) || isset($_POST['wyslij']) ) {
	/* Łączymy się z serwerem */
	require_once ('polaczenie_z_baza.php');

	$raport="";
	/*ob_end_clean();
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);*/

	if ($ostatni_raport=="Ostatni_raport")
		{
			$rok=date("Y");
			if ($stmt = $mysqli -> prepare("SELECT MAX(NrRaportu) FROM `" . $asortyment . "` WHERE Data LIKE '%" . $rok . "%' "))
			{
			$stmt -> execute();
			$stmt -> bind_result($Max_nr_raportu);
			$stmt -> store_result();
				if ($stmt -> fetch())
				{
					$nr_raportu = $Max_nr_raportu;
				}
			}
		}

	require ('fpdf/fpdf.php');

	$pdf = new FPDF('L');
	$pdf -> SetMargins(25.4,25.4,25.4,25.4);
	$pdf->AliasNbPages();
	$pdf -> AddPage();

	$pdf -> AddFont('arial_ce', '', 'arial_ce.php');
	$pdf -> AddFont('arial_ce', 'I', 'arial_ce_i.php');
	$pdf -> AddFont('arial_ce', 'B', 'arial_ce_b.php');
	$pdf -> AddFont('arial_ce', 'BI', 'arial_ce_bi.php');

	//Nagłówek
	$pdf -> SetFont('arial_ce', 'B', 16);
	$pdf -> SetXY(50, 24);
	$pdf -> Cell(40, 10, "Raport z procesu sterylizacji parowej", '', 'C');
	$pdf -> Ln(30);


	//usuwamy specjalne znaki takie jak '," aby nie możnabyło wpisać ich z formularza do zapytania SQL
	$asortyment = $mysqli -> real_escape_string($asortyment);
	$nr_raportu = $mysqli -> real_escape_string($nr_raportu);
	//$odbiorca = $mysqli -> real_escape_string($odbiorca);

	/* Utworzenie zapytania */
	$query = "SELECT NrRaportu,Odbiorca,Klient,Data,Godzina,PredkoscZasobnika,PredkoscSluzy1,PredkoscSluzy2,PredkoscSterylizatora,TemperaturaSterylizacji,CisnienieSterylizacji,PredkoscSuszarki1,NadmuchSuszarki1,TemperaturaSuszarki1,PredkoscSuszarki2,NadmuchSuszarki2,TemperaturaSuszarki2,PredkoscChlodziarki,NadmuchChlodziarki,WilgotnoscPoczatkowa,WilgotnoscKoncowa,WykonawcaPomiaru FROM `" . $asortyment . "` WHERE NrRaportu='" . $nr_raportu . "' ORDER BY Data, Godzina ASC";
	/*Przesłanie zapytania do bazy*/
	$result = $mysqli -> query($query);
	/* Przetwarzanie wierszy wyniku zapytania */
	$num_rows = mysqli_num_rows($result);

	if ($num_rows > 0 ) {

	$raport=TRUE;

	$row = $result -> fetch_object();
	$Nr_Raportu = $row -> NrRaportu;
	$Odbiorca = $row -> Odbiorca;
	$Klient = $row -> Klient;
	$Kto = "";
	$x1 = ""; //pozycja kursora przy wypisywaniu wyników za bazy danych
	if ($Odbiorca=="Klient") {
		$Kto=$Klient;
	}else {
		$Kto="Potrzeby własne";
	}


		/*Dane raportu*/
	$pdf -> SetFont('arial_ce', 'B', 10);
	$pdf -> SetXY(10, 40);
	$pdf -> Cell(15, 5, "Asortyment: $asortyment_czysty");
	$pdf -> SetXY(80, 40);
	$pdf -> Cell(15, 5, "Nr Raportu: $nr_raportu");
	$pdf -> SetXY(130, 40);
	$pdf -> Cell(15, 5, "Odbiorca: $Kto");

	//Tabela wielkości
	$pdf -> SetFont('arial_ce', 'B', 9);
	$pdf -> SetXY(10, 50);
	$pdf -> Multicell(45, 5, "Data\nGodzina\nPr�dko�� Zasobnika\nPr�dko�� �luzy 1\nPr�dko�� �luzy 2\nPr�dko�� Sterylizatora\nTemperatura Sterylizatora\nCi�nienie Sterylizatora\nPr�dko�� Suszarki 1\nNadmuch Suszarki 1\nTemperatura Suszarki 1\nPr�dko�� Suszarki 2\nNadmuch Suszarki 2\nTemperatura Suszarki 2\nPr�dko�� Chlodziarki\nNadmuch Chlodziarki\nWilgotno�� Pocz�tkowa\nWilgotno�� Ko�cowa\nOsoba odpowiedzialna");
	$pdf -> SetX(55);

		$result->data_seek(0);
		while ($row = $result -> fetch_object()) {

			$Data = $row -> Data;
			$Godzina = $row -> Godzina;
			$Godzina = substr($Godzina, 0, 5);
			$Predkosc_Zasobnika = $row -> PredkoscZasobnika;
			$Predkosc_Sluzy1 = $row -> PredkoscSluzy1;
			$Predkosc_Sluzy2 = $row -> PredkoscSluzy2;
			$Predkosc_Sterylizatora = $row -> PredkoscSterylizatora;
			$Temperatura_Sterylizacji = $row -> TemperaturaSterylizacji;
			$Cisnienie_Sterylizacji = $row -> CisnienieSterylizacji;
			$Predkosc_Suszarki1 = $row -> PredkoscSuszarki1;
			$Nadmuch_Suszarki1 = $row -> NadmuchSuszarki1;
			$Temperatura_Suszarki1 = $row -> TemperaturaSuszarki1;
			$Predkosc_Suszarki2 = $row -> PredkoscSuszarki2;
			$Nadmuch_Suszarki2 = $row -> NadmuchSuszarki2;
			$Temperatura_Suszarki2 = $row -> TemperaturaSuszarki2;
			$Predkosc_Chlodziarki = $row -> PredkoscChlodziarki;
			$Nadmuch_Chlodziarki = $row -> NadmuchChlodziarki;
			$Wilgotnosc_Poczatkowa = $row -> WilgotnoscPoczatkowa;
			$Wilgotnosc_Koncowa = $row -> WilgotnoscKoncowa;
			$Odpowiedzialny = $row -> WykonawcaPomiaru;

			/*Wyświetlenie/Wypisanie wyników z bazy danych*/

			//Sprawdzamy czy osiągnieto limit z prawej strony kartki, jeśli tak to otwieramy nową stronę i ustawiamy kurs na początek
			if ($x1>260) {
				$pdf -> AddPage();

				//Nagłówek
				$pdf -> SetFont('arial_ce', 'B', 16);
				$pdf -> SetXY(50, 24);
				$pdf -> Cell(40, 10, "Raport z procesu sterylizacji parowej", '', 'C');
				$pdf -> Ln(30);

				/*Dane raportu*/
				$pdf -> SetFont('arial_ce', 'B', 10);
				$pdf -> SetXY(10, 40);
				$pdf -> Cell(15, 5, "Asortyment: $asortyment");
				$pdf -> SetXY(80, 40);
				$pdf -> Cell(15, 5, "Nr Raportu: $nr_raportu");
				$pdf -> SetXY(130, 40);
				$pdf -> Cell(15, 5, "Odbiorca: $Kto");

				//Tabela wielkośc
				$pdf -> SetFont('arial_ce', 'B', 9);
				$pdf -> SetXY(10, 50);
				$pdf -> Multicell(45, 5, "Data\nGodzina\nPr�dko�� Zasobnika\nPr�dko�� �luzy 1\nPr�dko�� �luzy 2\nPr�dko�� Sterylizatora\nTemperatura Sterylizatora\nCi�nienie Sterylizatora\nPr�dko�� Suszarki 1\nNadmuch Suszarki 1\nTemperatura Suszarki 1\nPr�dko�� Suszarki 2\nNadmuch Suszarki 2\nTemperatura Suszarki 2\nPr�dko�� Chlodziarki\nNadmuch Chlodziarki\nWilgotno�� Pocz�tkowa\nWilgotno�� Ko�cowa\nOsoba odpowiedzialna");
				$pdf -> SetX(55);
			}

			$pdf -> SetFont('arial_ce', '', 9);
			$x = $pdf -> GetX();
			$x1 = $x + 2;



			$pdf -> SetXY($x1, 50);
			$pdf -> Cell(15, 5, "$Data");
			$pdf -> SetXY($x1, 55);
			$pdf -> Cell(15, 5, "$Godzina");
			$pdf -> SetXY($x1, 60);
			$pdf -> Cell(15, 5, "$Predkosc_Zasobnika Hz");
			$pdf -> SetXY($x1, 65);
			$pdf -> Cell(15, 5, "$Predkosc_Sluzy1 Hz");
			$pdf -> SetXY($x1, 70);
			$pdf -> Cell(15, 5, "$Predkosc_Sluzy2 Hz");
			$pdf -> SetXY($x1, 75);
			$pdf -> Cell(15, 5, "$Predkosc_Sterylizatora Hz");
			$pdf -> SetXY($x1, 80);
			$pdf -> Cell(15, 5, "$Temperatura_Sterylizacji *C");
			$pdf -> SetXY($x1, 85);
			$pdf -> Cell(15, 5, "$Cisnienie_Sterylizacji kPa");
			$pdf -> SetXY($x1, 90);
			$pdf -> Cell(15, 5, "$Predkosc_Suszarki1 Hz");
			$pdf -> SetXY($x1, 95);
			$pdf -> Cell(15, 5, "$Nadmuch_Suszarki1 Hz");
			$pdf -> SetXY($x1, 100);
			$pdf -> Cell(15, 5, "$Temperatura_Suszarki1 *C");
			$pdf -> SetXY($x1, 105);
			$pdf -> Cell(15, 5, "$Predkosc_Suszarki2 Hz");
			$pdf -> SetXY($x1, 110);
			$pdf -> Cell(15, 5, "$Nadmuch_Suszarki2 Hz");
			$pdf -> SetXY($x1, 115);
			$pdf -> Cell(15, 5, "$Temperatura_Suszarki2 *C");
			$pdf -> SetXY($x1, 120);
			$pdf -> Cell(15, 5, "$Predkosc_Chlodziarki Hz");
			$pdf -> SetXY($x1, 125);
			$pdf -> Cell(15, 5, "$Nadmuch_Chlodziarki Hz");
			$pdf -> SetXY($x1, 130);
			$pdf -> Cell(15, 5, "$Wilgotnosc_Poczatkowa %");
			$pdf -> SetXY($x1, 135);
			$pdf -> Cell(15, 5, "$Wilgotnosc_Koncowa %");
			$pdf -> SetXY($x1, 140);
			$pdf -> Cell(17, 5, "$Odpowiedzialny");

		}

		//Wyci�gamy �redni� warto�� wilgotno�� pocz�tkowej
		/* Utworzenie zapytania */
		$query = "SELECT AVG(WilgotnoscPoczatkowa)AS SredniaWilgotnoscPoczotkowa FROM `" . $asortyment . "` WHERE NrRaportu='" . $nr_raportu . "' ";
		/*Przesłanie zapytania do bazy*/
		$result = $mysqli -> query($query);
		/* Przetwarzanie wierszy wyniku zapytania */
		$num_rows = mysqli_num_rows($result);
		if ($num_rows > 0) {
		$row = $result -> fetch_object();
		$Srednia_Wilg_Poczatkowa = $row -> SredniaWilgotnoscPoczotkowa;
		$precision="";
		$Srednia_Wilg_Poczatkowa=round($Srednia_Wilg_Poczatkowa,$precision=2);
		}
		$pdf -> SetFont('arial_ce', 'B', 10);
		$pdf -> SetXY(10, 150);
		$pdf -> Cell(15, 5, "�rednia Wilgotno�� pocz�tkowa:");

		$pdf -> SetFont('arial_ce', '', 10);
		$pdf -> SetXY(66, 150);
		$pdf -> Cell(15, 5, "$Srednia_Wilg_Poczatkowa%");

		//Wyci�gamy �rednią warto�� wilgotno�ci ko�cowej
		/* Utworzenie zapytania */
		$query = "SELECT AVG(WilgotnoscKoncowa)AS SredniaWilgotnoscKoncowa FROM `" . $asortyment . "` WHERE NrRaportu='" . $nr_raportu . "' ";
		/*Przesłanie zapytania do bazy*/
		$result = $mysqli -> query($query);
		/* Przetwarzanie wierszy wyniku zapytania */
		$num_rows = mysqli_num_rows($result);
		if ($num_rows > 0) {
		$row = $result -> fetch_object();
		$Srednia_Wilg_Koncowa = $row -> SredniaWilgotnoscKoncowa;
		$precision="";
		$Srednia_Wilg_Koncowa=round($Srednia_Wilg_Koncowa,$precision=2);
		}
		$pdf -> SetFont('arial_ce', 'B', 10);
		$pdf -> SetXY(10, 155);
		$pdf -> Cell(15, 5, "�rednia wilgotno�� ko�cowa:");

		$pdf -> SetFont('arial_ce', '', 10);
		$pdf -> SetXY(61, 155);
		$pdf -> Cell(15, 5, "$Srednia_Wilg_Koncowa%");
		
		//Wyci�gamy info o sicie
		/* Utworzenie zapytania */
		$query = "SELECT Sito FROM `" . $asortyment . "` WHERE NrRaportu='" . $nr_raportu . "' ";
		/*Przesłanie zapytania do bazy*/
		$result = $mysqli -> query($query);
		/* Przetwarzanie wierszy wyniku zapytania */
		$num_rows = mysqli_num_rows($result);
		if ($num_rows > 0) {
		$row = $result -> fetch_object();
		$Sito = $row -> Sito;
		}
		$pdf -> SetFont('arial_ce', 'B', 10);
		$pdf -> SetXY(90, 150);
		$pdf -> Cell(15, 5, "Sito: ");

		$pdf -> SetFont('arial_ce', '', 10);
		$pdf -> SetXY(100, 150);
		$pdf -> Cell(15, 5, "$Sito");

		//Wyciągamy info o odsiewie
		/* Utworzenie zapytania */
		$query = "SELECT Odsiew FROM `" . $asortyment . "` WHERE NrRaportu='" . $nr_raportu . "' ";
		/*Przesłanie zapytania do bazy*/
		$result = $mysqli -> query($query);
		/* Przetwarzanie wierszy wyniku zapytania */
		$num_rows = mysqli_num_rows($result);
		if ($num_rows > 0) {
		$row = $result -> fetch_object();
		$Odsiew = $row -> Odsiew;
		}
		$pdf -> SetFont('arial_ce', 'B', 10);
		$pdf -> SetXY(90, 155);
		$pdf -> Cell(15, 5, "Odsiew: ");

		$pdf -> SetFont('arial_ce', '', 10);
		$pdf -> SetXY(105, 155);
		$pdf -> Cell(15, 5, "$Odsiew kg");

		//Wyciagamy info o metalu
		/* Utworzenie zapytania */
		$query = "SELECT Metal FROM `" . $asortyment . "` WHERE NrRaportu='" . $nr_raportu . "' ";
		/*Przesłanie zapytania do bazy*/
		$result = $mysqli -> query($query);
		/* Przetwarzanie wierszy wyniku zapytania */
		$num_rows = mysqli_num_rows($result);
		if ($num_rows > 0) {
		$row = $result -> fetch_object();
		$Metal = $row -> Metal;
		}
		$pdf -> SetFont('arial_ce', 'B', 10);
		$pdf -> SetXY(90, 160);
		$pdf -> Cell(15, 5, "Metal: ");

		$pdf -> SetFont('arial_ce', '', 10);
		$pdf -> SetXY(102, 160);
		$pdf -> Cell(15, 5, "$Metal kg");

		//Wyci�gamy info o wielko�ci parti na pocz�tku
		/* Utworzenie zapytania */
		$query = "SELECT PartiaPoczatek FROM `" . $asortyment . "` WHERE NrRaportu='" . $nr_raportu . "' ";
		/*Przesłanie zapytania do bazy*/
		$result = $mysqli -> query($query);
		/* Przetwarzanie wierszy wyniku zapytania */
		$num_rows = mysqli_num_rows($result);
		if ($num_rows > 0) {
		$row = $result -> fetch_object();
		$Partia_poczatek = $row -> PartiaPoczatek;
		}
		$pdf -> SetFont('arial_ce', 'B', 10);
		$pdf -> SetXY(130, 150);
		$pdf -> Cell(15, 5, "Wielko�� parti na pocz�tku: ");

		$pdf -> SetFont('arial_ce', '', 10);
		$pdf -> SetXY(178, 150);
		$pdf -> Cell(15, 5, "$Partia_poczatek kg");

		//Wyci�gamy info o wielko�ci parti na ko�cu
		/* Utworzenie zapytania */
		$query = "SELECT PartiaKoniec FROM `" . $asortyment . "` WHERE NrRaportu='" . $nr_raportu . "' ";
		/*Przesłanie zapytania do bazy*/
		$result = $mysqli -> query($query);
		/* Przetwarzanie wierszy wyniku zapytania */
		$num_rows = mysqli_num_rows($result);
		if ($num_rows > 0) {
		$row = $result -> fetch_object();
		$Partia_koniec = $row -> PartiaKoniec;
		}
		$pdf -> SetFont('arial_ce', 'B', 10);
		$pdf -> SetXY(130, 155);
		$pdf -> Cell(15, 5, "Wielko�� parti na ko�cu:");

		$pdf -> SetFont('arial_ce', '', 10);
		$pdf -> SetXY(173, 155);
		$pdf -> Cell(15, 5, "$Partia_koniec kg");

		//Wyci�gamy info o liczbie i masie work�w
		/* Utworzenie zapytania */
		$query = "SELECT LiczbaMasaWorkow FROM `" . $asortyment . "` WHERE NrRaportu='" . $nr_raportu . "' ";
		/*Przesłanie zapytania do bazy*/
		$result = $mysqli -> query($query);
		/* Przetwarzanie wierszy wyniku zapytania */
		$num_rows = mysqli_num_rows($result);
		if ($num_rows > 0) {
		$row = $result -> fetch_object();
		$Worki = $row -> LiczbaMasaWorkow;
		}
		$pdf -> SetFont('arial_ce', 'B', 10);
		$pdf -> SetXY(130, 160);
		$pdf -> Cell(15, 5, "Liczba i masa workow:");

		$pdf -> SetFont('arial_ce', '', 10);
		$pdf -> SetXY(170, 160);
		$pdf -> Cell(15, 5, "$Worki");

		//Wyciągamy info o wydajności
		/* Utworzenie zapytania */
		$query = "SELECT Wydajnosc FROM `" . $asortyment . "` WHERE NrRaportu='" . $nr_raportu . "' ";
		/*Przesłanie zapytania do bazy*/
		$result = $mysqli -> query($query);
		/* Przetwarzanie wierszy wyniku zapytania */
		$num_rows = mysqli_num_rows($result);
		if ($num_rows > 0) {
		$row = $result -> fetch_object();
		$Wydajnosc = $row -> Wydajnosc;
		}
		$pdf -> SetFont('arial_ce', 'B', 10);
		$pdf -> SetXY(130, 165);
		$pdf -> Cell(15, 5, "Wydajno��:");

		$pdf -> SetFont('arial_ce', '', 10);
		$pdf -> SetXY(155, 165);
		$pdf -> Cell(15, 5, "$Wydajnosc kg/h");

		//Obliczamy straty w towarze po procesie sterylizacji w kg
		/* Utworzenie zapytania */
		$query = "SELECT PartiaPoczatek,PartiaKoniec FROM `" . $asortyment . "` WHERE NrRaportu='" . $nr_raportu . "' ";
		/*Przesłanie zapytania do bazy*/
		$result = $mysqli -> query($query);
		/* Przetwarzanie wierszy wyniku zapytania */
		$num_rows = mysqli_num_rows($result);
		if ($num_rows > 0) {
		$row = $result -> fetch_object();
		$Partia_poczatek = $row -> PartiaPoczatek;
		$Partia_koniec = $row -> PartiaKoniec;
		$Wynik=$Partia_poczatek-$Partia_koniec;
		}
		$pdf -> SetFont('arial_ce', 'B', 10);
		$pdf -> SetXY(210, 150);
		$pdf -> Cell(15, 5, "Strata towaru w kg:");

		$pdf -> SetFont('arial_ce', '', 10);
		$pdf -> SetXY(244, 150);
		$pdf -> Cell(15, 5, "$Wynik kg");

		//Obliczamy straty w towarze po procesie sterylizacji w %
		/* Utworzenie zapytania */
		$query = "SELECT PartiaPoczatek,PartiaKoniec FROM `" . $asortyment . "` WHERE NrRaportu='" . $nr_raportu . "' ";
		/*Przesłanie zapytania do bazy*/
		$result = $mysqli -> query($query);
		/* Przetwarzanie wierszy wyniku zapytania */
		$num_rows = mysqli_num_rows($result);
		if ($num_rows > 0) {
		$row = $result -> fetch_object();
		$Partia_poczatek = $row -> PartiaPoczatek;
		$Partia_koniec = $row -> PartiaKoniec;
		$precision="";
		$Wynik="";
		if ($Partia_poczatek>0)
			{
			$Wynik=round((($Partia_poczatek-$Partia_koniec)*100)/$Partia_poczatek,$precision=2);
			}else {
				$Wynik=0;
			}
		}
		$pdf -> SetFont('arial_ce', 'B', 10);
		$pdf -> SetXY(210, 155);
		$pdf -> Cell(15, 5, "Strata towaru w %:");

		$pdf -> SetFont('arial_ce', '', 10);
		$pdf -> SetXY(243, 155);
		$pdf -> Cell(15, 5, "$Wynik %");

		//Wyci�gamy info o obsadzie
		/* Utworzenie zapytania */
		$query = "SELECT Obsada FROM `" . $asortyment . "` WHERE NrRaportu='" . $nr_raportu . "' ";
		/*Przesłanie zapytania do bazy*/
		$result = $mysqli -> query($query);
		/* Przetwarzanie wierszy wyniku zapytania */
		$num_rows = mysqli_num_rows($result);
		if ($num_rows > 0) {
		$row = $result -> fetch_object();
		$Obsada = $row -> Obsada;
		/*Zmieniamy kodowanie znaków z UTF-8 na Windows-1250 ponieważ klasa fpdf nie wspiera UTF-8
		co powoduje że tekst z bazy danych nie pokazuje polskich znaków */
		$Obsada = iconv('UTF-8', 'windows-1250', $Obsada);
		}
		$pdf -> SetFont('arial_ce', 'B', 10);
		$pdf -> SetXY(10, 170);
		$pdf -> Cell(15, 5, "Obsada: ");

		$pdf -> SetFont('arial_ce', '', 10);
		$pdf -> SetXY(25, 170);
		$pdf -> Cell(15, 5, "$Obsada");

		//Wyciągamy info o uwagach
		/* Utworzenie zapytania */
		$query = "SELECT Uwagi FROM `" . $asortyment . "` WHERE NrRaportu='" . $nr_raportu . "' ";
		/*Przesłanie zapytania do bazy*/
		$result = $mysqli -> query($query);
		/* Przetwarzanie wierszy wyniku zapytania */
		$num_rows = mysqli_num_rows($result);
		if ($num_rows > 0) {
		$row = $result -> fetch_object();
		$Uwagi = $row -> Uwagi;
		}
		$pdf -> SetFont('arial_ce', 'B', 10);
		$pdf -> SetXY(10, 175);
		$pdf -> Cell(15, 5, "Uwagi: ");

		$pdf -> SetFont('arial_ce', '', 10);
		$pdf -> SetXY(23, 175);
		$pdf -> Cell(15, 5, "$Uwagi");

		//Wyciągamy link do dokumentu
		/* Utworzenie zapytania */
		$query = "SELECT Dokument FROM `" . $asortyment . "` WHERE NrRaportu='" . $nr_raportu . "' ";
		/*Przesłanie zapytania do bazy*/
		$result = $mysqli -> query($query);
		/* Przetwarzanie wierszy wyniku zapytania */
		$num_rows = mysqli_num_rows($result);
		if ($num_rows > 0) {
		$row = $result -> fetch_object();
		$Dokument = $row -> Dokument;
		}
		$pdf -> SetFont('arial_ce', 'B', 10);
		$pdf -> SetXY(10, 180);
		$pdf -> Cell(15, 5, "Dokument: ");

		$pdf -> SetFont('arial_ce', '', 10);
		$pdf->SetTextColor(51, 122, 183);
		$pdf -> SetXY(30, 180);
		$pdf -> Cell(15, 5, "$Dokument",0,0,'L',false,"http://www.web-control.pl/NarzedziaProdukcyjne/dokumenty/raporty_sterylizacji/$Dokument");

		//Wyciągamy zdjęcie
		/* Utworzenie zapytania */
		$query = "SELECT Zdjecia,OpisZdjecia FROM `" . $asortyment . "` WHERE NrRaportu='" . $nr_raportu . "' ";
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
		$pdf->SetTextColor(0, 0, 0);
		$pdf -> SetXY(10, 30);
		$pdf -> Cell(15, 5, "Zdj�cia: ");

		$pdf -> Image("grafika/zdjecia_raporty_sterylizacji/$Zdjecie", 10, 40, 90, 60);

		$pdf -> SetFont('arial_ce', 'B', 10);
		$pdf -> SetXY(10, 170);
		$pdf -> Cell(15, 5, "Opis: ");

		$pdf -> SetFont('arial_ce', '', 10);
		$pdf -> SetXY(23, 170);
		$pdf -> Cell(15, 5, "$Opis");
		}
		}


	}
	else {
		$pdf -> SetFont('arial_ce', 'B', 24);
		$pdf -> SetXY(80, 80);
		$pdf -> Cell(50, 20, "Brak danych w bazie danych");

		$raport=FALSE;
		if (isset($_POST['wyslij']) && $raport==FALSE) {

			echo '<div class="alert alert-info"><strong>Info!</strong>&nbsp Nie wysłano raportu. Brak danych w bazie danych. </div>';
		}

	}

	//Jeśli nie wysyłamy raportu to wyswietlamy go
	if (!isset($_POST['wyslij'])) {
		$pdf -> Output();
	}

	//Wysyłanie raportu poprzez email
	if ($raport==TRUE && isset($_POST['wyslij'])&& !$email == null && !$asortyment == null && !$nr_raportu == null) {

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

		$mail->From = 'formularz@web-control.pl'; # REM: Gmail put Your e-mail here
		$mail -> FromName = 'Suszarnia Warzyw Jaworski - Narzędzia Produkcyjne Online';
		# Sender name
		$mail->SMTPAutoTLS = false;   //wyłączenie TLS
		$mail->SMTPSecure = '';    //
		$mail -> AddAddress($email, $email);
		# # Recipient (e-mail address + name) / Odbiorca (adres e-mail i nazwa)

		$mail -> IsHTML(true);
		# Email @ HTML


		$mail -> Subject = 'Raport z procesu sterylizacji parowej';
		$mail -> Body = "Witam.<br / ><br / >
						W załączniku znajduje się raport z procesu sterylizacji parowej.<br / >
						Asortyment: $asortyment.  Nr raportu: $nr_raportu. <br / ><br / >
						Wiadomość wysłana z aplikacji sieciowej - Narzędzia Produkcyjne Online Suszarnia Warzyw Jaworski.<br / >
						Proszę na nią nie odpowiadać.<br / ><br / >
						Administrator: Szymon Chomej. Email: schomej@jaworski.com.pl";
		$mail -> AltBody = 'Przepraszamy wystapił jakiś błąd tutaj powinna być treść wiadomości.';

		$doc = $pdf->Output('S');
		$mail->AddStringAttachment($doc, 'raport_sterylizacji.pdf', 'base64', 'application/pdf');


		if (!$mail -> Send()) {
			printf("<div class='alert alert-danger'><strong>Uwaga!</strong>&nbspBłąd podczas wysyłania wiadomości. Kod błędu: %s\n</div><br / ><br / >",$mail -> ErrorInfo);

			exit ;
		}

		echo '<div class="alert alert-success alert-dismissable fade in">
							<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
							<span class="glyphicon glyphicon-thumbs-up"></span>&nbsp;<strong>Sukces!</strong>&nbsp Raport został wysłany. </div><br / >';

	}
	else {
				if ($raport==TRUE) {
				echo "<div class='alert alert-warning'><span class='glyphicon glyphicon-alert'></span>&nbsp;<strong>Uwaga!</strong>&nbsp Aby wysłać raport podaj: Asortyment, Nr Raportu oraz Email.</div>";
				}
			}




}
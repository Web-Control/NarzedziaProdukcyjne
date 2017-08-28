<h2>Dodawanie nowego asortymentu</h2>

<div id="formularz">
		<div class="row" >
			<div class="form-group">
<form class="form_loguj" method="POST" action="index2.php?dodawanie_asortymentu=1.php">
	<fieldset>
					<legend>Dodaj nowy asortyment suszu</legend>
		<div class="row">
					<div class="col-sm-4">
						<label>Nazwa asortymentu:</label><br / >
						<input class="form-control"  type="text" name="nowy_asortyment_suszu" max="15" required>
					</div>
		</div>
		<br / >
		<span class="glyphicon glyphicon-plus-sign"></span>&nbsp<input type="submit" value="Dodaj" name="dodaj_susz">
	</fieldset>
</form>
</div>
</div>
</div>
<br / >
<br / >

<div id="formularz">
		<div class="row" >
			<div class="form-group">
<form class="form_loguj" method="POST" action="index2.php?dodawanie_asortymentu=1.php">
	<fieldset>
					<legend>Dodaj nowy asortyment sterylizacji</legend>
		<div class="row">
					<div class="col-sm-4">
						<label>Nazwa asortymentu:</label><br / >
						<input class="form-control"  type="text" name="nowy_asortyment_sterylizacji" max="15" required>
					</div>
		</div>
		<br / >
		<span class="glyphicon glyphicon-plus-sign"></span>&nbsp<input type="submit" value="Dodaj" name="dodaj_steryl">
	</fieldset>
</form>
</div>
</div>
</div>
<br / >
<br / >

<?php
/*ob_end_clean();
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);*/
//Dodajemy nowy asortyment suszu
if ($_POST['dodaj_susz'])
{
	//echo "Form działa<br / >";
	function filtruj($zmienna) {
					$data = trim($zmienna);
					//usuwa spacje, tagi
					$data = stripslashes($zmienna);
					//usuwa slashe
					$data = htmlspecialchars($zmienna);
					//zamienia tagi html na czytelne znaki aby w formularzu nie wpisać szkodliwego kodu
					return $zmienna;
				}
	$nowy_asortyment=filtruj($_POST['nowy_asortyment_suszu']);
	if($nowy_asortyment==null) {
		echo "<div class='alert alert-warning'><span class='glyphicon glyphicon-alert'></span>&nbsp<strong>Ostrzeżenie!</strong>&nbsp Podaj nowy asortyment.</div>";
		}

	//Sprawdzamy czy nazwa zawiera znak spacji ponieważ nazwy tabel ze spacjami powoduja problemy w zapisie do bazy danych
	$spacja=strpos($nowy_asortyment, " ");
	if (!$spacja==FALSE) {
		echo "<div class='alert alert-warning'><span class='glyphicon glyphicon-alert'></span>&nbsp<strong>Ostrzeżenie!</strong>&nbsp Podaj nazwę asortymentu bez znaku 'spacji'. Zamiast spacji możesz użyć podkreślnika '_'.</div>";

	}

	if ((!$nowy_asortyment==null) && ($spacja==FALSE))
	{
		//echo "Form działa<br / >";
			/* Łączymy się z serwerem */
			require_once ('polaczenie_z_baza.php');

			if (mysqli_connect_errno()) {

			printf("<div class='alert alert-danger'><span class='glyphicon glyphicon-thumb-down'></span>&nbsp<strong>Uwaga!</strong>&nbspBrak połączenia z serwerem MySQL. Kod błędu: %s\n</div>", mysqli_connect_error());

			} else
				{
					//Zapytanie do bazy o obecny asortyment i sprawdzamy czy nowy już czasami nie istnieje
					if ($stmt = $mysqli -> prepare("SELECT Asortyment FROM AsortymentSuszu"))
					{
						//echo "Zapytanie1 działa<br / >";
					$stmt -> execute();
					$stmt -> bind_result($Obecny_asortyment);
					$stmt -> store_result();
					}

					$Asortyment_wbazie=array();
					if ($stmt->num_rows > 0) {

					/* Wyciągamy dane z zapytania sql i zapisujemy do tablicy  */
    				while ($stmt->fetch()) {
					static $i=0;
					$Asortyment_wbazie[$i]=$Obecny_asortyment;
					$i++;
    				}
    				}

					//Sprawdzamy czy nowy asortyment już czasami nie istnieje w bazie
					$ilosc_asortymentu_wbazie=count($Asortyment_wbazie);
					$mozna_dodac="";

					//Jeśli w bazie nie ma żdnego asortymentu odrazu zezwalamy na zapis
					//pętla porównująca nowy asortyment z tymi w bazie nie działa przy pustej bazie!
					if(isset($Asortyment_wbazie[0])==FALSE)
					{
					$mozna_dodac=TRUE;
					}

					foreach ($Asortyment_wbazie as $key => $value)
					{
							for ($i=0; $i <$ilosc_asortymentu_wbazie+1 ; $i++)
							{

								if ($value==$nowy_asortyment)
									{
									$mozna_dodac=FALSE;
									break;
									}

								if ($i==$ilosc_asortymentu_wbazie)
									{
									$mozna_dodac=TRUE;
									}

							}

					}

					if ($mozna_dodac==FALSE) {
						echo "<div class='alert alert-warning'><span class='glyphicon glyphicon-alert'></span>&nbsp<strong>Ostrzeżenie!</strong>&nbsp Taki asortyment już istnieje w bazie danych. Podaj inną nazwę.</div>";
					}

					if ($mozna_dodac)
					{
						//echo "MOzna doadać działa<br / >";
					$nowy_asortyment=$mysqli ->real_escape_string($nowy_asortyment);

					//Dodajemy nowy asortyment do listy asortymentu
					if ($stmt = $mysqli -> prepare("INSERT INTO AsortymentSuszu (Asortyment) VALUES (?)"))
					{
						//echo "Wpisywanie do listy działa <br / >";
					$stmt -> bind_param("s",$nowy_asortyment);
					$stmt -> execute();

					if ($stmt -> affected_rows == 0 || $stmt -> affected_rows < 0 ||$stmt->affected_rows==NULL)
							{
								echo "<div class='alert alert-warning'><span class='glyphicon glyphicon-alert'></span>&nbsp<strong>Ostrzeżenie!</strong>&nbsp Nie dokonano zapisu. Możliwy błąd zapytania.</div>";
							}

							if ($stmt -> affected_rows > 0)
							{
								//echo "Kontrala zapisu do listy działa<br / >";
								//Tworzymy nową tabelę w bazie danych dla nowego asortymentu
								if ($stmt = $mysqli -> prepare(

								"CREATE TABLE `" . $nowy_asortyment . "` (
  								Lp int(11) NOT NULL AUTO_INCREMENT,
 								NrSuszarni int(11) DEFAULT NULL,
  								Data date NOT NULL,
  								Czas time NOT NULL,
 								PredkoscBlanszownika int(11) NOT NULL,
  								TemperaturaBlanszownika int(11) NOT NULL,
  								PredkoscSiatkiNr7 int(11) NOT NULL,
  								PredkoscSiatkiNr6 int(11) NOT NULL,
  								PredkoscSiatkiNr5 int(11) NOT NULL,
  								PredkoscSiatkiNr4 int(11) NOT NULL,
  								PredkoscSiatkiNr3 int(11) NOT NULL,
  								PredkoscSiatkiNr2 int(11) NOT NULL,
  								PredkoscSiatkiNr1 int(11) NOT NULL,
  								TemperaturaGora int(11) NOT NULL,
  								TemperaturaDol int(11) NOT NULL,
  								CzasSuszenia decimal(5,2) NOT NULL,
  								Wilgotnosc decimal(5,2) DEFAULT NULL,
  								WykonawcaPomiaru text CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL,
  								OcenaTowaruZmiany1 text CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL,
  								OcenaTowaruZmiany2 text CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL,
  								OcenaTowaruZmiany3 text CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL,
  								IloscSuszuZmiana1 int(11) NOT NULL,
  								IloscSuszuZmiana2 int(11) NOT NULL,
  								IloscSuszuZmiana3 int(11) NOT NULL,
  								CalkowitaIloscSuszu int(11) NOT NULL,
  								Dostawca text CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL,
  								Uwagi text CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL,
  								Zdjecia varchar(200) CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL,
  								OpisZdjecia text CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL,
  								PRIMARY KEY (Lp)
								)"))
									{
										//echo "Tworzenie tabeli działa<br / >";
										$stmt -> execute();

										if ($stmt = $mysqli -> prepare("SELECT count(*)FROM information_schema.columns WHERE table_name = '".$nowy_asortyment."' "))
										{
											//echo "Utworzono tabele działa<br / >";
											$stmt -> execute();
											$stmt -> store_result();
											if ($stmt -> num_rows > 0)
											{
											echo '<div class="alert alert-success alert-dismissable fade in">
				  							<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
				 				 			<span class="glyphicon glyphicon-thumb-up"></span>&nbsp<strong>Sukces!</strong>&nbsp Dodano nowy asortyment suszu.</div><br / >';
											}

										}

									}



							}
					}
					}

				}

$mysqli -> close();
	}


}

//Dodajemy nowy asortyment sterylizacji
if ($_POST['dodaj_steryl'])
{
	//echo "Form działa<br / >";
	function filtruj($zmienna) {
					$data = trim($zmienna);
					//usuwa spacje, tagi
					$data = stripslashes($zmienna);
					//usuwa slashe
					$data = htmlspecialchars($zmienna);
					//zamienia tagi html na czytelne znaki aby w formularzu nie wpisać szkodliwego kodu
					return $zmienna;
				}
	$nowy_asortyment=filtruj($_POST['nowy_asortyment_sterylizacji']);
	$koncowka="_Steryl";
	$nowy_asortyment="$nowy_asortyment"."$koncowka";

	//Sprawdzamy czy nazwa zawiera znak spacji ponieważ nazwy tabel ze spacjami powoduja problemy w zapisie do bazy danych
	$spacja=strpos($nowy_asortyment, " ");
	if (!$spacja==FALSE) {
		echo "<div class='alert alert-warning'><span class='glyphicon glyphicon-alert'></span>&nbsp<strong>Ostrzeżenie!</strong>&nbsp Podaj nazwę asortymentu bez znaku 'spacji'. Zamiast spacji możesz użyć podkreślnika '_'.</div>";
	}

	if ((!$nowy_asortyment==null) && ($spacja==FALSE))
	{
		//echo "Form działa<br / >";
			/* Łączymy się z serwerem */
			require_once ('polaczenie_z_baza.php');

			if (mysqli_connect_errno()) {

			printf("<div class='alert alert-danger'><span class='glyphicon glyph-thumb-down'></span>&nbsp<strong>Uwaga!</strong>&nbspBrak połączenia z serwerem MySQL. Kod błędu: %s\n</div>", mysqli_connect_error());

			} else
				{
					//Zapytanie do bazy o obecny asortyment i sprawdzamy czy nowy już czasami nie istnieje
					if ($stmt = $mysqli -> prepare("SELECT Asortyment FROM AsortymentSterylizacji"))
					{
						//echo "Zapytanie1 działa<br / >";
					$stmt -> execute();
					$stmt -> bind_result($Obecny_asortyment);
					$stmt -> store_result();
					}

					$Asortyment_wbazie=array();
					if ($stmt->num_rows > 0) {

					/* Wyciągamy dane z zapytania sql i zapisujemy do tablicy  */
    				while ($stmt->fetch()) {
					static $i=0;
					$Asortyment_wbazie[$i]=$Obecny_asortyment;
					$i++;
    				}
    				}

					//Sprawdzamy czy nowy asortyment już czasami nie istnieje w bazie
					$ilosc_asortymentu_wbazie=count($Asortyment_wbazie);
					$mozna_dodac="";

					//Jeśli w bazie nie ma żdnego asortymentu odrazu zezwalamy na zapis
					//pętla porównująca nowy asortyment z tymi w bazie nie działa przy pustej bazie!
					if(isset($Asortyment_wbazie[0])==FALSE)
					{
					$mozna_dodac=TRUE;
					}

					foreach ($Asortyment_wbazie as $key => $value)
					{
							for ($i=0; $i <$ilosc_asortymentu_wbazie+1 ; $i++)
							{

								if ($value==$nowy_asortyment)
									{
									$mozna_dodac=FALSE;
									break;
									}

								if ($i==$ilosc_asortymentu_wbazie)
									{
									$mozna_dodac=TRUE;
									}

							}

					}

					if ($mozna_dodac==FALSE) {
						echo "<div class='alert alert-warning'><span class='glyphicon glyphicon-alert'></span>&nbsp<strong>Ostrzeżenie!</strong>&nbsp Taki asortyment już istnieje w bazie danych. Podaj inną nazwę.</div>";
					}

					if ($mozna_dodac)
					{
						//echo "MOzna doadać działa<br / >";
					$nowy_asortyment=$mysqli ->real_escape_string($nowy_asortyment);

					//Dodajemy nowy asortyment do listy asortymentu
					if ($stmt = $mysqli -> prepare("INSERT INTO AsortymentSterylizacji (Asortyment) VALUES (?)"))
					{
						//echo "Wpisywanie do listy działa <br / >";
					$stmt -> bind_param("s",$nowy_asortyment);
					$stmt -> execute();

					if ($stmt -> affected_rows == 0 || $stmt -> affected_rows < 0 ||$stmt->affected_rows==NULL)
							{
								echo "<div class='alert alert-warning'><span class='glyphicon glyphicon-alert'></span>&nbsp<strong>Ostrzeżenie!</strong>&nbsp Nie dokonano zapisu. Możliwy błąd zapytania.</div>";
							}

							if ($stmt -> affected_rows > 0)
							{
								//echo "Kontrala zapisu do listy działa<br / >";
								//Tworzymy nową tabelę w bazie danych dla nowego asortymentu
								if ($stmt = $mysqli -> prepare(

								"CREATE TABLE `" . $nowy_asortyment . "`(
  Lp int(11) NOT NULL AUTO_INCREMENT,
  NrRaportu text CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL,
  Odbiorca text CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL,
  Klient text CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL,
  Data date NOT NULL,
  Godzina time NOT NULL,
  PredkoscZasobnika int(11) NOT NULL,
  PredkoscSluzy1 int(11) NOT NULL,
  PredkoscSluzy2 int(11) NOT NULL,
  PredkoscSterylizatora int(11) NOT NULL,
  TemperaturaSterylizacji int(11) NOT NULL,
  CisnienieSterylizacji int(11) NOT NULL,
  PredkoscSuszarki1 int(11) NOT NULL,
  NadmuchSuszarki1 int(11) NOT NULL,
  TemperaturaSuszarki1 int(11) NOT NULL,
  PredkoscSuszarki2 int(11) NOT NULL,
  NadmuchSuszarki2 int(11) NOT NULL,
  TemperaturaSuszarki2 int(11) NOT NULL,
  PredkoscChlodziarki int(11) NOT NULL,
  NadmuchChlodziarki int(11) NOT NULL,
  WilgotnoscPoczatkowa decimal(5,2) NOT NULL,
  WilgotnoscKoncowa decimal(5,2) NOT NULL,
  Sito text CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL,
  Odsiew int(11) NOT NULL,
  Metal int(11) NOT NULL,
  PartiaPoczatek int(11) NOT NULL,
  PartiaKoniec int(11) NOT NULL,
  LiczbaMasaWorkow text CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL,
  Wydajnosc int(11) NOT NULL,
  Obsada text CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL,
  Uwagi text CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL,
  WykonawcaPomiaru text CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL,
  Dokument varchar(200) CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL,
  Zdjecia varchar(200) CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL,
  OpisZdjecia text CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL,
  PRIMARY KEY (Lp)

								)"))
									{
										//echo "Tworzenie tabeli działa<br / >";
										$stmt -> execute();

										if ($stmt = $mysqli -> prepare("SELECT count(*)FROM information_schema.columns WHERE table_name = '".$nowy_asortyment."' "))
										{
											//echo "Utworzono tabele działa<br / >";
											$stmt -> execute();
											$stmt -> store_result();
											if ($stmt -> num_rows > 0)
											{
											echo '<div class="alert alert-success alert-dismissable fade in">
				  							<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
				 				 			<span class="glyphicon glyphicon--thumb-up"></span>&nbsp<strong>Sukces!</strong>&nbsp Dodano nowy asortyment sterylizacji.</div><br / >';
											}

										}
								$stmt->close();

									}



							}
					}
					}

				}

$mysqli -> close();
	}


}


?>
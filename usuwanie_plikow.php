<h2>Usuwanie zbędnych plików</h2>
<br / >
<div id="formularz">
		<div class="row" >
			<div class="form-group">
<form method="POST" action="index2.php?usuwanie_plikow=1.php">
	<fieldset>
					<legend>Usuń z serwera zdjęcia nienależące do raportów</legend>
		<span class="glyphicon glyphicon-remove"></span>&nbsp<input type="submit" value="Usuń zdjęcia" name="usun_zdjecia">

	</fieldset>
</form>
</div>
</div>
</div>
<br / >

<div id="formularz">
		<div class="row" >
			<div class="form-group">
<form method="POST" action="index2.php?usuwanie_plikow=1.php">
	<fieldset>
					<legend>Usuń z serwera pliki nienależące do raportów</legend>
		<span class="glyphicon glyphicon-remove"></span>&nbsp<input type="submit" value="Usuń pliki" name="usun_pliki">

	</fieldset>
</form>
</div>
</div>
</div>
<br / >

<?php
/*ob_end_clean();
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);*/
//Usuwamy zdjęcia
if ($_POST['usun_zdjecia']) {
	/* Łączymy się z serwerem */
require_once ('polaczenie_z_baza.php');

if (mysqli_connect_errno()) {

			printf("<div class='alert alert-danger'><span class='glyphicon glyphicon-thumb-down'></span>&nbsp<strong>Uwaga!</strong>&nbspBrak połączenia z serwerem MySQL. Kod błędu: %s\n</div>", mysqli_connect_error());

	} else
		{	//Zapytanie do bazy o obecny asortyment sterylizacji
			if ($stmt = $mysqli -> prepare("SELECT Asortyment FROM AsortymentSterylizacji ORDER BY Asortyment ASC"))
			{
			$stmt -> execute();
			$stmt -> bind_result($Obecny_asortyment);
			$stmt -> store_result();
			}

			$Asortyment_wbazie=array();

			if ($stmt->num_rows > 0) {
			/* Wyciągamy dane z zapytania sql i zapisujemy do tablicy  */
			$stmt -> data_seek(0);
    		while ($stmt->fetch()) {
			static $i=0;
			$Asortyment_wbazie[$i]=$Obecny_asortyment;
			$i++;
    		}
    		}
//print_r($Asortyment_wbazie);
			//Zapytanie o zdjęcia w bazie danych dotyczących raportów sterylizacji
			$Zdjecia_zbazy_steryl=array();

			for ($n=0; $n < count($Asortyment_wbazie) ; $n++) {
			if ($stmt = $mysqli -> prepare("SELECT DISTINCT Zdjecia FROM `$Asortyment_wbazie[$n]`"))
			{
			//$stmt -> bind_param("s",$nr_raportu);
			$stmt -> execute();
			$stmt -> bind_result($Zdjecie);
			$stmt -> store_result();
			$ilosc=$stmt->num_rows;

			if ($stmt->num_rows > 0) {
//echo "$stmt->num_rows <br / >";
				/* Wyciągamy dane z zapytania sql i zapisujemy do tablicy  */
				$stmt->data_seek(0);
				$m=0;//reset do pętli poniżej
    			while ($stmt->fetch()) {
					static $m=0;
					//$Zdjecia_zbazy_steryl=array("$Asortyment_wbazie[$n]"=>array($m=$Zdjecie));
					$Zdjecia_zbazy_steryl[$Asortyment_wbazie[$n]][$m]=$Zdjecie;
					$m++;
    			}
    			}

    			}
				}

			//print_r($Zdjecia_zbazy_steryl);

			if ($Zdjecia_zbazy_steryl==null) {
			echo '<div class="alert alert-info"><span class="glyphicon glyphicon-info-sign"></span>&nbsp<strong>Info!</strong>&nbsp Brak zdjęć w bazie raportów sterylizacji.</div>';

			}

		//Zapytanie do bazy o obecny asortyment suszu
			if ($stmt = $mysqli -> prepare("SELECT Asortyment FROM AsortymentSuszu ORDER BY Asortyment ASC"))
			{
			$stmt -> execute();
			$stmt -> bind_result($Obecny_asortyment);
			$stmt -> store_result();
			}

			$Asortyment_wbazie_suszu=array();

			if ($stmt->num_rows > 0) {
			/* Wyciągamy dane z zapytania sql i zapisujemy do tablicy  */
			$stmt -> data_seek(0);
    		while ($stmt->fetch()) {
			static $a=0;
			$Asortyment_wbazie_suszu[$a]=$Obecny_asortyment;
			$a++;
    		}
    		}
//print_r($Asortyment_wbazie_suszu);

			//Zapytanie o zdjęcia w bazie danych dotyczących raportów suszu
			$Zdjecia_zbazy_susz=array();

			for ($n=0; $n < count($Asortyment_wbazie_suszu) ; $n++) {
			if ($stmt = $mysqli -> prepare("SELECT DISTINCT Zdjecia FROM `$Asortyment_wbazie_suszu[$n]`"))
			{
				//echo "Zapytanie działa";
			//$stmt -> bind_param("s",$nr_raportu);
			$stmt -> execute();
			$stmt -> bind_result($Zdjecie);
			$stmt -> store_result();
			$ilosc=$stmt->num_rows;

			if ($stmt->num_rows > 0) {
	//echo "Ilosc zdjec w suszu: $stmt->num_rows <br / >";
				/* Wyciągamy dane z zapytania sql i zapisujemy do tablicy  */
				$stmt->data_seek(0);
				$m=0;//reset do pętli poniżej
    			while ($stmt->fetch()) {
					static $m=0;
					//$Zdjecia_zbazy_steryl=array("$Asortyment_wbazie[$n]"=>array($m=$Zdjecie));
					$Zdjecia_zbazy_susz[$Asortyment_wbazie_suszu[$n]][$m]=$Zdjecie;
					$m++;
    			}
    			}


    			}
				}

			//print_r($Zdjecia_zbazy_susz);

			if ($Zdjecia_zbazy_susz==null) {
			echo '<div class="alert alert-info"><span class="glyphicon glyphicon-info-sign"></span>&nbsp<strong>Info!</strong>&nbsp Brak zdjęć w bazie raportów suszenia.</div>';

			}

			$Zdjecia_zbazy=array_merge($Zdjecia_zbazy_steryl,$Zdjecia_zbazy_susz);

				//echo "Lista zdjeć w bazie <br / >";
				//print_r($Zdjecia_zbazy);

			//Sprawdzamy zdjęcia zapisane na serwerze

			//Ścieżki do katalogów ze zdjęciami
			$katalog_zdjec_steryl = "grafika/zdjecia_raporty_sterylizacji/";
			$katalog_zdjec_suszenia = "grafika/zdjecia_raporty_suszenia/";

			// Sprawdzamy katalogi i zapisujemy wyniki-liste zdjec do zmiennej, która jest tablicą
			$zdjecia_steryl = scandir($katalog_zdjec_steryl);
			$zdjecia_suszenie = scandir($katalog_zdjec_suszenia);

			$Zdjecia_zserwera = array_merge($zdjecia_steryl,$zdjecia_suszenie);


			//echo "<br / >Lista zdjeć na serwerze <br / >";
			//print_r($Zdjecia_zserwera);


			//Porównanie nazw zdjęć i kasowanie zdjęć z serwera jeśli nie ma ich w bazie danych
			$ilosc_usunietych_zdjec="";
			$ilosc_zdjec_wbazie=count($Zdjecia_zbazy, COUNT_RECURSIVE) - count($Zdjecia_zbazy);
			$ilosc_zdjec_naserwerze=count($Zdjecia_zserwera)-4;
			//echo "<br / >Ilość zdjęć w bazie: $ilosc_zdjec_wbazie <br / >";
			//echo "<br / > Ilość zdjęć na serwerze: $ilosc_zdjec_naserwerze <br / >";

			for ($z=0; $z <count($Zdjecia_zserwera) ; $z++)
				{
					$y=0;//reset licznika

				//echo "Zdjecie z serwera: $Zdjecia_zserwera[$z] <br / >";

					foreach ($Zdjecia_zbazy as $asort => $zdjecia){
						static $y=0;
						//$ilosc_zdjec_dla_asortymentu=count($zdjecia);
						//echo "$ilosc_zdjec_dla_asortymentu <br / >";

					for ($x=0; $x < count($zdjecia) ; $x++) {

				//echo "Zdjecie z bazy:$zdjecia[$x] <br / >";

					if ($Zdjecia_zserwera[$z]==$zdjecia[$x]) {
					break;
					}

						else {

							$y++;
							//echo "Licznik: $y ..<br / >";

							if ($y==$ilosc_zdjec_wbazie) {
								$zdjecie_do_usuniecia = "$katalog_zdjec_steryl"."$Zdjecia_zserwera[$z]";
								$zdjecie_do_usuniecia2 = "$katalog_zdjec_suszenia"."$Zdjecia_zserwera[$z]";
								static $licznik="";
								unlink($zdjecie_do_usuniecia);
								unlink($zdjecie_do_usuniecia2);
								$licznik++;
								$ilosc_usunietych_zdjec=$licznik-4; //minus 4 bo scandir zwraca na początku znaki kropek, które również są usuwane, kazdy scandir 2 znaki

							}

							}
				}
				}

			}

			if ($ilosc_usunietych_zdjec>0) {

			printf ("<div class='alert alert-success alert-dismissable fade in'>
					<a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>
					<span class='glyphicon glyphicon-thumb-up'></span>&nbsp<strong>Sukces!</strong>&nbsp Liczba usuniętych zdjęć: %s. </div><br / >",$ilosc_usunietych_zdjec);
			}
			else{
				echo '<div class="alert alert-info"><strong>Info!</strong>&nbsp Brak wolnych zdjęć nie przypisanych do bazy danych.</div>';
				}



		}
}

//Usuwamy pliki
if ($_POST['usun_pliki']) {
	/* Łączymy się z serwerem */
require_once ('polaczenie_z_baza.php');

if (mysqli_connect_errno()) {

			printf("<div class='alert alert-danger'><span class='glyphicon glyphicon-thumb-down'></span>&nbsp<strong>Uwaga!</strong>&nbspBrak połączenia z serwerem MySQL. Kod błędu: %s\n</div>", mysqli_connect_error());

	} else
		{
			if ($stmt = $mysqli -> prepare("SELECT Asortyment FROM AsortymentSterylizacji ORDER BY Asortyment ASC"))
			{
			$stmt -> execute();
			$stmt -> bind_result($Obecny_asortyment);
			$stmt -> store_result();
			}

			$Asortyment_wbazie=array();

			if ($stmt->num_rows > 0) {
			/* Wyciągamy dane z zapytania sql i zapisujemy do tablicy  */
			$stmt -> data_seek(0);
    		while ($stmt->fetch()) {
			static $i=0;
			$Asortyment_wbazie[$i]=$Obecny_asortyment;
			$i++;
    		}
    		}

			//Zapytanie o pliki w bazie danych
			$Dokumenty_zbazy=array();

			for ($n=0; $n < count($Asortyment_wbazie) ; $n++) {
			if ($stmt = $mysqli -> prepare("SELECT DISTINCT Dokument FROM `$Asortyment_wbazie[$n]`"))
			{
			//$stmt -> bind_param("s",$nr_raportu);
			$stmt -> execute();
			$stmt -> bind_result($Dokument);
			$stmt -> store_result();
			$ilosc=$stmt->num_rows;

			if ($stmt->num_rows > 0) {
//echo "$stmt->num_rows <br / >";
				/* Wyciągamy dane z zapytania sql i zapisujemy do tablicy  */
				$stmt->data_seek(0);
				$m=0;//reset do pętli poniżej
    			while ($stmt->fetch()) {
					static $m=0;
					//$Zdjecia_zbazy_steryl=array("$Asortyment_wbazie[$n]"=>array($m=$Zdjecie));
					$Dokumenty_zbazy[$Asortyment_wbazie[$n]][$m]=$Dokument;
					$m++;
    			}
    			}

    			}
				}

			//print_r($Dokumenty_zbazy);

			//Sprawdzamy dokumenty zapisane na serwerze

			//Ścieżka do katalogu ze zdjęciami
			$katalog_dokumentow = "dokumenty/raporty_sterylizacji/";

			// Sprawdzamy katalog i zapisujemy wynik-liste zdjec do zmiennej, która jest tablicą tablicy
			$Dokumenty_zserwera = scandir($katalog_dokumentow);

			//echo "Lista dokumentów na serwerze <br / >";
			//print_r($Dokumenty_zserwera);


			//Porównanie nazw zdjęć i kasowanie zdjęć z serwera jeśli nie ma ich w bazie danych
			$ilosc_usunietych_dokumentow="";
			$ilosc_dokumentow_wbazie=count($Dokumenty_zbazy, COUNT_RECURSIVE) - count($Dokumenty_zbazy);
			for ($z=0; $z <count($Dokumenty_zserwera) ; $z++)
			 {
			 	$y=0;
			 	foreach ($Dokumenty_zbazy as $asort => $dokumenty) {
					static $y=0;


				for ($x=0; $x <count($dokumenty) ; $x++) {

					if ($Dokumenty_zserwera[$z]==$dokumenty[$x]) {
					break;
					}
						else {
							$y++;
							if ($y==$ilosc_dokumentow_wbazie) {
								$dokument_do_usuniecia = "$katalog_dokumentow"."$Dokumenty_zserwera[$z]";
								static $licznik="";
								unlink($dokument_do_usuniecia);
								$licznik++;
								$ilosc_usunietych_dokumentow=$licznik-2; //minus 2 bo scandir zwraca na początku znaki kropek, które również są usuwane

							}
							}
				}
			 }
			}

			if ($ilosc_usunietych_dokumentow>0) {

			printf ("<div class='alert alert-success alert-dismissable fade in'>
					<a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>
					<span class='glyphicon glyphicon-thumb-up'></span>&nbsp<strong>Sukces!</strong>&nbsp Liczba usuniętych plików-dokumentów: %s. </div><br / >",$ilosc_usunietych_dokumentow);
			}
			else{
				echo '<div class="alert alert-info"><span class="glyphicon glyphicon-info-sign"></span>&nbsp<strong>Info!</strong>&nbsp Brak wolnych dokumentów nie przypisanych do bazy danych.</div>';
				}


		}
}


?>

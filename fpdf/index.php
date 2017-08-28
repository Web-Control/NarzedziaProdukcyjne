<!DOCTYPE html>
<html>
	<head>
		<title>Raporty Wilgotności</title>
		<META http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<link rel="stylesheet" href="arkusz.css" type="text/css">

	</head>
	<body>

		<header>
			<h1>Raporty Wilgotności</h1>
		</header>

		<div id="tresc">

			<form method="post" action="index.php">
				<fieldset>
					<legend>
						Dodawanie danych do raportów:
					</legend>

					<label >Asortyment</label>
					<select name="asortyment" required>
						<option value="Marchew10x10x2">Marchew 10x10x2</option>
						<option value="Marchew10x10x10">Marchew 10x10x10</option>
						<option value="Marchew4x4x4">Marchew 4x4x4</option>
						<option value="Fasolka 12.5">Fasolka 12.5</option>
						<option value="Cukinia">Cukinia</option>
						<option value="Burak10x10x2">Burak 10x10x2</option>
						<option value="BurakPasek">Burak Pasek</option>
						<option value="Pasternak10x10x2">Pasternak 10x10x2</option>
						<option value="PasternakPasek">Pasternak Pasek</option>
						<option value="Seler10x10x2">Seler 10x10x2</option>
						<option value="NatkaSelera">Natka Selera</option>
						<option value="Dynia10x10x2">Dynia 10x10x2</option>
						<option value="Ziemniak">Ziemniak</option>
					</select>
					&nbsp&nbsp

					<label>Nr Suszarni</label>
					<select name="nr_suszarni" required>
						<option value="1">1</option>
						<option value="2">2</option>
						<option value="3">3</option>
						<option value="4">4</option>
						<option value="5">5</option>
					</select>
					<br / >
					<br / >

					<label>Data</label>
					<input type="date" name="data" required/>
					&nbsp&nbsp

					<label >Godzina</label>
					<input type="time" name="godzina" required/>
					<br / >
					<br / >

					<label >Numer Dostawcy</label>
					<br / >
					<input type="number" name="dostawca" required/>
					<br / >
					<br / >

					<label >Prędkość Blanszownika</label>
					<br / >
					<input type="number" name="predkosc_blanszownika" />
					<br / >
					<br / >

					<label >Temperatura Blanszownika</label>
					<br / >
					<input type="number" name="temperatura_blanszownika" />
					<br / >
					<br / >

					<label >Prędkość Siatki nr 7</label>
					<br / >
					<input type="number" name="siatka7" required/>
					<br / >
					<br / >

					<label >Prędkość Siatki nr 6</label>
					<br / >
					<input type="number" name="siatka6" required/>
					<br / >
					<br / >

					<label >Prędkość Siatki nr 5</label>
					<br / >
					<input type="number" name="siatka5" required/>
					<br / >
					<br / >

					<label >Prędkość Siatki nr 4</label>
					<br / >
					<input type="number" name="siatka4" required/>
					<br / >
					<br / >

					<label >Prędkość Siatki nr 3</label>
					<br / >
					<input type="number" name="siatka3" required/>
					<br / >
					<br / >

					<label >Prędkość Siatki nr 2</label>
					<br / >
					<input type="number" name="siatka2" required/>
					<br / >
					<br / >

					<label >Prędkość Siatki nr 1</label>
					<br / >
					<input type="number" name="siatka1" required/>
					<br / >
					<br / >

					<label >Górna Temperatura Suszarni</label>
					<br / >
					<input type="number" name="temperatura_gorna" required/>
					<br / >
					<br / >

					<label >Dolna Temperatura Suszarni</label>
					<br / >
					<input type="number" name="temperatura_dolna" required/>
					<br / >
					<br / >

					<label >Wilgotność</label>
					<br / >
					<input type="decimal" name="wilgotnosc" required/>
					<br / >
					<br / >

					<label >Osoba dokonująca Pomiaru</label>
					<br / >
					<select name="osoba_odpowiedzialna" required>
						<option value="Magdalena Kubrowska">Magdalena Kubrowska</option>
						<option value="Andrzej Komsta">Andrzej Komsta</option>
						<option value="Szymon Chomej">Szymon Chomej</option>
						<option value="Krzysztof Dąbrowski">Krzysztof Dąbrowski</option>
						<option value="Mariusz Tumiel">Mariusz Tumiel</option>
						<option value="Piotr Mielczarczyk">Piotr Mielczarczyk</option>
						<option value="Mariusz Ślązak">Mariusz Ślązak</option>
					</select>
					<br / >
					<br / >
					<br / >
					<input type="submit" value="Zapisz" name="submit">
				</fieldset>
			</form>
			<br / >

			<?php

			/*Odbieramy dane z formularza*/
			$asortyment = $_POST['asortyment'];
			$nr_suszarni = $_POST['nr_suszarni'];
			$wilgotnosc = $_POST['wilgotnosc'];
			$data = $_POST['data'];
			$godzina = $_POST['godzina'];
			$dostawca = $_POST['dostawca'];
			$predkosc_blanszownika = $_POST['predkosc_blanszownika'];
			$temperatura_blanszownika = $_POST['temperatura_blanszownika'];
			$v_siatka7 = $_POST['siatka7'];
			$v_siatka6 = $_POST['siatka6'];
			$v_siatka5 = $_POST['siatka5'];
			$v_siatka4 = $_POST['siatka4'];
			$v_siatka3 = $_POST['siatka3'];
			$v_siatka2 = $_POST['siatka2'];
			$v_siatka1 = $_POST['siatka1'];
			$temp_gorna = $_POST['temperatura_gorna'];
			$temp_dolna = $_POST['temperatura_dolna'];
			$odpowiedzialny = $_POST['osoba_odpowiedzialna'];

			/* Łączymy się z serwerem */

			if (isset($_POST['submit']) && $wilgotnosc && $data && $godzina && $dostawca && $odpowiedzialny && $v_siatka1 && $temp_gorna && $temp_dolna) {

				$mysqli = new mysqli('mysql530int.cp.az.pl', 'u6001900_szymon', 'mNa5YWLL', 'db6001900_RaportyWilgotnosci');

				if (mysqli_connect_errno()) {

					printf("Brak połączenia z serwerem MySQL. Kod błędu: %s\n", mysqli_connect_error());
				} else {
					printf('Połączono z bazą danych <br / ><br / >');
					printf("<b>Asortyment:</b>&nbsp %s &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp <b>Nr Suszarni:</b>&nbsp %s &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp <b>Data:</b>&nbsp %s <br / ><br / >", $asortyment, $nr_suszarni, $data);

					/* Utworzenie zapytania */
					$wpis = "INSERT INTO " . $asortyment . " (NrSuszarni,Data,Czas,NumerDostawcy,PredkoscBlanszownika,TemperaturaBlanszownika,PredkoscSiatkiNr7,PredkoscSiatkiNr6,PredkoscSiatkiNr5,PredkoscSiatkiNr4,PredkoscSiatkiNr3,PredkoscSiatkiNr2,PredkoscSiatkiNr1,TemperaturaGora,TemperaturaDol,Wilgotnosc,WykonawcaPomiaru) VALUES ('$nr_suszarni','$data','$godzina','$dostawca','$predkosc_blanszownika','$temperatura_blanszownika','$v_siatka7','$v_siatka6','$v_siatka5','$v_siatka4','$v_siatka3','$v_siatka2','$v_siatka1','$temp_gorna','$temp_dolna','$wilgotnosc','$odpowiedzialny')";

					/*Przesłanie zapytania do bazy*/
					$zapis = $mysqli -> query($wpis);

					/* Utworzenie zapytania */

					$query = "SELECT Czas,NumerDostawcy,PredkoscBlanszownika,TemperaturaBlanszownika,PredkoscSiatkiNr7,PredkoscSiatkiNr6,PredkoscSiatkiNr5,PredkoscSiatkiNr4,PredkoscSiatkiNr3,PredkoscSiatkiNr2,PredkoscSiatkiNr1,TemperaturaGora,TemperaturaDol,Wilgotnosc,WykonawcaPomiaru FROM " . $asortyment . " WHERE Data='" . $data . "' AND NrSuszarni='" . $nr_suszarni . "' ORDER BY Czas";

					/*Przesłanie zapytania do bazy*/
					$result = $mysqli -> query($query);

					/* Przetwarzanie wierszy wyniku zapytania */
					$num_rows = mysqli_num_rows($result);
					if ($num_rows > 0) {
						echo '<div id="tabela_wielkosci">Godzina<br / >Dostawca<br / >Pręd Blansz<br / >Temp Blans<br / >Siatka nr 7<br / >Siatka nr 6<br / >Siatka nr 5<br / >Siatka nr 4<br / >Siatka nr 3<br / >Siatka nr 2<br / >Siatka nr 1<br / >Temp. Góra<br / >Temp. Dół<br / >Wilgotność<br / >Osoba odpowiedzi.<br / ></div>';

						while ($row = $result -> fetch_object()) {

							$Czas = $row -> Czas;
							$Dostawca = $row -> NumerDostawcy;
							$Predkosc_Blanszownika = $row -> PredkoscBlanszownika;
							$Temperatura_Blanszownika = $row -> TemperaturaBlanszownika;
							$V_Siatka7 = $row -> PredkoscSiatkiNr7;
							$V_Siatka6 = $row -> PredkoscSiatkiNr6;
							$V_Siatka5 = $row -> PredkoscSiatkiNr5;
							$V_Siatka4 = $row -> PredkoscSiatkiNr4;
							$V_Siatka3 = $row -> PredkoscSiatkiNr3;
							$V_Siatka2 = $row -> PredkoscSiatkiNr2;
							$V_Siatka1 = $row -> PredkoscSiatkiNr1;
							$Temp_Gorna = $row -> TemperaturaGora;
							$Temp_Dolna = $row -> TemperaturaDol;
							$Wilgotnosc = $row -> Wilgotnosc;
							$Odpowiedzialny = $row -> WykonawcaPomiaru;

							printf("<div id='tabela_wynikow'>%s. <br / >%s <br / >%s Hz<br / >%s &deg;C<br / >%s Hz<br / > %s Hz<br / >%s Hz<br / > %s Hz<br / > %s Hz<br / > %s Hz<br / >%s Hz<br / >%s &deg;C<br / > %s &deg;C<br / >%s %% <br / > %s.</div>", $Czas, $Dostawca, $Predkosc_Blanszownika, $Temperatura_Blanszownika, $V_Siatka7, $V_Siatka6, $V_Siatka5, $V_Siatka4, $V_Siatka3, $V_Siatka2, $V_Siatka1, $Temp_Gorna, $Temp_Dolna, $Wilgotnosc, $Odpowiedzialny);
						}

						/* Usuwamy wynik zapytania z pamięci */

						$result -> close();

						/* Zamykamy połączenie z bazą */

						$mysqli -> close();

					} else {
						echo 'Brak danych w bazie danych';
					}
				}
			}
			?>
			<br / >
			<br / >
			<form method="post" action="index.php">
				<fieldset>
					<legend>
						Pobieranie raportu:
					</legend>

					<label >Asortyment</label>
					<br / >
					<select name="asortyment_suszu" required>
						<option value="Marchew10x10x2">Marchew 10x10x2</option>
						<option value="Marchew10x10x10">Marchew 10x10x10</option>
						<option value="Marchew4x4x4">Marchew 4x4x4</option>
						<option value="Fasolka 12.5">Fasolka 12.5</option>
						<option value="Cukinia">Cukinia</option>
						<option value="Burak10x10x2">Burak 10x10x2</option>
						<option value="BurakPasek">Burak Pasek</option>
						<option value="Pasternak10x10x2">Pasternak 10x10x2</option>
						<option value="PasternakPasek">Pasternak Pasek</option>
						<option value="Seler10x10x2">Seler 10x10x2</option>
						<option value="NatkaSelera">Natka Selera</option>
						<option value="Dynia10x10x2">Dynia 10x10x2</option>
						<option value="Ziemniak">Ziemniak</option>
					</select>
					<br / >
					<br / >

					<label>Nr Suszarni</label>
					<select name="nr_suszarni" required>
						<option value="1">1</option>
						<option value="2">2</option>
						<option value="3">3</option>
						<option value="4">4</option>
						<option value="5">5</option>
					</select>
					<br / >
					<br / >

					<label >Data</label>
					<br / >
					<input type="date" name="data_raportu" value="rrrr-mm-dd" required/>
					<br / >
					<br / >
					<input type="submit" value="Odczytaj" name="submit2">
				</fieldset>
			</form>

			<?php
			/*Odbieramy dane z formularza*/
			$data_raportu = $_POST['data_raportu'];
			$asortyment_suszu = $_POST['asortyment_suszu'];
			$nr_suszarni = $_POST['nr_suszarni'];

			/* Łączymy się z serwerem */
			if (isset($_POST['submit2']) && $data_raportu && $nr_suszarni) {

				$mysqli = new mysqli('mysql530int.cp.az.pl', 'u6001900_szymon', 'mNa5YWLL', 'db6001900_RaportyWilgotnosci');

				if (mysqli_connect_errno()) {

					printf("Brak połączenia z serwerem MySQL. Kod błędu: %s\n", mysqli_connect_error());
				} else {
					printf('Połączono z bazą danych <br / ><br / >');
					printf("<b>Asortyment:</b>&nbsp %s &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp <b>Nr Suszarni:</b>&nbsp %s &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp <b>Data:</b>&nbsp %s <br / ><br / >", $asortyment_suszu, $nr_suszarni, $data_raportu);

					/* Utworzenie zapytania */
					$query = "SELECT Czas,NumerDostawcy,PredkoscBlanszownika,TemperaturaBlanszownika,PredkoscSiatkiNr7,PredkoscSiatkiNr6,PredkoscSiatkiNr5,PredkoscSiatkiNr4,PredkoscSiatkiNr3,PredkoscSiatkiNr2,PredkoscSiatkiNr1,TemperaturaGora,TemperaturaDol,Wilgotnosc,WykonawcaPomiaru FROM " . $asortyment_suszu . " WHERE Data='" . $data_raportu . "' AND NrSuszarni='" . $nr_suszarni . "' ORDER BY Czas";

					/*Przesłanie zapytania do bazy*/
					$result = $mysqli -> query($query);

					/* Przetwarzanie wierszy wyniku zapytania */
					$num_rows = mysqli_num_rows($result);
					if ($num_rows > 0) {
						echo '<div id="tabela_wielkosci">Godzina<br / >Dostawca<br / >Pręd Blansz<br / >Temp Blans<br / >Siatka nr 7<br / >Siatka nr 6<br / >Siatka nr 5<br / >Siatka nr 4<br / >Siatka nr 3<br / >Siatka nr 2<br / >Siatka nr 1<br / >Temp. Góra<br / >Temp. Dół<br / >Wilgotność<br / >Osoba odpowiedzi.<br / ></div>';

						while ($row = $result -> fetch_object()) {

							$Czas = $row -> Czas;
							$Dostawca = $row -> NumerDostawcy;
							$Predkosc_Blanszownika = $row -> PredkoscBlanszownika;
							$Temperatura_Blanszownika = $row -> TemperaturaBlanszownika;
							$V_Siatka7 = $row -> PredkoscSiatkiNr7;
							$V_Siatka6 = $row -> PredkoscSiatkiNr6;
							$V_Siatka5 = $row -> PredkoscSiatkiNr5;
							$V_Siatka4 = $row -> PredkoscSiatkiNr4;
							$V_Siatka3 = $row -> PredkoscSiatkiNr3;
							$V_Siatka2 = $row -> PredkoscSiatkiNr2;
							$V_Siatka1 = $row -> PredkoscSiatkiNr1;
							$Temp_Gorna = $row -> TemperaturaGora;
							$Temp_Dolna = $row -> TemperaturaDol;
							$Wilgotnosc = $row -> Wilgotnosc;
							$Odpowiedzialny = $row -> WykonawcaPomiaru;

							printf("<div id='tabela_wynikow'>%s. <br / >%s <br / >%s Hz<br / >%s &deg;C<br / >%s Hz<br / > %s Hz<br / >%s Hz<br / > %s Hz<br / > %s Hz<br / > %s Hz<br / >%s Hz<br / >%s &deg;C<br / > %s &deg;C<br / >%s %% <br / > %s.</div>", $Czas, $Dostawca, $Predkosc_Blanszownika, $Temperatura_Blanszownika, $V_Siatka7, $V_Siatka6, $V_Siatka5, $V_Siatka4, $V_Siatka3, $V_Siatka2, $V_Siatka1, $Temp_Gorna, $Temp_Dolna, $Wilgotnosc, $Odpowiedzialny);

						}

						/*Tworzenie pliku raportu
						 $raport = fopen('raport.txt', 'w+');

						 $zawartosc = "Coś tam";

						 fwrite($raport, $zawartosc);

						 printf('<br / ><br / ><a href="raport.txt">Pobierz raport</a>');
						 */

						/* Usuwamy wynik zapytania z pamięci */

						$result -> close();

						/* Zamykamy połączenie z bazą */

						$mysqli -> close();

					} else {
						echo 'Brak danych w bazie danych';
					}

				}
			}
			?>
			<br / >
			<br / >
			<form>
					<input type="submit" value="Pobierz raport PDF" name="pdf">		
			</form>
			<?php
			
			if (isset($_POST['pdf'])) {
					
				require ('fpdf/fpdf.php');
				
				$pdf = new FPDF();
				$pdf -> AddPage();
				$pdf -> SetFont('Arial','B',16);
				$pdf -> Cell(40,10,'Hello World!');
				$pdf -> Output();
				 	
			};
			?>
		</div>

		<footer>
			Created by Web-Control 2016
		</footer>

	</body>
</html>
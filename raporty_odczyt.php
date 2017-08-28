<?php
// Start the session
session_start();
if (isset($_GET['wyloguj']) == 1) {
						$_SESSION['zalogowany'] = false;
						session_destroy();
					}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
   <? include('head.php'); ?>
</head>
<body>

<? include('header.php'); ?>

<div class="container-fluid text-center">
  <div class="row content">
    <div class="col-sm-2 sidenav">
     <? include('boczne_menu.php'); ?>
    </div>
    <div class="col-sm-8 text-left">
      <h1>Raport z procesu suszenia</h1>
      <ul class="nav nav-tabs">
  <li><a href="raporty.php">Tworzenie raportu</a></li>
  <li class="active"><a href="raporty_odczyt.php">Odczyt raportu</a></li>
  <li><a href="raporty_pobierz.php">Pobór raportu</a></li>

</ul>
<br />
     <form method="post" action="raporty_odczyt.php">
				<fieldset>
					<legend>
						Odczyt raportu:
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

					<label >Data</label>
					<br / >
					<input type="date" name="data_raportu" value="rrrr-mm-dd" required/>
					<br / >
					<br / >

					<label>Nr Suszarni</label>
					<br / >
					<select name="nr_suszarni"  min="1" max="5" required>
						<option value="1">1</option>
						<option value="2">2</option>
						<option value="3">3</option>
						<option value="4">4</option>
						<option value="5">5</option>
					</select>
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

			/*Sprawdzamy czy formularz został wypełniony*/
			if (isset($_POST['submit2']) && $data_raportu && $nr_suszarni) {

				/*Ustawiamy zmienne sesji do poźniejszego tworzenia raportu pdf*/
				$_SESSION["data_raportu"] = $data_raportu;
				$_SESSION["asortyment_suszu"] = $asortyment_suszu;
				$_SESSION["nr_suszarni"] = $nr_suszarni;

				/* Łączymy się z serwerem */
				$mysqli = new mysqli('mysql530int.cp.az.pl', 'u6001900_szymon', 'mNa5YWLL', 'db6001900_RaportyWilgotnosci');

				if (mysqli_connect_errno()) {

					printf("Brak połączenia z serwerem MySQL. Kod błędu: %s\n", mysqli_connect_error());
				} else {
					printf('<div class="infopozytyw">Połączono z bazą danych </div><br / ><br / >');
					printf("<b>Asortyment:</b>&nbsp %s &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp <b>Data:</b>&nbsp %s &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp <b>Nr Suszarni:</b>&nbsp %s <br / ><br / >", $asortyment_suszu, $data_raportu, $nr_suszarni);

					/* Utworzenie zapytania */
					$query = "SELECT Czas,NumerDostawcy,PredkoscBlanszownika,TemperaturaBlanszownika,PredkoscSiatkiNr7,PredkoscSiatkiNr6,PredkoscSiatkiNr5,PredkoscSiatkiNr4,PredkoscSiatkiNr3,PredkoscSiatkiNr2,PredkoscSiatkiNr1,TemperaturaGora,TemperaturaDol,Wilgotnosc,WykonawcaPomiaru FROM " . $asortyment_suszu . " WHERE Data='" . $data_raportu . "' AND NrSuszarni='" . $nr_suszarni . "' ORDER BY Czas";

					/*Przesłanie zapytania do bazy*/
					$result = $mysqli -> query($query);

					/* Przetwarzanie wierszy wyniku zapytania */
					$num_rows = mysqli_num_rows($result);
					if ($num_rows > 0) {
						echo '<div id="tabela_wielkosci">Godzina<br / >Dostawca<br / >Pręd Blansz<br / >Temp Blans<br / >Siatka nr 7<br / >Siatka nr 6<br / >Siatka nr 5<br / >Siatka nr 4<br / >Siatka nr 3<br / >Siatka nr 2<br / >Siatka nr 1<br / >Temp. Góra<br / >Temp. Dół<br / >Wilgotność<br / >Osoba<br / ></div>';

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

							printf("<div id='tabela_wynikow'>%s. <br / >%s <br / >%s Hz<br / >%s &deg;C<br / >%s Hz<br / > %s Hz<br / >%s Hz<br / > %s Hz<br / > %s Hz<br / > %s Hz<br / >%s Hz<br / >%s &deg;C<br / > %s &deg;C<br / >%s %% <br / > %s</div>", $Czas, $Dostawca, $Predkosc_Blanszownika, $Temperatura_Blanszownika, $V_Siatka7, $V_Siatka6, $V_Siatka5, $V_Siatka4, $V_Siatka3, $V_Siatka2, $V_Siatka1, $Temp_Gorna, $Temp_Dolna, $Wilgotnosc, $Odpowiedzialny);

						}
						echo "<form method='post' action='raportpdf.php' target='_blank'><input type='submit' value='Pobierz raport PDF' name='pdf'></form>";

						/* Usuwamy wynik zapytania z pamięci */

						$result -> close();

						/* Zamykamy połączenie z bazą */

						$mysqli -> close();

					} else {
						echo '<div class="alert alert-warning">Brak danych w bazie danych</div>';
					}

				}
			}
			?>
			<br / >
			<br / >


    </div>
    <div class="col-sm-2 sidenav">
      <? include ('boczne_dodatki.php'); ?>
    </div>
  </div>
</div>

<footer class="container-fluid text-center">
   <? include('footer.php'); ?>
</footer>

</body>
</html>

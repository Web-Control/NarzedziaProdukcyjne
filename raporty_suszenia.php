
<?php/*
if ($_SESSION['sukces']) {
	echo '<div class="alert alert-success alert-dismissable fade in">
		<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
		<span class="glyphicon glyphicon-thumbs-up"></span>&nbsp;<strong>Sukces!</strong>&nbsp Zapisano dane. Poniżej znajduje się twój raport. </div><br / >';
	
	//Resetujemy komunikaty	
	$_SESSION['sukces']=FALSE;
} 
 * */
?>

	<h1>Raport z procesu suszenia</h1>
	      <ul class="nav nav-tabs">
	  <li class="active"><a href="index2.php?raporty_suszenia=1&reset=1">Tworzenie</a></li>
	  <li><a href="index2.php?raporty_suszenia_odczyt=1">Odczyt</a></li>
	  <li><a href="index2.php?raporty_suszenia_pobierz=1">Pobór</a></li>
	  <li><a href="index2.php?statystyki_suszenia=1">Wykresy</a></li>
	</ul>
	<br / >


<div id="formularz">
		<div class="row" >
			<div class="form-group">
     <form method="post" action="index2.php?raporty_suszenia=1">
				<fieldset>
					<legend>
					Pobierz paramatery
					</legend>

				<div class="row">
					<div class="col-sm-4">
					<label >Asortyment</label>
					<select class="form-control" name="asortyment" required>
						<?php
			//Wyswietlamy wybór asortymentu dostępnego w bazie danych
			/* Łączymy się z serwerem */
			require_once ('polaczenie_z_baza.php');

			if (mysqli_connect_errno()) {

			printf("<div class='alert alert-danger'><strong>Uwaga!</strong>&nbspBrak połączenia z serwerem MySQL. Kod błędu: %s\n</div>", mysqli_connect_error());

			} else
				{
					//Zapytanie do bazy o obecny asortyment
					if ($stmt = $mysqli -> prepare("SELECT Asortyment FROM AsortymentSuszu ORDER BY Asortyment ASC"))
					{
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

					foreach ($Asortyment_wbazie as $key => $value) {

						printf("<option value='%s'>%s</option>",$value,$value);
					}

				}
?>
					</select>
					</div>

					<div class="col-sm-4">
					<label>Nr Suszarni</label>
					<select class="form-control" name="nr_suszarni"  min="1" max="5" required>
						<option value="1">1</option>
						<option value="2">2</option>
						<option value="3">3</option>
						<option value="4">4</option>
						<option value="5">5</option>
					</select>
					</div>

					<div class="row">
					<div class="col-sm-4">
					<label >Żądana wilgotność</label>
					<input class="form-control"  type="number" min="1" max="10" step="0.5" maxlength="5"  name="wilgotnosc" required/>
					</div>

				</div>

				</div>
					<hr></hr>

					<span class="glyphicon glyphicon-export"></span>&nbsp<input type="submit" value="Pobierz parametry" name="parametry"><br / ><br / >

				</fieldset>
			</form>
		</div>
	</div>
</div>

<?php
//Pobieramy parametry suszarni dla żądanej wilgotności

	/*ob_end_clean();
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);*/

			if (isset($_POST['parametry'])) {

				//funkcja filtrująca dane
				function filtruj($zmienna) {
					$data = trim($zmienna);
					//usuwa spacje, tagi
					$data = stripslashes($zmienna);
					//usuwa slashe
					$data = htmlspecialchars($zmienna);
					//zamienia tagi html na czytelne znaki aby w formularzu nie wpisać szkodliwego kodu
					return $zmienna;
				}

				/*Odbieramy dane z formularza*/
				$asortyment = filtruj($_POST['asortyment']);
				$nr_suszarni = filtruj($_POST['nr_suszarni']);
				$wilgotnosc = filtruj($_POST['wilgotnosc']);

				//* Łączymy się z serwerem */
				require_once ('polaczenie_z_baza.php');

				if (mysqli_connect_errno()) {

					printf("<div class='alert alert-danger'><strong>Uwaga!</strong>&nbsp Brak połączenia z serwerem MySQL. Kod błędu: %s\n</div>", mysqli_connect_error());
				} else {
					//usuwamy specjalne znaki takie jak '," aby nie możnabyło wpisać ich z formularza do zapytania SQL
					$asortyment = $mysqli -> real_escape_string($asortyment);
					$nr_suszarni = $mysqli -> real_escape_string($nr_suszarni);
					$wilgotnosc = $mysqli -> real_escape_string($wilgotnosc);

					$wilgotnosc1 = $wilgotnosc - 0.25;
					$wilgotnosc2 = $wilgotnosc + 0.25;

					if ($stmt = $mysqli -> prepare("SELECT Data,Czas,PredkoscBlanszownika,TemperaturaBlanszownika,PredkoscSiatkiNr7,PredkoscSiatkiNr6,PredkoscSiatkiNr5,PredkoscSiatkiNr4,PredkoscSiatkiNr3,PredkoscSiatkiNr2,PredkoscSiatkiNr1,CzasSuszenia,TemperaturaGora,TemperaturaDol,Wilgotnosc,WykonawcaPomiaru FROM `" . $asortyment . "`  WHERE NrSuszarni=? AND Wilgotnosc BETWEEN ? AND ? ORDER BY RAND() LIMIT 8"))
									{//echo "Zapytanie działa <br / >";
										/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
										$stmt -> bind_param("sss", $nr_suszarni,$wilgotnosc1,$wilgotnosc2);
										$stmt -> execute();
										$stmt -> bind_result($Data,$Czas,$Predkosc_Blanszownika,$Temperatura_Blanszownika,$V_Siatka7,$V_Siatka6,$V_Siatka5,$V_Siatka4,$V_Siatka3,$V_Siatka2,$V_Siatka1,$Czas_Suszenia,$Temp_Gorna,$Temp_Dolna,$Wilgotnosc,$Odpowiedzialny);
										$stmt -> store_result();

										if ($stmt->num_rows > 0) {
											//Nagłówek
											echo "<h4><b>Parametry procesu suszenia:</b></h4><br / >";
											echo "<div class='row'><div class='col-sm-4'><h4><b>Suszarnia nr: " .$nr_suszarni." </b></h4></div> <div class='col-sm-4'><h4><b> Asortyment: ".$asortyment." </b></h4></div> <div class='col-sm-4'><h4><b> Cel wilgotności: " .$wilgotnosc." %</b></h4></div></div>";
											/*Tabela wielkości*/
											echo '<div id="tabela_wielkosci">Godzina<br / >Pręd Blansz<br / >Temp Blans<br / >Siatka nr 7<br / >Siatka nr 6<br / >Siatka nr 5<br / >Siatka nr 4<br / >Siatka nr 3<br / >Siatka nr 2<br / >Siatka nr 1<br / >Czas Susz.<br / >Temp. Góra<br / >Temp. Dół<br / >Wilgotność<br / >Osoba<br / ></div>';


											$stmt->data_seek(0);
											while ($stmt->fetch())
											{
					   					    printf ("<div id='tabela_wynikow'>%s. <br / >%s Hz<br / >%s &deg;C<br / >%s Hz<br / > %s Hz<br / >%s Hz<br / > %s Hz<br / > %s Hz<br / > %s Hz<br / >%s Hz<br / >%s min<br / >%s &deg;C<br / > %s &deg;C<br / >%s %% <br / > %s</div>", $Czas = substr($Czas, 0, 5),$Predkosc_Blanszownika,$Temperatura_Blanszownika,$V_Siatka7,$V_Siatka6,$V_Siatka5,$V_Siatka4,$V_Siatka3,$V_Siatka2,$V_Siatka1,$Czas_Suszenia,$Temp_Gorna,$Temp_Dolna,$Wilgotnosc,$Odpowiedzialny);
					    					}
				    					} else {
												echo '<div class="alert alert-info"><span class="glyphicon glyphicon-info-sign"></span>&nbsp<strong>Info!</strong>&nbsp Brak danych w bazie danych</div>';
												}


									}


				}

			}
?>
<br / ><br / >
<div id="formularz">
		<div class="row" >
			<div class="form-group">
     <form method="post" action="index2.php?raporty_suszenia=1">
				<fieldset>
					<legend>
					Stwórz raport
					</legend>
					<div class="row" >
						<div class="col-sm-4">
						<label >Asortyment</label>
						<select class="form-control" name="asortyment" required>
							<?php
			//Wyswietlamy wybór asortymentu dostępnego w bazie danych
			/* Łączymy się z serwerem */
			require_once ('polaczenie_z_baza.php');

			if (mysqli_connect_errno()) {

			printf("<div class='alert alert-danger'><strong>Uwaga!</strong>&nbspBrak połączenia z serwerem MySQL. Kod błędu: %s\n</div>", mysqli_connect_error());

			} else
				{
					//Zapytanie do bazy o obecny asortyment
					if ($stmt = $mysqli -> prepare("SELECT Asortyment FROM AsortymentSuszu ORDER BY Asortyment ASC"))
					{
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
					if (isset($_POST['asortyment']))
					{
						echo '<option value="'.$_POST['asortyment'].'">'.$_POST['asortyment'].'</option>';

					}
					else {

						foreach ($Asortyment_wbazie as $key => $value) {

						printf("<option value='%s'>%s</option>",$value,$value);
						}
					}

				}
?>

						</select>
						</div>

						<div class="col-sm-4">
						<label >Nr Suszarni</label>
						<select class="form-control" name="nr_suszarni"  min="1" max="5" required>
							<?php
							if (isset($_POST['asortyment']))
							{
							echo '<option value="'.$_POST['nr_suszarni'].'">'.$_POST['nr_suszarni'].'</option>';

							}
							?>
							<option value="1">1</option>
							<option value="2">2</option>
							<option value="3">3</option>
							<option value="4">4</option>
							<option value="5">5</option>
						</select>
						</div>

					</div>

					<div class="row">
						<div class="col-sm-4">
						<label >Data</label>
						<input class="form-control" type="date" name="data" required <?php echo "value='".$_POST['data']."'" ?>/>
						</div>

						<div class="col-sm-4">
						<label >Godzina</label>
						<input class="form-control" type="time" name="godzina" required />
						</div>
					</div>
						<hr></hr>

					<div class="row">
						<div class="col-sm-4">
						<label >Prędkość Blanszownika</label>
						<input class="form-control" type="number" name="predkosc_blanszownika"  min="0" max="140" <?php echo "value='".$_POST['predkosc_blanszownika']."'" ?>/>
						</div>

						<div class="col-sm-4">
						<label >Temperatura Blanszownika</label>
						<input class="form-control" type="number" name="temperatura_blanszownika"  min="0" max="110" <?php echo "value='".$_POST['temperatura_blanszownika']."'" ?>/>
						</div>
					</div>
						<hr>

					<div class="row">
						<div class="col-sm-4">
						<label >Prędkość Siatki nr 7</label>
						<input class="form-control" type="number" name="siatka7"  min="1" max="140" required <?php echo "value='".$_POST['siatka7']."'" ?>/>
						</div>

						<div class="col-sm-4">
						<label >Prędkość Siatki nr 6</label>
						<input class="form-control" type="number" name="siatka6"  min="1" max="140" required <?php echo "value='".$_POST['siatka6']."'" ?>/>
						</div>

						<div class="col-sm-4">
						<label >Prędkość Siatki nr 5</label>
						<input class="form-control" type="number" name="siatka5"  min="1" max="140" required <?php echo "value='".$_POST['siatka5']."'" ?>/>
						</div>
					</div>

					<div class="row">
						<div class="col-sm-4">
						<label >Prędkość Siatki nr 4</label>
						<input class="form-control" type="number" name="siatka4"  min="1" max="140" required <?php echo "value='".$_POST['siatka4']."'" ?>/>
						</div>

						<div class="col-sm-4">
						<label >Prędkość Siatki nr 3</label>
						<input class="form-control" type="number" name="siatka3"  min="1" max="140" required <?php echo "value='".$_POST['siatka3']."'" ?>/>
						</div>
					</div>

					<div class="row">
						<div class="col-sm-4">
						<label >Prędkość Siatki nr 2</label>
						<input class="form-control" type="number" name="siatka2"  min="1" max="140" required <?php echo "value='".$_POST['siatka2']."'" ?>/>
						</div>

						<div class="col-sm-4">
						<label >Prędkość Siatki nr 1</label>
						<input class="form-control" type="number" name="siatka1"  min="1" max="140" required <?php echo "value='".$_POST['siatka1']."'" ?>/>
						</div>
					</div>
						<hr></hr>

					<div class="row">
						<div class="col-sm-4">
						<label >Górna Temperatura Suszarni</label>
						<input class="form-control" type="number" name="temperatura_gorna"  min="1" max="110" required <?php echo "value='".$_POST['temperatura_gorna']."'" ?>/>
						</div>

						<div class="col-sm-4">
						<label >Dolna Temperatura Suszarni</label>
						<input class="form-control" type="number" name="temperatura_dolna"  min="1" max="110" required <?php echo "value='".$_POST['temperatura_dolna']."'" ?>/>
						</div>
					</div>
						<hr></hr>

					<div class="row">
						<div class="col-sm-4">
						<label >Wilgotność</label>
						<input class="form-control" type="text" name="wilgotnosc"  min="0" max="100" maxlength="5" />
						</div>

						<div class="col-sm-4">
						<label >Osoba dokonująca Pomiaru</label>
						<select class="form-control" name="osoba_odpowiedzialna" required>
              <?php
              if ( $_SESSION['login'] == 'Szymon Ch.')
                {
                	if (isset($_POST['osoba_odpowiedzialna'])) {
						echo "<option value='" . $_POST['osoba_odpowiedzialna'] . "' >" . $_POST['osoba_odpowiedzialna'] . "</option>";
					}
					
                  echo "
                  		<option Value='Szymon Ch.'>Szymon Ch.</option>
                        <option Value='Patryk Z.'>Patryk Z.</option>
                        <option Value='Magda K.'>Magda K.</option>
                        <option Value='Krzysztof D.'>Krzysztof D.</option>
                        <option Value='Edward K.'>Edward K.</option>
                        <option Value='Mariusz Ś.'>Mariusz Ś.</option>
                        <option Value='Piotr M.'>Piotr M.</option>
                        <option Value='Krzysztof P.'>Krzysztof P.</option>
                        <option Value='Mateusz P.'>Mateusz P.</option>
                        <option Value='Wojtek H.'>Wojtek H.</option>
                     ";

                }
                else {
                  echo "
                  <option value='" . $_SESSION['login'] . "' >" . $_SESSION['login'] . "</option>
                  ";
                }
                ?>
						</select>
						</div>
					</div>

						<hr ></hr>
						<span class="glyphicon glyphicon-save"></span>&nbsp;<input type="submit" value="Zapisz" name="zapisz"><br / ><br / ><br / >
						<span class="glyphicon glyphicon-edit"></span>&nbsp;<input type="submit" value="Modyfikuj" name="modyfikuj"><br / ><br / >
						<span class="glyphicon glyphicon-floppy-remove"></span>&nbsp;<input type="submit" value="Usuń" name="usun"><br / ><br / ><br / >
						<span class="glyphicon glyphicon-remove"></span>&nbsp;<a href="index2.php?raporty_suszenia=1">Reset</a>

				</fieldset>
			</form>
</div>
</div>
</div>
			<br / ><br / >

			<span class="glyphicon glyphicon-plus"></span>&nbsp<button data-toggle="collapse" data-target="#informacje_dodatkowe">Informacje dodatkowe</button>
			<br / ><br /  >

<div id="informacje_dodatkowe" class="collapse">
	<div id="formularz">
		<div class="row" >
			<div class="form-group">
			<form name="info do raportu" method="post" enctype="multipart/form-data" action="index2.php?raporty_suszenia=1" >
				<fieldset>
					<legend>Informacje dodatkowe</legend>

					<div class="row">
						<div class="col-sm-4">
						<label >Asortyment</label>
						<select class="form-control" name="asortyment" required>
							<?php
			//Wyswietlamy wybór asortymentu dostępnego w bazie danych
			/* Łączymy się z serwerem */
			require_once ('polaczenie_z_baza.php');

			if (mysqli_connect_errno()) {

			printf("<div class='alert alert-danger'><span class='glyphicon glyphicon-thumbs-down'></span>&nbsp;<strong>Uwaga!</strong>&nbspBrak połączenia z serwerem MySQL. Kod błędu: %s\n</div>", mysqli_connect_error());

			} else
				{
					//Zapytanie do bazy o obecny asortyment
					if ($stmt = $mysqli -> prepare("SELECT Asortyment FROM AsortymentSuszu ORDER BY Asortyment ASC"))
					{
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

					foreach ($Asortyment_wbazie as $key => $value) {

						printf("<option value='%s'>%s</option>",$value,$value);
					}

				}
?>

						</select>
						</div>

						<div class="col-sm-4">
						<label >Data</label>
						<input class="form-control" type="date" name="data" required />
						</div>

						<div class="col-sm-4">
						<label >Nr Suszarni</label>
						<select class="form-control" name="nr_suszarni" required>
							<?php
							if (isset($_POST['asortyment']))
							{
							echo '<option value="'.$_POST['nr_suszarni'].'">'.$_POST['nr_suszarni'].'</option>';

							}
							?>
							<option value="1">1</option>
							<option value="2">2</option>
							<option value="3">3</option>
							<option value="4">4</option>
							<option value="5">5</option>
							<option value="1 i 2">1 i 2</option>
							<option value="3 i 4">3 i 4</option>
							<option value="2 i 3 i 4">2 i 3 i 4</option>
						</select>
						</div>

					</div>
						<hr></hr>

					<div class="row">
						<div class="col-sm-4">
						<label>Ocena towaru po I zmianie</label>
						<input class="form-control" type="text" name="ocena_zmiany1" maxlength="100"/>
						</div>

						<div class="col-sm-4">
						<label>Ocena towaru po II zmianie</label>
						<input class="form-control" type="text" name="ocena_zmiany2" maxlength="100"/>
						</div>

						<div class="col-sm-4">
						<label>Ocena towaru po III zmianie</label>
						<input class="form-control" type="text" name="ocena_zmiany3" maxlength="100"/>
						</div>
					</div><br / >

					<div class="row">
						<div class="col-sm-4">
						<label>Ilość suszu na I zmianie</label>
						<input class="form-control" type="number" name="susz_zmiana1" maxlength="5"/>
						</div>

						<div class="col-sm-4">
						<label>Ilość suszu na II zmianie</label>
						<input class="form-control" type="number" name="susz_zmiana2" maxlength="5"/>
						</div>

						<div class="col-sm-4">
						<label>Ilość suszu na III zmianie</label>
						<input class="form-control" type="number" name="susz_zmiana3" maxlength="5"/>
						</div>
					</div><br / >


					<div class="row">
						<!--
						<div class="col-sm-4">
						<label >Całkowita ilość Suszu</label>
						<input class="form-control" type="number" name="ilosc_suszu"  maxlength="5" />
						</div>
						-->
						<div class="col-sm-4">
						<label >Dostawca</label>
						<input class="form-control" type="text" name="dostawca" maxlength="200" />
						</div>

						<div class="col-sm-4">
						<label>Uwagi</label>
						<textarea class="form-control" name="uwagi" rows="1" cols="30" maxlength="150" ></textarea>
						</div>

					</div><br / >

					<div class="row">
						<div class="col-sm-4">
						 <!-- MAX_FILE_SIZE musi poprzedzać input pliku docelowego -->
    					<input type="hidden" name="MAX_FILE_SIZE" value="17000000" />

						<label>Zdjęcie</label>
						<input class="form-control" type="file" name="zdjecie" />
						</div>

						<div class="col-sm-4">
						<label>Opis zdjecia</label>
						<input class="form-control" type="text" name="opis_zdjecia" maxlength="160" />
						</div>
					</div>

						<hr></hr>


						<span class="glyphicon glyphicon-save"></span>&nbsp;<input type="submit" value="Zapisz" name="info_dodatkowe">

				</fieldset>
			</form>
			</div>
		</div>
	</div>
</div>
			<br / ><br / >


			<?php
	/*ob_end_clean();
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);*/

			//Obsługa formularza głównego - raport
			if ($_GET['reset']==1) {
				$_SESSION['stala_data']="";
				$_SESSION['kolejny_dzien']="";
			}

			if (isset($_POST['zapisz']) || isset($_POST['modyfikuj']) || isset($_POST['usun'])) {
				//funkcja filtrująca dane
				function filtruj($zmienna) {
					$data = trim($zmienna);
					//usuwa spacje, tagi
					$data = stripslashes($zmienna);
					//usuwa slashe
					$data = htmlspecialchars($zmienna);
					//zamienia tagi html na czytelne znaki aby w formularzu nie wpisać szkodliwego kodu
					return $zmienna;
				}
				
				//Resetujemy komunikaty	
				//$_SESSION['sukces']=FALSE;	
				
				/*Odbieramy dane z formularza*/
				$asortyment = filtruj($_POST['asortyment']);
				$nr_suszarni = filtruj($_POST['nr_suszarni']);
				$wilgotnosc = filtruj($_POST['wilgotnosc']);
				$data = filtruj($_POST['data']);
				$godzina = filtruj($_POST['godzina']);
				$predkosc_blanszownika = filtruj($_POST['predkosc_blanszownika']);
				$temperatura_blanszownika = filtruj($_POST['temperatura_blanszownika']);
				$v_siatka7 = filtruj($_POST['siatka7']);
				$v_siatka6 = filtruj($_POST['siatka6']);
				$v_siatka5 = filtruj($_POST['siatka5']);
				$v_siatka4 = filtruj($_POST['siatka4']);
				$v_siatka3 = filtruj($_POST['siatka3']);
				$v_siatka2 = filtruj($_POST['siatka2']);
				$v_siatka1 = filtruj($_POST['siatka1']);
				$temp_gorna = filtruj($_POST['temperatura_gorna']);
				$temp_dolna = filtruj($_POST['temperatura_dolna']);
				$odpowiedzialny = filtruj($_POST['osoba_odpowiedzialna']);


				$wszystkie_dane = array($asortyment, $data,$godzina, $nr_suszarni, $odpowiedzialny, $predkosc_blanszownika, $temp_dolna, $temp_gorna, $temperatura_blanszownika, $v_siatka1, $v_siatka2, $v_siatka3, $v_siatka4, $v_siatka5, $v_siatka6, $v_siatka7, $wilgotnosc);
				$dane_tekstowe = array($asortyment,$odpowiedzialny);
				$dane_numeryczne = array($nr_suszarni, $predkosc_blanszownika, $temp_dolna, $temp_gorna, $temperatura_blanszownika, $v_siatka1, $v_siatka2, $v_siatka3, $v_siatka4, $v_siatka5, $v_siatka6, $v_siatka7, $wilgotnosc);

				function sprawdz_istnienie_danych($tablica) {
					foreach ($tablica as $element) {
						if (!isset($element) || $element == null) {
							echo "<div class='alert alert-warning'><strong>Uwaga!</strong>&nbsp Nie wprowadzono wszystkich danych.</div>";
							return false;
							break;
						}
					}
					return TRUE;
				}

				function sprawdz_dane_tekstowe($tablica) {
					foreach ($tablica as $element) {
						if (!is_string($element) || strlen($element) > 50) {
							echo "<div class='alert alert-warning'><strong>Uwaga!</strong>&nbsp Podałeś zły format danych. Tekst jest za długi - max 15 znaków.</div>";
							return false;
							break;
						}
					}
					return TRUE;
				}

				function sprawdz_dane_numeryczne($tablica) {
					foreach ($tablica as $element) {
						if (!is_numeric($element) || strlen($element) > 5) {
							echo "<div class='alert alert-warning'><strong>Uwaga!</strong>&nbsp Podałeś zły format danych. W miejscu gdzie powinny być liczby wpisałeś tekst lub dane liczbowe są za długie - max 5 znaków.</div>";
							return false;
							break;
						}
					}
					return TRUE;
				}

				if (sprawdz_istnienie_danych($wszystkie_dane) && sprawdz_dane_numeryczne($dane_numeryczne) && sprawdz_dane_tekstowe($dane_tekstowe)) {

					/* Łączymy się z serwerem */
					require_once ('polaczenie_z_baza.php');

					if (mysqli_connect_errno()) {

						printf("<div class='alert alert-danger'><span class='glyphicon glyphicon-thumbs-down'></span>&nbsp;<strong>Uwaga!</strong>&nbspBrak połączenia z serwerem MySQL. Kod błędu: %s\n</div>.", mysqli_connect_error());
					} else {
						//usuwamy specjalne znaki takie jak '," aby nie możnabyło wpisać ich z formularza do zapytania SQL
						$asortyment = $mysqli -> real_escape_string($asortyment);
						$nr_suszarni = $mysqli -> real_escape_string($nr_suszarni);

						$data = $mysqli -> real_escape_string($data);
						$data_do_odczytu=$data;

						//Poniżej ustawiamy date na stałe do sesji aby wykorzytac ją do zapytania SQL w pierwszym członie-do wyciągnięcia danych z dnia w którym rozpoczyna się raport


						$godzina_local=$godzina;
						settype($godzina_local, 'integer');
							if ($godzina_local >= 0 && $godzina_local< 8) {
							$data_do_odczytu = date('Y-m-d', strtotime($data . ' -1 day'));
							}

						$kolejny_dzien = date('Y-m-d', strtotime($data_do_odczytu . ' +1 day'));

						$godzina = $mysqli -> real_escape_string($godzina);
						$predkosc_blanszownika = $mysqli -> real_escape_string($predkosc_blanszownika);
						$temperatura_blanszownika = $mysqli -> real_escape_string($temperatura_blanszownika);
						$v_siatka7 = $mysqli -> real_escape_string($v_siatka7);
						$v_siatka6 = $mysqli -> real_escape_string($v_siatka6);
						$v_siatka5 = $mysqli -> real_escape_string($v_siatka5);
						$v_siatka4 = $mysqli -> real_escape_string($v_siatka4);
						$v_siatka3 = $mysqli -> real_escape_string($v_siatka3);
						$v_siatka2 = $mysqli -> real_escape_string($v_siatka2);
						$v_siatka1 = $mysqli -> real_escape_string($v_siatka1);
						$temp_gorna = $mysqli -> real_escape_string($temp_gorna);
						$temp_dolna = $mysqli -> real_escape_string($temp_dolna);
						$wilgotnosc = $mysqli -> real_escape_string($wilgotnosc);
						$odpowiedzialny = $mysqli -> real_escape_string($odpowiedzialny);

						//Obliczamy czas suszenia
						$czas_suszenia="";
						$czas_suszenia6="";
						$czas_suszenia5="";
						$czas_suszenia4="";
						$czas_suszenia3="";
						$czas_suszenia2="";
						$czas_suszenia1="";
						$czas_suszenia_godzinyminuty="";
						$czas_odniesienia=215; //Sprawdzony doświadczalnie czas w minutach w jakim towar jest w szafie przy prędkości 50Hz na każdej siatce - nie licząc blanszownika

						//$wspolczynnik_przyspieszenia = 0.75;
						//$wspolczynnik_opoznienia = 0.4;

						/*Przy poniższych obliczeniach przyspieszenia i opóżnienia jakie powoduje poszczególna siatka wykorzystano współczynniki
						które zostały ustalone przy pomiarach doświadczalnych - przyspieszano i zwalniano szafę o 5Hz na każdej siatce (w stosunku do 50Hz czyli 55, 60 itd) i
						mierzono czas posuwu siatki. Każdy współczynnik to wynik z równania: x=przyspieszenie lub opoźnienie:5(bo przyspieszaliśmy lub zwalnialiśmy o 5 Hz.)
						W ten sposób obliczono jaki wpływ ma każdy 1Hz dodany lub odjęty od prędkości odniesienia jaką jest 50Hz */

						//Czas po 6 siatce
						if ($v_siatka6>50) {
							$przyspieszenie=(($v_siatka6-50)*0.35);
							$czas_suszenia6=$czas_odniesienia-$przyspieszenie;
						}
						if ($v_siatka6<50) {
							$opoznienie=((50-$v_siatka6)*0.5);
							$czas_suszenia6=$czas_odniesienia+$opoznienie;
						}
						if ($v_siatka6==50) {
							$czas_suszenia6=$czas_odniesienia;
						}

							//Czas po 5 siatce
						if ($v_siatka5>50) {
							$przyspieszenie=(($v_siatka5-50)*0.4);
							$czas_suszenia5=$czas_suszenia6-$przyspieszenie;
						}
						if ($v_siatka5<50) {
							$opoznienie=((50-$v_siatka5)*0.3);
							$czas_suszenia5=$czas_suszenia6+$opoznienie;
						}
						if ($v_siatka5==50) {
							$czas_suszenia5=$czas_suszenia6;
						}

							//Czas po 4 siatce
						if ($v_siatka4>50) {
							$przyspieszenie=(($v_siatka4-50)*0.65);
							$czas_suszenia4=$czas_suszenia5-$przyspieszenie;
						}
						if ($v_siatka4<50) {
							$opoznienie=((50-$v_siatka4)*0.8);
							$czas_suszenia4=$czas_suszenia5+$opoznienie;
						}
						if ($v_siatka4==50) {
							$czas_suszenia4=$czas_suszenia5;
						}

						//Czas po 3 siatce
						if ($v_siatka3>50) {
							$przyspieszenie=(($v_siatka3-50)*0.6);
							$czas_suszenia3=$czas_suszenia4-$przyspieszenie;
						}
						if ($v_siatka3<50) {
							$opoznienie=((50-$v_siatka3)*1);
							$czas_suszenia3=$czas_suszenia4+$opoznienie;
						}
						if ($v_siatka3==50) {
							$czas_suszenia3=$czas_suszenia4;
						}

						//Czas po 2 siatce
						if ($v_siatka2>50) {
							$przyspieszenie=(($v_siatka2-50)*0.7);
							$czas_suszenia2=$czas_suszenia3-$przyspieszenie;
						}
						if ($v_siatka2<50) {
							$opoznienie=((50-$v_siatka2)*1.1);
							$czas_suszenia2=$czas_suszenia3+$opoznienie;
						}
						if ($v_siatka2==50) {
							$czas_suszenia2=$czas_suszenia3;
						}

						//Czas po 1 siatce
						if ($v_siatka1>50) {
							$przyspieszenie=(($v_siatka1-50)*0.9);
							$czas_suszenia1=$czas_suszenia2-$przyspieszenie;
						}
						if ($v_siatka1<50) {
							$opoznienie=((50-$v_siatka1)*0.9);
							$czas_suszenia1=$czas_suszenia2+$opoznienie;
						}
						if ($v_siatka1==50) {
							$czas_suszenia1=$czas_suszenia2;
						}

						$czas_suszenia=$czas_suszenia1;

						/*
						echo "Czas po 6 siatce: $czas_suszenia6 <br / >";
						echo "Czas po 5 siatce: $czas_suszenia5 <br / >";
						echo "Czas po 4 siatce: $czas_suszenia4 <br / >";
						echo "Czas po 3 siatce: $czas_suszenia3 <br / >";
						echo "Czas po 2 siatce: $czas_suszenia2 <br / >";
						echo "Czas po 1 siatce: $czas_suszenia1 <br / >";
						*/

						function convertToHoursMins($time, $format = '%02d:%02d') {
   						 if ($time < 1) {
      					  return;
  							  }
   							 $hours = floor($time / 60);
   								 $minutes = ($time % 60);
    								return sprintf($format, $hours, $minutes);
						}

							$czas_suszenia_godzinyminuty=convertToHoursMins($czas_suszenia, '%02d hours %02d minutes');


					if (isset($_POST['zapisz']))
							{
								if ($stmt = $mysqli -> prepare("INSERT INTO `" . $asortyment . "` (NrSuszarni,Data,Czas,PredkoscBlanszownika,TemperaturaBlanszownika,PredkoscSiatkiNr7,PredkoscSiatkiNr6,PredkoscSiatkiNr5,PredkoscSiatkiNr4,PredkoscSiatkiNr3,PredkoscSiatkiNr2,PredkoscSiatkiNr1,CzasSuszenia,TemperaturaGora,TemperaturaDol,Wilgotnosc,WykonawcaPomiaru) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"))
						 		{

								/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
								$stmt -> bind_param("sssssssssssssssss", $nr_suszarni,$data,$godzina,$predkosc_blanszownika,$temperatura_blanszownika,$v_siatka7,$v_siatka6,$v_siatka5,$v_siatka4,$v_siatka3,$v_siatka2,$v_siatka1,$czas_suszenia,$temp_gorna,$temp_dolna,$wilgotnosc,$odpowiedzialny);
								$stmt -> execute();
						 		}
								else {
									echo '<div class="alert alert-danger"><strong>Info!</strong>&nbsp Błąd podczas zapisu do bazy danych.</div>';
									}
							}

					if (isset($_POST['modyfikuj']))
							{
								if ($stmt = $mysqli -> prepare("UPDATE `" . $asortyment . "` SET PredkoscBlanszownika=?,TemperaturaBlanszownika=?,PredkoscSiatkiNr7=?,PredkoscSiatkiNr6=?,PredkoscSiatkiNr5=?,PredkoscSiatkiNr4=?,PredkoscSiatkiNr3=?,PredkoscSiatkiNr2=?,PredkoscSiatkiNr1=?,TemperaturaGora=?,TemperaturaDol=?,Wilgotnosc=?,WykonawcaPomiaru=? WHERE NrSuszarni=? AND Data=? AND Czas=?"))
						 		{

								/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
								$stmt -> bind_param("ssssssssssssssss", $predkosc_blanszownika,$temperatura_blanszownika,$v_siatka7,$v_siatka6,$v_siatka5,$v_siatka4,$v_siatka3,$v_siatka2,$v_siatka1,$temp_gorna,$temp_dolna,$wilgotnosc,$odpowiedzialny,$nr_suszarni,$data,$godzina);
								$stmt -> execute();
						 		}
								else {
									echo '<div class="alert alert-danger"><strong>Info!</strong>&nbsp Błąd podczas zapisu do bazy danych.</div>';
									}
							}

					if (isset($_POST['usun']))
							{
								if ($stmt = $mysqli -> prepare("DELETE FROM `" . $asortyment . "` WHERE NrSuszarni=? AND Data=? AND Czas=?"))
						 		{

								/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
								$stmt -> bind_param("sss", $nr_suszarni,$data,$godzina);
								$stmt -> execute();
						 		}
								else {
									echo '<div class="alert alert-danger"><strong>Info!</strong>&nbsp Błąd podczas usuwania informacji z bazy danych.</div>';
									}
							}




							if ($stmt -> affected_rows == 0 || $stmt -> affected_rows < 0 ||$stmt->affected_rows==NULL) {
								echo "<div class='alert alert-warning'><strong>Ostrzeżenie!</strong>&nbsp Nie dokokano zapisu. Możliwy błąd zapytania.</div>";
							}

							//Uwaga Polecenie Union działa tutaj tylko gdy pobieramy wszystkie kolumny z tabeli w bazie danej - nie wiem dlaczego.
							if ($stmt -> affected_rows > 0) {

								if($stmt = $mysqli -> prepare ("SELECT Data,Czas,PredkoscBlanszownika,TemperaturaBlanszownika,PredkoscSiatkiNr7,PredkoscSiatkiNr6,PredkoscSiatkiNr5,PredkoscSiatkiNr4,PredkoscSiatkiNr3,PredkoscSiatkiNr2,PredkoscSiatkiNr1,CzasSuszenia,TemperaturaGora,TemperaturaDol,Wilgotnosc,WykonawcaPomiaru FROM `".$asortyment."` WHERE Data=? AND Czas >=  STR_TO_DATE('08:00:00','%h:%i:%s') AND NrSuszarni=?
								UNION ALL
								SELECT Data,Czas,PredkoscBlanszownika,TemperaturaBlanszownika,PredkoscSiatkiNr7,PredkoscSiatkiNr6,PredkoscSiatkiNr5,PredkoscSiatkiNr4,PredkoscSiatkiNr3,PredkoscSiatkiNr2,PredkoscSiatkiNr1,CzasSuszenia,TemperaturaGora,TemperaturaDol,Wilgotnosc,WykonawcaPomiaru FROM `".$asortyment."` WHERE Data=? AND Czas <=  STR_TO_DATE('06:00:00','%h:%i:%s') AND NrSuszarni=? ORDER BY Data, Czas ASC"
								))
								{
								$stmt -> bind_param("ssss",$data_do_odczytu,$nr_suszarni,$kolejny_dzien,$nr_suszarni);
								$stmt->execute();

								 /* Powiązujemy dane z zapytania do zmiennych, których uzyjemy do wyswietlenia danych */
								$stmt->bind_result($Data,$Czas,$Predkosc_Blanszownika,$Temperatura_Blanszownika,$V_Siatka7,$V_Siatka6,$V_Siatka5,$V_Siatka4,$V_Siatka3,$V_Siatka2,$V_Siatka1,$Czas_Suszenia,$Temp_Gorna,$Temp_Dolna,$Wilgotnosc,$Odpowiedzialny);

								 /* Bufurujemy wynik */
    							$stmt->store_result();

								/*Sprawdzamy czy są jakieś dane jesli tak to wyswietlamy jesli nie to zgłaszamy ich brak*/
   								if ($stmt->num_rows > 0) {
   									echo '<div class="alert alert-success alert-dismissable fade in">
										<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
										<span class="glyphicon glyphicon-thumbs-up"></span>&nbsp;<strong>Sukces!</strong>&nbsp Zapisano dane. Poniżej znajduje się twój raport. </div><br / >';
		
   									
   								//$_SESSION['sukces']=TRUE;

								printf("<b>Asortyment:</b>&nbsp %s &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp <b>Data:</b>&nbsp %s &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp <b>Nr Suszarni:</b>&nbsp %s <br / ><br / >", $asortyment, $data_do_odczytu, $nr_suszarni);

								echo '<div id="tabela_wielkosci">Godzina<br / >Pręd Blansz<br / >Temp Blans<br / >Siatka nr 7<br / >Siatka nr 6<br / >Siatka nr 5<br / >Siatka nr 4<br / >Siatka nr 3<br / >Siatka nr 2<br / >Siatka nr 1<br / >Czas Susz.<br / >Temp. Góra<br / >Temp. Dół<br / >Wilgotność<br / >Osoba<br / ></div>';

								while ($stmt->fetch()) {
									printf("<div id='tabela_wynikow'>%s. <br / >%s Hz<br / >%s &deg;C<br / >%s Hz<br / > %s Hz<br / >%s Hz<br / > %s Hz<br / > %s Hz<br / > %s Hz<br / >%s Hz<br / >%s min<br / >%s &deg;C<br / > %s &deg;C<br / >%s %% <br / > %s</div>", $Czas = substr($Czas, 0, 5), $Predkosc_Blanszownika, $Temperatura_Blanszownika, $V_Siatka7, $V_Siatka6, $V_Siatka5, $V_Siatka4, $V_Siatka3, $V_Siatka2, $V_Siatka1,$Czas_Suszenia,$Temp_Gorna, $Temp_Dolna, $Wilgotnosc, $Odpowiedzialny);
								}

					//Wyciągmy średnią wartość wilgotności
					$Suma_Wilgotnosc1="";
					$Suma_Wilgotnosc2="";
					$Suma_Wilgotnosc="";
					$Ilosc_pomiarow1="";
					$Ilosc_pomiarow2="";
					$Ilosc_pomiarow="";
					$Srednia_Wilgotnosc="";
					$precision="";

					if ($stmt = $mysqli -> prepare("SELECT SUM(Wilgotnosc) FROM `" . $asortyment . "` WHERE Data=? AND Czas >=  STR_TO_DATE('08:00:00','%h:%i:%s') AND NrSuszarni=? AND Wilgotnosc > 0"))
						{

					$stmt -> bind_param("ss",$data_do_odczytu,$nr_suszarni);
					$stmt -> execute();
					$stmt -> bind_result($Sum_Wilg);
					$stmt -> store_result();
					$stmt->data_seek(0);
						if ($stmt -> fetch()){
							$Suma_Wilgotnosc1=$Sum_Wilg;
							}

						if ($stmt = $mysqli -> prepare("SELECT COUNT(Wilgotnosc) FROM `" . $asortyment . "` WHERE Data=? AND Czas >=  STR_TO_DATE('08:00:00','%h:%i:%s') AND NrSuszarni=? AND Wilgotnosc > 0 "))
						{
					$stmt -> bind_param("ss", $data_do_odczytu,$nr_suszarni);
					$stmt -> execute();
					$stmt -> bind_result($Ilosc_pom);
					$stmt -> store_result();
					$stmt->data_seek(0);
						if ($stmt -> fetch()){
							$Ilosc_pomiarow1=$Ilosc_pom;

							}
						}

						}


						if ($stmt = $mysqli -> prepare("SELECT Sum(Wilgotnosc) FROM `" . $asortyment . "` WHERE Data=? AND Czas <=  STR_TO_DATE('06:00:00','%h:%i:%s') AND NrSuszarni=? AND Wilgotnosc > 0 "))
						{
					$stmt -> bind_param("ss",$kolejny_dzien,$nr_suszarni);
					$stmt -> execute();
					$stmt -> bind_result($Sum_Wilg);
					$stmt -> store_result();
					$stmt->data_seek(0);
						if ($stmt -> fetch()){
							$Suma_Wilgotnosc2=$Sum_Wilg;
						}

						if ($stmt = $mysqli -> prepare("SELECT COUNT(Wilgotnosc) FROM `" . $asortyment . "` WHERE Data=? AND Czas <=  STR_TO_DATE('06:00:00','%h:%i:%s') AND NrSuszarni=? AND Wilgotnosc > 0"))
						{
					$stmt -> bind_param("ss", $kolejny_dzien,$nr_suszarni);
					$stmt -> execute();
					$stmt -> bind_result($Ilosc_pom);
					$stmt -> store_result();
					$stmt->data_seek(0);
						if ($stmt -> fetch()){
							$Ilosc_pomiarow2=$Ilosc_pom;

							}
						}

						}

						$Suma_Wilgotnosc=$Suma_Wilgotnosc1+$Suma_Wilgotnosc2;
						$Ilosc_pomiarow=$Ilosc_pomiarow1+$Ilosc_pomiarow2;

						$Srednia_Wilgotnosc=($Suma_Wilgotnosc/$Ilosc_pomiarow);
						printf("<br /><br /> <b>Średnia wilgotność:</b>&nbsp %s %%", round($Srednia_Wilgotnosc,$precision=2));


					//Wyciągmy info o ocenie suszu na 1 zmianie
					if ($stmt = $mysqli -> prepare("SELECT OcenaTowaruZmiany1 FROM `" . $asortyment . "` WHERE Data=? AND NrSuszarni=?"))
						{
					/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
					$stmt -> bind_param("ss",$data_do_odczytu,$nr_suszarni);

					$stmt -> execute();
					$stmt -> bind_result($Ocena_suszu1);
					$stmt -> store_result();
					$stmt->data_seek(0);
						if ($stmt -> fetch()){
							printf("<br /><br /> <b>Ocena suszu po I zmianie:</b>&nbsp %s", $Ocena_suszu1);
							}
						}

					//Wyciągmy info o ocenie suszu na 2 zmianie
					if ($stmt = $mysqli -> prepare("SELECT OcenaTowaruZmiany2 FROM `" . $asortyment . "` WHERE Data=? AND NrSuszarni=?"))
						{
					/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
					$stmt -> bind_param("ss",$data_do_odczytu,$nr_suszarni);

					$stmt -> execute();
					$stmt -> bind_result($Ocena_suszu2);
					$stmt -> store_result();
					$stmt->data_seek(0);
						if ($stmt -> fetch()){
							printf("<br /><br /> <b>Ocena suszu po II zmianie:</b>&nbsp %s", $Ocena_suszu2);
							}
						}

						//Wyciągmy info o ocenie suszu na 3 zmianie
					if ($stmt = $mysqli -> prepare("SELECT OcenaTowaruZmiany3 FROM `" . $asortyment . "` WHERE Data=? AND NrSuszarni=?"))
						{
					/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
					$stmt -> bind_param("ss",$data_do_odczytu,$nr_suszarni);

					$stmt -> execute();
					$stmt -> bind_result($Ocena_suszu3);
					$stmt -> store_result();
					$stmt->data_seek(0);
						if ($stmt -> fetch()){
							printf("<br /><br /> <b>Ocena suszu po III zmianie:</b>&nbsp %s", $Ocena_suszu3);
							}
						}

					//Wyciągmy wartość iloci suszu na 1 zmianie
					if ($stmt = $mysqli -> prepare("SELECT IloscSuszuZmiana1 FROM `" . $asortyment . "` WHERE Data=? AND NrSuszarni=? "))
						{
					/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
					$stmt -> bind_param("ss",$data_do_odczytu,$nr_suszarni);

					$stmt -> execute();
					$stmt -> bind_result($Ilosc_suszu1);
					$stmt -> store_result();
					$stmt->data_seek(0);
						if ($stmt -> fetch()){
							printf("<br /><br /> <b>Ilość suszu na I zmianie:</b>&nbsp %s kg", $Ilosc_suszu1);
							}
						}

					//Wyciągmy wartość iloci suszu na 2 zmianie
					if ($stmt = $mysqli -> prepare("SELECT IloscSuszuZmiana2 FROM `" . $asortyment . "` WHERE Data=? AND NrSuszarni=?"))
						{
					/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
					$stmt -> bind_param("ss",$data_do_odczytu,$nr_suszarni);

					$stmt -> execute();
					$stmt -> bind_result($Ilosc_suszu2);
					$stmt -> store_result();
					$stmt->data_seek(0);
						if ($stmt -> fetch()){
							printf("<br /><br /> <b>Ilość suszu na II zmianie:</b>&nbsp %s kg", $Ilosc_suszu2);
							}
						}

					//Wyciągmy wartość iloci suszu na 3 zmianie
					if ($stmt = $mysqli -> prepare("SELECT IloscSuszuZmiana3 FROM `" . $asortyment . "` WHERE Data=? AND NrSuszarni=?"))
						{
					/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
					$stmt -> bind_param("ss",$data_do_odczytu,$nr_suszarni);

					$stmt -> execute();
					$stmt -> bind_result($Ilosc_suszu3);
					$stmt -> store_result();
					$stmt->data_seek(0);
						if ($stmt -> fetch()){
							printf("<br /><br /> <b>Ilość suszu na III zmianie:</b>&nbsp %s kg", $Ilosc_suszu3);
							}
						}

					//Wyciągmy wartość całkwitej iloci suszu
					if ($stmt = $mysqli -> prepare("SELECT CalkowitaIloscSuszu FROM `" . $asortyment . "` WHERE Data=? AND NrSuszarni=?"))
						{
					/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
					$stmt -> bind_param("ss",$data_do_odczytu,$nr_suszarni);

					$stmt -> execute();
					$stmt -> bind_result($Ilosc_suszu);
					$stmt -> store_result();
					$stmt->data_seek(0);
						if ($stmt -> fetch()){
							printf("<br /><br /> <b>Całkowita ilość suszu:</b>&nbsp %s kg", $Ilosc_suszu);
							}
						}

						//Wyciągmy info o dostawcy
					if ($stmt = $mysqli -> prepare("SELECT Dostawca FROM `" . $asortyment . "` WHERE Data=? AND NrSuszarni=?"))
						{
					/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
					$stmt -> bind_param("ss",$data_do_odczytu,$nr_suszarni);

					$stmt -> execute();
					$stmt -> bind_result($Dostawca);
					$stmt -> store_result();
					$stmt->data_seek(0);
						if ($stmt -> fetch()){
							printf("<br /><br /> <b>Dostawca:</b>&nbsp %s ", $Dostawca);
							}
						}

						//Wyciągmy informacje o uwagach
					if ($stmt = $mysqli -> prepare("SELECT Uwagi FROM `" . $asortyment . "` WHERE Data=? AND NrSuszarni=?"))
						{
					/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
					$stmt -> bind_param("ss",$data_do_odczytu,$nr_suszarni);

					$stmt -> execute();
					$stmt -> bind_result($Uwagi);
					$stmt -> store_result();
					$stmt->data_seek(0);
						if ($stmt -> fetch()){
							printf("<br /><br /> <b>Uwagi:</b>&nbsp %s <br / ><br / >", $Uwagi);
							}
						}



    							$stmt->close();
								}
					}
				}

								else {
									echo '<div class="alert alert-info"><strong>Info!</strong>&nbsp Dokonano wpisu ale nie można dokonac odczytu z bazy danych.</div>';
									}
							$stmt->close();

					}
					$mysqli -> close();
				}
			}



			//Obróbka formularza "Informcje dodatkowe"
			if (isset($_POST['info_dodatkowe']))
			{

				function filtruj($zmienna) {
					$data = trim($zmienna);
					//usuwa spacje, tagi
					$data = stripslashes($zmienna);
					//usuwa slashe
					$data = htmlspecialchars($zmienna);
					//zamienia tagi html na czytelne znaki aby w formularzu nie wpisać szkodliwego kodu
					return $zmienna;
				}

				/*Odbieramy dane z formularza*/
				$asortyment = filtruj($_POST['asortyment']);
				$data = filtruj($_POST['data']);
				$nr_suszarni = filtruj($_POST['nr_suszarni']);

				$ocena_zmiany1=filtruj($_POST['ocena_zmiany1']);
				$ocena_zmiany2=filtruj($_POST['ocena_zmiany2']);
				$ocena_zmiany3=filtruj($_POST['ocena_zmiany3']);

				$susz_zmiana1=filtruj($_POST['susz_zmiana1']);
				$susz_zmiana2=filtruj($_POST['susz_zmiana2']);
				$susz_zmiana3=filtruj($_POST['susz_zmiana3']);

				//$ilosc_suszu=filtruj($_POST['ilosc_suszu']);
				$ilosc_suszu=$susz_zmiana1+$susz_zmiana2+$susz_zmiana3;
				$dostawca = filtruj($_POST['dostawca']);
				$uwagi = filtruj($_POST['uwagi']);




				$zdjecie = $_FILES['zdjecie']['name'];
				$sciezka = "grafika/zdjecia_raporty_suszenia/".basename($_FILES['zdjecie']['name']);
				$opis_zdjecia = filtruj($_POST['opis_zdjecia']);
				$typ_zdjecia = $_FILES['zdjecie']['type'];

				$dane="";

				if (((!empty($dostawca)&&(!$dostawca == null))||(!empty($ocena_zmiany3)&&(!$ocena_zmiany3 == null))||(!empty($ocena_zmiany2)&&(!$ocena_zmiany2 == null))||(!empty($ocena_zmiany2)&&(!$ocena_zmiany2 == null))||(!empty($ocena_zmiany1)&&(!$ocena_zmiany1 == null))||(!empty($susz_zmiana3)&&(!$susz_zmiana3 == null))||(!empty($susz_zmiana2)&&(!$susz_zmiana2 == null))||(!empty($susz_zmiana1)&&(!$susz_zmiana1 == null))||(!empty($ilosc_suszu)&&(!$ilosc_suszu == null))||!empty($zdjecie)||(!empty($zdjecie) && !empty($opis_zdjecia)) || (isset($_POST['uwagi']) && !$uwagi == null)) && (isset($_POST['asortyment']) && !$asortyment == null && isset($_POST['data']) && !$data == null))
				{
					$dane=TRUE;
				}


				if ($dane)
				{
					//* Łączymy się z serwerem */
					require_once ('polaczenie_z_baza.php');

					if (mysqli_connect_errno()) {

						printf("<div class='alert alert-danger'><strong>Uwaga!</strong>&nbspBrak połączenia z serwerem MySQL. Kod błędu: %s\n</div>", mysqli_connect_error());
					}
						else
						{

						//usuwamy specjalne znaki takie jak '," aby nie możnabyło wpisać ich z formularza do zapytania SQL
						$ocena_zmiany1 = $mysqli -> real_escape_string($ocena_zmiany1);
						$ocena_zmiany2 = $mysqli -> real_escape_string($ocena_zmiany2);
						$ocena_zmiany3 = $mysqli -> real_escape_string($ocena_zmiany3);
						$susz_zmiana1 = $mysqli -> real_escape_string($susz_zmiana1);
						$susz_zmiana2 = $mysqli -> real_escape_string($susz_zmiana2);
						$susz_zmiana3 = $mysqli -> real_escape_string($susz_zmiana3);
						$ilosc_suszu = $mysqli -> real_escape_string($ilosc_suszu);
						$dostawca = $mysqli -> real_escape_string($dostawca);
						$uwagi = $mysqli -> real_escape_string($uwagi);
						$asortyment = $mysqli -> real_escape_string($asortyment);
						$opis_zdjecia = $mysqli-> real_escape_string($opis_zdjecia);

						$opcja1="";
						$opcja2="";
						$opcja3="";
						$opcja4="";
						$opcja5="";
						$opcja6="";
						$opcja7="";
						$opcja8="";
						$opcja9="";
						$opcja10="";
						$opcja11="";

							//Opcja 1 jeśli podano tylko uwagi
							if (!$uwagi == null) {

							if (strlen($uwagi) > 180) {
							 		echo "<div class='alert alert-warning'><strong>Ostrzeżenie!</strong>&nbsp Pole 'Uwagi' przekroczyło maksymalną wartość znaków. Dopuszczalna ilość znaków = <b>180</b>. </div>";
								}
								else {

									$zapytanie1="";
									 $zapytanie2="";
									 $zapytanie3="";

									switch ($nr_suszarni) {
										case '1 i 2':
												//Tworzymy zapytanie
												if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET Uwagi=? WHERE Data=? AND NrSuszarni=1 LIMIT 1"))
												 {
												/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
												$stmt -> bind_param("ss",$uwagi,$data);
												$stmt -> execute();
												$zapytanie1=TRUE;
												 }

												 //Tworzymy zapytanie
												if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET Uwagi=? WHERE Data=? AND NrSuszarni=2 LIMIT 1"))
												 {
												/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
												$stmt -> bind_param("ss",$uwagi,$data);
												$stmt -> execute();
												$zapytanie2=TRUE;
												 }

												 if ($zapytanie1 && $zapytanie2) {
												 	$opcja1=TRUE;
												 }

											break;

										case '3 i 4':
												//Tworzymy zapytanie
												if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET Uwagi=? WHERE Data=? AND NrSuszarni=3 LIMIT 1"))
												 {
												/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
												$stmt -> bind_param("ss",$uwagi,$data);
												$stmt -> execute();
												$zapytanie1=TRUE;
												 }

												 //Tworzymy zapytanie
												if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET Uwagi=? WHERE Data=? AND NrSuszarni=4 LIMIT 1"))
												 {
												/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
												$stmt -> bind_param("ss",$uwagi,$data);
												$stmt -> execute();
												$zapytanie2=TRUE;
												 }

												 if ($zapytanie1 && $zapytanie2) {
												 	$opcja1=TRUE;
												 }

											break;

										case '2 i 3 i 4':
												//Tworzymy zapytanie
												if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET Uwagi=? WHERE Data=? AND NrSuszarni=2 LIMIT 1"))
												 {
												/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
												$stmt -> bind_param("ss",$uwagi,$data);
												$stmt -> execute();
												$zapytanie1=TRUE;
												 }

												 //Tworzymy zapytanie
												if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET Uwagi=? WHERE Data=? AND NrSuszarni=3 LIMIT 1"))
												 {
												/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
												$stmt -> bind_param("ss",$uwagi,$data);
												$stmt -> execute();
												$zapytanie2=TRUE;
												 }

												 //Tworzymy zapytanie
												if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET Uwagi=? WHERE Data=? AND NrSuszarni=4 LIMIT 1"))
												 {
												/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
												$stmt -> bind_param("ss",$uwagi,$data);
												$stmt -> execute();
												$zapytanie3=TRUE;
												 }

												 if ($zapytanie1 && $zapytanie2 && $zapytanie3) {
												 	$opcja1=TRUE;
												 }

											break;

										default:
												//Tworzymy zapytanie
												if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET Uwagi=? WHERE Data=? AND NrSuszarni=? LIMIT 1"))
												 {
												/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
												$stmt -> bind_param("sss",$uwagi,$data,$nr_suszarni);
												$stmt -> execute();
												$opcja1=TRUE;
												 }
											break;
									}

								}
							}

							//Opcja2 -Jeśli oprócz asortymentu i nr raportu podano zdjęcie i opis
							if (!empty($zdjecie) && !empty($opis_zdjecia) )
							 {
							 	if ($typ_zdjecia=="image/jpg" || $typ_zdjecia=="image/jpeg" || $typ_zdjecia=="image/pjpeg" || $typ_zdjecia=="image/png" || $typ_zdjecia=="image/x-png"  || $typ_zdjecia=="image/gif")
								 {

							//Zmieniamy istniejące dane
							if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET Zdjecia=?,OpisZdjecia=? WHERE Data=? AND NrSuszarni=?  LIMIT 1"))
							 {

							/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
							$stmt -> bind_param("ssss",$zdjecie,$opis_zdjecia,$data,$nr_suszarni );
							$stmt -> execute();
							if (move_uploaded_file($_FILES['zdjecie']['tmp_name'], $sciezka)) {
								$opcja2=true;
							}
								else {
									echo "<div class='alert alert-warning'><strong>Ostrzeżenie!</strong>&nbsp Wystąpił błąd. Nie dodano zdjecia.</div>";
									break;
										}
							 }

							 }
								 else {
									echo "<div class='alert alert-warning'><strong>Ostrzeżenie!</strong>&nbsp Wybrano zły format zdjęcia. Dopuszczalne formaty to: jpeg, jpg, gif oraz png.</div>";
									break;
										}

							}

							//Opcja3 -Jeśli oprócz asortymentu i nr raportu podano zdjęcie
							if (!empty($zdjecie))
							 {
							 	if ($typ_zdjecia=="image/jpg" || $typ_zdjecia=="image/jpeg" || $typ_zdjecia=="image/pjpeg" || $typ_zdjecia=="image/png" || $typ_zdjecia=="image/x-png"  || $typ_zdjecia=="image/gif")
								 {

							//Zmieniamy istniejące dane
							if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET Zdjecia=? WHERE Data=? AND NrSuszarni=? LIMIT 1"))
							 {

							/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
							$stmt -> bind_param("sss",$zdjecie,$data,$nr_suszarni );
							$stmt -> execute();
							if (move_uploaded_file($_FILES['zdjecie']['tmp_name'], $sciezka)) {
								$opcja3=true;
							}
								else {
									echo "<div class='alert alert-warning'><strong>Ostrzeżenie!</strong>&nbsp Wystąpił błąd. Nie dodano zdjecia.</div>";
									break;
										}
							 }

							 }
								 else {
									echo "<div class='alert alert-warning'><strong>Ostrzeżenie!</strong>&nbsp Wybrano zły format zdjęcia. Dopuszczalne formaty to: jpeg, jpg, gif oraz png.</div>";
									break;
										}

							}


							//Opcja 4 jeśli podano ilość suszu
							if (!$ilosc_suszu == null)
							{
								$zapytanie1="";
									 $zapytanie2="";
									 $zapytanie3="";
								//Tworzymy zapytanie

								switch ($nr_suszarni) {
									case '1 i 2':

									 	if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET CalkowitaIloscSuszu=? WHERE Data=? AND NrSuszarni=1 LIMIT 1"))
									 {
									/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
									$stmt -> bind_param("ss",$ilosc_suszu,$data);
									$stmt -> execute();
									$zapytanie1=TRUE;
									 }

									 if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET CalkowitaIloscSuszu=? WHERE Data=? AND NrSuszarni=2 LIMIT 1"))
									 {
									/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
									$stmt -> bind_param("ss",$ilosc_suszu,$data);
									$stmt -> execute();
									$zapytanie2=TRUE;
									 }

									 if ($zapytanie1 && $zapytanie2) {
										 $opcja4=TRUE;
									 }


										 break;

									case '3 i 4':

									 	if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET CalkowitaIloscSuszu=? WHERE Data=? AND NrSuszarni=3 LIMIT 1"))
									 {
									/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
									$stmt -> bind_param("ss",$ilosc_suszu,$data);
									$stmt -> execute();
									$zapytanie1=TRUE;
									 }

									 if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET CalkowitaIloscSuszu=? WHERE Data=? AND NrSuszarni=4 LIMIT 1"))
									 {
									/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
									$stmt -> bind_param("ss",$ilosc_suszu,$data);
									$stmt -> execute();
									$zapytanie2=TRUE;
									 }

									 if ($zapytanie1 && $zapytanie2) {
										 $opcja4=TRUE;
									 }


										 break;

									case '2 i 3 i 4':

									 	if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET CalkowitaIloscSuszu=? WHERE Data=? AND NrSuszarni=2 LIMIT 1"))
									 {
									/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
									$stmt -> bind_param("ss",$ilosc_suszu,$data);
									$stmt -> execute();
									$zapytanie1=TRUE;
									 }

									 if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET CalkowitaIloscSuszu=? WHERE Data=? AND NrSuszarni=3 LIMIT 1"))
									 {
									/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
									$stmt -> bind_param("ss",$ilosc_suszu,$data);
									$stmt -> execute();
									$zapytanie2=TRUE;
									 }

									  if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET CalkowitaIloscSuszu=? WHERE Data=? AND NrSuszarni=4 LIMIT 1"))
									 {
									/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
									$stmt -> bind_param("ss",$ilosc_suszu,$data);
									$stmt -> execute();
									$zapytanie3=TRUE;
									 }

									 if ($zapytanie1 && $zapytanie2 && $zapytanie3) {
										 $opcja4=TRUE;
									 }


										 break;

									default:
										//Tworzymy zapytanie
										if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET CalkowitaIloscSuszu=? WHERE Data=? AND NrSuszarni=? LIMIT 1"))
										 {
										/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
										$stmt -> bind_param("sss",$ilosc_suszu,$data,$nr_suszarni );
										$stmt -> execute();
										$opcja4=TRUE;
										 }

										break;
								}

							}

							//Opcja 5 jeśli podano ilość suszu 1 zmiana
							if (!$susz_zmiana1 == null)
							{
								  	 $zapytanie1="";
									 $zapytanie2="";
									 $zapytanie3="";
								//Tworzymy zapytanie
								 switch ($nr_suszarni)
								  {
									 case '1 i 2':

									 	if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET IloscSuszuZmiana1=? WHERE Data=? AND NrSuszarni=1 LIMIT 1"))
									 {
									/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
									$stmt -> bind_param("ss",$susz_zmiana1,$data);
									$stmt -> execute();
									$zapytanie1=TRUE;
									 }

									 if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET IloscSuszuZmiana1=? WHERE Data=? AND NrSuszarni=2 LIMIT 1"))
									 {
									/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
									$stmt -> bind_param("ss",$susz_zmiana1,$data);
									$stmt -> execute();
									$zapytanie2=TRUE;
									 }

									 if ($zapytanie1 && $zapytanie2) {
										 $opcja5=TRUE;
									 }


										 break;

									case '3 i 4':

									 	if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET IloscSuszuZmiana1=? WHERE Data=? AND NrSuszarni=3 LIMIT 1"))
									 {
									/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
									$stmt -> bind_param("ss",$susz_zmiana1,$data);
									$stmt -> execute();
									$zapytanie1=TRUE;
									 }

									 if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET IloscSuszuZmiana1=? WHERE Data=? AND NrSuszarni=4 LIMIT 1"))
									 {
									/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
									$stmt -> bind_param("ss",$susz_zmiana1,$data);
									$stmt -> execute();
									$zapytanie2=TRUE;
									 }

									 if ($zapytanie1 && $zapytanie2) {
										 $opcja5=TRUE;
									 }


										 break;

									case '2 i 3 i 4':

									 	if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET IloscSuszuZmiana1=? WHERE Data=? AND NrSuszarni=2 LIMIT 1"))
									 {
									/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
									$stmt -> bind_param("ss",$susz_zmiana1,$data);
									$stmt -> execute();
									$zapytanie1=TRUE;
									 }

									 if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET IloscSuszuZmiana1=? WHERE Data=? AND NrSuszarni=3 LIMIT 1"))
									 {
									/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
									$stmt -> bind_param("ss",$susz_zmiana1,$data);
									$stmt -> execute();
									$zapytanie2=TRUE;
									 }

									  if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET IloscSuszuZmiana1=? WHERE Data=? AND NrSuszarni=4 LIMIT 1"))
									 {
									/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
									$stmt -> bind_param("ss",$susz_zmiana1,$data);
									$stmt -> execute();
									$zapytanie3=TRUE;
									 }

									 if ($zapytanie1 && $zapytanie2 && $zapytanie3) {
										 $opcja5=TRUE;
									 }


										 break;

									 default:
										 if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET IloscSuszuZmiana1=? WHERE Data=? AND NrSuszarni=? LIMIT 1"))
									 {
									/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
									$stmt -> bind_param("sss",$susz_zmiana1,$data,$nr_suszarni);
									$stmt -> execute();
									$opcja5=TRUE;
									 }
										 break;
								 }


							}

							//Opcja 6 jeśli podano ilość suszu 2 zmiana
							if (!$susz_zmiana2 == null)
							{
									$zapytanie1="";
									 $zapytanie2="";
									 $zapytanie3="";
								//Tworzymy zapytanie

								switch ($nr_suszarni) {
									case '1 i 2':

									 	if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET IloscSuszuZmiana2=? WHERE Data=? AND NrSuszarni=1 LIMIT 1"))
									 {
									/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
									$stmt -> bind_param("ss",$susz_zmiana2,$data);
									$stmt -> execute();
									$zapytanie1=TRUE;
									 }

									 if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET IloscSuszuZmiana2=? WHERE Data=? AND NrSuszarni=2 LIMIT 1"))
									 {
									/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
									$stmt -> bind_param("ss",$susz_zmiana2,$data);
									$stmt -> execute();
									$zapytanie2=TRUE;
									 }

									 if ($zapytanie1 && $zapytanie2) {
										 $opcja6=TRUE;
									 }


										 break;

									case '3 i 4':

									 	if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET IloscSuszuZmiana2=? WHERE Data=? AND NrSuszarni=3 LIMIT 1"))
									 {
									/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
									$stmt -> bind_param("ss",$susz_zmiana2,$data);
									$stmt -> execute();
									$zapytanie1=TRUE;
									 }

									 if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET IloscSuszuZmiana2=? WHERE Data=? AND NrSuszarni=4 LIMIT 1"))
									 {
									/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
									$stmt -> bind_param("ss",$susz_zmiana2,$data);
									$stmt -> execute();
									$zapytanie2=TRUE;
									 }

									 if ($zapytanie1 && $zapytanie2) {
										 $opcja6=TRUE;
									 }


										 break;

									case '2 i 3 i 4':

									 	if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET IloscSuszuZmiana2=? WHERE Data=? AND NrSuszarni=2 LIMIT 1"))
									 {
									/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
									$stmt -> bind_param("ss",$susz_zmiana2,$data);
									$stmt -> execute();
									$zapytanie1=TRUE;
									 }

									 if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET IloscSuszuZmiana2=? WHERE Data=? AND NrSuszarni=3 LIMIT 1"))
									 {
									/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
									$stmt -> bind_param("ss",$susz_zmiana2,$data);
									$stmt -> execute();
									$zapytanie2=TRUE;
									 }

									  if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET IloscSuszuZmiana2=? WHERE Data=? AND NrSuszarni=4 LIMIT 1"))
									 {
									/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
									$stmt -> bind_param("ss",$susz_zmiana2,$data);
									$stmt -> execute();
									$zapytanie3=TRUE;
									 }

									 if ($zapytanie1 && $zapytanie2 && $zapytanie3) {
										 $opcja6=TRUE;
									 }


										 break;

									default:
										if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET IloscSuszuZmiana2=? WHERE Data=? AND NrSuszarni=? LIMIT 1"))
										 {
										/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
										$stmt -> bind_param("sss",$susz_zmiana2,$data,$nr_suszarni );
										$stmt -> execute();
										$opcja6=TRUE;
										 }

										break;
								}

							}

							//Opcja 7 jeśli podano ilość suszu 3 zmiana
							if (!$susz_zmiana3 == null)
							{
									$zapytanie1="";
									 $zapytanie2="";
									 $zapytanie3="";
								//Tworzymy zapytanie

								switch ($nr_suszarni) {
									case '1 i 2':

									 	if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET IloscSuszuZmiana3=? WHERE Data=? AND NrSuszarni=1 LIMIT 1"))
									 {
									/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
									$stmt -> bind_param("ss",$susz_zmiana3,$data);
									$stmt -> execute();
									$zapytanie1=TRUE;
									 }

									 if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET IloscSuszuZmiana3=? WHERE Data=? AND NrSuszarni=2 LIMIT 1"))
									 {
									/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
									$stmt -> bind_param("ss",$susz_zmiana3,$data);
									$stmt -> execute();
									$zapytanie2=TRUE;
									 }

									 if ($zapytanie1 && $zapytanie2) {
										 $opcja7=TRUE;
									 }


										 break;

									case '3 i 4':

									 	if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET IloscSuszuZmiana3=? WHERE Data=? AND NrSuszarni=3 LIMIT 1"))
									 {
									/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
									$stmt -> bind_param("ss",$susz_zmiana3,$data);
									$stmt -> execute();
									$zapytanie1=TRUE;
									 }

									 if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET IloscSuszuZmiana3=? WHERE Data=? AND NrSuszarni=4 LIMIT 1"))
									 {
									/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
									$stmt -> bind_param("ss",$susz_zmiana3,$data);
									$stmt -> execute();
									$zapytanie2=TRUE;
									 }

									 if ($zapytanie1 && $zapytanie2) {
										 $opcja7=TRUE;
									 }


										 break;

									case '2 i 3 i 4':

									 	if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET IloscSuszuZmiana3=? WHERE Data=? AND NrSuszarni=2 LIMIT 1"))
									 {
									/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
									$stmt -> bind_param("ss",$susz_zmiana3,$data);
									$stmt -> execute();
									$zapytanie1=TRUE;
									 }

									 if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET IloscSuszuZmiana3=? WHERE Data=? AND NrSuszarni=3 LIMIT 1"))
									 {
									/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
									$stmt -> bind_param("ss",$susz_zmiana3,$data);
									$stmt -> execute();
									$zapytanie2=TRUE;
									 }

									  if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET IloscSuszuZmiana3=? WHERE Data=? AND NrSuszarni=4 LIMIT 1"))
									 {
									/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
									$stmt -> bind_param("ss",$susz_zmiana3,$data);
									$stmt -> execute();
									$zapytanie3=TRUE;
									 }

									 if ($zapytanie1 && $zapytanie2 && $zapytanie3) {
										 $opcja7=TRUE;
									 }


										 break;

									default:
										if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET IloscSuszuZmiana3=? WHERE Data=? AND NrSuszarni=? LIMIT 1"))
										 {
										/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
										$stmt -> bind_param("sss",$susz_zmiana3,$data,$nr_suszarni );
										$stmt -> execute();
										$opcja7=TRUE;
										 }

										break;
								}
							}

							//Opcja 8 jeśli podano ocene suszu po zmainie 1
							if (!$ocena_zmiany1 == null)
							{
									$zapytanie1="";
									 $zapytanie2="";
									 $zapytanie3="";

								switch ($nr_suszarni) {
									case '1 i 2':
												//Tworzymy zapytanie
										if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET OcenaTowaruZmiany1=? WHERE Data=? AND NrSuszarni=1 LIMIT 1"))
										 {
										/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
										$stmt -> bind_param("ss",$ocena_zmiany1,$data );
										$stmt -> execute();
										$zapytanie1=TRUE;
										 }

										 //Tworzymy zapytanie
										if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET OcenaTowaruZmiany1=? WHERE Data=? AND NrSuszarni=2 LIMIT 1"))
										 {
										/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
										$stmt -> bind_param("ss",$ocena_zmiany1,$data );
										$stmt -> execute();
										$zapytanie2=TRUE;
										 }

										 if ($zapytanie1 && $zapytanie2) {
											 $opcja8=TRUE;
										 }

										break;

										case '3 i 4':
												//Tworzymy zapytanie
										if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET OcenaTowaruZmiany1=? WHERE Data=? AND NrSuszarni=3 LIMIT 1"))
										 {
										/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
										$stmt -> bind_param("ss",$ocena_zmiany1,$data );
										$stmt -> execute();
										$zapytanie1=TRUE;
										 }

										 //Tworzymy zapytanie
										if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET OcenaTowaruZmiany1=? WHERE Data=? AND NrSuszarni=4 LIMIT 1"))
										 {
										/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
										$stmt -> bind_param("ss",$ocena_zmiany1,$data );
										$stmt -> execute();
										$zapytanie2=TRUE;
										 }

										 if ($zapytanie1 && $zapytanie2) {
											 $opcja8=TRUE;
										 }

										break;

									case '2 i 3 i 4':
												//Tworzymy zapytanie
										if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET OcenaTowaruZmiany1=? WHERE Data=? AND NrSuszarni=2 LIMIT 1"))
										 {
										/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
										$stmt -> bind_param("ss",$ocena_zmiany1,$data );
										$stmt -> execute();
										$zapytanie1=TRUE;
										 }

										 //Tworzymy zapytanie
										if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET OcenaTowaruZmiany1=? WHERE Data=? AND NrSuszarni=3 LIMIT 1"))
										 {
										/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
										$stmt -> bind_param("ss",$ocena_zmiany1,$data );
										$stmt -> execute();
										$zapytanie2=TRUE;
										 }

										//Tworzymy zapytanie
										if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET OcenaTowaruZmiany1=? WHERE Data=? AND NrSuszarni=4 LIMIT 1"))
										 {
										/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
										$stmt -> bind_param("ss",$ocena_zmiany1,$data );
										$stmt -> execute();
										$zapytanie3=TRUE;
										 }

										 if ($zapytanie1 && $zapytanie2 && $zapytanie3) {
											 $opcja8=TRUE;
										 }

										break;

									default:
										//Tworzymy zapytanie
										if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET OcenaTowaruZmiany1=? WHERE Data=? AND NrSuszarni=? LIMIT 1"))
										 {
										/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
										$stmt -> bind_param("sss",$ocena_zmiany1,$data,$nr_suszarni );
										$stmt -> execute();
										$opcja8=TRUE;
										 }

										break;
								}

							}

							//Opcja 9 jeśli podano ocene suszu po zmainie 2
							if (!$ocena_zmiany2 == null)
							{
								$zapytanie1="";
									 $zapytanie2="";
									 $zapytanie3="";

								switch ($nr_suszarni) {
									case '1 i 2':
												//Tworzymy zapytanie
										if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET OcenaTowaruZmiany2=? WHERE Data=? AND NrSuszarni=1 LIMIT 1"))
										 {
										/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
										$stmt -> bind_param("ss",$ocena_zmiany2,$data );
										$stmt -> execute();
										$zapytanie1=TRUE;
										 }

										 //Tworzymy zapytanie
										if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET OcenaTowaruZmiany2=? WHERE Data=? AND NrSuszarni=2 LIMIT 1"))
										 {
										/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
										$stmt -> bind_param("ss",$ocena_zmiany2,$data );
										$stmt -> execute();
										$zapytanie2=TRUE;
										 }

										 if ($zapytanie1 && $zapytanie2) {
											 $opcja9=TRUE;
										 }

										break;

										case '3 i 4':
												//Tworzymy zapytanie
												if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET OcenaTowaruZmiany2=? WHERE Data=? AND NrSuszarni=3 LIMIT 1"))
												 {
												/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
												$stmt -> bind_param("ss",$ocena_zmiany2,$data );
												$stmt -> execute();
												$zapytanie1=TRUE;
												 }

												 //Tworzymy zapytanie
												if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET OcenaTowaruZmiany2=? WHERE Data=? AND NrSuszarni=4 LIMIT 1"))
												 {
												/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
												$stmt -> bind_param("ss",$ocena_zmiany2,$data );
												$stmt -> execute();
												$zapytanie2=TRUE;
												 }

												 if ($zapytanie1 && $zapytanie2) {
													 $opcja9=TRUE;
												 }

										break;

									case '2 i 3 i 4':
												//Tworzymy zapytanie
										if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET OcenaTowaruZmiany2=? WHERE Data=? AND NrSuszarni=2 LIMIT 1"))
										 {
										/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
										$stmt -> bind_param("ss",$ocena_zmiany2,$data );
										$stmt -> execute();
										$zapytanie1=TRUE;
										 }

										 //Tworzymy zapytanie
										if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET OcenaTowaruZmiany2=? WHERE Data=? AND NrSuszarni=3 LIMIT 1"))
										 {
										/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
										$stmt -> bind_param("ss",$ocena_zmiany2,$data );
										$stmt -> execute();
										$zapytanie2=TRUE;
										 }

										//Tworzymy zapytanie
										if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET OcenaTowaruZmiany2=? WHERE Data=? AND NrSuszarni=4 LIMIT 1"))
										 {
										/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
										$stmt -> bind_param("ss",$ocena_zmiany2,$data );
										$stmt -> execute();
										$zapytanie3=TRUE;
										 }

										 if ($zapytanie1 && $zapytanie2 && $zapytanie3) {
											 $opcja9=TRUE;
										 }

										break;

									default:
										//Tworzymy zapytanie
										if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET OcenaTowaruZmiany2=? WHERE Data=? AND NrSuszarni=? LIMIT 1"))
										 {
										/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
										$stmt -> bind_param("sss",$ocena_zmiany2,$data,$nr_suszarni );
										$stmt -> execute();
										$opcja9=TRUE;
										 }

										break;
								}


							}

							//Opcja 10 jeśli podano ocene suszu po zmainie 3
							if (!$ocena_zmiany3 == null)
							{
								$zapytanie1="";
									 $zapytanie2="";
									 $zapytanie3="";

								switch ($nr_suszarni) {
									case '1 i 2':
												//Tworzymy zapytanie
										if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET OcenaTowaruZmiany3=? WHERE Data=? AND NrSuszarni=1 LIMIT 1"))
										 {
										/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
										$stmt -> bind_param("ss",$ocena_zmiany3,$data );
										$stmt -> execute();
										$zapytanie1=TRUE;
										 }

										 //Tworzymy zapytanie
										if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET OcenaTowaruZmiany3=? WHERE Data=? AND NrSuszarni=2 LIMIT 1"))
										 {
										/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
										$stmt -> bind_param("ss",$ocena_zmiany3,$data );
										$stmt -> execute();
										$zapytanie2=TRUE;
										 }

										 if ($zapytanie1 && $zapytanie2) {
											 $opcja10=TRUE;
										 }

										break;

										case '3 i 4':
												//Tworzymy zapytanie
										if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET OcenaTowaruZmiany3=? WHERE Data=? AND NrSuszarni=3 LIMIT 1"))
										 {
										/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
										$stmt -> bind_param("ss",$ocena_zmiany3,$data );
										$stmt -> execute();
										$zapytanie1=TRUE;
										 }

										 //Tworzymy zapytanie
										if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET OcenaTowaruZmiany3=? WHERE Data=? AND NrSuszarni=4 LIMIT 1"))
										 {
										/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
										$stmt -> bind_param("ss",$ocena_zmiany3,$data );
										$stmt -> execute();
										$zapytanie2=TRUE;
										 }

										 if ($zapytanie1 && $zapytanie2) {
											 $opcja10=TRUE;
										 }

										break;

									case '2 i 3 i 4':
												//Tworzymy zapytanie
										if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET OcenaTowaruZmiany3=? WHERE Data=? AND NrSuszarni=2 LIMIT 1"))
										 {
										/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
										$stmt -> bind_param("ss",$ocena_zmiany3,$data );
										$stmt -> execute();
										$zapytanie1=TRUE;
										 }

										 //Tworzymy zapytanie
										if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET OcenaTowaruZmiany3=? WHERE Data=? AND NrSuszarni=3 LIMIT 1"))
										 {
										/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
										$stmt -> bind_param("ss",$ocena_zmiany3,$data );
										$stmt -> execute();
										$zapytanie2=TRUE;
										 }

										//Tworzymy zapytanie
										if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET OcenaTowaruZmiany3=? WHERE Data=? AND NrSuszarni=4 LIMIT 1"))
										 {
										/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
										$stmt -> bind_param("ss",$ocena_zmiany3,$data );
										$stmt -> execute();
										$zapytanie3=TRUE;
										 }

										 if ($zapytanie1 && $zapytanie2 && $zapytanie3) {
											 $opcja10=TRUE;
										 }

										break;

									default:

										//Tworzymy zapytanie
										if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET OcenaTowaruZmiany3=? WHERE Data=? AND NrSuszarni=? LIMIT 1"))
										 {
										/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
										$stmt -> bind_param("sss",$ocena_zmiany3,$data,$nr_suszarni);
										$stmt -> execute();
										$opcja10=TRUE;
										 }

										break;
								}


							}

							//Opcja 11 jeśli podano dostawce
							if (!$dostawca == null)
							{
								$zapytanie1="";
								$zapytanie2="";
								$zapytanie3="";

								switch ($nr_suszarni) {
									case '1 i 2':
										//Tworzymy zapytanie
										if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET Dostawca=? WHERE Data=? AND NrSuszarni=1 LIMIT 1"))
										 {
										/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
										$stmt -> bind_param("ss",$dostawca,$data);
										$stmt -> execute();
										$zapytanie1=TRUE;
										 }

										 //Tworzymy zapytanie
										if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET Dostawca=? WHERE Data=? AND NrSuszarni=2 LIMIT 1"))
										 {
										/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
										$stmt -> bind_param("ss",$dostawca,$data);
										$stmt -> execute();
										$zapytanie2=TRUE;
										 }

										 if ($zapytanie1 && $zapytanie2) {
											 $opcja11=TRUE;
										 }

										break;

										case '3 i 4':
										//Tworzymy zapytanie
										if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET Dostawca=? WHERE Data=? AND NrSuszarni=3 LIMIT 1"))
										 {
										/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
										$stmt -> bind_param("ss",$dostawca,$data);
										$stmt -> execute();
										$zapytanie1=TRUE;
										 }

										 //Tworzymy zapytanie
										if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET Dostawca=? WHERE Data=? AND NrSuszarni=4 LIMIT 1"))
										 {
										/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
										$stmt -> bind_param("ss",$dostawca,$data);
										$stmt -> execute();
										$zapytanie2=TRUE;
										 }

										 if ($zapytanie1 && $zapytanie2) {
											 $opcja11=TRUE;
										 }

										break;

										case '2 i 3 i 4':
										//Tworzymy zapytanie
										if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET Dostawca=? WHERE Data=? AND NrSuszarni=2 LIMIT 1"))
										 {
										/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
										$stmt -> bind_param("ss",$dostawca,$data);
										$stmt -> execute();
										$zapytanie1=TRUE;
										 }

										 //Tworzymy zapytanie
										if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET Dostawca=? WHERE Data=? AND NrSuszarni=3 LIMIT 1"))
										 {
										/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
										$stmt -> bind_param("ss",$dostawca,$data);
										$stmt -> execute();
										$zapytanie2=TRUE;
										 }

										  //Tworzymy zapytanie
										if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET Dostawca=? WHERE Data=? AND NrSuszarni=4 LIMIT 1"))
										 {
										/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
										$stmt -> bind_param("ss",$dostawca,$data);
										$stmt -> execute();
										$zapytanie3=TRUE;
										 }

										 if ($zapytanie1 && $zapytanie2 && $zapytanie3) {
											 $opcja11=TRUE;
										 }

										break;

									default:
										//Tworzymy zapytanie
										if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET Dostawca=? WHERE Data=? AND NrSuszarni=? LIMIT 1"))
										 {
										/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
										$stmt -> bind_param("sss",$dostawca,$data,$nr_suszarni);
										$stmt -> execute();
										$opcja11=TRUE;
										 }

										break;
								}

							}


							 if ($opcja1 || $opcja2 || $opcja3 || $opcja4 || $opcja5 || $opcja6|| $opcja7 || $opcja8 || $opcja9 || $opcja10 || $opcja11)
							{
								if ($stmt -> affected_rows == 0 || $stmt -> affected_rows < 0 ||$stmt->affected_rows==NULL)
								{
								echo "<div class='alert alert-warning'><span class='glyphicon glyphicon-alert'></span>&nbsp<strong>Ostrzeżenie!</strong>&nbsp Nie dokonano zapisu. Możliwy błąd zapytania.</div>";
								}

								if ($stmt -> affected_rows > 0)
								{
								echo '<div class="alert alert-success alert-dismissable fade in">
								<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
								<span class="glyphicon glyphicon-thumbs-up"></span>&nbsp;<strong>Sukces!</strong>&nbsp Dokonano zapisu danych. </div><br / >';

								}
								$stmt->close();
							}


						}
				}
				else {
						echo "<div class='alert alert-warning'><span class='glyphicon glyphicon-alert'></span>&nbsp<strong>Ostrzeżenie!</strong>&nbsp Podaj Asortyment, Datę, Nr Suszarni i jedną z danych:Ocenę towaru, Ilosć suszu, Dostawcę, Uwagi, Zdjęcie lub Zdjęcie i opis.</div>";
						}

			}
?>
			<br / >
			<br / >

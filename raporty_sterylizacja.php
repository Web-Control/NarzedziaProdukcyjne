<h1>Raport z procesu sterylizacji parowej</h1>
      <ul class="nav nav-tabs">
  <li class="active"><a href="index2.php?raporty_sterylizacja=1">Tworzenie</a></li>
  <li><a href="index2.php?raporty_sterylizacji_odczyt=1">Odczyt</a></li>
  <li><a href="index2.php?raporty_sterylizacji_pobierz=1">Pobór</a></li>
  <li><a href="index2.php?statystyki_sterylizacji=1">Wykresy</a></li>
</ul>
<br / >
<div id="formularz">
<div class="row" >
	<div class="form-group">
     <form name="raport" method="post" action="index2.php?raporty_sterylizacja=1">
				<fieldset>
					<legend>
					Stwórz raport
					</legend>
					<!--<div id="zapis_danych"> -->
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
					if ($stmt = $mysqli -> prepare("SELECT Asortyment FROM AsortymentSterylizacji ORDER BY Asortyment ASC"))
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
						$czysta_nazwa=substr($_POST['asortyment'],0,-7);//Usuwamy koncówkę _Steryl z nazwy
						echo '<option value="'.$_POST['asortyment'].'">'.$czysta_nazwa.'</option>';

					}
					else {

							foreach ($Asortyment_wbazie as $key => $value) {

							$czysta_nazwa=substr($value,0,-7);//Usuwamy koncówkę _Steryl z nazwy


							printf("<option value='%s'>%s</option>",$value,$czysta_nazwa);
							}
						}

				}
							?>
						</select>
						</div>


						<?php
						//Pobieramy informacje o ostatnim numerze raportu

						$rok=date("Y");//bierzący rok

						/* Łączymy się z serwerem */
						require_once ('polaczenie_z_baza.php');

						if (mysqli_connect_errno()) {

						printf("<div class='alert alert-danger'><span class='glyphicon glyphicon-thumbs-down'></span>&nbsp;<strong>Uwaga!</strong>&nbspBrak połączenia z serwerem MySQL. Kod błędu: %s\n</div>", mysqli_connect_error());

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
								static $a=0;
								$Asortyment_wbazie[$a]=$Obecny_asortyment;
								$a++;
    							}
    							}

								//print_r($Asortyment_wbazie);

								//Zapytanie najwyśze numery raportów w poszczególnych asortymentach
								$Numery_raportow=array();

								for ($n=0; $n < count($Asortyment_wbazie) ; $n++) {
								if ($stmt = $mysqli -> prepare("SELECT MAX(CAST(NrRaportu AS UNSIGNED)) FROM `$Asortyment_wbazie[$n]` WHERE NrRaportu LIKE '%" . $rok . "%' "))
									{
									//$stmt -> bind_param("s",$nr_raportu);
									$stmt -> execute();
									$stmt -> bind_result($max_nr_raportu);
									$stmt -> store_result();

								if ($stmt->num_rows > 0) {
								/* Wyciągamy dane z zapytania sql i zapisujemy do tablicy  */
									$stmt->data_seek(0);
    								while ($stmt->fetch())
    									{
									$Numery_raportow[$Asortyment_wbazie[$n]] = $max_nr_raportu;
								 		}
    							}

    							}
								}
								$max_raport=max($Numery_raportow);
								$info_o_maxraporcie="Ostatni numer raportu: $max_raport";
								$_SESSION['ostatni_raport']=$info_o_maxraporcie;
						}

						?>


						<div class="col-sm-4">
						<label>Nr Raportu</label>
						<a href="#"  class="podpowiedz" data-toggle="tooltip" data-placement="bottom" <?php echo "title='".$_SESSION['ostatni_raport']."'"; ?>  style="decoration:none;"><input class="form-control" type="text" name="nr_raportu" maxlength="25" required <?php echo "value='".$_POST['nr_raportu']."'" ?> /></a>
						</div>

						<div class="col-sm-4">
						<label>Odbiorca</label>&nbsp&nbsp
						Potrzeby własne<input type="radio" name="odbiorca" id="wlasne" value="Potrzeby własne" onclick="pole_klient()" <?php if ($_POST['odbiorca']=='' || $_POST['odbiorca']=='Potrzeby własne') {echo "checked";} ?> >&nbsp
  						Klient<input type="radio" name="odbiorca" id="klient" value="Klient" onclick="pole_klient()" <?php if ($_POST['odbiorca']=='Klient') {echo "checked";} ?> ><br / >

						<?php
						//Pobieramy informacje o klientach w istniejących raportach

						$rok=date("Y");//bierzący rok

						/* Łączymy się z serwerem */
						require_once ('polaczenie_z_baza.php');

						if (mysqli_connect_errno()) {

						printf("<div class='alert alert-danger'><span class='glyphicon glyphicon-thumbs-down'></span>&nbsp;<strong>Uwaga!</strong>&nbspBrak połączenia z serwerem MySQL. Kod błędu: %s\n</div>", mysqli_connect_error());

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
								static $b=0;
								$Asortyment_wbazie[$b]=$Obecny_asortyment;
								$b++;
    							}
    							}

								//print_r($Asortyment_wbazie);

								//Zapytanie najwyśze numery raportów w poszczególnych asortymentach
								$Klienci=array();

								for ($n=0; $n < count($Asortyment_wbazie) ; $n++) {
								if ($stmt = $mysqli -> prepare("SELECT DISTINCT Klient FROM `$Asortyment_wbazie[$n]` WHERE NrRaportu LIKE '%" . $rok . "%' "))
									{

									//$stmt -> bind_param("s",$nr_raportu);
									$stmt -> execute();
									$stmt -> bind_result($Klient);
									$stmt -> store_result();

								if ($stmt->num_rows > 0) {
								/* Wyciągamy dane z zapytania sql i zapisujemy do tablicy  */
									$stmt->data_seek(0);
    								while ($stmt->fetch())
    									{
									$Klienci[$Asortyment_wbazie[$n]] = $Klient;
								 		}
    							}

    							}
								}

								//print_r($Klienci);

								$Klienci_unikalni = array_unique($Klienci);

								echo '<div class="alert alert-info" id="klienci" style="display:none;"><strong>Info!</strong>&nbsp<b>Klienci w raportach:</b><br / >';
								foreach ($Klienci_unikalni as $klient) {

									echo "$klient <br / >";

								}
								echo "</div>";

						}

						?>

						<label>Klient</label>
						<input class="form-control"  style="background-color: silver;" type="text" name="klient" id="klient_nazwa" maxlength="25" readonly required <?php if ($_POST['odbiorca']=='Klient') {echo "value='".$_POST['klient']."'";} ?> >
						</div>

					</div>
					<hr></hr>

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
						<label >Prędkość Zasobnika</label>
						<input class="form-control" type="number" name="predkosc_zasobnika"  min="1" max="140" required <?php echo "value='".$_POST['predkosc_zasobnika']."'" ?>/>
						</div>

						<div class="col-sm-4">
						<label >Prędkość Śluzy nr 1</label>&nbsp;
						<input class="form-control" type="number" name="predkosc_sluzy1"  min="1" max="140" required <?php echo "value='".$_POST['predkosc_sluzy1']."'" ?>/>
						</div>

						<div class="col-sm-4">
						<label >Prędkość Śluzy nr 2</label>
						<input class="form-control" type="number" name="predkosc_sluzy2"  min="1" max="140" required <?php echo "value='".$_POST['predkosc_sluzy2']."'" ?>/>
						</div>

					</div>
					<br / ><br / >

					<div class="row">

						<div class="col-sm-4">
						<label >Prędkość Sterylizatora</label>
						<input class="form-control" type="number" name="predkosc_sterylizatora"  min="1" max="140" required <?php echo "value='".$_POST['predkosc_sterylizatora']."'" ?>/>
						</div>

						<div class="col-sm-4">
						<label >Temperatura Sterylizacji</label>
						<input class="form-control" type="number" name="temperatura_sterylizacji"  min="1" max="200" required <?php echo "value='".$_POST['temperatura_sterylizacji']."'" ?>/>
						</div>

						<div class="col-sm-4">
						<label >Ciśnienie Sterylizacji</label>
						<input class="form-control" type="number" name="cisnienie_sterylizacji"  min="1" max="999" required <?php echo "value='".$_POST['cisnienie_sterylizacji']."'" ?>/>
						</div>

					</div>
					<br / ><br / >

					<div class="row">

						<div class="col-sm-4">
						<label >Prędkość Suszarki nr 1</label>
						<input class="form-control" type="number" name="predkosc_suszarki1"  min="1" max="140" required <?php echo "value='".$_POST['predkosc_suszarki1']."'" ?>/>
						</div>

						<div class="col-sm-4">
						<label >Prędkość nadmuchu Suszarki nr 1</label>
						<input class="form-control" type="number" name="nadmuch_suszarki1"  min="1" max="140" required <?php echo "value='".$_POST['nadmuch_suszarki1']."'" ?>/>
						</div>

						<div class="col-sm-4">
						<label >Temperatura Suszarki nr 1</label>
						<input class="form-control" type="number" name="temperatura_suszarki1"  min="1" max="140" required <?php echo "value='".$_POST['temperatura_suszarki1']."'" ?>/>
						</div>

					</div>
					<br / ><br / >

					<div class="row">

						<div class="col-sm-4">
						<label >Prędkość Suszarki nr 2</label>
						<input class="form-control" type="number" name="predkosc_suszarki2"  min="1" max="140" required <?php echo "value='".$_POST['predkosc_suszarki2']."'" ?>/>
						</div>

						<div class="col-sm-4">
						<label >Prędkość nadmuchu Suszarki nr 2</label>
						<input class="form-control" type="number" name="nadmuch_suszarki2"  min="1" max="140" required <?php echo "value='".$_POST['nadmuch_suszarki2']."'" ?>/>
						</div>

						<div class="col-sm-4">
						<label >Temperatura Suszarki nr 2</label>
						<input class="form-control" type="number" name="temperatura_suszarki2"  min="1" max="140" required <?php echo "value='".$_POST['temperatura_suszarki2']."'" ?>/>
						</div>

					</div>
					<br / ><br / >

					<div class="row">

						<div class="col-sm-4">
						<label >Prędkość Chłodziarki</label>&nbsp;&nbsp;&nbsp;
						<input class="form-control" type="number" name="predkosc_chlodziarki"  min="1" max="140" required <?php echo "value='".$_POST['predkosc_chlodziarki']."'" ?>/>
						</div>

						<div class="col-sm-4">
						<label >Prędkość Nadmuchu Chłodziarki</label>&nbsp;&nbsp;
						<input class="form-control" type="number" name="nadmuch_chlodziarki"  min="1" max="140" required <?php echo "value='".$_POST['nadmuch_chlodziarki']."'" ?>/>
						</div>
					</div>
					<hr></hr>

					<div class="row">

						<div class="col-sm-4">
						<label >Wilgotność Początkowa</label>
						<input class="form-control" type="text" name="wilgotnosc_poczatkowa"  min="0" max="100" maxlength="5" required />
						</div>

						<div class="col-sm-4">
						<label >Wilgotność Końcowa</label>
						<input class="form-control" type="text" name="wilgotnosc_koncowa"  min="0" max="100" maxlength="5" />
						</div>
					</div>
					<hr></hr>

					<div class="row">

						<div class="col-sm-4">
						<label >Osoba Dokonująca Pomiaru</label>
						<select class="form-control" name="osoba_odpowiedzialna" required>
							<option value="<?php echo "" . $_SESSION['login'] . ""; ?>"><?php echo "" . $_SESSION['login'] . ""; ?></option>

						</select>
						</div>

					</div>
					<hr></hr>

					<div class="row">
						<div class="col-sm-4">
						<span class="glyphicon glyphicon-save"></span>&nbsp;<input type="submit" value="Zapisz" name="zapisz"><br / ><br / ><br / >
						<span class="glyphicon glyphicon-edit"></span>&nbsp;<input type="submit" value="Modyfikuj" name="modyfikuj"><br / ><br / >
						<span class="glyphicon glyphicon-floppy-remove"></span>&nbsp;<input type="submit" value="Usuń" name="usun"><br / ><br / ><br / >
						<span class="glyphicon glyphicon-remove"></span>&nbsp;<a href="index2.php?raporty_sterylizacja=1">Reset</a>
						</div>
					</div>

					<!--</div>-->
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

			<form name="info do raportu" method="post" enctype="multipart/form-data" action="index2.php?raporty_sterylizacja=1" >
				<fieldset>
					<legend>Informacje dodatkowe</legend>

				<div class="row" >
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
					if ($stmt = $mysqli -> prepare("SELECT Asortyment FROM AsortymentSterylizacji ORDER BY Asortyment ASC"))
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

						$czysta_nazwa=substr($value,0,-7);//Usuwamy koncówkę _Steryl z nazwy

						printf("<option value='%s'>%s</option>",$value,$czysta_nazwa);
					}

				}
				?>

						</select>
					</div>

					<div class="col-sm-4">
						<label>Nr Raportu</label>
						<input class="form-control" type="text" name="nr_raportu" maxlength="25" required/>
					</div>
				</div>
						<hr></hr>
					<div class="row">
						<div class="col-sm-4">
						<label >Sito</label>
						<input class="form-control" type="text" name="sito" maxlength="5" />
						</div>

						<div class="col-sm-4">
						<label>Odsiew</label>
						<input class="form-control" type="number" name="odsiew" max="100000" maxlength="6"/>
						</div>

						<div class="col-sm-4">
						<label>Metal</label>
						<input class="form-control" type="number" name="metal" max="100000" maxlength="6"/>
						</div>
					</div>
						<br /><br />

					<div class="row">
						<div class="col-sm-4">
						<label >Wielkość Parti Na Początku</label>
						<input class="form-control" type="number" name="wielkosc_parti_poczatek"  maxlength="7" />
						</div>

						<div class="col-sm-4">
						<label >Wielkość Parti Na Końcu</label>
						<input class="form-control" type="number" name="wielkosc_parti_koniec"  maxlength="7" />
						</div>

						<div class="col-sm-4">
						<label>Liczba i Waga Netto Worków</label>
						<input class="form-control" type="text" name="worki" maxlength="60" />
						</div>

					</div>
					<br /><br />

					<div class="row">
						<div class="col-sm-4">
						<label >Wydajność</label>
						<input class="form-control" type="number" name="wydajnosc"  maxlength="7" />
						</div>

						<div class="col-sm-4">
						<label>Obsada</label>
						<input class="form-control" type="text" name="obsada" maxlength="200"/>
						</div>

						<div class="col-sm-4">
						<label>Uwagi</label>
						<textarea class="form-control" name="uwagi" rows="1" cols="30" maxlength="160"></textarea>
						</div>
					</div>
						<br / ><br / >

					<div class="row">
						<div class="col-sm-4">
						<label>Dokument</label>
						<input class="form-control" type="file" name="dokument"/>
						</div>

						 <!-- MAX_FILE_SIZE musi poprzedzać input pliku docelowego -->
    					<input type="hidden" name="MAX_FILE_SIZE" value="17000000" />


						<div class="col-sm-4">
						<label>Zdjęcie</label>
						<input class="form-control" type="file" name="zdjecie" >
						</div>

						<div class="col-sm-4">
						<label>Opis zdjecia</label>
						<input class="form-control" type="text" name="opis_zdjecia" maxlength="160">
						</div>
					</div>
						<hr></hr>

						<span class="glyphicon glyphicon-save"></span>&nbsp;<input  type="submit" value="Zapisz" name="info_dodatkowe">
				</fieldset>
			</form>
			</div>
		</div>
	</div>
</div>

			<br / ><br / >

<?php
			if (isset($_POST['zapisz']) || isset($_POST['modyfikuj']) || isset($_POST['usun'])) {

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
				$nr_raportu=filtruj($_POST['nr_raportu']);
				$asortyment = filtruj($_POST['asortyment']);
				$asortyment_czysty=substr($asortyment,0,-7);//Usuwamy tekst '_Steryl' z końca nazwy asortymentu, który jest w bazie danych
				$odbiorca = filtruj($_POST['odbiorca']);
				$klient = filtruj($_POST['klient']);
				$data = filtruj($_POST['data']);
				$godzina = filtruj($_POST['godzina']);
				$predkosc_zasobnika = filtruj($_POST['predkosc_zasobnika']);
				$predkosc_sluzy1 = filtruj($_POST['predkosc_sluzy1']);
				$predkosc_sluzy2 = filtruj($_POST['predkosc_sluzy2']);
				$predkosc_sterylizatora = filtruj($_POST['predkosc_sterylizatora']);
				$temperatura_sterylizacji = filtruj($_POST['temperatura_sterylizacji']);
				$cisnienie_sterylizacji = filtruj($_POST['cisnienie_sterylizacji']);
				$predkosc_suszarki1 = filtruj($_POST['predkosc_suszarki1']);
				$nadmuch_suszarki1 = filtruj($_POST['nadmuch_suszarki1']);
				$temperatura_suszarki1 = filtruj($_POST['temperatura_suszarki1']);
				$predkosc_suszarki2 = filtruj($_POST['predkosc_suszarki2']);
				$nadmuch_suszarki2 = filtruj($_POST['nadmuch_suszarki2']);
				$temperatura_suszarki2 = filtruj($_POST['temperatura_suszarki2']);
				$predkosc_chlodziarki = filtruj($_POST['predkosc_chlodziarki']);
				$nadmuch_chlodziarki = filtruj($_POST['nadmuch_chlodziarki']);
				$wilgotnosc_poczatkowa = filtruj($_POST['wilgotnosc_poczatkowa']);
				$wilgotnosc_koncowa = filtruj($_POST['wilgotnosc_koncowa']);
				$odpowiedzialny = filtruj($_POST['osoba_odpowiedzialna']);

				$wszystkie_dane = array($nr_raportu,$asortyment, $odbiorca, $data, $godzina, $predkosc_zasobnika, $predkosc_sluzy1, $predkosc_sluzy2, $predkosc_sterylizatora, $temperatura_sterylizacji, $cisnienie_sterylizacji, $predkosc_suszarki1, $nadmuch_suszarki1, $temperatura_suszarki1, $predkosc_suszarki2, $nadmuch_suszarki2, $temperatura_suszarki2, $predkosc_chlodziarki, $nadmuch_chlodziarki, $wilgotnosc_poczatkowa, $wilgotnosc_koncowa, $odpowiedzialny);
				$dane_tekstowe = array($nr_raportu,$asortyment, $odbiorca, $odpowiedzialny);
				$dane_numeryczne = array($predkosc_zasobnika, $predkosc_sluzy1, $predkosc_sluzy2, $predkosc_sterylizatora, $temperatura_sterylizacji, $cisnienie_sterylizacji, $predkosc_suszarki1, $nadmuch_suszarki1, $temperatura_suszarki1, $predkosc_suszarki2, $nadmuch_suszarki2, $temperatura_suszarki2, $predkosc_chlodziarki, $nadmuch_chlodziarki, $wilgotnosc_poczatkowa, $wilgotnosc_koncowa);

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
						if (!is_string($element) || strlen($element) > 35) {
							echo "<div class='alert alert-warning'><strong>Uwaga!</strong>&nbsp Podałeś zły format danych. Tekst jest za długi - max 35 znaków.</div>";
							return FALSE;
							break;
						}
					}
					return TRUE;
				}

				function sprawdz_dane_numeryczne($tablica) {
					foreach ($tablica as $element) {
						if (!is_numeric($element) || strlen($element) > 7) {
							echo "<div class='alert alert-warning'><strong>Uwaga!</strong>&nbsp Podałeś zły format danych. W miejscu gdzie powinny być liczby wpisałeś tekst lub dane liczbowe są za długie - max 5 znaków.</div>";
							return FALSE;
							break;
						}
					}
					return TRUE;
				}

				if (sprawdz_istnienie_danych($wszystkie_dane) && sprawdz_dane_numeryczne($dane_numeryczne) && sprawdz_dane_tekstowe($dane_tekstowe))
				{

					/* Łączymy się z serwerem */
					require_once ('polaczenie_z_baza.php');

					if (mysqli_connect_errno()) {

						printf("<div class='alert alert-danger'><strong>Uwaga!</strong>&nbspBrak połączenia z serwerem MySQL. Kod błędu: %s\n</div>", mysqli_connect_error());
					} else {

						//usuwamy specjalne znaki takie jak '," aby nie możnabyło wpisać ich z formularza do zapytania SQL
						$nr_raportu = $mysqli -> real_escape_string($nr_raportu);
						$asortyment = $mysqli -> real_escape_string($asortyment);
						$odbiorca = $mysqli -> real_escape_string($odbiorca);
						$klient = $mysqli -> real_escape_string($klient);
						$data = $mysqli -> real_escape_string($data);
						$godzina = $mysqli -> real_escape_string($godzina);
						$predkosc_zasobnika = $mysqli -> real_escape_string($predkosc_zasobnika);
						$predkosc_sluzy1 = $mysqli -> real_escape_string($predkosc_sluzy1);
						$predkosc_sluzy2 = $mysqli -> real_escape_string($predkosc_sluzy2);
						$predkosc_sterylizatora = $mysqli -> real_escape_string($predkosc_sterylizatora);
						$temperatura_sterylizacji = $mysqli -> real_escape_string($temperatura_sterylizacji);
						$cisnienie_sterylizacji = $mysqli -> real_escape_string($cisnienie_sterylizacji);
						$predkosc_suszarki1 = $mysqli -> real_escape_string($predkosc_suszarki1);
						$nadmuch_suszarki1 = $mysqli -> real_escape_string($nadmuch_suszarki1);
						$temperatura_suszarki1 = $mysqli -> real_escape_string($temperatura_suszarki1);
						$predkosc_suszarki2 = $mysqli -> real_escape_string($predkosc_suszarki2);
						$nadmuch_suszarki2 = $mysqli -> real_escape_string($nadmuch_suszarki2);
						$temperatura_suszarki2 = $mysqli -> real_escape_string($temperatura_suszarki2);
						$predkosc_chlodziarki = $mysqli -> real_escape_string($predkosc_chlodziarki);
						$nadmuch_chlodziarki = $mysqli -> real_escape_string($nadmuch_chlodziarki);
						$wilgotnosc_poczatkowa = $mysqli -> real_escape_string($wilgotnosc_poczatkowa);
						$wilgotnosc_koncowa = $mysqli -> real_escape_string($wilgotnosc_koncowa);
						$sito = $mysqli-> real_escape_string($sito);
						$partia_poczatek = $mysqli-> real_escape_string($partia_poczatek);
						$partia_koniec = $mysqli-> real_escape_string($partia_koniec);
						$worki = $mysqli-> real_escape_string($worki);
						$uwagi = $mysqli-> real_escape_string($uwagi);
						$odpowiedzialny = $mysqli -> real_escape_string($odpowiedzialny);

						$element_zapytania="";
						$elemet_parowania="";


						if ($odbiorca=='Potrzeby własne') {
							$element_zapytania="Odbiorca";
							$elemet_parowania="$odbiorca";
						}

						if ($odbiorca=='Klient') {
							$element_zapytania="Klient";
							$elemet_parowania="$klient";
						}

						if (isset($_POST['zapisz']))
						 {
							if ($stmt = $mysqli -> prepare("INSERT INTO `" . $asortyment . "` (NrRaportu,Odbiorca,Klient,Data,Godzina,PredkoscZasobnika,PredkoscSluzy1,PredkoscSluzy2,PredkoscSterylizatora,TemperaturaSterylizacji,CisnienieSterylizacji,PredkoscSuszarki1,NadmuchSuszarki1,TemperaturaSuszarki1,PredkoscSuszarki2,NadmuchSuszarki2,TemperaturaSuszarki2,PredkoscChlodziarki,NadmuchChlodziarki,WilgotnoscPoczatkowa,WilgotnoscKoncowa,WykonawcaPomiaru) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"))
						 	{

							/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
							$stmt -> bind_param("ssssssssssssssssssssss",$nr_raportu, $odbiorca, $klient, $data, $godzina, $predkosc_zasobnika, $predkosc_sluzy1, $predkosc_sluzy2, $predkosc_sterylizatora, $temperatura_sterylizacji, $cisnienie_sterylizacji, $predkosc_suszarki1, $nadmuch_suszarki1, $temperatura_suszarki1, $predkosc_suszarki2, $nadmuch_suszarki2, $temperatura_suszarki2, $predkosc_chlodziarki, $nadmuch_chlodziarki, $wilgotnosc_poczatkowa, $wilgotnosc_koncowa, $odpowiedzialny);
							$stmt -> execute();
							}
								else {
									echo '<div class="alert alert-danger"><strong>Info!</strong>&nbsp Błąd podczas zapisu do bazy danych.</div>';
									}
						}


						if (isset($_POST['modyfikuj']))
						 {
							if ($stmt = $mysqli -> prepare("UPDATE `" . $asortyment . "` SET Odbiorca=?,Klient=?,PredkoscZasobnika=?,PredkoscSluzy1=?,PredkoscSluzy2=?,PredkoscSterylizatora=?,TemperaturaSterylizacji=?,CisnienieSterylizacji=?,PredkoscSuszarki1=?,NadmuchSuszarki1=?,TemperaturaSuszarki1=?,PredkoscSuszarki2=?,NadmuchSuszarki2=?,TemperaturaSuszarki2=?,PredkoscChlodziarki=?,NadmuchChlodziarki=?,WilgotnoscPoczatkowa=?,WilgotnoscKoncowa=?,WykonawcaPomiaru=? WHERE NrRaportu=? AND Data=? AND Godzina=? LIMIT 1"))
						 	{

							/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
							$stmt -> bind_param("ssssssssssssssssssssss",$odbiorca, $klient, $predkosc_zasobnika, $predkosc_sluzy1, $predkosc_sluzy2, $predkosc_sterylizatora, $temperatura_sterylizacji, $cisnienie_sterylizacji, $predkosc_suszarki1, $nadmuch_suszarki1, $temperatura_suszarki1, $predkosc_suszarki2, $nadmuch_suszarki2, $temperatura_suszarki2, $predkosc_chlodziarki, $nadmuch_chlodziarki, $wilgotnosc_poczatkowa, $wilgotnosc_koncowa, $odpowiedzialny, $nr_raportu, $data, $godzina);
							$stmt -> execute();
							}
								else {
									echo '<div class="alert alert-danger"><strong>Info!</strong>&nbsp Błąd podczas zapisu do bazy danych.</div>';
									}
						}


						if (isset($_POST['usun']))
							{
								if ($stmt = $mysqli -> prepare("DELETE FROM `" . $asortyment . "` WHERE NrRaportu=? AND Data=? AND Godzina=?"))
						 		{

								/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
								$stmt -> bind_param("sss", $nr_raportu, $data, $godzina);
								$stmt -> execute();
						 		}
								else {
									echo '<div class="alert alert-danger"><strong>Info!</strong>&nbsp Błąd podczas usuwania informacji z bazy danych.</div>';
									}
							}


							if ($stmt -> affected_rows == 0 || $stmt -> affected_rows < 0 ||$stmt->affected_rows==NULL) {
								echo "<div class='alert alert-warning'><strong>Ostrzeżenie!</strong>&nbsp Nie dokonano zapisu. Możliwy błąd zapytania.</div>";
							}

							if ($stmt -> affected_rows > 0) {

								$stmt=$mysqli->prepare("SELECT Data,Godzina,PredkoscZasobnika,PredkoscSluzy1,PredkoscSluzy2,PredkoscSterylizatora,TemperaturaSterylizacji,CisnienieSterylizacji,PredkoscSuszarki1,NadmuchSuszarki1,TemperaturaSuszarki1,PredkoscSuszarki2,NadmuchSuszarki2,TemperaturaSuszarki2,PredkoscChlodziarki,NadmuchChlodziarki,WilgotnoscPoczatkowa,WilgotnoscKoncowa,WykonawcaPomiaru FROM `" . $asortyment . "` WHERE NrRaportu=? AND ".$element_zapytania."=? ORDER BY Data, Godzina ASC");
								$stmt->bind_param(ss,$nr_raportu,$elemet_parowania);

								/*if ($odbiorca=='Potrzeby własne') {
									$stmt=$mysqli->prepare("SELECT Data,Godzina,PredkoscZasobnika,PredkoscSluzy1,PredkoscSluzy2,PredkoscSterylizatora,TemperaturaSterylizacji,CisnienieSterylizacji,PredkoscSuszarki1,NadmuchSuszarki1,TemperaturaSuszarki1,PredkoscSuszarki2,NadmuchSuszarki2,TemperaturaSuszarki2,PredkoscChlodziarki,NadmuchChlodziarki,WilgotnoscPoczatkowa,WilgotnoscKoncowa,WykonawcaPomiaru FROM " . $asortyment . " WHERE NrRaportu=? AND Odbiorca=? ORDER BY Data, Godzina ASC");
									$stmt->bind_param(ss,$nr_raportu,$odbiorca);
								}

								if ($odbiorca=='Klient') {
									$stmt=$mysqli->prepare("SELECT Data,Godzina,PredkoscZasobnika,PredkoscSluzy1,PredkoscSluzy2,PredkoscSterylizatora,TemperaturaSterylizacji,CisnienieSterylizacji,PredkoscSuszarki1,NadmuchSuszarki1,TemperaturaSuszarki1,PredkoscSuszarki2,NadmuchSuszarki2,TemperaturaSuszarki2,PredkoscChlodziarki,NadmuchChlodziarki,WilgotnoscPoczatkowa,WilgotnoscKoncowa,WykonawcaPomiaru FROM " . $asortyment . " WHERE NrRaportu=? AND Klient=? ORDER BY Data, Godzina ASC");
									$stmt->bind_param(ss,$nr_raportu,$klient);
								}*/

								$stmt->execute();

								 /* Powiązujemy dane z zapytania do zmiennych, których uzyjemy do wyswietlenia danych */
								$stmt->bind_result($Data,$Godzina,$Predkosc_Zasobnika,$Predkosc_Sluzy1,$Predkosc_Sluzy2,$Predkosc_Sterylizatora,$Temperatura_Sterylizacji,$Cisnienie_Sterylizacji,$Predkosc_Suszarki1,$Nadmuch_Suszarki1,$Temperatura_Suszarki1,$Predkosc_Suszarki2,$Nadmuch_Suszarki2 ,$Temperatura_Suszarki2,$Predkosc_Chlodziarki,$Nadmuch_Chlodziarki,$Wilgotnosc_Poczatkowa,$Wilgotnosc_Koncowa,$Odpowiedzialny);

								 /* Bufurejemy wynik */
    							$stmt->store_result();

								/*Sprawdamy czy są jakieś dane jesli tak to wyswietlamy jesli nie to zgłaszamy ich brak*/
   								if ($stmt->num_rows > 0) {
   								echo '<div class="alert alert-success alert-dismissable fade in">
								<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
								<span class="glyphicon glyphicon-thumbs-up"></span>&nbsp;<strong>Sukces!</strong>&nbsp Dane zostały zapisane. Poniżej jest twój raport: </div><br / >';
								$info_o_odbiorcy="";

								if ($odbiorca=='Potrzeby własne') {
									$info_o_odbiorcy=$odbiorca;
								}
								if ($odbiorca=='Klient') {
									$info_o_odbiorcy=$klient;
								}

								printf("<b>Nr Raportu:</b>&nbsp %s &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp <b>Asortyment:</b>&nbsp %s &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp <b>Odbiorca:</b>&nbsp %s <br / ><br / >",$nr_raportu, $asortyment_czysty, $info_o_odbiorcy);
								/*
								if ($odbiorca=='Potrzeby własne') {
								printf("<b>Nr Raportu:</b>&nbsp %s &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp <b>Asortyment:</b>&nbsp %s &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp <b>Odbiorca:</b>&nbsp %s <br / ><br / >",$nr_raportu, $asortyment, $odbiorca);
								}

								if ($odbiorca=='Klient') {
								printf("<b>Nr Raportu:</b>&nbsp %s &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp <b>Asortyment:</b>&nbsp %s &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp <b>Odbiorca:</b>&nbsp %s <br / ><br / >",$nr_raportu, $asortyment, $klient);
								}
								*/

								echo '<div id="tabela_wielkosci">Data<br / >Godzina<br / >Pręd Zasob<br / >Pręd śluzy1<br / >Pręd Śluzy2<br / >Pręd Steryl<br / >Temp Steryl<br / >Ciś Steryl<br / >Pręd Susz1<br / >Nad Susz1<br / >Temp Susz1<br / >Pręd Susz2<br / >Nad Susz2<br / >Temp Susz2<br / >Pręd Chlod<br / >Nad Chlod<br / >Wilgo. Pocz.<br / >Wilgo Kon.<br / >Osoba<br / ></div>';

								while ($stmt->fetch()) {
									printf("<div id='tabela_wynikow'>%s <br / >%s <br / >%s Hz<br / >%s Hz<br / >%s Hz<br / >%s Hz<br / > %s &deg;C<br / >%s hPa<br / > %s Hz<br / > %s Hz<br / > %s &deg;C<br / >%s Hz<br / >%s Hz<br / > %s &deg;C<br / >%s Hz<br / > %s Hz<br / >%s %%<br / >%s %%<br / >%s</div>",$Data, $Godzina = substr($Godzina, 0, 5), $Predkosc_Zasobnika, $Predkosc_Sluzy1, $Predkosc_Sluzy2, $Predkosc_Sterylizatora, $Temperatura_Sterylizacji, $Cisnienie_Sterylizacji, $Predkosc_Suszarki1, $Nadmuch_Suszarki1, $Temperatura_Suszarki1, $Predkosc_Suszarki2, $Nadmuch_Suszarki2, $Temperatura_Suszarki2, $Predkosc_Chlodziarki, $Nadmuch_Chlodziarki, $Wilgotnosc_Poczatkowa, $Wilgotnosc_Koncowa, $Odpowiedzialny);
								}

								//Wyciągmy średnią wartość wilgotności początkowej
					if ($stmt = $mysqli -> prepare("SELECT AVG(WilgotnoscPoczatkowa) FROM `" . $asortyment . "` WHERE NrRaportu=? "))
						{
					/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
					$stmt -> bind_param("s",$nr_raportu);

					$stmt -> execute();
					$stmt -> bind_result($Srednia_Wilg_Poczatkowa);
					$stmt -> store_result();
					$stmt->data_seek(0);
						if ($stmt -> fetch()){
							printf("<br /><br /> <b>Średnia wilgotność początkowa:</b>&nbsp %s %%<br / >", round($Srednia_Wilg_Poczatkowa,$precision=2));
							}
						}

					//Wyciągmy średnią wartość wilgotności końcowej
					if ($stmt = $mysqli -> prepare("SELECT AVG(WilgotnoscKoncowa) FROM `" . $asortyment . "` WHERE NrRaportu=? "))
						{
					/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
					$stmt -> bind_param("s",$nr_raportu);

					$stmt -> execute();
					$stmt -> bind_result($Srednia_Wilg_Koncowa);
					$stmt -> store_result();
					$stmt->data_seek(0);
						if ($stmt -> fetch()){
							printf("<b>Średnia wilgotność końcowa:</b>&nbsp %s %%<br / ><br / >", round($Srednia_Wilg_Koncowa,$precision=2));
							}
						}

					//Wyciągmy info o odsiewie
					if ($stmt = $mysqli -> prepare("SELECT Odsiew FROM `" . $asortyment . "` WHERE NrRaportu=? "))
						{
					/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
					$stmt -> bind_param("s",$nr_raportu);

					$stmt -> execute();
					$stmt -> bind_result($Odsiew);
					$stmt -> store_result();
					$stmt->data_seek(0);
						if ($stmt -> fetch()){
							printf("<b>Odsiew:</b>&nbsp %s kg<br />", $Odsiew);
							}
						}

					//Wyciągmy info o metalu
					if ($stmt = $mysqli -> prepare("SELECT Metal FROM `" . $asortyment . "` WHERE NrRaportu=? "))
						{
					/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
					$stmt -> bind_param("s",$nr_raportu);

					$stmt -> execute();
					$stmt -> bind_result($Metal);
					$stmt -> store_result();
					$stmt->data_seek(0);
						if ($stmt -> fetch()){
							printf("<b>Metal:</b>&nbsp %s kg<br /><br / >", $Metal);
							}
						}

					//Wyciągmy info o parti początkowej
					if ($stmt = $mysqli -> prepare("SELECT PartiaPoczatek FROM `" . $asortyment . "` WHERE NrRaportu=? "))
						{
					/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
					$stmt -> bind_param("s",$nr_raportu);

					$stmt -> execute();
					$stmt -> bind_result($Partia_poczatek);
					$stmt -> store_result();
					$stmt->data_seek(0);
						if ($stmt -> fetch()){
							printf("<b>Wielkość parti na początku:</b>&nbsp %s kg<br />", $Partia_poczatek);
							}
						}

					//Wyciągmy info o parti końcowej
					if ($stmt = $mysqli -> prepare("SELECT PartiaKoniec FROM `" . $asortyment . "` WHERE NrRaportu=? "))
						{
					/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
					$stmt -> bind_param("s",$nr_raportu);

					$stmt -> execute();
					$stmt -> bind_result($Partia_koniec);
					$stmt -> store_result();
					$stmt->data_seek(0);
						if ($stmt -> fetch()){
							printf("<b>Wielkość parti na końcu:</b>&nbsp %s kg<br / >", $Partia_koniec);
							}
						}

					//Wyciągmy info o liczbie i masie worków
					if ($stmt = $mysqli -> prepare("SELECT LiczbaMasaWorkow FROM `" . $asortyment . "` WHERE NrRaportu=? "))
						{
					/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
					$stmt -> bind_param("s",$nr_raportu);

					$stmt -> execute();
					$stmt -> bind_result($Worki);
					$stmt -> store_result();
					$stmt->data_seek(0);
						if ($stmt -> fetch()){
							printf("<b>Liczba i masa netto worków:</b>&nbsp %s <br / ><br / >", $Worki);
							}
						}

					//Obliczamy straty w towarze po procesie sterylizacji w kg
					if ($stmt = $mysqli -> prepare("SELECT PartiaPoczatek,PartiaKoniec FROM `" . $asortyment . "` WHERE NrRaportu=? "))
						{
					/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
					$stmt -> bind_param("s",$nr_raportu);

					$stmt -> execute();
					$stmt -> bind_result($Partia_poczatek,$Partia_koniec);
					$stmt -> store_result();
					$stmt->data_seek(0);
					$roznica="";
						if ($stmt -> fetch()){
							printf("<b>Strata towaru w kg:</b>&nbsp %s kg <br / >", $roznica=$Partia_poczatek-$Partia_koniec);
							}
						}

					//Obliczamy straty w towarze po procesie sterylizacji w %
					if ($stmt = $mysqli -> prepare("SELECT PartiaPoczatek,PartiaKoniec FROM `" . $asortyment . "` WHERE NrRaportu=? "))
						{
					/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
					$stmt -> bind_param("s",$nr_raportu);

					$stmt -> execute();
					$stmt -> bind_result($Partia_poczatek,$Partia_koniec);
					$stmt -> store_result();
					$stmt->data_seek(0);
					$roznica="";
						if ($stmt -> fetch()){
							printf("<b>Strata towaru w %%:</b>&nbsp %s %%<br / ><br / >", round($roznica=(($Partia_poczatek-$Partia_koniec)*100)/$Partia_poczatek,$precision=2) );
							}
						}

					//Wyciągmy info o sicie
					if ($stmt = $mysqli -> prepare("SELECT Sito FROM `" . $asortyment . "` WHERE NrRaportu=? "))
						{
					/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
					$stmt -> bind_param("s",$nr_raportu);

					$stmt -> execute();
					$stmt -> bind_result($Sito);
					$stmt -> store_result();
					$stmt->data_seek(0);
						if ($stmt -> fetch()){
							printf("<b>Sito:</b>&nbsp %s <br / ><br / >", $Sito);
							}
						}

					//Wyciągmy info o uwagach
					if ($stmt = $mysqli -> prepare("SELECT Uwagi FROM `" . $asortyment . "` WHERE NrRaportu=? "))
						{
					/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
					$stmt -> bind_param("s",$nr_raportu);

					$stmt -> execute();
					$stmt -> bind_result($Uwagi);
					$stmt -> store_result();
					$stmt->data_seek(0);
						if ($stmt -> fetch()){
							printf("<b>Uwagi:</b>&nbsp %s <br / ><br / >", $Uwagi);
							}
						}

					//Wyciągamy link do pliku
					if ($stmt = $mysqli -> prepare("SELECT Dokument FROM `" . $asortyment . "` WHERE NrRaportu=? "))
						{
					/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
					$stmt -> bind_param("s",$nr_raportu);

					$stmt -> execute();
					$stmt -> bind_result($Dokument);
					$stmt -> store_result();
					$stmt->data_seek(0);
						if ($stmt -> fetch()){
							if (empty($Dokument)) {echo "<b>Dokument:</b> Brak dokumentu.<br / ><br / >";}
							else {
							printf('<b>Dokument:</b>&nbsp<a href="dokumenty/raporty_sterylizacji/%s" target="_blank">%s</a><br / ><br / >',$Dokument,$Dokument);

							}
							}
						}

						//Wyciągamy zdjęcie
					if ($stmt = $mysqli -> prepare("SELECT Zdjecia,OpisZdjecia FROM `" . $asortyment . "` WHERE NrRaportu=? "))
						{
					/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
					$stmt -> bind_param("s",$nr_raportu);

					$stmt -> execute();
					$stmt -> bind_result($Zdjecie,$Opis_zdjecia);
					$stmt -> store_result();
					$stmt->data_seek(0);
						if ($stmt -> fetch()){
							if (empty($Zdjecie)) {echo "<b>Zdjęcia:</b> Brak zdjęć.<br / ><br / >";}
							else {
							printf('<b>Zdjęcia:</b> <br / ><br / ><a href="grafika/zdjecia_raporty_sterylizacji/%s" data-lightbox="zdjecie z raportu" data-title="Zdjecie z raportu sterylizacji"><img id="zdjecie_raportu" src="grafika/zdjecia_raporty_sterylizacji/%s"> </a><br / ><br / >',$Zdjecie,$Zdjecie);
							printf('<b>Opis:</b> %s<br / ><br / >',$Opis_zdjecia);
							}
							}
						}

    							$stmt->close();
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





			//Obróbka fromularza "Informcje dodatkowe"
			if (isset($_POST['info_dodatkowe']))
			{
				/*ob_end_clean();
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);*/

				function filtruj($zmienna) {
					$data = trim($zmienna);
					//usuwa spacje, tagi
					$data = stripslashes($zmienna);
					//usuwa slashe
					$data = htmlspecialchars($zmienna);
					//zamienia tagi html na czytelne znaki aby w formularzu nie wpisać szkodliwego kodu
					return $zmienna;
				}

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
						if (!is_string($element) || strlen($element) > 25) {
							echo "<div class='alert alert-warning'><strong>Uwaga!</strong>&nbsp Podałeś zły format danych. Tekst jest za długi - max 15 znaków.</div>";
							return FALSE;
							break;
						}
					}
					return TRUE;
				}

				function sprawdz_dane_numeryczne($tablica) {
					foreach ($tablica as $element) {
						if (!is_numeric($element) || strlen($element) > 7) {
							echo "<div class='alert alert-warning'><strong>Uwaga!</strong>&nbsp Podałeś zły format danych. W miejscu gdzie powinny być liczby wpisałeś tekst lub dane liczbowe są za długie - max 5 znaków.</div>";
							return FALSE;
							break;
						}
					}
					return TRUE;
				}

				$asortyment = filtruj($_POST['asortyment']);
				$nr_raportu=filtruj($_POST['nr_raportu']);
				$sito = filtruj($_POST['sito']);
				$odsiew = filtruj($_POST['odsiew']);
				$metal = filtruj($_POST['metal']);
				$partia_poczatek = filtruj($_POST['wielkosc_parti_poczatek']);
				$partia_koniec = filtruj($_POST['wielkosc_parti_koniec']);
				$worki = filtruj($_POST['worki']);
				$wydajnosc = filtruj($_POST['wydajnosc']);
				$uwagi = filtruj($_POST['uwagi']);
				$obsada = filtruj($_POST['obsada']);

				$dokument = $_FILES['dokument']['name'];
				$dokument_sciezka = "dokumenty/raporty_sterylizacji/".basename($_FILES['dokument']['name']);

				$zdjecie = $_FILES['zdjecie']['name'];
				$sciezka = "grafika/zdjecia_raporty_sterylizacji/".basename($_FILES['zdjecie']['name']);
				$opis_zdjecia = filtruj($_POST['opis_zdjecia']);
				$typ_zdjecia = $_FILES['zdjecie']['type'];



				if (!$asortyment==null && !$nr_raportu==null)
				{

					/* Łączymy się z serwerem */
					require_once ('polaczenie_z_baza.php');

					if (mysqli_connect_errno()) {

						printf("<div class='alert alert-danger'><span class='glyphicon glyphicon-thumbs-down'></span>&nbsp;<strong>Uwaga!</strong>&nbspBrak połączenia z serwerem MySQL. Kod błędu: %s\n</div>", mysqli_connect_error());
					}
						else
						{

						//usuwamy specjalne znaki takie jak '," aby nie możnabyło wpisać ich z formularza do zapytania SQL
						$nr_raportu = $mysqli -> real_escape_string($nr_raportu);
						$asortyment = $mysqli -> real_escape_string($asortyment);
						$sito = $mysqli -> real_escape_string($sito);
						$odsiew = $mysqli -> real_escape_string($odsiew);
						$metal = $mysqli -> real_escape_string($metal);
						$partia_poczatek = $mysqli -> real_escape_string($partia_poczatek);
						$partia_koniec = $mysqli -> real_escape_string($partia_koniec);
						$worki = $mysqli -> real_escape_string($worki);
						$wydajnosc = $mysqli -> real_escape_string($wydajnosc);
						$uwagi = $mysqli -> real_escape_string($uwagi);
						$obsada = $mysqli -> real_escape_string($obsada);
						$opis_zdjecia = $mysqli-> real_escape_string($opis_zdjecia);

						if ($sito==null && $odsiew==null && $metal==null && $partia_poczatek==null && $partia_koniec==null && $worki==null && $wydajnosc==null && $obsada==null && $uwagi==null && empty($zdjecie) && empty($dokument))
							 {
							echo "<div class='alert alert-warning'><strong>Ostrzeżenie!</strong>&nbsp Oprócz Asortymentu i Nr Raportu podaj przynajmniej jedną z danych:sito, odsiew, metal, wielkość parti, liczbę worków, wydajność,obsadę, uwagi lub plik do załączenia. Możesz też podać wszytskie dane naraz.</div>";
							 }
						else{
							//Opcje zapytania do bazy danych
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
							$opcja12="";

							 //Opcja1 - Jeśli oprócz asortymentyui nr raportu podano sito
							if (!$sito==null)
							 {
							//Zmieniamy istniejące dane
							if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET Sito=? WHERE NrRaportu=? LIMIT 1"))
							 {

							/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
							$stmt -> bind_param("ss",$sito,$nr_raportu );
							$stmt -> execute();
							$opcja1=true;
							 }
							}

							 //Opcja2 - Jeśli oprócz asortymentu i nr raportu podano uwagi
							if (!$uwagi==null)
							 {
							 	if (strlen($uwagi) > 100) {
							 		echo "<div class='alert alert-warning'><strong>Ostrzeżenie!</strong>&nbsp Pole 'Uwagi' przekroczyło maksymalną wartość znaków. Dopuszczalna ilość znaków = <b>100</b>. </div>";
								}
								else {

									//Zmieniamy istniejące dane
									if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET Uwagi=? WHERE NrRaportu=? LIMIT 1"))
							 		{
									/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
									$stmt -> bind_param("ss",$uwagi,$nr_raportu );
									$stmt -> execute();
									$opcja2=true;
									 }
									}
							}

							 //Opcja3 - Jeśli oprócz asortymentu i nr raportu podano partie początkową
							if (!$partia_poczatek==null)
							 {
							//Zmieniamy istniejące dane
							if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET PartiaPoczatek=? WHERE NrRaportu=? LIMIT 1"))
							 {

							/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
							$stmt -> bind_param("ss",$partia_poczatek,$nr_raportu );
							$stmt -> execute();
							$opcja3=true;
							 }
							}

							 //Opcja4 - Jeśli oprócz asortymentu i nr raportu podano partię końcową
							if (!$partia_koniec==null)
							 {
							//Zmieniamy istniejące dane
							if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET PartiaKoniec=? WHERE NrRaportu=? LIMIT 1"))
							 {

							/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
							$stmt -> bind_param("ss",$partia_koniec,$nr_raportu );
							$stmt -> execute();
							$opcja4=true;
							 }
							}

							 //Opcja5 - Jeśli oprócz asortymentu i nr raportu podano liczbę worków
							if (!$worki==null)
							 {
							//Zmieniamy istniejące dane
							if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET LiczbaMasaWorkow=? WHERE NrRaportu=? LIMIT 1"))
							 {

							/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
							$stmt -> bind_param("ss",$worki,$nr_raportu );
							$stmt -> execute();
							$opcja5=true;
							 }
							}

							  //Opcja6 -Jeśli oprócz asortymentu i nr raportu podano zdjęcie
							if ( !empty($zdjecie) )
							 {
							 	if ($typ_zdjecia=="image/jpg" || $typ_zdjecia=="image/jpeg" || $typ_zdjecia=="image/pjpeg" || $typ_zdjecia=="image/png" || $typ_zdjecia=="image/x-png"  || $typ_zdjecia=="image/gif")
								 {

							//Zmieniamy istniejące dane
							if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET Zdjecia=?,OpisZdjecia=? WHERE NrRaportu=? LIMIT 1"))
							 {

							/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
							$stmt -> bind_param("sss",$zdjecie,$opis_zdjecia,$nr_raportu );
							$stmt -> execute();
							if (move_uploaded_file($_FILES['zdjecie']['tmp_name'], $sciezka)) {
								$opcja6=true;
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


							 //Opcja7 -Jeśli oprócz asortymentu i nr raportu podano odsiew.
							if (!$odsiew==null)
							 {
							//Zmieniamy istniejące dane
							if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET Odsiew=? WHERE NrRaportu=? LIMIT 1"))
							 {

							/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
							$stmt -> bind_param("ss",$odsiew,$nr_raportu );
							$stmt -> execute();
							$opcja7=true;
							 }

							}

							  //Opcja8 -Jeśli oprócz asortymentu i nr raportu podano metal.
							if (!$metal==null)
							 {
							//Zmieniamy istniejące dane
							if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET Metal=? WHERE NrRaportu=? LIMIT 1"))
							 {

							/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
							$stmt -> bind_param("ss",$metal,$nr_raportu );
							$stmt -> execute();
							$opcja8=true;
							 }

							}

							 //Opcja9 -Jeśli oprócz asortymentu i nr raportu podano tylko dokument
							if ( !empty($dokument) )
							 {
							 	if (1==1)
								 {

							//Zmieniamy istniejące dane
							if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET Dokument=? WHERE NrRaportu=? LIMIT 1"))
							 {

							/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
							$stmt -> bind_param("ss",$dokument,$nr_raportu );
							$stmt -> execute();
							if (move_uploaded_file($_FILES['dokument']['tmp_name'], $dokument_sciezka)) {
								$opcja9=true;
							}
								else {
									echo "<div class='alert alert-warning'><strong>Ostrzeżenie!</strong>&nbsp Wystąpił błąd. Nie dodano dokumentu.</div>";
									break;
										}
							 }

							 }
								 else {
									echo "<div class='alert alert-warning'><strong>Ostrzeżenie!</strong>&nbsp Wybrano zły format dokumentu. Dopuszczalne formaty to: pdf, doc, txt.</div>";
									break;
										}

							}

							 //Opcja10 - Jeśli oprócz asortymentyui nr raportu podano tylko wydajność
							if ( !$wydajnosc==null)
							 {
							//Zmieniamy istniejące dane
							if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET Wydajnosc=? WHERE NrRaportu=? LIMIT 1"))
							 {

							/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
							$stmt -> bind_param("ss",$wydajnosc,$nr_raportu );
							$stmt -> execute();
							$opcja10=true;
							 }
							}

							 //Opcja11 -J eśli oprócz asortymentu, nr raportu, zdjęcia i pliku podano wszystkie dane wybierz te zapytanie
							if (!$sito==null && !$odsiew==null && !$metal==null && !$partia_poczatek==null && !$partia_koniec==null && !$worki==null && !$wydajnosc==null && !$uwagi==null && empty($zdjecie) && empty($dokument))
							 {
							//Zmieniamy istniejące dane
							if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET Sito=?,Odsiew=?,Metal=?,PartiaPoczatek=?,PartiaKoniec=?,LiczbaMasaWorkow=?,Wydajnosc=?,Uwagi=? WHERE NrRaportu=? LIMIT 1"))
							 {

							/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
							$stmt -> bind_param("sssssssss",$sito,$odsiew,$metal,$partia_poczatek,$partia_koniec,$worki,$wydajnosc,$uwagi,$nr_raportu );
							$stmt -> execute();
							$opcja11=true;
							 }

							}

							  //Opcja1 - Jeśli oprócz asortymentyui nr raportu podano sito
							if (!$obsada==null)
							 {
							//Zmieniamy istniejące dane
							if ($stmt = $mysqli -> prepare("UPDATE `".$asortyment."` SET Obsada=? WHERE NrRaportu=? LIMIT 1"))
							 {

							/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
							$stmt -> bind_param("ss",$obsada,$nr_raportu );
							$stmt -> execute();
							$opcja12=true;
							 }
							}


							if ($opcja1||$opcja2||$opcja3||$opcja4||$opcja5||$opcja6||$opcja7||$opcja8||$opcja9||$opcja10||$opcja11||$opcja12)
							{
								if ($stmt -> affected_rows == 0 || $stmt -> affected_rows < 0 ||$stmt->affected_rows==NULL)
								{
								echo "<div class='alert alert-warning'><strong>Ostrzeżenie!</strong>&nbsp Nie dokonano zapisu. Możliwy błąd zapytania.</div>";
								}

								if ($stmt -> affected_rows > 0)
								{
								echo '<div class="alert alert-success alert-dismissable fade in">
								<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
								<span class="glyphicon glyphicon-thumbs-up"></span>&nbsp;<strong>Sukces!</strong>&nbsp Dokonano zapisu danych. </div><br / >';

								}


								$stmt->close();
							}
								else
									{
									echo "<div class='alert alert-warning'><span class='glyphicon glyphicon-alert'></span>&nbsp<strong>Ostrzeżenie!</strong>&nbsp Oprócz Asortymentu i Nr Raportu podaj jedną z danych:sito, odsiew, metal, wielkość parti, liczbę worków,wydajność,obsadę, uwagi lub plik do zamieszczenia.</div>";
							 		}

						}
					}
					$mysqli -> close();

				}
					else {
						echo "<div class='alert alert-warning'><span class='glyphicon glyphicon-alert'></span>&nbsp<strong>Ostrzeżenie!</strong>&nbsp Podaj Asortyment, Nr Raportu oraz przynajmniej jedną z danych:sito,odsiew, metal, wielkość parti, liczbę worków, wydajność,obsadę, uwagi lub plik do zamieszczenia.</div>";
						}

			}

?>
			<br / >
			<br / >
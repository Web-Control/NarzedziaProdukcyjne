<h1>Raport z procesu sterylizacji parowej</h1>
<ul class="nav nav-tabs">
	<li>
		<a href="index2.php?raporty_sterylizacja=1">Tworzenie</a>
	</li>
	<li class="active">
		<a href="index2.php?raporty_sterylizacji_odczyt=1">Odczyt</a>
	</li>
	<li>
		<a href="index2.php?raporty_sterylizacji_pobierz=1">Pobór</a>
	</li>
	<li>
		<a href="index2.php?statystyki_sterylizacji=1">Wykresy</a>
	</li>
</ul>
<br / >

<div id="formularz">
		<div class="row" >
			<div class="form-group">
				<form method="post" action="index2.php?raporty_sterylizacji_odczyt=1">
	<fieldset>
		<legend>
		Odczytaj raport
		</legend>

		<div class="row" >
			<div class="col-sm-4">
			<label>Asortyment</label>
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
			<label>Nr Raportu</label> &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp Ostatni<input type="checkbox" name="ostatni_raport" value="Ostatni_raport"/>
			<input class="form-control" type="text" name="nr_raportu" maxlength="25" />
			</div>
		</div>
			<hr></hr>

		<div class="row" >
			<div class="col-sm-4">
			<label>Odbiorca</label><br / >
			Potrzeby własne<input type="radio" name="odbiorca" id="wlasne" value="Potrzeby własne" onclick="pole_klient()" checked>&nbsp
  			Klient<input type="radio" name="odbiorca" id="klient" value="Klient" onclick="pole_klient()">
			Wszyscy<input type="radio" name="odbiorca" id="wszyscy" value="Wszyscy" onclick="pole_klient()"><br / >

			<?php
						//Pobieramy informacje o klientach w istniejących raportach

						$rok=date("Y");//bierzący rok

						/* Łączymy się z serwerem */
						require_once ('polaczenie_z_baza.php');

						if (mysqli_connect_errno()) {

						printf("<div class='alert alert-danger'><strong>Uwaga!</strong>&nbspBrak połączenia z serwerem MySQL. Kod błędu: %s\n</div>", mysqli_connect_error());

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

								echo '<div class="alert alert-info" id="klienci" style="display:none;"><strong>Info!</strong>&nbsp <b>Klienci w raportach:</b><br / >';
								foreach ($Klienci_unikalni as $klient) {

									echo "$klient <br / >";

								}
								echo "</div>";

						}

						?>


			<label>Klient</label>
			<input class="form-control" style="background-color: silver;" type="text" name="klient" id="klient_nazwa" maxlength="25" readonly required>
			</div>

			<br / ><br / >
			<div class="col-sm-4">
			<label >Rok</label>
			<input class="form-control"  type="number" min="1900" max="2099" step="1" value="<?php echo date("Y"); ?>" name="rok" />
			</div>

			<div class="col-sm-4">
			<label >Data</label>
			<input class="form-control" type="date" name="data" />
			</div>
		</div>

			<hr></hr>
			<span class="glyphicon glyphicon-export"></span>&nbsp<input  type="submit" value="Odczytaj raport" name="submit"> <br / ><br / >
			<span class="glyphicon glyphicon-export"></span>&nbsp<input  type="submit" value="Pokaż listę raportów" name="lista_raportow">

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
function filtruj($zmienna) {
	$data = trim($zmienna);
	//usuwa spacje, tagi
	$data = stripslashes($zmienna);
	//usuwa slashe
	$data = htmlspecialchars($zmienna);
	//zamienia tagi html na czytelne znaki aby w formularzu nie wpisać szkodliwego kodu
	return $zmienna;
}
if(isset($_POST['submit']))
{
/*Odbieramy dane z formularza*/
$asortyment = filtruj($_POST['asortyment']);
$asortyment_czysty=substr($asortyment,0,-7);//Usuwamy tekst '_Steryl' z końca nazwy asortymentu, który jest w bazie danych
$nr_raportu = filtruj($_POST['nr_raportu']);
$odbiorca = filtruj($_POST['odbiorca']);
$klient = filtruj($_POST['klient']);
$data = filtruj($_POST['data']);
$ostatni_raport=filtruj($_POST['ostatni_raport']);

/*Sprawdzamy czy formularz został wypełniony*/
	$opcja1="";
	$opcja2="";
	$opcja3="";
	$opcja4="";

	if (isset($nr_raportu) && !$nr_raportu == null && strlen($nr_raportu)>0)
	{ $opcja1=TRUE; }

	if ($odbiorca=="Potrzeby własne" && isset($data) && !$data == null && strlen($data)>0)
	{ $opcja2=TRUE; }

	if ($odbiorca=="Klient" && isset($data) && !$data == null && strlen($data)>0 )
	{ $opcja3=TRUE; }

	if (isset($ostatni_raport) && !$ostatni_raport == null && strlen($ostatni_raport)>0 &&($odbiorca=="Potrzeby własne" || $odbiorca=="Klient"))
	{$opcja4=TRUE;	}

	if ($opcja1 || $opcja2 || $opcja3 || $opcja4) {


		/* Łączymy się z serwerem */
		require_once ('polaczenie_z_baza.php');

		if (mysqli_connect_errno()) {

			printf("<div class='alert alert-danger'><strong>Uwaga!</strong>&nbspBrak połączenia z serwerem MySQL. Kod błędu: %s\n</div>", mysqli_connect_error());

		} else {

			//usuwamy specjalne znaki takie jak '," aby nie możnabyło wpisać ich z formularza do zapytania SQL
			$asortyment = $mysqli -> real_escape_string($asortyment);
			$nr_raportu = $mysqli -> real_escape_string($nr_raportu);
			$data = $mysqli -> real_escape_string($data);
			$odbiorca = $mysqli -> real_escape_string($odbiorca);
			$klient = $mysqli -> real_escape_string($klient);

			$element_zapytania="";
			$elemet_parowania="";

			if ($odbiorca=="Potrzeby własne") {
				$element_zapytania="Odbiorca";
				$elemet_parowania="$odbiorca";
			}
			if ($odbiorca=="Klient") {
				$element_zapytania="Klient";
				$elemet_parowania="$klient";
			}

			/*Tworzymy zapytanie i sprawdzamy czy nie zwraca błedu*/

				$zapytanie="";

				 if ($opcja1) {
			 	 	if ($stmt = $mysqli -> prepare("SELECT NrRaportu,Odbiorca,Klient,Data,Godzina,PredkoscZasobnika,PredkoscSluzy1,PredkoscSluzy2,PredkoscSterylizatora,TemperaturaSterylizacji,CisnienieSterylizacji,PredkoscSuszarki1,NadmuchSuszarki1,TemperaturaSuszarki1,PredkoscSuszarki2,NadmuchSuszarki2,TemperaturaSuszarki2,PredkoscChlodziarki,NadmuchChlodziarki,WilgotnoscPoczatkowa,WilgotnoscKoncowa,WykonawcaPomiaru FROM `" . $asortyment . "` WHERE NrRaportu=? ORDER BY Data, Godzina ASC"))
						{
					/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
					$stmt -> bind_param("s", $nr_raportu);
					$zapytanie=TRUE;
						}
					 }

				if ($opcja2 || $opcja3) {
					if ($stmt = $mysqli -> prepare("SELECT NrRaportu,Odbiorca,Klient,Data,Godzina,PredkoscZasobnika,PredkoscSluzy1,PredkoscSluzy2,PredkoscSterylizatora,TemperaturaSterylizacji,CisnienieSterylizacji,PredkoscSuszarki1,NadmuchSuszarki1,TemperaturaSuszarki1,PredkoscSuszarki2,NadmuchSuszarki2,TemperaturaSuszarki2,PredkoscChlodziarki,NadmuchChlodziarki,WilgotnoscPoczatkowa,WilgotnoscKoncowa,WykonawcaPomiaru FROM `" . $asortyment . "` WHERE ".$element_zapytania."=? AND Data=? ORDER BY Data, Godzina ASC"))
						{
					/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
					$stmt -> bind_param("ss", $elemet_parowania,$data);
					$zapytanie=TRUE;
						}
					}

				if ($opcja4){
					if ($stmt = $mysqli -> prepare("SELECT NrRaportu,Odbiorca,Klient,Data,Godzina,PredkoscZasobnika,PredkoscSluzy1,PredkoscSluzy2,PredkoscSterylizatora,TemperaturaSterylizacji,CisnienieSterylizacji,PredkoscSuszarki1,NadmuchSuszarki1,TemperaturaSuszarki1,PredkoscSuszarki2,NadmuchSuszarki2,TemperaturaSuszarki2,PredkoscChlodziarki,NadmuchChlodziarki,WilgotnoscPoczatkowa,WilgotnoscKoncowa,WykonawcaPomiaru FROM `" . $asortyment . "`  WHERE NrRaportu=(SELECT MAX(NrRaportu) FROM `" . $asortyment . "`  WHERE ".$element_zapytania."=?) AND Data LIKE '%" . $rok . "%' ORDER BY Data, Godzina ASC"))
						{
					/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
					$stmt -> bind_param("s", $elemet_parowania);
					$zapytanie=TRUE;
						}
					}



					if ($zapytanie)
			 {

				$stmt -> execute();

				/* Powiązujemy dane z zapytania do zmiennych, których uzyjemy do wyswietlenia danych */
				$stmt -> bind_result($Nr_Raportu,$Odbiorca,$Klient,$Data, $Godzina, $Predkosc_Zasobnika, $Predkosc_Sluzy1, $Predkosc_Sluzy2, $Predkosc_Sterylizatora, $Temperatura_Sterylizacji, $Cisnienie_Sterylizacji, $Predkosc_Suszarki1, $Nadmuch_Suszarki1, $Temperatura_Suszarki1, $Predkosc_Suszarki2, $Nadmuch_Suszarki2, $Temperatura_Suszarki2, $Predkosc_Chlodziarki, $Nadmuch_Chlodziarki, $Wilgotnosc_Poczatkowa, $Wilgotnosc_Koncowa, $Odpowiedzialny);

				/* Bufurejemy wynik */
				$stmt -> store_result();

				/*Sprawdamy czy są jakieś dane jesli tak to wyswietlamy jesli nie to zgłaszamy ich brak*/
				if ($stmt -> num_rows > 0) {
					echo '<div class="alert alert-success alert-dismissable fade in">
							<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
							<span class="glyphicon glyphicon-thumbs-up"></span>&nbsp;<strong>Sukces!</strong>&nbsp To twój wynik zapytania: </div><br / >';

					//*Wyswietlamy dane nagłówkowe*/
					//Aby ponownie przeszukać wyniki musimy doadać funckje data_seek() która ustawia wskaźnik na wskazaną pozycje
					$stmt->data_seek(0);
					if ($stmt -> fetch()) {
						printf("<b>Nr Raportu:</b>&nbsp %s &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp", $Nr_Raportu );
						/*Ustawiamy zmienne sesji do poźniejszego tworzenia raportu pdf*/
						$_SESSION["asortyment"] = $asortyment;
						$_SESSION["nr_raportu"] = $Nr_Raportu;
						$_SESSION['ostatni_raport']=$ostatni_raport;
						}
					printf("<b>Asortyment:</b>&nbsp %s &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp",$asortyment_czysty);

					//Aby ponownie przeszukać wyniki musimy doadać funckje data_seek() która ustawia wskaźnik na wskazaną pozycje
					$stmt->data_seek(0);
					if ($stmt -> fetch() && $Odbiorca=="Potrzeby własne") {printf("<b>Odbiorca:</b>&nbsp %s <br / ><br / >", $Odbiorca);}
					//Aby ponownie przeszukać wyniki musimy doadać funckje data_seek() która ustawia wskaźnik na wskazaną pozycje
					$stmt->data_seek(0);
					if ($stmt -> fetch() && $Odbiorca=="Klient") {printf("<b>Odbiorca:</b>&nbsp %s <br / ><br / >", $Klient);}

					/*Tabela wielkości*/
					/*echo '<table class="table table-hover">
								 <thead>
     							 <tr><th>Data</th></tr> <tr><th>Godzina</th></tr> <tr><th>Pręd Zasob</th></tr> <tr><th>Pręd śluzy1</th></tr> <tr><th>Pręd Śluzy2</th></tr> <tr><th>Pręd Steryl</th></tr>  <tr><th>Temp Steryl</th></tr> <tr><th>Ciś Steryl</th></tr> <tr><th>Pręd Susz1</th></tr> <tr><th>Nad Susz1</th></tr> <tr><th>Temp Susz1</th></tr> <tr><th>Pręd Susz2</th></tr> <tr><th>Nad Susz2</th></tr> <tr><th>Temp Susz2</th></tr> <tr><th>Pręd Chlod</th></tr> <tr><th>Nad Chlod</th></tr> <tr><th>Wilgo. Pocz.</th></tr> <tr><th>Wilgo Kon.</th></tr> <tr><th>Osoba</th></tr>
     							 </thead>
     						</table>';*/

					echo '<div id="tabela_wielkosci">Data<br / >Godzina<br / >Pręd Zasob<br / >Pręd śluzy1<br / >Pręd Śluzy2<br / >Pręd Steryl<br / >Temp Steryl<br / >Ciś Steryl<br / >Pręd Susz1<br / >Nad Susz1<br / >Temp Susz1<br / >Pręd Susz2<br / >Nad Susz2<br / >Temp Susz2<br / >Pręd Chlod<br / >Nad Chlod<br / >Wilgo. Pocz.<br / >Wilgo Kon.<br / >Osoba<br / ></div>';

					/* Wyświetlamy dane */
					//Aby ponownie przeszukać wyniki musimy doadać funckje data_seek() która ustawia wskaźnik na wskazaną pozycje
					$stmt->data_seek(0);
					while ($stmt -> fetch()) {

						printf("<div id='tabela_wynikow'>%s <br / >%s <br / >%s Hz<br / >%s Hz<br / >%s Hz<br / >%s Hz<br / > %s &deg;C<br / >%s hPa<br / > %s Hz<br / > %s Hz<br / > %s &deg;C<br / >%s Hz<br / >%s Hz<br / > %s &deg;C<br / >%s Hz<br / > %s Hz<br / >%s %%<br / >%s %%<br / >%s</div>", $Data, $Godzina = substr($Godzina, 0, 5), $Predkosc_Zasobnika, $Predkosc_Sluzy1, $Predkosc_Sluzy2, $Predkosc_Sterylizatora, $Temperatura_Sterylizacji, $Cisnienie_Sterylizacji, $Predkosc_Suszarki1, $Nadmuch_Suszarki1, $Temperatura_Suszarki1, $Predkosc_Suszarki2, $Nadmuch_Suszarki2, $Temperatura_Suszarki2, $Predkosc_Chlodziarki, $Nadmuch_Chlodziarki, $Wilgotnosc_Poczatkowa, $Wilgotnosc_Koncowa, $Odpowiedzialny);

					}

					//Wyciągmy średnią wartość wilgotności początkowej
					if ($stmt = $mysqli -> prepare("SELECT AVG(WilgotnoscPoczatkowa) FROM `" . $asortyment . "` WHERE NrRaportu=? "))
						{
					/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
					$stmt -> bind_param("s",$_SESSION["nr_raportu"]);

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
					$stmt -> bind_param("s",$_SESSION["nr_raportu"]);

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
					$stmt -> bind_param("s",$_SESSION["nr_raportu"]);

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
					$stmt -> bind_param("s",$_SESSION["nr_raportu"]);

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
					$stmt -> bind_param("s",$_SESSION["nr_raportu"]);

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
					$stmt -> bind_param("s",$_SESSION["nr_raportu"]);

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
					$stmt -> bind_param("s",$_SESSION["nr_raportu"]);

					$stmt -> execute();
					$stmt -> bind_result($Worki);
					$stmt -> store_result();
					$stmt->data_seek(0);
						if ($stmt -> fetch()){
							printf("<b>Liczba i masa netto worków:</b>&nbsp %s <br / >", $Worki);
							}
						}

					//Wyciągmy info o wydajności
					if ($stmt = $mysqli -> prepare("SELECT Wydajnosc FROM `" . $asortyment . "` WHERE NrRaportu=? "))
						{
					/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
					$stmt -> bind_param("s",$_SESSION["nr_raportu"]);

					$stmt -> execute();
					$stmt -> bind_result($Wydajnosc);
					$stmt -> store_result();
					$stmt->data_seek(0);
						if ($stmt -> fetch()){
							printf("<b>Wydajność:</b>&nbsp %s kg/h<br / ><br / >", $Wydajnosc);
							}
						}

					//Obliczamy straty w towarze po procesie sterylizacji w kg
					if ($stmt = $mysqli -> prepare("SELECT PartiaPoczatek,PartiaKoniec FROM `" . $asortyment . "` WHERE NrRaportu=? "))
						{
					/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
					$stmt -> bind_param("s",$_SESSION["nr_raportu"]);

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
					$stmt -> bind_param("s",$_SESSION["nr_raportu"]);

					$stmt -> execute();
					$stmt -> bind_result($Partia_poczatek,$Partia_koniec);
					$stmt -> store_result();
					$stmt->data_seek(0);
					$roznica="";
						if ($stmt -> fetch()){
							printf("<b>Strata towaru w %%:</b>&nbsp %s %%<br / ><br / >", round($roznica=(($Partia_poczatek-$Partia_koniec)*100)/$Partia_poczatek,$precision=2) );
							}
						}

					//Wyciągamy info o sicie
					if ($stmt = $mysqli -> prepare("SELECT Sito FROM `" . $asortyment . "` WHERE NrRaportu=? "))
						{
					/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
					$stmt -> bind_param("s",$_SESSION["nr_raportu"]);

					$stmt -> execute();
					$stmt -> bind_result($Sito);
					$stmt -> store_result();
					$stmt->data_seek(0);
						if ($stmt -> fetch()){
							printf("<b>Sito:</b>&nbsp %s <br / ><br / >", $Sito);
							}
						}

						//Wyciągamy info o obsadzie
					if ($stmt = $mysqli -> prepare("SELECT Obsada FROM `" . $asortyment . "` WHERE NrRaportu=? "))
						{
					/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
					$stmt -> bind_param("s",$_SESSION["nr_raportu"]);

					$stmt -> execute();
					$stmt -> bind_result($Obsada);
					$stmt -> store_result();
					$stmt->data_seek(0);
						if ($stmt -> fetch()){
							printf("<b>Obsada:</b>&nbsp %s <br / ><br / >", $Obsada);
							}
						}

					//Wyciągamy info o uwagach
					if ($stmt = $mysqli -> prepare("SELECT Uwagi FROM `" . $asortyment . "` WHERE NrRaportu=? "))
						{
					/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
					$stmt -> bind_param("s",$_SESSION["nr_raportu"]);

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
					$stmt -> bind_param("s",$_SESSION["nr_raportu"]);

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
					$stmt -> bind_param("s",$_SESSION["nr_raportu"]);

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




					//Link do tworzenia raportu PDF
					echo "<form method='post' action='raportpdf_sterylizacji_pokaz.php' target='_blank'><input type='submit' value='Pobierz raport PDF' name='pdf'></form>";


					$stmt -> close();
				} else {
					echo '<div class="alert alert-info"><span class="glyphicon glyphicon-info-sign"></span>&nbsp;<strong>Info!</strong>&nbsp Brak danych w bazie danych</div>';
				}
			}
			else {
				echo '<div class="alert alert-danger"><span class="glyphicon glyphicon-thumbs-down"></span>&nbsp;<strong>Info!</strong>&nbsp Błąd w zapytaniu do bazy danych.</div>';
			}

			$mysqli -> close();

		}
	}
	else {
		echo "<div class='alert alert-warning'><strong>Uwaga!</strong>&nbsp Oprócz Asortymentu podaj: Nr Raportu lub Odbiorcę i datę lub zaznacz opcję 'Ostatni Raport' i wybierz rodzaj odbiorcy.</div>";
	}
}

//Wyświetlamy listę raportów dla podanego asortymentu
if(isset($_POST['lista_raportow']))
{
	if (isset($_POST['asortyment'])&& isset($_POST['rok'])) {
	/*Odbieramy dane z formularza*/
	$asortyment = filtruj($_POST['asortyment']);
	$asortyment_czysty=substr($asortyment,0,-7);//Usuwamy tekst '_Steryl' z końca nazwy asortymentu, który jest w bazie danych
	$odbiorca = filtruj($_POST['odbiorca']);
	$klient = filtruj($_POST['klient']);
	$rok=filtruj($_POST['rok']);

		/* Łączymy się z serwerem */
		require_once ('polaczenie_z_baza.php');

		if (mysqli_connect_errno()) {

			printf("<div class='alert alert-danger'><strong>Uwaga!</strong>&nbspBrak połączenia z serwerem MySQL. Kod błędu: %s\n</div>", mysqli_connect_error());

		}
		else {

			//usuwamy specjalne znaki takie jak '," aby nie możnabyło wpisać ich z formularza do zapytania SQL
			$asortyment = $mysqli -> real_escape_string($asortyment);
			$odbiorca = $mysqli -> real_escape_string($odbiorca);
			$klient = $mysqli -> real_escape_string($klient);
			$rok = $mysqli -> real_escape_string($rok);

			$pobrano_raporty='';
			$pobrano_daty='';

			$element_zapytania="";
			$elemet_parowania="";

			if ($odbiorca=="Potrzeby własne") {
				$element_zapytania="Odbiorca";
				$elemet_parowania="$odbiorca";
			}
			if ($odbiorca=="Klient") {
				$element_zapytania="Klient";
				$elemet_parowania="$klient";
			}

			$zapytanie='';

					if($odbiorca=="Potrzeby własne" || $odbiorca=="Klient"){
					if ($stmt = $mysqli -> prepare("SELECT DISTINCT NrRaportu FROM `" . $asortyment . "` WHERE ".$element_zapytania."=? AND Data LIKE '%" . $rok . "%' ORDER BY NrRaportu ASC"))
						{
					/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
					$stmt -> bind_param("s",$elemet_parowania);
					$zapytanie=TRUE;
						}
					}

					if($odbiorca=="Wszyscy"){
					if ($stmt = $mysqli -> prepare("SELECT DISTINCT NrRaportu FROM `" . $asortyment . "` WHERE Data LIKE '%" . $rok . "%' ORDER BY LENGTH(NrRaportu), NrRaportu ASC"))
						{
					$zapytanie=TRUE;
						}
					}



					if ($zapytanie){

					$stmt -> execute();
					$stmt -> bind_result($nr_raportu);
					$stmt -> store_result();

					$Raporty=array();

					if ($stmt->num_rows > 0) {
					/* Wyciągamy dane z zapytania sql i zapisujemy do tablicy  */
					$stmt -> data_seek(0);
    				while ($stmt->fetch()) {
					static $a=0;
					$Raporty[$a]=$nr_raportu;
					$a++;
    				}
					$pobrano_raporty=TRUE;
    				}else {
							echo '<div class="alert alert-info"><span class="glyphicon glyphicon-info-sign"></span>&nbsp;<strong>Info!</strong>&nbsp Brak raportów dla podanego asortymentu.</div>';
							brake;
							}
					//print_r($Raporty);

					$Raporty_i_daty=array();
					$Raporty_daty_odbiorca=array();
					for ($n=0; $n < count($Raporty) ; $n++)
					{
						if ($stmt = $mysqli -> prepare("SELECT  MIN(Data),Odbiorca,Klient FROM `" . $asortyment . "` WHERE NrRaportu=? AND Data LIKE '%" . $rok . "%'"))
						{
							$stmt -> bind_param("s",$Raporty[$n]);
							$stmt -> execute();
							$stmt -> bind_result($data,$odbiorca_zbazy,$klient_zbazy);
							$stmt -> store_result();

							if ($stmt->num_rows > 0) {

							/* Wyciągamy dane z zapytania sql i zapisujemy do tablicy  */
							$stmt->data_seek(0);
							$m=0;//reset do pętli poniżej
    						while ($stmt->fetch()) {

							$Raporty_i_daty[$Raporty[$n]]=$data;
							$Raporty_daty_odbiorca[$Raporty[$n]][$data]="$odbiorca_zbazy $klient_zbazy";

    						}
							$pobrano_daty=TRUE;
    						}
						}
					}

					//echo "<br / >";
					//print_r($Raporty_i_daty);
					//echo "<br / >";

					//echo "<br / >";
					//print_r($Raporty_daty_odbiorca);
					//echo "<br / >";

					if ($pobrano_raporty && $pobrano_daty)
					{
						echo '<div class="alert alert-success alert-dismissable fade in">
							<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
							<span class="glyphicon glyphicon-thumbs-up"></span>&nbsp;<strong>Sukces!</strong>&nbsp To twój wynik zapytania: </div><br / >';

						$Odbiorca='';
						if ($odbiorca=="Potrzeby własne") {
							$Odbiorca="Potrzeby własne";
						}
						if ($odbiorca=="Klient") {
							$Odbiorca=$klient;
						}

						if ($odbiorca=="Wszyscy") {
							$Odbiorca="Wszyscy";
						}

						if ($Odbiorca=="Wszyscy") {

							echo "<div class='row'> <div class='col-sm-4'><h4><b>Lista raportów:</b> $asortyment_czysty</h4></div>  <div class='col-sm-4'><h4><b>Rok:</b> $rok </h4></div> <div class='col-sm-4'><h4><b>Odbiorca:</b> $Odbiorca </h4></div> </div>
								<hr></hr>
								<div class='row'> <div class='col-sm-4'><b>Nr Raportu:</b></div> <div class='col-sm-4'><b>Data:</b></div> <div class='col-sm-4'><b>Odbiorca:</b></div> </div><br / >";

							foreach ($Raporty_daty_odbiorca as $raport => $tab)
							{
								foreach ($tab as $data => $odbiorca) {
									echo "<div class='row' > <div class='col-sm-4'>$raport</div> <div class='col-sm-4'>$data</div> <div class='col-sm-4'>$odbiorca</div> </div>";
									}
							}

						}
						else {

							echo "<h4><b>Lista raportów dla asortymentu:</b> $asortyment_czysty &nbsp&nbsp&nbsp&nbsp <b>Rok:</b> $rok &nbsp&nbsp&nbsp&nbsp <b>Odbiorca:</b> $Odbiorca </h4>
								<div class='row' > <div class='col-sm-4'><b>Nr Raportu:</b></div> <div class='col-sm-4'><b>Data:</b></div> </div>";


							foreach ($Raporty_i_daty as $raport => $data) {
								echo "<div class='row' > <div class='col-sm-4'>$raport</div> <div class='col-sm-4'>$data</div> </div><br / >";
									}
							}

					}

				}


			}

	}else {
		echo "<div class='alert alert-warning'><span class='glyphicon glyphicon-alert'></span>&nbsp<strong>Uwaga!</strong>&nbsp Podaj asortyment dla, którego ma być wyświetlona lista raportów.</div>";
			}
}
?>
<br / >
<br / >

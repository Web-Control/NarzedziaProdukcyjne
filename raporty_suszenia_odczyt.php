<h1>Raport z procesu suszenia</h1>
      <ul class="nav nav-tabs">
  <li><a href="index2.php?raporty_suszenia=1">Tworzenie</a></li>
  <li class="active"><a href="index2.php?raporty_suszenia_odczyt=1">Odczyt</a></li>
  <li><a href="index2.php?raporty_suszenia_pobierz=1">Pobór</a></li>
  <li><a href="index2.php?statystyki_suszenia=1">Wykresy</a></li>
</ul>
<br />

<div id="formularz">
		<div class="row" >
			<div class="form-group">
     <form method="post" action="index2.php?raporty_suszenia_odczyt=1">
				<fieldset>
					<legend>
					Odczytaj raport
					</legend>

				<div class="row">
					<div class="col-sm-4">
					<label >Asortyment</label>
					<select class="form-control" name="asortyment_suszu" required>
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
					
					if (isset($_POST['asortyment_suszu']))
					{
						echo '<option value="'.$_POST['asortyment_suszu'].'">'.$_POST['asortyment_suszu'].'</option>';

					}

						foreach ($Asortyment_wbazie as $key => $value) {
	
							printf("<option value='%s'>%s</option>",$value,$value);
						}
				

				}
?>
					</select>
					</div>

					<div class="col-sm-4">
					<label >Data</label>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp Ostatni<input type="checkbox" name="ostatni_raport" value="Ostatni_raport"/>
					<input class="form-control" type="date" name="data_raportu" <?php if (isset($_POST['data_raportu'])) {echo "value='".$_POST['data_raportu']."'";}else{  echo "value='rrrr-mm-dd'";} ?>/>
					</div>

					<div class="col-sm-4">
					<label>Nr Suszarni</label>
					<select class="form-control" name="nr_suszarni"  min="1" max="5" required>
						<?php
							if (isset($_POST['nr_suszarni']))
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
					<label >Rok</label>
					<input class="form-control"  type="number" min="1900" max="2099" step="1" value="<?php echo date("Y"); ?>" name="rok" />
					</div>

				</div>
					<hr></hr>

					<span class="glyphicon glyphicon-export"></span>&nbsp<input type="submit" value="Odczytaj raport" name="submit2"><br / ><br / >
					<span class="glyphicon glyphicon-export"></span>&nbsp<input type="submit" value="Podsumowanie asortymentu" name="produkcja_asortymentu"><br / ><br / >
					<span class="glyphicon glyphicon-export"></span>&nbsp<input type="submit" value="Podsumowanie całej produkcji" name="cala_produkcja"><br / ><br / >
					
					<?php 
					if ($_POST['submit2'] || $_POST['poprzedni'] || $_POST['nastepny']) {
						echo "<hr><br / ><div class='row'>
							<div class='col-sm-4'><input type='submit' value=' < Poprzedni dzień' name='poprzedni'></div>
							<div class='col-sm-4'><input type='submit' value='Następny dzień > ' name='nastepny'></div>
						</div>";
					}
					?>
					
				</fieldset>
			</form>
		</div>
	</div>
</div>
				<br / ><br / >
<?php
	/*ob_end_clean();
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);*/
			function filtruj($zmienna) {
							$data = trim($zmienna);//usuwa spacje, tagi
							$data = stripslashes($zmienna);//usuwa slashe
							$data = htmlspecialchars($zmienna);//zamienia tagi html na czytelne znaki aby w formularzu nie wpisać szkodliwego kodu
							return $zmienna;
						}
			/*Odbieramy dane z formularza*/
			$data_raportu = filtruj($_POST['data_raportu']);
			$asortyment_suszu = filtruj($_POST['asortyment_suszu']);
			$nr_suszarni = filtruj($_POST['nr_suszarni']);
			$ostatni_raport=filtruj($_POST['ostatni_raport']);
			$opcja_ostatni_raport="";
			$opcja2="";
			
			$wszystkie_dane=array($asortyment_suszu,$nr_suszarni);
			
			//echo "Działa DS".$_SESSION['data_do_nav']."DR ".$data_raportu."";

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

			if (isset($ostatni_raport) && !$ostatni_raport == null && strlen($ostatni_raport)>0 && sprawdz_istnienie_danych($wszystkie_dane))
			{$opcja_ostatni_raport=TRUE;	}

			if (!$opcja_ostatni_raport && isset($data_raportu) && !$data_raportu == null && strlen($data_raportu)>0 && sprawdz_istnienie_danych($wszystkie_dane))
			{$opcja2=TRUE;}

			/*Sprawdzamy czy formularz został wypełniony*/
			if (isset($_POST['submit2']) || isset($_POST['poprzedni']) ||isset($_POST['nastepny'])) {
				if ($opcja_ostatni_raport || $opcja2 || isset($_POST['poprzedni']) ||isset($_POST['nastepny'])) {

				/*Ustawiamy zmienne sesji do poźniejszego tworzenia raportu pdf*/
				$_SESSION["asortyment_suszu"] = $asortyment_suszu;
				$_SESSION["nr_suszarni"] = $nr_suszarni;


				//* Łączymy się z serwerem */
				require_once ('polaczenie_z_baza.php');

				if (mysqli_connect_errno()) {

					printf("<div class='alert alert-danger'><strong>Uwaga!</strong>&nbsp Brak połączenia z serwerem MySQL. Kod błędu: %s\n</div>", mysqli_connect_error());
				} else {
					$zapytanie="";
					//usuwamy specjalne znaki takie jak '," aby nie możnabyło wpisać ich z formularza do zapytania SQL
					$asortyment_suszu = $mysqli -> real_escape_string($asortyment_suszu);
					$data_raportu = $mysqli -> real_escape_string($data_raportu);
					
					//Ustawiamy date raportu do nawigacji poprzedni/nastepny dzień
					if (isset($_POST['submit2']) || isset($_POST['ostatni_raport'])) {
						//echo "Działa DS".$_SESSION['data_do_nav']." DR ".$data_raportu."";
							$_SESSION['data_do_nav']="";
						}	
					if ($_POST['poprzedni']) {
						
						if (!isset($_SESSION['data_do_nav'])) 
						{
						$data_raportu=date('Y-m-d', strtotime($data_raportu . ' -1 day'));
						$_SESSION['data_do_nav']=$data_raportu;
						}else
							{
							$_SESSION['data_do_nav']=date('Y-m-d', strtotime($_SESSION['data_do_nav'] . ' -1 day'));
							$data_raportu=$_SESSION['data_do_nav'];
							}
					}
					
					if ($_POST['nastepny']) {
						
						if (!isset($_SESSION['data_do_nav'])) 
						{
						$data_raportu=date('Y-m-d', strtotime($data_raportu . ' +1 day'));
						$_SESSION['data_do_nav']=$data_raportu;
						}else
							{
							$_SESSION['data_do_nav']=date('Y-m-d', strtotime($_SESSION['data_do_nav'] . ' +1 day'));
							$data_raportu=$_SESSION['data_do_nav'];
							}
					}
					
					$kolejny_dzien = date('Y-m-d', strtotime($data_raportu . ' +1 day'));
					$nr_suszarni = $mysqli -> real_escape_string($nr_suszarni);
					$ostatni_raport = $mysqli -> real_escape_string($ostatni_raport);
					
					

					if ($opcja_ostatni_raport){
						if ($stmt = $mysqli -> prepare("SELECT Czas FROM `" . $asortyment_suszu . "`  WHERE Data=(SELECT MAX(Data) FROM `" . $asortyment_suszu . "`) AND Czas >=  STR_TO_DATE('08:00:00','%h:%i:%s') AND NrSuszarni=?"))
							{
							/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
								$stmt -> bind_param("s", $nr_suszarni);
								$stmt->execute();
								$stmt->store_result();

								if ($stmt->num_rows > 0) {


									if ($stmt = $mysqli -> prepare("SELECT Data,Czas,PredkoscBlanszownika,TemperaturaBlanszownika,PredkoscSiatkiNr7,PredkoscSiatkiNr6,PredkoscSiatkiNr5,PredkoscSiatkiNr4,PredkoscSiatkiNr3,PredkoscSiatkiNr2,PredkoscSiatkiNr1,CzasSuszenia,TemperaturaGora,TemperaturaDol,Wilgotnosc,WykonawcaPomiaru FROM `" . $asortyment_suszu . "`  WHERE Data=(SELECT MAX(Data) FROM `" . $asortyment_suszu . "`) AND Czas >=  STR_TO_DATE('08:00:00','%h:%i:%s') AND NrSuszarni=?
									UNION ALL
									SELECT Data,Czas,PredkoscBlanszownika,TemperaturaBlanszownika,PredkoscSiatkiNr7,PredkoscSiatkiNr6,PredkoscSiatkiNr5,PredkoscSiatkiNr4,PredkoscSiatkiNr3,PredkoscSiatkiNr2,PredkoscSiatkiNr1,CzasSuszenia,TemperaturaGora,TemperaturaDol,Wilgotnosc,WykonawcaPomiaru FROM `".$asortyment_suszu."` WHERE Data=DATE_ADD((SELECT MAX(Data) FROM `" . $asortyment_suszu . "`),INTERVAL 1 DAY) AND Czas <=  STR_TO_DATE('06:00:00','%h:%i:%s') AND NrSuszarni=? ORDER BY Data, Czas ASC"
									))
									{
									/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
									$stmt -> bind_param("ss", $nr_suszarni,$nr_suszarni);
									$zapytanie=TRUE;
									}
							}else{

								if ($stmt = $mysqli -> prepare("SELECT Data,Czas,PredkoscBlanszownika,TemperaturaBlanszownika,PredkoscSiatkiNr7,PredkoscSiatkiNr6,PredkoscSiatkiNr5,PredkoscSiatkiNr4,PredkoscSiatkiNr3,PredkoscSiatkiNr2,PredkoscSiatkiNr1,CzasSuszenia,TemperaturaGora,TemperaturaDol,Wilgotnosc,WykonawcaPomiaru FROM `" . $asortyment_suszu . "`  WHERE Data=DATE_ADD((SELECT MAX(Data) FROM `" . $asortyment_suszu . "`),INTERVAL -1 DAY) AND Czas >=  STR_TO_DATE('08:00:00','%h:%i:%s') AND NrSuszarni=?
									UNION ALL
									SELECT Data,Czas,PredkoscBlanszownika,TemperaturaBlanszownika,PredkoscSiatkiNr7,PredkoscSiatkiNr6,PredkoscSiatkiNr5,PredkoscSiatkiNr4,PredkoscSiatkiNr3,PredkoscSiatkiNr2,PredkoscSiatkiNr1,CzasSuszenia,TemperaturaGora,TemperaturaDol,Wilgotnosc,WykonawcaPomiaru FROM `".$asortyment_suszu."` WHERE Data=(SELECT MAX(Data) FROM `" . $asortyment_suszu . "`) AND Czas <=  STR_TO_DATE('06:00:00','%h:%i:%s') AND NrSuszarni=? ORDER BY Data, Czas ASC"
									))
									{
									/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
									$stmt -> bind_param("ss", $nr_suszarni,$nr_suszarni);
									$zapytanie=TRUE;
									}


								}

						}
					}

					if ($opcja2){
					/*Tworzymy zapytanie i sprawdzamy czy nie zwraca błedu*/
					if ($stmt=$mysqli->prepare("SELECT Data,Czas,PredkoscBlanszownika,TemperaturaBlanszownika,PredkoscSiatkiNr7,PredkoscSiatkiNr6,PredkoscSiatkiNr5,PredkoscSiatkiNr4,PredkoscSiatkiNr3,PredkoscSiatkiNr2,PredkoscSiatkiNr1,CzasSuszenia,TemperaturaGora,TemperaturaDol,Wilgotnosc,WykonawcaPomiaru FROM `".$asortyment_suszu."` WHERE Data=? AND Czas >=  STR_TO_DATE('08:00:00','%h:%i:%s') AND NrSuszarni=?
					UNION ALL
					SELECT Data,Czas,PredkoscBlanszownika,TemperaturaBlanszownika,PredkoscSiatkiNr7,PredkoscSiatkiNr6,PredkoscSiatkiNr5,PredkoscSiatkiNr4,PredkoscSiatkiNr3,PredkoscSiatkiNr2,PredkoscSiatkiNr1,CzasSuszenia,TemperaturaGora,TemperaturaDol,Wilgotnosc,WykonawcaPomiaru FROM `".$asortyment_suszu."` WHERE Data=? AND Czas <=  STR_TO_DATE('06:00:00','%h:%i:%s') AND NrSuszarni=? ORDER BY Data, Czas ASC"
					))

					{
					/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
					$stmt->bind_param("ssss",$data_raportu,$nr_suszarni,$kolejny_dzien,$nr_suszarni);
					$zapytanie=TRUE;
					}
					}

					if ($zapytanie) {

					$stmt->execute();

					 /* Powiązujemy dane z zapytania do zmiennych, których uzyjemy do wyswietlenia danych */
    				$stmt->bind_result($Data,$Czas,$Predkosc_Blanszownika,$Temperatura_Blanszownika,$V_Siatka7,$V_Siatka6,$V_Siatka5,$V_Siatka4,$V_Siatka3,$V_Siatka2,$V_Siatka1,$Czas_Suszenia,$Temp_Gorna,$Temp_Dolna,$Wilgotnosc,$Odpowiedzialny);

					  /* Bufurejemy wynik */
    				$stmt->store_result();

   					/*Sprawdamy czy są jakieś dane jesli tak to wyswietlamy jesli nie to zgłaszamy ich brak*/
   					if ($stmt->num_rows > 0) {
   						echo '<div class="alert alert-success alert-dismissable fade in">
							<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
							<span class="glyphicon glyphicon-thumbs-up"></span>&nbsp;<strong>Sukces!</strong>&nbsp Odczytano dane. Poniżej znajduje się twój raport. </div><br / >';
							

						/*Wyswietlamy dane nagłówkowe*/
						printf("<b>Asortyment:</b>&nbsp %s &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp ", $asortyment_suszu);
						//Aby ponownie przeszukać wyniki musimy doadać funckje data_seek() która ustawia wskaźnik na wskazaną pozycje
						$stmt->data_seek(0);
						if ($stmt -> fetch()) {
							printf("<b>Dat:</b>&nbsp %s &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp", $Data);
							/*Ustawiamy zmienne sesji do poźniejszego tworzenia raportu pdf*/
							$_SESSION["data_raportu"] = $Data;
						}
						printf("<b>Nr Suszarni:</b>&nbsp %s <br / ><br / >", $nr_suszarni);


						/*Tabela wielkości*/
						echo '<div id="tabela_wielkosci">Godzina<br / >Pręd Blansz<br / >Temp Blans<br / >Siatka nr 7<br / >Siatka nr 6<br / >Siatka nr 5<br / >Siatka nr 4<br / >Siatka nr 3<br / >Siatka nr 2<br / >Siatka nr 1<br / >Czas Susz.<br / >Temp. Góra<br / >Temp. Dół<br / >Wilgotność<br / >Osoba<br / ></div>';

					/* Wyświetlamy dane */
					$stmt->data_seek(0);
   					while ($stmt->fetch()) {
   					    printf ("<div id='tabela_wynikow'>%s. <br / >%s Hz<br / >%s &deg;C<br / >%s Hz<br / > %s Hz<br / >%s Hz<br / > %s Hz<br / > %s Hz<br / > %s Hz<br / >%s Hz<br / >%s min<br / >%s &deg;C<br / > %s &deg;C<br / >%s %% <br / > %s</div>", $Czas = substr($Czas, 0, 5),$Predkosc_Blanszownika,$Temperatura_Blanszownika,$V_Siatka7,$V_Siatka6,$V_Siatka5,$V_Siatka4,$V_Siatka3,$V_Siatka2,$V_Siatka1,$Czas_Suszenia,$Temp_Gorna,$Temp_Dolna,$Wilgotnosc,$Odpowiedzialny);
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


					if ($opcja_ostatni_raport){

				$kolejny_dzien = "";

				if ($stmt = $mysqli -> prepare("SELECT MAX(Data) FROM `" . $asortyment_suszu . "` WHERE Czas >=  STR_TO_DATE('08:00:00','%h:%i:%s') AND NrSuszarni=? "))
				{
					$stmt -> bind_param("s",$nr_suszarni);
					$stmt -> execute();
					$stmt -> bind_result($Max_data_raportu);
					$stmt -> store_result();
					if ($stmt -> fetch()) {
				$data_raportu = $Max_data_raportu;
				$kolejny_dzien = date('Y-m-d', strtotime($data_raportu . ' +1 day'));
					}
				}


					if ($stmt = $mysqli -> prepare("SELECT SUM(Wilgotnosc) FROM `" . $asortyment_suszu . "` WHERE Data=? AND Czas >=  STR_TO_DATE('08:00:00','%h:%i:%s') AND NrSuszarni=? AND Wilgotnosc > 0"))
						{
					$stmt -> bind_param("ss", $data_raportu,$nr_suszarni);
					$stmt -> execute();
					$stmt -> bind_result($Suma_Wilg);
					$stmt -> store_result();
					$stmt->data_seek(0);
						if ($stmt -> fetch()){
							$Suma_Wilgotnosc1=$Suma_Wilg;

							}

						if ($stmt = $mysqli -> prepare("SELECT COUNT(Wilgotnosc) FROM `" . $asortyment_suszu . "` WHERE Data=? AND Czas >=  STR_TO_DATE('08:00:00','%h:%i:%s') AND NrSuszarni=? AND Wilgotnosc > 0"))
						{
					$stmt -> bind_param("ss", $data_raportu,$nr_suszarni);
					$stmt -> execute();
					$stmt -> bind_result($Ilosc_pom);
					$stmt -> store_result();
					$stmt->data_seek(0);
						if ($stmt -> fetch()){
							$Ilosc_pomiarow1=$Ilosc_pom;

							}
						}


						if ($stmt = $mysqli -> prepare("SELECT SUM(Wilgotnosc) FROM `" . $asortyment_suszu . "` WHERE Data=? AND Czas <=  STR_TO_DATE('06:00:00','%h:%i:%s') AND NrSuszarni=? AND Wilgotnosc > 0"))
						{
					$stmt -> bind_param("ss", $kolejny_dzien,$nr_suszarni);
					$stmt -> execute();
					$stmt -> bind_result($Suma_Wilg);
					$stmt -> store_result();
					$stmt->data_seek(0);
						if ($stmt -> fetch()){
							$Suma_Wilgotnosc2=$Suma_Wilg;
						}

						if ($stmt = $mysqli -> prepare("SELECT COUNT(Wilgotnosc) FROM `" . $asortyment_suszu . "` WHERE Data=? AND Czas <=  STR_TO_DATE('06:00:00','%h:%i:%s') AND NrSuszarni=? AND Wilgotnosc > 0"))
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
						}

						$Suma_Wilgotnosc=$Suma_Wilgotnosc1+$Suma_Wilgotnosc2;
						$Ilosc_pomiarow=$Ilosc_pomiarow1+$Ilosc_pomiarow2;

						$Srednia_Wilgotnosc=($Suma_Wilgotnosc/$Ilosc_pomiarow);
						printf("<br /><br /> <b>Średnia wilgotność:</b>&nbsp %s %%", round($Srednia_Wilgotnosc,$precision=2));

					} else
						{
							if ($stmt = $mysqli -> prepare("SELECT SUM(Wilgotnosc) FROM `" . $asortyment_suszu . "` WHERE Data=? AND Czas >=  STR_TO_DATE('08:00:00','%h:%i:%s') AND NrSuszarni=? AND Wilgotnosc > 0"))
						{

					$stmt -> bind_param("ss",$data_raportu,$nr_suszarni);
					$stmt -> execute();
					$stmt -> bind_result($Sum_Wilg);
					$stmt -> store_result();
					$stmt->data_seek(0);
						if ($stmt -> fetch()){
							$Suma_Wilgotnosc1=$Sum_Wilg;
							}

						if ($stmt = $mysqli -> prepare("SELECT COUNT(Wilgotnosc) FROM `" . $asortyment_suszu . "` WHERE Data=? AND Czas >=  STR_TO_DATE('08:00:00','%h:%i:%s') AND NrSuszarni=? AND Wilgotnosc > 0"))
						{
					$stmt -> bind_param("ss", $data_raportu,$nr_suszarni);
					$stmt -> execute();
					$stmt -> bind_result($Ilosc_pom);
					$stmt -> store_result();
					$stmt->data_seek(0);
						if ($stmt -> fetch()){
							$Ilosc_pomiarow1=$Ilosc_pom;

							}
						}

						}


						if ($stmt = $mysqli -> prepare("SELECT Sum(Wilgotnosc) FROM `" . $asortyment_suszu . "` WHERE Data=? AND Czas <=  STR_TO_DATE('06:00:00','%h:%i:%s') AND NrSuszarni=? AND Wilgotnosc > 0"))
						{
					$stmt -> bind_param("ss",$kolejny_dzien,$nr_suszarni);
					$stmt -> execute();
					$stmt -> bind_result($Sum_Wilg);
					$stmt -> store_result();
					$stmt->data_seek(0);
						if ($stmt -> fetch()){
							$Suma_Wilgotnosc2=$Sum_Wilg;
						}

						if ($stmt = $mysqli -> prepare("SELECT COUNT(Wilgotnosc) FROM `" . $asortyment_suszu . "` WHERE Data=? AND Czas <=  STR_TO_DATE('06:00:00','%h:%i:%s') AND NrSuszarni=? AND Wilgotnosc > 0"))
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
					}






					//Wyciągmy info o ocenie suszu na I zmianie
					if ($opcja_ostatni_raport){
					if ($stmt = $mysqli -> prepare("SELECT OcenaTowaruZmiany1 FROM `" . $asortyment_suszu . "` WHERE Data=(SELECT MAX(Data) FROM `" . $asortyment_suszu . "` WHERE Czas >=  STR_TO_DATE('08:00:00','%h:%i:%s')AND NrSuszarni=?) AND NrSuszarni=? "))
						{
					$stmt -> bind_param("ss",$nr_suszarni,$nr_suszarni);
					$stmt -> execute();
					$stmt -> bind_result($Ocena_suszu1);
					$stmt -> store_result();
					$stmt->data_seek(0);
						if ($stmt -> fetch()){
							printf("<br /><br /> <b>Ocena suszu po I zmianie:</b>&nbsp %s", $Ocena_suszu1);
							}
						}
					}else
						{

						if ($stmt = $mysqli -> prepare("SELECT OcenaTowaruZmiany1 FROM `" . $asortyment_suszu . "` WHERE Data=? AND NrSuszarni=? "))
						{
					$stmt -> bind_param("ss",$data_raportu,$nr_suszarni);
					$stmt -> execute();
					$stmt -> bind_result($Ocena_suszu1);
					$stmt -> store_result();
					$stmt->data_seek(0);
						if ($stmt -> fetch()){
							printf("<br /><br /> <b>Ocena suszu po I zmianie:</b>&nbsp %s", $Ocena_suszu1);
							}
						}
						}

						//Wyciągmy info o ocenie suszu na II zmianie
					if ($opcja_ostatni_raport){
					if ($stmt = $mysqli -> prepare("SELECT OcenaTowaruZmiany2 FROM `" . $asortyment_suszu . "` WHERE Data=(SELECT MAX(Data) FROM `" . $asortyment_suszu . "` WHERE Czas >=  STR_TO_DATE('08:00:00','%h:%i:%s')AND NrSuszarni=?) AND NrSuszarni=? "))
						{
					$stmt -> bind_param("ss",$nr_suszarni,$nr_suszarni);
					$stmt -> execute();
					$stmt -> bind_result($Ocena_suszu2);
					$stmt -> store_result();
					$stmt->data_seek(0);
						if ($stmt -> fetch()){
							printf("<br /><br /> <b>Ocena suszu po II zmianie:</b>&nbsp %s", $Ocena_suszu2);
							}
						}
					}else
						{

						if ($stmt = $mysqli -> prepare("SELECT OcenaTowaruZmiany2 FROM `" . $asortyment_suszu . "` WHERE Data=? AND NrSuszarni=?"))
						{
					$stmt -> bind_param("ss",$data_raportu,$nr_suszarni);
					$stmt -> execute();
					$stmt -> bind_result($Ocena_suszu2);
					$stmt -> store_result();
					$stmt->data_seek(0);
						if ($stmt -> fetch()){
							printf("<br /><br /> <b>Ocena suszu po II zmianie:</b>&nbsp %s", $Ocena_suszu2);
							}
						}
						}

						//Wyciągmy info o ocenie suszu na III zmianie
					if ($opcja_ostatni_raport){
					if ($stmt = $mysqli -> prepare("SELECT OcenaTowaruZmiany3 FROM `" . $asortyment_suszu . "` WHERE Data=(SELECT MAX(Data) FROM `" . $asortyment_suszu . "` WHERE Czas >=  STR_TO_DATE('08:00:00','%h:%i:%s')AND NrSuszarni=?) AND NrSuszarni=?"))
						{
					$stmt -> bind_param("ss",$nr_suszarni,$nr_suszarni);
					$stmt -> execute();
					$stmt -> bind_result($Ocena_suszu3);
					$stmt -> store_result();
					$stmt->data_seek(0);
						if ($stmt -> fetch()){
							printf("<br /><br /> <b>Ocena suszu po III zmianie:</b>&nbsp %s", $Ocena_suszu3);
							}
						}
					}else
						{

						if ($stmt = $mysqli -> prepare("SELECT OcenaTowaruZmiany3 FROM `" . $asortyment_suszu . "` WHERE Data=? AND NrSuszarni=?" ))
						{
					$stmt -> bind_param("ss",$data_raportu,$nr_suszarni);
					$stmt -> execute();
					$stmt -> bind_result($Ocena_suszu3);
					$stmt -> store_result();
					$stmt->data_seek(0);
						if ($stmt -> fetch()){
							printf("<br /><br /> <b>Ocena suszu po III zmianie:</b>&nbsp %s <br / >", $Ocena_suszu3);
							}
						}
						}

					//Wyciągmy wartość iloci suszu na I zmianie
					if ($opcja_ostatni_raport){
					if ($stmt = $mysqli -> prepare("SELECT IloscSuszuZmiana1 FROM `" . $asortyment_suszu . "` WHERE Data=(SELECT MAX(Data) FROM `" . $asortyment_suszu . "` WHERE Czas >=  STR_TO_DATE('08:00:00','%h:%i:%s')AND NrSuszarni=?) AND NrSuszarni=?"))
						{
					$stmt -> bind_param("ss",$nr_suszarni,$nr_suszarni);
					$stmt -> execute();
					$stmt -> bind_result($Ilosc_suszu1);
					$stmt -> store_result();
					$stmt->data_seek(0);
						if ($stmt -> fetch()){
							printf("<br /><br /> <b>Ilość suszu na I zmianie:</b>&nbsp %s kg", $Ilosc_suszu1);
							}
						}
					}else
						{

						if ($stmt = $mysqli -> prepare("SELECT IloscSuszuZmiana1 FROM `" . $asortyment_suszu . "` WHERE Data=? AND NrSuszarni=?"))
						{
					$stmt -> bind_param("ss",$data_raportu,$nr_suszarni);
					$stmt -> execute();
					$stmt -> bind_result($Ilosc_suszu1);
					$stmt -> store_result();
					$stmt->data_seek(0);
						if ($stmt -> fetch()){
							printf("<br /><br /> <b>Ilość suszu na I zmianie:</b>&nbsp %s kg", $Ilosc_suszu1);
							}
						}
						}

					//Wyciągmy wartość iloci suszu na II zmianie
					if ($opcja_ostatni_raport){
					if ($stmt = $mysqli -> prepare("SELECT IloscSuszuZmiana2 FROM `" . $asortyment_suszu . "` WHERE Data=(SELECT MAX(Data) FROM `" . $asortyment_suszu . "` WHERE Czas >=  STR_TO_DATE('08:00:00','%h:%i:%s')AND NrSuszarni=?) AND NrSuszarni=?"))
						{
					$stmt -> bind_param("ss",$nr_suszarni,$nr_suszarni);
					$stmt -> execute();
					$stmt -> bind_result($Ilosc_suszu2);
					$stmt -> store_result();
					$stmt->data_seek(0);
						if ($stmt -> fetch()){
							printf("<br /><br /> <b>Ilość suszu na II zmianie:</b>&nbsp %s kg", $Ilosc_suszu2);
							}
						}
					}else
						{

						if ($stmt = $mysqli -> prepare("SELECT IloscSuszuZmiana2 FROM `" . $asortyment_suszu . "` WHERE Data=? AND NrSuszarni=?"))
						{
					$stmt -> bind_param("ss",$data_raportu,$nr_suszarni);
					$stmt -> execute();
					$stmt -> bind_result($Ilosc_suszu2);
					$stmt -> store_result();
					$stmt->data_seek(0);
						if ($stmt -> fetch()){
							printf("<br /><br /> <b>Ilość suszu na II zmianie:</b>&nbsp %s kg", $Ilosc_suszu2);
							}
						}
						}

					//Wyciągmy wartość iloci suszu na III zmianie
					if ($opcja_ostatni_raport){
					if ($stmt = $mysqli -> prepare("SELECT IloscSuszuZmiana3 FROM `" . $asortyment_suszu . "` WHERE Data=(SELECT MAX(Data) FROM `" . $asortyment_suszu . "` WHERE Czas >=  STR_TO_DATE('08:00:00','%h:%i:%s')AND NrSuszarni=?) AND NrSuszarni=?"))
						{
					$stmt -> bind_param("ss", $nr_suszarni,$nr_suszarni);
					$stmt -> execute();
					$stmt -> bind_result($Ilosc_suszu3);
					$stmt -> store_result();
					$stmt->data_seek(0);
						if ($stmt -> fetch()){
							printf("<br /><br /> <b>Ilość suszu na III zmianie:</b>&nbsp %s kg", $Ilosc_suszu3);
							}
						}
					}else
						{

						if ($stmt = $mysqli -> prepare("SELECT IloscSuszuZmiana3 FROM `" . $asortyment_suszu . "` WHERE Data=? AND NrSuszarni=?"))
						{
					$stmt -> bind_param("ss",$data_raportu,$nr_suszarni);
					$stmt -> execute();
					$stmt -> bind_result($Ilosc_suszu3);
					$stmt -> store_result();
					$stmt->data_seek(0);
						if ($stmt -> fetch()){
							printf("<br /><br /> <b>Ilość suszu na III zmianie:</b>&nbsp %s kg <br / >", $Ilosc_suszu3);
							}
						}
						}

					//Wyciągmy wartość całkowitej iloci suszu
					if ($opcja_ostatni_raport){
					if ($stmt = $mysqli -> prepare("SELECT CalkowitaIloscSuszu FROM `" . $asortyment_suszu . "` WHERE Data=(SELECT MAX(Data) FROM `" . $asortyment_suszu . "` WHERE Czas >=  STR_TO_DATE('08:00:00','%h:%i:%s')AND NrSuszarni=?) AND NrSuszarni=?"))
						{
					$stmt -> bind_param("ss",$nr_suszarni,$nr_suszarni);
					$stmt -> execute();
					$stmt -> bind_result($Ilosc_suszu);
					$stmt -> store_result();
					$stmt->data_seek(0);
						if ($stmt -> fetch()){
							printf("<br /><br /> <b>Całkowita ilość suszu:</b>&nbsp %s kg", $Ilosc_suszu);
							}
						}
					}else
						{

						if ($stmt = $mysqli -> prepare("SELECT CalkowitaIloscSuszu FROM `" . $asortyment_suszu . "` WHERE Data=? AND NrSuszarni=?"))
						{
					$stmt -> bind_param("ss",$data_raportu,$nr_suszarni);
					$stmt -> execute();
					$stmt -> bind_result($Ilosc_suszu);
					$stmt -> store_result();
					$stmt->data_seek(0);
						if ($stmt -> fetch()){
							printf("<br /><br /> <b>Całkowita ilość suszu:</b>&nbsp %s kg", $Ilosc_suszu);
							}
						}
						}

					//Wyciągmy informacje o dostawcy
					if ($opcja_ostatni_raport){
					if ($stmt = $mysqli -> prepare("SELECT Dostawca FROM `" . $asortyment_suszu . "` WHERE Data=(SELECT MAX(Data) FROM `" . $asortyment_suszu . "` WHERE Czas >=  STR_TO_DATE('08:00:00','%h:%i:%s')AND NrSuszarni=?) AND NrSuszarni=?" ))
						{
					$stmt -> bind_param("ss",$nr_suszarni,$nr_suszarni);
					$stmt -> execute();
					$stmt -> bind_result($Dostawca);
					$stmt -> store_result();
					$stmt->data_seek(0);
						if ($stmt -> fetch()){
							printf("<br /><br /> <b>Dostawca:</b>&nbsp %s <br / ><br / >", $Dostawca);
							}
						}
					}else
					{
						if ($stmt = $mysqli -> prepare("SELECT Dostawca FROM `" . $asortyment_suszu . "` WHERE Data=? AND NrSuszarni=?"))
						{
					$stmt -> bind_param("ss",$data_raportu,$nr_suszarni);
					$stmt -> execute();
					$stmt -> bind_result($Dostawca);
					$stmt -> store_result();
					$stmt->data_seek(0);
						if ($stmt -> fetch()){
							printf("<br /><br /> <b>Dostawca:</b>&nbsp %s <br / >", $Dostawca);
							}
						}

					}

						//Wyciągmy informacje o uwagach
					if ($opcja_ostatni_raport){
					if ($stmt = $mysqli -> prepare("SELECT Uwagi FROM `" . $asortyment_suszu . "` WHERE Data=(SELECT MAX(Data) FROM `" . $asortyment_suszu . "` WHERE Czas >=  STR_TO_DATE('08:00:00','%h:%i:%s')AND NrSuszarni=?) AND NrSuszarni=?"))
						{
					$stmt -> bind_param("ss",$nr_suszarni,$nr_suszarni);
					$stmt -> execute();
					$stmt -> bind_result($Uwagi);
					$stmt -> store_result();
					$stmt->data_seek(0);
						if ($stmt -> fetch()){
							printf("<br /> <b>Uwagi:</b>&nbsp %s <br / ><br / >", $Uwagi);
							}
						}
					}else
					{
						if ($stmt = $mysqli -> prepare("SELECT Uwagi FROM `" . $asortyment_suszu . "` WHERE Data=? AND NrSuszarni=?"))
						{
					$stmt -> bind_param("ss",$data_raportu,$nr_suszarni);
					$stmt -> execute();
					$stmt -> bind_result($Uwagi);
					$stmt -> store_result();
					$stmt->data_seek(0);
						if ($stmt -> fetch()){
							printf("<br /><br /> <b>Uwagi:</b>&nbsp %s <br / ><br / >", $Uwagi);
							}
						}

					}

					//Wyciągamy zdjęcie
					if ($opcja_ostatni_raport){
					if ($stmt = $mysqli -> prepare("SELECT Zdjecia,OpisZdjecia FROM `" . $asortyment_suszu. "` WHERE Data=(SELECT MAX(Data) FROM `" . $asortyment_suszu . "` WHERE Czas >=  STR_TO_DATE('08:00:00','%h:%i:%s')AND NrSuszarni=?) AND NrSuszarni=?"))
						{
					$stmt -> bind_param("ss",$nr_suszarni,$nr_suszarni);
					$stmt -> execute();
					$stmt -> bind_result($Zdjecie,$Opis_zdjecia);
					$stmt -> store_result();
					$stmt->data_seek(0);
						if ($stmt -> fetch()){
							if (empty($Zdjecie)) {echo "<b>Zdjęcia:</b> Brak zdjęć.<br / ><br / >";}
							else {
							printf('<b>Zdjęcia:</b> <br / ><br / ><a href="grafika/zdjecia_raporty_suszenia/%s" data-lightbox="zdjecie z raportu" data-title="Zdjecie z raportu suszenia"><img id="zdjecie_raportu" src="grafika/zdjecia_raporty_suszenia/%s"> </a><br / ><br / >',$Zdjecie,$Zdjecie);
							printf('<b>Opis:</b> %s<br / ><br / >',$Opis_zdjecia);
							}
							}
						}
					}else
						{

						if ($stmt = $mysqli -> prepare("SELECT Zdjecia,OpisZdjecia FROM `" . $asortyment_suszu. "` WHERE Data=? AND NrSuszarni=?"))
						{
					$stmt -> bind_param("ss",$data_raportu,$nr_suszarni);
					$stmt -> execute();
					$stmt -> bind_result($Zdjecie,$Opis_zdjecia);
					$stmt -> store_result();
					$stmt->data_seek(0);
						if ($stmt -> fetch()){
							if (empty($Zdjecie)) {echo "<b>Zdjęcia:</b> Brak zdjęć.<br / ><br / >";}
							else {
							printf('<b>Zdjęcia:</b> <br / ><br / ><a href="grafika/zdjecia_raporty_suszenia/%s" data-lightbox="zdjecie z raportu" data-title="Zdjecie z raportu suszenia"><img id="zdjecie_raportu" src="grafika/zdjecia_raporty_suszenia/%s"> </a><br / ><br / >',$Zdjecie,$Zdjecie);
							printf('<b>Opis:</b> %s<br / ><br / >',$Opis_zdjecia);
							}
							}
						}
						}


    					echo "<form method='post' action='raportpdf_suszenia_pokaz.php' target='_blank'><input type='submit' value='Pobierz raport PDF' name='pdf'></form>";

    					$stmt->close();
    					}
    					else {
						echo '<div class="alert alert-info"><span class="glyphicon glyphicon-info-sign"></span>&nbsp<strong>Info!</strong>&nbsp Brak danych w bazie danych</div>';
					}
    				}

    				else {
						echo '<div class="alert alert-danger"><span class="glyphicon glyphicon-thumb-down"></span>&nbsp<strong>Info!</strong>&nbsp Błąd w zapytaniu do bazy danych.</div>';
					}

						$mysqli -> close();
				}
				}
				else {
				echo "<div class='alert alert-warning'><span class='glyphicon glyphicon-alert'></span>&nbsp<strong>Uwaga!</strong>&nbsp Podaj: Asortyment,Nr Suszarni i Datę lub zaznacz opcję 'Ostatni' .</div>";
					}
			}

//Wczutyjemy zakres dat raportów,obliczmay liczbę dni produkcyjnych oraz sumujemy wyprodukowany susz  dla podanego asortymentu
if (isset($_POST['produkcja_asortymentu']))
{

//odbieramy dane
$asortyment_suszu = filtruj($_POST['asortyment_suszu']);
$nr_suszarni = filtruj($_POST['nr_suszarni']);
$rok=filtruj($_POST['rok']);

	/* Łączymy się z serwerem */
		require_once ('polaczenie_z_baza.php');

		if (mysqli_connect_errno()) {

			printf("<div class='alert alert-danger'><span class='glyphicon glyphicon-thumbs-down'></span>&nbsp<strong>Uwaga!</strong>&nbspBrak połączenia z serwerem MySQL. Kod błędu: %s\n</div>", mysqli_connect_error());

		}
		else
		{
			$rok = $mysqli -> real_escape_string($rok);
			$asortyment_suszu = $mysqli -> real_escape_string($asortyment_suszu);
			$nr_suszarni = $mysqli -> real_escape_string($nr_suszarni);

			$zapytanie1="";
			$zapytanie2="";
			
			$Suma_Wilgotnosc="";
			$Ilosc_pomiarow="";
			$Srednia_Wilgotnosc="";
			$precision="";
			
			if ($stmt = $mysqli -> prepare("SELECT SUM(Wilgotnosc) FROM `" . $asortyment_suszu. "` WHERE NrSuszarni=? AND Data LIKE '%" . $rok . "%' AND Wilgotnosc > 0"))
						{ 

					$stmt -> bind_param("s",$nr_suszarni);
					$stmt -> execute();
					$stmt -> bind_result($Sum_Wilg);
					$stmt -> store_result();
					$stmt->data_seek(0);
						if ($stmt -> fetch()){
							$Suma_Wilgotnosc=$Sum_Wilg;
							}

						if ($stmt = $mysqli -> prepare("SELECT COUNT(Wilgotnosc) FROM `" . $asortyment_suszu . "` WHERE NrSuszarni=? AND Data LIKE '%" . $rok . "%' AND Wilgotnosc > 0 "))
						{ 
					$stmt -> bind_param("s", $nr_suszarni);
					$stmt -> execute();
					$stmt -> bind_result($Ilosc_pom);
					$stmt -> store_result();
					$stmt->data_seek(0);
						if ($stmt -> fetch()){
							$Ilosc_pomiarow=$Ilosc_pom;

							}
						}
						
						$Srednia_Wilgotnosc=($Suma_Wilgotnosc/$Ilosc_pomiarow);
						$Srednia_Wilgotnosc=round($Srednia_Wilgotnosc,$precision=2);
						

						}


			if ($stmt = $mysqli -> prepare("SELECT Data,MIN(Data),MAX(Data),COUNT(DISTINCT Data),SUM(CalkowitaIloscSuszu),Dostawca FROM `" . $asortyment_suszu . "` WHERE NrSuszarni=? AND Data LIKE '%" . $rok . "%'"))
						{
							$stmt->bind_param("s",$nr_suszarni);
							$stmt -> execute();
							$stmt -> bind_result($data,$pierwsza_data,$ostatnia_data,$liczba_dni,$ilosc_suszu,$dostawca);
							$stmt -> store_result();

							if ($stmt->num_rows > 0)
							{
								$zapytanie1=TRUE;

								$stmt->data_seek(0);
								if ($stmt->fetch())
								{
									if (isset($pierwsza_data) || isset($ostatnia_data))
									{

								echo '<div class="alert alert-success alert-dismissable fade in">
										<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
										<span class="glyphicon glyphicon-thumbs-up"></span>&nbsp;<strong>Sukces!</strong>&nbsp Odczytano dane. Poniżej znajduje się twój raport. </div><br / >';

								$wydajnosc=$ilosc_suszu/$liczba_dni;
								$precision = "";
								$wydajnosc=round($wydajnosc,$precision=0);//wydajność na dobę
								$wydajnosc2=$wydajnosc/24;
								$wydajnosc2=round($wydajnosc2,$precision=0);//wydajność na h
								

								//Zmienne do raportu pdf
								$_SESSION['rok'] = $rok;
								$_SESSION['asortyment'] = $asortyment_suszu;
								$_SESSION['nr_suszarni'] = $nr_suszarni;
								$_SESSION['pierwsza_data'] = $pierwsza_data;
								$_SESSION['ostatnia_data'] = $ostatnia_data;
								$_SESSION['liczba_dni'] = $liczba_dni;
								$_SESSION['ilosc_suszu'] = $ilosc_suszu;
								$_SESSION['sr_wilg'] =$Srednia_Wilgotnosc;
								$_SESSION['wydajnosc'] =$wydajnosc ;
								$_SESSION['wydajnosc_h'] = $wydajnosc2;

								echo "<div class='row'><div class='col-sm-8'><h4><b>Podsumowanie produkcji:</b><br /></h4></div></div>
									<div class='row'> <div class='col-sm-4'><h4><b>Asortyment:<b></h4> $asortyment_suszu</div> <div class='col-sm-4'><h4><b>Rok:</b> $rok</h4></div> <div class='col-sm-4'><h4><b>Nr Suszarni:</b> $nr_suszarni </h4></div> </div>
									<hr></hr>
									<div class='row'> <div class='col-sm-4'><b>Pierwsza data: </b> $pierwsza_data </div> <div class='col-sm-4'><b>Ostatnia data:</b> $ostatnia_data</div> <div class='col-sm-4'><b>Liczba dni produkcyjnych: </b> $liczba_dni </div> </div><br / >
									<div class='row'> <div class='col-sm-4'><b>Ilość towaru: </b>$ilosc_suszu kg</div> <div class='col-sm-4'><b>Wydajność: </b>$wydajnosc kg/24h  $wydajnosc2 kg/h</div> <div class='col-sm-4'><b>Średnia Wilgotność:</b> $Srednia_Wilgotnosc %</div><br / ><br / > </div>";

									}else
										{
										echo '<div class="alert alert-info"><strong>Info!</strong>&nbsp Brak raportów dla podanego asortymentu.</div>';
										}

								}

							}else
									{
									echo '<div class="alert alert-info"><strong>Info!</strong>&nbsp Brak raportów dla podanego asortymentu.</div>';
									}


						}

						if ($stmt = $mysqli -> prepare("SELECT DISTINCT Data,Dostawca FROM `" . $asortyment_suszu . "` WHERE NrSuszarni=? AND Data LIKE '%" . $rok . "%' HAVING Dostawca > 0"))
						{
							$stmt->bind_param("s",$nr_suszarni);
							$stmt -> execute();
							$stmt -> bind_result($data,$dostawca);
							$stmt -> store_result();

							if ($stmt->num_rows > 0)
							{
								$zapytanie2 = TRUE;
								echo "<hr><br / ><div class='row'><div class='col-sm-4'><b>Data</b>:</div><div class='col-sm-8'><b>Dostawca:</b></div></div><br / >";
								$stmt->data_seek(0);
									while ($stmt->fetch()) {
										echo "<div class='row'>
										 		<div class='col-sm-4'>$data</div><div class='col-sm-8'>$dostawca</div>

										 	</div><br / >";
									}


							}

						}

				if ($zapytanie1 && $zapytanie2) {

					echo "<hr><form method='post' action='raportpdf_podsumowanie_suszenia_pokaz.php' target='_blank'><input type='submit' value='Pobierz raport PDF' name='pdf'></form><br / >";
					echo "<form method='post' action='raportpdf_podsumowanie_suszenia_pokaz.php' target='_blank'>
										<label>Email</label>
									<div class='row'><div class='col-sm-4'>
									<input class='form-control' type='email' name='email' maxlength='50' required/>
									</div></div>
									<br / >
										<input type='submit' value='Wyślij raport PDF' name='wyslij'>
									</fieldset></form>";

				}
		}


}

//Tworzymy raport podsumowania całej produkcji
if ($_POST['cala_produkcja']) 
{
	
	$rok=filtruj($_POST['rok']);
	$_SESSION['rok']=$rok;
	$Calkowita_ilosc_suszu="";

	/* Łączymy się z serwerem */
	require_once ('polaczenie_z_baza.php');

		if (mysqli_connect_errno()) {

			printf("<div class='alert alert-danger'><span class='glyphicon glyphicon-thumbs-down'></span>&nbsp<strong>Uwaga!</strong>&nbspBrak połączenia z serwerem MySQL. Kod błędu: %s\n</div>", mysqli_connect_error());

		}
		else
		{
				 $Asortyment_wbazie=array();
				 $Zestawienie_suszu=array();
				 $Zestawienie_wilgotnosci=array();
				 	
				//Robimy liste asortymentu. Zapytanie do bazy o obecny asortyment 
					if ($stmt = $mysqli -> prepare("SELECT Asortyment FROM AsortymentSuszu WHERE Asortyment NOT LIKE '%Arbuz%' "))
					{
							//echo "Zapytanie1 działa<br / >";
						$stmt -> execute();
						$stmt -> bind_result($Obecny_asortyment);
						$stmt -> store_result();
						
						if ($stmt->num_rows > 0) {
	
							/* Wyciągamy dane z zapytania sql i zapisujemy do tablicy  */
		    				while ($stmt->fetch()) {
							static $i=0;
							$Asortyment_wbazie[$i]=$Obecny_asortyment;
							$i++;
		    				}
	    				}		
					}
				
				//Pobieramy ilosc suszu dla każdego asortymentu
				//Dla jednej suszarni bo całkowitą ilość suszu wpisujemy do każdej suszarni na której idzie dany asortyment 
				foreach ($Asortyment_wbazie as $key => $asortyment) 
				{
					
				if ($stmt = $mysqli -> prepare("SELECT SUM(CalkowitaIloscSuszu) FROM `" .$asortyment. "` WHERE NrSuszarni=(SELECT MIN(NrSuszarni) FROM  `" .$asortyment. "`) AND Data LIKE '%" . $rok . "%' "))
						{ 
					$stmt -> execute();
					$stmt -> bind_result($Suma_suszu);
					$stmt -> store_result();
					$stmt->data_seek(0);
						if ($stmt -> fetch())
							{
							$Zestawienie_suszu[$asortyment]=$Suma_suszu;								
							}
						}
				}
				
				//Obliczamy całkowitą ilość suszu
				foreach ($Zestawienie_suszu as $asortyment => $ilosc_suszu) {
						$Calkowita_ilosc_suszu=$Calkowita_ilosc_suszu+$ilosc_suszu;
				}
				
				//Pobieramy średnią wartość wilgotność dla każdego asortymentu
					foreach ($Asortyment_wbazie as $key => $asortyment) 
				{
					
				if ($stmt = $mysqli -> prepare("SELECT AVG(Wilgotnosc) FROM `" .$asortyment. "` WHERE Wilgotnosc > 0 AND Data LIKE '%" . $rok . "%' "))
						{ 
					$stmt -> execute();
					$stmt -> bind_result($Sr_wilg);
					$stmt -> store_result();
					$stmt->data_seek(0);
						if ($stmt -> fetch())
							{
							$Sr_wilg=round($Sr_wilg,2);	
							$Zestawienie_wilgotnosci[$asortyment]=$Sr_wilg;								
							}
						}
				}
				
										
				 if ($Calkowita_ilosc_suszu == 0) {
					 
					 echo '<div class="alert alert-info"><span class="glyphicon glyphicon-info-sign"></span>&nbsp<strong>Info!</strong>&nbsp Brak danych w bazie danych</div>';
				 } else{
				 			echo '<div class="alert alert-success alert-dismissable fade in">
								<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
								<span class="glyphicon glyphicon-thumbs-up"></span>&nbsp;<strong>Sukces!</strong>&nbsp Odczytano dane. Poniżej znajduje się twój raport. </div><br / >';
							
							//Raport ilości suszu z całego roku dla wszystkich asortymentów
							echo"
							<div class='row'><div class='col-sm-8'><h3><b>Podsumowanie produkcji rok: $rok</b><br /></h3></div></div>
							<div class='row'><div class='col-sm-4'><h4><b>Asortyment:<b></h4></div> <div class='col-sm-4'><h4><b>Ilość suszu:</b></h4></div> <div class='col-sm-4'><h4><b>Średnia wilgotność:</b></h4></div> </div><hr>
							";
							
							foreach ($Zestawienie_suszu as $asortyment => $ilosc_suszu) 	
							{
								
								echo "<div class='row'> <div class='col-sm-4'>$asortyment</div> <div class='col-sm-4'> $ilosc_suszu kg</div> <div class='col-sm-4'> $Zestawienie_wilgotnosci[$asortyment] %</div></div><br / >";
								
							}
							
							echo "<hr><div class='row'><div class='col-sm-8'><h4><b>Całkowita ilość suszu: $Calkowita_ilosc_suszu kg</b><br /></h4></div></div>";
							
							echo "<hr><form method='post' action='raportpdf_roczne_podsumowanie_suszenia_pokaz.php' target='_blank'><input type='submit' value='Pobierz raport PDF' name='pdf'></form><br / >";
							echo "<form method='post' action='raportpdf_roczne_podsumowanie_suszenia_pokaz.php' target='_blank'>
													<label>Email</label>
												<div class='row'><div class='col-sm-4'>
												<input class='form-control' type='email' name='email' maxlength='50' required/>
												</div></div>
												<br / >
													<input type='submit' value='Wyślij raport PDF' name='wyslij'>
												</fieldset></form>";
						}
				
		}
	
}

?>
			<br / >
			<br / >

<?php
//require_once ('raportpdf_podsumowanie_suszenia.php');
?>
<?php
unlink("dane.json");
unlink("dane2.json");
?>
<h1>Raport z procesu suszenia</h1>
<ul class="nav nav-tabs">
	<li>
		<a href="index2.php?raporty_suszenia=1&reset=1">Tworzenie</a>
	</li>
	<li >
		<a href="index2.php?raporty_suszenia_odczyt=1">Odczyt</a>
	</li>
	<li>
		<a href="index2.php?raporty_suszenia_pobierz=1">Pobór</a>
	</li>
	<li class="active">
		<a href="index2.php?statystyki_suszenia=1">Wykresy</a>
	</li>
</ul>
<br / >
<legend>Wizualizacja danych</legend>
<br / >

<div id="formularz">
		<div class="row" >
			<div class="form-group">
<form class="form_loguj" method="POST" action="index2.php?statystyki_suszenia=1">

				<fieldset>
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

					foreach ($Asortyment_wbazie as $key => $value) {

						printf("<option value='%s'>%s</option>",$value,$value);
					}

				}
?>
					</select>
					</div>

					<div class="col-sm-4">
					<label >Data</label>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp Ostatni<input type="checkbox" name="ostatni_raport" value="Ostatni_raport"/>
					<input class="form-control" type="date" name="data_raportu" value="rrrr-mm-dd" />
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

				</div>
				<div class="row">
					<div class="col-sm-4">
					<label >Rok</label>
					<input class="form-control"  type="number" min="1900" max="2099" step="1" value="<?php echo date("Y"); ?>" name="rok" />
					</div>

				</div>
					<hr></hr>

					<span class="glyphicon glyphicon-export"></span>&nbsp<input type="submit" value="Pobierz wykres" name="wczytaj"><br / ><br / >
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

if (isset($_POST['wczytaj'])) {
	if ((isset($_POST['data_raportu']) && $_POST['data_raportu'] !=0) || isset($_POST['ostatni_raport'])) {
		
//Odbieramy dane o numerze raportu
$asortyment=filtruj($_POST['asortyment_suszu']);
$data=filtruj($_POST['data_raportu']);
$kolejny_dzien = date('Y-m-d', strtotime($data. ' +1 day'));
$nr_suszarni=filtruj($_POST['nr_suszarni']);
$rok=$_POST['rok'];
$zapytanie="";

/* Łączymy się z serwerem */
require_once ('polaczenie_z_baza.php');

if (mysqli_connect_errno()) {

			printf("<div class='alert alert-danger'><span class='glyphicon glyphicon-thumb-down'></span>&nbsp<strong>Uwaga!</strong>&nbspBrak połączenia z serwerem MySQL. Kod błędu: %s\n</div>", mysqli_connect_error());

		} else
		{

		$asortyment= $mysqli -> real_escape_string($asortyment);
		$nr_suszarni = $mysqli -> real_escape_string($nr_suszarni);
		$data= $mysqli -> real_escape_string($data);
		$rok= $mysqli -> real_escape_string($rok);


		//Zapytanie do wykresu o wilgotności vs czas
		if (isset($_POST['ostatni_raport']))
		{
						if ($stmt = $mysqli -> prepare("SELECT Czas FROM `" . $asortyment . "`  WHERE Data=(SELECT MAX(Data) FROM `" . $asortyment. "`) AND Czas >=  STR_TO_DATE('08:00:00','%h:%i:%s') AND NrSuszarni=?"))
							{
							/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
								$stmt -> bind_param("s", $nr_suszarni);
								$stmt->execute();
								$stmt->store_result();

								if ($stmt->num_rows > 0) {


									if ($stmt = $mysqli -> prepare("SELECT Data,Czas,PredkoscBlanszownika,TemperaturaBlanszownika,PredkoscSiatkiNr7,PredkoscSiatkiNr6,PredkoscSiatkiNr5,PredkoscSiatkiNr4,PredkoscSiatkiNr3,PredkoscSiatkiNr2,PredkoscSiatkiNr1,CzasSuszenia,TemperaturaGora,TemperaturaDol,Wilgotnosc,WykonawcaPomiaru FROM `" . $asortyment . "`  WHERE Data=(SELECT MAX(Data) FROM `" . $asortyment . "`) AND Czas >=  STR_TO_DATE('08:00:00','%h:%i:%s') AND NrSuszarni=?
									UNION ALL
									SELECT Data,Czas,PredkoscBlanszownika,TemperaturaBlanszownika,PredkoscSiatkiNr7,PredkoscSiatkiNr6,PredkoscSiatkiNr5,PredkoscSiatkiNr4,PredkoscSiatkiNr3,PredkoscSiatkiNr2,PredkoscSiatkiNr1,CzasSuszenia,TemperaturaGora,TemperaturaDol,Wilgotnosc,WykonawcaPomiaru FROM `".$asortyment."` WHERE Data=DATE_ADD((SELECT MAX(Data) FROM `" . $asortyment . "`),INTERVAL 1 DAY) AND Czas <=  STR_TO_DATE('06:00:00','%h:%i:%s') AND NrSuszarni=? ORDER BY Data, Czas ASC"
									))
									{
									/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
									$stmt -> bind_param("ss", $nr_suszarni,$nr_suszarni);
									$zapytanie=TRUE;
									}
							}else{

								if ($stmt = $mysqli -> prepare("SELECT Data,Czas,PredkoscBlanszownika,TemperaturaBlanszownika,PredkoscSiatkiNr7,PredkoscSiatkiNr6,PredkoscSiatkiNr5,PredkoscSiatkiNr4,PredkoscSiatkiNr3,PredkoscSiatkiNr2,PredkoscSiatkiNr1,CzasSuszenia,TemperaturaGora,TemperaturaDol,Wilgotnosc,WykonawcaPomiaru FROM `" . $asortyment . "`  WHERE Data=DATE_ADD((SELECT MAX(Data) FROM `" . $asortyment . "`),INTERVAL -1 DAY) AND Czas >=  STR_TO_DATE('08:00:00','%h:%i:%s') AND NrSuszarni=?
									UNION ALL
									SELECT Data,Czas,PredkoscBlanszownika,TemperaturaBlanszownika,PredkoscSiatkiNr7,PredkoscSiatkiNr6,PredkoscSiatkiNr5,PredkoscSiatkiNr4,PredkoscSiatkiNr3,PredkoscSiatkiNr2,PredkoscSiatkiNr1,CzasSuszenia,TemperaturaGora,TemperaturaDol,Wilgotnosc,WykonawcaPomiaru FROM `".$asortyment ."` WHERE Data=(SELECT MAX(Data) FROM `" . $asortyment . "`) AND Czas <=  STR_TO_DATE('06:00:00','%h:%i:%s') AND NrSuszarni=? ORDER BY Data, Czas ASC"
									))
									{
									/*Przypisujemy zmienne do znaczników ? w zapytaniu sql*/
									$stmt -> bind_param("ss", $nr_suszarni,$nr_suszarni);
									$zapytanie=TRUE;
									}


								}

						}
					}
	else {
		 		if ($stmt = $mysqli -> prepare("SELECT Data,Czas,PredkoscBlanszownika,TemperaturaBlanszownika,PredkoscSiatkiNr7,PredkoscSiatkiNr6,PredkoscSiatkiNr5,PredkoscSiatkiNr4,PredkoscSiatkiNr3,PredkoscSiatkiNr2,PredkoscSiatkiNr1,CzasSuszenia,TemperaturaGora,TemperaturaDol,Wilgotnosc,WykonawcaPomiaru FROM `".$asortyment."` WHERE Data=? AND Czas >=  STR_TO_DATE('08:00:00','%h:%i:%s') AND NrSuszarni=?
					UNION ALL
					SELECT Data,Czas,PredkoscBlanszownika,TemperaturaBlanszownika,PredkoscSiatkiNr7,PredkoscSiatkiNr6,PredkoscSiatkiNr5,PredkoscSiatkiNr4,PredkoscSiatkiNr3,PredkoscSiatkiNr2,PredkoscSiatkiNr1,CzasSuszenia,TemperaturaGora,TemperaturaDol,Wilgotnosc,WykonawcaPomiaru FROM `".$asortyment."` WHERE Data=? AND Czas <=  STR_TO_DATE('06:00:00','%h:%i:%s') AND NrSuszarni=? ORDER BY Data, Czas ASC"
					))
					{

					$stmt->bind_param("ssss",$data,$nr_suszarni,$kolejny_dzien,$nr_suszarni);
					$zapytanie=TRUE;
						}
			}
		
		
		if ($zapytanie) {
			
		$stmt -> execute();
		$stmt->bind_result($Data,$Godzina,$Predkosc_Blanszownika,$Temperatura_Blanszownika,$V_Siatka7,$V_Siatka6,$V_Siatka5,$V_Siatka4,$V_Siatka3,$V_Siatka2,$V_Siatka1,$Czas_Suszenia,$Temp_Gorna,$Temp_Dolna,$Wilgotnosc,$Odpowiedzialny);

		$stmt -> store_result();

		echo '<br / ><div class="alert alert-success alert-dismissable fade in">
			<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
			<span class="glyphicon glyphicon-thumb-up"></span>&nbsp<strong>Sukces!</strong>&nbsp To twój wynik zapytania: </div><br / >';

		$rows = array();
  		$table = array();
  		$table['cols'] = array(

    // Labels for your chart, these represent the column titles.
    /*
        note that one column is in "string" format and another one is in "number" format
        as pie chart only required "numbers" for calculating percentage
        and string will be used for Slice title
    */

    array('label' => 'Godzina', 'type' => 'string'),
    array('label' => 'Wilgotność', 'type' => 'number'),
	array('label' => 'Temperatura Górna', 'type' => 'number'),
	array('label' => 'Temperatura Dolna', 'type' => 'number'),
		);
    /* Wyciągamy dane z zapytania sql */
    $stmt->data_seek(0);
    while ($stmt->fetch()) {
      $temp = array();
	//Tworzenie lini czasu
	//Godzine ograniczamy do 5 znaków aby pozbyć się wyświetlania sekund
	  $Czas=$Data .$Godzina = substr($Godzina, 0, 5);

	/*Całą datę z godziną dzielimy na dwie zmienne Dzien i Godzina a nastepnie
	 * przypisujemy je do jednej zmiennej i odzielamy znakiem spacji aby na wykresie
	 * nie prylegały do siebie*/
	  $Dzien=substr($Czas, 0, 10);
	  $Godzina=substr($Godzina, -5, 5);
	  $Czas="$Godzina $Dzien";
      // Tworzymy osi x wykresu
      $temp[] = array('v' => (string) $Czas );

      // Wartości każdej lini w wykresie
      $temp[] = array('v' => (float) $Wilgotnosc);
	  $temp[] = array('v' => (float) $Temp_Gorna);
	  $temp[] = array('v' => (float) $Temp_Dolna);
      $rows[] = array('c' => $temp);
    }

$table['rows'] = $rows;

// Konwersja danych do formatu JSON
$jsonTable = json_encode($table);
//echo "$jsonTable";
$dane = fopen("dane.json", "w") or die("Unable to open file!");
fwrite($dane, $jsonTable);
fclose($dane);

$stmt->close();
}
	else {
	echo '<br / ><div class="alert alert-info"><span class="glyphicon glyphicon-info-sign"></span>&nbsp<strong>Info!</strong>&nbsp Brak danych w bazie danych</div><br / >';
		}

//Zapytanie do wykres o średnie wartości: wilgotności i temperatur
	if ($stmt = $mysqli -> prepare("SELECT AVG(Wilgotnosc), AVG(TemperaturaGora), AVG(TemperaturaDol) FROM `" . $asortyment . "` WHERE Data=? "))
		{
			$stmt -> bind_param("s",$data);
			$stmt -> execute();
			$stmt -> bind_result($Sr_wilg,$Sr_temp_gora,$Sr_temp_dol);
			$stmt -> store_result();
			$stmt->data_seek(0);
			if ($stmt -> fetch()){
				$precision="";
				$Sr_wilg=round($Sr_wilg,$precision=2);
				$_SESSION['sr_wilg'] = $Sr_wilg;
				
				$Sr_temp_gora=round($Sr_temp_gora,$precision=0);
				$_SESSION['sr_temp_gora'] = $Sr_temp_gora;
				
				$Sr_temp_dol=round($Sr_temp_dol,$precision=0);
				$_SESSION['sr_temp_dol'] = $Sr_temp_dol;
				
				
				}

		$stmt->close();
		}

}
$mysqli -> close();

if ($_POST['wczytaj'] && (!$data==null || isset($_POST['ostatni_raport']))) {

printf("<b>Asortyment: %s &nbsp&nbsp&nbsp&nbsp Data raportu: %s</b><br / >",$asortyment,$data);
  echo '
<div class="row" >

	<div class="row" >
  		<div class="col-sm-12">
    		<!--Div that will hold the line chart-->
    		<div id="chart_div" style="width:100%;height: 350px;"></div>
    	</div>
  	</div>
    <br / >
    <br / >
  
</div>
    ';
	
	/*<div class="row">
    	<div class="col-sm-8">
   			 <!--Div that will hold the gauge chart-->
  			 <p><b>Średnie wartości wilgotności oraz temperatur.</b></p>
    		<div id="chart_div2" style="width:100%;height: 300px;"></div>
    	</div>
    </div>*/

//echo "<form method='post' action='wykresypdf_sterylizacji.php' target='_blank'><input type='submit'  value='Pobierz raport PDF' name='wykresy_pdf'  onclick='wykres()' ></form><br / ><br / >";



}

}else {
	echo "<div class='alert alert-warning'><strong>Uwaga!</strong>&nbsp Podaj: Datę raportu lub zaznacz opcję 'Ostatni' .</div>";
}

}

?>

<script type="text/javascript">

    // Load the Visualization API and the package.
    google.charts.load('current', {'packages':['corechart','gauge']});

    // Set a callback to run when the Google Visualization API is loaded.
    google.charts.setOnLoadCallback(drawLineChart);
   	google.charts.setOnLoadCallback(drawChart);
    // Set a callback to run when the Google Visualization API is loaded.
    google.charts.setOnLoadCallback(drawPieChart);
	// Set a callback to run when the Google Visualization API is loaded.
    google.charts.setOnLoadCallback(drawGaugeChart);
    // Set a callback to run when the Google Visualization API is loaded.
    google.charts.setOnLoadCallback(drawGaugeChart2);

	var imgUri;

    function drawLineChart() {

      var jsonData = $.ajax({
          url: "dane.json",
         dataType: "json",
         async: false
         }).responseText;

          var options = {
		'title':'Wilgotność suszu',
		legend: {
          	position: 'bottom'
          },

         	hAxis: {
          title: 'Data - Godzina',
          textStyle: {
            color: '#1a237e',
            fontSize: 12,
            bold: true
          },
          titleTextStyle: {
            color: '#1a237e',
            fontSize: 14,
            bold: true
          }
        },
        
        series: {
          0: {targetAxisIndex: 0, maxValue: 20},
          1: {targetAxisIndex: 1, maxValue: 100},
          2: {targetAxisIndex: 1, maxValue: 100}
         
        },
        
       vAxes: {
        0: {
          title: 'Wilgotność %',
          textStyle: {
            color: '#1a237e',
            fontSize: 12,
            bold: true
          },
          titleTextStyle: {
            color: '#1a237e',
            fontSize: 14,
            bold: true
          }

        },
        
         1: {
          title: 'Temperatura *C',
          textStyle: {
            color: '#1a237e',
            fontSize: 12,
            bold: true
          },
          titleTextStyle: {
            color: '#1a237e',
            fontSize: 14,
            bold: true
          }

        }
        
	},
	

};



      // Create our data table out of JSON data loaded from server.
     var data = new google.visualization.DataTable(jsonData);

      // Instantiate and draw our chart, passing in some options.
      var chart = new google.visualization.LineChart(document.getElementById('chart_div'));

	google.visualization.events.addListener(chart, 'ready', function () {
    var imgUri = chart.getImageURI();
    // do something with the image URI, like:
   // window.open(imgUri);


   });

      chart.draw(data,  options);
}

/*function wykres(var imgUri) {
  var xhttp;
  xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {

    }
  };
  xhttp.open("GET", "wykresypdf_sterylizacji.php?link="+str, true);
  xhttp.send();
}*/

   // Load the Visualization API and the package.
   // google.charts.load('current',  {'packages':['gauge']});
	// Set a callback to run when the Google Visualization API is loaded.
    //google.charts.setOnLoadCallback(drawGaugeChart);


//Wykres średnich wilgotności
function drawGaugeChart() {

        var data = google.visualization.arrayToDataTable([
          ['Label', 'Value'],
          ['Wilgotność', <?php echo "".$_SESSION['sr_wilg'].""; ?>]
        ]);

        var options = {
        	max: 20,min: 0,
          width: 400, height: 150,
          redFrom: 12, redTo: 20,
          yellowFrom:10, yellowTo: 12,
          minorTicks: 5
        };

        var chart = new google.visualization.Gauge(document.getElementById('chart_div2'));

        chart.draw(data, options);

      }

</script>
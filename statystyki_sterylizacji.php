<?php
unlink("dane.json");
unlink("dane2.json");
?>
<h1>Raport z procesu sterylizacji parowej</h1>
<ul class="nav nav-tabs">
	<li>
		<a href="index2.php?raporty_sterylizacja=1">Tworzenie</a>
	</li>
	<li >
		<a href="index2.php?raporty_sterylizacji_odczyt=1">Odczyt</a>
	</li>
	<li>
		<a href="index2.php?raporty_sterylizacji_pobierz=1">Pobór</a>
	</li>
	<li class="active">
		<a href="index2.php?statystyki_sterylizacji=1">Wykresy</a>
	</li>
</ul>
<br / >
<legend>Wizualizacja danych</legend>
<br / >

<div id="formularz">
		<div class="row" >
			<div class="form-group">
<form class="form_loguj" method="POST" action="index2.php?statystyki_sterylizacji=1">

		<div class="row">
					<div class="col-sm-4">
					<label >Asortyment</label>
						<select class="form-control" name="asortyment" required>
							<?php
							//Wyswietlamy wybór asortymentu dostępnego w bazie danych
			/* Łączymy się z serwerem */
			require_once ('polaczenie_z_baza.php');

			if (mysqli_connect_errno()) {

			printf("<div class='alert alert-danger'><span class='glyphicon glyphicon-thumb-down'></span>&nbsp<strong>Uwaga!</strong>&nbspBrak połączenia z serwerem MySQL. Kod błędu: %s\n</div>", mysqli_connect_error());

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
						<label>Nr raportu:</label> &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp Ostatni<input type="checkbox" name="ostatni_raport" value="Ostatni_raport"/><br / >
						<input class="form-control" type="text" name="nr_raportu" maxlength="12" />
					</div>
		</div>
		<br / >
		<span class="glyphicon glyphicon-export"></span>&nbsp;<input type="submit" value="Odczytaj" name="wczytaj">
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
	if (isset($_POST['nr_raportu']) || isset($_POST['ostatni_raport'])) {

		$_SESSION['sr_wil_pocz']="";
		$_SESSION['sr_wil_kon']="";
		$_SESSION['roznica'] ="";
//Odbieramy dane o numerze raportu
$nr_raportu=filtruj($_POST['nr_raportu']);
$asortyment=$_POST['asortyment'];
$asortyment_czysty=substr($asortyment,0,-7);//Usuwamy tekst '_Steryl' z końca nazwy asortymentu, który jest w bazie danych

/* Łączymy się z serwerem */
require_once ('polaczenie_z_baza.php');

if (mysqli_connect_errno()) {

			printf("<div class='alert alert-danger'><span class='glyphicon glyphicon-thumb-down'></span>&nbsp<strong>Uwaga!</strong>&nbspBrak połączenia z serwerem MySQL. Kod błędu: %s\n</div>", mysqli_connect_error());

		} else
		{


		$nr_raportu = $mysqli -> real_escape_string($nr_raportu);
		$rok=date("Y");

		if (isset($_POST['ostatni_raport']))
		{
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

		//Zapytanie do wykresu o wilgotności początkowej vs wilgotność końcowa
		if ($stmt = $mysqli -> prepare("SELECT Data,Godzina,WilgotnoscPoczatkowa,WilgotnoscKoncowa FROM `" . $asortyment . "` WHERE NrRaportu=? ORDER BY Data, Godzina ASC"))
		{

		$stmt -> bind_param("s",$nr_raportu);
		$stmt -> execute();
		$stmt -> bind_result($Data,$Godzina,$Wil_poczatek,$Wil_koniec);
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
    array('label' => 'Wilgotność Początkowa', 'type' => 'number'),
    array('label' => 'Wilgotność Końcowa', 'type' => 'number')

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
      $temp[] = array('v' => (float) $Wil_poczatek);
      $temp[] = array('v' => (float) $Wil_koniec);
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

//Zapytanie do wykres o ilość straty towaru
if ($stmt = $mysqli -> prepare("SELECT Odsiew,Metal,PartiaPoczatek,PartiaKoniec FROM `" . $asortyment . "` WHERE NrRaportu=? ORDER BY Data, Godzina ASC"))
{
		$stmt -> bind_param("s",$nr_raportu);
		$stmt -> execute();
		$stmt -> bind_result($Odsiew,$Metal,$Partia_poczatek,$Partia_koniec);
		$stmt -> store_result();
		$stmt->data_seek(0);

		if ($stmt -> fetch()) {

		$Reszta_straty = (($Partia_poczatek - $Partia_koniec)-($Odsiew+$Metal));

		$rows = array();
  		$table = array();
		//Tabela nazw nazw-wycinków na wykresie
		$naglowki = array('Odsiew','Metal','Inne');
		//Tabela wartości do wykresu
		$dane = array($Odsiew,$Metal,$Reszta_straty);

  		$table['cols'] = array(

    // Labels for your chart, these represent the column titles.
    /*
        note that one column is in "string" format and another one is in "number" format
        as pie chart only required "numbers" for calculating percentage
        and string will be used for Slice title
    */

     	array('label' => 'Towar', 'type' => 'string'),
        array('label' => 'Procenty', 'type' => 'number')

		);
    /* Tworzymy tabele w pliku typu JSON do wyswietlenia wykresu
	 * powtórz pętle tyle razy ile jest wycinków na wykresie*/
    for ($x=0; $x <3 ; $x++) {
      $temp = array();

      // Tworzymy wycinki wykresu PieChart
         $temp[] = array('v' => (string) $naglowki[$x]);


      // Wartości każdego kawałka wykresu PieChart
         $temp[] = array('v' => (int) $dane[$x]);

         $rows[] = array('c' => $temp);
    }

$table['rows'] = $rows;

// konwesrja danych do formatu JSON
$jsonTable = json_encode($table);
//echo "$jsonTable";
$dane = fopen("dane2.json", "w") or die("Unable to open file!");
fwrite($dane, $jsonTable);
fclose($dane);

}
$stmt->close();
}

	//Zapytanie do wykres o średnie wilgotności
	if ($stmt = $mysqli -> prepare("SELECT AVG(WilgotnoscPoczatkowa), AVG(WilgotnoscKoncowa) FROM `" . $asortyment . "` WHERE NrRaportu=? "))
		{
			$stmt -> bind_param("s",$nr_raportu);
			$stmt -> execute();
			$stmt -> bind_result($Sr_wil_pocz,$Sr_wil_kon);
			$stmt -> store_result();
			$stmt->data_seek(0);
			if ($stmt -> fetch()){
				$precision="";
				$Sr_wil_pocz=round($Sr_wil_pocz,$precision=2);
				$_SESSION['sr_wil_pocz'] = $Sr_wil_pocz;
				$Sr_wil_kon=round($Sr_wil_kon,$precision=2);
				$_SESSION['sr_wil_kon'] = $Sr_wil_kon;
				$roznica = $Sr_wil_kon - $Sr_wil_pocz;
				$_SESSION['roznica'] = $roznica;
				}

		$stmt->close();
		}
}
$mysqli -> close();

if ($_POST['wczytaj'] && (!$nr_raportu==null || isset($_POST['ostatni_raport']))) {

printf("<b>Asortyment: %s &nbsp&nbsp&nbsp&nbspNr raportu: %s</b><br / >",$asortyment_czysty,$nr_raportu);
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

	<div class="row" >
		<div class="col-sm-8">
   			 <!--Div that will hold the gauge chart-->
  			 <p><b>Średnie wartości wilgotności początkowej oraz końcowej.</b></p>
    		<div id="chart_div2" style="width:100%;height: 200px;"></div>
    	</div>
    	<div class="col-sm-4">
   			 <!--Div that will hold the gauge chart-->
   			 <p><b>Różnica średnich wilgotności.</b></p>
    		<div id="chart_div2_1" style="width:100%;height: 200px;"></div>
    	</div>
    </div>
    		<br / >
    		<br / >
    <div class="row" >
    	<div class="col-sm-8">
    		<!--Div that will hold the pie chart-->
    		<div id="chart_div3" style="width:100%;height: 350px;"></div>
    	</div>
   </div>
    <br / >
    <br / >
</div>
    ';

//echo "<form method='post' action='wykresypdf_sterylizacji.php' target='_blank'><input type='submit'  value='Pobierz raport PDF' name='wykresy_pdf'  onclick='wykres()' ></form><br / ><br / >";



}

}else {
	echo "<div class='alert alert-warning'><strong>Uwaga!</strong>&nbsp Podaj: Nr Raportu lub zaznacz opcję 'Ostatni' .</div>";
}


}


?>

  <script type="text/javascript">

    // Load the Visualization API and the package.
    google.charts.load('current', {'packages':['corechart','gauge']});

    // Set a callback to run when the Google Visualization API is loaded.
    google.charts.setOnLoadCallback(drawLineChart);
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
		'title':'Wilgotność Początkowa VS Wilgotność Końcowa',
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
        vAxis: {
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

        }

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
          ['Początkowa', <?php echo "".$_SESSION['sr_wil_pocz'].""; ?>],
          ['Końcowa', <?php echo "".$_SESSION['sr_wil_kon'].""; ?>]
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

//Wykres róznicy średnich wilgotności
 function drawGaugeChart2() {

        var data = google.visualization.arrayToDataTable([
          ['Label', 'Value'],
          ['Różnica', <?php echo "".$_SESSION['roznica'].""; ?>]
        ]);

        var options = {
        	max: 2,min: -2,
          width: 400, height: 150,
          redFrom: 1, redTo: 2,
          yellowFrom:0.8, yellowTo: 1,
          minorTicks:10
        };

        var chart = new google.visualization.Gauge(document.getElementById('chart_div2_1'));

        chart.draw(data, options);

      }


    function drawPieChart() {

      var jsonData = $.ajax({
          url: "dane2.json",
         dataType: "json",
         async: false
         }).responseText;

         var options = {
		title:'Straty Towaru w %',
		pieHole: 0.4,
			slices: {
            0: { color: 'red' },
            1: { color: 'blue' },
            2: { color: 'orange' }
          }

          };



      // Create our data table out of JSON data loaded from server.
     var data = new google.visualization.DataTable(jsonData);

      // Instantiate and draw our chart, passing in some options.
      var chart = new google.visualization.PieChart(document.getElementById('chart_div3'));
      chart.draw(data,  options);

    }



</script>
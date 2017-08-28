<h1>Raport z procesu suszenia</h1>
     <ul class="nav nav-tabs">
  <li><a href="index2.php?raporty_suszenia=1">Tworzenie</a></li>
  <li><a href="index2.php?raporty_suszenia_odczyt=1">Odczyt</a></li>
  <li class="active"><a href="index2.php?raporty_suszenia_pobierz=1">Pobór</a></li>
 <li><a href="index2.php?statystyki_suszenia=1">Wykresy</a></li>
</ul>
<br / >

<div id="formularz">
		<div class="row" >
			<div class="form-group">
			<form method="post" action="raportpdf_suszenia_pokaz.php" target="_blank">
				<fieldset>
					<legend>
					Pobierz raport
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
					<hr></hr>

					<span class="glyphicon glyphicon-export"></span>&nbsp;<input type="submit" value="Pobierz raport PDF" name="pdf2" >
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
     		<form method="post" action="index2.php?raporty_suszenia_pobierz=1" >
	<fieldset>
		<legend>
			Wyślij raport
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
		<br / >
		<div class="row">
			<div class="col-sm-4">
			<label>Email</label>
			<input class="form-control" type="email" name="email" maxlength="35" required/>
			</div>
		</div>

			<hr></hr>

			<span class="glyphicon glyphicon-send"></span>&nbsp;<input type="submit" value="Wyślij raport PDF" name="wyslij">
	</fieldset>
</form>
</div>
</div>
</div>

<br / ><br / >

<?php
require_once ('raportpdf_suszenia.php');
?>

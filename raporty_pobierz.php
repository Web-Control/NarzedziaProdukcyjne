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
  <li><a href="raporty_odczyt.php">Odczyt raportu</a></li>
  <li class="active"><a href="raporty_pobierz.php">Pobór raportu</a></li>

</ul>
<br / >
			<form method="post" action="raportpdf.php" target="_blank">
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
					<input type="submit" value="Pobierz raport PDF" name="pdf2" >
				</fieldset>
			</form>
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

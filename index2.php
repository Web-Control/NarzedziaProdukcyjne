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
		<?
		include ('header.php');
		?>

		<div class="container-fluid text-center">
			<div class="row content">
				<div class="col-sm-2 sidenav">
					<?php
					if ($_SESSION['zalogowany'] == 1) {
						if ($_GET['raporty_suszenia']==1||$_GET['raporty_suszenia_odczyt']==1||$_GET['raporty_suszenia_pobierz']==1||$_GET['raporty_sterylizacja']==1||$_GET['raporty_sterylizacji_odczyt']==1||$_GET['raporty_sterylizacji_pobierz']==1||$_GET['statystyki_sterylizacji']==1 ||$_GET['statystyki_suszenia']==1
						|| $_GET['kontrola_magnezow']==1 || $_GET['kontrola_magnezow_odczyt']==1 || $_GET['kontrola_magnezow_weryfikacja']==1) {
						include('boczne_menu.php');
						}
						if ($_GET['rejestracja']==1|| $_GET['usuwanie_plikow']==1|| $_GET['dodawanie_asortymentu']==1 || $_GET['dodawanie_lini']==1 || $_GET['kopia_zapasowa']==1) {
						include('boczne_menu2.php');
						}
					}
					?>
				</div>
				<div class="col-sm-8 text-left">
					<!--treść-->
					<?php
					if ($_SESSION['zalogowany'] == 1) {
						if ($_GET['powitanie']==1) {
						include 'powitanie.php';
						}
						if ($_GET['raporty_suszenia']==1) {
						include 'raporty_suszenia.php';
						}
						if ($_GET['kontrola_magnezow']==1) {
							include 'kontrola_magnezow.html';
							}
						if ($_GET['kontrola_magnezow_odczyt']==1) {
							include 'kontrola_magnezow_odczyt.html';
							}
						if ($_GET['kontrola_magnezow_weryfikacja']==1) {
							include 'kontrola_magnezow_weryfikacja.html';
							}
						if ($_GET['raporty_suszenia_odczyt']==1) {
						include 'raporty_suszenia_odczyt.php';
						}
						if ($_GET['raporty_suszenia_pobierz']==1) {
						include 'raporty_suszenia_pobierz.php';
						}
						if ($_GET['raporty_sterylizacja']==1) {
						include 'raporty_sterylizacja.php';
						}
						if ($_GET['raporty_sterylizacji_odczyt']==1) {
						include 'raporty_sterylizacji_odczyt.php';
						}
						if ($_GET['raporty_sterylizacji_pobierz']==1) {
						include 'raporty_sterylizacji_pobierz.php';
						}
						if ($_GET['statystyki_sterylizacji']==1) {
						include 'statystyki_sterylizacji.php';
						}
						if ($_GET['statystyki_suszenia']==1) {
						include 'statystyki_suszenia.php';
						}
						if ($_GET['kontakt']==1) {
						include 'kontakt.php';
						}

						if ($_GET['administracja']==1) {
						include 'rejestracja.php';
						}
						if ($_GET['rejestracja']==1) {
						include 'rejestracja.php';
						}
						if ($_GET['dodawanie_asortymentu']==1) {
						include 'dodawanie_asortymentu.php';
						}
						if ($_GET['dodawanie_lini']==1) {
						include 'dodawanie_lini.html';
						}

						if ($_GET['usuwanie_plikow']==1) {
						include 'usuwanie_plikow.php';
						}
						if ($_GET['kopia_zapasowa']==1) {
						include 'kopia_zapasowa.php';
						}
					}
					else{
						echo "<h2>Jeśli chcesz korzystać z aplikacji:</h2><a href='index.php'>Zaloguje się</a>";
					}
					?>
				</div>
				<div class="col-sm-2 sidenav">
					<? include ('boczne_dodatki.php'); ?>
				</div>
			</div>
		</div>

		<footer class="container-fluid text-center">
			<?
			include ('footer.php');
			?>
		</footer>
<script src="lightbox/src/js/lightbox.js"></script>
	</body>
</html>

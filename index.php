<?php
// Start the session
session_start();
ob_start()
?>
<!DOCTYPE html>
<html lang="pl">
	<head>
		<?
		include ('head.php');
		?>
	</head>
	<body>

		<div class="container-fluid text-center">
			<div class="row content">
				<div class="col-sm-2 sidenav">

				</div>
				<div class="col-sm-8 text-left">
					<img src="grafika/suszarnia_logo3.jpg" style="float:left;margin-right:30px;width:113px;height:110px;"/><h2 style="margin-top:64px;"><span style="font-size:1,5em;" class="glyphicon glyphicon-cog"></span>&nbsp Narzędzia produkcyjne online</h2>

					<br / >

	<div id="formularz">
		<div class="row" >
			<div class="form-group">
					<form class="form_loguj" method="POST" action="index.php">
					<div class="row">
						<div class="col-sm-4">
							<span class="glyphicon glyphicon-user"></span><label>Login:</label>
							<input class="form-control"  type="text" name="login" max="10" required>
							<br / >
							<span class="glyphicon glyphicon-lock"></span><label>Hasło:</label>
							<input class="form-control"  type="password" name="haslo" max="10" required>
							<!--<div class="g-recaptcha" data-sitekey="6LdFnBIUAAAAANfX1LSn0m7j0RhveRY0Fm4df8fQ"></div>-->

						</div>
					</div>
						<br / >


							<span class="glyphicon glyphicon-log-in"></span>&nbsp<input type="submit" value="Zaloguj" name="loguj">

					</form>
				</div>
		</div>
	</div>
					<?php
					if (isset($_GET['wyloguj']) == 1) {
						$_SESSION['zalogowany'] = 0;
						session_destroy();
					}

					function filtruj($zmienna) {
						$data = trim($zmienna);
						//usuwa spacje, tagi
						$data = stripslashes($zmienna);
						//usuwa slashe
						$data = htmlspecialchars($zmienna);
						//zamienia tagi html na czytelne znaki aby w formularzu nie wpisać szkodliwego kodu
						return $zmienna;
					}

					if (isset($_POST['loguj'])) {
						if (isset($_POST['login']) && isset($_POST['haslo'])) {

							$login = filtruj($_POST['login']);
							$haslo = filtruj($_POST['haslo']);
							$ip = filtruj($_SERVER['REMOTE_ADDR']);
							//$response = $_POST['g-recaptcha-response'];

							/*if (!isset($_POST['g-recaptcha-response'])) {
							 echo "<div class='alert alert-warning'><strong>Uwaga!</strong>&nbsp Zaznacz, że nie jesteś robotem!</div>";
							 }
							 $recaptcha_secret = "Y6LdFnBIUAAAAAOswPim3Gu62Vf7q_LwRXqO26k3b";
							 $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=" . $recaptcha_secret . "&response=" . $_POST['g-recaptcha-response']);
							 $response = json_decode($verify);
							 if ($response["success"] == true) {
							 echo "Logged In Successfully";
							 } else {
							 echo "You are a robot";
							 }*/

							/*
							 $secret = 'Y6LdFnBIUAAAAAOswPim3Gu62Vf7q_LwRXqO26k3b';
							 $url="https://www.google.com/recaptcha/api/siteverify?secret=". $secret ."&response=".$response."";
							 $verify=file_get_contents($url);
							 $captcha_success = json_decode($verify);
							 if ($captcha_success->success == false) {
							 echo "<div class='alert alert-warning'><strong>Uwaga!</strong>&nbsp Stwierdzono, że jesteś robotem! Spróbuj ponownie.</div>";
							 }*/

							//if ($captcha_success -> success == true) {
							//if ($response["success"] == true) {

							if (strlen($login) <= 20 && strlen($haslo) <= 20) {

								/* Łączymy się z serwerem */
								$mysqli = new mysqli('mysql530int.cp.az.pl', 'u6001900_szymon', 'mNa5YWLL', 'db6001900_RaportyWilgotnosci');

								/* Utworzenie zapytania */
								$login = $mysqli -> real_escape_string($login);
								$query = "SELECT Login,Haslo FROM Uzytkownicy WHERE Login = '" . $login . "'";
								/*Przesłanie zapytania do bazy*/
								$result = $mysqli -> query($query);

								while ($row = $result -> fetch_object()) {
									$_SESSION['login'] = $row -> Login;
									$_SESSION['hashed_haslo'] = $row -> Haslo;
								}

								/* sprawdzamy czy login i hasło są dobre*/
								$num_rows = mysqli_num_rows($result);
								if ($num_rows > 0 && $_SESSION['login'] == $login && password_verify($haslo, $_SESSION['hashed_haslo'])) {
									/*uaktualniamy date logowania oraz ip*/
									$query = "UPDATE Uzytkownicy SET Data logowania= '" . time() . "', ip = '" . $ip . "' WHERE Login = '" . $login . "';";
									/*Przesłanie zapytania do bazy*/
									$result = $mysqli -> query($query);
									$_SESSION['zalogowany'] = 1;
									$_SESSION['login'] = $login;
									// zalogowany
								} else {
									echo '<div class="alert alert-warning"><span class="glyphicon glyphicon-alert"></span>&nbsp<strong>Uwaga!</strong>&nbspWpisano złe dane!</div>';
								}
							} else {
								echo "<div class='alert alert-warning'><span class='glyphicon glyphicon-alert'></span>&nbsp<strong>Uwaga!</strong>&nbsp Podałeś zbyt długi Login lub Hasło.</div>";
							}
							//}
						} else {
							echo "<div class='alert alert-warning'><span class='glyphicon glyphicon-alert'></span>&nbsp<strong>Uwaga!</strong>&nbsp Podaj login i hasło.</div>";
						}

					}

					if ($_SESSION['zalogowany'] == true) {
						header('Location: http://www.web-control.pl/NarzedziaProdukcyjne/index2.php?powitanie=1');
						echo "Witaj <b>" . $_SESSION['login'] . "</b><br><br>";
						echo '<a href="index2.php?raporty_suszenia=1">Rozpocznij pracę</a><br / ><br / >';
						echo '<a href="?wyloguj=1">[Wyloguj]</a>';
						//header( 'refresh: 2; url=http://www.web-control.pl/RaportyWilgotnosci/index2.php' );
					}
					?>
					<br / >

				</div>
				<div class="col-sm-2 sidenav">
					<?
					include ('boczne_dodatki.php');
					?>
				</div>
			</div>
		</div>

		<footer class="container-fluid text-center">
			<p>
				Suszarnia Warzyw Jaworski
				<br / >
				wspierane przez web-control.pl
			</p>
		</footer>

	</body>
</html>
<?php
ob_end_flush()
?>
<h2>Rejestracja użytkownika</h2>

<div id="formularz">
		<div class="row" >
			<div class="form-group">
<form class="form_loguj" method="POST" action="index2.php?rejestracja=1.php">

		<div class="row">
					<div class="col-sm-4">
						<span class="glyphicon glyphicon-user"></span><label>Login:</label><br / >
						<input class="form-control"  type="text" name="login" max="10" required>

						<span class="glyphicon glyphicon-lock"></span><label>Hasło:</label><br / >
						<input class="form-control"  type="password" name="haslo1" max="10" required>

						<span class="glyphicon glyphicon-lock"></span><label>Powtórz Hasło:</label><br / >
						<input class="form-control"  type="password" name="haslo2" max="10" required>
					</div>
		</div>
		<br / >
		<span class="glyphicon glyphicon-pencil"></span>&nbsp<input type="submit" value="Rejestruj" name="rejestruj">
</form>
</div>
</div>
</div>
<br / >
<br / >
<?php
function filtruj($zmienna) {
							$data = trim($zmienna);//usuwa spacje, tagi
							$data = stripslashes($zmienna);//usuwa slashe
							$data = htmlspecialchars($zmienna);//zamienia tagi html na czytelne znaki aby w formularzu nie wpisać szkodliwego kodu
							return $zmienna;
						}

$login = filtruj($_POST['login']);
$haslo1 = filtruj($_POST['haslo1']);
$haslo2 = filtruj($_POST['haslo2']);

if ($_POST['rejestruj'] && isset($haslo1) && isset($haslo2)&&($_SESSION['login']=='Szymon Ch.')) {

	if ($haslo1 == $haslo2) {
		$hashed_haslo = password_hash($haslo2, PASSWORD_DEFAULT);

		/* Łączymy się z serwerem */
		$mysqli = new mysqli('mysql530int.cp.az.pl', 'u6001900_szymon', 'mNa5YWLL', 'db6001900_RaportyWilgotnosci');

		if (mysqli_connect_errno()) {
			printf("<div class='alert alert-danger'><span class='glyphicon glyphicon-thumb-down'></span>&nbsp<strong>Uwaga!</strong>&nbspBrak połączenia z serwerem MySQL. Kod błędu: %s\n</div>.", mysqli_connect_error());
		} else {
			/* Utworzenie zapytania */
			$query = "INSERT INTO Uzytkownicy (Login,Haslo) VALUES ('$login','$hashed_haslo')";//"INSERT INTO Uzytkownicy (Login,Haslo) VALUES ('$login','$hashed_haslo')";
			/*Przesłanie zapytania do bazy*/
			$result = $mysqli -> query($query);
			/*Sprawdzamy czy wpis do bazy został wykonany*/
			if (mysqli_affected_rows($mysqli) > 0) {
				$_SESSION['dokonano_rejestracji'] = 1;
			}
			if (mysqli_affected_rows($mysqli) == 0 || mysqli_affected_rows($mysqli) < 0) {

				echo "<div class='alert alert-warning'><span class='glyphicon glyphicon-alert'></span>&nbsp<strong>Uwaga!&nbsp Nie dokonano zapisu. Możliwy błąd zapytania.</strong></div>";
			}
			if ($_SESSION['dokonano_rejestracji']== 1) {
				echo '<div class="alert alert-success alert-dismissable fade in">
				  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
				  <span class="glyphicon glyphicon-thumb-up"></span>&nbsp<strong>Sukces!</strong>&nbsp Dokonano rejestracji użytkownika.</div><br / >';
			}

			$mysqli -> close();
			$_SESSION['dokonano_rejestracji'] = 0;
		}
	} else {echo "<div class='alert alert-warning'><span class='glyphicon glyphicon-alert'></span>&nbsp<strong>Uwaga!</strong>&nbsp Hasła nie pasują do siebie. Wpisz je ponownie.</div>";
	}

}
?>
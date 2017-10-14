<h1><span style="font-size:1,8em;" class="glyphicon glyphicon-cog"></span>&nbsp Narzędzia produkcyjne online</h1>
<br / >
<?php
if (isset($_SESSION['zalogowany']) == TRUE) {
	echo "<h2>Witaj &nbsp&nbsp<span style='font-size:1,8em;' class='glyphicon glyphicon-user'></span>&nbsp <b>" . $_SESSION['login'] . "</h2></b><br><br>";
} else {
	echo "<a href='index.php'>Zaloguj się</a>";
}
?>

<br / >

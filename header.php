<nav class="navbar navbar-inverse">
  <div class="container-fluid">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="#"><img src="grafika/suszarnia_logo.png" id="logo"/></a>
    </div>
    <div class="collapse navbar-collapse" id="myNavbar">
      <ul class="nav navbar-nav">
        <li <?php if ($_GET['powitanie']==1) {echo"class='active'";} ?>><a href="index2.php?powitanie=1">START</a></li>
<?php
        if ($_SESSION['zalogowany']==1) {


			if ($_GET['raporty_suszenia']==1||$_GET['raporty_suszenia_odczyt']==1||$_GET['raporty_suszenia_pobierz']==1||$_GET['raporty_sterylizacja']==1||$_GET['raporty_sterylizacji_odczyt']==1||$_GET['raporty_sterylizacji_pobierz']==1||$_GET['statystyki_steryl']==1) {
			$klasa="class='active'";
			}
			else {
			$klasa="class=''";
			}
			if ($_GET['statystyki_sterylizacji']==1) {
			$klasa="class='active'";
			}
			else {
			$klasa="class=''";
			}

			if ($_GET['rejestracja']==1||$_GET['usuwanie_plikow']==1 ||$_GET['dodawanie_asortymentu']==1 ) {
				$klasa1="class='active'";
			}

			if  ($_GET['kontakt']==1) {
				$klasa2="class='active'";
			}


       echo "<li ". $klasa ."><a href='index2.php?raporty_suszenia=1'>RAPORTY</a></li>
        <li ". $klasa2 ."><a href='index2.php?kontakt=1'>KONTAKT</a></li>";
		}
		if ($_SESSION['login']=='Chomej Sz.') {
			echo "<li ". $klasa1 ."><a href='index2.php?rejestracja=1'>ADMIN</a></li>";
		}
?>
      </ul>
<?php
        if (isset($_SESSION['login'])) {
         printf("<ul class='nav navbar-nav navbar-right'><li><a href='index.php?wyloguj=1'><span class='glyphicon glyphicon-user'></span>%s&nbsp&nbsp<span class='glyphicon glyphicon-log-out'></span> Wyloguj</a></li></ul>", $_SESSION['login']);
        };
?>
    </div>
  </div>
</nav>
<h2>Tworzenie kopii zapasowych</h2>
<div id="formularz">
		<div class="row" >
			<div class="form-group">
<form class="form_loguj" method="POST" action="index2.php?kopia_zapasowa=1.php">
	<fieldset>
					<legend>Kopia bazy danych</legend>
		<br / >
		<span class="glyphicon glyphicon-copy"></span>&nbsp<input type="submit" value="Zrób kopię" name="stworz_kopie_bazy">
	</fieldset>
</form>
</div>
</div>
</div>
<?php
require 'vendor/autoload.php';
use Ifsnop\Mysqldump as IMysqldump;

   if (isset($_POST['stworz_kopie_bazy']) )
   {
   	/*ob_end_clean();
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);*/

	$data=date("Y-m-d");
	$nazwa_pliku="Narzedzia_Produkcyjne_Online_kopia_bazy_danych_$data.sql";
	$sciezka_do_pliku="kopia_bazy/$nazwa_pliku";

		try {
		    $dump = new IMysqldump\Mysqldump('mysql:host=mysql530int.cp.az.pl;dbname=db6001900_RaportyWilgotnosci', 'u6001900_szymon', 'mNa5YWLL');
		    $dump->start("kopia_bazy/$nazwa_pliku");
		} catch (\Exception $e) {
		    echo 'mysqldump-php error: ' . $e->getMessage();
		}

			if (file_exists($sciezka_do_pliku)) {
				echo '<div class="alert alert-success alert-dismissable fade in">
					<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
				<span class="glyphicon glyphicon-thumbs-up"></span>&nbsp;<strong>Sukces!</strong>&nbsp Wykonano kopię zapasową bazy danych. Kopia znajduje się na serwerze w katalogu "kopia_bazy". </div><br / >';

				$zip = new ZipArchive;
				$kopia_zip = fopen("kopia_bazy/Narzedzia_Produkcyjne_Online_kopia_bazy_danych_$data.zip", "w");//Bez wcześniejszego utworzenia pliku ZipArchive nie działa
				$kopia_zip_sciezka="kopia_bazy/Narzedzia_Produkcyjne_Online_kopia_bazy_danych_$data.zip";
				$kopia_zip_nazwa="Narzedzia_Produkcyjne_Online_kopia_bazy_danych_$data.zip";

				$zip->open($kopia_zip_sciezka,  ZipArchive::CREATE);

				if ($zip->open($kopia_zip_sciezka) === TRUE) {
				    $zip->addFile($sciezka_do_pliku);
				    $zip->close();

					echo "<br>Pobierz plik: <a href='$kopia_zip_sciezka'>$kopia_zip_nazwa</a>";

				/*header('Content-Description: File Transfer');
			    header('Content-Type: application/zip');
			    header('Content-Disposition: attachment; filename="'.basename($kopia_zip_sciezka).'"');
			    header('Expires: 0');
			    header('Cache-Control: must-revalidate');
			    header('Pragma: public');
			    header('Content-Length: ' . filesize($kopia_zip_sciezka));
			    readfile($kopia_zip_sciezka);
			    exit;*/


				}


			}else
				{
				echo "<div class='alert alert-warning'><span class='glyphicon glyphicon-alert'></span>&nbsp<strong>Ostrzeżenie!</strong>&nbsp Nie udało się wykonać kopii bazy danych.</div>";
				}
   }
?>

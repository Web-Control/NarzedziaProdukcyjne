<h2>Tworzenie kopii zapasowych</h2>
<div id="formularz">
		<div class="row" >
			<div class="form-group">
<form class="form_loguj" method="POST" action="index2.php?kopia_zapasowa=1.php">
	<fieldset>
					<legend>Kopia bazy danych</legend>
		<br / >
		<span class="glyphicon glyphicon-copy"></span>&nbsp<input type="submit" value="Wykonaj kopię" name="stworz_kopie_bazy">
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

	$wykonano_kopie_bazy_danych="";
	$wykonano_kopie_katalogu_zdjec="";

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

					$wykonano_kopie_bazy_danych=TRUE;
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



		//Poniżej robimy zapasową kopię zdjeć i dokumentów należących do raportów suszenia

		//Ścieżki do katalogu ze zdjęciami
		$katalog_zdjec_suszenia = "grafika/zdjecia_raporty_suszenia/";

		// Sprawdzamy katalogi i zapisujemy wyniki-liste zdjec do zmiennej, która jest tablicą
		$zdjecia_raportow_suszenia = scandir($katalog_zdjec_suszenia);
		//print_r($zdjecia_raportow_suszenia);

		if (count($zdjecia_raportow_suszenia>0))
		{

			$kopia_zip_zdjecia = fopen("kopia_bazy/Narzedzia_Produkcyjne_Online_kopia_bazy_zdjec_$data.zip", "w");//Bez wcześniejszego utworzenia pliku ZipArchive nie działa
			$kopia_zip_zdjecia_sciezka="kopia_bazy/Narzedzia_Produkcyjne_Online_kopia_bazy_zdjec_$data.zip";
			$kopia_zip_zdjecia_nazwa="Narzedzia_Produkcyjne_Online_kopia_bazy_zdjec_$data.zip";

			$zip = new ZipArchive;
			$zip->open($kopia_zip_zdjecia_sciezka,  ZipArchive::CREATE);

				if ($zip->open($kopia_zip_zdjecia_sciezka) === TRUE)
				{
					for ($i=2; $i <count($zdjecia_raportow_suszenia) ; $i++)//i=2 poniewaz dwa pierwsze elementy tablicy to tylko kropki zwrócone przez funkcje scandir
					{
						$plik ="grafika/zdjecia_raporty_suszenia/$zdjecia_raportow_suszenia[$i]";
						$zip->addFile($plik);
					}

				    $zip->close();

					if (file_exists($sciezka_do_pliku))
					{
						$wykonano_kopie_katalogu_zdjec=TRUE;
						echo '<div class="alert alert-success alert-dismissable fade in">
						<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
						<span class="glyphicon glyphicon-thumbs-up"></span>&nbsp;<strong>Sukces!</strong>&nbsp Wykonano kopię zapasową zdjęć należących do raportów suszenia. Kopia znajduje się na serwerze w katalogu "kopia_bazy". </div><br / >';


						echo "<br>Pobierz plik: <a href='$kopia_zip_zdjecia_sciezka'>$kopia_zip_zdjecia_nazwa</a>";
					}
				}
		}else
			{
			echo '<div class="alert alert-info"><span class="glyphicon glyphicon-info-sign"></span>&nbsp<strong>Info!</strong>&nbsp Brak zdjęć załączonych do raportów.</div>';
			}

	if ($wykonano_kopie_bazy_danych || $wykonano_kopie_katalogu_zdjec) {

		echo "<br><br><br><br><div class='alert alert-info'><span class='glyphicon glyphicon-info-sign'></span>&nbsp<strong>Info!</strong>&nbsp W celu przywrócenia bazy danych plik sql, który znajduje się w zip należy importować do bazy danych za pomocą narzędzia
			  'phpmyadmn' w pulpcie zarządzania serwerem.</div><br><br>";

		echo "<div class='alert alert-info'><span class='glyphicon glyphicon-info-sign'></span>&nbsp<strong>Info!</strong>&nbsp W celu przywrócenia zdjeć zawartych w raportach suszenia należy je przesłać do następującego katalogu niniejszej aplikacji: '$katalog_zdjec_suszenia'</div><br><br>";


	}


   }
?>

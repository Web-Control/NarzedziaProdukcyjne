<?php
require 'vendor/autoload.php';
use Ifsnop\Mysqldump as IMysqldump;

	ob_end_clean();
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

	$data=date("Y-m-d");
	
	//Tworzymy kopie bazy danych sql
	$nazwa_pliku="Narzedzia_Produkcyjne_Online_kopia_bazy_danych_$data.sql";
	$sciezka_do_pliku="kopia_bazy/$nazwa_pliku";

		try {
		    $dump = new IMysqldump\Mysqldump('mysql:host=mysql530int.cp.az.pl;dbname=db6001900_RaportyWilgotnosci', 'u6001900_szymon', 'mNa5YWLL');
		    $dump->start("kopia_bazy/$nazwa_pliku");
		} catch (\Exception $e) {
		    echo 'mysqldump-php error: ' . $e->getMessage();
		}
		
		//Poniżej robimy zapasową kopię zdjeć i dokumentów należących do raportów suszenia

		//Ścieżki do katalogu ze zdjęciami
		$katalog_zdjec_suszenia = "grafika/zdjecia_raporty_suszenia/";
		$katalog_zdjec_sterylizacji = "grafika/zdjecia_raporty_sterylizacji/";

		// Sprawdzamy katalogi i zapisujemy wyniki-liste zdjec do zmiennej, która jest tablicą
		$zdjecia_raportow_suszenia = scandir($katalog_zdjec_suszenia);
		$zdjecia_raportow_sterylizacji = scandir($katalog_zdjec_sterylizacji);
		//print_r($zdjecia_raportow_suszenia);
		$kopia_zip_zdjecia_suszenia_sciezka="";
		$kopia_zip_zdjecia_sterylizacji_sciezka="";
		
		if (count($zdjecia_raportow_suszenia)>2) //2 poniewaz dwa pierwsze elementy tablicy to tylko kropki zwrócone przez funkcje scandir
		{

			$kopia_zip_zdjecia = fopen("kopia_bazy/Narzedzia_Produkcyjne_Online_kopia_bazy_zdjec_suszenia_$data.zip", "w");//Bez wcześniejszego utworzenia pliku ZipArchive nie działa
			$kopia_zip_zdjecia_sciezka="kopia_bazy/Narzedzia_Produkcyjne_Online_kopia_bazy_zdjec_suszenia_$data.zip";
			$kopia_zip_zdjecia_nazwa="Narzedzia_Produkcyjne_Online_kopia_bazy_zdjec_suszenia_$data.zip";

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

					if (file_exists($kopia_zip_zdjecia_sciezka))
					{
						$kopia_zip_zdjecia_suszenia_sciezka=$kopia_zip_zdjecia_sciezka;
					}
				}
		}
		
			
		if (count($zdjecia_raportow_sterylizacji)>2)//2 poniewaz dwa pierwsze elementy tablicy to tylko kropki zwrócone przez funkcje scandir
		{

			$kopia_zip_zdjecia = fopen("kopia_bazy/Narzedzia_Produkcyjne_Online_kopia_bazy_zdjec_sterylizacji_$data.zip", "w");//Bez wcześniejszego utworzenia pliku ZipArchive nie działa
			$kopia_zip_zdjecia_sciezka="kopia_bazy/Narzedzia_Produkcyjne_Online_kopia_bazy_zdjec_sterylizacji_$data.zip";
			$kopia_zip_zdjecia_nazwa="Narzedzia_Produkcyjne_Online_kopia_bazy_zdjec_sterylizacji_$data.zip";

			$zip = new ZipArchive;
			$zip->open($kopia_zip_zdjecia_sciezka,  ZipArchive::CREATE);

				if ($zip->open($kopia_zip_zdjecia_sciezka) === TRUE)
				{
					for ($i=2; $i <count($zdjecia_raportow_suszenia) ; $i++)//i=2 poniewaz dwa pierwsze elementy tablicy to tylko kropki zwrócone przez funkcje scandir
					{
						$plik ="grafika/zdjecia_raporty_sterylizacji/$zdjecia_raportow_sterylizacji[$i]";
						$zip->addFile($plik);
					}

				    $zip->close();

					if (file_exists($kopia_zip_zdjecia_sciezka))
					{
						$kopia_zip_zdjecia_sterylizacji_sciezka=$kopia_zip_zdjecia_sciezka;
					}
				}
		}
		

			if (file_exists($sciezka_do_pliku)) {

				$zip = new ZipArchive;
				$kopia_zip = fopen("kopia_bazy/Narzedzia_Produkcyjne_Online_kopia_bazy_danych_$data.zip", "w");//Bez wcześniejszego utworzenia pliku ZipArchive nie działa
				$kopia_zip_sciezka="kopia_bazy/Narzedzia_Produkcyjne_Online_kopia_bazy_danych_$data.zip";
				$kopia_zip_nazwa="Narzedzia_Produkcyjne_Online_kopia_bazy_danych_$data.zip";
				$kopia_zip_katalogu_zdjec_suszenia_nazwa="Narzedzia_Produkcyjne_Online_kopia_katalogu_zdjec_suszenia_$data.zip";
				$kopia_zip_katalogu_zdjec_sterylizacji_nazwa="Narzedzia_Produkcyjne_Online_kopia_katalogu_zdjec_sterylizacji_$data.zip";
					
				$zip->open($kopia_zip_sciezka,  ZipArchive::CREATE);

				if ($zip->open($kopia_zip_sciezka) === TRUE) {
				    $zip->addFile($sciezka_do_pliku);
				    $zip->close();

					if (file_exists($kopia_zip_sciezka))
					 {
						require_once ('PHPMailer/PHPMailerAutoload.php');
						# patch where is PHPMailer / ścieżka do PHPMailera
						$email='schomej@jaworski.com.pl';
						$mail = new PHPMailer;
						$mail -> CharSet = "UTF-8";

						$mail -> IsSMTP();
						$mail -> Host = 'mailing.az.pl';
						# Gmail SMTP host
						$mail -> Port = 587;
						# Gmail SMTP port
						$mail -> SMTPAuth = true;
						# Enable SMTP authentication / Autoryzacja SMTP
						$mail -> Username = "formularz@web-control.pl";
						# Gmail username (e-mail) / Nazwa użytkownika
						$mail -> Password = "GqxQ4~w.pz";
						# Gmail password / Hasło użytkownika
						$mail -> SMTPSecure = 'ssl';

						$mail -> From = 'formularz@web-control.pl';
						# REM: Gmail put Your e-mail here
						$mail -> FromName = 'Suszarnia Warzyw Jaworski - Narzędzia Produkcyjne Online';
						# Sender name
						$mail -> SMTPAutoTLS = false;
						//wyłączenie TLS
						$mail -> SMTPSecure = '';
						//
						$mail -> AddAddress($email, $email);
						# # Recipient (e-mail address + name) / Odbiorca (adres e-mail i nazwa)

						$mail -> IsHTML(true);
						# Email @ HTML

						$mail -> Subject = 'Zapasowa kopia bazy danych.';
						$mail -> Body = "Witam.<br / ><br / >
										W załącznikach znajdują się zapasowa kopia bazy danych oraz katalogów zdjeć z raportów suszenia i sterylizacji aplikacji Narzędzia Produkcyjne Online z dnia: $data. <br / >
									 	Możesz je również pobrać klikając poniższe linki:<br / >
									 	- Kopia bazy danych: <a href='$kopia_zip_sciezka'>$kopia_zip_nazwa</a><br / >
									 	- Kopia katalogu zdjęć suszenia: <a href='$kopia_zip_zdjecia_suszenia_sciezka'>$kopia_zip_katalogu_zdjec_suszenia_nazwa</a> <br / >
									 	- Kopia katalogu zdjęć sterylizacji: <a href='$kopia_zip_zdjecia_sterylizacji_sciezka'>$kopia_zip_katalogu_zdjec_sterylizacji_nazwa</a> <br / ><br / >
									 	
										Wiadomość wysłana z aplikacji sieciowej - Narzędzia Produkcyjne Online Suszarnia Warzyw Jaworski<br / >
										Proszę na nią nie odpowiadać.<br / ><br / >
										Administrator: Szymon Chomej. Email: schomej@jaworski.com.pl";
						$mail -> AltBody = 'Przepraszamy wystapił jakiś błąd tutaj powinna być treść wiadomości.';

						$baza_sql = $_SERVER["DOCUMENT_ROOT"].$kopia_zip_sciezka;
						$katalog_zdjec_suszenia=$_SERVER["DOCUMENT_ROOT"].$kopia_zip_zdjecia_suszenia_sciezka;
						$katalog_zdjec_sterylizacji=$_SERVER["DOCUMENT_ROOT"].$kopia_zip_zdjecia_sterylizacji_sciezka;
						$mail -> AddStringAttachment($baza_sql, $kopia_zip_nazwa);
						$mail -> AddStringAttachment($katalog_zdjec_suszenia, $kopia_zip_katalogu_zdjec_suszenia_nazwa);
						$mail -> AddStringAttachment($katalog_zdjec_sterylizacji, $kopia_zip_katalogu_zdjec_sterylizacji_nazwa);
						$mail -> Send();

					}
				}

			}

?>
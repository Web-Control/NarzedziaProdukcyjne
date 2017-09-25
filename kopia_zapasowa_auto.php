<?php
require 'vendor/autoload.php';
use Ifsnop\Mysqldump as IMysqldump;

	ob_end_clean();
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

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

				$zip = new ZipArchive;
				$kopia_zip = fopen("kopia_bazy/Narzedzia_Produkcyjne_Online_kopia_bazy_danych_$data.zip", "w");//Bez wcześniejszego utworzenia pliku ZipArchive nie działa
				$kopia_zip_sciezka="kopia_bazy/Narzedzia_Produkcyjne_Online_kopia_bazy_danych_$data.zip";
				$kopia_zip_nazwa="Narzedzia_Produkcyjne_Online_kopia_bazy_danych_$data.zip";

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
										W załączniku znajduje się zapasowa kopia bazy danych Narzędzi Produkcyjnych Online z dnia: $data. <br / >
									 	W celu jej użycia rozpakuj plik zip a plik sql importuj do bazy danych.<br / ><br / >
										Wiadomość wysłana z aplikacji sieciowej - Narzędzia Produkcyjne Online Suszarnia Warzyw Jaworski<br / >
										Proszę na nią nie odpowiadać.<br / ><br / >
										Administrator: Szymon Chomej. Email: schomej@jaworski.com.pl";
						$mail -> AltBody = 'Przepraszamy wystapił jakiś błąd tutaj powinna być treść wiadomości.';

						$doc = $_SERVER["DOCUMENT_ROOT"].$kopia_zip_sciezka;
						$mail -> AddStringAttachment($doc, $kopia_zip_nazwa);
						$mail -> Send();

					}
				}

			}

?>
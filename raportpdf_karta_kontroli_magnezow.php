<?php

function filtruj($zmienna) {
	$data = trim($zmienna);
	//usuwa spacje, tagi
	$data = stripslashes($zmienna);
	//usuwa slashe
	$data = htmlspecialchars($zmienna);
	//zamienia tagi html na czytelne znaki aby w formularzu nie wpisac szkodliwego kodu
	return $zmienna;
}

if (isset($_POST['pdf']) || isset($_POST['wyslij']) ) {
	
	$karta = $_SESSION['karta_kontroli_magnezow'];
    $linia = $karta[0]['Linia'];
    $data = $karta[0]['Data'];
    $wynik_weryfikacji = $karta[0]['WynikWeryfikacji'];
    $osoba_weryfikujaca = $karta[0]['OsobaWeryfikujaca'];

	$email = filtruj($_POST['email']);

}

if (isset($_POST['wyslij'])) {
	
	$email = filtruj($_POST['email']);
	
}

if (isset($_POST['pdf']) || isset($_POST['wyslij'])) {
	
	 ob_end_clean();
	 ini_set('display_errors', 1);
	 ini_set('display_startup_errors', 1);
	 error_reporting(E_ALL);

	require ('fpdf/fpdf.php');

	$pdf = new FPDF('L');
	$pdf -> SetMargins(25.4, 25.4, 25.4, 25.4);
	$pdf -> AliasNbPages();
	$pdf -> AddPage();

	$pdf -> AddFont('arial_ce', '', 'arial_ce.php');
	$pdf -> AddFont('arial_ce', 'I', 'arial_ce_i.php');
	$pdf -> AddFont('arial_ce', 'B', 'arial_ce_b.php');
	$pdf -> AddFont('arial_ce', 'BI', 'arial_ce_bi.php');

	$pdf -> SetFont('arial_ce', 'B', 16);
	$tekst = "Karta kontroli separatora magnetycznego";
	iconv('UTF-8', 'iso-8859-2//TRANSLIT//IGNORE', $tekst);
	$pdf -> SetXY(50, 24);
	$pdf -> Cell(40, 10, "$tekst", '', 'C');
	$pdf -> Ln(30);

	/*Dane raportu*/
	$pdf -> SetFont('arial_ce', 'B', 14);
	$pdf -> SetXY(10, 40);
	$pdf -> Cell(15, 5, "Linia: $linia ");
	$pdf -> SetXY(100, 40);
	$pdf -> Cell(15, 5, "Data: $data");
	
	$pdf -> Line(10,50,260,50);
	
	
								
								$pdf -> SetFont('arial_ce', 'B', 14);
								$pdf -> SetXY(10, 60);
								$pdf -> Cell(15, 5, "Godzina: ");
								
								$pdf -> SetXY(50, 60);
								$pdf -> Cell(15, 5, "Wynik: ");

								$pdf -> SetXY(90, 60);
								$pdf -> Cell(15, 5, "Uwagi: ");

								$pdf -> SetXY(150, 60);
								$pdf -> Cell(15, 5, "Kontroluj¹cy: ");

								$pdf -> SetFont('arial_ce', '', 12);

								for($i=0; $i < count($karta) ;$i++)
								{	
									$y = $pdf -> GetY();
									$y1 = $y + 7;
									$pdf -> SetXY(10, $y1);
									$pdf -> Cell(15, 5, $karta[$i]['Godzina']);
									$pdf -> SetXY(50, $y1);
									$pdf -> Cell(15, 5, $karta[$i]['Wynik']);
									$pdf -> SetXY(90, $y1);
									$pdf -> Cell(15, 5, $karta[$i]['Uwagi']);
									$pdf -> SetXY(150, $y1);
									/*Zmieniamy kodowanie znakï¿½w z UTF-8 na Windows-1250 poniewaï¿½ klasa fpdf nie wspiera UTF-8
									co powoduje ï¿½e tekst z bazy danych nie pokazuje polskich znakï¿½w */
									$osoba = iconv('UTF-8', 'windows-1250',$karta[$i]['OsobaKontrolujaca']);
									$pdf -> Cell(15, 5, $osoba);
									
								}

								$y = $pdf -> GetY();
								$y2=$y+12;
								$pdf -> SetFont('arial_ce', 'B', 12);
								$pdf -> SetXY(10, $y2);
								$pdf -> Cell(15, 5, "Weryfikacja karty: $wynik_weryfikacji ");
								$pdf -> SetXY(100, $y2);
								$pdf -> Cell(15, 5, "Osoba Weryfikuj¹ca: $osoba_weryfikujaca ");

								
								

	

	//Je??li nie wysy??amy raportu to wyswietlamy go
	if (!isset($_POST['wyslij'])) {
		$pdf -> Output();
	}

	//Wysy??anie raportu poprzez email
	if (isset($_POST['wyslij']) && !$email == null) {

		require_once ('PHPMailer/PHPMailerAutoload.php');
		# patch where is PHPMailer / ?ie??do PHPMailera

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
		# Gmail username (e-mail) / Nazwa u??kownika
		$mail -> Password = "GqxQ4~w.pz";
		# Gmail password / Has??ytkownika
		$mail -> SMTPSecure = 'ssl';

		$mail -> From = 'formularz@web-control.pl';
		# REM: Gmail put Your e-mail here
		$mail -> FromName = 'Suszarnia Warzyw Jaworski - NarzÄ™dzia Produkcyjne Online';
		# Sender name
		$mail -> SMTPAutoTLS = false;
		//wy???czenie TLS
		$mail -> SMTPSecure = '';
		//
		$mail -> AddAddress($email, $email);
		# # Recipient (e-mail address + name) / Odbiorca (adres e-mail i nazwa)

		$mail -> IsHTML(true);
		# Email @ HTML

		$mail -> Subject = 'Karta kontroli separatora magnetycznego';
		$mail -> Body = "Witam.<br / ><br / >
						W za³¹czniku znajduje siê karta kontroli separatora magnetycznego w lini: $linia z dnia: $data.<br / ><br / >
						Wiadomoœæ wys³ana z aplikacji sieciowej - Narzêdzia Produkcyjne Online Suszarnia Warzyw Jaworski<br / >
						Proszê na ni¹ nie odpowiadaæ.<br / ><br / >
						Administrator: Szymon Chomej. Email: schomej@jaworski.com.pl";
		$mail -> AltBody = 'Przepraszamy wyst¹pi³ jakiœ b³¹d tutaj powinna byæ wiadomoœæ';

		$doc = $pdf -> Output('S');
		$mail -> AddStringAttachment($doc, 'raport_podsumowanie_suszenia.pdf', 'base64', 'application/pdf');

		if (!$mail -> Send()) {
			printf("<div class='alert alert-danger'><strong>Uwaga!</strong>&nbspBÅ‚Ä…d podczas wysyÅ‚ania wiadomoÅ›ci Kod bÅ‚Ä™du: %s\n</div><br / ><br / >", $mail -> ErrorInfo);

			exit ;
		}

		echo '<div class="alert alert-success alert-dismissable fade in">
							<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
							<span class="glyphicon glyphicon-thumbs-up"></span>&nbsp;<strong>Sukces!</strong>&nbsp Raport zostaï¿½ wysï¿½any. </div><br / >';

	} 

		
	
	else {
		if (isset($_POST['wyslij']) && $email == null) {		
			echo "<div class='alert alert-warning'><span class='glyphicon glyphicon-alert'></span>&nbsp;<strong>Uwaga!</strong>&nbsp Aby wysï¿½ï¿½ï¿½ raport podaj adres Email.</div>";
	  }
	}

};


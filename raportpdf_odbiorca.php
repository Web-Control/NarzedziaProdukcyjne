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
	
	$rok = $_SESSION['rok'];
	$asortyment_suszu = $_SESSION['asortyment'];
	$nr_dostawcy = $_SESSION['nr_dostawcy'];
	/*Zmieniamy kodowanie znakï¿½w z UTF-8 na Windows-1250 poniewaï¿½ klasa fpdf nie wspiera UTF-8
	co powoduje ï¿½e tekst z bazy danych nie pokazuje polskich znakï¿½w */
	$asortyment_suszu2 = iconv('UTF-8', 'windows-1250', $asortyment_suszu);
	$Zestawienie = $_SESSION['zestawienie'];
	
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
	$tekst = "Daty raportów suszenia dla wybranego dostawcy";
	iconv('UTF-8', 'iso-8859-2//TRANSLIT//IGNORE', $tekst);
	$pdf -> SetXY(50, 24);
	$pdf -> Cell(40, 10, "$tekst", '', 'C');
	$pdf -> Ln(30);

	/*Dane raportu*/
	$pdf -> SetFont('arial_ce', 'B', 14);
	$pdf -> SetXY(10, 40);
	$pdf -> Cell(15, 5, "Asortyment: $asortyment_suszu2");
	$pdf -> SetXY(100, 40);
	$pdf -> Cell(15, 5, "Rok: $rok");
	$pdf -> SetXY(150, 40);
	$pdf -> Cell(15, 5, "Dostawca: $nr_dostawcy");
	
	$pdf -> Line(10,50,260,50);
	$pdf -> Ln(10);	
										
	$pdf -> SetFont('arial_ce', 'B', 14);
	$pdf -> SetXY(10, 60);
	$pdf -> Cell(15, 5, "Data: ");
										
	$pdf -> SetXY(40, 60);
	$pdf -> Cell(15, 5, "Dostawca: ");
	
	$y1 = ""; //pozycja kursora przy wypisywaniu wynik???a bazy danych
	
									if (count($Zestawienie)>0) {
										
										foreach ($Zestawienie as $data => $dostawca)
										 {
												 if ($y1>180) 
												 {
													$pdf -> AddPage();
													
													//Nag?????
													$pdf -> SetFont('arial_ce', 'B', 16);
													$pdf -> SetXY(50, 24);
													$pdf -> Cell(40, 10, "Daty raportÃ³w dla wybranego dostawcy", '', 'C');
													$pdf -> Ln(30);	
													
													/*Dane raportu*/
													$pdf -> SetFont('arial_ce', 'B', 14);
													$pdf -> SetXY(10, 40);
													$pdf -> Cell(15, 5, "Asortyment: $asortyment_suszu2");
													$pdf -> SetXY(100, 40);
													$pdf -> Cell(15, 5, "Rok: $rok");
													$pdf -> SetXY(150, 40);
													$pdf -> Cell(15, 5, "Nr odbiorcy: $nr_odbiorcy");
													
													$pdf -> Line(10,50,260,50);
													$pdf -> Ln(10);	
													
													$pdf -> SetFont('arial_ce', 'B', 14);
													$pdf -> SetXY(10, 60);
													$pdf -> Cell(15, 5, "Data: ");
													
													$pdf -> SetXY(40, 60);
													$pdf -> Cell(15, 5, "Dostawca: ");
												
												}
																				
											$pdf -> SetFont('arial_ce', '', 12);
											$y = $pdf -> GetY();
											$y1 = $y + 7;
											$pdf -> SetXY(10,$y1);
											$pdf -> Cell(15, 5, "$data");
									
											$pdf -> SetXY(40, $y1);
											$pdf -> MultiCell(220, 5, "$dostawca");
										}
										
									}
									
	

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
		$tresc="Witam.<br / ><br / >
				W za³¹czniku znajduje siê raport z list¹ dat raportów suszenia dla dostawcy: $nr_dostawcy.<br / ><br / >
				Wiadomoœæ wys³ana z aplikacji sieciowej - Narzêdzia Produkcyjne Online Suszarnia Warzyw Jaworski<br / >
				Proszê na ni¹ nie odpowiadaæ.<br / ><br / >
				Administrator: Szymon Chomej. Email: schomej@jaworski.com.pl";
				
		$tresc2 = iconv('windows-1250', 'UTF-8', $tresc);

		$mail -> Subject = 'Raporty suszenia dla wybranego dostawcy';
		$mail -> Body = "$tresc2";
		$mail -> AltBody = 'Przepraszamy wyst¹pi³ jakiœ b³¹d tutaj powinna byæ wiadomoœæ';

		$doc = $pdf -> Output('S');
		$mail -> AddStringAttachment($doc, 'raport_podsumowanie_suszenia.pdf', 'base64', 'application/pdf');

		if (!$mail -> Send()) {
			printf("<div class='alert alert-danger'><strong>Uwaga!</strong>&nbspB³¹dd podczas wysy³ania wiadomoœci Kod b³êdu: %s\n</div><br / ><br / >", $mail -> ErrorInfo);

			exit ;
		}

		echo '<div class="alert alert-success alert-dismissable fade in">
							<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
							<span class="glyphicon glyphicon-thumbs-up"></span>&nbsp;<strong>Sukces!</strong>&nbsp Raport zosta³ wys³any. </div><br / >';

	} 

		
	
	else {
		if (isset($_POST['wyslij']) && $email == null) {		
			echo "<div class='alert alert-warning'><span class='glyphicon glyphicon-alert'></span>&nbsp;<strong>Uwaga!</strong>&nbsp Aby wys³aæ raport podaj adres Email.</div>";
	  }
	}

};


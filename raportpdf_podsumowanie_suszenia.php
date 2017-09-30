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
	/*Zmieniamy kodowanie znaków z UTF-8 na Windows-1250 poniewa¿ klasa fpdf nie wspiera UTF-8
	co powoduje ¿e tekst z bazy danych nie pokazuje polskich znaków */
	$asortyment_suszu2 = iconv('UTF-8', 'windows-1250', $asortyment_suszu);
	
	$nr_suszarni = $_SESSION['nr_suszarni'];
	$pierwsza_data = $_SESSION['pierwsza_data'];
	$ostatnia_data = $_SESSION['ostatnia_data'];
	$liczba_dni = $_SESSION['liczba_dni'];
	$ilosc_suszu = $_SESSION['ilosc_suszu'];
	$srednia_wilgotnosc = $_SESSION['sr_wilg'];
	$wydajnosc = $_SESSION['wydajnosc'];
	$wydajnosc2 = $_SESSION['wydajnosc_h'];
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
	$tekst = "Podsumowanie z procesu suszenia";
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
	$pdf -> Cell(15, 5, "Nr suszarni: $nr_suszarni");
	
	$pdf -> Line(10,50,260,50);

	$pdf -> SetFont('arial_ce', 'B', 12);
	$pdf -> SetXY(10, 60);
	$pdf -> Cell(15, 5, "Pocz¹tek produkcji: ");
	$pdf -> SetFont('arial_ce', '', 12);
	$pdf -> SetXY(52, 60);
	$pdf -> Cell(15, 5, "$pierwsza_data");
	
	
	$pdf -> SetFont('arial_ce', 'B', 12);
	$pdf -> SetXY(85, 60);
	$pdf -> Cell(15, 5, "Koniec produkcji: ");
	$pdf -> SetFont('arial_ce', '', 12);
	$pdf -> SetXY(122, 60);
	$pdf -> Cell(15, 5, "$ostatnia_data");
	
	$pdf -> SetFont('arial_ce', 'B', 12);
	$pdf -> SetXY(150, 60);
	$pdf -> Cell(15, 5, "Liczba dni produkcyjnych: ");
	$pdf -> SetFont('arial_ce', '', 12);
	$pdf -> SetXY(205, 60);
	$pdf -> Cell(15, 5, "$liczba_dni");
	
	$pdf -> SetFont('arial_ce', 'B', 12);
	$pdf -> SetXY(10, 70);
	$pdf -> Cell(15, 5, "Iloœæ towaru: ");
	$pdf -> SetFont('arial_ce', '', 12);
	$pdf -> SetXY(38, 70);
	$pdf -> Cell(15, 5, "$ilosc_suszu kg");
	
	$pdf -> SetFont('arial_ce', 'B', 12);
	$pdf -> SetXY(85, 70);
	$pdf -> Cell(15, 5, "Wydajnoœæ:");
	$pdf -> SetFont('arial_ce', '', 12);
	$pdf -> SetXY(110, 70);
	$pdf -> Cell(15, 5, "$wydajnosc kg/24h");
	
	$pdf -> SetFont('arial_ce', 'B', 12);
	$pdf -> SetXY(150, 70);
	$pdf -> Cell(15, 5, "Œrednia wilgotnoœæ:");
	$pdf -> SetFont('arial_ce', '', 12);
	$pdf -> SetXY(193, 70);
	$pdf -> Cell(15, 5, "$srednia_wilgotnosc %");
	$pdf -> Line(10,80,260,80);
	
	/* ????czymy si??? serwerem */
	require_once ('polaczenie_z_baza.php');
	
	$y1 = ""; //pozycja y kursora przy wypisywaniu wynik???a bazy danych
	
	if ($stmt = $mysqli -> prepare("SELECT DISTINCT Data,Dostawca FROM `" . $asortyment_suszu . "` WHERE NrSuszarni=? AND Data LIKE '%" . $rok . "%' HAVING Dostawca > 0"))
						{
							$stmt->bind_param("s",$nr_suszarni);
							$stmt -> execute();
							$stmt -> bind_result($Data,$Dostawca);
							$stmt -> store_result();
							
							if ($stmt->num_rows > 0)
							{
								
								$pdf -> SetFont('arial_ce', 'B', 14);
								$pdf -> SetXY(10, 90);
								$pdf -> Cell(15, 5, "Data: ");
								
								$pdf -> SetXY(40, 90);
								$pdf -> Cell(15, 5, "Dostawca: ");
								
								$stmt->data_seek(0);
									while ($stmt->fetch()) {
										
										if ($y1>170) {
										$pdf -> AddPage();
										
										//Nag?????
										$pdf -> SetFont('arial_ce', 'B', 16);
										$pdf -> SetXY(50, 24);
										$pdf -> Cell(40, 10, "Podsumowanie z procesu suszenia", '', 'C');
										$pdf -> Ln(30);	
										
										/*Dane raportu*/
										$pdf -> SetFont('arial_ce', 'B', 14);
										$pdf -> SetXY(10, 40);
										$pdf -> Cell(15, 5, "Asortyment: $asortyment_suszu2");
										$pdf -> SetXY(100, 40);
										$pdf -> Cell(15, 5, "Rok: $rok");
										$pdf -> SetXY(150, 40);
										$pdf -> Cell(15, 5, "Nr suszarni: $nr_suszarni");
										
										$pdf -> Line(10,50,260,50);
										$pdf -> Ln(10);	
										
										$pdf -> SetFont('arial_ce', 'B', 14);
										$pdf -> SetXY(10, 60);
										$pdf -> Cell(15, 5, "Data: ");
										
										$pdf -> SetXY(40, 60);
										$pdf -> Cell(15, 5, "Dostawca: ");
										
										}
										
										$data=$Data;
										$dostawca=$Dostawca;
										
										$pdf -> SetFont('arial_ce', '', 12);
										$y = $pdf -> GetY();
										$y1 = $y + 7;
										
										$pdf -> SetXY(10,$y1);
										$pdf -> Cell(15, 5, "$data");
										
										$pdf -> SetXY(40, $y1);
										$pdf -> MultiCell(220, 5, "$dostawca");
										
									}
									
									
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

		$mail -> Subject = 'Raport z podsumowania procesu suszenia';
		$mail -> Body = "Witam.<br / ><br / >
						W zaÅ‚Ä…czniku znajduje siÄ™ raport z podsumowania procesu suszenia dla asortymentu: $asortyment_suszu.<br / ><br / >
						WiadomoÅ›Ä‡ wysÅ‚ana z aplikacji sieciowej - NarzÄ™dzia Produkcyjne Online Suszarnia Warzyw Jaworski<br / >
						ProszÄ™ na niÄ… nie odpowiadaÄ‡.<br / ><br / >
						Administrator: Szymon Chomej. Email: schomej@jaworski.com.pl";
		$mail -> AltBody = 'Przepraszamy wyst?i? jaki?b?d tutaj powinna by?re? wiadomo?i';

		$doc = $pdf -> Output('S');
		$mail -> AddStringAttachment($doc, 'raport_podsumowanie_suszenia.pdf', 'base64', 'application/pdf');

		if (!$mail -> Send()) {
			printf("<div class='alert alert-danger'><strong>Uwaga!</strong>&nbspBÅ‚Ä…d podczas wysyÅ‚ania wiadomoÅ›ci Kod bÅ‚Ä™du: %s\n</div><br / ><br / >", $mail -> ErrorInfo);

			exit ;
		}

		echo '<div class="alert alert-success alert-dismissable fade in">
							<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
							<span class="glyphicon glyphicon-thumbs-up"></span>&nbsp;<strong>Sukces!</strong>&nbsp Raport zosta³ wys³any. </div><br / >';

	} 

		
	
	else {
		if (isset($_POST['wyslij']) && $email == null) {		
			echo "<div class='alert alert-warning'><span class='glyphicon glyphicon-alert'></span>&nbsp;<strong>Uwaga!</strong>&nbsp Aby wys³¹æ raport podaj adres Email.</div>";
	  }
	}

};


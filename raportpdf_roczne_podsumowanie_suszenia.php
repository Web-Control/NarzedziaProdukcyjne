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
	$email = filtruj($_POST['email']);

}

if (isset($_POST['wyslij'])) {
	
	$email = filtruj($_POST['email']);
	
}

if (isset($_POST['pdf']) || isset($_POST['wyslij'])) {
	
	/* ob_end_clean();
	 ini_set('display_errors', 1);
	 ini_set('display_startup_errors', 1);
	 error_reporting(E_ALL);*/

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
	$tekst = "Roczne podsumowanie procesu suszenia";
	iconv('UTF-8', 'iso-8859-2//TRANSLIT//IGNORE', $tekst);
	$pdf -> SetXY(50, 24);
	$pdf -> Cell(40, 10, "$tekst", '', 'C');
	$pdf -> Ln(30);

	/*Dane raportu*/
	$pdf -> SetFont('arial_ce', 'B', 14);
	$pdf -> SetXY(10, 40);
	$pdf -> Cell(15, 5, "Rok: $rok");
	
	$pdf -> Line(10,50,260,50);
	
	/* ????czymy si??? serwerem */
	require_once ('polaczenie_z_baza.php');
	
	 $Asortyment_wbazie=array();
	 $Zestawienie_suszu=array();
	 $Calkowita_ilosc_suszu="";
	 $Zestawienie_wilgotnosci="";
				 	
					//Robimy liste asortymentu. Zapytanie do bazy o obecny asortyment 
					if ($stmt = $mysqli -> prepare("SELECT Asortyment FROM AsortymentSuszu WHERE Asortyment NOT LIKE '%Arbuz%' "))
					{
							//echo "Zapytanie1 dziaÅ‚a<br / >";
						$stmt -> execute();
						$stmt -> bind_result($Obecny_asortyment);
						$stmt -> store_result();
						
						if ($stmt->num_rows > 0) {
	
							/* WyciÄ…gamy dane z zapytania sql i zapisujemy do tablicy  */
		    				while ($stmt->fetch()) {
							static $i=0;
							$Asortyment_wbazie[$i]=$Obecny_asortyment;
							$i++;
		    				}
	    				}		
					}
					
				//Pobieramy ilosc suszu dla kaÅ¼dego asortymentu	
				foreach ($Asortyment_wbazie as $key => $asortyment) 
				{
					
				if ($stmt = $mysqli -> prepare("SELECT SUM(CalkowitaIloscSuszu) FROM `" .$asortyment. "` WHERE NrSuszarni=(SELECT MIN(NrSuszarni) FROM  `" .$asortyment. "`) AND Data LIKE '%" . $rok . "%' "))
						{ 
					$stmt -> execute();
					$stmt -> bind_result($Suma_suszu);
					$stmt -> store_result();
					$stmt->data_seek(0);
						if ($stmt -> fetch())
							{
							$Zestawienie_suszu[$asortyment]=$Suma_suszu;								
							}
						}
				}
				
				//Obliczamy caÅ‚kowitÄ… iloÅ›Ä‡ suszu
				foreach ($Zestawienie_suszu as $asortyment => $ilosc_suszu) {
				$Calkowita_ilosc_suszu=$Calkowita_ilosc_suszu+$ilosc_suszu;
				}
				
				//Pobieramy Å›redniÄ… wartoÅ›Ä‡ wilgotnoÅ›Ä‡ dla kaÅ¼dego asortymentu
				foreach ($Asortyment_wbazie as $key => $asortyment) 
				{
					
				if ($stmt = $mysqli -> prepare("SELECT AVG(Wilgotnosc) FROM `" .$asortyment. "` WHERE Wilgotnosc > 0 AND Data LIKE '%" . $rok . "%' "))
						{ 
					$stmt -> execute();
					$stmt -> bind_result($Sr_wilg);
					$stmt -> store_result();
					$stmt->data_seek(0);
						if ($stmt -> fetch())
							{
							$Sr_wilg=round($Sr_wilg,2);	
							$Zestawienie_wilgotnosci[$asortyment]=$Sr_wilg;								
							}
						}
				}
				
	//NagÅ‚Ã³wki tabeli			
	$pdf -> SetFont('arial_ce', 'B', 14);
	$pdf -> SetXY(10, 60);
	$pdf -> Cell(15, 5, "Asortyment");
	
	$pdf -> SetFont('arial_ce', 'B', 14);
	$pdf -> SetXY(100, 60);
	$pdf -> Cell(15, 5, "Iloœæ suszu");
	
	$pdf -> SetFont('arial_ce', 'B', 14);
	$pdf -> SetXY(190, 60);
	$pdf -> Cell(15, 5, "Œrednia wilgotnoœæ");
	
	$pdf -> Ln(5);
	
	$y = ""; //pozycja kursora przy wypisywaniu wynikÃ³w z tablicy
		foreach ($Zestawienie_suszu as $asortyment => $ilosc_suszu) 
		{
			/*Zmieniamy kodowanie znakï¿½w z UTF-8 na Windows-1250 poniewaï¿½ klasa fpdf nie wspiera UTF-8
			co powoduje ï¿½e tekst z bazy danych nie pokazuje polskich znakï¿½w */
			$asortyment2=$asortyment;
			$asortyment2 = iconv('UTF-8', 'windows-1250', $asortyment2);
			
			$y = $pdf -> GetY();
			$y1 = $y + 7;
			
					if ($y1>180) {
					$pdf -> AddPage();
					
					$pdf -> SetFont('arial_ce', 'B', 16);
					$tekst = "Roczne podsumowanie procesu suszenia";
					iconv('UTF-8', 'iso-8859-2//TRANSLIT//IGNORE', $tekst);
					$pdf -> SetXY(50, 24);
					$pdf -> Cell(40, 10, "$tekst", '', 'C');
					$pdf -> Ln(30);
				
					/*Dane raportu*/
					$pdf -> SetFont('arial_ce', 'B', 14);
					$pdf -> SetXY(10, 40);
					$pdf -> Cell(15, 5, "Rok: $rok");
					
					$pdf -> Line(10,50,260,50);
					
					//NagÅ‚Ã³wki tabeli			
					$pdf -> SetFont('arial_ce', 'B', 14);
					$pdf -> SetXY(10, 60);
					$pdf -> Cell(15, 5, "Asortyment");
					
					$pdf -> SetFont('arial_ce', 'B', 14);
					$pdf -> SetXY(100, 60);
					$pdf -> Cell(15, 5, "Iloœæ suszu");
					
					$pdf -> SetFont('arial_ce', 'B', 14);
					$pdf -> SetXY(190, 60);
					$pdf -> Cell(15, 5, "Œrednia wilgotnoœæ");
					
					$pdf -> Ln(5);
					
					}
				
			$pdf -> SetFont('arial_ce', 'B', 12);
			$pdf -> SetXY(10, $y1);
			$pdf -> Cell(15, 5, "$asortyment2");	
			
			$pdf -> SetFont('arial_ce', 'B', 12);
			$pdf -> SetXY(100, $y1);
			$pdf -> Cell(15, 5, "$ilosc_suszu kg");
			
			$pdf -> SetFont('arial_ce', 'B', 12);
			$pdf -> SetXY(190, $y1);
			$pdf -> Cell(15, 5, "$Zestawienie_wilgotnosci[$asortyment] %");	
			
		}
	
	
	$y = $pdf -> GetY();
	$y2=$y+10;
	$y3=$y+20;
	
	$pdf -> Line(10,$y2,260,$y2);
	
	$pdf -> SetFont('arial_ce', 'B', 14);
	$pdf -> SetXY(10, $y3);
	$pdf -> Cell(15, 5, "Ca³kowita iloœæ suszu:");
	
	$pdf -> SetFont('arial_ce', '', 14);
	$pdf -> SetXY(100, $y3);
	$pdf -> Cell(15, 5, "$Calkowita_ilosc_suszu kg");
		
	

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

		$mail -> Subject = 'Raport z rocznego podsumowania procesu suszenia';
		$mail -> Body = "Witam.<br / ><br / >
						W za³¹czniku znajduje siê raport z rocznego podsumowania procesu suszenia: $rok.<br / ><br / >
						Wiadomoœæ wys³ana z aplikacji sieciowej - Narzêdzia Produkcyjne Online Suszarnia Warzyw Jaworski<br / >
						Proszê na ni¹ nie odpowiadaæ.<br / ><br / >
						Administrator: Szymon Chomej. Email: schomej@jaworski.com.pl";
		$mail -> AltBody = 'Przepraszamy wyst¹pi³ jakiœ b³¹d tutaj powinna byæ wiadomoœæ';

		$doc = $pdf -> Output('S');
		$mail -> AddStringAttachment($doc, 'raport_roczne_podsumowanie_suszenia.pdf', 'base64', 'application/pdf');

		if (!$mail -> Send()) {
			printf("<div class='alert alert-danger'><strong>Uwaga!</strong>Wyst¹pi³ b³¹d podczas wysy³ania wiadomoœci Kod b³êdu: %s\n</div><br / ><br / >", $mail -> ErrorInfo);

			exit ;
		}

		echo '<div class="alert alert-success alert-dismissable fade in">
							<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
							<span class="glyphicon glyphicon-thumbs-up"></span>&nbsp;<strong>Sukces!</strong>&nbsp Raport zosta³ wys³any. </div><br / >';

	} 

		
	
	else {
		if (isset($_POST['wyslij']) && $email == null) {		
			echo "<div class='alert alert-warning'><span class='glyphicon glyphicon-alert'></span>&nbsp;<strong>Uwaga!</strong>&nbsp Aby wysï¿½aï¿½ raport podaj adres Email.</div>";
	  }
	}

};


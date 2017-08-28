<?php
session_start();
	ob_end_clean();
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
if ($_POST['wykresy_pdf']) {

	$sr_wil_pocz = $_SESSION['sr_wil_pocz'];
	$sr_wil_kon = $_SESSION['sr_wil_kon'];
	$roznica = $_SESSION['roznica'];

	require ('fpdf/fpdf.php');
	require ('fpdf/writeJavaScript.php');
	require ('fpdf/writeHTML.php');

	$pdf=new PDF_HTML('L');
	$pdf->AliasNbPages();
	$pdf -> AddPage();

	$pdf -> AddFont('arial_ce', '', 'arial_ce.php');
	$pdf -> AddFont('arial_ce', 'I', 'arial_ce_i.php');
	$pdf -> AddFont('arial_ce', 'B', 'arial_ce_b.php');
	$pdf -> AddFont('arial_ce', 'BI', 'arial_ce_bi.php');

	//Nagłówek
	$pdf -> SetFont('arial_ce', 'B', 16);
	$pdf -> SetXY(50, 24);
	$pdf -> Cell(40, 10, "Wykresy z procesu sterylizacji parowej", '', 'C');
	$pdf -> Ln(30);


	$pdf->SetFont('Arial');
	$pdf -> SetXY(10, 40);
	$pdf->WriteHTML('<div id="chart_div" style="width:100%;height: 350px;">Działa</div><br / >
					 <div id="chart_div2" style="width:100%;height: 200px;"></div><br / >
					 <div id="chart_div3" style="width:100%;height: 350px;"></div>');

	$pdf->WriteHTML('Tez działa');

	$link = $_REQUEST["link"];
	$pdf -> Image("$link", 10, 40, 90, 60);

	$pdf -> Output();
}
?>
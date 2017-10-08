<?php
require_once ('funkcje.php');
/* Łączymy się z serwerem */
require_once ('polaczenie_z_baza.php');

   	$linia = filtruj($_POST['linia']);
    $data = filtruj($_POST['data']);
	$kolejny_dzien = date('Y-m-d', strtotime($data . ' +1 day'));
	$pobrane_dane =array();
	
	echo "Linia: $linia , Data: $data";

    if (mysqli_connect_errno()) {
         printf("<div class='alert alert-danger'><span class='glyphicon glyphicon-thumbs-down'></span>&nbsp;<strong>Uwaga!</strong>&nbspBrak połączenia z serwerem MySQL. Kod błędu: %s\n</div>.", mysqli_connect_error());
        } else {

                IF ($stmt = $mysqli -> prepare ("SELECT FROM Karta_Kontroli_Separatora_Magnetycznego Linia,Data,Godzina,Wynik,Uwagi,OsobaKontrolujaca,WynikWeryfikacji,OsobaWeryfikujaca WHERE Linia=? AND WHERE Data=? AND Godzina >=  STR_TO_DATE('08:00:00','%h:%i:%s')
                								UNION
                								SELECT FROM Karta_Kontroli_Separatora_Magnetycznego Linia,Data,Godzina,Wynik,Uwagi,OsobaKontrolujaca,WynikWeryfikacji,OsobaWeryfikujaca WHERE Linia=? AND WHERE Data=? AND Godzina <=  STR_TO_DATE('06:00:00','%h:%i:%s') ORDER BY Data, Czas ASC"))
                {
                    $stmt->bind_param("ssss", $linia, $data,$linia,$kolejny_dzien);
                    $stmt->execute();
                    $stmt-> bind_result($Linia,$Data,$Godzina,$Wynik_Kontroli,$Uwagi,Osoba_Kontrolujaca,$Wynik_Weryfikacji,$Osoba_Weryfikujaca);
					$stmt->store_result();
					
					$wynik = $stmt->get_result();
					
					while ($data = $wynik->fetch_assoc())
					{
					    $pobrane_dane[] = $data;
					}
					
				

                    if ($stmt -> affected_rows == 0 || $stmt -> affected_rows < 0 ||$stmt->affected_rows==NULL) {
                        echo "<div class='alert alert-warning'><strong>Ostrzeżenie!</strong>&nbsp Nie dokokano zapisu. Możliwy błąd zapytania.</div>";
                    }else {
                            if ($stmt -> affected_rows > 0) {
                              	echo json_encode($pobrane_dane);  
                            }

                         }

                }else {
                         echo '<div class="alert alert-danger"><strong>Info!</strong>&nbsp Błąd podczas odczytu z bazy danych.</div>';
                    }

            }

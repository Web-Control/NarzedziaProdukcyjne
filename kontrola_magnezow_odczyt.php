<?php
require_once ('funkcje.php');
/* Łączymy się z serwerem */
require_once ('polaczenie_z_baza.php');

   	$linia = filtruj($_GET['linia']);
    $data = filtruj($_GET['data']);
	$kolejny_dzien = date('Y-m-d', strtotime($data . ' +1 day'));
	$pobrane_dane =array();
	
	echo "Linia: $linia , Data: $data";

    if (mysqli_connect_errno()) {
         printf("<div class='alert alert-danger'><span class='glyphicon glyphicon-thumbs-down'></span>&nbsp;<strong>Uwaga!</strong>&nbspBrak połączenia z serwerem MySQL. Kod błędu: %s\n</div>.", mysqli_connect_error());
        } else {

                IF ($stmt = $mysqli -> prepare ("SELECT Linia,Data,Godzina,Wynik,Uwagi,OsobaKontrolujaca,WynikWeryfikacji,OsobaWeryfikujaca FROM Karta_Kontroli_Separatora_Magnetycznego WHERE Linia=? AND Data=? AND Godzina >=  STR_TO_DATE('08:00:00','%h:%i:%s') 
                UNION ALL 
                SELECT Linia,Data,Godzina,Wynik,Uwagi,OsobaKontrolujaca,WynikWeryfikacji,OsobaWeryfikujaca FROM Karta_Kontroli_Separatora_Magnetycznego WHERE Linia=? AND Data=? AND Godzina <=  STR_TO_DATE('06:00:00','%h:%i:%s') ORDER BY Data, Godzina ASC"))
                {echo"Działa";
                    $stmt->bind_param("ssss", $linia, $data,$linia,$kolejny_dzien);
                    $stmt->execute(); 
                    $stmt-> bind_result($Linia,$Data,$Godzina,$Wynik_Kontroli,$Uwagi,$Osoba_Kontrolujaca,$Wynik_Weryfikacji,$Osoba_Weryfikujaca);
                    $stmt->store_result();
                    
                    $wiersze = $stmt->num_rows;
                    echo "Liczba wierszy: $wiersze";

                   // var_dump($pobrane_dane);
					
					//$wynik = $stmt->get_result();
                    
                   // $pobrane_dane = $stmt->fetchAll();
                    
					// while ($data = $wynik->fetch_assoc())
					// {
					//     $pobrane_dane[] = $data;
                    // }

                    function stmt_bind_assoc (&$stmt, &$out) {
                        $data = mysqli_stmt_result_metadata($stmt);
                        $fields = array();
                        $out = array();
                    
                        $fields[0] = $stmt;
                        $count = 1;
                    
                        while($field = mysqli_fetch_field($data)) {
                            $fields[$count] = &$out[$field->name];
                            $count++;
                        }    
                        call_user_func_array(mysqli_stmt_bind_result, $fields);
                    }

                    $wiersz=array();
                    stmt_bind_assoc($stmt, $wiersz);


                    while($stmt->fetch()){
                            $pobrane_dane[$wiersz];
                    }
                    
                    var_dump($pobrane_dane);
					

                    if ( $stmt->num_rows == 0 ) {
                        echo "<div class='alert alert-warning'><strong>Ostrzeżenie!</strong>&nbsp Nie dokokano odczytu. Możliwy błąd zapytania.</div>";
                    }else {
                            if ($stmt->num_rows > 0) {
                               echo"Tez działa";
                              	echo json_encode($pobrane_dane);  
                            }

                         }

                }else {
                         echo '<div class="alert alert-danger"><strong>Info!</strong>&nbsp Błąd podczas odczytu z bazy danych.</div>';
                    }
                    $stmt=$mysqli->close();         
            }

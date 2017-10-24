<?php
require_once ('funkcje.php');
/* Łączymy się z serwerem */
require_once ('polaczenie_z_baza_pdo.php');

   	$linia = filtruj($_GET['linia']);
    $data = filtruj($_GET['data']);
    $godzina = filtruj($_GET['godzina']);
    $kolejny_dzien = date('Y-m-d', strtotime($data . ' +1 day'));
   
    if(!$godzina==null) //Aby uniknąć zamiany godziny o wartość null na 0 
    {
    settype($godzina, 'integer');
        if ($godzina >= 0 && $godzina < 8)
        {
            $data = date('Y-m-d', strtotime($data . ' -1 day'));
            $kolejny_dzien = date('Y-m-d', strtotime($kolejny_dzien . ' -1 day'));
        }
    }
    
	$pobrane_dane =array();
	
                IF ($stmt = $db -> prepare ("SELECT Linia,Data,Godzina,Wynik,Uwagi,OsobaKontrolujaca,WynikWeryfikacji,OsobaWeryfikujaca FROM Karta_Kontroli_Separatora_Magnetycznego WHERE Linia=:linia AND Data=:data AND Godzina >=  STR_TO_DATE('08:00:00','%h:%i:%s') 
                UNION ALL 
                SELECT Linia,Data,Godzina,Wynik,Uwagi,OsobaKontrolujaca,WynikWeryfikacji,OsobaWeryfikujaca FROM Karta_Kontroli_Separatora_Magnetycznego WHERE Linia=:linia AND Data=:kolejny_dzien AND Godzina <=  STR_TO_DATE('06:00:00','%h:%i:%s') ORDER BY Data, Godzina ASC"))
                {
                    $stmt->bindValue(':linia',$linia);
                    $stmt->bindValue(':data',$data);
                    $stmt->bindValue(':kolejny_dzien',$kolejny_dzien);
                    $stmt->execute();
                    $wynik = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    //var_dump($wynik);

                    if ( count($wynik) > 0 ) {
                        echo json_encode($wynik);
                    }else {
                            echo "<div class='alert alert-warning'><strong>Ostrzeżenie!</strong>&nbsp Nie dokonano odczytu. Możliwy błąd zapytania.</div>";
                         }

                }else {
                         echo '<div class="alert alert-danger"><strong>Info!</strong>&nbsp Błąd podczas odczytu z bazy danych.</div>';
                    }
                    $stmt=$db->close();         
            

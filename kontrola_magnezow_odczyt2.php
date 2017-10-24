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

                    //Ustawiamy zmienną do stworzenia pliku pdf
                    $_SESSION['karta_kontroli_magnezow'] = $wynik;

                   // var_dump($wynik);
                  // var_dump($_SESSION['karta_kontroli_magnezow']);

                    if ( count($wynik) > 0 ) {
                        echo '<div class="alert alert-success alert-dismissable fade in">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        <span class="glyphicon glyphicon-thumbs-up"></span>&nbsp;<strong>Sukces!</strong>&nbsp Odczytano dane. Poniżej znajduje się twój raport. </div><br / >';
                        
                        echo " <div class='table-responsive' ><table class='table table-hover'>";
                        echo"<caption>Karta kontroli separatora magnetycznego</caption><thead><tr><th>Linia: ".$wynik[0]['Linia']."</th><th>Data: ".$wynik[0]['Data']."</th></tr><tr><th>Godzina</th><th>Wynik</th><th>Uwagi</th><th>Kontrolujący</th></tr></thead>";

                        for($x=0; $x < count($wynik) ;$x++)
                        {
                           
                         echo "<tr><td>".$wynik[$x]['Godzina']."</td><td>".$wynik[$x]['Wynik']."</td><td>".$wynik[$x]['Uwagi']."</td><td>".$wynik[$x]['OsobaKontrolujaca']."</td></tr>";
                
                        }
                        echo"<tr><td><b>Weryfikacja karty: ".$wynik[0]['WynikWeryfikacji']."</b></td><td><b>Osoba Weryfikująca: ".$wynik[0]['OsobaWeryfikujaca']."</b></td></tr>";
                        echo"</table> <br><br>";

                        echo "<hr><form method='post' action='raportpdf_karta_kontroli_magnezow_pokaz.php' target='_blank'><input type='submit' value='Pobierz raport PDF' name='pdf'></form><br / >";

                       // echo json_encode($wynik);
                    }else {
                            echo "<div class='alert alert-info'><strong>Ostrzeżenie!</strong>&nbsp Brak karty w podanym dniu.</div>";
                         }

                }else {
                         echo '<div class="alert alert-danger"><strong>Info!</strong>&nbsp Błąd podczas odczytu z bazy danych.</div>';
                    }
                    $stmt=$db->close();         
            

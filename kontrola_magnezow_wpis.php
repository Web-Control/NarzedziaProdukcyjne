<?php
require_once ('funkcje.php');
/* Łączymy się z serwerem */
require_once ('polaczenie_z_baza.php');

if (1==1) {
    
    $linia = filtruj($_POST['linia']);
    $data = filtruj($_POST['data']);
    $godzina = filtruj($_POST['godzina']);
    $wynik = filtruj($_POST['wynik']);
    $uwagi = filtruj($_POST['uwagi']);
    $osoba_kontrolujaca = filtruj($_POST['osoba_kontrolujaca']);
	
	//echo "Linia: $linia, Data: $data, Godzina: $godzina, Wynik: $wynik, Uwagi: $uwagi, Osoba: $osoba_kontrolujaca";

    if (mysqli_connect_errno()) {
         printf("<div class='alert alert-danger'><span class='glyphicon glyphicon-thumbs-down'></span>&nbsp;<strong>Uwaga!</strong>&nbspBrak połączenia z serwerem MySQL. Kod błędu: %s\n</div>.", mysqli_connect_error());
        } else {

                if ($stmt= $mysqli->prepare("SELECT Linia,Data,Godzina,Wynik,Uwagi,OsobaKontrolujaca,WynikWeryfikacji,OsobaWeryfikujaca FROM Karta_Kontroli_Separatora_Magnetycznego WHERE Linia=? AND Data=? AND Godzina=?"))
                {
                    $stmt->bind_param("sss", $linia,$data,$godzina);
                    $stmt->execute();
                    $stmt->store_result();
                    
                         if ($stmt->num_rows > 0 )
                         {  
                            echo '<div class="alert alert-warning"><strong>Info!</strong>&nbsp Wpis o tej godzinie już istnieje!</div><br />';      
                          }
                            else{
                
                                    IF ($stmt = $mysqli -> prepare ("INSERT INTO Karta_Kontroli_Separatora_Magnetycznego(Linia,Data,Godzina,Wynik,OsobaKontrolujaca) VALUES(?,?,?,?,?)"))
                                    {
                                        $stmt->bind_param("sssss", $linia,$data,$godzina,$wynik,$osoba_kontrolujaca);
                                        $stmt->execute();

                                        if ($stmt -> affected_rows == 0 || $stmt -> affected_rows < 0 ||$stmt->affected_rows==NULL) {
                                            echo "<div class='alert alert-warning'><strong>Ostrzeżenie!</strong>&nbsp Nie dokokano zapisu. Możliwy błąd zapytania.</div>";
                                        }else {
                                                if ($stmt -> affected_rows > 0) {
                                                    echo '<div class="alert alert-success alert-dismissable fade in">
                                                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                                    <span class="glyphicon glyphicon-thumbs-up"></span>&nbsp;<strong>Sukces!</strong>&nbsp Zapisano dane. Poniżej znajduje się twój raport. </div><br / >';

                                                }

                                            }

                                    }else {
                                            echo '<div class="alert alert-danger"><strong>Info!</strong>&nbsp Błąd podczas zapisu do bazy danych.</div>';
                                        }
                                 }

                }
				
				if (isset($uwagi) && !$uwagi==null) {
					
					 IF ($stmt = $mysqli -> prepare ("UPDATE Karta_Kontroli_Separatora_Magnetycznego SET Uwagi=? WHERE Linia=? AND Data=? AND Godzina=? "))
                {
                    $stmt->bind_param("ssss", $uwagi,$linia,$data,$godzina);
                    $stmt->execute();

                    if ($stmt -> affected_rows == 0 || $stmt -> affected_rows < 0 ||$stmt->affected_rows==NULL) {
                        echo "<div class='alert alert-warning'><strong>Ostrzeżenie!</strong>&nbsp Nie dokokano zapisu uwag. Możliwy błąd zapytania.</div>";
                    }

                }else {
                         echo '<div class="alert alert-danger"><strong>Info!</strong>&nbsp Błąd podczas zapisu uwag do bazy danych.</div>';
                    }
					
				}
				
				


            }


}
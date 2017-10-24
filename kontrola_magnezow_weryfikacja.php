<?php
require_once ('funkcje.php');
/* Łączymy się z serwerem */
require_once ('polaczenie_z_baza.php');

if (1==1) {
  
   	$linia = filtruj($_POST['linia']);
    $data = filtruj($_POST['data']);
	$wynik_weryfikacji = filtruj($_POST['wynik']);
    $osoba_weryfikujaca = filtruj($_POST['osoba_kontrolujaca']);

    if (mysqli_connect_errno()) {
         printf("<div class='alert alert-danger'><span class='glyphicon glyphicon-thumbs-down'></span>&nbsp;<strong>Uwaga!</strong>&nbspBrak połączenia z serwerem MySQL. Kod błędu: %s\n</div>.", mysqli_connect_error());
        } else {
                
                if ($stmt=$mysqli->prepare("SELECT WynikWeryfikacji FROM Karta_Kontroli_Separatora_Magnetycznego WHERE Godzina = STR_TO_DATE('08:00:00','%h:%i:%s') AND Linia=? AND Data=? "))
                {
                    $stmt->bind_param("ss", $linia,$data); 
                    $stmt->execute();
                    $stmt->bind_result($WYNIK);
                    $stmt->store_result();
                    $stmt->data_seek(0);
                    $wynik="";
                    if ($stmt -> fetch()){
                        $wynik=$WYNIK;
                    }
                         if ($wynik =='OK')
                         { 
                            echo "<div class='alert alert-warning'><strong>Ostrzeżenie!</strong>&nbsp Karta została już zweryfikowana!</div><br><br>"; 
                         } else
                            {
                                IF ($stmt = $mysqli -> prepare ("UPDATE Karta_Kontroli_Separatora_Magnetycznego SET WynikWeryfikacji=?,OsobaWeryfikujaca=? WHERE Linia=? AND Data=? AND Godzina = STR_TO_DATE('08:00:00','%h:%i:%s') LIMIT 1 "))
                                {
                                    $stmt->bind_param("ssss", $wynik_weryfikacji, $osoba_weryfikujaca,$linia,$data);
                                    $stmt->execute();

                                    if ($stmt -> affected_rows == 0 || $stmt -> affected_rows < 0 ||$stmt->affected_rows==NULL) {
                                        echo "<div class='alert alert-warning'><strong>Ostrzeżenie!</strong>&nbsp Nie dokonano weryfikacji. Możliwy błąd zapytania.</div><br>";
                                    }else {
                                            if ($stmt -> affected_rows > 0) {
                                                echo '<div class="alert alert-success alert-dismissable fade in">
                                                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                                <span class="glyphicon glyphicon-thumbs-up"></span>&nbsp;<strong>Sukces!</strong>&nbsp Dokonano weryfikacji karty kontroli. </div><br / >';

                                            }

                                        }

                                }else {
                                        echo '<div class="alert alert-danger"><strong>Info!</strong>&nbsp Błąd podczas zapisu do bazy danych.</div><br>';
                                    }
                            }
                }

            }


}
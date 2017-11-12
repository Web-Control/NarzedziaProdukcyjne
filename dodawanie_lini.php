<?php
session_start();
require_once ('funkcje.php');
/* Łączymy się z serwerem */
require_once ('polaczenie_z_baza.php');

   	$nazwa_lini = filtruj($_GET['nazwa_lini']);
	$mozna_dodac ="";
	
	if ($nazwa_lini == null) 
	{
		echo "<div class='alert alert-warning'><strong>Ostrzeżenie!</strong>&nbsp Podaj nazwę lini.</div><br><br>";
		
	} else 
		{
			
			$linie_w_bazie = array();
			//Pobieramy listę lini z bazy
			$linie_w_bazie = ListaLini($linie_w_bazie);
			$ilosc_lini=count($linie_w_bazie);
			
			
			//Sprawdzamy czy taka linia już istnieje
			
				for ($i=0; $i <$ilosc_lini+1 ; $i++)
					{

						if ($linie_w_bazie[$i]==$nazwa_lini)
							{
							$mozna_dodac=FALSE;
							break;
							}

						if ($i==$ilosc_lini)
							{
							$mozna_dodac=TRUE;
							}

					}

					
			
			if ($mozna_dodac == FALSE) 
			{
				
				echo "<div class='alert alert-warning'><strong>Ostrzeżenie!</strong>&nbsp Linia o podanej nazwie już istnieje!!! </div><br><br>";
				
			} else 
				{
					
					//Dodajemy nowy asortyment do listy asortymentu
					if ($stmt = $mysqli -> prepare("INSERT INTO Linie (NazwaLini) VALUES (?)"))
					{
						//echo "Wpisywanie do listy działa <br / >";
					$stmt -> bind_param("s",$nazwa_lini);
					$stmt -> execute();

					if ($stmt -> affected_rows == 0 || $stmt -> affected_rows < 0 ||$stmt->affected_rows==NULL)
							{
								echo "<div class='alert alert-warning'><span class='glyphicon glyphicon-alert'></span>&nbsp<strong>Ostrzeżenie!</strong>&nbsp Nie dokonano zapisu. Możliwy błąd zapytania.</div>";
							}

							if ($stmt -> affected_rows > 0)
							{
							
							echo '<div class="alert alert-success alert-dismissable fade in">
				  							<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
				 				 			<span class="glyphicon glyphicon-thumb-up"></span>&nbsp<strong>Sukces!</strong>&nbsp Dodano nową linię.</div><br / ><br>';	
								
							}
					}
					
				}
			
			
		
		
		}
?>
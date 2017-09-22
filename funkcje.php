<?php
//funkcja filtrująca dane
	function filtruj($zmienna)
	{
		$data = trim($zmienna);
		//usuwa spacje, tagi
		$data = stripslashes($zmienna);
		//usuwa slashe
		$data = htmlspecialchars($zmienna);
		//zamienia tagi html na czytelne znaki aby w formularzu nie wpisać szkodliwego kodu

		return $zmienna;
	}

// Konwersja UTF-8 -> ISO-8859-2
function Utf8ToIso($str)
{
    return iconv("utf-8", "iso-8859-2", $str);
}

// Konwersja ISO-8859-2 -> UTF-8
function IsoToUtf8($str)
{
    return iconv("iso-8859-2", "utf-8", $str);
}

//Robimy liste asortymentu. Zapytanie do bazy o obecny asortyment
	function ListaAsortymentu($tablica)
	{
	GLOBAL $mysqli;//Ze względu na Variable Scope
		if (mysqli_connect_errno()) {

			printf("<div class='alert alert-danger'><span class='glyphicon glyphicon-thumbs-down'></span>&nbsp;<strong>Uwaga!</strong>&nbspBrak połączenia z serwerem MySQL. Kod błędu: %s\n</div>", mysqli_connect_error());

			} else
			{
			if ($stmt = $mysqli -> prepare("SELECT Asortyment FROM AsortymentSuszu "))
				{
					$stmt -> execute();
					$stmt -> bind_result($Obecny_asortyment);
					$stmt -> store_result();
		
					if ($stmt->num_rows > 0)
						{
							/* Wyciągamy dane z zapytania sql i zapisujemy do tablicy  */
				    		while ($stmt->fetch())
							 {
								static $i=0;
								$tablica[$i]=$Obecny_asortyment;
								$i++;
				    		}
			    		}
		
						return $tablica;
				}
			}

	}

//Robimy liste użytkowników.
	function ListaUzytkownikow($tablica)
	{
		GLOBAL $mysqli;//Ze względu na Variable Scope

			if (mysqli_connect_errno()) {

			printf("<div class='alert alert-danger'><span class='glyphicon glyphicon-thumbs-down'></span>&nbsp;<strong>Uwaga!</strong>&nbspBrak połączenia z serwerem MySQL. Kod błędu: %s\n</div>", mysqli_connect_error());

			} else
				{
					if ($stmt = $mysqli -> prepare("SELECT Login FROM Uzytkownicy WHERE LOGIN NOT LIKE '%Daniel J.%' ORDER BY Login ASC"))
					{
						$stmt -> execute();
						$stmt -> bind_result($Uzytkownik);
						$stmt -> store_result();

						if ($stmt->num_rows > 0)
							{
								/* Wyciągamy dane z zapytania sql i zapisujemy do tablicy  */
					    		while ($stmt->fetch())
								 {
									static $a=0;
									$tablica[$a]=$Uzytkownik;
									$a++;
					    		}
								 
								 return $tablica;
				    		}

					}
				}

				
		}


//Funkcja znajduje ciąg znaków w innym ciągu znaków i zaznacza go kolorem
function nadaj_kolor_w_tresci($tresc,$znaki,$kolor)
	{
		$znaki_w_kolorze="<span style='color:$kolor'><b>$znaki</b></span>";
		$tresc=str_ireplace($znaki, $znaki_w_kolorze, $tresc);

		return $tresc;
	}

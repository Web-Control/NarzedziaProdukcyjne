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

//Robimy liste asortymentu. Zapytanie do bazy o obecny asortyment
	function asortyment($tablica)
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

//Robimy liste użytkowników.
	function uzytkownicy($tablica)
	{
	if ($stmt = $mysqli -> prepare("SELECT Login FROM Uzytkownicy"))
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
	    		}

				return $tablica;
		}

	}

//Funkcja znajduje ciąg znaków w innym ciągu znaków i zaznacza go kolorem
function nadaj_kolor_w_tresci($tresc,$znaki,$kolor)
	{
		$znaki_w_kolorze="<span style='color:$kolor'><b>$znaki</b></span>";
		$tresc=str_ireplace($znaki, $znaki_w_kolorze, $tresc);

		return $tresc;
	}

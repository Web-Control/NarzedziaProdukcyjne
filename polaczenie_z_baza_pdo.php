<?php
/* Łączymy się z serwerem */

try {
$db = new PDO('mysql:host=mysql530int.cp.az.pl;dbname=db6001900_RaportyWilgotnosci', 'u6001900_szymon', 'mNa5YWLL');
$db -> query ('SET NAMES utf8');
$db -> query ('SET CHARACTER_SET utf8_unicode_ci');
}

catch (PDOException $e)
{
 echo "<div class='alert alert-danger'><span class='glyphicon glyphicon-thumbs-down'></span>&nbsp<strong>Uwaga!</strong>&nbspBrak połączenia z serwerem MySQL !!! Błąd: ". $e->getMessage()."</div>";
 die();
}
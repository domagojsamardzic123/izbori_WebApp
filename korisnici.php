  <?php
    header('Content-Type: text/html; charset=utf-8');
    include ("zaglavlje.php");
    $bp=spojiSeNaBazu();
    ini_set('default_charset','UTF-8');
?>


<!DOCTYPE html>
<html>

<head>
    <link href="stil.css" rel="stylesheet" type="text/css"/>
    <meta charset="UTF-8"/>
</head>

<body>

<?php

  if(isset($_SESSION['aktivni_korisnik']) && $_SESSION['aktivni_korisnik_tip']==1)
  {
    $utf8=izvrsiUpit($bp,"SET NAMES utf8;");
    $upit_korisnici="SELECT * FROM korisnik ORDER BY tip_korisnika_id ASC, prezime ASC";
    $rezultat_korisnici=izvrsiUpit($bp,$upit_korisnici);
    echo "<div class='prostornaslova'>";
    echo "<table width='100%'><thead></thead><tbody><tr><td>";
    echo "<form method='POST' action='uredi_korisnika.php'>";
    echo "<button class='dugme_dodaj' type='submit' name='novikorisnik'>NOVI KORISNIK</button>";
    echo "</form>";
    echo "</td></tr></tbody></table>";
    echo "<h2>Pregled korisnika</h2>";
    echo "</div>";
    echo "<table class='tablica_izbornamjesta'>";
    echo "<thead><th><h2>Ime</h2></th><td><h2>Prezime</h2></th><th><h2>Tip korisnika</h2></th><th><h2>E-mail</h2></th><td><h2>Korisničko ime</h2></th></thead>";
    echo "<tbody>";
    foreach($rezultat_korisnici as $kor)
    {
        echo "<tr>";
        echo "<td><strong>".$kor['ime']."</strong></td>";
        echo "<td><strong>".$kor['prezime']."</strong></td>";
        if($kor['tip_korisnika_id']==1)
        {
        echo "<td>"."ADMINISTRATOR"."</td>";
        }
        elseif($kor['tip_korisnika_id']==2)
        {
        echo "<td>"."VODITELJ"."</td>";
        }
        elseif($kor['tip_korisnika_id']==3)
        {
        echo "<td>"."KORISNIK"."</td>";
        }
        echo "<td>".$kor['email']."</td>";
        echo "<td>".$kor['korisnicko_ime']."</td>";
        echo "<td><a class='dugme_uredi_obrisi' href='uredi_korisnika.php?id_korisnik=$kor[korisnik_id]'>UREDI</a></td>";
        echo "<td><a class='dugme_uredi_obrisi' href='obrisi_korisnika.php?id_korisnik=$kor[korisnik_id]'>OBRIŠI</a></td>";
        echo "</tr>";
    }
  }

?>

</body>

</html>
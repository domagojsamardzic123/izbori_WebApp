<?php
    include ("zaglavlje.php");
    $bp=spojiSeNaBazu();
?>

<?php
  $utf8=izvrsiUpit($bp,"SET NAMES utf8;");
 $upit="SELECT izborno_mjesto_id, moderator_id, naziv, opis FROM izborno_mjesto ORDER BY izborno_mjesto.naziv";
 $rez=izvrsiUpit($bp,$upit);

 function provjeramoderatora($bp)
 {
     $upit="SELECT izborno_mjesto_id FROM izborno_mjesto WHERE moderator_id='$_SESSION[aktivni_korisnik_id]'";
     $rezultat=izvrsiupit($bp,$upit);
     return $rezultat;
 }
?>
<!DOCTYPE html>
<html>

<head>
    
</head>
<body>
    <?php
    echo "<div class='prostornaslova'>";
        if(isset($_SESSION['aktivni_korisnik']))
        {
            if($_SESSION['aktivni_korisnik_tip']==1)
            {
                echo "<form class='prostor_zadugme' method='POST' action='uredi_izborno_mjesto.php'>";
                echo "<table width=100%><thead></thead><tbody><tr><td>";
                echo "<button class='dugme_dodaj' type='submit' name='novoizbornomjesto'>NOVO IZBORNO MJESTO</button>";
                echo "</td></tr></tbody></table>";
                echo "</form>";
            }
        }
        echo "<p id='naslov_tablicaprijava'>Izborna mjesta</h3>";
        echo "</div>";
        echo "<table class='tablica_izbornamjesta'>";
        echo "<thead></thead>";
        echo "<tbody>";
        while (list($izbmid,$modid,$naziv,$opis)=mysqli_fetch_array($rez))
        {
            echo "<tr id='red_izbori'>";

            echo "<td><a href='izbori.php?id_izbornomjesto=$izbmid'>".$naziv."</a></td>";
            echo "<td>".$opis."</td>";
            
            
            if(isset($_SESSION['aktivni_korisnik']))
            {
                if($_SESSION['aktivni_korisnik_tip']==1)
                {
                    echo "<td ><a class='dugme_uredi_obrisi' href='uredi_izborno_mjesto.php?id_izbornomjesto=$izbmid'>UREDI</a></td>";
                }
            }
            
            echo "</tr>";
        }
        echo "</tbody>";
        echo "</table>";
    
    
    ?>

</body>

</html>
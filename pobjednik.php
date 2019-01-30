<?php
    include ("zaglavlje.php");
    $veza=spojiSeNaBazu();
?>
<?php
    $upit_pobjednik="SELECT * FROM korisnik k, kandidat t WHERE k.korisnik_id=t.korisnik_id AND t.izbor_id = '$_GET[id_izbor]' AND t.status = 'P'";
    $rezultat_pobjednik=izvrsiUpit($veza,$upit_pobjednik);


    $pobjednik = mysqli_fetch_array($rezultat_pobjednik);

        echo "<div class='pobjednikizbora'>";    
        if (!isset($_GET['id_izbor']) || $pobjednik=="")
        {
            echo "<h2>Pobjednik izbora nije pronađen!</h2>"."<br>";
        }
        else
        {
            echo "<div class='slika_i_ime'>";
            echo "<img src='korisnici/".$pobjednik["korisnicko_ime"].".jpg' width=300px height=400px/>";
            echo "<h1>" . $pobjednik["ime"] . " " . $pobjednik["prezime"] . "</h1>";
            echo "</div>";
            echo "<h2>" . $pobjednik["email"] . "</h2>";
            echo "<p>" . $pobjednik["zivotopis"] . "</p>";
            echo "<video src='$pobjednik[video]' width='640' height='480' controls></video>"; 

        }
        echo "</div>";

?>

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
    
    $utf8=izvrsiUpit($bp,"SET NAMES utf8;");
    $upit_korisnik="SELECT ime, prezime,slika FROM korisnik WHERE korisnik_id='$_GET[id_korisnik]'";
    $rezultat_korisnik=izvrsiUpit($bp,$upit_korisnik);
    $korisnik=mysqli_fetch_array($rezultat_korisnik);
    echo "<form class='prijava' method='POST' style='text-align:center' >";
    echo "<h4>Jeste li sigurni da želite obrisati korisnika: </h4>";
    echo "<h3>".$korisnik['ime']." ".$korisnik['prezime']." ?</h3>";
    $slika=$korisnik['slika'];
    echo "<span><input class='dugme_filtriraj_resetiraj' type='submit' name='yes' value='Da'></input><input class='dugme_filtriraj_resetiraj' type='submit' name='no' value='Ne'></input>";
    echo "</form>";
    echo "</div>";

    if(isset($_POST['yes']))
    {
        $utf8=izvrsiUpit($bp,"SET NAMES utf8;");
        $sql_obrisikorisnika="DELETE FROM korisnik WHERE korisnik_id='$_GET[id_korisnik]'";
        $slika=$korisnik['slika'];
        if(file_exists($slika)) unlink($slika);
        $rez_obrisikorisnika=izvrsiUpit($bp,$sql_obrisikorisnika);
        if(!(empty($rez_obrisikorisnika)) && !(file_exists($slika)))
        {
            $uspjesno_poruka="Korisnik uspješno obrisan!";
            header("Refresh:2 URL=korisnici.php");
        }
    }
    if(isset($_POST['no']))
    {
        header("Location:korisnici.php");
    }
    if(isset($uspjesno_poruka))
    {
        echo "<table width='100%'><tr><td colspan='2' class='uspjesno'>$uspjesno_poruka</td></tr></table>";
    }

?>


</body>

</html>
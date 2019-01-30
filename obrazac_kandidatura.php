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
    echo "<div class='prostornaslova'>";  
    echo "<h1>Dobrodošli na obrazac za kandidaturu</h1>";
    echo "</div>";
    if(isset($_GET['izbor_id']) && isset($_GET['korisnik_id']))
    {
        $idizbora=$_GET['izbor_id'];
        $idkor=$_GET['korisnik_id'];
        $upit="SELECT ime, prezime, email, slika FROM korisnik WHERE korisnik_id=$idkor";
        $rezultat=izvrsiUpit($bp, $upit);
        while(list($im, $prez, $mail, $sli)=mysqli_fetch_array($rezultat)){
            $ime=$im;
            $prezime=$prez;
            $email=$mail;
            $slika=$sli;
        }

        echo "<div class='pobjednikizbora'>";
        echo "<table class='prijava_tablica'><thead></thead><tbody>";
        echo "<tr><td colspan='2'><img src='$slika' width=300px height=400px/></td></tr>";
        echo "<tr><td colspan='2'><h2>$ime $prezime</h2></td></tr>";
        echo "<tr><td colspan='2'><h3>$email</h3></td></tr>";
        echo "<form class='prijava' method='post'>";
        echo "<tr><td class='prijavalabela'>Zivotopis</td><td><textarea class='textarea' name='zivotopis' placeholder='molimo dodajte svoj životopis'></textarea></td></tr>";
        echo "<tr><td class='prijavalabela'>Službeni video kampanje</td><td>";
        echo "<input class='input_forma' name='url' type='text' placeholder='zaljepite link promotivnog videa kampanje'></input></td></tr>";
        echo "<tr><td colspan='2'><input class='dugme_forma' type='submit' name='potvrdikandidaturu' value='Potvrdi kandidaturu'/></td></tr></tbody></table>";
        echo "</form>";
        echo "</div>";

        if(isset($_POST['potvrdikandidaturu']))
        {
            if(!empty($_POST['zivotopis']))
            {
                if(!empty($_POST['url']))
                {
                    $upitzakandidaturu="INSERT INTO kandidat(korisnik_id, izbor_id, zivotopis, video, status) VALUES ('$_GET[korisnik_id]', '$_GET[izbor_id]', '$_POST[zivotopis]', '$_POST[url]', 'K')";
                    $rezultat=izvrsiUpit($bp,$upitzakandidaturu);
                    $promjenjeno=mysqli_affected_rows($bp);
                    $id_izbora=$_GET['izbor_id'];
                    if($promjenjeno>0)
                    {
                        echo "Uspješno ste se kandidirali!";
                        header("Location:izbor.php?id_izbor=$id_izbora");
                    }
                    else echo "Nešto je pošlo po krivu!";
                    

                }
                else echo "Url je obavezan za upisati";
                
            }     
            else echo "Životopis je obavezan za popuniti!";   
        }
    }  
    ?>

</body>

</html>
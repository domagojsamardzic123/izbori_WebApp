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
$sql_moderatori="SELECT korisnik.korisnik_id, korisnik.ime, korisnik.prezime FROM korisnik WHERE tip_korisnika_id=2";
$moderatori=izvrsiUpit($bp,$sql_moderatori);

   
if(isset($_SESSION['aktivni_korisnik']) && $_SESSION['aktivni_korisnik_tip']==1)
{

    if(isset($_GET['id_izbornomjesto']))
    {
        $utf8=izvrsiUpit($bp,"SET NAMES utf8;");
        if (isset($_POST['urediizbmj']))
        {
            if (empty($_POST['moderator'])) 
            {
                $modErr = "Moderator se mora unijeti!";
            } 
            else 
            {
                $izbmj_mod = $_POST['moderator'];
                $modErr="";
            }
            if (empty($_POST['naziv'])) 
            {
                $nazivErr = "Naziv izbornog mjesta je obavezno polje";
            } 
            else 
            {
                $izbmj_naziv = $_POST['naziv'];
                $nazivErr="";
            }
            
            if (empty($_POST['opis'])) 
            {
                $opisErr= "U opis unesite lokaciju izbornog mjesta";
            } 
            else 
            {
                $izbmj_opis=$_POST['opis'];
                $opisErr="";
            }

            if(empty($modErr) && empty($nazivErr) && empty($opisErr))
            {      
                
                $sql_urediizbmj="UPDATE izborno_mjesto SET moderator_id='$izbmj_mod', naziv='$izbmj_naziv',opis='$izbmj_opis' WHERE izborno_mjesto_id='$_GET[id_izbornomjesto]'";
                $rez_urediizbmj=izvrsiUpit($bp,$sql_urediizbmj);
                
                if(!(empty($rez_urediizbmj)))
                        {
                            $uspjesno_poruka="USPJEŠNO STE UREDILI POSTOJEĆE IZBORNO MJESTO";
                            header("Refresh: 2 URL=izborna_mjesta.php");
                        }
                else
                    header("Location:uredi_izborno_mjesto.php");
            }
        }
        $utf8=izvrsiUpit($bp,"SET NAMES utf8;");
        $upit_urediizbmj="SELECT * FROM izborno_mjesto WHERE izborno_mjesto_id='$_GET[id_izbornomjesto]'";
        $rezultat_izbmj=izvrsiUpit($bp,$upit_urediizbmj);
        $izbmj=mysqli_fetch_array($rezultat_izbmj);
        echo "<div class='prostornaslova'>";
        echo "<h2>Uredi izborno mjesto: </h2><h3>$izbmj[naziv]</h3>";
        echo "</div>";
        echo "<form class='prijava' method='POST'>";
        echo "<table class='prijava_tablica'>";
        echo "<thead></thead><tbody>";
        echo "<tr><td class='prijavalabela'>Moderator</td><td>";
        echo "<select class='select' name='moderator'>";
            while(list($id,$ime,$prez)=mysqli_fetch_array($moderatori))
            {
                echo "<option id='opcija' value='$id'";
                if($id==$izbmj['moderator_id']) echo "selected";
                echo">".$ime." ".$prez."</option>";
            }
        echo "</select>";
        echo "</td>";
        echo "</tr>";
        echo "<tr><td class='prijavalabela'>Naziv izbora</td><td><input class='input_forma' name='naziv'type='text' value='$izbmj[naziv]'></input></td></tr>";
        if(!empty($nazivErr))
        {
            echo "<tr><td class='upozorenje' colspan='2'>$nazivErr</td></tr>";
        }
        echo "<tr><td class='prijavalabela'>Opis izbora</td><td><textarea class='textarea' name='opis'type='text'>$izbmj[opis]</textarea></td></tr>";
        if(!empty($opisErr))
        {
            echo "<tr><td class='upozorenje' colspan='2'>$opisErr</td></tr>";
        }
        
        if(isset($uspjesno_poruka))
        {
            echo "<tr><td colspan='2' class='uspjesno'>$uspjesno_poruka</td></tr>";
        }
        else
        {
            echo "<tr><td colspan='2'><button type='submit' class='dugme_forma' name='urediizbmj'>Uredi izborno mjesto</button></td></tr>";
        }

        echo "</tbody></table>";
        echo "</form>";

    }   

    else
    {
        if (isset($_POST['urediizbmj']))
        {
            $utf8=izvrsiUpit($bp,"SET NAMES utf8;");
            if (empty($_POST['moderator'])) 
            {
                $modErr = "Moderator je obavezno polje";
            } 
            else 
            {
                $izbmj_mod = $_POST['moderator'];
                $modErr="";
            }
            if (empty($_POST['naziv'])) 
            {
                $nazivErr = "Naziv izbora je obavezno polje";
            } 
            else 
            {
                $izbmj_naziv = $_POST['naziv'];
                $nazivErr="";
            }
            
            if (empty($_POST['opis'])) 
            {
                $opisErr= "U opis unesite lokaciju izbornog mjesta";
            } 
            else 
            {
                $izbmj_opis=$_POST['opis'];
                $opisErr="";
            }

            if(empty($modErr) && empty($nazivErr) && empty($opisErr))
            {      
                $sql_dodajizbmj="INSERT INTO izborno_mjesto (moderator_id, naziv, opis) VALUES ('$izbmj_mod','$izbmj_naziv','$izbmj_opis')";
                $rez_dodajizbmj=izvrsiUpit($bp,$sql_dodajizbmj);
                
                if(!(empty($rez_dodajizbmj)))
                {
                    $uspjesno_poruka="USPJEŠNO STE DODALI NOVO IZBORNO MJESTO";
                    header("Refresh: 2 URL=izborna_mjesta.php");
                }
                else
                    header("Location:uredi_izborno_mjesto.php");
            }
        }
        
        $utf8=izvrsiUpit($bp,"SET NAMES utf8;");
        echo "<div class='prostornaslova'>";
        echo "<h2>Obrazac za dodavanje novog izbornog mjesta</h2>";
        echo "</div>";
        echo "<form class='prijava' method='POST'>";
        echo "<table class='prijava_tablica'>";
        echo "<thead></thead><tbody>";
        echo "<tr><td class='prijavalabela'>Moderator</td><td>";
        echo "<select class='select' name='moderator'>";
            while(list($id,$ime,$prez)=mysqli_fetch_array($moderatori))
            {
                echo "<option id='opcija' value='$id'>".$ime." ".$prez."</option>";
            }
        echo "</select>";
        echo "</td>";
        echo "</tr>";
        echo "<tr><td class='prijavalabela'>Naziv izbornog mjesta</td><td><input class='input_forma' name='naziv' type='text'></input></td></tr>";
        if(!empty($nazivErr))
        {
            echo "<tr><td class='upozorenje' colspan='2'>$nazivErr</td></tr>";
        }
        echo "<tr><td class='prijavalabela'>Opis izbornog mjesta</td><td><textarea class='textarea' name='opis' type='text'></textarea></td></tr>";
        if(!empty($opisErr))
        {
            echo "<tr><td class='upozorenje' colspan='2'>$opisErr</td></tr>";
        }

        if(isset($uspjesno_poruka))
        {
            echo "<tr><td colspan='2' class='uspjesno'>$uspjesno_poruka</td></tr>";
        }
        else
        {
            echo "<tr><td colspan='2'><button class='dugme_forma' type='submit' name='urediizbmj'>Dodaj izborno mjesto</button></td></tr>";
        }
        echo "</tbody></table>";
        echo "</form>";
    }

}
?>


</body>

</html>
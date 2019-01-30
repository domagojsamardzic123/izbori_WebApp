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
if(isset($_SESSION['aktivni_korisnik']) && ($_SESSION['aktivni_korisnik_tip']==2 || $_SESSION['aktivni_korisnik_tip']==1))
{

    if(isset($_GET['izbor_id']))
    {
        $utf8=izvrsiUpit($bp,"SET NAMES utf8;");
        if(isset($_POST['urediizbor']))
        {
            if(isset($_POST['naziv_izbornog_mjesta']) && $_POST['naziv_izbornog_mjesta']!=="")
            {
                $izbmjErr="";
            }
            else
            {
                $izbmjErr="Potrebno je unijeti naziv izbornog mjesta!";
            }

            if(isset($_POST['naziv_izbora']) && $_POST['naziv_izbora']!=="")
            {
                $izbErr="";
            }
            else
            {
                $izbErr="Potrebno je unijeti naziv izbora!";
            }

            if(isset($_POST['datum_pocetak']) && $_POST['datum_pocetak']!=="")
            {
                if (DateTime::createFromFormat('d.m.Y. H:i:s', $_POST['datum_pocetak']) !== FALSE)
                {   
                    $dvpErr="";
                }
                else
                {
                    $dvpErr="Pravilan format datuma je: dd.mm.gggg. hh:mm:ss";
                }
            }
            else
            {
                $dvpErr="Potrebno je unijeti datum!";
            }

            if(empty($izbmjErr) && empty($izbErr) && empty($dvpErr))
            {
                $izborno_mjesto=$_POST['naziv_izbornog_mjesta'];
                $naziv_izbora=$_POST['naziv_izbora'];
                $datum_pocetak=date("Y-m-d H:i:s", strtotime($_POST['datum_pocetak']));
                $datum_zavrsetak=date("Y-m-d H:i:s",strtotime("+ 7 days",strtotime($_POST['datum_pocetak'])));
                $opis_izbora=$_POST['opis_izbora'];
                if(isset($_GET['ponovljeni']) && $_GET['ponovljeni']==1)
                {
                    $sql="INSERT INTO izbor (izbor_id,izborno_mjesto_id,naziv,datum_vrijeme_pocetka,datum_vrijeme_zavrsetka,opis) VALUES ('','$izborno_mjesto','$naziv_izbora','$datum_pocetak','$datum_zavrsetak','$opis_izbora')";
                }
                else $sql="UPDATE izbor SET izborno_mjesto_id='$izborno_mjesto', naziv='$naziv_izbora', datum_vrijeme_pocetka='$datum_pocetak', datum_vrijeme_zavrsetka='$datum_zavrsetak', opis='$opis_izbora' WHERE izbor_id='$_GET[izbor_id]'";
                $rez_urediizbor=izvrsiUpit($bp,$sql);
                if(!(empty($rez_urediizbor)))
                        {
                            $uspjesno_poruka="USPJEŠNO STE UREDILI POSTOJEĆE IZBORE";
                            header("Refresh: 2 URL=upravljanje_izborima.php");
   
                        }
                        else
                            header("Location:uredi_izbor.php");
            }
            
        }

        
        $utf8=izvrsiUpit($bp,"SET NAMES utf8;");
        $upit_urediizbor="SELECT izbor.naziv as izb_naz, izbor.datum_vrijeme_pocetka, izbor.datum_vrijeme_zavrsetka, izbor.opis as izb_opis, izborno_mjesto.naziv as izbmj_naz, izborno_mjesto.opis as izbmj_opis FROM izbor , izborno_mjesto  where izbor.izborno_mjesto_id=izborno_mjesto.izborno_mjesto_id AND izbor.izbor_id='$_GET[izbor_id]'";
        $forma_izbor=izvrsiUpit($bp,$upit_urediizbor);
        $izbor_pod=mysqli_fetch_array($forma_izbor);
        echo "<div class='prostornaslova'>";
        echo "<h2>Uredi izbor: </h2><h3>$izbor_pod[izb_naz], $izbor_pod[izbmj_naz]</h3>";
        echo "</div>";
        if($_SESSION['aktivni_korisnik_tip']==2)
        {
            $upit_izbornamjesta="SELECT DISTINCT izborno_mjesto.izborno_mjesto_id, izborno_mjesto.naziv FROM izborno_mjesto , izbor  WHERE izborno_mjesto.izborno_mjesto_id = izbor.izborno_mjesto_id AND izborno_mjesto.moderator_id = '$_SESSION[aktivni_korisnik_id]' ORDER BY izborno_mjesto.naziv ASC";
        }
        else
        {
            $upit_izbornamjesta="SELECT DISTINCT izborno_mjesto.izborno_mjesto_id, izborno_mjesto.naziv FROM izborno_mjesto , izbor  WHERE izborno_mjesto.izborno_mjesto_id = izbor.izborno_mjesto_id ORDER BY izborno_mjesto.naziv ASC";
        }
        
        $rezultat_izbornamjesta=izvrsiUpit($bp,$upit_izbornamjesta);

        echo "<form class='prijava' method='post'>";
        echo "<table class='prijava_tablica'><thead></thead><tbody>";
        echo "<tr><td class='prijavalabela'>Izborno mjesto: </td><td>";
        echo "<select class='select' name='naziv_izbornog_mjesta'>";
        while(list($id,$naziv)=mysqli_fetch_array($rezultat_izbornamjesta))
        {
            echo "<option name='naziv_izbornog_mjesta' value='$id' ";
            if($naziv==$izbor_pod['izbmj_naz'])
            {
                echo "selected";
            }
            echo ">$naziv</option>";
            
        }

        echo "</select></td></tr>";
        echo "<tr><td class='prijavalabela'>Naziv izbora: </td><td><input class='input_forma' name='naziv_izbora' type='text' size='25' value='$izbor_pod[izb_naz]'/></td></tr>";
        if(!(empty($izbErr)))
        {
            echo "<tr><td colspan='2' class='upozorenje'>$izbErr</td></tr>";
        }
        if(isset($_GET['ponovljeni']) && $_GET['ponovljeni']==1)
        {
            $datum_pocetka=date("d.m.Y. H:i:s");
        }
        else
        {
            $datum_pocetka=date("d.m.Y. H:i:s",strtotime($izbor_pod['datum_vrijeme_pocetka']));
        }
        if(!(empty($dvpErr)))
        {
            echo "<tr><td colspan='2' class='upozorenje'>$dvpErr</td></tr>";
        }
        echo "<tr><td class='prijavalabela'>Datum početka: </td><td><input class='input_forma' name='datum_pocetak' type='text' value='$datum_pocetka'/></td><tr>";
        
        echo "<tr><td class='prijavalabela'>Datum završetka: </td><td><input class='input_forma' name='datum_zavrsetak' type='text' size='42' value='AUTOMATSKI : 7 dana od vremena početka' readonly/></td><tr>";
        
        echo "<tr><td class='prijavalabela'>Opis: </td><td><textarea class='textarea' name='opis_izbora' width='500' height='250' >".$izbor_pod['izb_opis']."</textarea></td></tr>";

        if(isset($uspjesno_poruka))
        {
            echo "<tr><td colspan='2' class='uspjesno'>$uspjesno_poruka</td></tr>";
        }
        else
        {
            echo "<tr><td colspan='2'><input type='submit' class='dugme_forma' value='Uredi izbor' name='urediizbor'/></td></tr>";
        }
        
        echo "</form>";
    }
    
    
    else
    {   
        
        $utf8=izvrsiUpit($bp,"SET NAMES utf8;");

        if (isset($_POST['unesiizbor'])) 
        { 
            
            $izbmj=$_POST['naziv_izbornog_mjesta'];


            if(isset($_POST['naziv_izbora']) && $_POST['naziv_izbora']!=="")
            {
                $izbErr="";
                if (is_string($_POST['naziv_izbora']))
                {   
                    $izbErr="";
                    $izb = $_POST['naziv_izbora'];
                }
                else
                {
                    $izbErr="Pravilno unesite naziv izbora!";
                    $izb="";
                }
            }
            else
            {
                $izbErr="Potrebno je unijeti naziv izbora!";
                $izb="";
            }


            if(isset($_POST['datum_pocetak']) && $_POST['datum_pocetak']!=="")
            {
                if (DateTime::createFromFormat('d.m.Y. H:i:s', $_POST['datum_pocetak']) !== FALSE)
                {   
                    $dvpErr="";
                    $dvp = date("Y-m-d H:i:s",strtotime($_POST['datum_pocetak']));
                    $dvz = date("Y-m-d H:i:s",strtotime("+ 7 days",strtotime($_POST['datum_pocetak'])));
                }
                else
                {
                    $dvp="";
                    $dvz="";
                    $dvpErr="Pravilan format datuma je: dd.mm.gggg. hh:mm:ss";
                }
            }
            else
            {
                $dvp="";
                $dvz="";
                $dvpErr="Potrebno je unijeti datum!";
            }

            
            if(empty($izbErr) && empty($dvpErr))
            {
                if(is_string($_POST['naziv_izbora']))
                {
                    if (DateTime::createFromFormat("d.m.Y. H:i:s", $_POST['datum_pocetak']) !== FALSE) 
                    {
                        $sql2="INSERT INTO izbor (izbor_id,izborno_mjesto_id,naziv,datum_vrijeme_pocetka,datum_vrijeme_zavrsetka,opis) VALUES ('','$izbmj','$izb','$dvp','$dvz','$_POST[opis_izbora]')";
                        $rez_unesiizbor=izvrsiUpit($bp,$sql2);
                        if(!(empty($rez_unesiizbor)))
                        {
                            $uspjesno_poruka="USPJEŠNO STE DODALI NOVE IZBORE";
                            header("Refresh: 2 URL=upravljanje_izborima.php");
                        }
                        else
                            header("Location:uredi_izbor.php");
                    }
                    else ;
                    
                }
            
            }
        }


        echo "<div class='prostornaslova'>";
            echo "<h2>Forma za unos novog izbora</h2>";
        echo "</div>";
        
        echo "<form class='prijava' method='POST'>";
        echo "<table class='prijava_tablica'><thead></thead><tbody>";
        echo "<tr><td class='prijavalabela'>Izborno mjesto: </td><td>";
        echo "<select class='select' name='naziv_izbornog_mjesta' onkeyup='saveValue(this);'>";
        if($_SESSION['aktivni_korisnik_tip']==2)
        {
            $upit_mjesta="SELECT izborno_mjesto_id, naziv FROM izborno_mjesto  WHERE izborno_mjesto.moderator_id = '$_SESSION[aktivni_korisnik_id]' ORDER BY izborno_mjesto.naziv ASC";
        }
        if($_SESSION['aktivni_korisnik_tip']==1)
        {
            $upit_mjesta="SELECT izborno_mjesto_id, naziv FROM izborno_mjesto ORDER BY izborno_mjesto.naziv ASC";
        }
        
        $izbornamjesta=izvrsiUpit($bp,$upit_mjesta);
        if(isset($_GET['ponovljeni']))
        {
            echo "Ovo su ponovljeni izbori!";
        }
        while(list($id,$naziv)=mysqli_fetch_array($izbornamjesta))
        {
            echo "<option value='$id'>$naziv</option>";
        }
        echo "</select></td></tr>";
        echo "<tr><td class='prijavalabela'>Naziv izbora: </td><td><input value='";
        if(isset($_POST['naziv_izbora'])) echo $_POST['naziv_izbora'];
        echo "' class='input_forma' name='naziv_izbora' type='text' size='25'/></td></tr>";
        if(!(empty($izbErr)))
        {
            echo "<tr><td class='upozorenje' colspan='2'>$izbErr</td></tr>";
        }

        echo "<tr><td class='prijavalabela'>Datum početka: </td><td><input value='";
        if(isset($_POST['datum_pocetak'])) echo $_POST['datum_pocetak'];
        echo "'class='input_forma' name='datum_pocetak' type='text'/></td><tr>";
        if(!(empty($dvpErr)))
        {
            echo "<tr><td class='upozorenje' colspan='2'>$dvpErr</td></tr>";
        }
        echo "<tr><td class='prijavalabela'>Datum završetka: </td><td><input class='input_forma' name='datum_zavrsetak' type='text' size='42' value='AUTOMATSKI SE RAČUNA : 7 dana od vremena početka' readonly/></td><tr>";
        echo "<tr><td class='prijavalabela'>Opis: </td><td><textarea class='textarea' name='opis_izbora' width='500' height='250' >";
        if(isset($_POST['opis_izbora'])) echo $_POST['opis_izbora'];
        echo "</textarea>";

        if(isset($uspjesno_poruka))
        {
            echo "<tr><td colspan='2' class='uspjesno'>$uspjesno_poruka</td></tr>";
        }
        else
        {
            echo "<tr><td colspan='2'><input type='submit' class='dugme_forma' value='Kreiraj izbor' name='unesiizbor'/></td></tr>";
        }
        
        echo "</tbody></table>";
        echo "</form>";
    }
    
}
?>


</body>

</html>
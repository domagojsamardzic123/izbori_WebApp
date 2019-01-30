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

    if($_SESSION['aktivni_korisnik_tip']==2)
    {
        $upit_izbornamjesta_otvorena="SELECT * FROM izborno_mjesto m, izbor i
        WHERE m.izborno_mjesto_id = i.izborno_mjesto_id
        AND m.moderator_id = '$_SESSION[aktivni_korisnik_id]' AND i.datum_vrijeme_zavrsetka>CURRENT_TIMESTAMP() ORDER BY i.datum_vrijeme_zavrsetka DESC;";
        $upit_izbornamjesta_zatvorena="SELECT * FROM izborno_mjesto m, izbor i
        WHERE m.izborno_mjesto_id = i.izborno_mjesto_id
        AND m.moderator_id = '$_SESSION[aktivni_korisnik_id]' AND i.datum_vrijeme_zavrsetka<CURRENT_TIMESTAMP() ORDER BY i.datum_vrijeme_zavrsetka DESC;";
    }
    elseif($_SESSION['aktivni_korisnik_tip']==1)
    {
        $upit_izbornamjesta_otvorena="SELECT * FROM izborno_mjesto m, izbor i
        WHERE m.izborno_mjesto_id = i.izborno_mjesto_id
        AND i.datum_vrijeme_zavrsetka>CURRENT_TIMESTAMP() ORDER BY i.datum_vrijeme_zavrsetka DESC;";
        $upit_izbornamjesta_zatvorena="SELECT * FROM izborno_mjesto m, izbor i
        WHERE m.izborno_mjesto_id = i.izborno_mjesto_id 
        AND i.datum_vrijeme_zavrsetka<CURRENT_TIMESTAMP() ORDER BY i.datum_vrijeme_zavrsetka DESC;";
    }
    
    echo "<div class='prostornaslova'>";
    
    if(isset($_SESSION['aktivni_korisnik']) && ($_SESSION['aktivni_korisnik_tip']==2 || $_SESSION['aktivni_korisnik_tip']==1))
    {
        if($_SESSION['aktivni_korisnik_tip']==1)
        {
            $upit_izbornamjesta="SELECT * FROM izborno_mjesto ORDER BY naziv ASC";
        }
        else
        {
            $upit_izbornamjesta="SELECT * FROM izborno_mjesto WHERE moderator_id = '$_SESSION[aktivni_korisnik_id]' ORDER BY naziv ASC";
        }
        $rezultat_izbornamjesta=izvrsiUpit($bp,$upit_izbornamjesta);
        $brojizbornihmjesta=mysqli_num_rows($rezultat_izbornamjesta);

        
        
        if($brojizbornihmjesta>0)
        {
            echo "<form action='uredi_izbor.php' method='POST'>";
            echo "<table width='100%'><thead></thead><tbody><tr><td>";
            echo "<input class='dugme_dodaj' name='kreirajizbor' type='submit' value='NOVI IZBOR'/>";
            echo "</td></tr></tbody></table>";
            echo "</form>";

            echo "<table class='meni_izbornamjesta'>";
            echo "<thead></thead><tbody><tr>";
            
            while(list($id,$modid,$izbmj,$opis)=mysqli_fetch_array($rezultat_izbornamjesta))
            {
                
                echo "<td id='meni_izbornomjesto' width='calc(100%/$brojizbornihmjesta);'><h3>".$izbmj."</h3></td>";
            }

            echo "</tr></tbody></table><br>";
        }
        elseif($brojizbornihmjesta==0)
        {
            echo "<table class='meni_izbornamjesta'>";
            echo "<thead></thead><tbody><tr>";
            echo "<td id='meni_izbornomjesto'><h3>Nemate svojih izbornih mjesta</h3></td></tr></tbody></table>";
        }

    }
    
    echo "</div>";
    
    $rezultat_izbori_otvoreni=izvrsiUpit($bp,$upit_izbornamjesta_otvorena);
    echo "<table class='tablica_izbornamjesta'>";
    echo "<thead><th><h2>Naziv izbora</h2></th><th><h2>Izborno mjesto</h2></th><th><h2>Početak izbora</h2></th><th><h2>Završetak izbora</h2></th>";
    echo "</thead>";
    echo "<tbody>";
    echo "<tr><td colspan='4'><h3 id='naslov'>Vaši otvoreni izbori:</h3></td></tr>";
    while(list($a,$b,$izbmj,$opis,$c,$d,$izbnaziv,$dvp,$dvz,$izbopis)=mysqli_fetch_array($rezultat_izbori_otvoreni))
    {
        echo "<tr>";
        echo "<td><a href='izbor.php?id_izbor=$c'>".$izbnaziv."</a></td>";
        echo "<td><a href='izbori.php?id_izbornomjesto=$a'>".$izbmj."</a></td>";
        echo "<td>".date("d.m.Y. H:i:s",strtotime($dvp))."</td>";
        echo "<td>".date("d.m.Y. H:i:s",strtotime($dvz))."</td>";
        echo "<td>"."<a class='dugme_uredi_obrisi' href='uredi_izbor.php?izbor_id=$c'>UREDI</a></td>";
        echo "</tr>";
    }

    $rezultat_izbori_zatvoreni=izvrsiUpit($bp,$upit_izbornamjesta_zatvorena);
    echo "<tr><td colspan='4'><h3 id='naslov'>Vaši zatvoreni izbori: </h3></td></tr>";
    while(list($a,$b,$izbmj,$opis,$c,$d,$izbnaziv,$dvp,$dvz,$izbopis)=mysqli_fetch_array($rezultat_izbori_zatvoreni))
    {
        echo "<tr>";
        echo "<td><a href='izbor.php?id_izbor=$c'>".$izbnaziv."</td></a>";
        echo "<td><a href='izbori.php?id_izbornomjesto=$a'>".$izbmj."</a></td>";
        echo "<td>".date("d.m.Y. H:i:s",strtotime($dvp))."</td>";
        echo "<td>".date("d.m.Y. H:i:s",strtotime($dvz))."</td>";
        echo "<td>"."<a class='dugme_uredi_obrisi' href='uredi_izbor.php?izbor_id=$c'>UREDI</a>";
        echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";

    echo "<div class='prostornaslova'>";
    echo "<h4>Provjera broja kandidatura po kandidatu u određenom periodu</h4>";
    echo "<p>Unesite datume za provjeru:</p>";
    echo "<table align='center'><thead></thead>";
    echo "<tbody><tr>";
    echo "<form id='formafiltriranja' method='POST'>";
    echo "<td><label for='od_datum'>Datum OD: </label><td>";
    echo "<td><input class='input_filter' name='datum_od' id='od_datum' type='text' placeholder='dd.mm.gggg. hh:ii:ss'></input></td>";
    echo "<td><label for='do_datum'>Datum DO: </label></td>";
    echo "<td><input class='input_filter' name='datum_do' id='do_datum' type='text' placeholder='dd.mm.gggg. hh:ii:ss'></input></td>";
    echo "<td><input class= 'dugme_filtriraj_resetiraj' type='submit' name='unesidatume' value='Unesi datume'></input></td>";
    echo "</tr>";
    echo "</tbody></table>";
    echo "</form>";

    $odErrPor=$doErrPor=0;
    if(isset($_POST['unesidatume']))
    {
        if(empty($_POST['datum_od']))
        {
            $odErrPor="Potrebno je unijeti početni datum";
        }
        else
        {
            $datum_od=date("Y-m-d H:i:s",strtotime($_POST['datum_od']));
        }
        if(empty($_POST['datum_od']))
        {
            $doErrPor="Potrebno je unijeti završni datum";
        }
        else
        {
            $datum_do=date("Y-m-d H:i:s",strtotime($_POST['datum_do']));
        }
        
        if(empty($odErrPor) && empty($doErrPor))
        {
            if($_SESSION['aktivni_korisnik_tip']==1)
            {
                $rang_lista_korisnika_sql="SELECT COUNT(*) AS rang_lista, k.ime, k.prezime FROM korisnik k, kandidat t, izbor i
                WHERE k.korisnik_id = t.korisnik_id AND t.izbor_id=i.izbor_id AND t.status<>'O'
                AND i.datum_vrijeme_zavrsetka BETWEEN '$datum_od' AND '$datum_do'
                GROUP BY t.korisnik_id ORDER BY rang_lista DESC";
            }
            
            elseif($_SESSION['aktivni_korisnik_tip']==2)
            {
                $rang_lista_korisnika_sql="SELECT COUNT(*) AS rang_lista, k.ime, k.prezime FROM korisnik k, kandidat t, izbor i
                WHERE k.korisnik_id = t.korisnik_id AND t.izbor_id=i.izbor_id AND t.status<>'O'
                AND i.datum_vrijeme_zavrsetka BETWEEN '$datum_od' AND '$datum_do'
                AND i.izborno_mjesto_id IN (SELECT izborno_mjesto_id FROM izborno_mjesto WHERE moderator_id = '$_SESSION[aktivni_korisnik_id]')
                GROUP BY t.korisnik_id ORDER BY rang_lista DESC";
            }
            
            $rang_lista=izvrsiUpit($bp,$rang_lista_korisnika_sql);
            echo "<table class='tablica_izbornamjesta'>";
            echo "<thead><td>Ime</td><td>Prezime</td><td>Broj kandidatura</td></thead>";
            echo "<tbody>";
            while(list($rang, $ime, $prezime)=mysqli_fetch_array($rang_lista))
            {
                echo "<tr>";
                echo "<td>".$ime."</td>";
                echo "<td>".$prezime."</td>";
                echo "<td>".$rang."</td>";
                echo "</tr>";
            }
            echo "</tbody></table>";
        }
        echo "</div>";
    }
?>




</body>
</html>
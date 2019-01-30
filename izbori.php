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

    if(isset($_GET['id_izbornomjesto']))
    {
        $upit_naziv_get="SELECT izborno_mjesto_id, naziv FROM izborno_mjesto WHERE izborno_mjesto_id='$_GET[id_izbornomjesto]'";
        $imena=izvrsiUpit($bp,"SET NAMES utf8;");
        $izbMjesto=izvrsiUpit($bp,$upit_naziv_get);
        $nazivMjesta=mysqli_fetch_array($izbMjesto);
        echo "<div class='prostornaslova'>";
        echo "<h3>Pregled izbora za izborno mjesto:</h3>";
        echo "<h1>".$nazivMjesta["naziv"]."</h1>";
        echo "</div>";
        


        if(isset($_SESSION['aktivni_korisnik']))
        {   
            $otvoreniizbori_upit="SELECT * FROM izborno_mjesto i, izbor r WHERE i.izborno_mjesto_id = r.izborno_mjesto_id AND i.izborno_mjesto_id = '$_GET[id_izbornomjesto]' AND r.datum_vrijeme_zavrsetka > CURRENT_TIMESTAMP() ORDER BY r.datum_vrijeme_zavrsetka ASC";
            $rezultat2=izvrsiUpit($bp,$otvoreniizbori_upit);

            echo "<table class='tablica_izbornamjesta'>";
            echo "<thead><th><h2>Naziv izbora</h2></th><th><h2>Početak izbora</h2></th><th><h2>Završetak izbora</h2></th><th><h2>Opis izbora</h2></th></thead>";
            echo "<tbody>";
            echo "<tr><td colspan='4'><h3 id='naslov'>Otvoreni izbori</h3></td></tr>";
            if(mysqli_num_rows($rezultat2)==0)
            {
                echo "<tr><td>Trenutno nema otvorenih izbora</td></tr>";
            }
            else
            {
                foreach($rezultat2 as $otvizbor)
                {
                    echo "<tr>";
                    echo "<td>"."<a href='izbor.php?id_izbor=$otvizbor[izbor_id]'>". $otvizbor['naziv'] ."</a>". "</td>";
                    echo "<td>" . date("d.m.Y. H:i:s", strtotime($otvizbor['datum_vrijeme_pocetka'])) . "</td>";
                    echo "<td>" . date("d.m.Y. H:i:s", strtotime($otvizbor['datum_vrijeme_zavrsetka'])) . "</td>";
                    echo "<td>" . $otvizbor['opis'] . "</td>";
                    echo "</tr>";
                }
            }
            

            $zatvoreniizbori_upit="SELECT * FROM izborno_mjesto i, izbor r WHERE i.izborno_mjesto_id = r.izborno_mjesto_id AND i.izborno_mjesto_id = '$_GET[id_izbornomjesto]' AND r.datum_vrijeme_zavrsetka < CURRENT_TIMESTAMP() ORDER BY r.datum_vrijeme_zavrsetka ASC";
            $rezultat3=izvrsiUpit($bp,$zatvoreniizbori_upit);
            echo "<tr><td colspan='4'><h3 id='naslov'>Zatvoreni izbori</h3></td></tr>";
            if(mysqli_num_rows($rezultat3)==0)
            {
                echo "<tr><td>Trenutno nema zatvorenih izbora</td></tr>";
            }
            else
            {
                foreach($rezultat3 as $zatvizbor)
                {
                    echo "<tr>";
                    echo "<td>"."<a href='izbor.php?id_izbor=$zatvizbor[izbor_id]'>". $zatvizbor['naziv'] ."</a>". "</td>";
                    echo "<td>" . date("d.m.Y. H:i:s", strtotime($zatvizbor['datum_vrijeme_pocetka'])) . "</td>";
                    echo "<td>" . date("d.m.Y. H:i:s", strtotime($zatvizbor['datum_vrijeme_zavrsetka'])) . "</td>";
                    echo "<td>" . $zatvizbor['opis'] . "</td>";
                    echo "</tr>";
                }
            }



            $upit_kandidiraose="SELECT * FROM izbor, kandidat WHERE izbor.izbor_id=kandidat.izbor_id AND kandidat.korisnik_id=$aktivni_korisnik_id ORDER BY izbor.datum_vrijeme_zavrsetka ASC" ;
            $rez=izvrsiUpit($bp,$upit_kandidiraose);
            echo "<tr><td colspan='4'><h3 id='naslov'>Izbori na koje ste se kandidirali</h3></td></tr>";
            if(mysqli_num_rows($rez)==0)
            {
                echo "<tr><td>Niste se kandidirali ni na jedne izbore</td></tr>";
            }
            else
            {
                while (list($a,$b,$c,$d,$e,$f,$g,$h,$i)=mysqli_fetch_array($rez))
                {
                    echo "<tr>";
                    echo "<td><a href='izbor.php?id_izbor=$i'>".$c."</a></td>";
                    echo "<td>".date("d.m.Y. H:i:s", strtotime($d))."</td>";
                    echo "<td>".date("d.m.Y. H:i:s", strtotime($e))."</td>";
                    echo "<td>".$f."</td>";
                    echo "</tr>";
                }
            }

        echo "</tbody></table>";
        }


        else
        {
            $upit_zatvoreniizbori="SELECT * FROM izborno_mjesto i, izbor r WHERE i.izborno_mjesto_id = r.izborno_mjesto_id AND i.izborno_mjesto_id = '$_GET[id_izbornomjesto]' AND r.datum_vrijeme_zavrsetka < CURRENT_TIMESTAMP() ORDER BY r.datum_vrijeme_zavrsetka ASC";
            $rezultat=izvrsiUpit($bp,$upit_zatvoreniizbori);
            echo "<table class='tablica_izbornamjesta'>";
            echo "<thead><th><h2>Naziv izbora</h2></th><th><h2>Početak izbora</h2></th><th><h2>Završetak izbora</h2></th><th><h2>Opis</h2></th></thead>";
            echo "<tbody>";
            echo "<tr><td colspan='4'><h3 id='naslov'>Zatvoreni izbori</h3></td></tr>";

            if(mysqli_num_rows($rezultat)==0)
            {
                echo "<tr><td>Trenutno nema zatvorenih izbora</td></tr>";
                echo "</tbody></table>";
            }
            else
            {
            foreach($rezultat as $zatizbor)
                {
                    echo "<tr>";
                    echo "<td><a href='pobjednik.php?id_izbor=$zatizbor[izbor_id]'>" . $zatizbor['naziv'] . "</a></td>";
                    echo "<td>" . date("d.m.Y. H:i:s", strtotime($zatizbor['datum_vrijeme_pocetka'])) . "</td>";
                    echo "<td>" . date("d.m.Y. H:i:s", strtotime($zatizbor['datum_vrijeme_zavrsetka'])) . "</td>";
                    echo "<td>" . $zatizbor['opis'] . "</td>";
                    echo "</tr>";
                }
                echo "</tbody>";
                echo "</table>";
            }
        }
        
    }
    
    

    else
    {
        $imena=izvrsiUpit($bp,"SET NAMES utf8;");
        if(isset($_SESSION['aktivni_korisnik']))
        {
            if($aktivni_korisnik_tip==3)
            {
                echo "<div class='prostornaslova'>";
                echo "<h2>Pregled izbora</h2>";
                echo "</div>";
                $upit_otvoreniizbori="SELECT * , i.naziv AS izbmj_naziv FROM izborno_mjesto i, izbor r WHERE i.izborno_mjesto_id = r.izborno_mjesto_id AND r.datum_vrijeme_zavrsetka > CURRENT_TIMESTAMP() ORDER BY r.datum_vrijeme_zavrsetka DESC";
                $rezultat5=izvrsiUpit($bp,$upit_otvoreniizbori);
                echo "<table class='tablica_izbornamjesta'>";
                echo "<thead><th><h2>Naziv izbora</h2></th><th><h2>Izborno mjesto</h2></th><th><h2>Početak izbora</h2></th><th><h2>Završetak izbora</h2></th><th><h2>Opis</h2></th></thead>";
                echo "<tbody>";
                echo "<tr><td colspan='5'><h3 id='naslov'>Otvoreni izbori</h3></td></tr>";
                if(mysqli_num_rows($rezultat5)==0)
                {
                    echo "<tr><td>Trenutno nema otvorenih izbora</td></tr>";
                }
                else
                {
                    foreach($rezultat5 as $otvizbor)
                    {
                        echo "<tr>";
                        echo "<td><a href='izbor.php?id_izbor=$otvizbor[izbor_id]'>" . $otvizbor['naziv'] . "</a></td>";
                        echo "<td><a href='izbori.php?id_izbornomjesto=$otvizbor[izborno_mjesto_id]'>".$otvizbor["izbmj_naziv"]."</a></td>";
                        echo "<td>" . date("d.m.Y. H:i:s", strtotime($otvizbor['datum_vrijeme_pocetka'])) . "</td>";
                        echo "<td>" . date("d.m.Y. H:i:s", strtotime($otvizbor['datum_vrijeme_zavrsetka'])) . "</td>";
                        echo "<td>" . $otvizbor['opis'] . "</td>";
                        echo "</tr>";
                    }
                }
                
    
                $zatizbori_upit="SELECT *, i.naziv AS izbmj_naziv FROM izborno_mjesto i, izbor r WHERE i.izborno_mjesto_id = r.izborno_mjesto_id AND r.datum_vrijeme_zavrsetka < CURRENT_TIMESTAMP() ORDER BY r.datum_vrijeme_zavrsetka DESC";
                $rezultat4=izvrsiUpit($bp,$zatizbori_upit);
                echo "<tr><td colspan='5'><h3 id='naslov'>Zatvoreni izbori</h3></td></tr>";

                if(mysqli_num_rows($rezultat4)==0)
                {
                    echo "<tr><td>Trenutno nema zatvorenih izbora</td></tr>";
                }
                else
                {
                    foreach($rezultat4 as $zatvizbor)
                    {
                        echo "<tr>";
                        echo "<td><a href='izbor.php?id_izbor=$zatvizbor[izbor_id]'>" . $zatvizbor['naziv'] . "</a></td>";
                        echo "<td><a href='izbori.php?id_izbornomjesto=$zatvizbor[izborno_mjesto_id]'>".$zatvizbor["izbmj_naziv"]."</a></td>";
                        echo "<td>" . date("d.m.Y. H:i:s", strtotime($zatvizbor['datum_vrijeme_pocetka'])) . "</td>";
                        echo "<td>" . date("d.m.Y. H:i:s", strtotime($zatvizbor['datum_vrijeme_zavrsetka'])) . "</td>";
                        echo "<td>" . $zatvizbor['opis'] . "</td>";
                        echo "</tr>";
                    }
                }
    
                $upit_kandidiraose="SELECT * FROM izborno_mjesto, izbor, kandidat WHERE izborno_mjesto.izborno_mjesto_id=izbor.izborno_mjesto_id AND izbor.izbor_id=kandidat.izbor_id AND kandidat.korisnik_id=$aktivni_korisnik_id AND kandidat.status <> 'O' ORDER BY izbor.datum_vrijeme_zavrsetka DESC" ;
                $rez_kandidatura=izvrsiUpit($bp,$upit_kandidiraose);
                echo "<tr><td colspan='5'><h3 id='naslov'>Izbori na koje ste se kandidirali</h3></td></tr>";
                if(mysqli_num_rows($rez_kandidatura)==0)
                {
                    echo "<tr><td>Niste se kandidirali ni na jedne izbore</td></tr>";
                }
                else
                {
                    while (list($pr1,$pr2,$pr3,$pr4,$a,$b,$c,$d,$e,$f,$g,$h,$i)=mysqli_fetch_array($rez_kandidatura))
                    {
                        echo "<tr>";
                        echo "<td><a href='izbor.php?id_izbor=$i'>".$c."</a></td>";
                        echo "<td style='font-size:20'><a href='izbori.php?id_izbornomjesto=$pr1'>$pr3</a></td>";
                        echo "<td>".date("d.m.Y. H:i:s", strtotime($d))."</td>";
                        echo "<td>".date("d.m.Y. H:i:s", strtotime($e))."</td>";
                        echo "<td>".$f."</td>";
                        echo "</tr>";
                    }
                }
                echo "</tbody></table>";
            }
            if($aktivni_korisnik_tip==2 || $aktivni_korisnik_tip==1)
            {
                if($_SESSION['aktivni_korisnik_tip']==1)
                {
                    $sql_q="SELECT izborno_mjesto_id, naziv FROM izborno_mjesto ORDER BY naziv ASC";
                    $rez_q=izvrsiUpit($bp,$sql_q);
                    $sql_izb="SELECT DISTINCT naziv FROM izbor ORDER BY naziv ASC";
                    $rez_izb=izvrsiUpit($bp,$sql_izb);
                    
                    echo "<div class='prostornaslova'>";
                    echo "<p>Filtriranje izbora po vremenskom razdoblju: </p>";
                    echo "<form id='formafiltriranja' method='GET'>";
                    echo "<select class='select_filter' name='izbmj'>";
                    echo "<option value=''></option>";
                    while(list($id,$naziv)=mysqli_fetch_array($rez_q))
                    {
                        echo "<option id='opcija' value='$id'";
                        if(isset($_GET['izbmj'])&&!isset($_GET['resetiraj']))
                        {
                            if($_GET['izbmj']==$id)
                            echo " selected";
                        }
                        echo ">$naziv</option>";
                    }
                    
                    echo "</select>";
                    echo "<select class='select_filter' name='izb_naz'>";
                    echo "<option value=''></option>";
                    while(list($naziv_izb)=mysqli_fetch_array($rez_izb))
                    {
                        echo "<option id='opcija' value='$naziv_izb'";
                        if(isset($_GET['izb_naz'])&&!isset($_GET['resetiraj']))
                        {
                            if($_GET['izb_naz']==$naziv_izb)
                            echo " selected";
                        }
                        echo ">$naziv_izb</option>";
                    }
                    
                    echo "</select>";
                    
                    echo "<input class='dugme_filtriraj_resetiraj' type='submit' name='filtriraj' value='Filtriraj'></input>";
                    echo "<input class='dugme_filtriraj_resetiraj' type='submit' name='resetiraj' value='Resetiraj'></input>";
                    echo "</form>";
                    echo "<h2>Pregled izbora</h2>";
                    echo "</div>";
                    
                }
                $otvizbori="SELECT m.izborno_mjesto_id, m.naziv, i.izbor_id, i.izborno_mjesto_id, i.naziv, i.datum_vrijeme_pocetka, i.datum_vrijeme_zavrsetka FROM izborno_mjesto m,izbor i WHERE m.izborno_mjesto_id= i.izborno_mjesto_id AND i.datum_vrijeme_zavrsetka > CURRENT_TIMESTAMP()";
                $zatvizbori="SELECT m.izborno_mjesto_id, m.naziv, i.izbor_id, i.izborno_mjesto_id, i.naziv, i.datum_vrijeme_pocetka, i.datum_vrijeme_zavrsetka FROM izborno_mjesto m, izbor i WHERE m.izborno_mjesto_id= i.izborno_mjesto_id AND i.datum_vrijeme_zavrsetka < CURRENT_TIMESTAMP()";
                if(isset($_GET['resetiraj']))
                {
                    header("Location:izbori.php");
        
                }
                if(isset($_GET['filtriraj']) || isset($_GET['izb_naz']))
                {
                    if($_GET['izb_naz']!="" && $_GET['izbmj']!="")
                    {
                        $izbmj=$_GET['izbmj'];
                        $naziv_izbora = $_GET['izb_naz'];
                        $otvizbori=$otvizbori." AND i.izborno_mjesto_id='$izbmj' AND i.naziv='$naziv_izbora'";
                        $zatvizbori=$zatvizbori." AND i.izborno_mjesto_id='$izbmj' AND i.naziv='$naziv_izbora'";
                    }
                    elseif($_GET['izbmj']!="")
                    {
                        $izbmj=$_GET['izbmj'];
                        $otvizbori=$otvizbori." AND i.izborno_mjesto_id='$izbmj'";
                        $zatvizbori=$zatvizbori." AND i.izborno_mjesto_id='$izbmj'";
                    }
                    elseif($_GET['izb_naz']!="")
                    {
                        $naziv_izbora = $_GET['izb_naz'];
                        $otvizbori=$otvizbori." AND i.naziv='$naziv_izbora'";
                        $zatvizbori=$zatvizbori." AND i.naziv='$naziv_izbora'";
                    }
                    else
                    {
    
                    }    
                }

                if(isset($_GET['red']))
                {
                    $red=" ".$_GET['red'];
                }
                else
                {
                    $red=" i.datum_vrijeme_zavrsetka";
                }
                $sort="DESC";
                if(isset($_GET['sort']))
                {
                    if($_GET['sort']=="DESC")
                    {
                        $sort="ASC";
                    }
                    elseif($_GET['sort']=="ASC")
                    {
                        $sort="DESC";
                    }
                }
                
                
                
                $otvizbori=$otvizbori."ORDER BY ".$red." ".$sort;
                $zatvizbori=$zatvizbori." ORDER BY ".$red." ".$sort;

                $utf=izvrsiUpit($bp,"SET NAMES utf8;");
                $rez_otvizbori=izvrsiUpit($bp,$otvizbori);
                $rez_zatvizbori=izvrsiUpit($bp,$zatvizbori);
                
                echo "<table class='tablica_izbornamjesta'>";


                echo "<thead>";
                    $ii="i.naziv";
                    echo "<th><span class='strelice'><a href='?red=$ii && sort=$sort'>Naziv izbora<img src='slike/ikone/sort.png'/></a></span></th>";
                    $i="m.naziv";
                    echo "<th><span class='strelice'><a href='?red=$i && sort=$sort'>Izborno mjesto<img src='slike/ikone/sort.png'/></a></span></th>";
                    echo "<th><h2>Početak izbora</h2></th>";
                    $dz="i.datum_vrijeme_zavrsetka";
                    echo "<th><h2>Završetak izbora</h2></th>";
                echo"</thead>";
                

                echo "<tbody>";
                echo "<tr><td colspan='5'><h3 id='naslov'>Otvoreni izbori</h3></td></tr>";
                while(list($mjid,$naz,$izbid,$izbmjid,$ime,$dvp,$dvz)=mysqli_fetch_array($rez_otvizbori))
                {
                    echo "<tr>";
                    echo "<td><a href='izbor.php?id_izbor=$izbid'>$ime</a></td>";
                    echo "<td><a href='izbori.php?id_izbornomjesto=$mjid'>$naz</a></td>";
                    echo "<td>".date("d.m.Y. H:i:s", strtotime($dvp))."</td>";
                    echo "<td>".date("d.m.Y. H:i:s", strtotime($dvz))."</td>";
                    echo "</tr>";
                }

                echo "<tr><td colspan='5'><h3 id='naslov'>Zatvoreni izbori</h3></td></tr>";
                while(list($mjid,$naz,$izbid,$izbmjid,$ime,$dvp,$dvz)=mysqli_fetch_array($rez_zatvizbori))
                {
                    echo "<tr>";
                    echo "<td><a href='izbor.php?id_izbor=$izbid'>$ime</a></td>";
                    echo "<td><a href='izbori.php?id_izbornomjesto=$mjid'>$naz</a></td>";
                    echo "<td>".date("d.m.Y. H:i:s", strtotime($dvp))."</td>";
                    echo "<td>".date("d.m.Y. H:i:s", strtotime($dvz))."</td>";
                    echo "</tr>";
                }

                $upit_kandidiraose="SELECT * FROM izborno_mjesto, izbor, kandidat WHERE izborno_mjesto.izborno_mjesto_id=izbor.izborno_mjesto_id AND izbor.izbor_id=kandidat.izbor_id AND kandidat.korisnik_id=$aktivni_korisnik_id AND kandidat.status <> 'O' ORDER BY izbor.datum_vrijeme_zavrsetka DESC" ;
                $rez_kandidatura=izvrsiUpit($bp,$upit_kandidiraose);
                echo "<tr><td colspan='5'><h3 id='naslov'>Izbori na koje ste se kandidirali</h3></td></tr>";
                
                if(mysqli_num_rows($rez_kandidatura)==0)
                {
                    echo "<tr><td>Niste se kandidirali ni na jedne izbore</td></tr>";
                    echo "</tbody></table>";
                }
                else
                {
                    while (list($pr1,$pr2,$pr3,$pr4,$a,$b,$c,$d,$e,$f,$g,$h,$i)=mysqli_fetch_array($rez_kandidatura))
                    {
                        echo "<tr>";
                        echo "<td><a href='izbor.php?id_izbor=$i'>".$c."</a></td>";
                        echo "<td><a href='izbori.php?id_izbornomjesto=$pr1'>$pr3</a></td>";
                        echo "<td>".date("d.m.Y. H:i:s", strtotime($d))."</td>";
                        echo "<td>".date("d.m.Y. H:i:s", strtotime($e))."</td>";
                        echo "</tr>";
                    }
        
                    echo "</table>";
                } 
            }
        }
    }
?>




</body>
</html>
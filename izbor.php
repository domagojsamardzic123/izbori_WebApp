<?php
    header('Content-Type: text/html; charset=utf-8');
    include ("zaglavlje.php");
    $bp=spojiSeNaBazu();
    ini_set('default_charset','UTF-8');

    function provjeriglasanje($bp)
    {
        $idizbora=$_GET['id_izbor'];
        $idkorisnika=$_SESSION['aktivni_korisnik_id'];
        $pretraga="SELECT kandidat_id FROM kandidat WHERE izbor_id=$idizbora";
        $pretraga2="SELECT kandidat_id FROM glas WHERE korisnik_id=$idkorisnika";
        $trazi=izvrsiUpit($bp,$pretraga);
        $trazi2=izvrsiUpit($bp,$pretraga2);
        $stanje=0;
        foreach($trazi as $t)
        {
            foreach($trazi2 as $t2)
            {
                if($t==$t2)
                {
                    $stanje=$stanje+1;
                }
                else;
            }
        }
        return $stanje;
    }
    function provjerikandidaturu($bp)
    {
        $kandidirao_se=0;
        $idizbora=$_GET['id_izbor'];
        $idkorisnika=$_SESSION['aktivni_korisnik_id'];
        $sql_upit="SELECT * FROM kandidat WHERE izbor_id=$idizbora AND korisnik_id=$idkorisnika AND `status` <> 'O'";
        $rezultat=izvrsiUpit($bp, $sql_upit);
        $string=mysqli_fetch_array($rezultat);
        if(mysqli_num_rows($rezultat)>0)
        {
            $kandidirao_se=1;
        }

        return $kandidirao_se;
    }
    function povucikandidaturu($bp){
        $odustao=0;
        $idizbora=$_GET['id_izbor'];
        $idkorisnika=$_SESSION['aktivni_korisnik_id'];
        $upit="UPDATE kandidat SET `status`='O' WHERE izbor_id=".$idizbora." AND korisnik_id=".$idkorisnika;
        $rezultat=izvrsiUpit($bp,$upit);
        if($rezultat)
        {
            $upit_kandidat="SELECT kandidat_id FROM kandidat WHERE korisnik_id='$idkorisnika' AND izbor_id='$idizbora'";
            $kandidat_id=izvrsiUpit($bp,$upit_kandidat);
            if($kandidat_id)
            {
                $kand_id=mysqli_fetch_array($kandidat_id);
                $id_kandidata=$kand_id['kandidat_id']; 
                $upit_brisi_glasove="DELETE FROM glas WHERE kandidat_id='$id_kandidata'";
                $rezultat_brisanje_glasova=izvrsiUpit($bp,$upit_brisi_glasove);
                if($rezultat_brisanje_glasova>0)
                {
                    $odustao=1;
                }
    
            }
        }
        return $odustao;
    }

    function provjeriodustajanje($bp)
    {
        $odustao=0;
        $idkorisnika=$_SESSION['aktivni_korisnik_id'];
        $idizbora=$_GET['id_izbor'];
        $sqlupit="SELECT * FROM kandidat WHERE izbor_id=$idizbora AND korisnik_id=$idkorisnika AND status='O'";
        $rezultat=izvrsiUpit($bp,$sqlupit);
        if(mysqli_num_rows($rezultat)>0){
            $odustao=1;
        }
        return $odustao;
    }

    function provjeriaktivnost($bp)
    {
        $upit="SELECT datum_vrijeme_pocetka,datum_vrijeme_zavrsetka FROM izbor WHERE izbor_id='$_GET[id_izbor]'";
        $zavrsetak=izvrsiUpit($bp,$upit);
        $izbori_aktivni=0;
        $vrijeme=mysqli_fetch_array($zavrsetak);

            if(($vrijeme['datum_vrijeme_pocetka']<date('Y-m-d H:i:s')) && (date('Y-m-d H:i:s')<$vrijeme['datum_vrijeme_zavrsetka']))
            {
                $izbori_aktivni=1;
            }
            else
            {
                echo "<div class='upozorenje'>IZBORI NISU AKTIVNI!</div>";
            }
        return $izbori_aktivni;
    }

    function provjerimoderatora($bp)
    {
        $je_moderator=0;
        $sql="SELECT i.izbor_id FROM izbor i,izborno_mjesto m WHERE i.izbor_id='$_GET[id_izbor]' AND i.izborno_mjesto_id=m.izborno_mjesto_id AND m.moderator_id='$_SESSION[aktivni_korisnik_id]' ";
        $mod=izvrsiUpit($bp,$sql);
        $moder=mysqli_fetch_array($mod);
        $idizbora=$_GET['id_izbor'];
        if($moder['izbor_id']==$idizbora)
        {
            $je_moderator=1;
        }
        return $je_moderator;
    }    
    function provjeripobjednika($bp)
    {
        
        $upit_pobjednik="SELECT * FROM korisnik k, kandidat t WHERE k.korisnik_id=t.korisnik_id AND t.izbor_id = '$_GET[id_izbor]' AND t.status = 'P'";
        $rezultat_pobjednik=izvrsiUpit($bp,$upit_pobjednik);


        $pobjednik = mysqli_fetch_array($rezultat_pobjednik);

        return $pobjednik;
    }

    ?>


    <!DOCTYPE html>
    <html>

    <head>
    <link href="stil.css" rel="stylesheet" type="text/css"/>
    <meta charset="UTF-8"/>
    </head>

    <body>

    <?php

        
    $provjeraaktivnosti=provjeriaktivnost($bp);
    $provjeraodustajanja=provjeriodustajanje($bp);
    $provjerakandidature=provjerikandidaturu($bp);
    $provjeraglasanja=provjeriglasanje($bp);
    $provjeramoderatora=provjerimoderatora($bp);
    $provjerapobjednika=provjeripobjednika($bp);
    $pobjednik_kand_id=$postotak_glasova=0;
    $trenutno_vrijeme=date("Y-m-d H:i:s");
    
        $upit="SELECT * FROM izbor WHERE izbor_id='$_GET[id_izbor]'";
        $upit2="SELECT * FROM kandidat k, korisnik t WHERE k.izbor_id='$_GET[id_izbor]' AND k.korisnik_id=t.korisnik_id AND status<>'O'";
        $utf8=izvrsiUpit($bp,"SET NAMES utf8;");
        $prikazizbora=izvrsiUpit($bp,$upit);
        date_default_timezone_set("Europe/Zagreb");

        echo "<div class='prostornaslova'>";
        echo "<h1>Podatci o izboru</h1>";
        while (list($id_izbora,$id_izb_mj,$naziv,$datum_vp,$datum_vz,$opis) = mysqli_fetch_array($prikazizbora))
        {
            echo "<div><h3>".$naziv."</h3></div>";
            echo "<div>".date('d.m.Y H:i:s',strtotime($datum_vp))."</div>";
            echo "<div>".date('d.m.Y H:i:s',strtotime($datum_vz))."</div>";
            $vrijeme_pocetka=$datum_vp;
            $vrijeme_zavrsetka=$datum_vz;
            echo "<div>".$opis ."</div>";
        }
        echo "</div>";
        echo "<div class='kandidatiizbora'>";
        echo "<h1>Lista kandidata izbora:</h1>";
        echo "<form method='post'>";
        echo "<table class='tablica_izbornamjesta'>";
        echo "<thead></thead>";
        echo "<tbody>";

        $upit_bezpostotka="SELECT kandidat.kandidat_id, korisnik.korisnik_id, korisnik.ime, korisnik.prezime FROM kandidat,korisnik WHERE kandidat.izbor_id='$_GET[id_izbor]' AND kandidat.korisnik_id=korisnik.korisnik_id AND status <> 'O'";
        $prikazkandidata=izvrsiUpit($bp,$upit_bezpostotka);
        $upit_svi_glasovi_na_izboru="SELECT COUNT(*) AS ukupno FROM kandidat, glas where kandidat.izbor_id='$_GET[id_izbor]' AND kandidat.kandidat_id=glas.kandidat_id AND kandidat.status <> 'O'";
        $rezultat_svi_glasovi=izvrsiUpit($bp,$upit_svi_glasovi_na_izboru);
        $svi_glasovi=mysqli_fetch_array($rezultat_svi_glasovi);
        $i=0;

        foreach ($prikazkandidata as $kand)
        {   
            $i=$i+1;
            echo "<tr>";
            echo "<td>".$i.".</td>";
            if (isset($_SESSION['aktivni_korisnik']) && $provjeraglasanja==0)
            {
                if($vrijeme_pocetka<$trenutno_vrijeme && $trenutno_vrijeme<$vrijeme_zavrsetka)
                {
                    echo "<td><input type='radio' value='$kand[kandidat_id]' name='glasaj'/></td>";
                }
            }
            echo "<td>".$kand['ime']."</td>";
            echo "<td>".$kand['prezime']."</td>";

            
            if($provjeramoderatora==1 || $_SESSION['aktivni_korisnik_tip']==1)
            {
                $sql_b="SELECT COUNT(*) as broj_glasova FROM glas WHERE kandidat_id='$kand[kandidat_id]'";
                $broj=mysqli_fetch_array(izvrsiUpit($bp,$sql_b));
                echo "<td>".$broj['broj_glasova']."</td>";
                if($svi_glasovi['ukupno']>0)
                {
                    $postotak=(($broj['broj_glasova'])/($svi_glasovi['ukupno']))*100;
                }
                else $postotak=0;
                echo "<td>".number_format((float)$postotak,2,".","")."%</td>";
                if($postotak>50)
                {
                    $pobjednik_kand_id=$kand['kandidat_id'];
                    $postotak_glasova=(float)$postotak;
                }
                
            }
            
            echo "</tr>";
        }
        echo "</tbody>";
        echo "</table>";

        if($vrijeme_pocetka<$trenutno_vrijeme && $trenutno_vrijeme<$vrijeme_zavrsetka)
        {
            if (isset($_SESSION['aktivni_korisnik']) && $provjeraglasanja==0)
            {
                echo "<input class='dugme_uredi_obrisi' name='potvrdiglas 'type='submit' value='Glasaj'></input>";
            }
            else
            {
                echo "<div class='upozorenje'><h4>Na ovim izborima ste dali svoj glas.</h4></div>";
            }
        }


        if(isset($_SESSION['aktivni_korisnik']))
        {
            if($provjerakandidature==0)
            {
                if($provjeraodustajanja==0)
                {
                    if($vrijeme_pocetka>$trenutno_vrijeme)
                    {
                        echo "<input class='dugme_uredi_obrisi' type='submit' value='Kandidiraj se' name = 'kandidatura'; ?>";
                    }
                }
                elseif($provjeraodustajanja>0)
                {
                    if($vrijeme_pocetka>$trenutno_vrijeme)
                    {
                        echo "<div class='upozorenje'>Povukli ste kandidaturu i više se ne možete kandidirati!</div>";
                    }

                }
                else
                {
                    
                }
                
            }
            else
            {
                if($vrijeme_pocetka>$trenutno_vrijeme)
                {
                    echo "<div class='upozorenje'>Korisnik se već kandidirao na ovim izborima!</div>";
                    echo "<div><input class='dugme_uredi_obrisi' type='submit' value='Odustani od kandidature' name = 'povlacenjekandidature'; ?></div>";
                }
            }
        }


        if($provjeramoderatora==1 || $_SESSION['aktivni_korisnik_tip']==1)
        {   
            if($provjerapobjednika==0)
            {
                if($pobjednik_kand_id!=0 && $postotak_glasova>50 && $trenutno_vrijeme>$vrijeme_zavrsetka)
                {
                    echo "<button class='dugme_uredi_obrisi' type='submit' name='proglasipobjednika'>Proglasi pobjednika</button>";
                }
                if($vrijeme_pocetka<$trenutno_vrijeme && $trenutno_vrijeme<$vrijeme_zavrsetka)
                {
                    echo "<button class='dugme_uredi_obrisi' type='submit' name='proglasipobjednika' disabled>Proglasi pobjednika</button>"; 
                }
                if($pobjednik_kand_id==0 && $postotak_glasova==0 && $trenutno_vrijeme>$vrijeme_zavrsetka)
                {
                    echo "<button class='dugme_uredi_obrisi' type='submit' name='ponovljeniizbori'>PONOVI IZBORE</button>";
                }
            }
        }


        echo "</div>";

        if(isset($_SESSION['aktivni_korisnik']) && $trenutno_vrijeme>$vrijeme_zavrsetka)
        {
            if(empty($provjerapobjednika))
            {
                echo "<div class='pobjednikizbora'>";
                echo "<h3>Pobjednik izbora nije proglašen!</h3>";
                echo "</div>";
            }

            else
            {
                echo "<div class='pobjednikizbora'>";
                echo "<h2>Pobjednik izbora: </h2><br>";
                echo "<div class='slika_i_ime'>";
                echo "<img src='korisnici/".$provjerapobjednika["korisnicko_ime"].".jpg' width=300px height=400px/>";
                echo "<h1>" . $provjerapobjednika["ime"] . " " . $provjerapobjednika["prezime"] . "</h1>" . "<br>";
                echo "</div>";
                echo "<h2>" . $provjerapobjednika["email"] . "</h2>" . "<br>";
                echo "<p>" . $provjerapobjednika["zivotopis"] . "</p>";
                echo "<video src='$provjerapobjednika[video]' width='640' height='480' controls></video>" ;
                echo "</div>";
            }
        }   
        

        if (isset($_POST['glasaj']))
        {
            $stanje=provjeriglasanje($bp);
            if($stanje==0)
            {   
                $insert="INSERT INTO glas (korisnik_id, kandidat_id) VALUES (".$_SESSION['aktivni_korisnik_id'].", ".$_POST['glasaj'].")";
                $umetanje=izvrsiUpit($bp,$insert);
                $promjenjeni_red=mysqli_affected_rows($bp);
                if($umetanje!="")
                {
                    Header("Refresh:0");   
                }
                else echo "Nešto je pošlo po krivu s upitom. ";
                
            }
        }
        

       
        
        
        echo "</form>";

        if(isset($_POST['kandidatura']))
        {
            header("Location:obrazac_kandidatura.php?izbor_id=$_GET[id_izbor]&korisnik_id=$_SESSION[aktivni_korisnik_id]");
        }
        if(isset($_POST['povlacenjekandidature']))
        {
            $povlacenje=povucikandidaturu($bp);
            if($povlacenje>0)
            {
                header("Refresh:0");
                echo "Kandidat je odustao od kandidature!";
            }
        }
        
        if(isset($_POST['proglasipobjednika']))
        {
            
            if($provjerapobjednika==0)
            {
                $sql_postavi="UPDATE kandidat SET status='P' WHERE kandidat_id='$pobjednik_kand_id' AND izbor_id='$_GET[id_izbor]' AND status<>'P'";
                $postavi=izvrsiUpit($bp,$sql_postavi);
                if(mysqli_affected_rows($bp)==1)
                {
                    echo "USPJELI STE";
                    header("Refresh:0");
                }
            }
        }
        
        if(isset($_POST['ponovljeniizbori']))
        {
            header("Location:uredi_izbor.php?izbor_id=$_GET[id_izbor]&&ponovljeni=1");
        }

        
        
        
?>


</body>

</html>
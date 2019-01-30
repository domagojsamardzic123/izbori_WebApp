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
    if(isset($_GET['id_korisnik']))
    {
        if(isset($_POST['uredikorisnika']))
        {
            $utf8=izvrsiUpit($bp,"SET NAMES utf8;");
            if (empty($_POST['tip_korisnika'])) 
            {
                $tipkorErr = "Tip korisnika je obavezno polje!";
            } 
            else 
            {
                $korisnik_tip = $_POST['tip_korisnika'];
            }
                
            if (empty($_POST['korime'])) 
            {
                $korimeErr = "Korisničko ime je obavezno polje";
            } 
            else 
            {
                $korisnik_korime = $_POST['korime'];
            }
                
            if (empty($_POST['lozinka'])) 
            {
                $lozErr= "Lozinka je obavezno polje!";
            } 
            else 
            {
                $korisnik_lozinka=$_POST['lozinka'];
            }

            if (empty($_POST['ime'])) 
            {
                $imeErr= "Ime je obavezno polje!";
            } 
            else 
            {
                $korisnik_ime=$_POST['ime'];
            }

            if (empty($_POST['prezime'])) 
            {
                $prezErr= "Prezime je obavezno polje!";
            } 
            else 
            {
                $korisnik_prezime=$_POST['prezime'];
            }
            if (empty($_POST['email'])) 
            {
                $mailErr= "Email je obavezno polje!";
            } 
            else 
            {
                $korisnik_email=$_POST['email'];
            }
            
              
            if (empty($_FILES['slika'])) 
            {
                $slikaErr= "Slika je obavezno polje!";
            } 
            else 
            {
                $slikaErr="";
                $ime_slike = $_FILES['slika']['name'];

            }


            if(empty($tipkorErr) && empty($korimeErr) && empty($lozErr) && empty($imeErr) && empty($prezErr) && empty($mailErr) && empty($slikaErr))
            {
                if($_FILES['slika'])
                {
                    $razdvoji=explode(".",$ime_slike);
                    $ekstenzija=end($razdvoji);
                    $slika_ime=$korisnik_korime.".".$ekstenzija;
                    $folder="korisnici/".$slika_ime;
                    move_uploaded_file($_FILES['slika']['tmp_name'],$folder);
                    $sql_uredikorisnika="UPDATE korisnik SET tip_korisnika_id='$korisnik_tip',korisnicko_ime='$korisnik_korime',lozinka='$korisnik_lozinka',ime='$korisnik_ime',prezime='$korisnik_prezime',email='$korisnik_email',slika='$folder' WHERE korisnik_id='$_GET[id_korisnik]'";
                }
                else
                {
                    $sql_uredikorisnika="UPDATE korisnik SET tip_korisnika_id='$korisnik_tip',korisnicko_ime='$korisnik_korime',lozinka='$korisnik_lozinka',ime='$korisnik_ime',prezime='$korisnik_prezime',email='$korisnik_email' WHERE korisnik_id='$_GET[id_korisnik]'";
                }
                
                $utf8=izvrsiUpit($bp,"SET NAMES utf8;");
                $rez_uredikorisnika=izvrsiUpit($bp,$sql_uredikorisnika);
                if(!(empty($rez_uredikorisnika)))
                {
                    $uspjesno_poruka="USPJEŠNO STE UREDILI POSTOJEĆEG KORISNIKA";
                    header("Refresh: 2 URL=korisnici.php");
                }
                else
                    header("Location:uredi_korisnika.php");       
            }
        
            
        }
        $utf8=izvrsiUpit($bp,"SET NAMES utf8;");
        $upit_uredikorisnika="SELECT * FROM korisnik WHERE korisnik_id='$_GET[id_korisnik]'";
        $rezultat_korisnik=izvrsiUpit($bp,$upit_uredikorisnika);
        $korisnik=mysqli_fetch_array($rezultat_korisnik);
        echo "<div class='prostornaslova'>";
        echo "<h2>Uredi korisnika: </h2><h3>$korisnik[ime] $korisnik[prezime]</h3>";
        echo "</div>";
        echo "<form class='prijava' method='POST' enctype='multipart/form-data'>";
        echo "<table class='prijava_tablica'>";
        echo "<thead></thead><tbody>";
        $slika_korisnika=$korisnik['slika'];
        if(file_exists($slika_korisnika) && $slika_korisnika!=="")
            {
                echo "<tr><td colspan='2' class='slikakorisnika'><img src='$slika_korisnika' heigth='300' width='200'></td></tr>";
            }
            else echo "<tr><td colspan='2' class='upozorenje'>Slika korisnika ne postoji!</td></tr>";
        
        echo "<tr><td class='prijavalabela'>Promjeni sliku</td><td><input name='slika' type='file'></input></td></tr>";
        echo "<tr><td class='prijavalabela'>Tip korisnika</td><td>";
        echo "<select class='select' name='tip_korisnika'>";
        echo "<option value='1' ";
        if($korisnik['tip_korisnika_id']==1) echo "selected";
        echo "> Administrator</option>";
        echo "<option value='2'";
        if($korisnik['tip_korisnika_id']==2) echo "selected";
        echo "> Moderator</option>";
        echo "<option value='3'";
        if($korisnik['tip_korisnika_id']==3) echo "selected";
        echo "> Prijavljeni korisnik</option>";
        echo "</select>";
        echo "<td></tr>";
        echo "<tr><td class='prijavalabela'>Korisničko ime</td><td><input class='input_forma' name='korime'type='text' value='$korisnik[korisnicko_ime]'></input></td></tr>";
        if(!(empty($korimeErr)))
        {
            echo "<tr><td class='upozorenje' colspan='2'>$korimeErr</td></tr>";
        }
        echo "<tr><td class='prijavalabela'>Lozinka</td><td><input class='input_forma' name='lozinka'type='text' value='$korisnik[lozinka]'></input></td></tr>";
        if(!(empty($lozErr)))
        {
            echo "<tr><td class='upozorenje' colspan='2'>$lozErr</td></tr>";
        }
        echo "<tr><td class='prijavalabela'>Ime</td><td><input class='input_forma' name='ime' type='text' value='$korisnik[ime]'></input></td></tr>";
        if(!(empty($imeErr)))
        {
            echo "<tr><td class='upozorenje' colspan='2'>$imeErr</td></tr>";
        }
        echo "<tr><td class='prijavalabela'>Prezime</td><td><input class='input_forma' name='prezime' type='text' value='$korisnik[prezime]'></input></td></tr>";
        if(!(empty($prezErr)))
        {
            echo "<tr><td class='upozorenje' colspan='2'>$prezErr</td></tr>";
        }
        echo "<tr><td class='prijavalabela'>Email</td><td><input class='input_forma' name='email' type='text' value='$korisnik[email]'></input></td></tr>";
        if(!(empty($emailErr)))
        {
            echo "<tr><td class='upozorenje' colspan='2'>$emailErr</td></tr>";
        }

        if(isset($uspjesno_poruka))
        {
            echo "<tr><td colspan='2' class='uspjesno'>$uspjesno_poruka</td></tr>";
        }
        else
        {
            echo "<tr><td colspan='2'><button class='dugme_forma' type='submit' name='uredikorisnika'>Uredi korisnika</button></td></tr>";
        }

        echo "</tbody></table>";
        echo "</form>";
    }
/******************************************************************************************************** */
/******************************************************************************************************** */
/******************************************************************************************************** */
    else
    {
        
        if (isset($_POST['dodajkorisnika'])) 
        {
            $utf8=izvrsiUpit($bp,"SET NAMES utf8;");
            if (empty($_POST['tip_korisnika'])) 
            {
                $tipkorErr = "Tip korisnika je obavezno polje!";
            } 
            else 
            {
                $korisnik_tip = $_POST['tip_korisnika'];
                $tipkorErr="";
            }
            
            if (empty($_POST['korime'])) 
            {
                $korimeErr = "Korisničko ime je obavezno polje";
            } 
            else 
            {
                $korimeErr="";
                $korisnik_korime = $_POST['korime'];
            }
            
            if (empty($_POST['lozinka'])) 
            {
                $lozErr= "Lozinka je obavezno polje!";
            } 
            else 
            {
                $lozErr="";
                $korisnik_lozinka=$_POST['lozinka'];
            }

            if (empty($_POST['ime'])) 
            {
                $imeErr= "Ime je obavezno polje!";
            }
            else 
            {
                $imeErr="";
                $korisnik_ime=$_POST['ime'];
            }

            if (empty($_POST['prezime'])) 
            {
                $prezErr= "Prezime je obavezno polje!";
            } 
            else 
            {
                $prezErr="";
                $korisnik_prezime=$_POST['prezime'];
            }
            if (empty($_POST['email'])) 
            {
                $mailErr= "Email je obavezno polje!";
            } 
            else 
            {
                $mailErr="";
                $korisnik_email=$_POST['email'];
            }
            if (empty($_FILES['slika'])) 
            {
                $slikaErr= "Slika je obavezno polje!";
            } 
            else 
            {
                $slikaErr="";
                $ime_slike = $_FILES['slika']['name'];

            }


            if(empty($tipkorErr) && empty($korimeErr) && empty($lozErr) && empty($imeErr) && empty($prezErr) && empty($mailErr))
            {
                $utf8=izvrsiUpit($bp,"SET NAMES utf8;");
                if($_FILES['slika'])
                {
                    $razdvoji=explode(".",$ime_slike);
                    $ekstenzija=end($razdvoji);
                    $slika_ime=$korisnik_korime.".".$ekstenzija;
                    $folder="korisnici/".$slika_ime;
                    move_uploaded_file($_FILES['slika']['tmp_name'],$folder);
                    $sql_dodajkorisnika="INSERT INTO korisnik (korisnik_id,tip_korisnika_id,korisnicko_ime,lozinka,ime,prezime,email,slika) VALUES ('','$korisnik_tip','$korisnik_korime','$korisnik_lozinka','$korisnik_ime','$korisnik_prezime','$korisnik_email','$folder')";
                }
                else
                {
                    $sql_dodajkorisnika="INSERT INTO korisnik (korisnik_id,tip_korisnika_id,korisnicko_ime,lozinka,ime,prezime,email) VALUES ('','$korisnik_tip','$korisnik_korime','$korisnik_lozinka','$korisnik_ime','$korisnik_prezime','$korisnik_email')";
                }
                    
                $rez_dodajkorisnika=izvrsiUpit($bp,$sql_dodajkorisnika);
            
                if(!(empty($rez_dodajkorisnika)))
                {
                    $uspjesno_poruka="USPJEŠNO STE DODALI NOVOG KORISNIKA";
                    header("Refresh: 2 URL=korisnici.php");
                }
                else
                    header("Location:uredi_korisnika.php");       
            }

        }    

        $utf8=izvrsiUpit($bp,"SET NAMES utf8;");
        echo "<div class='prostornaslova'>";
        echo "<h2>Forma za unos novog korisnika: </h3>";
        echo "</div>";
        echo "<form class='prijava' method='POST' enctype='multipart/form-data'>";
        echo "<table class='prijava_tablica'>";
        echo "<thead></thead><tbody>";
        echo "<tr><td class='prijavalabela'>Tip korisnika</td><td>";
        echo "<select class='select' name='tip_korisnika'>";
        echo "<option class='opcija' value='1'> Administrator </option>";
        echo "<option value='2'> Voditelj </option>";
        echo "<option value='3' selected> Korisnik </option>";
        echo "</select>";
        echo "<td></tr>";
        echo "<tr><td class='prijavalabela'>Korisnicko ime</td><td><input value='";
        if(isset($_POST['korime'])) echo $_POST['korime'];
        echo "' class='input_forma' name='korime'type='text'></input></td></tr>";
        if(!(empty($korimeErr)))
        {
            echo "<tr><td class='upozorenje' colspan='2'>$korimeErr</td></tr>";
        }
        echo "<tr><td class='prijavalabela'>Lozinka</td><td><input value='";
        if(isset($_POST['lozinka'])) echo $_POST['lozinka'];
        echo "' class='input_forma' name='lozinka'type='text'></input></td></tr>";
        if(!(empty($lozErr)))
        {
            echo "<tr><td class='upozorenje' colspan='2'>$lozErr</td></tr>";
        }
        echo "<tr><td class='prijavalabela'>Ime</td><td><input value='";
        if(isset($_POST['ime'])) echo $_POST['ime'];
        echo "' class='input_forma' name='ime' type='text' ></input></td></tr>";
        if(!(empty($imeErr)))
        {
            echo "<tr><td class='upozorenje' colspan='2'>$imeErr</td></tr>";
        }
        echo "<tr><td class='prijavalabela'>Prezime</td><td><input value='";
        if(isset($_POST['prezime'])) echo $_POST['prezime'];
        echo "' class='input_forma' name='prezime' type='text'></input></td></tr>";
        if(!(empty($prezErr)))
        {
            echo "<tr><td class='upozorenje' colspan='2'>$prezErr</td></tr>";
        }
        echo "<tr><td class='prijavalabela'>Email</td><td><input value='";
        if(isset($_POST['email'])) echo $_POST['email'];
        echo "' class='input_forma' name='email' type='text'></input></td></tr>";
        if(!(empty($mailErr)))
        {
            echo "<tr><td class='upozorenje' colspan='2'>$mailErr</td></tr>";
        }
        echo "<tr><td class='prijavalabela'>Slika</td><td><input class='input_forma' name='slika' type='file'></input></td></tr>";
        if(!(empty($slikaErr)))
        {
            echo "<tr><td class='upozorenje' colspan='2'>$slikaErr</td></tr>";
        }


        if(isset($uspjesno_poruka))
        {
            echo "<tr><td colspan='2' class='uspjesno'>$uspjesno_poruka</td></tr>";
        }
        else
        {
            echo "<tr><td colspan='2'><button class='dugme_forma' type='submit' name='dodajkorisnika'>Dodaj korisnika</button></td></tr>";
        }
        
        echo "</tbody></table>";
        echo "</form>";
    
    }
    


}

?>


</body>

</html>
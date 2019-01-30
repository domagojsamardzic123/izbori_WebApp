<?php
    include ("zaglavlje.php");
    $baza=spojiSeNaBazu();
?>
<?php
    if(isset($_POST['prijava'])){
        $utf_8=izvrsiUpit($bp,"SET NAMES utf8;");
        $korisnicko_ime=mysqli_real_escape_string($baza,$_POST['korisnicko_ime']);
        $lozinka=mysqli_real_escape_string($baza,$_POST['lozinka']);

        if(!empty($korisnicko_ime)&&!empty($lozinka)){
            $upit="SELECT korisnik_id, tip_korisnika_id, ime, prezime FROM korisnik WHERE korisnicko_ime='$korisnicko_ime' AND lozinka='$lozinka'";
            $rez_upita=izvrsiUpit($baza,$upit);
            if(mysqli_num_rows($rez_upita)==0){
                echo "Ne postoji korisnik sa unesenim korisničkim imenom ili lozinkom".mysqli_error($rez_upita);
            }
            else{
                list($id,$tip,$ime,$prezime)=mysqli_fetch_array($rez_upita);
                $_SESSION['aktivni_korisnik']=$korisnicko_ime;
                $_SESSION['aktivni_korisnik_ime']=$ime;
                $_SESSION['aktivni_korisnik_prezime']=$prezime;
                $_SESSION['aktivni_korisnik_id']=$id;
                $_SESSION['aktivni_korisnik_tip']=$tip;
                

                if($_SESSION['aktivni_korisnik_tip']==2)
                {
                    header("Location:upravljanje_izborima.php");
                }
                elseif($_SESSION['aktivni_korisnik_tip']==3)
                {
                    header("Location:izbori.php");
                }
                else
                {
                    header("Location:index.php");
                }
            }
        }
        else{
            echo "Molim unesite korisničko ime i lozinku!"."<br>";
        }
    }



?>

<!DOCTYPE html>
<html>
<head>

</head>

<body>
    <form class="prijava" name="prijava" action="prijava.php" method="post" >
     
    <p id="naslov_tablicaprijava">Prijava u sustav</p>
    <table class="prijava_tablica"> 
            <tbody>
                <tr >
                    <td class="prijavalabela">
                        <label for="korisnicko_ime">Korisničko ime</label>
                    </td>
                    <td >
                        <input class="prijavainput" name="korisnicko_ime" id="korisnicko_ime" type="text"/>
                    </td>
                </tr>
                <tr>
                    <td class="prijavalabela">
                        <label for="lozinka">Lozinka</label>
                    </td>
                    <td class="prijavapoljeunos">
                        <input class="prijavainput" name="lozinka" id="korisnicko_ime" type="password"/>
                    </td>
                </tr>
                <tr>
                    <td colspan="2"> 
                        <input class="prijavadugme" name="prijava" type="submit" value="Prijava"/>
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
</body>
</html>
<?php
    zatvoriVezuNaBazu($baza);
?>

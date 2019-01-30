<?php
    include("baza.php");
    ini_set('default_charset','UTF-8');
    header("Content-Type: text/html;charset=UTF-8");
    if(session_id()=="") session_start();

    $trenutna_stranica=basename($_SERVER["PHP_SELF"]);
    $aktivni_korisnik_tip=0;

    if (isset($_SESSION['aktivni_korisnik'])){
        $aktivni_korisnik=$_SESSION['aktivni_korisnik'];
        $aktivni_korisnik_ime=$_SESSION['aktivni_korisnik_ime'];
        $aktivni_korisnik_tip=$_SESSION['aktivni_korisnik_tip'];
        $aktivni_korisnik_id=$_SESSION['aktivni_korisnik_id'];
    }
?>

<!DOCTYPE html>
<html>

<head>
    <meta name="autor" content="Domagoj Samardžić"/>
    <meta name="datum" content="25.6.2018."/>
    <meta charset="utf-8"/>
    <link href="stil.css" rel="stylesheet" type="text/css"/>
</head>

<body>
    <header>
        <nav id="navigacija" class="meni">
            <?php
                echo "<a href='index.php'";
                if($trenutna_stranica=="index.php")echo "class='aktivna'";
                echo ">POČETNA</a>";
                echo "<a href='o_autoru.html'>O AUTORU</a>";
                echo "<a href='izborna_mjesta.php'";
                if($trenutna_stranica=="izborna_mjesta.php")echo "class='aktivna'";
                echo ">IZBORNA MJESTA</a>";


                if(isset($_SESSION['aktivni_korisnik']))
                {
                    
                    echo "<a href='izbori.php'";
                    if($trenutna_stranica=="izbori.php")echo "class='aktivna'";
                    echo ">IZBORI</a>";
                
                    if($aktivni_korisnik_tip==2)
                    {
                        echo "<a href='upravljanje_izborima.php'";
                        if($trenutna_stranica=="upravljanje_izborima.php")echo "class='aktivna'";
                        echo ">UPRAVLJANJE IZBORIMA</a>";     
                    }
                    if($aktivni_korisnik_tip==1)
                    {
                        echo "<a href='upravljanje_izborima.php'";
                        if($trenutna_stranica=="upravljanje_izborima.php")echo "class='aktivna'";
                        echo ">UPRAVLJANJE IZBORIMA</a>";
                        echo "<a href='korisnici.php'";
                        if($trenutna_stranica=="korisnici.php")echo "class='aktivna'";
                        echo">KORISNICI</a>";
                    } 
                    echo "<div class='meni-desno'>";
                    echo "<a href='odjava.php'>Odjava</a>";
                    echo "<a href='#'>$aktivni_korisnik_ime</a>";
                    echo "</div>";
                }
                else{
                    echo "<div class='meni-desno'>";
                    echo "<a href='prijava.php'>Prijava</a>";
                    echo "</div>";
            }

            ?>
        </nav>
    </header>
</body>
</html>

<?php
    define("POSLUZITELJ","localhost");
    define("BAZA","iwa_2017_vz_projekt");
    define("BAZA_KORISNIK","iwa_2017");
    define("BAZA_LOZINKA","foi2017");


function spojiSeNaBazu(){

    $veza=mysqli_connect(POSLUZITELJ,BAZA_KORISNIK,BAZA_LOZINKA);

    if(!$veza) echo "GREŠKA! Nastao je problem prilikom povezivanja s bazom podataka!".mysqli_connect_error();


    mysqli_select_db($veza,BAZA);
	
    if(mysqli_error($veza)!=="") echo "GREŠKA! Nastao je problem prilikom odabira baze podataka!".mysqli_error($veza);

	
    mysqli_set_charset($veza,"utf-8");


    return $veza;
    }

function izvrsiUpit($veza, $upit){
    
    $rezultat=mysqli_query($veza,$upit);

    if(mysqli_error($veza)!=="") echo "GREŠKA! Nastao je problem prilikom izvršavanja upita ".$upit." nad bazom podataka!".mysqli_error($veza);

    return $rezultat;
}

function zatvoriVezuNaBazu($veza){
    mysqli_close($veza);
}

?>
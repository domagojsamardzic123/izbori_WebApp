<?php
session_start();
if(session_destroy()){
        unset($_SESSION["aktivni_korisnik"]);
        unset($_SESSION['aktivni_korisnik_ime']);
        unset($_SESSION["aktivni_korisnik_tip"]);
        unset($_SESSION["aktivni_korisnik_id"]);
        session_destroy();
        header("Location:index.php");
    }


?>
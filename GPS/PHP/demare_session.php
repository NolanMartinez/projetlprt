<?php
    if (!empty($_COOKIE['deco'])){
            setcookie("deco", "", time() - 3600);
            session_start();
            session_destroy();
        }
    session_start();
    if (!empty($_SESSION['identifiant'])){
        
    }
    else{
        session_destroy();
        $url = '../PHP';
		header('Location: '.$url);;
    }
?>
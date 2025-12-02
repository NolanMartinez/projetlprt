<?php
    session_start();
    if (!empty($_SESSION['identifiant'])){

    }
    else{
        session_destroy();
        $url = '../test';
        $url = 'page_test.php';
		header('Location: '.$url);;
    }
?>
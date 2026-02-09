<?php
$host_name = "localhost";
$user_name = "ESP";
$mdp = "objet_connect";
$db_name = "Projet_objet_connectes";

$db_connection = mysqli_connect($host_name, $user_name, $mdp, $db_name);

if (!$db_connection){
    die("connection échoué " . mysqli_connect_error());
}
if (isset($_GET['data'])){
    $type_data = $_GET['data'];
    $type_min_max = $type_data;
    $type_min_max .="_min, ";
    $type_min_max .= $type_data;
    $type_min_max .="_max";

    $sql = "SELECT $type_min_max FROM valeur_par_defaut WHERE actif =1;";
    $result=mysqli_query($db_connection,$sql);
    if($result){
        while ($row = mysqli_fetch_row($result)){
            $resultat_json = json_encode($row);
            echo($resultat_json);
        } 
    }
    else{
        echo("ERREUR " .$sql. "<br>" . mysqli_error($db_connection));
    }
}
else{
    echo("connection à la base de données OK<br>");

    if(isset($_POST["temperature"]) && isset($_POST["luminosites"]) && isset($_POST["humidite_pot"]) && isset($_POST["humidite_air"])){
        $temperature = $_POST["temperature"];
        $luminosites = $_POST["luminosites"];
        $humidite_pot = $_POST["humidite_pot"];
        $humidite_air = $_POST["humidite_air"];

        if($temperature == "nan"){
            die("Variables incorrectes");
        }
        $sql = "INSERT INTO data (temperature,humidite_pot, humidite_air, luminosite) VALUE ($temperature, $humidite_pot, $humidite_air, $luminosites);";
        if(mysqli_query($db_connection,$sql)){
            echo("\nNouvelles données enregistrées");
        }
        else{
            echo("ERREUR " .$sql. "<br>" . mysqli_error($db_connection));
        }
        
    } 
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../CSS/style.css" />
    <link rel="stylesheet" href="../CSS/style_modif.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
    crossorigin=""/>
</head>
<body >
    <script>
                function envoie_cap() {
                    document.cookie = "deco= 0;";
                    document.getElementById('date').value = "nouvelle";
                    document.cookie = "suppr= n_suppr;";
                    document.forms["info"].submit();
                }
                function envoi() {
                    document.cookie = "ajout= 0;";
                    document.cookie = "suppr= n_suppr;";
                    document.forms["info"].submit();
                }
                function envoie_ajout() {
                    document.cookie = "ajout= 1;";
                    document.cookie = "suppr= n_suppr;";
                    document.forms["info"].submit();
                }
                function envoie_suppr() {
                    document.cookie = "ajout= 1;";
                    document.cookie = "suppr= o_suppr;";
                    document.forms["info"].submit();
                }
            </script>
    <script src="../JS/script_page.js"></script>
    <?php
        include ( "variable.php");
        include ( "demare_session.php");
        
        function alert($msg) {
            echo "<script type='text/javascript'>alert('$msg');</script>";
        }
        if (!empty($_COOKIE['suppr'])){
            $suppr = $_COOKIE['suppr'];
        }
        else{
            $suppr ="n_suppr";
        }
        if (!empty($_COOKIE['ajout'])){
            $ajout = $_COOKIE['ajout'];
        }
        else{
            $ajout = 0;
        }
        if (!empty($_POST['date'])){
            $id_date = $_POST['date'];
        }
        else{
            $id_date = "nouvelle";
        }
        if (!empty($_POST['capteur'])){
            $id_cap = $_POST['capteur'];
        }
        else{
            $id_cap = "1";
        }
        if (!empty($_POST['x'])){
            $latitude = $_POST['x'];
        }
        else{
            $latitude = null;
        }
        if (!empty($_POST['y'])){
            $longitude = $_POST['y'];
        }
        else{
            $longitude = null;
        }
        $db_connection = pg_connect("host=$ip port=5432 dbname=projet_gps user=utilisateur password=utilisateur");
        if (!$db_connection) {
            echo "An error occurred.\n";
        exit;
        }

        $date = date("Y-m-j G:i:s");
            if ($id_cap and $latitude and $longitude and $ajout == 1 and $id_date == "nouvelle" and $suppr == "n_suppr"){
                $sql_id = pg_query($db_connection, "SELECT id_donnees FROM donnees ORDER BY id_donnees");
                while ($row = pg_fetch_row($sql_id)) {
                    $id_donnees = $row[0];
                }
                $id_donnees += 1;
                $db_connection_envoie = pg_connect("host=$ip port=5432 dbname=projet_gps user=envoie password=script"); 
                $sql_envoie = pg_query($db_connection_envoie, "INSERT INTO donnees (id_donnees, id_capteur, longitude, latitude, date_donnees)VALUES ($id_donnees, $id_cap, $longitude, $latitude, '$date')");
                $ajout=0;
                $id_date = $id_donnees;
                setcookie("ajout", 0);
                alert("la donnée à été ajouté");
            }
            elseif ($id_cap and $latitude and $longitude and $ajout == 1 and $id_date != "nouvelle" and $suppr == "o_suppr"){
                $db_connection_envoie = pg_connect("host=$ip port=5432 dbname=projet_gps user=supprimeur password=jaimesuppr");
                $sql_envoie = pg_query($db_connection_envoie, "DELETE FROM donnees WHERE id_donnees = '$id_date'");
                $ajout=0;
                $id_date = "nouvelle";
                setcookie("ajout", 0);
                setcookie("suppr", "n_suppr");
                alert("la donnée à été supprimé");

            }
            elseif ($id_cap and $latitude and $longitude and $ajout == 1 and $id_date != "nouvelle" and $suppr == "n_suppr"){
                $db_connection_envoie = pg_connect("host=$ip port=5432 dbname=projet_gps user=edit password=edit2000");
                $sql_envoie = pg_query($db_connection_envoie, "UPDATE donnees SET longitude = '$longitude', latitude = '$latitude' WHERE id_donnees = '$id_date'");
                $ajout=0;
                setcookie("ajout", 0);
                alert("la donnée à été modifié");
            }
            if ($_SESSION['identifiant'] != "aadmin"){
                header('Location: '."page.php");
            }
    ?>
    <script>
        function affiche_bandeau(){
            if (document.getElementById("deroulant").style.display=="block"){
                document.getElementById("deroulant").style.display="none";
            }
            else{
                document.getElementById("deroulant").style.display="block";
                document.getElementById("visualiser").style.display="block";
                document.getElementById("modifier").style.display="block";
            }
        }
    </script>
    <div id="bandeau">
        <ul>
            <li class="utilisateur">
                <?php 
                    echo '<p onclick="affiche_bandeau()" id="nom">';
                    echo $_SESSION['identifiant'];
                    echo '</p>';
                ?>
                <ul id="deroulant">
                    <li>
                        <input type="button" id="deco" value="déconnexion" onclick="deco()">
                    </li>
                    <li class="sous_menus" id="visualiser">
                        <p><a href="page.php">Visualiser</a></p>
                    </li>
                    <li class="sous_menus" id="modifier">
                        <p>Modifier</p>
                        <ul class="element_modifier">
                            <li><a href="#">Zones</a></li>
                            <li><a href="#">Capteur</a></li>
                        </ul>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
    <div class="corp">
        <form method="post" id="info">
            <label for="capteur">Choisissez un capteur :</label>
            <select id="capteur" name="capteur" onchange="envoie_cap()">
                <?php
                $sql_cap = pg_query($db_connection, "SELECT * FROM capteur");
                while ($row = pg_fetch_row($sql_cap)) {
                    if ($row[0] == $id_cap){
                        echo '<option selected value="';
                    }
                    else{
                        echo '<option value="';
                    }
                    echo $row[0];
                    echo '">';
                    echo $row[1];
                    echo '</option>';
                }
                ?>
            </select>
            <label for="date">Choisissez une date :</label>
            <select id="date" name="date" onchange="envoi()">
                <option value="nouvelle">Nouvelle</option>
                <?php
                $sql_date = pg_query($db_connection, "SELECT * FROM donnees WHERE Id_capteur = '$id_cap' ORDER BY id_donnees DESC;");
                if (!$sql_date) {
                    echo "An error occurred.\n";
                exit;
                }
                while ($row = pg_fetch_row($sql_date)) {
                    $date = $row[5];
                    $date_form = date('\l\e j/n/Y à G\Hi s\s', strtotime($date));
                    if ($row[0] == $id_date){
                        echo '<option selected value="';
                    }
                    else{
                        echo '<option value="';
                    }
                    echo $row[0];
                    echo '">';
                    echo $date_form;
                    echo '</option>';
                }
                ?>
            </select>
            <div class="zone_button">
                <input type="button" value="Ajouter" id="envoie" onclick="envoie_ajout()">
                <?php
                if ($id_date != "nouvelle"){
                    echo('<input type="button" value="Supprimer" id="suppr_btn" onclick="envoie_suppr()">');
                }
                ?>
                <input type="button" id="reset" value="Réinitialiser">
            </div>
        
            <div class="donnees">
                <label for="x" class="label_coordonnes"><p id="x_label">Cliquer sur</p></label>
                <input type="text" name="x" id="x" class="zone_coordonnes" onchange="entre_coord()">

                <label for="x" class="label_coordonnes"><p id="y_label">la carte</p></label>
                <input type="text" name="y" id="y" class="zone_coordonnes" onchange="entre_coord()">
            </div>
        </form>
        <div id="map"></div>
        <script
            src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"
            integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA=="
            crossorigin=""
        ></script>
    <script src="../JS/script_modif.js"></script>
    </div>
    <script>
        <?php
            if ($id_date !="nouvelle"){
                //echo $id_date;
                $sql_data = pg_query($db_connection, "SELECT * FROM donnees WHERE id_donnees = '$id_date'");
                if (!$sql_data) {
                    echo "An error occurred.\n";
                exit;
                }
                while ($row = pg_fetch_row($sql_data)) {
                    echo ('document.getElementById("x").value=');
                    echo($row[3]);
                    echo(";");
                    echo ('document.getElementById("y").value=');
                    echo($row[2]);
                    echo(";");
                    echo('document.getElementById("envoie").value = "Modifier";');
                    echo('document.getElementById("x_label").innerHTML = "x = ";');
                    echo('document.getElementById("y_label").innerHTML = "y = ";');
                    echo('document.getElementById("x").style.visibility = "visible";');
                    echo('document.getElementById("y").style.visibility = "visible";');
                    echo('entre_coord()');
                }
            }
        ?>
    </script>
</body>
</html>

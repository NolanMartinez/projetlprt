<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../CSS/style.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
    crossorigin=""/>
</head>
<body >
    <script src="../JS/script_page.js"></script>
    <?php
        include ( "variable.php");
        include( "demare_session.php");
        if (!empty($_POST['capteur'])){
            $id_cap = $_POST['capteur'];
        }
        else{
            $id_cap = "erreur";
        }
        if (!empty($_POST['date'])){
            $id_date = $_POST['date'];
        }
        else{
            $id_date = "defaut";
        }

        $db_connection = pg_connect("host=$ip port=5432 dbname=projet_gps user=utilisateur password=utilisateur");
        if (!$db_connection) {
            echo "An error occurred.\n";
        exit;
        }
        if ($_SESSION['droit'] == "admin"){
            $sql_cap = pg_query($db_connection, "SELECT * FROM capteur WHERE actif = 't' LIMIT 1");
        }
        else{
            $id_compte = $_SESSION['id'];
            $sql_cap = pg_query($db_connection, "SELECT * FROM capteur INNER JOIN capteur_compte USING (id_capteur) WHERE id_compte = $id_compte AND actif = 't' LIMIT 1");
        }
        while ($row = pg_fetch_row($sql_cap)) {
            if($id_cap == "erreur"){
                $id_cap = $row[0];
            }
        }
        if ($_SESSION['droit'] == "ajouter"){
            header('Location: '."page_ajout.php");;
        }
    ?>
    <script>
        function affiche_bandeau(){
            if (document.getElementById("deroulant").style.display=="block"){
                document.getElementById("deroulant").style.display="none";
                document.getElementById("logo_bandeau").innerHTML="▼";

            }
            else{
                document.getElementById("deroulant").style.display="block";
                document.getElementById("logo_bandeau").innerHTML="▲";
                document.getElementById("modifier").style.display="block";
                <?php
                if ($_SESSION['droit'] != "voir"){
                    echo'document.getElementById("btn_adj_donnees").style.display="block";';
                }
                ?>
            }
        }
        function affiche_donnees() {
            if((document.getElementById("liste_donnees"))){
                if (document.getElementById("liste_donnees").style.display=="block"){
                    document.getElementById("liste_donnees").style.display="none";
                    document.getElementById("btn_plus_moins").value="▼ Voir plus ▼";
                }
                else{
                    document.getElementById("liste_donnees").style.display="block";
                    document.getElementById("btn_plus_moins").value="▲ Voir moins ▲"
                }  
            }
        }
    </script>
    <div id="bandeau">
        <ul>
            <li class="utilisateur">
                <div id="div_nom_logo" onclick="affiche_bandeau()">
                    <?php 
                        echo '<p id="nom">';
                        echo $_SESSION['identifiant'];
                        echo '</p>';
                    ?>
                    <p id="logo_bandeau">▼</p>
                </div>
                <ul id="deroulant">
                    <li>
                        <input type="button" id="deco" value="déconnexion" onclick="deco()">
                    </li>
                    <li class="sous_menus" id="modifier">
                        <p>Ajouter/Modifier</p>
                        <ul class="element_modifier">
                            <li><a href="#">Zones</a></li>
                            <li><a href="page_modif_capteur.php">Capteur</a></li>
                            <li><a href="page_ajout.php" id="btn_adj_donnees">Données</a></li>
                        </ul>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
    <div class="corp">
        <form method="post" id="info">
            <script>
                function envoie_cap() {
                    document.getElementById('date').value = "defaut";
                    document.forms["info"].submit();
                }
                function envoie() {
                    document.forms["info"].submit();
                }
            </script>
            <label for="capteur">Choisissez un capteur :</label>
            <select id="capteur" name="capteur" onchange="envoie_cap()">
                <?php
                if ($_SESSION['droit'] == "admin"){
                    $sql_cap = pg_query($db_connection, "SELECT * FROM capteur WHERE actif = 't' ORDER BY id_capteur");
                }
                else{
                    $id_compte = $_SESSION['id'];
                    $sql_cap = pg_query($db_connection, "SELECT * FROM capteur INNER JOIN capteur_compte USING (id_capteur) WHERE id_compte = $id_compte AND actif = 't' ORDER BY id_capteur");
                }
                $iteration =0;
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
                    $iteration +=1;
                }
                ?>
            </select>
            <label for="date">Choisissez une date :</label>
            <select id="date" name="date" onchange="envoie()">
                <option value="defaut">Maintenant</option>
                <?php
                if ($id_date == "tout") {
                    echo '<option value="tout" selected>Tous les capteur</option>';
                }
                else{
                    echo '<option value="tout">Tous les capteur</option>';
                }
                $sql_date = pg_query($db_connection, "SELECT * FROM donnees WHERE Id_capteur = '$id_cap' ORDER BY Id_donnees");
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
            <input type="submit" value="Réinitialiser">
        </form>
        <?php
        if ($id_date == 'defaut'){
            $sql = pg_query($db_connection, "SELECT * FROM donnees WHERE Id_capteur = '$id_cap' ORDER BY Id_donnees DESC LIMIT 1");
        }
        elseif($id_date == "tout"){
            $sql = pg_query($db_connection, "SELECT * FROM donnees WHERE Id_capteur = '$id_cap' ORDER BY Id_donnees DESC");
        }
        else{
            $sql = pg_query($db_connection, "SELECT * FROM donnees WHERE Id_donnees = '$id_date'");
        }
        $premier = 0;
        while ($row = pg_fetch_row($sql)) {
            $x = $row[3];
            $y = $row[2];
            if ($premier ==1){
                echo('<ul id="liste_donnees">');
            }
            if ($premier ==0 ){
                echo '<div class="donnees">';
                if ($id_date == "tout"){
                    $date = $row[5];
                    $date_form = date('j/n/Y G\Hi s\s', strtotime($date));
                    echo '<p class="coordonnees">Le ';
                    echo $date_form;
                    echo '</p>';
                }
                echo'<p class="coordonnees">x = ';
                echo $row[3];
                echo '</p>';
                echo '</p>';
                echo '<p class="coordonnees">y = ';
                echo $row[2];
                echo '</div>';
            }        
            else{
                echo('<li>');
                echo '<div class="donnees">';
                if ($id_date == "tout"){
                    $date = $row[5];
                    $date_form = date('j/n/Y G\Hi s\s', strtotime($date));
                    echo '<p class="coordonnees">Le ';
                    echo $date_form;
                    echo '</p>';
                }
                echo'<p class="coordonnees">x = ';
                echo $row[3];
                echo '</p>';
                echo '</p>';
                echo '<p class="coordonnees">y = ';
                echo $row[2];
                echo '</div>';
                echo('</li>');
            }
            $premier +=1;
            
        }
        if ($premier != 1){
            echo('</ul>');
            echo('<div class="donnees"><input type="button" value="▼ Voir plus ▼" id="btn_plus_moins" onclick="affiche_donnees()"></div>');
        }
        ?>
        </style>
        <div id="map"></div>
        <script
            src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"
            integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA=="
            crossorigin=""
        ></script>
    <script src="../JS/script_maps.js"></script>
    <script>
        <?php
        if ($id_date == 'defaut' or $id_date == "tout"){
            $sql = pg_query($db_connection, "SELECT * FROM donnees WHERE Id_capteur = '$id_cap' ORDER BY Id_donnees DESC LIMIT 1");
        }
        else{
            $sql = pg_query($db_connection, "SELECT * FROM donnees WHERE Id_donnees = '$id_date'");
        }
        while ($row = pg_fetch_row($sql)) {
            echo 'var marker = L.marker([';
            echo $row[3];
            echo ', ';
            echo $row[2];
            echo ']).addTo(map);';
        }
        ?>
        var polyline = L.polyline([
            <?php
                if ($id_date == "tout"){
                    $sql = pg_query($db_connection, "SELECT * FROM donnees WHERE Id_capteur = '$id_cap' ORDER BY Id_donnees");
        
                    while ($row = pg_fetch_row($sql)) {
                        echo '[';
                        echo $row[3];
                        echo ', ';
                        echo $row[2];
                        echo '],';
                    }
                }
            ?>
        ]).addTo(map);
    </script>
    </div>
</body>
</html>

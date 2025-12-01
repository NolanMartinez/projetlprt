<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
    crossorigin=""/>
</head>
<body>

    <div id="zoneAlert" class="hidden">Capteur hors de la zone !</div>

    <script src="script_page.js"></script>
    <?php
        if (!empty($_POST['capteur'])){
            $id_cap = $_POST['capteur'];
        }
        else{
            $id_cap = "1";
        }
        if (!empty($_POST['date'])){
            $id_date = $_POST['date'];
        }
        else{
            $id_date = "defaut";
        }
        if (!empty($_COOKIE['id'])){
            $id= $_COOKIE['id'];
        }
        else{
            $id= null;
            $url = '../test';
            header('Location: '.$url);
        }

        if (!empty($_COOKIE['mdp'])){
            $mdp= $_COOKIE['mdp'];
        }
        else{
            $mdp= null;
        }
        $db_connection = pg_connect("host=10.108.6.226 port=5432 dbname=projet_gps user=$id password=$mdp");
        if (!$db_connection) {
            echo "An error occurred.\n";
            exit;
        }
    ?>

    <div id="utilisateur">
        <?php 
            echo '<label for="deco">';
            echo $id;
            echo ' :</label>';
        ?>
        <input type="button" id="deco" value="déconnexion" onclick="deco()">
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
            <select id="date" name="date" onchange="envoie()">
                <option value="defaut">Maintenant</option>
                <?php
                $sql_date = pg_query($db_connection, "SELECT * FROM donnees WHERE Id_capteur = '$id_cap'");
                if (!$sql_date) {
                    echo "An error occurred.\n";
                    exit;
                }
                while ($row = pg_fetch_row($sql_date)) {
                    $dateformat = $row[5];
                    if ($row[0] == $id_date){
                        echo '<option selected value="';
                    }
                    else{
                        echo '<option value="';
                    }
                    echo $row[0];
                    echo '">';
                    echo $row[5];
                    echo '</option>';
                }
                if ($id_date == "tout") {
                    echo '<option value="tout" selected>Tous les capteur</option>';
                }
                else{
                    echo '<option value="tout">Tous les capteur</option>';
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
            $sql = pg_query($db_connection, "SELECT * FROM donnees WHERE Id_capteur = '$id_cap' ORDER BY Id_donnees");
        }
        else{
            $sql = pg_query($db_connection, "SELECT * FROM donnees WHERE Id_donnees = '$id_date'");
        }
        while ($row = pg_fetch_row($sql)) {
            echo '<div class="donnees"><p class="coordonnees">x = ';
            echo $row[3];
            echo '</p><p class="coordonnees">y = ';
            echo $row[2];
            echo '</p></div>';
        }
        ?>

        <div id="map"></div>

        <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>

        <script>
            <?php
            if ($id_date == 'defaut' || $id_date == "tout") {
                $sql = pg_query($db_connection, "SELECT * FROM donnees WHERE Id_capteur = '$id_cap' ORDER BY Id_donnees DESC LIMIT 1");
            } else {
                $sql = pg_query($db_connection, "SELECT * FROM donnees WHERE Id_donnees = '$id_date'");
            }
            $row = pg_fetch_row($sql);
            if ($row) {
                echo "var currentLat = " . $row[3] . ";\n";
                echo "var currentLng = " . $row[2] . ";\n";
            } else {
                echo "var currentLat = 46.75; var currentLng = 1.7;";
            }
            ?>

            <?php if ($id_date == "tout"): ?>
            var polylinePoints = [
                <?php
                $sql = pg_query($db_connection, "SELECT * FROM donnees WHERE Id_capteur = '$id_cap' ORDER BY Id_donnees");
                while ($row = pg_fetch_row($sql)) {
                    echo "[" . $row[3] . ", " . $row[2] . "],";
                }
                ?>
            ];
            <?php endif; ?>
        </script>

        <script src="script_maps.js"></script>
    </div>
</body>
</html>
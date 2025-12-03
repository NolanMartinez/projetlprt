<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet" href="style_modif.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
    crossorigin=""/>
</head>
<body >
    <script src="script_page.js"></script>
    <?php
        include ( "variable.php");
        include ( "demare_session.php");
        if (!empty($_POST['capteur'])){
            $id_cap = $_POST['capteur'];
        }
        else{
            $id_cap = null;
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
    ?>
    <div id="utilisateur">
        <?php 
            echo '<label for="deco">';
            echo $_SESSION['identifiant'];
            echo ' :</label>';
        ?>
        <input type="button" id="deco" value="déconnexion" onclick="deco()">
    </div>
    <div class="corp">
        <form method="post" id="info">
            <label for="capteur">Choisissez un capteur :</label>
            <select id="capteur" name="capteur">
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
            <div class="zone_button">
                <input type="submit" value="Ajouter">
                <input type="button" id="reset" value="Réinitialiser">
            </div>
        
            <div class="donnees">
                <label for="x" class="label_coordonnes"><p id="x_label">Cliquer sur</p></label>
                <input type="text" name="x" id="x" class="zone_coordonnes" onchange="entre_coord()">

                <label for="x" class="label_coordonnes"><p id="y_label">la carte</p></label>
                <input type="text" name="y" id="y" class="zone_coordonnes" onchange="entre_coord()">
            </div>
        </form>
        <?php 
            $date = date("Y-n-j");
            if ($id_cap and $latitude and $longitude){
                $sql_id = pg_query($db_connection, "SELECT id_donnees FROM donnees ORDER BY id_donnees");
                while ($row = pg_fetch_row($sql_id)) {
                    $id_donnees = $row[0];
                }
                $id_donnees += 1;
                $db_connection_envoie = pg_connect("host=$ip port=5432 dbname=projet_gps user=envoie password=script"); 
                $sql_envoie = pg_query($db_connection_envoie, "INSERT INTO donnees (id_donnees, id_capteur, longitude, latitude, date_donnees)VALUES ($id_donnees, $id_cap, $longitude, $latitude, '$date')");
            }

            
        ?>
        <div id="map"></div>
        <script
            src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"
            integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA=="
            crossorigin=""
        ></script>
    <script src="script_modif.js"></script>
    </div>
</body>
</html>

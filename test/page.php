<?php
session_start();

if (empty($_SESSION['user']) || $_SESSION['user'] !== 'admin') {
    header('Location: index.php');
    exit;
}

$id = $_SESSION['user'];

$db_connection = @pg_connect("
    host=10.59.164.226 
    port=5432 
    dbname=projet_gps 
    user=admin
    password=admin
");

if (!$db_connection) {
    die("Erreur : impossible de se connecter à la base de données.");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projet GPS - Carte</title>
    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <script src="script_page.js"></script>

    <div id="utilisateur">
        <label for="deco"><?php echo htmlspecialchars($id); ?> :</label>
        <input type="button" id="deco" value="Déconnexion" onclick="deco()">
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

            <label for="capteur">Capteur :</label>
            <select id="capteur" name="capteur" onchange="envoie_cap()">
                <?php
                $id_cap = $_POST['capteur'] ?? '1';
                $sql_cap = pg_query($db_connection, "SELECT * FROM capteur");
                while ($row = pg_fetch_row($sql_cap)) {
                    $selected = ($row[0] == $id_cap) ? 'selected' : '';
                    echo "<option value=\"{$row[0]}\" $selected>{$row[1]}</option>";
                }
                ?>
            </select>

            <label for="date">Date :</label>
            <select id="date" name="date" onchange="envoie()">
                <option value="defaut">Maintenant</option>
                <?php
                $id_date = $_POST['date'] ?? 'defaut';
                $sql_date = pg_query($db_connection, "SELECT * FROM donnees WHERE Id_capteur = '$id_cap'");
                while ($row = pg_fetch_row($sql_date)) {
                    $selected = ($row[0] == $id_date) ? 'selected' : '';
                    echo "<option value=\"{$row[0]}\" $selected>{$row[5]}</option>";
                }
                $tout_selected = ($id_date === 'tout') ? 'selected' : '';
                echo "<option value=\"tout\" $tout_selected>Tous les points</option>";
                ?>
            </select>

            <input type="submit" value="Valider">
            <input type="reset" value="Réinitialiser" style="margin-left:10px;">
        </form>

        <?php
        if ($id_date === 'defaut') {
            $sql = pg_query($db_connection, "SELECT * FROM donnees WHERE Id_capteur = '$id_cap' ORDER BY Id_donnees DESC LIMIT 1");
        } elseif ($id_date === 'tout') {
            $sql = pg_query($db_connection, "SELECT * FROM donnees WHERE Id_capteur = '$id_cap' ORDER BY Id_donnees");
        } else {
            $sql = pg_query($db_connection, "SELECT * FROM donnees WHERE Id_donnees = '$id_date'");
        }

        echo '<div class="donnees">';
        while ($row = pg_fetch_row($sql)) {
            echo "<p class=\"coordonnees\">x = {$row[3]}</p>";
            echo "<p class=\"coordonnees\">y = {$row[2]}</p>";
        }
        echo '</div>';
        ?>

        <div id="map"></div>
    </div>

    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script>
        var map = L.map('map').setView([48.8584, 2.2945], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

        <?php
        if ($id_date === 'defaut' || $id_date === 'tout') {
            $sql = pg_query($db_connection, "SELECT * FROM donnees WHERE Id_capteur = '$id_cap' ORDER BY Id_donnees DESC LIMIT 1");
        } else {
            $sql = pg_query($db_connection, "SELECT * FROM donnees WHERE Id_donnees = '$id_date'");
        }
        while ($row = pg_fetch_row($sql)) {
            echo "L.marker([{$row[3]}, {$row[2]}]).addTo(map);";
        }

        if ($id_date === 'tout') {
            echo "var polyline = L.polyline([";
            $sql = pg_query($db_connection, "SELECT * FROM donnees WHERE Id_capteur = '$id_cap' ORDER BY Id_donnees");
            $points = [];
            while ($row = pg_fetch_row($sql)) {
                $points[] = "[{$row[3]}, {$row[2]}]";
            }
            echo implode(',', $points);
            echo "], {color: 'red'}).addTo(map);";
        }
        ?>
    </script>
</body>
</html>
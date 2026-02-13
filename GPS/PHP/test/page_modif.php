<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Modifier la zone</title>
    <link rel="stylesheet" href="style.css" />
    <link
      rel="stylesheet"
      href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
      crossorigin=""
    />
    <style>
      .hidden {
        display: none;
      }
      #zoneAlert {
        color: #a94442;
        background-color: #f2dede;
        border: 1px solid #ebccd1;
        padding: 10px;
        margin-bottom: 15px;
        font-weight: bold;
      }
    </style>
</head>
<body>

<?php
if (!empty($_POST['capteur'])) {
    $id_cap = $_POST['capteur'];
} else {
    $id_cap = "1";
}

if (!empty($_POST['date'])) {
    $id_date = $_POST['date'];
} else {
    $id_date = "defaut";
}

if (!empty($_COOKIE['id'])) {
    $id = $_COOKIE['id'];
} else {
    $id = null;
    header('Location: ../test');
    exit;
}

if (!empty($_COOKIE['mdp'])) {
    $mdp = $_COOKIE['mdp'];
} else {
    $mdp = null;
}

require_once __DIR__ . '/config.php';
$db_connection = db_connect_with($id, $mdp);
if (!$db_connection) {
    echo "An error occurred.\n";
    exit;
}
?>

<div id="zoneAlert" class="hidden"> Capteur hors de la zone !</div>

<div id="utilisateur">
    <label for="deco"><?= htmlspecialchars($id) ?> :</label>
    <input type="button" id="deco" value="déconnexion" onclick="deco()" />
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
                $selected = ($row[0] == $id_cap) ? "selected" : "";
                echo "<option value=\"{$row[0]}\" $selected>{$row[1]}</option>";
            }
            ?>
        </select>

        <label for="date">Choisissez une date :</label>
        <select id="date" name="date" onchange="envoie()">
            <option value="defaut">Maintenant</option>
            <?php
            $sql_date = pg_query($db_connection, "SELECT * FROM donnees WHERE Id_capteur = '$id_cap'");
            if (!$sql_date) {
                echo "<option disabled>An error occurred.</option>";
            }
            while ($row = pg_fetch_row($sql_date)) {
                $selected = ($row[0] == $id_date) ? "selected" : "";
                echo "<option value=\"{$row[0]}\" $selected>{$row[5]}</option>";
            }
            $selectedTout = ($id_date == "tout") ? "selected" : "";
            echo "<option value=\"tout\" $selectedTout>Tous les capteurs</option>";
            ?>
        </select>
        <input type="submit" value="Réinitialiser" />
    </form>

    <?php
    if ($id_date == 'defaut') {
        $sql = pg_query($db_connection, "SELECT * FROM donnees WHERE Id_capteur = '$id_cap' ORDER BY Id_donnees DESC LIMIT 1");
    } elseif ($id_date == "tout") {
        $sql = pg_query($db_connection, "SELECT * FROM donnees WHERE Id_capteur = '$id_cap' ORDER BY Id_donnees");
    } else {
        $sql = pg_query($db_connection, "SELECT * FROM donnees WHERE Id_donnees = '$id_date'");
    }
    while ($row = pg_fetch_row($sql)) {
        echo '<div class="donnees"><p class="coordonnees">x = ' . htmlspecialchars($row[3]) . '</p><p class="coordonnees">y = ' . htmlspecialchars($row[2]) . '</p></div>';
    }
    ?>

    <p><a href="page.php">Retour à la vue</a></p>

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

<script>
  const capteurId = "<?php echo addslashes($id_cap); ?>";
  const source = new EventSource("GPS/PHP/test/alert_stream.php?capteur=" + capteurId);

  source.onmessage = function(event) {
    const data = JSON.parse(event.data);
    const alertBox = document.getElementById("zoneAlert");
    if (data.alert === true) {
      alertBox.classList.remove("hidden");
      alertBox.textContent = "Capteur hors de la zone !";
    } else {
      alertBox.classList.add("hidden");
      alertBox.textContent = "";
    }
  };

  source.onerror = function() {
    console.log("Connexion SSE perdue...");
  };
</script>

</body>
</html>
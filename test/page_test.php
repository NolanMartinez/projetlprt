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
<body >
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
        $db_connection = pg_connect("host=10.59.164.226 port=5432 dbname=projet_gps user=$id password=$mdp");
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
            <input type="submit" value="Ajouter">
            <input type="button" id="reset" value="Réinitialiser">
        </form>
        <div class="donnees">
            <p class="coordonnees" id="x">Cliquer sur</p>
            <p class="coordonnees" id="y">la carte</p>
        </div>
        <div id="map"></div>
        <script
            src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"
            integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA=="
            crossorigin=""
        ></script>
    <script src="script_test.js"></script>
    </div>
</body>
</html>

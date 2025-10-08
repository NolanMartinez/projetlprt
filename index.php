
<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Document</title>
</head>
<body>
    <?php
        try {
            $pdo = new PDO("mysql:host=localhost;port=3306;dbname=projet_gps", "root");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Erreur de connexion : " . $e->getMessage());
        }
    ?>
    
	<form method="post">
        <label for="capteur">Choisissez un capteur :</label>
        <select id="capteur" name="capteur">
            <?php
            $sql_cap = "SELECT * FROM capteur";
            foreach ($pdo->query($sql_cap) as $row) {
                echo '<option value="';
                echo $row['Id_capteur'];
                echo '">';
                echo $row['nom'];
                echo '</option>';
            }
            ?>
        </select>
		<label for="choix">Choisissez une date :</label>
        <select id="choix" name="choix">
            <option value="option1" selected>Maintenant</option>
            <option value="option2">07/10/2025</option>
        </select>
        <input type="submit" value="valider">
    </form>
    <?php
    $id_cap = $_POST['capteur'];
    
    $sql = "SELECT * FROM donnees WHERE Id_capteur = '$id_cap' ORDER BY Id_donnees DESC LIMIT 1";
    foreach ($pdo->query($sql) as $row) {
        echo '<p>x = ';
        echo $row['longitude'];
        echo '</p>';

        echo '<p>y = ';
        echo $row['latitude'];
        echo '</p>';

        echo '<p>z = ';
        echo $row['altitude'];
        echo '</p>';
    }

    ?>

</body>
</html>
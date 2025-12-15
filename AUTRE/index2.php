
<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Document</title>
	<style>
		#plans{
			background-color : #ddddee;
			height : 300px;
			width : 456px;
			border: solid 2px #ddddee;
			border-radius : 10px;

		}
		#img-1{
			height: 300px;
			position : absolute;
			border-radius : 10px;
		}
		#img-2{
			height:35px;
			position : relative;
			top : 240px;
			left : 420px;

		}
	</style>
</head>
<body>
    <?php
        $verif = null;
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
        try {
            $pdo = new PDO("mysql:host=localhost;port=3306;dbname=projet_gps", "root");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Erreur de connexion : " . $e->getMessage());
        }
    ?>
    
	<form method="post" id="info">
        <script>
            function envoie() {
                document.forms["info"].submit();
            }
        </script>
        <label for="capteur">Choisissez un capteur :</label>
        <select id="capteur" name="capteur" onchange="envoie()">
            <?php
            $sql_cap = "SELECT * FROM capteur";
            foreach ($pdo->query($sql_cap) as $row) {
                if ($row['Id_capteur'] == $id_cap){
                    echo '<option selected value="';
                }
                else{
                    echo '<option value="';
                }
                echo $row['Id_capteur'];
                echo '">';
                echo $row['nom'];
                echo '</option>';
            }
            ?>
        </select>
		<label for="date">Choisissez une date :</label>
        <select id="date" name="date" onchange="envoie()">
            <option value="defaut">Maintenant</option>
            <?php
            $sql_date = "SELECT * FROM donnees WHERE Id_capteur = '$id_cap'";
            foreach ($pdo->query($sql_date) as $row) {
                if ($row['Id_donnees'] == $id_date){
                    echo '<option selected value="';
                }
                else{
                    echo '<option value="';
                }
                echo $row['Id_donnees'];
                echo '">';
                echo $row['date_donnees'];
                echo '</option>';
            }
            ?>
        </select>
        <input type="submit" value="valider">
    </form>
    <?php
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
    <div id="plans">
	<img id="img-1" src="bat_ab_3.png" >
	<img id="img-2" src="position.png" >
    </div>

</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Document</title>
	<link rel="stylesheet" href="style_index.css" />
</head>
<body>
	<form method="post" id="identification">
		<div id="erreur">
			<p>Mauvais identifiant/mot de passe.</p>
		</div>
		<label for="identifiant">
			<p>Identifiant :</p> 
			<input type="text" name="identifiant" id="identifiant" class="text">
		</label>
		<br>
		<label for="mdp">
			<p>Mot de passe :</p> 
			<input type="password" name="mdp" id="mdp" class="text">
		</label>
		<br>
		<input type="button" value="Se connecter" class="bouton-connection" onclick="Affiche()">
	</form>
	<div class="cachee">
	<p id="id"></p>
	<p id="md_p"></p>
	<script src="script.js"></script>
	<?php
		if (!empty($_POST['identifiant'])){
            $id = $_POST['identifiant'];
        }
        else{
            $id= null;
        }
		if (!empty($_POST['mdp'])){
				$mdp= $_POST['mdp'];
			}
		else{
			$mdp= null;
		}
		if ($id){
			$db_connection = pg_connect("host=10.59.164.226 port=5432 dbname=projet_gps user=$id password=$mdp");
			if (!$db_connection) {
				echo "l'identifiant ou le mot de passe incorect";
				exit;
			}
			else{
				$url = 'page.php';
				header('Location: '.$url);
				exit;
			}
		}
    ?>
	</div>
	<style>
		#erreur {
			visibility: collapse;
			height : 0px;
		}
	</style>
</body>
</html>
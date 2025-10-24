<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Projet GPS</title>
    <link rel="stylesheet" href="style_index.css" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <div class="login-container">
        <form method="post" id="identification">
            <?php
<<<<<<< HEAD
<<<<<<< HEAD
				session_start();
                $erreur = false;
                if ($_POST && !empty($_POST['identifiant']) && !empty($_POST['mdp'])) {
           			$id = trim($_POST['identifiant']);
            		$mdp = trim($_POST['mdp']);
                    $db_connection = @pg_connect("host=10.59.164.226 port=5432 dbname=projet_gps user=$id password=$mdp");
                    
                    if (!$db_connection) {
                        $_SESSION['user'] = $id;
                		header('Location: page.php');
                		exit;
            			} else {
                		$erreur = true;
            			}
				}

				if (!empty($_SESSION['user'])) {
            		header('Location: page.php');
            		exit;
=======
=======
>>>>>>> parent of 67b6c05 (a supprimer)
                $erreur = false;
                $id = !empty($_COOKIE['id']) ? $_COOKIE['id'] : null;
                $mdp = !empty($_COOKIE['mdp']) ? $_COOKIE['mdp'] : null;
                if ($id !== null && $mdp !== null) {
                    $db_connection = @pg_connect("host=10.59.164.226 port=5432 dbname=projet_gps user=$id password=$mdp");
                    
                    if (!$db_connection) {
                        $erreur = true;
                    } else {
                        header('Location: page.php');
                        exit;
                    }
<<<<<<< HEAD
>>>>>>> parent of 67b6c05 (a supprimer)
=======
>>>>>>> parent of 67b6c05 (a supprimer)
                }
            ?>

            <div id="erreur" class="<?php echo $erreur ? 'show' : ''; ?>">
                <p>Mauvais identifiant/mot de passe.</p>
            </div>

            <div class="form-group">
                <label for="identifiant">Identifiant :</label>
                <input type="text" name="identifiant" id="identifiant" class="text" placeholder="Entrez votre identifiant" required>
            </div>

            <div class="form-group">
                <label for="mdp">Mot de passe :</label>
                <input type="password" name="mdp" id="mdp" class="text" placeholder="Entrez votre mot de passe" required>
            </div>

            <input type="button" value="Se connecter" class="bouton-connection" onclick="Affiche()">
        </form>
    </div>
        <script src="script.js"></script>
</body>
</html>
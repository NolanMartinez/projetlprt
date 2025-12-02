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
                $erreur = false;
                $id = !empty($_COOKIE['id']) ? $_COOKIE['id'] : null;
                $mdp = !empty($_COOKIE['mdp']) ? $_COOKIE['mdp'] : null;
                if ($id !== null && $mdp !== null) {
                    $db_connection = @pg_connect("host=10.247.80.226 port=5432 dbname=projet_gps user=$id password=$mdp");
                    
                    if (!$db_connection) {
                        $erreur = true;
                    } else {
                        header('Location: page.php');
                        exit;
                    }
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
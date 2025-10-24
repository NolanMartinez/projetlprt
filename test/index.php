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
    <?php
        session_start();
        $erreur = false;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $identifiant = trim($_POST['identifiant'] ?? '');
            $mdp = trim($_POST['mdp'] ?? '');

            if ($identifiant === 'admin' && $mdp === 'admin') {
                $_SESSION['user'] = 'admin';
                header('Location: page.php');
                exit;
            } else {
                $erreur = true;
            }
        }

        if (!empty($_SESSION['user'])) {
            header('Location: page.php');
            exit;
        }
    ?>

    <div class="login-container">
        <form method="post" id="identification">
            <div id="erreur" class="<?php echo $erreur ? 'show' : ''; ?>">
                <p>Mauvais identifiant/mot de passe.</p>
            </div>

            <div class="form-group">
                <label for="identifiant">Identifiant :</label>
                <input type="text" name="identifiant" id="identifiant" class="text" 
                       placeholder="Entrez votre identifiant" value="admin" required>
            </div>

            <div class="form-group">
                <label for="mdp">Mot de passe :</label>
                <input type="password" name="mdp" id="mdp" class="text" 
                       placeholder="Entrez votre mot de passe" required>
            </div>

            <input type="submit" value="Se connecter" class="bouton-connection">
        </form>
    </div>
</body>
</html>
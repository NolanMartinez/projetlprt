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
    <script src="script_test.js"></script>
    <div class="login-container">
        <form method="post" id="identification">
            <?php
                include ( "variable.php");
                $erreur = false;
                session_start();
                if (!empty($_SESSION['identifiant'])){
                    $url = 'page.php';
		            header('Location: '.$url);
                }
                else{
                    session_destroy();
                }
                if (!empty($_POST['identifiant'])){
                    $id = $_POST['identifiant'];
                }
                else{
                    $id = null;
                }
                if (!empty($_POST['mdp'])){
                    $mdp = $_POST['mdp'];
                    $mdp_hash = hash('sha256',$mdp);
                }
                else{
                    $mdp = null;
                }
                if ($id !== null && $mdp !== null) {
                    $db_connection = @pg_connect("host=$ip port=5432 dbname=projet_gps user=aadmin password=admin");
                    
                    if (!$db_connection) {
                        echo "Ereur\n";
                        exit;
                    }
                    $sql_compte = pg_query($db_connection, "SELECT mdp_hash, droit FROM compte WHERE nom_d_utilisateur = '$id'");
                        while ($row = pg_fetch_row($sql_compte)) {
                            if ($row[0] == $mdp_hash){
                                
                                session_start();
    
                                //On définit des variables de session
                                $_SESSION['identifiant'] = $id;
                                $_SESSION['droit'] = $row[1];
                                header('Location: page.php');

                            }
                            else{
                                $erreur = true;
                            }
                }
                }
                else{
                    $erreur = true;
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

            <input type="button" value="Se connecter" class="bouton-connection" onclick="commit()">
        </form>
    </div>
        <script src="script.js"></script>
</body>
</html>
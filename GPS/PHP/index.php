<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Projet GPS</title>
    <link rel="stylesheet" href="../CSS/style_index.css" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    
    <div class="login-container">
        <form method="post" id="identification">
            <?php
                function alert($msg) {
                    echo "<script type='text/javascript'>alert('$msg');</script>";
                }
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
                    $db_connection = @pg_connect("host=$ip port=5432 dbname=projet_gps user=utilisateur password=utilisateur");
                    
                    if (!$db_connection) {
                        echo "Ereur\n";
                        exit;
                    }
                    $sql_compte = pg_query($db_connection, "SELECT mdp_hash, droit, id_compte FROM compte WHERE nom_d_utilisateur = '$id'");
                        $i =0;
                        while ($row = pg_fetch_row($sql_compte)) {
                            if ($row[0] == $mdp_hash){
                                session_start();
                                //On définit des variables de session
                                $_SESSION['identifiant'] = $id;
                                $_SESSION['droit'] = $row[1];
                                $_SESSION['id'] = $row[2];
                                header('Location: page.php');

                            }
                            else{
                                $erreur = true;
                            }
                            $i++;
                        }
                        if ($i == 0){
                            $erreur = true;
                        }
                }
                else{
                    $erreur = false;
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

            <input type="submit" value="Se connecter" class="bouton-connection">
        </form>
    </div>
        <script src="../JS/script.js"></script>
</body>
</html>
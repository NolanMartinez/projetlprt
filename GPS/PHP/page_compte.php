<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../CSS/style.css" />
    <link rel="stylesheet" href="../CSS/style_compte.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
    crossorigin=""/>
</head>
<body>
    <script src="../JS/script_page.js"></script>
    <?php
        include ( "variable.php");
        include( "demare_session.php");

        function alert($msg) {
            echo "<script type='text/javascript'>alert('$msg');</script>";
        }

        if (!empty($_COOKIE['cookie_session'])){
				$cookie_de_session= $_COOKIE['cookie_session'];
			}
		else{
			$cookie_de_session= null;
            $url = '../test';
			//header('Location: '.$url);
		}

        $db_connection = pg_connect("host=$ip port=5432 dbname=projet_gps user=utilisateur password=utilisateur");
        if (!$db_connection) {
            echo "An error occurred.\n";
        exit;

        }
        if (!empty($_POST['type_sous_page'])){
            $type_sous_page = $_POST['type_sous_page'];
            if (!empty($_POST['mdp_vérif'])){
                $mdp_verif = $_POST['mdp_vérif'];
            }else{
                $mdp_verif = null;
            }
            if ($type_sous_page == "nom_utl"){
                if (!empty($_POST['Nouveau_nom'])){
                    $nouv_nom_utl = $_POST['Nouveau_nom'];
                }else{
                    $nouv_nom_utl = null;
                }
                
            }
            else{
                $nouv_nom_utl = null;
            }
            if ($type_sous_page == "change_mdp"){
                if (!empty($_POST['new_mdp_1'])){
                    $new_mdp_1 = $_POST['new_mdp_1'];
                }else{
                    $new_mdp_1 = null;
                }
                if (!empty($_POST['new_mdp_2'])){
                    $new_mdp_2 = $_POST['new_mdp_2'];
                }else{
                    $new_mdp_2 = null;
                }
                if ($new_mdp_1 == $new_mdp_2){
                    $new_mdp = hash('sha256',$new_mdp_1);
                    
                }
                else{
                    $new_mdp = null;
                }
            }
            else{
                $new_mdp = null;
            }
        }
        else{
            $type_sous_page = null;
            $nouv_nom_utl = null;
            $mdp_verif = null;
            $new_mdp = null;
        }
        if ($type_sous_page == "nom_utl" and $nouv_nom_utl and $mdp_verif){
            $mdp_hash = hash('sha256',$mdp_verif);
            $id = $_SESSION['id'];
            $sql_compte = pg_query($db_connection, "SELECT mdp_hash FROM compte WHERE id_compte = '$id'");
            while ($row = pg_fetch_row($sql_compte)) {
                if ($row[0] == $mdp_hash){
                    $db_connection_envoie = pg_connect("host=$ip port=5432 dbname=projet_gps user=edit password=edit2000");
                    $sql_envoie = pg_query($db_connection_envoie, "UPDATE compte SET nom_d_utilisateur = '$nouv_nom_utl' WHERE id_compte = '$id'");
                    $_SESSION['identifiant']= $nouv_nom_utl;
                    alert('le nouveau nom d\'utilisateur est $nouv_nom_utl');
                }
                else{
                    alert('Le mot de passe est incorrecte');
                }
            }
            
        }
        elseif ($type_sous_page == "change_mdp" and $new_mdp and $mdp_verif){
            $mdp_hash = hash('sha256',$mdp_verif);
            $id = $_SESSION['id'];
            $sql_compte = pg_query($db_connection, "SELECT mdp_hash FROM compte WHERE id_compte = '$id'");
            while ($row = pg_fetch_row($sql_compte)) {
                if ($row[0] == $mdp_hash){
                    $db_connection_envoie = pg_connect("host=$ip port=5432 dbname=projet_gps user=edit password=edit2000");
                    $sql_envoie = pg_query($db_connection_envoie, "UPDATE compte SET mdp_hash = '$new_mdp' WHERE id_compte = '$id'");
                    alert('le mot de passe a été changer');
                }
            }
        }
    ?>
    <script>
        
        function affiche_bandeau(){
            if (document.getElementById("deroulant").style.display=="block"){
                document.getElementById("deroulant").style.display="none";
                document.getElementById("logo_bandeau").innerHTML="▼";

            }
            else{
                document.getElementById("deroulant").style.display="block";
                document.getElementById("logo_bandeau").innerHTML="▲";
                document.getElementById("modifier").style.display="block";
                <?php
                if ($_SESSION['droit'] != "voir"){
                    echo'document.getElementById("btn_adj_donnees").style.display="block";';
                }
                if ($_SESSION['droit'] != "ajouter"){
                    echo'document.getElementById("visualiser").style.display="block";';
                }
                ?>  
            }
        }
        function affiche_corp_page($page_select){
            document.getElementById("info_generale").style.display="none";
            document.getElementById("nom_utilisateur").style.display="none";
            document.getElementById("mot_de_passe").style.display="none";
            document.getElementById($page_select).style.display="block";
            
            if ($page_select == "nom_utilisateur"){
                document.getElementById("type_sous_page").value="nom_utl";
                document.getElementById("btn_nom_utl").classList.add("select");
                document.getElementById("btn_mdp").classList.remove("select");
                document.getElementById("btn_info").classList.remove("select");
            }
            if ($page_select == "mot_de_passe"){
                document.getElementById("type_sous_page").value="change_mdp";
                document.getElementById("btn_mdp").classList.add("select");
                document.getElementById("btn_info").classList.remove("select");
                document.getElementById("btn_nom_utl").classList.remove("select");
            }

            if ($page_select != "info_generale"){
                document.getElementById("verif_mdp_plus_btn").style.display="block";
            }
            else{
                document.getElementById("verif_mdp_plus_btn").style.display="none";
                document.getElementById("type_sous_page").value="";
                document.getElementById("btn_info").classList.add("select");
                document.getElementById("btn_mdp").classList.remove("select");
                document.getElementById("btn_nom_utl").classList.remove("select");
            }
        }
    </script>
    <div id="bandeau">
        <ul>
            <li class="utilisateur">
                <div id="div_nom_logo" onclick="affiche_bandeau()">
                    <?php 
                        echo '<p id="nom">';
                        echo $_SESSION['identifiant'];
                        echo '</p>';
                    ?>
                    <p id="logo_bandeau">▼</p>
                </div>
                <ul id="deroulant">
                    <li>
                        <input type="button" id="deco" value="déconnexion" onclick="deco()">
                    </li>
                    <li class="sous_menus" id="visualiser">
                        <p><a href="page.php">Visualiser</a></p>
                    </li>
                    <li class="sous_menus" id="modifier">
                        <p>Ajouter</p>
                        <ul class="element_modifier">
                            <li><a href="#">Zones</a></li>
                            <li><a href="page_modif_capteur.php">Capteur</a></li>
                            <li><a href="page_ajout.php" id="btn_adj_donnees">Données</a></li>
                        </ul>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
    <div class="corp">
        <form method="post" id="info">
            <ul>
                <li onclick="affiche_corp_page('info_generale')" id="btn_info" class="select">Information général</li>
                <li onclick="affiche_corp_page('nom_utilisateur')" id="btn_nom_utl">Modifier le Nom d'utilisateur</li>
                <li onclick="affiche_corp_page('mot_de_passe')" id="btn_mdp">Modifier le mot de passe</li>
            </ul>
            
            <div id="info_generale" class="sous_page">
                <table>
                    <tbody>
                        <?php
                            echo ('<tr>');
                            echo ('<th>Nom d\'utilisateur : </th><td>');
                            echo($_SESSION['identifiant']);
                            echo('</td>');
                            echo('</tr>');
                            echo('<tr>');
                            echo ('<th>Rôle : </th><td>');
                            echo($_SESSION['droit']);
                            echo('</td>');
                            echo('</tr>');
                        ?>
                    </tbody>
                </table>
            </div>

            <div id="nom_utilisateur" class="sous_page">
                <?php
                echo('<p>Le Nom d\'utilisateur actuel est : <b>');
                echo($_SESSION['identifiant']);
                echo('</b></p>');
                ?>
                <label for="Nouveau_nom">Saisiser un nouveau nom d'utilisateur :</label>
                <input type="text" name="Nouveau_nom" id="Nouveau_nom" class="input_text" placeholder="Nouveau nom d'utilisateur">
            </div>

            <div id="mot_de_passe" class="sous_page">
                <label for="new_mdp_1">Nouveau mot de passe</label>
                <input type="password" name="new_mdp_1" id="new_mdp_1" class="input_text" placeholder="Entrer le nouveau mot de passe">
                <label for="new_mdp_1">Confirmation du nouveau mot de passe</label>
                <input type="password" name="new_mdp_2" id="new_mdp_2" class="input_text" placeholder="Entrer la confirmation du nouveau mot de passe">
            </div>

            <div id="verif_mdp_plus_btn">
                <label for="mdp_vérif">Entrer le mot de passe pour vérifier :</label>
                <input type="password" name="mdp_vérif" id="mdp_vérif" class="input_text" placeholder="Mot de passe">
                <input type="submit" id="btn_valider" value="Valider">
            </div>

            <input type="hidden" name="type_sous_page" id="type_sous_page" value="">
        </form>
        
        </style>
    </script>
    </div>
</body>
</html>

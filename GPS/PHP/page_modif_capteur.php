<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../CSS/style.css" />
    <link rel="stylesheet" href="../CSS/style_modif_capteur.css" />
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

        if (!empty($_POST['capteur'])){
            $id_cap = $_POST['capteur'];
        }
        else{
            $id_cap = "erreur";
        }
        if (!empty($_POST['suppr/ajout'])){
            $suppr_ajout = $_POST['suppr/ajout'];
            if ($suppr_ajout == 'ajout'){
                if (!empty($_POST['new_compte_acces'])){
                    $id_compte_ajout_suppr = $_POST['new_compte_acces'];
                }
                else{
                    $id_compte_ajout_suppr = "";
                }
            }elseif($suppr_ajout == 'suppr'){
                if (!empty($_POST['id_suppr_accees'])){
                    $id_compte_ajout_suppr = $_POST['id_suppr_accees'];
                }
                else{
                    $id_compte_ajout_suppr = "";
                }
            }elseif($suppr_ajout == 'renommer'){
                if (!empty($_POST['nouveau_nom'])){
                    $id_compte_ajout_suppr = $_POST['nouveau_nom'];
                }
                else{
                    $id_compte_ajout_suppr = "";
                }
            }elseif($suppr_ajout == 'activer_desactiver'){
                if(!empty($_POST['id_suppr_accees'])){
                    $id_compte_ajout_suppr = $_POST['id_suppr_accees'];
                }
                else{
                    $id_compte_ajout_suppr = "";
                }
            }
        }
        else{
            $suppr_ajout = "";
        }
        if (empty($id_compte_ajout_suppr)){
            $id_compte_ajout_suppr = null;
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
        if ($_SESSION['droit'] == "admin"){
            $sql_cap = pg_query($db_connection, "SELECT * FROM capteur LIMIT 1");
        }
        else{
            $id_compte = $_SESSION['id'];
            $sql_cap = pg_query($db_connection, "SELECT * FROM capteur WHERE proprietaire = $id_compte LIMIT 1");
        }
        while ($row = pg_fetch_row($sql_cap)) {
            if($id_cap == "erreur"){
                $id_cap = $row[0];
            }
        }

        if ($id_cap and $id_compte_ajout_suppr and $suppr_ajout == 'ajout'){
            $sql_id = pg_query($db_connection, "SELECT id_capteur_compte FROM capteur_compte ORDER BY id_capteur_compte");
            while ($row = pg_fetch_row($sql_id)) {
                $id_capteur_compte = $row[0];
            }
            $id_capteur_compte += 1;
            $db_connection_envoie = pg_connect("host=$ip port=5432 dbname=projet_gps user=envoie password=script"); 
            $sql_envoie = pg_query($db_connection_envoie, "INSERT INTO capteur_compte (id_capteur_compte, id_compte, id_capteur)VALUES ($id_capteur_compte, $id_compte_ajout_suppr, $id_cap)");
            alert("le capteur à été associer à l\'utilisateur");
        }
        elseif ($id_cap and $id_compte_ajout_suppr and $suppr_ajout == 'suppr'){
            $db_connection_envoie = pg_connect("host=$ip port=5432 dbname=projet_gps user=supprimeur password=jaimesuppr");
            $sql_envoie = pg_query($db_connection_envoie, "DELETE FROM capteur_compte WHERE id_compte =$id_compte_ajout_suppr and id_capteur = '$id_cap'");
            alert("le capteur à été dissocier à l\'utilisateur");
        }
        elseif ($id_cap and $id_cap != "nouveau" and $id_compte_ajout_suppr and $suppr_ajout == 'renommer'){
            $db_connection_envoie = pg_connect("host=$ip port=5432 dbname=projet_gps user=edit password=edit2000");
            $sql_envoie = pg_query($db_connection_envoie, "UPDATE capteur SET nom = '$id_compte_ajout_suppr' WHERE id_capteur = '$id_cap'");
            alert("le capteur à été renommé en $id_compte_ajout_suppr");
        }
        elseif ($id_cap and $id_cap == "nouveau" and $id_compte_ajout_suppr and $suppr_ajout == 'renommer'){
            $id_compte = $_SESSION['id'];
            $sql_id = pg_query($db_connection, "SELECT id_capteur FROM capteur ORDER BY id_capteur");
            while ($row = pg_fetch_row($sql_id)) {
                $id_capteur = $row[0];
            }
            $id_capteur +=1;
            $sql_id = pg_query($db_connection, "SELECT id_capteur_compte FROM capteur_compte ORDER BY id_capteur_compte");
            while ($row = pg_fetch_row($sql_id)) {
                $id_capteur_compte = $row[0];
            }
            $id_capteur_compte += 1;
            $db_connection_envoie = pg_connect("host=$ip port=5432 dbname=projet_gps user=envoie password=script");
            $sql_envoie = pg_query($db_connection_envoie, "INSERT INTO capteur (id_capteur, nom, proprietaire, actif)VALUES ($id_capteur, '$id_compte_ajout_suppr', $id_compte,'true')");
            $sql_envoie = pg_query($db_connection_envoie, "INSERT INTO capteur_compte (id_capteur_compte, id_compte, id_capteur)VALUES ($id_capteur_compte, $id_compte, $id_capteur)");
            alert("le capteur $id_compte_ajout_suppr à été Crée");
            $id_cap = $id_capteur;

        }
        elseif ($id_cap and $id_compte_ajout_suppr and $suppr_ajout == 'activer_desactiver'){
            if ($id_compte_ajout_suppr == 'activer'){
                $db_connection_envoie = pg_connect("host=$ip port=5432 dbname=projet_gps user=edit password=edit2000");
                $sql_envoie = pg_query($db_connection_envoie, "UPDATE capteur SET actif = 'true' WHERE id_capteur = '$id_cap'");
                alert("le capteur à été activé");
            }
            else{
                $db_connection_envoie = pg_connect("host=$ip port=5432 dbname=projet_gps user=edit password=edit2000");
                $sql_envoie = pg_query($db_connection_envoie, "UPDATE capteur SET actif = 'false' WHERE id_capteur = '$id_cap'");
                alert("le capteur à été desactivé");
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
                document.getElementById("mon_compte").style.display="block";
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
        function ajout(){
            document.getElementById("suppr/ajout").value='ajout';
            document.forms["info"].submit();
        }
        function suppr($id_suppr){
            document.getElementById("suppr/ajout").value='suppr';
            document.getElementById("id_suppr_accees").value=$id_suppr;
            document.forms["info"].submit();
        }
        function renommer(){
            document.getElementById("suppr/ajout").value='renommer';
            document.forms["info"].submit();
        }
        function desactiver(){
            document.getElementById("suppr/ajout").value='activer_desactiver';
            if (document.getElementById("actif_btn").value == "Activer le capteur"){
                document.getElementById("id_suppr_accees").value="activer";
            }else{
                document.getElementById("id_suppr_accees").value="desactiver";
            }
            document.forms["info"].submit();
        }
        function envoie_cap() {
            document.forms["info"].submit();
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
                    <li class="sous_menus" id="mon_compte"><p><a href="page_compte.php">Mon compte</a></p></li>
                    <li class="sous_menus" id="visualiser">
                        <p><a href="page.php">Visualiser</a></p>
                    </li>
                    <li class="sous_menus" id="modifier">
                        <p>Ajouter</p>
                        <ul class="element_modifier">
                            <li><a href="#">Zones</a></li>
                            <li><a href="page_ajout.php" id="btn_adj_donnees">Données</a></li>
                        </ul>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
    <div class="corp">
        <form method="post" id="info">
            <label for="capteur">Choisissez un capteur :</label>
            <select id="capteur" name="capteur" onchange="envoie_cap()">
                <?php
                if ($_SESSION['droit'] == "admin"){
                    $sql_cap = pg_query($db_connection, "SELECT id_capteur,nom,actif,nom_d_utilisateur FROM capteur INNER JOIN compte ON capteur.proprietaire = Compte.id_compte ORDER BY id_capteur");
                }
                else{
                    $id_compte = $_SESSION['id'];
                    $sql_cap = pg_query($db_connection, "SELECT id_capteur,nom,actif FROM capteur WHERE proprietaire = $id_compte");
                    echo('<option value="nouveau">Nouveau capteur</option>)');
                }
                $nb_boucle = 0;
                while ($row = pg_fetch_row($sql_cap)) {
                    if ($row[0] == $id_cap){
                        if($id_cap != "nouveau"){
                            $actif = $row[2];
                        }
                        echo '<option selected value="';
                    }
                    else{
                        echo '<option value="';
                    }
                    echo $row[0];
                    echo '">';
                    echo $row[1];
                    if ($_SESSION['droit'] == "admin"){
                        echo(' (');
                        echo($row[3]);
                        echo(')');
                    }
                    echo '</option>';
                    $nb_boucle +=1;
                }
                if ($nb_boucle ==0){
                    $id_cap = "nouveau";
                }
                ?>
            </select>
            <div id='renommer'>
                <?php
                    echo('<input type="text" name="nouveau_nom" id="nouveau_nom" placeholder="');
                    if($id_cap == "nouveau"){
                        echo('Nom du capteur');
                    }else{
                        echo('Nouveau nom');
                    }
                    echo('">');
                    echo('<input type="button" value="');
                    if($id_cap == "nouveau"){
                        echo('Ajouter');
                    }else{
                        echo('Renommer');
                    }
                    echo('" id="btn_renommer" class="bouton" onclick="renommer()">');
                ?>
                
            </div>
            <?php
                if($id_cap != "nouveau"){
                    echo '<input type="button" value="';
                    if ($actif == 't'){
                        echo 'Désactiver le capteur';
                    }else{
                        echo 'Activer le capteur';
                    }
                    echo '" id="actif_btn" class="bouton" onclick="desactiver()">';
                }
            ?>
            <table id="non_nouveau">
                <thead>
                    <tr>
                        <th colspan="2"
                        <?php
                        if($id_cap != "nouveau"){
                            if ($actif == 'f'){
                                echo(' style="color:#aaa" ');
                            }
                        }
                        ?>
                        >Accées</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        if($id_cap != "nouveau"){
                            $id = $_SESSION['id'];
                            $text_sql_ajout = "SELECT id_compte,nom_d_utilisateur FROM compte 
                            WHERE droit != 'admin' AND id_compte !=$id ";
                            
                            $sql = pg_query($db_connection, "SELECT id_compte,nom_d_utilisateur,proprietaire FROM capteur_compte 
                            INNER JOIN capteur USING(id_capteur) 
                            INNER JOIN compte USING(id_compte) 
                            WHERE Id_capteur = '$id_cap' and id_compte != proprietaire
                            ");
                            while ($row = pg_fetch_row($sql)) {
                                $id_compte_acces = $row[0];
                                echo('<tr>');
                                echo('<th');
                                if ($actif == 'f'){
                                    echo(' style="color:#aaa" ');
                                }
                                echo ('>');
                                echo($row[1]);
                                echo('</th>');
                                echo('<td><input type="button" value="supprimer"  class="bouton suppr" onclick="suppr(');
                                echo($id_compte_acces);
                                echo(')"');
                                if ($actif == 'f'){
                                    echo(' disabled ');
                                }
                                echo('></td>');
                                echo('</tr>');
                                $text_sql_ajout .= " and id_compte !='$id_compte_acces' and id_compte !=".$row[2];
                            }
                            echo'<tr class="zone_adj_acces">';
                            echo'<td><select name="new_compte_acces" id="new_compte_acces"';
                            if ($actif == 'f'){
                                echo(' disabled ');
                            }
                            echo'>';
                            $sql_ajout = pg_query($db_connection, $text_sql_ajout);
                            $nb_boucle_select =0;
                            while ($row = pg_fetch_row($sql_ajout)) {
                                echo '<option value="';
                                echo $row[0];
                                echo '">';
                                echo $row[1];
                                echo '</option>';
                                $nb_boucle_select +=1;
                            }
                            echo'</select></td>';
                            if ($nb_boucle_select == 0){
                                echo('<style> .zone_adj_acces{ display: none;}</style>');
                            }
                            echo('<td><input type="button" value="ajouter"  class="bouton" onclick="ajout()"');
                            if ($actif == 'f'){
                                echo(' disabled ');
                            }
                            echo ('></td>');
                            echo'</tr>';
                        }
                    ?>
                </tbody>
            </table>
            <input type="hidden" name="id_suppr_accees" id="id_suppr_accees" value="">
            <input type="hidden" name="suppr/ajout" id="suppr/ajout" value="">
        </form>
        <?php
            if($id_cap == "nouveau"){
                echo('<script>document.getElementById("non_nouveau").style.display="none";</script>');
            }
        ?>
        </style>
    </script>
    </div>
</body>
</html>

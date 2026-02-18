<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../CSS/style.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
    crossorigin=""/>
</head>
<body >
    <script src="../JS/script_page.js"></script>
    <?php
        include ( "variable.php");
        include( "demare_session.php");
        if (!empty($_POST['zone'])){
            $id_zone = $_POST['zone'];
        }
        else{
            $id_zone = "1";
        }
        

        $db_connection = pg_connect("host=$ip port=5432 dbname=projet_gps user=utilisateur password=utilisateur");
        if (!$db_connection) {
            echo "An error occurred.\n";
        exit;
        }
        
        if ($_SESSION['droit'] == "ajouter"){
            header('Location: '."page_ajout.php");;
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
                ?>
            }
        }
        function affiche_donnees() {
            if((document.getElementById("liste_donnees"))){
                if (document.getElementById("liste_donnees").style.display=="block"){
                    document.getElementById("liste_donnees").style.display="none";
                    document.getElementById("btn_plus_moins").value="▼ Voir plus ▼";
                }
                else{
                    document.getElementById("liste_donnees").style.display="block";
                    document.getElementById("btn_plus_moins").value="▲ Voir moins ▲"
                }  
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
                    <li class="sous_menus" id="mon_compte"><p><a href="page_compte.php">Mon compte</a></p></li>
                    <li class="sous_menus" id="modifier">
                        <p>Ajouter/Modifier</p>
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
            <script>
                function envoie_zone() {
                    document.forms["info"].submit();
                }
            </script>
            <label for="zone">Choisissez une zone :</label>
            <select id="zone" name="zone" onchange="envoie_zone()">
                <?php
                $sql_cap = pg_query($db_connection, "SELECT * FROM zones ORDER BY id_zone");
                
                $iteration =0;
                while ($row = pg_fetch_row($sql_cap)) {
                    if ($row[0] == $id_zone){
                        echo '<option selected value="';
                    }
                    else{
                        echo '<option value="';
                    }
                    echo $row[0];
                    echo '">';
                    echo $row[1];
                    echo '</option>';
                    $iteration +=1;
                }
                ?>
            </select>
            
            <input type="submit" value="Réinitialiser">
        </form>
        <?php
        
        $sql = pg_query($db_connection, "SELECT * FROM zones WHERE id_zone = '$id_zone' ORDER BY id_zone");
        
        $premier = 0;
        while ($row = pg_fetch_row($sql)) {
            $x = $row[3];
            $y = $row[2];
            echo ("<script>
                var id_z =".$row[0].";
                var lat_cookie =".$row[2].";
                var lng_cookie =".$row[3].";
                var radius_cookie =".$row[4].";
            </script>");
            
            echo '<div class="donnees"><p class="coordonnees" id="x">x = ' . htmlspecialchars($row[2]) . '</p><p class="coordonnees" id="y">y = ' . htmlspecialchars($row[3]) . '</p><p class="coordonnees" id="r">Radius = ' . htmlspecialchars($row[4]) . '</p></div>';
            
        }
        
        ?>
        </style>
        <div id="map"></div>
        <script
            src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"
            integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA=="
            crossorigin=""
        ></script>
    <script src="../JS/script_maps.js"></script>
    
    <script src="test/script_maps.js"></script>
    </div>
</body>
</html>

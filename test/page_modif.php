<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <script src="script_page.js"></script>
    <?php
        if (!empty($_POST['capteur'])){
            $id_cap = $_POST['capteur'];
        }
        else{
            $id_cap = "1";
        }
        $db_connection = pg_connect("host=10.59.164.226 port=5432 dbname=projet_gps user=aadmin password=admin");
        if (!$db_connection) {
            echo "An error occurred.\n";
        exit;
        }
    ?>
    <div id="utilisateur">
        <?php 
            echo '<label for="deco">';
            echo 'aadmin';
            echo ' :</label>';
        ?>
        <input type="button" id="deco" value="déconnexion" onclick="deco()">
    </div>
    <div class="corp">
        <form method="post" id="info">
            <script>
                function envoie_cap() {
                    document.forms["info"].submit();
                }
                function envoie() {
                    document.forms["info"].submit();
                }
            </script>
            <label for="capteur">Choisissez un capteur :</label>
            <select id="capteur" name="capteur" onchange="envoie_cap()">
                <?php
                $sql_cap = pg_query($db_connection, "SELECT * FROM capteur");
                while ($row = pg_fetch_row($sql_cap)) {
                    if ($row[0] == $id_cap){
                        echo '<option selected value="';
                    }
                    else{
                        echo '<option value="';
                    }
                    echo $row[0];
                    echo '">';
                    echo $row[1];
                    echo '</option>';
                }
                ?>
            </select>
            <input type="submit" value="Envoyer">
        </form>
        <style>
            #img-2{
                top : calc(0px - 36px);
                left : calc(0px - 8px);;
            }
        </style>
        <div id="plans">
        <img id="img-1" src="Carte_France_geo_dep.png" >
        <img id="img-2" src="position.png" >
        </div>
    </div>
</body>
</html>
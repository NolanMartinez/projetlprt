<?php
header('Content-Type: text/html; charset=utf-8');

if (!empty($_COOKIE['id'])){
    $id = $_COOKIE['id'];
}
else{
    $id = null;
}

if (!empty($_COOKIE['mdp'])){
    $mdp = $_COOKIE['mdp'];
}
else{
    $mdp = null;
}

$db_connection = pg_connect("host=10.247.80.226 port=5432 dbname=projet_gps user=$id password=$mdp");
if (!$db_connection) {
    echo "Erreur de connexion à la BD<br>";
    exit;
}

// Récupérer la structure de la table
$sql = "SELECT column_name, data_type, is_nullable, column_default FROM information_schema.columns WHERE table_name='zones' ORDER BY ordinal_position";
$result = pg_query($db_connection, $sql);

echo "<h2>Structure de la table zones:</h2>";
echo "<table border='1'>";
echo "<tr><th>Colonne</th><th>Type</th><th>Nullable</th><th>Défaut</th></tr>";
while ($row = pg_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td>" . $row['column_name'] . "</td>";
    echo "<td>" . $row['data_type'] . "</td>";
    echo "<td>" . $row['is_nullable'] . "</td>";
    echo "<td>" . $row['column_default'] . "</td>";
    echo "</tr>";
}
echo "</table>";

// Afficher les séquences
$sql2 = "SELECT * FROM information_schema.sequences WHERE sequence_name LIKE '%zone%'";
$result2 = pg_query($db_connection, $sql2);

echo "<h2>Séquences:</h2>";
if (pg_num_rows($result2) > 0) {
    echo "<pre>";
    while ($row = pg_fetch_assoc($result2)) {
        print_r($row);
    }
    echo "</pre>";
} else {
    echo "Pas de séquence trouvée pour zones";
}

// Afficher les données existantes
$sql3 = "SELECT * FROM zones LIMIT 5";
$result3 = pg_query($db_connection, $sql3);

echo "<h2>Données existantes:</h2>";
echo "<table border='1'>";
while ($row = pg_fetch_assoc($result3)) {
    echo "<tr>";
    foreach ($row as $val) {
        echo "<td>" . htmlspecialchars($val) . "</td>";
    }
    echo "</tr>";
}
echo "</table>";

pg_close($db_connection);
?>

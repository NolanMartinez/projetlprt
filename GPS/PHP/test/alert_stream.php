<?php
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');

require_once __DIR__ . '/config.php';

$id = !empty($_COOKIE['id']) ? $_COOKIE['id'] : null;
$mdp = !empty($_COOKIE['mdp']) ? $_COOKIE['mdp'] : null;

if (!$id || !$mdp) {
    echo "data: " . json_encode(["error" => "non authentifié"]) . "\n\n";
    flush();
    exit;
}

$db_connection = db_connect_with($id, $mdp);

if (!$db_connection) {
    echo "data: " . json_encode(["error" => "connexion échouée"]) . "\n\n";
    flush();
    exit;
}

$id_cap = $_GET['capteur'] ?? 1;

while (ob_get_level() > 0) {
    ob_end_flush();
}
ob_implicit_flush(true);

while (true) {

    $sql = pg_query($db_connection, "
        SELECT * FROM donnees
        WHERE Id_capteur = '$id_cap'
        ORDER BY Id_donnees DESC
        LIMIT 1
    ");

    if ($row = pg_fetch_row($sql)) {

        $lat = $row[3];
        $lng = $row[2];

        $horsZone = false;

        if ($lat < 45 || $lat > 47 || $lng < 0 || $lng > 3) {
            $horsZone = true;
        }

        echo "data: " . json_encode([
            "alert" => $horsZone,
            "lat" => $lat,
            "lng" => $lng
        ]) . "\n\n";

        flush();
    }

    sleep(2);
}

<?php
header('Content-Type: application/json');

//require_once __DIR__ . '/config.php';
include("variable.php");



$db_connection = pg_connect("host=$ip port=5432 dbname=projet_gps user=utilisateur password=utilisateur");
if (!$db_connection) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

$sql_check = "SELECT column_name FROM information_schema.columns WHERE table_name='zones'";
$result_check = pg_query($db_connection, $sql_check);
$columns = [];
while ($row = pg_fetch_assoc($result_check)) {
    $columns[] = $row['column_name'];
}

$sql_seq_check = "SELECT EXISTS (SELECT 1 FROM information_schema.sequences WHERE sequence_name = 'zones_id_zone_seq')";
$seq_result = pg_query($db_connection, $sql_seq_check);
$seq_exists = pg_fetch_row($seq_result)[0];

if (!$seq_exists) {
    pg_query($db_connection, "CREATE SEQUENCE zones_id_zone_seq");
    pg_query($db_connection, "ALTER TABLE zones ALTER COLUMN id_zone SET DEFAULT nextval('zones_id_zone_seq')");
    pg_query($db_connection, "SELECT setval('zones_id_zone_seq', COALESCE((SELECT MAX(id_zone) FROM zones), 0) + 1)");
}

if (!in_array('latitude', $columns)) {
    pg_query($db_connection, "ALTER TABLE zones ADD COLUMN latitude FLOAT");
}
if (!in_array('longitude', $columns)) {
    pg_query($db_connection, "ALTER TABLE zones ADD COLUMN longitude FLOAT");
}
if (!in_array('radius', $columns)) {
    pg_query($db_connection, "ALTER TABLE zones ADD COLUMN radius FLOAT");
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $sql= null;
    if (!empty($_GET['id'])){
        $id_z = $_GET['id'];
        if($id_z !="undefined"){
            $sql = "SELECT id_zone, nom_zone, latitude, longitude, radius FROM zones WHERE id_zone = $id_z";
        }
    }
    else{
        $sql = "SELECT id_zone, nom_zone, latitude, longitude, radius FROM zones ORDER BY id_zone";
    }
    if(!$sql){
        http_response_code(500);
        echo json_encode(['error' => 'Query failed']);
        exit;
    }
    $result = pg_query($db_connection, $sql);
    if (!$result) {
        http_response_code(500);
        echo json_encode(['error' => 'Query failed']);
        exit;
    }
    
    $zones = [];
    while ($row = pg_fetch_assoc($result)) {
        $zones[] = $row;
    }
    
    echo json_encode($zones);
}

elseif ($method === 'POST') {
    $db_connection = pg_connect("host=$ip port=5432 dbname=projet_gps user=edit password=edit2000");
    if (!$db_connection) {
        http_response_code(500);
        echo json_encode(['error' => 'Database connection failed']);
        exit;
    }
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['latitude']) || !isset($data['longitude']) || !isset($data['radius'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required fields']);
        exit;
    }
    
    $nom = $data['nom'] ?? null;
    $latitude = $data['latitude'];
    $longitude = $data['longitude'];
    $radius = $data['radius'];
    
    $id_zone = isset($data['id_zone']) ? intval($data['id_zone']) : 0;

    if($id_zone > 0 && $nom !=null){
        $sql = "UPDATE zones SET nom_zone = $1 WHERE id_zone = $2";
        $result = pg_query_params($db_connection, $sql, [$nom, $id_zone]);
        if (!$result) {
            http_response_code(500);
            echo json_encode(['error' => 'Query failed']);
            exit;
        }
        echo json_encode(['success' => true, 'id_zone' => $id_zone]);
        exit;
    }
    if ($id_zone > 0) {
        $sql = "UPDATE zones SET latitude = $1, longitude = $2, radius = $3 WHERE id_zone = $4";
        $result = pg_query_params($db_connection, $sql, [$latitude, $longitude, $radius, $id_zone]);
        if (!$result) {
            http_response_code(500);
            echo json_encode(['error' => 'Query failed']);
            exit;
        }
        echo json_encode(['success' => true, 'id_zone' => $id_zone]);
    } else {
        $db_connection = pg_connect("host=$ip port=5432 dbname=projet_gps user=envoie password=script");
        $seq_result = pg_query($db_connection, "SELECT nextval('zones_id_zone_seq')");
        if (!$seq_result) {
            pg_query($db_connection, "CREATE SEQUENCE IF NOT EXISTS zones_id_zone_seq");
            $seq_result = pg_query($db_connection, "SELECT nextval('zones_id_zone_seq')");
        }
        $seq_row = pg_fetch_row($seq_result);
        $next_id = $seq_row[0];
        
        $sql = "INSERT INTO zones (id_zone, nom_zone, latitude, longitude, radius) VALUES ($1, $2, $3, $4, $5) RETURNING id_zone";
        $result = pg_query_params($db_connection, $sql, [$next_id, $nom, $latitude, $longitude, $radius]);
        if (!$result) {
            http_response_code(500);
            echo json_encode(['error' => 'Query failed']);
            exit;
        }
        $row = pg_fetch_assoc($result);
        echo json_encode(['success' => true, 'id_zone' => $row['id_zone']]);
    }
}

/*elseif ($method === 'DELETE') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['id_zone'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing id_zone']);
        exit;
    }
    
    $sql = "DELETE FROM zones WHERE id_zone = $1";
    $result = pg_query_params($db_connection, $sql, [$data['id_zone']]);
    
    if (!$result) {
        http_response_code(500);
        echo json_encode(['error' => 'Query failed']);
        exit;
    }
    
    echo json_encode(['success' => true]);
}*/

else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}

pg_close($db_connection);
?>

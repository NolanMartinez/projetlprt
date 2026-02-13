<?php
include('../variable.php');
$DB_HOST = $ip;
$DB_PORT = '5432';
$DB_NAME = 'projet_gps';

function db_connect_with($user, $pass) {
    global $DB_HOST, $DB_PORT, $DB_NAME;
    $conn_str = "host={$DB_HOST} port={$DB_PORT} dbname={$DB_NAME} user={$user} password={$pass}";
    return @pg_connect($conn_str);
}

?>
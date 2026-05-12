<?php
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '030615aa';
$DB_NAME = 'cisc3003_scenario_c';

$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($mysqli->connect_errno) {
    http_response_code(500);
    exit('Database connection failed.');
}
$mysqli->set_charset('utf8mb4');

<?php
// db.php - include this to get $conn (mysqli)
require_once __DIR__ . '/create_db.php';
$host = 'localhost';
$user = 'root';
$pass = ''; // change if needed
$dbname = 'test';

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die('DB connection error: ' . $conn->connect_error);
}
$conn->set_charset('utf8mb4');

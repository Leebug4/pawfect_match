<?php
$host = 'localhost';
$user = 'root';
$pass = '';     
$dbname = 'test';

// connect to MySQL server
$mysqli = new mysqli($host, $user, $pass);
if ($mysqli->connect_error) {
    die('MySQL connection error: ' . $mysqli->connect_error);
}

// create database if not exists
if (! $mysqli->query("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci")) {
    die('Failed to create database: ' . $mysqli->error);
}

$mysqli->select_db($dbname);

// ------------------
// Create pets table
// ------------------
$createPets = <<<SQL
CREATE TABLE IF NOT EXISTS pets (
  id VARCHAR(20) PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  species VARCHAR(50) NOT NULL,
  breed VARCHAR(100) DEFAULT '',
  gender ENUM('Male','Female','Unknown') DEFAULT 'Unknown',
  age FLOAT DEFAULT 0,
  status ENUM('Available','Adopted') DEFAULT 'Available',
  description TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
SQL;

if (! $mysqli->query($createPets)) {
    die('Failed to create pets table: ' . $mysqli->error);
}

// ------------------
// Create pet_images table
// ------------------
$createImages = <<<SQL
CREATE TABLE IF NOT EXISTS pet_images (
  id INT AUTO_INCREMENT PRIMARY KEY,
  pet_id VARCHAR(20) NOT NULL,
  filename VARCHAR(255) NOT NULL,
  uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_pet_images_pet FOREIGN KEY (pet_id) REFERENCES pets(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
SQL;

if (! $mysqli->query($createImages)) {
    die('Failed to create pet_images table: ' . $mysqli->error);
}

$mysqli->close();
return;
?>

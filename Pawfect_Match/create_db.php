<?php
// create_db.php - creates pet_adoption_db, pets, pet_images if missing
$host = 'localhost';
$user = 'root';
$pass = ''; // change if needed
$dbname = 'pet_adoption_db';

$mysqli = new mysqli($host, $user, $pass);
if ($mysqli->connect_error) {
    die('MySQL connect error: ' . $mysqli->connect_error);
}

$mysqli->query("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$mysqli->select_db($dbname);

// pets table
$createPets = "
CREATE TABLE IF NOT EXISTS pets (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  type VARCHAR(50) NOT NULL,
  age FLOAT DEFAULT 0,
  status ENUM('Available','Adopted') DEFAULT 'Available',
  description TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";
$mysqli->query($createPets);

// pet_images table
$createImages = "
CREATE TABLE IF NOT EXISTS pet_images (
  id INT AUTO_INCREMENT PRIMARY KEY,
  pet_id INT NOT NULL,
  filename VARCHAR(255) NOT NULL,
  uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (pet_id) REFERENCES pets(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";
$mysqli->query($createImages);

$mysqli->close();

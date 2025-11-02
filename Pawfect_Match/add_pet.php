<?php
session_start();
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);

if (empty($_SESSION['is_admin'])) {
    header('Location: login.php');
    exit;
}

require_once 'db.php';

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id          = trim($_POST['id']);
    $name        = trim($_POST['name']);
    $species     = trim($_POST['species']);
    $breed       = trim($_POST['breed']);
    $gender      = trim($_POST['gender']);
    $age         = trim($_POST['age']);
    $status      = trim($_POST['status']);
    $description = trim($_POST['description']);

    if ($id === '' || $name === '' || $species === '') {
        $msg = '❌ Please fill in all required fields (ID, Name, Species).';
    } else {
        // Check if pet ID already exists
        $check = $conn->prepare("SELECT id FROM pets WHERE id = ?");
        $check->bind_param("s", $id);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $msg = "⚠️ A pet with ID $id already exists.";
        } else {
            // Insert new pet (no image)
            $stmt = $conn->prepare("INSERT INTO pets (id, name, species, breed, gender, age, status, description)
                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssdss", $id, $name, $species, $breed, $gender, $age, $status, $description);

            if ($stmt->execute()) {
                $msg = '✅ Pet info added successfully!';
            } else {
                $msg = '❌ Database error: ' . $stmt->error;
            }
        }
        $check->close();
    }
}
?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Add Pet</title>
</head>
<body>
<h2>Add a New Pet (Info Only)</h2>

<?php if ($msg): ?>
<p><strong><?php echo htmlspecialchars($msg); ?></strong></p>
<?php endif; ?>

<form method="post">
  <label>ID (e.g. C167-31-4401):</label><br>
  <input type="text" name="id" required><br><br>

  <label>Name:</label><br>
  <input type="text" name="name" required><br><br>

  <label>Species:</label><br>
  <input type="text" name="species" required><br><br>

  <label>Breed:</label><br>
  <input type="text" name="breed"><br><br>

  <label>Gender:</label><br>
  <select name="gender" required>
    <option value="Male">Male</option>
    <option value="Female">Female</option>
    <option value="Unknown">Unknown</option>
  </select><br><br>

  <label>Age:</label><br>
  <input type="number" name="age" step="0.1"><br><br>

  <label>Status:</label><br>
  <select name="status" required>
    <option value="Available">Available</option>
    <option value="Adopted">Adopted</option>
  </select><br><br>

  <label>Description:</label><br>
  <textarea name="description" rows="4" cols="40"></textarea><br><br>

  <button type="submit">Save Pet</button>
</form>

<a href="admin.php"><button>Back to Admin Panel</button></a>

</body>
</html>

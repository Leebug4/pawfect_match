<?php
session_start();
if (empty($_SESSION['is_admin'])) { header('Location: login.php'); exit; }
require_once 'db.php';

// fetch types for select
$types = [];
$q = $conn->query("SELECT DISTINCT type FROM pets WHERE type <> '' ORDER BY type ASC");
if ($q) while ($r = $q->fetch_assoc()) $types[] = $r['type'];
if (empty($types)) $types = ['Dog','Cat','Fish','Bird','Other'];

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $selected_type = trim($_POST['type_select'] ?? '');
    $new_type = trim($_POST['type_new'] ?? '');
    $type = ($new_type !== '') ? $new_type : $selected_type;
    $age = floatval($_POST['age'] ?? 0);
    $status = (isset($_POST['status']) && $_POST['status'] === 'Adopted') ? 'Adopted' : 'Available';
    $description = trim($_POST['description'] ?? '');

    if ($name === '') $errors[] = 'Name is required.';
    if ($type === '') $errors[] = 'Type is required.';

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO pets (name, type, age, status, description) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('ssdss', $name, $type, $age, $status, $description);
        if ($stmt->execute()) {
            $_SESSION['msg'] = 'Pet added. You can upload images now.';
            header('Location: admin.php');
            exit;
        } else {
            $errors[] = 'DB insert error: ' . $stmt->error;
        }
    }
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Add Pet</title></head>
<body>
<h2>Add New Pet</h2>
<?php if ($errors) foreach ($errors as $e) echo "<p style='color:red'>".htmlspecialchars($e)."</p>"; ?>
<form method="post">
  <label>Name:<br><input type="text" name="name" required value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>"></label><br><br>

  <label>Type (choose existing):<br>
    <select name="type_select"><option value="">--Select--</option>
      <?php foreach ($types as $t): ?>
        <option value="<?php echo htmlspecialchars($t); ?>"><?php echo htmlspecialchars($t); ?></option>
      <?php endforeach; ?>
    </select>
  </label><br>
  <small>Or enter new type below:</small><br>
  <label>New Type:<br><input type="text" name="type_new"></label><br><br>

  <label>Age:<br><input type="number" step="0.1" name="age" value="0"></label><br><br>

  <label>Status:<br>
    <select name="status"><option value="Available">Available</option><option value="Adopted">Adopted</option></select>
  </label><br><br>

  <label>Description:<br><textarea name="description" rows="4" cols="40"></textarea></label><br><br>

  <button type="submit">Add Pet</button>
</form>
<p><a href="admin.php">Back to Admin</a></p>
</body>
</html>

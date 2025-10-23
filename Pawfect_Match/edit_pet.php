<?php
session_start();
if (empty($_SESSION['is_admin'])) { header('Location: login.php'); exit; }
require_once 'db.php';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) { header('Location: admin.php'); exit; }

$stmt = $conn->prepare("SELECT * FROM pets WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) { header('Location: admin.php'); exit; }
$pet = $res->fetch_assoc();

$types = [];
$q = $conn->query("SELECT DISTINCT type FROM pets WHERE type <> '' ORDER BY type ASC");
if ($q) while ($r = $q->fetch_assoc()) $types[] = $r['type'];
if (empty($types)) $types = ['Dogs','Cats','Fishs','Birds',' Reptiles','Rabbits'];

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $selected_type = trim($_POST['type_select'] ?? '');
    $new_type = trim($_POST['type_new'] ?? '');
    $type = ($new_type !== '') ? $new_type : $selected_type;
    $age = floatval($_POST['age'] ?? 0);
    $status = (isset($_POST['status']) && $_POST['status'] === 'Adopted') ? 'Adopted' : 'Available';
    $description = trim($_POST['description'] ?? '');

    if ($name === '') $errors[] = 'Name required.';
    if ($type === '') $errors[] = 'Type required.';

    if (empty($errors)) {
        $u = $conn->prepare("UPDATE pets SET name=?, type=?, age=?, status=?, description=? WHERE id=?");
        $u->bind_param('ssdssi', $name, $type, $age, $status, $description, $id);
        if ($u->execute()) {
            $_SESSION['msg'] = 'Pet updated.';
            header('Location: admin.php');
            exit;
        } else {
            $errors[] = 'DB update error: ' . $u->error;
        }
    }
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Edit Pet</title></head>
<body>
<h2>Edit Pet #<?php echo $pet['id']; ?></h2>
<?php if ($errors) foreach ($errors as $e) echo "<p style='color:red'>".htmlspecialchars($e)."</p>"; ?>
<form method="post">
  <label>Name:<br><input type="text" name="name" required value="<?php echo htmlspecialchars($pet['name']); ?>"></label><br><br>

  <label>Type (choose existing):<br>
    <select name="type_select"><option value="">--Select--</option>
      <?php foreach ($types as $t): ?>
        <option value="<?php echo htmlspecialchars($t); ?>" <?php if($pet['type']===$t) echo 'selected'; ?>><?php echo htmlspecialchars($t); ?></option>
      <?php endforeach; ?>
    </select>
  </label><br>
  <small>Or enter new type below (will be used if provided):</small><br>
  <label>New Type:<br><input type="text" name="type_new"></label><br><br>

  <label>Age:<br><input type="number" step="0.1" name="age" value="<?php echo htmlspecialchars($pet['age']); ?>"></label><br><br>

  <label>Status:<br>
    <select name="status">
      <option value="Available" <?php if($pet['status']=='Available') echo 'selected'; ?>>Available</option>
      <option value="Adopted" <?php if($pet['status']=='Adopted') echo 'selected'; ?>>Adopted</option>
    </select>
  </label><br><br>

  <label>Description:<br><textarea name="description" rows="4" cols="40"><?php echo htmlspecialchars($pet['description']); ?></textarea></label><br><br>

  <button type="submit">Save Changes</button>
</form>
<p><a href="admin.php">Back to Admin</a></p>
</body>
</html>

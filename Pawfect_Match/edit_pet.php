<?php
session_start();
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);

if (empty($_SESSION['is_admin'])) { header('Location: login.php'); exit; }
require_once 'db.php';

function esc($s){ return htmlspecialchars($s, ENT_QUOTES); }

$id = isset($_GET['id']) ? trim($_GET['id']) : '';
if ($id === '') { header('Location: admin.php'); exit; }

// fetch pet by string id
$stmt = $conn->prepare("SELECT * FROM pets WHERE id = ?");
$stmt->bind_param('s', $id);
$stmt->execute();
$res = $stmt->get_result();
if (!$res || $res->num_rows === 0) { $_SESSION['msg'] = 'Pet not found.'; header('Location: admin.php'); exit; }
$pet = $res->fetch_assoc();
$stmt->close();

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $species = trim($_POST['species'] ?? '');
    $breed = trim($_POST['breed'] ?? '');
    $gender = in_array($_POST['gender'] ?? 'Unknown', ['Male','Female','Unknown']) ? $_POST['gender'] : 'Unknown';
    $age = isset($_POST['age']) ? floatval($_POST['age']) : 0;
    $status = (isset($_POST['status']) && $_POST['status'] === 'Adopted') ? 'Adopted' : 'Available';
    $description = trim($_POST['description'] ?? '');

    if ($name === '') $errors[] = 'Name is required.';
    if ($species === '') $errors[] = 'Species is required.';

    if (empty($errors)) {
        $u = $conn->prepare("UPDATE pets SET name=?, species=?, breed=?, gender=?, age=?, status=?, description=? WHERE id = ?");
        if (!$u) { $errors[] = 'Prepare failed: ' . $conn->error; }
        else {
            $u->bind_param('ssssdsss', $name, $species, $breed, $gender, $age, $status, $description, $id);
            if ($u->execute()) {
                $success = 'Pet updated.';
                $stmt2 = $conn->prepare("SELECT * FROM pets WHERE id = ?");
                $stmt2->bind_param('s', $id);
                $stmt2->execute();
                $pet = $stmt2->get_result()->fetch_assoc();
                $stmt2->close();
            } else {
                $errors[] = 'Update failed: ' . $u->error;
            }
            $u->close();
        }
    }
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Edit Pet <?php echo esc($id); ?></title></head>
<body>
<h2>Edit Pet <?php echo esc($id); ?></h2>
<?php if ($success) echo '<p style="color:green">'.esc($success).'</p>'; ?>
<?php if (!empty($errors)) foreach ($errors as $e) echo '<p style="color:red">'.esc($e).'</p>'; ?>

<form method="post" action="edit_pet.php?id=<?php echo urlencode($id); ?>">
    <label>ID (manual):<br><input type="text" value="<?php echo esc($id); ?>" readonly></label><br><br>

    <label>Name:<br><input type="text" name="name" required value="<?php echo esc($pet['name']); ?>"></label><br><br>
    <label>Species:<br><input type="text" name="species" required value="<?php echo esc($pet['species']); ?>"></label><br><br>
    <label>Breed:<br><input type="text" name="breed" value="<?php echo esc($pet['breed']); ?>"></label><br><br>
    <label>Gender:<br>
      <select name="gender">
        <option value="Unknown" <?php if($pet['gender']=='Unknown') echo 'selected'; ?>>Unknown</option>
        <option value="Male" <?php if($pet['gender']=='Male') echo 'selected'; ?>>Male</option>
        <option value="Female" <?php if($pet['gender']=='Female') echo 'selected'; ?>>Female</option>
      </select>
    </label><br><br>
    <label>Age:<br><input type="number" step="0.1" name="age" value="<?php echo esc($pet['age']); ?>"></label><br><br>
    <label>Status:<br>
      <select name="status">
        <option value="Available" <?php if($pet['status']=='Available') echo 'selected'; ?>>Available</option>
        <option value="Adopted" <?php if($pet['status']=='Adopted') echo 'selected'; ?>>Adopted</option>
      </select>
    </label><br><br>
    <label>Description:<br><textarea name="description" rows="4" cols="40"><?php echo esc($pet['description']); ?></textarea></label><br><br>

    <button type="submit">Save Changes</button>
</form>

<a href="admin.php"><button>Back to Admin Panel</button></a>
</body>
</html>

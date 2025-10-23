<?php
session_start();
if (empty($_SESSION['is_admin'])) { header('Location: login.php'); exit; }
require_once 'db.php';

// messages
$msg = '';
if (isset($_SESSION['msg'])) { $msg = $_SESSION['msg']; unset($_SESSION['msg']); }

$res = $conn->query("SELECT * FROM pets ORDER BY created_at DESC");
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Admin - Pawfect Match</title></head>
<body>
<h1>Admin Dashboard</h1>
<p><a href="add_pet.php">+ Add New Pet</a> | <a href="logout.php">Logout</a> | <a href="index.php">Guest view</a></p>
<?php if ($msg) echo '<p style="color:green">'.htmlspecialchars($msg).'</p>'; ?>

<table border="1" cellpadding="6" cellspacing="0">
  <tr><th>ID</th><th>Photos</th><th>Name</th><th>Type</th><th>Age</th><th>Status</th><th>Actions</th></tr>
  <?php if ($res && $res->num_rows > 0): while ($row = $res->fetch_assoc()): ?>
    <tr>
      <td><?php echo $row['id']; ?></td>
      <td style="min-width:200px">
        <?php
        // show all images for this pet
        $imgs = $conn->prepare("SELECT id, filename FROM pet_images WHERE pet_id = ? ORDER BY uploaded_at DESC");
        $imgs->bind_param('i', $row['id']);
        $imgs->execute();
        $imgRes = $imgs->get_result();
        while ($ir = $imgRes->fetch_assoc()) {
            echo '<div style="display:inline-block;margin:4px;text-align:center">';
            echo '<img src="animals/'.rawurlencode($ir['filename']).'" width="80"><br>';
            echo '<form method="post" action="delete_image.php" onsubmit="return confirm(\'Delete image?\')">';
            echo '<input type="hidden" name="image_id" value="'.intval($ir['id']).'">';
            echo '<button type="submit">Delete</button></form>';
            echo '</div>';
        }
        ?>
        <br>
        <!-- upload form -->
        <form method="post" action="upload_image.php" enctype="multipart/form-data" style="margin-top:8px">
          <input type="hidden" name="pet_id" value="<?php echo $row['id']; ?>">
          <input type="file" name="image" accept="image/*" required>
          <button type="submit">Upload</button>
        </form>
      </td>
      <td><?php echo htmlspecialchars($row['name']); ?></td>
      <td><?php echo htmlspecialchars($row['type']); ?></td>
      <td><?php echo htmlspecialchars($row['age']); ?></td>
      <td><?php echo htmlspecialchars($row['status']); ?></td>
      <td>
        <a href="edit_pet.php?id=<?php echo $row['id']; ?>">Edit</a>
        <form method="post" action="delete_pet.php" style="display:inline" onsubmit="return confirm('Delete this pet?')">
          <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
          <button type="submit">Delete</button>
        </form>
      </td>
    </tr>
  <?php endwhile; else: ?>
    <tr><td colspan="7">No pets</td></tr>
  <?php endif; ?>
</table>
</body>
</html>

<?php
require_once 'db.php';
$type = isset($_GET['type']) ? trim($_GET['type']) : '';
if ($type === '') { header('Location: guest.php'); exit; }

// optional message
$msg = isset($_GET['msg']) ? $_GET['msg'] : '';

// fetch pets of this type
$stmt = $conn->prepare("SELECT * FROM pets WHERE type = ? ORDER BY created_at DESC");
$stmt->bind_param('s', $type);
$stmt->execute();
$res = $stmt->get_result();
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title><?php echo htmlspecialchars($type); ?> - Pawfect Match</title></head>
<body>
<h1><?php echo htmlspecialchars($type); ?> for Adoption</h1>
<p><a href="guest.php">Back to categories</a> | <a href="index.php">Home</a></p>

<?php if ($msg) echo '<p style="color:green">'.htmlspecialchars($msg).'</p>'; ?>

<?php if ($res && $res->num_rows > 0): ?>
  <table border="1" cellpadding="6" cellspacing="0">
    <tr><th>ID</th><th>Photo</th><th>Name</th><th>Type</th><th>Age</th><th>Description</th><th>Status</th><th>Action</th></tr>
    <?php while ($row = $res->fetch_assoc()): ?>
      <tr>
        <td><?php echo $row['id']; ?></td>
        <td>
          <?php
          // get first image for this pet
          $imgStmt = $conn->prepare("SELECT filename FROM pet_images WHERE pet_id = ? ORDER BY uploaded_at DESC LIMIT 1");
          $imgStmt->bind_param('i', $row['id']);
          $imgStmt->execute();
          $imgRes = $imgStmt->get_result();
          $imgRow = $imgRes->fetch_assoc();
          if ($imgRow && file_exists(__DIR__ . '/animals/' . $imgRow['filename'])) {
              echo '<img src="animals/'.rawurlencode($imgRow['filename']).'" width="100" alt="">';
          } else {
              echo 'No photo';
          }
          ?>
        </td>
        <td><?php echo htmlspecialchars($row['name']); ?></td>
        <td><?php echo htmlspecialchars($row['type']); ?></td>
        <td><?php echo htmlspecialchars($row['age']); ?> yrs</td>
        <td><?php echo nl2br(htmlspecialchars($row['description'])); ?></td>
        <td><?php echo htmlspecialchars($row['status']); ?></td>
        <td>
          <?php if ($row['status'] === 'Available'): ?>
            <form method="post" action="adopt.php" onsubmit="return confirm('Adopt <?php echo htmlspecialchars(addslashes($row['name'])); ?>?');">
              <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
              <button type="submit">Adopt</button>
            </form>
          <?php else: ?>
            <button disabled>Already Adopted</button>
          <?php endif; ?>
        </td>
      </tr>
    <?php endwhile; ?>
  </table>
<?php else: ?>
  <p>No <?php echo htmlspecialchars($type); ?> available yet.</p>
<?php endif; ?>
</body>
</html>

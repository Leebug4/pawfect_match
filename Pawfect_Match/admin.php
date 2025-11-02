<?php
// admin.php - Admin dashboard for Pawfect Match
session_start();
ini_set('display_errors',1);
error_reporting(E_ALL);

// require DB connection
require_once 'db.php';

// check admin session
if (empty($_SESSION['is_admin'])) {
    header('Location: login.php');
    exit;
}

function esc($s){ return htmlspecialchars($s, ENT_QUOTES); }

// Fetch flash message
$msg = '';
if (isset($_SESSION['msg'])) { $msg = $_SESSION['msg']; unset($_SESSION['msg']); }

// Fetch all pets
$stmt = $conn->prepare("SELECT * FROM pets ORDER BY created_at DESC");
$stmt->execute();
$petsRes = $stmt->get_result();

// helper to get images for a pet
function get_pet_images($conn, $pet_id) {
    $out = [];
    $qi = $conn->prepare("SELECT id, filename FROM pet_images WHERE pet_id = ? ORDER BY uploaded_at DESC");
    $qi->bind_param('s', $pet_id); // <-- FIXED: string not integer
    $qi->execute();
    $r = $qi->get_result();
    while ($row = $r->fetch_assoc()) $out[] = $row;
    $qi->close();
    return $out;
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Admin - Pawfect Match</title>
</head>
<body>
  <h1>Admin Dashboard</h1>
  <p>
    <a href="add_pet.php"><button type="button">+ Add New Pet</button></a>
    <a href="adminprofile.php"><button type="button">Profile</button></a>    
    <a href="logout.php"><button type="button">Logout</button></a>
  </p>

  <?php if ($msg): ?>
    <p style="color:green;"><?php echo esc($msg); ?></p>
  <?php endif; ?>

  <table border="1" cellpadding="6" cellspacing="0">
    <tr>
      <th>ID</th>
      <th>Photos</th>
      <th>Name</th>
      <th>Species</th>
      <th>Breed</th>
      <th>Gender</th>
      <th>Age</th>
      <th>Status</th>
      <th>Description</th>
      <th>Actions</th>
    </tr>
    
    <?php if ($petsRes && $petsRes->num_rows > 0): ?>
      <?php while ($pet = $petsRes->fetch_assoc()): ?>
        <tr>
          <td><?php echo esc($pet['id']); ?></td> <!-- FIXED: no (int) -->
          <td style="min-width:220px; vertical-align:top;">
            <?php
              $images = get_pet_images($conn, $pet['id']);
              if (!empty($images)) {
                  foreach ($images as $img) {
                      $fn = $img['filename'];
                      $path1 = __DIR__ . '/uploads/' . $fn;
                      $path2 = __DIR__ . '/animals/' . $fn;
                      $web = '';
                      if (is_file($path1)) $web = 'uploads/' . rawurlencode($fn);
                      elseif (is_file($path2)) $web = 'animals/' . rawurlencode($fn);
                      echo '<div style="display:inline-block;margin:6px;text-align:center;">';
                      if ($web) {
                          echo '<img src="'.esc($web).'" width="100" alt="">';
                      } else {
                          echo '<div style="width:100px;height:70px;border:1px solid #000;display:flex;align-items:center;justify-content:center;">No file</div>';
                      }
                      echo '<form method="post" action="delete_image.php" onsubmit="return confirm(\'Delete this image?\');" style="margin-top:4px;">';
                      echo '<input type="hidden" name="image_id" value="'.(int)$img['id'].'">';
                      echo '<button type="submit">Delete</button>';
                      echo '</form>';
                      echo '</div>';
                  }
              } else {
                  echo 'No photos';
              }
            ?>
            <!-- upload form -->
            <div style="margin-top:8px;">
              <form method="post" action="upload_image.php" enctype="multipart/form-data">
                <input type="hidden" name="pet_id" value="<?php echo esc($pet['id']); ?>">
                <input type="file" name="image" accept="image/*" required>
                <button type="submit">Upload</button>
              </form>
            </div>
          </td>
          <td><?php echo esc($pet['name']); ?></td>
          <td><?php echo esc($pet['species']); ?></td>
          <td><?php echo esc($pet['breed']); ?></td>
          <td><?php echo esc($pet['gender']); ?></td>
          <td><?php echo esc($pet['age']); ?></td>
          <td><?php echo esc($pet['status']); ?></td>
          <td><?php echo nl2br(esc($pet['description'])); ?>
        </td>
          <td>
            <button type="button" onclick="window.location.href='edit_pet.php?id=<?php echo urlencode($pet['id']); ?>'">Edit</button>
            <form method="post" action="delete_pet.php" style="display:inline"
                  onsubmit="return confirm('Are you sure you want to delete <?php echo esc($pet['name']); ?>?');">
              <input type="hidden" name="id" value="<?php echo esc($pet['id']); ?>">
              <button type="submit">Delete</button>
            </form>
          </td>
        </tr>
      <?php endwhile; ?>
    <?php else: ?>
      <tr><td colspan="10">No pets found.</td></tr>
    <?php endif; ?>
  </table>
</body>
</html>

<?php
// category.php - show pets by species and display image from pet_images (checks animals/ and uploads/)
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);

require_once 'db.php';

// read species from query (backwards-compatible with ?type=... if needed)
$species = '';
if (isset($_GET['species'])) $species = trim($_GET['species']);
elseif (isset($_GET['type'])) $species = trim($_GET['type']);

if ($species === '') {
    header('Location: guest.php');
    exit;
}

$msg = isset($_GET['msg']) ? trim($_GET['msg']) : '';

// helper: escape
function esc($s){ return htmlspecialchars($s, ENT_QUOTES); }

// helper: find web path for filename (returns relative web path or empty string)
function find_image_path($filename) {
    if (empty($filename)) return '';
    $p1 = __DIR__ . '/animals/' . $filename;
    $p2 = __DIR__ . '/uploads/' . $filename;
    if (is_file($p1)) return 'animals/' . rawurlencode($filename);
    if (is_file($p2)) return 'uploads/' . rawurlencode($filename);
    return '';
}

// helper: get first image filename from pet_images table
function get_first_image_filename($conn, $pet_id) {
    $fname = '';
    $q = $conn->prepare("SELECT filename FROM pet_images WHERE pet_id = ? ORDER BY uploaded_at DESC LIMIT 1");
    if (! $q) return '';
    $q->bind_param('s', $pet_id);
    $q->execute();
    $r = $q->get_result();
    if ($r && ($row = $r->fetch_assoc())) $fname = $row['filename'];
    $q->close();
    return $fname;
}

// fetch pets for this species
$stmt = $conn->prepare("SELECT * FROM pets WHERE species = ? ORDER BY created_at DESC");
$stmt->bind_param('s', $species);
$stmt->execute();
$res = $stmt->get_result();
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title><?php echo esc($species); ?> - Pawfect Match</title></head>
<body>
<h1><?php echo esc($species); ?> for Adoption</h1>
<p><a href="guest.php">Back to categories</a></p>

<?php if ($msg) echo '<p style="color:green">'.esc($msg).'</p>'; ?>

<?php if ($res && $res->num_rows > 0): ?>
  <table border="1" cellpadding="6" cellspacing="0">
    <tr>
      <th>ID</th><th>Photo</th><th>Name</th><th>Species</th><th>Breed</th><th>Gender</th><th>Age</th><th>Description</th><th>Status</th><th>Action</th>
    </tr>

    <?php while ($row = $res->fetch_assoc()): ?>
      <tr>
        <td><?php echo esc($row['id']); ?></td>
        <td>
          <?php
            // get first image filename from DB
            $dbFilename = get_first_image_filename($conn, $row['id']);
            $webPath = find_image_path($dbFilename);

            if ($webPath !== '') {
                echo '<img src="'.esc($webPath).'" width="100" alt="">';
            } else {
                // no file found — show helpful debug info so you can fix quickly
                if ($dbFilename !== '') {
                    echo 'No file on disk for DB filename: <br><small>'.esc($dbFilename).'</small>';
                } else {
                    echo 'No image recorded';
                }
            }
          ?>
        </td>

        <td><?php echo esc($row['name']); ?></td>
        <td><?php echo esc($row['species']); ?></td>
        <td><?php echo esc($row['breed']); ?></td>
        <td><?php echo esc($row['gender']); ?></td>
        <td><?php echo esc($row['age']); ?> yrs</td>
        <td><?php echo nl2br(esc($row['description'])); ?></td>
        <td><?php echo esc($row['status']); ?></td>
        <td>
          <?php if ($row['status'] === 'Available'): ?>
            <form method="post" action="adopt.php" onsubmit="return confirm('Adopt <?php echo esc(addslashes($row['name'])); ?>?');">
              <input type="hidden" name="id" value="<?php echo esc($row['id']); ?>">
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
  <p style="color:red;">No <?php echo esc($species); ?> available.</p>
<?php endif; ?>

</body>
</html>

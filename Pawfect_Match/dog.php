<?php
// dog.php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);

require_once 'db.php';
function esc($s){ return htmlspecialchars($s, ENT_QUOTES); }

// POST adopt handling
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['adopt_id'])) {
    $pet_id = trim($_POST['adopt_id']);
    if ($pet_id !== '') {
        $conn->begin_transaction();
        $g = $conn->prepare("SELECT status, name FROM pets WHERE id = ? FOR UPDATE");
        $g->bind_param('s', $pet_id);
        $g->execute();
        $res = $g->get_result();
        if ($res && $res->num_rows > 0) {
            $r = $res->fetch_assoc();
            if ($r['status'] === 'Available') {
                $u = $conn->prepare("UPDATE pets SET status = 'Adopted' WHERE id = ?");
                $u->bind_param('s', $pet_id);
                if ($u->execute()) { $conn->commit(); $msg = "You adopted " . $r['name'] . "!"; }
                else { $conn->rollback(); $msg = "Failed to adopt (DB)."; }
            } else { $conn->rollback(); $msg = "Sorry, this pet has already been adopted."; }
        } else { $conn->rollback(); $msg = "Pet not found."; }
    } else { $msg = "Invalid pet id."; }
    header('Location: dog.php?msg=' . urlencode($msg));
    exit;
}

// optional toast
$shown_msg = isset($_GET['msg']) ? trim($_GET['msg']) : '';

// helper: find web path for filename
function find_image_path($filename) {
    if (empty($filename)) return '';
    $p1 = __DIR__ . '/uploads/' . $filename;
    $p2 = __DIR__ . '/animals/' . $filename;
    if (is_file($p1)) return 'uploads/' . rawurlencode($filename);
    if (is_file($p2)) return 'animals/' . rawurlencode($filename);
    return '';
}

// helper: get first image filename
function get_first_image($conn, $pet_id) {
    $fname = '';
    $q = $conn->prepare("SELECT filename FROM pet_images WHERE pet_id = ? ORDER BY uploaded_at DESC LIMIT 1");
    $q->bind_param('s', $pet_id);
    $q->execute();
    $r = $q->get_result();
    if ($r && $row = $r->fetch_assoc()) $fname = $row['filename'];
    $q->close();
    return $fname;
}

// fetch dogs
$species = 'Dog';
$stmt = $conn->prepare("SELECT * FROM pets WHERE species = ? ORDER BY created_at DESC");
$stmt->bind_param('s', $species);
$stmt->execute();
$result = $stmt->get_result();

// header categories for search
$cats = ['Dog'=>'animals/dog.jpg','Cat'=>'animals/cat.jpg','Hamster'=>'animals/hamster.jpg','Rabbit'=>'animals/rabbit.jpg'];
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Dogs - Pawfect Match</title>
<link rel="stylesheet" href="generalAdoption.css">
<link rel="stylesheet" href="dog.css">
</head>
<body>
<div class="header">
  <div class="logo">
    <img src="logo/LOGO.png" alt="Pawfect Match Logo">
  </div>
  <div class="nav-links">
    <a href="guest.php" class="active">Home</a>
    <a href="adopt.php">Adopt</a>
    <a href="donation.php">Donation</a>
    <a href="aboutUs.php">About Us</a>
    <a href="profile.php">Profile</a>
  </div>
  <form method="get" action="" class="search-bar">
    <input type="text" id="search" name="search" placeholder="Search pets...">
    <button type="submit">🔍</button>
  </form>
</div>


  <hr>
<h1>All Dogs</h1>

<?php if ($shown_msg !== ''): ?>
  <div id="toast" style="position:fixed;top:16px;right:16px;background:#222;color:#fff;padding:10px;border-radius:8px;">
    <?php echo esc($shown_msg); ?> 
    <button onclick="document.getElementById('toast').style.display='none'">OK</button>
  </div>
<?php endif; ?>

<?php if ($result && $result->num_rows > 0): ?>
  <table border="1" cellpadding="6" cellspacing="0">
    <tr>
      <th>ID</th>
      <th>Photo</th>
      <th>Name</th>
      <th>Breed</th>
      <th>Gender</th>
      <th>Age</th>
      <th>Description</th>
      <th>Status</th>
      <th>Action</th>
    </tr>

    <?php while ($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?php echo esc($row['id']); ?></td>
        <td>
          <?php
            $fname = get_first_image($conn, $row['id']);
            $img = find_image_path($fname);
            echo $img ? '<img src="'.esc($img).'" width="100" alt="">' : 'No photo';
          ?>
        </td>
        <td><?php echo esc($row['name']); ?></td>
        <td><?php echo esc($row['breed']); ?></td>
        <td><?php echo esc($row['gender']); ?></td>
        <td><?php echo esc($row['age']); ?> yrs</td>
        <td><?php echo nl2br(esc($row['description'])); ?></td>
        <td><?php echo esc($row['status']); ?></td>
        <td>
          <?php if (strtolower($row['status']) === 'available'): ?>
            <form action="adopted.php" method="GET">
              <input type="hidden" name="id" value="<?php echo esc($row['id']); ?>">
              <input type="hidden" name="name" value="<?php echo esc($row['name']); ?>">
              <input type="hidden" name="age" value="<?php echo esc($row['age']); ?>">
              <input type="hidden" name="breed" value="<?php echo esc($row['breed']); ?>">
              <input type="hidden" name="gender" value="<?php echo esc($row['gender']); ?>">
              <input type="hidden" name="description" value="<?php echo esc($row['description']); ?>">
              <input type="hidden" name="image" value="<?php echo esc($img); ?>">
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
  <p>No dogs found.</p>
<?php endif; ?>

<p><a href="guest.php"><button>Back to Category</button></a></p>
</body> 
</html>
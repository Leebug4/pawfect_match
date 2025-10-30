<?php
// adminprofile.php
session_start();
require_once 'db.php'; 

function esc($s) {
    return htmlspecialchars($s ?? '', ENT_QUOTES);
}

// get current DB name
$dbname = 'test';
$dr = $conn->query("SELECT DATABASE()");
if ($dr) {
    $tmp = $dr->fetch_row();
    $dbname = $tmp[0] ?? '';
}

$hasAdoptedDate = false;
if ($dbname !== '') {
    $checkSql = "SELECT COUNT(*) AS c FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'pets' AND COLUMN_NAME = 'adopted_date'";
    if ($chkStmt = $conn->prepare($checkSql)) {
        $chkStmt->bind_param('s', $dbname);
        $chkStmt->execute();
        $res = $chkStmt->get_result();
        if ($row = $res->fetch_assoc()) {
            $hasAdoptedDate = intval($row['c']) > 0;
        }
        $chkStmt->close();
    }
}

// prepare data depending on presence of adopted_date
$today = date('Y-m-d');
$todayLabel = date('F j, Y');

$total_today = 0;
$adoptedRows = [];
$note = '';

if ($hasAdoptedDate) {
    // Count adopted today and fetch details
    $countSql = "SELECT COUNT(*) AS total_today FROM pets WHERE status = 'Adopted' AND DATE(adopted_date) = ?";
    if ($countStmt = $conn->prepare($countSql)) {
        $countStmt->bind_param('s', $today);
        $countStmt->execute();
        $cres = $countStmt->get_result();
        if ($crow = $cres->fetch_assoc()) {
            $total_today = intval($crow['total_today']);
        }
        $countStmt->close();
    }

    // fetch list of today's adopted pets
    $listSql = "SELECT id, name, species, breed, gender, age FROM pets WHERE status = 'Adopted' AND DATE(adopted_date) = ? ORDER BY name ASC";
    if ($listStmt = $conn->prepare($listSql)) {
        $listStmt->bind_param('s', $today);
        $listStmt->execute();
        $lres = $listStmt->get_result();
        while ($r = $lres->fetch_assoc()) {
            $adoptedRows[] = $r;
        }
        $listStmt->close();
    }
} else {
    // fallback: adopted_date not present — show all adopted pets and warn user
    $note = "Note: Here is just the total number of adopted pets as for today";

    // total adopted (all time)
    $countSql = "SELECT COUNT(*) AS total_adopted FROM pets WHERE status = 'Adopted'";
    if ($countStmt = $conn->prepare($countSql)) {
        $countStmt->execute();
        $cres = $countStmt->get_result();
        if ($crow = $cres->fetch_assoc()) {
            $total_today = intval($crow['total_adopted']);
        }
        $countStmt->close();
    }

    $listSql = "SELECT id, name, species, breed, gender, age, created_at FROM pets WHERE status = 'Adopted' ORDER BY adopted_date DESC, name ASC";
    // Note: 'adopted_date' may not exist; we use ORDER BY adopted_date safely by avoiding it if not present.
    // We'll fetch without the adopted_date ordering to avoid SQL error.
    $listSql = "SELECT id, name, species, breed, gender, age FROM pets WHERE status = 'Adopted' ORDER BY name ASC";
    if ($listStmt = $conn->prepare($listSql)) {
        $listStmt->execute();
        $lres = $listStmt->get_result();
        while ($r = $lres->fetch_assoc()) {
            $adoptedRows[] = $r;
        }
        $listStmt->close();
    }
}

?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Admin - Today's Adoptions</title>
</head>
<body>

<h1>Admin — Today's Adoption Summary</h1>
  <p>
    <a href="index.php"><button type="button">Home</button></a>
    <a href="add_pet.php"><button type="button">+ Add New Pet</button></a>
    <a href="adminprofile.php"><button type="button">Profile</button></a>
    <a href="logout.php"><button type="button">Logout</button></a>
  </p>
<p>As of today (<strong><?php echo esc($todayLabel); ?></strong>)</p>

<?php if ($hasAdoptedDate): ?>
  <p><strong>Total pets adopted today:</strong> <?php echo esc($total_today); ?></p>
<?php else: ?>
  <p><strong>Total adopted (all time):</strong> <?php echo esc($total_today); ?></p>
  <p style="color:maroon;"><?php echo $note; ?></p>
<?php endif; ?>

<?php if (!empty($adoptedRows)): ?>
  <h3>List of adopted pets <?php echo $hasAdoptedDate ? 'today' : '(all adopted)'; ?>:</h3>
  <table border="1" cellpadding="6" cellspacing="0">
    <tr>
      <th>ID</th>
      <th>Name</th>
      <th>Species</th>
      <th>Breed</th>
      <th>Gender</th>
      <th>Age</th>
    </tr>
    <?php foreach ($adoptedRows as $r): ?>
      <tr>
        <td><?php echo esc($r['id']); ?></td>
        <td><?php echo esc($r['name']); ?></td>
        <td><?php echo esc($r['species']); ?></td>
        <td><?php echo esc($r['breed']); ?></td>
        <td><?php echo esc($r['gender']); ?></td>
        <td><?php echo esc($r['age']); ?> yrs</td>
      </tr>
    <?php endforeach; ?>
  </table>
<?php else: ?>
  <p>No adopted pets to show.</p>
<?php endif; ?>

<p><a href="admin.php"><button type="button">Back to Admin Dashboard</button></a></p>

</body>
</html>

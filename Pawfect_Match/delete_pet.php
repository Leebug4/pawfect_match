<?php
session_start();
if (empty($_SESSION['is_admin'])) { header('Location: login.php'); exit; }
require_once 'db.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id'])) { header('Location: admin.php'); exit; }
$id = intval($_POST['id']);
if ($id <= 0) { header('Location: admin.php'); exit; }

// fetch filenames
$g = $conn->prepare("SELECT filename FROM pet_images WHERE pet_id = ?");
$g->bind_param('i', $id);
$g->execute();
$r = $g->get_result();
$files = [];
while ($row = $r->fetch_assoc()) $files[] = $row['filename'];

// delete pet (this cascades pet_images rows)
$d = $conn->prepare("DELETE FROM pets WHERE id = ?");
$d->bind_param('i', $id);
if ($d->execute()) {
    // unlink files
    foreach ($files as $f) {
        $fp = __DIR__ . '/animals/' . $f;
        if (file_exists($fp)) @unlink($fp);
    }
    $_SESSION['msg'] = 'Pet deleted.';
} else {
    $_SESSION['msg'] = 'Failed to delete pet: ' . $d->error;
}
header('Location: admin.php');
exit;

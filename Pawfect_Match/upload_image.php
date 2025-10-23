<?php
session_start();
if (empty($_SESSION['is_admin'])) { header('Location: login.php'); exit; }
require_once 'db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pet_id'])) {
    $pet_id = intval($_POST['pet_id']);
    if ($pet_id <= 0) { header('Location: admin.php'); exit; }

    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['msg'] = 'No file uploaded or upload error.';
        header('Location: admin.php'); exit;
    }

    $allowed = ['image/jpeg','image/png','image/webp'];
    if (!in_array($_FILES['image']['type'], $allowed)) {
        $_SESSION['msg'] = 'Only JPG/PNG/WEBP allowed.';
        header('Location: admin.php'); exit;
    }

    $uploadDir = __DIR__ . '/animals/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    $basename = time() . '_' . preg_replace('/[^a-zA-Z0-9_\-\.]/','', basename($_FILES['image']['name']));
    $target = $uploadDir . $basename;

    if (!move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
        $_SESSION['msg'] = 'Failed to move uploaded file.';
        header('Location: admin.php'); exit;
    }

    $ins = $conn->prepare("INSERT INTO pet_images (pet_id, filename) VALUES (?, ?)");
    $ins->bind_param('is', $pet_id, $basename);
    if ($ins->execute()) {
        $_SESSION['msg'] = 'Image uploaded.';
    } else {
        @unlink($target);
        $_SESSION['msg'] = 'DB error: ' . $ins->error;
    }
}
header('Location: admin.php');
exit;

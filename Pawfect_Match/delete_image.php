<?php
session_start();
if (empty($_SESSION['is_admin'])) { header('Location: login.php'); exit; }
require_once 'db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['image_id'])) {
    $image_id = intval($_POST['image_id']);
    if ($image_id <= 0) { header('Location: admin.php'); exit; }

    $g = $conn->prepare("SELECT filename FROM pet_images WHERE id = ?");
    $g->bind_param('i', $image_id);
    $g->execute();
    $res = $g->get_result();
    if ($res->num_rows === 0) { header('Location: admin.php'); exit; }
    $img = $res->fetch_assoc()['filename'];

    $d = $conn->prepare("DELETE FROM pet_images WHERE id = ?");
    $d->bind_param('i', $image_id);
    if ($d->execute()) {
        $fp = __DIR__ . '/animals/' . $img;
        if (file_exists($fp)) @unlink($fp);
        $_SESSION['msg'] = 'Image deleted.';
    } else {
        $_SESSION['msg'] = 'Failed to delete image: ' . $d->error;
    }
}
header('Location: admin.php');
exit;

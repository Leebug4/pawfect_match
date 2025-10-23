<?php
require_once 'db.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id'])) {
    header('Location: guest.php'); exit;
}
$id = intval($_POST['id']);
if ($id <= 0) { header('Location: guest.php'); exit; }

// transaction like safe update
$conn->begin_transaction();

$get = $conn->prepare("SELECT status, type, name FROM pets WHERE id = ? FOR UPDATE");
$get->bind_param('i', $id);
$get->execute();
$res = $get->get_result();
if (!$res || $res->num_rows === 0) {
    $conn->rollback();
    die('Pet not found.');
}
$row = $res->fetch_assoc();
if ($row['status'] !== 'Available') {
    $conn->rollback();
    $msg = 'Sorry, this pet has already been adopted.';
    header('Location: category.php?type=' . urlencode($row['type']) . '&msg=' . urlencode($msg));
    exit;
}
$upd = $conn->prepare("UPDATE pets SET status = 'Adopted' WHERE id = ?");
$upd->bind_param('i', $id);
if ($upd->execute()) {
    $conn->commit();
    $msg = 'You adopted ' . $row['name'] . '! Thank you!';
    header('Location: category.php?type=' . urlencode($row['type']) . '&msg=' . urlencode($msg));
    exit;
} else {
    $conn->rollback();
    die('Failed to update status: ' . $conn->error);
}

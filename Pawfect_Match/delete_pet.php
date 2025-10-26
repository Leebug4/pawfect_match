<?php
session_start();
require_once 'db.php';

if (empty($_SESSION['is_admin'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['id'])) {
    $pet_id = trim($_POST['id']);

    // Delete pet record safely using prepared statement
    $stmt = $conn->prepare("DELETE FROM pets WHERE id = ?");
    $stmt->bind_param('s', $pet_id); // <-- 's' because manual IDs are strings

    if ($stmt->execute()) {
        // Also delete any images linked to that pet
        $imgStmt = $conn->prepare("DELETE FROM pet_images WHERE pet_id = ?");
        $imgStmt->bind_param('s', $pet_id);
        $imgStmt->execute();
        $imgStmt->close();

        $_SESSION['msg'] = "Pet (ID: $pet_id) deleted successfully.";
    } else {
        $_SESSION['msg'] = "Error deleting pet: " . $stmt->error;
    }

    $stmt->close();
    header('Location: admin.php');
    exit;
} else {
    $_SESSION['msg'] = "❌ Invalid request. Pet ID missing.";
    header('Location: admin.php');
    exit;
}

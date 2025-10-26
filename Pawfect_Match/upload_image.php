<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_FILES['image']) && !empty($_POST['pet_id'])) {
    $pet_id = trim($_POST['pet_id']);
    $uploadDir = __DIR__ . '/uploads/';

    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    // Check if the pet ID exists
    $check = $conn->prepare("SELECT id FROM pets WHERE id = ?");
    $check->bind_param("s", $pet_id);
    $check->execute();
    $res = $check->get_result();

    if ($res->num_rows === 0) {
        $_SESSION['msg'] = "❌ Error: Pet ID '$pet_id' not found.";
        header("Location: admin.php");
        exit;
    }
    $check->close();

    // Handle image upload safely
    $file = $_FILES['image'];
    $originalName = basename($file['name']);
    $extension = pathinfo($originalName, PATHINFO_EXTENSION);
    
    // Create a unique filename: petid_timestamp_random.ext
    $uniqueName = $pet_id . "_" . time() . "_" . rand(1000,9999) . "." . $extension;
    $targetFile = $uploadDir . $uniqueName;

    if (move_uploaded_file($file['tmp_name'], $targetFile)) {
        // Save to DB
        $stmt = $conn->prepare("INSERT INTO pet_images (pet_id, filename) VALUES (?, ?)");
        $stmt->bind_param("ss", $pet_id, $uniqueName);
        $stmt->execute();
        $stmt->close();

        $_SESSION['msg'] = "✅ Image uploaded successfully for Pet ID: $pet_id.";
    } else {
        $_SESSION['msg'] = "❌ Upload failed. Check folder permissions.";
    }

    header("Location: admin.php");
    exit;
} else {
    $_SESSION['msg'] = "❌ Invalid upload request.";
    header("Location: admin.php");
    exit;
}
?>

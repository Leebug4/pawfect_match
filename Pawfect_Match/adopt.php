<?php
session_start();
require_once 'db.php'; // must provide $conn

function esc($s) {
    return htmlspecialchars($s ?? '', ENT_QUOTES);
}

$message = '';
$pet = null;

// if form submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['id'])) {
        $message = "❌ Invalid request. Missing Pet ID.";
    } elseif (empty($_POST['agree'])) {
        $message = "⚠️ You must agree to the adoption terms.";
    } else {
        $id = trim($_POST['id']);

        // check if exists
        $stmt = $conn->prepare("SELECT id, name, species, status FROM pets WHERE id = ?");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $message = "❌ Pet not found in database.";
        } else {
            $pet = $result->fetch_assoc();

            if (strtolower($pet['status']) === 'adopted') {
                $message = "⚠️ This pet is already adopted.";
            } else {
                // mark as adopted
                $update = $conn->prepare("UPDATE pets SET status='Adopted' WHERE id=?");
                $update->bind_param("s", $id);
                if ($update->execute()) {
                    $message = "✅ Congratulations! You have successfully adopted " . esc($pet['name']) . ".";
                } else {
                    $message = "❌ Failed to update adoption status.";
                }
                $update->close();
            }
        }
        $stmt->close();
    }
} else {
    $message = "Invalid access. Please go through the adoption form.";
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Adopt Confirmation</title>
</head>
<body>

<h2>Adoption Result</h2>

<p><?php echo esc($message); ?></p>

<?php if ($pet): ?>
  <h3>Pet Information</h3>
  <p><strong>ID:</strong> <?php echo esc($pet['id']); ?></p>
  <p><strong>Name:</strong> <?php echo esc($pet['name']); ?></p>
  <p><strong>Species:</strong> <?php echo esc($pet['species']); ?></p>
  <p><strong>Status:</strong> Adopted ✅</p>
<?php endif; ?>

<p><a href="guest.php">Back to Categories</a></p>
</body>
</html>

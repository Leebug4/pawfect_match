<?php
// Show all errors for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// connect to database
require_once('db.php');

// Create database/table if not existing
require_once('create_db.php');

// Handle adopt action
if (isset($_GET['adopt_id'])) {
    $pet_id = intval($_GET['adopt_id']);
    $update = $conn->query("UPDATE pets SET status='Adopted' WHERE id=$pet_id");
    if ($update) {
        echo "<p>Pet ID $pet_id has been adopted successfully!</p>";
    }
}

// Handle filter by type
$type_filter = "";
if (isset($_GET['type']) && $_GET['type'] != "") {
    $type = $_GET['type'];
    $type_filter = "WHERE type='$type'";
}

// Fetch pets from database
$sql = "SELECT * FROM pets $type_filter";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pawfect Match - Guest View</title>
</head>
<body>
    <h1>🐾 Pawfect Match - Adopt a Pet</h1>

    <!-- Dropdown Filter -->
    <form method="GET" action="guest.php">
        <label>Select Pet Type:</label>
        <select name="type">
            <option value="">All</option>
            <option value="Dog">Dog</option>
            <option value="Cat">Cat</option>
            <option value="Fish">Fish</option>
            <option value="Bird">Bird</option>
        </select>
        <button type="submit">Show</button>
    </form>
    <hr>

    <!-- Display pets -->
    <table border="1" cellpadding="8" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Type</th>
            <th>Age</th>
            <th>Status</th>
            <th>Image</th>
            <th>Description</th>
            <th>Action</th>
        </tr>

        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['type']; ?></td>
                    <td><?php echo $row['age']; ?></td>
                    <td><?php echo $row['status']; ?></td>
                    <td>
                        <?php if (!empty($row['image'])): ?>
                            <img src="uploads/<?php echo $row['image']; ?>" width="100" height="100">
                        <?php else: ?>
                            No Image
                        <?php endif; ?>
                    </td>
                    <td><?php echo $row['description']; ?></td>
                    <td>
                        <?php if ($row['status'] == 'Available'): ?>
                            <a href="guest.php?adopt_id=<?php echo $row['id']; ?>">Adopt</a>
                        <?php else: ?>
                            Adopted
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="8">No pets found.</td></tr>
        <?php endif; ?>
    </table>

    <p><a href="index.php">⬅ Back to Home</a></p>
</body>
</html>

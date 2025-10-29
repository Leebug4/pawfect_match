<?php
session_start();
require_once 'db.php'; // must provide $conn

function esc($s) {
    return htmlspecialchars($s ?? '', ENT_QUOTES);
}

// helper: find image path
function resolve_pet_image($id) {
    // if ?image is provided via GET, use it
    if (!empty($_GET['image'])) {
        $img = basename($_GET['image']);
        if (file_exists("uploads/$img")) return "uploads/" . $img;
        if (file_exists("animals/$img")) return "animals/" . $img;
        return esc($_GET['image']);
    }

    // fallback by ID
    if (file_exists("uploads/$id.jpg")) return "uploads/$id.jpg";
    if (file_exists("animals/$id.jpg")) return "animals/$id.jpg";

    return ''; // no image
}

// make sure we have an id
if (!isset($_GET['id']) || trim($_GET['id']) === '') {
    echo "Invalid request: missing pet id.";
    exit;
}

$id = trim($_GET['id']);

// select pet (NO 'image' column)
$stmt = $conn->prepare("SELECT id, name, species, breed, gender, age, description, status FROM pets WHERE id = ?");
$stmt->bind_param("s", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Pet not found.";
    exit;
}

$pet = $result->fetch_assoc();
$stmt->close();

$imagePath = resolve_pet_image($pet['id']);
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Adoption Agreement - <?php echo esc($pet['name']); ?></title>
</head>
<body>

<h2>Adoption Agreement</h2>

<?php if (strtolower($pet['status']) === 'adopted'): ?>
  <p style="color:red;"><strong>Note:</strong> This pet is already marked as Adopted in the database.</p>
<?php endif; ?>

<table border="0" width="100%">
  <tr valign="top">
    <!-- Left: Pet details -->
    <td width="45%">
      <h3>Pet Details</h3>

      <?php if ($imagePath): ?>
        <img src="<?php echo esc($imagePath); ?>" width="250" alt=""><br><br>
      <?php else: ?>
        <div style="width:250px;height:250px;border:1px solid #000;text-align:center;line-height:250px;">No Photo</div><br>
      <?php endif; ?>

      <p><strong>ID:</strong> <?php echo esc($pet['id']); ?></p>
      <p><strong>Name:</strong> <?php echo esc($pet['name']); ?></p>
      <p><strong>Species:</strong> <?php echo esc($pet['species']); ?></p>
      <p><strong>Breed:</strong> <?php echo esc($pet['breed']); ?></p>
      <p><strong>Gender:</strong> <?php echo esc($pet['gender']); ?></p>
      <p><strong>Age:</strong> <?php echo esc($pet['age']); ?></p>
      <p><strong>Description:</strong><br><?php echo nl2br(esc($pet['description'])); ?></p>
    </td>

    <!-- Right: Agreement -->
    <td width="55%">
      <h3>Agreement & Profile (Reference)</h3>
      <p>I, the adopter, agree to the following terms:</p>
      <ul>
        <li>Submit the adoption application form.</li>
        <li>Attend the Zoom interview.</li>
        <li>Meet the shelter animals in person.</li>
        <li>Visit your chosen pet to confirm your choice.</li>
        <li>Wait for vet clearance and schedule pick up.</li>
        <li>Pay the adoption fee (example: ₱500 for cats / ₱1000 for dogs).</li>
        <li>Take your pet home and care for them responsibly.</li>
      </ul>

 <!-- Profile Section -->
  <h3>Adopter Profile</h3>
  <p><b>Name:</b> Timothy Smith</p>
  <p>This account has been verified as an adopter in the Pawfect Match Pet Adoption Community.</p>
  <p>He/She has met all necessary requirements and is officially certified.</p>
  <img src="DevsImages/Samson_AboutUs.png" width="200" alt="Timothy Smith">

      <h4>When you are ready</h4>
      <form method="post" action="adopt.php">
        <input type="hidden" name="id" value="<?php echo esc($pet['id']); ?>">
        <p>
          <input type="checkbox" name="agree" required> I agree to the adoption terms.
        </p>
        <p>
          <button type="submit">Confirm Adopt</button>
        </p>
      </form>

      <p><a href="guest.php">Back to categories</a></p>
    </td>
  </tr>
</table>

</body>
</html>

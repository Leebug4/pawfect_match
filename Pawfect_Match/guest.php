<?php
// guest.php - Guest dashboard with text search and image buttons (no CSS)
session_start();
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);

require_once 'db.php';

if (empty($_SESSION['is_guest'])) {
    header('Location: guestLogin.php');
    exit;
}
// valid categories list
$valid_types = ['Dog', 'Cat', 'Hamster', 'Rabbit'];

// simple sanitize helper
function esc($s){ return htmlspecialchars($s, ENT_QUOTES); }

// check search input
$search_msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search'])) {
    $search = trim($_GET['search']);
    if ($search === '') {
        $search_msg = "Please enter a pet type.";
    } elseif (!in_array(ucfirst(strtolower($search)), $valid_types)) {
        $search_msg = "Invalid input — no pets shown.";
    } else {
        // valid input: redirect to category page
        header("Location: category.php?type=" . ucfirst(strtolower($search)));
        exit;
    }
}

// categories and images
$cats = [
    'Dog' => 'animals/dog.jpg',
    'Cat' => 'animals/cat.jpg',
    'Hamster' => 'animals/hamster.jpg',
    'Rabbit' => 'animals/rabbit.jpg'
];
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Pawfect Match — Guest</title>
</head>
<body>

<!-- Header: Home | About Us | Profile | Search -->
<div>
  <a href="guest.php"><button type="button">Home</button></a>
  <a href="adopt.php"><button type="button">Adopt</button></a>
  <a href="donation.php"><button type="button">Donation</button></a>
  <a href="aboutUs.php"><button type="button">About us</button></a>
  <a href="profile.php"><button type="button">Profile</button></a>
  <a href="logout.php"><button type="button">Logout</button></a>

  <!-- Text search -->
  <form method="get" action="" style="display:inline; margin-left:20px;">
    <label for="search">Search:</label>
    <input type="text" id="search" name="search" placeholder="Type Dog, Cat, etc.">
    <button type="submit">Go</button>
  </form>
</div>

<?php if ($search_msg): ?>
  <p style="color:red;"><?php echo esc($search_msg); ?></p>
<?php endif; ?>

<hr>

<h2>Choose a category</h2>

<!-- Image buttons for categories (links to specific pages) -->
<table border="0" cellpadding="8" cellspacing="8">
  <tr>
    <?php foreach ($cats as $type => $img): ?>
      <td align="center" valign="top">
        <a href="<?php echo strtolower($type); ?>.php" style="text-decoration:none;">
          <?php if (file_exists(__DIR__ . '/' . $img)): ?>
            <img src="<?php echo esc($img); ?>" alt="<?php echo esc($type); ?>" width="160" height="120"><br>
          <?php else: ?>
            <div style="width:160px;height:120px;border:1px solid #000;display:inline-block;line-height:120px;">
              <?php echo esc($type); ?> Image
            </div><br>
          <?php endif; ?>
          <button type="button"><?php echo esc($type); ?></button>
        </a>
      </td>
    <?php endforeach; ?>
  </tr>
</table>

   <p> <a href="index.php"><button>Back to Login Page</button></a></p>
</body>
</html>

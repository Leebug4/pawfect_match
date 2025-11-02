<?php
session_start();
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);

require_once 'db.php';

if (empty($_SESSION['is_guest'])) {
    header('Location: guestLogin.php');
    exit;
}

$valid_types = ['Dog', 'Cat', 'Hamster', 'Rabbit'];
function esc($s){ return htmlspecialchars($s, ENT_QUOTES); }

$search_msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search'])) {
    $search = trim($_GET['search']);
    if ($search === '') {
        $search_msg = "Please enter a pet type.";
    } elseif (!in_array(ucfirst(strtolower($search)), $valid_types)) {
        $search_msg = "Invalid input — no pets shown.";
    } else {
        header("Location: category.php?type=" . ucfirst(strtolower($search)));
        exit;
    }
}


$cats = [
    'Dog' => 'backgrounds/DOG.png',
    'Cat' => 'backgrounds/CAT.png',
    'Rabbit' => 'backgrounds/RABBIT.png',
    'Hamster' => 'backgrounds/HAMSTER.png'
];
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Pawfect Match — Guest</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="guest.css">
</head>
<body>

<header>
  <div class="header-container">
    <img src="backgrounds/logo.png" alt="Pawfect Match Logo" class="header-logo">
    <nav>
      <div class="nav-links">
        <a href="guest.php">Home</a>
        <a href="adopt.php">Adopt</a>
        <a href="donation.php">Donation</a>
        <a href="aboutUs.php">About Us</a>
      </div>
      <a href="profile.php" class="active">
        <button class="profile-btn">Profile</button>
      </a>
    </nav>
  <form method="get" action="" class="search-bar">
    <input type="text" id="search" name="search" placeholder="Search pets...">
    <button type="submit">🔍</button>
  </form>
  </div>
    <?php if ($search_msg): ?>
    <p style="color:red;"><?php echo esc($search_msg); ?></p>
    <?php endif; ?>
</header>

<div class="category-container">
  <?php foreach ($cats as $type => $img): ?>
    <div class="category-card">
      <?php if (file_exists(__DIR__ . '/' . $img)): ?>
        <img src="<?php echo esc($img); ?>" alt="<?php echo esc($type); ?>">
      <?php else: ?>
        <div style="width:100px;height:100px;border-radius:50%;background:#ccc;display:flex;align-items:center;justify-content:center;margin:0 auto 5px auto;">
          <span><?php echo esc($type); ?></span>
        </div>
      <?php endif; ?>
      <button onclick="window.location.href='<?php echo strtolower($type); ?>.php'"><?php echo esc($type); ?>s</button> 
    </div> 
  <?php endforeach; ?> 
</div> 

<div class="banner">
  <a href="donation.php">
    <img src="backgrounds/Banner.png" alt="Donate to Help Pets">
  </a>
</div>
</body>
</html>

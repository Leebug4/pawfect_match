<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pawfect Match - Donation</title>
  <link rel="stylesheet" href="donation.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Fredoka+One&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
  <!-- HEADER -->
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
        <a href="logout.php">
          <button class="logout-btn">Logout</button>
        </a>
      </nav>
    </div>
  </header>

  <!-- MAIN CONTENT -->
  <main class="container">
    <div class="images">
      <div class="img-box tall">
        <img src="animals/Donation1a.png" alt="Donation Image 1">
      </div>
      <div class="img-box square">
        <img src="animals/Donation2a.png" alt="Donation Image 2">
      </div>
      <div class="img-box wide">
        <img src="animals/Donation3a.png" alt="Donation Image 3">
      </div>
      <div class="img-box small-rect">
        <img src="animals/Donation4a.png" alt="Donation Image 4">
      </div>
    </div>

    <div class="donation-box">
      <h3>Donate Online</h3>
      <p class="intro"><b>Help us help more animals.</b></p>
      <p>The shelter and all our programs and campaigns are funded solely by donations. You can donate any amount via bank deposit or online. Thank you!</p>

      <form method="post" action="">
        <label for="name"><b>Name</b></label>
        <input type="text" id="name" name="name" value="Timothy Smith">

        <label for="donation_for"><b>Donation For</b></label>
        <select name="donation_for" id="donation_for">
          <option value="Dog">Dog</option>
          <option value="Cat">Cat</option>
          <option value="Rabbit">Rabbit</option>
          <option value="Hamster">Hamster</option>
          <option value="Rescue">Rescue Operations</option>
          <option value="Spay/Neuter">Spay/Neuter for Pets of Indigent Owners</option>
          <option value="Medical">Medical Mission</option>
          <option value="Donation Home">Donation for Home of Pets</option>
          <option value="Donations">Donation for All Pets</option>
        </select>

        <label for="amount"><b>Amount</b></label>
        <input type="text" id="amount" name="amount" placeholder="₱">

        <button type="button" onclick="alert('Purr-nated successfully!')">Donate Now</button>
      </form>
    </div>
  </main>
</body>
</html>

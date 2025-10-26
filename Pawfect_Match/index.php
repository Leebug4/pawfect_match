<?php
// index.php - landing page
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pawfect Match - Welcome</title>
  <link rel="stylesheet" href="index.css">
</head>
<body>
  <div class="container">
    <h1>Welcome to <span style="color: var(--secondary); font-weight: 700;">Pawfect Match</span></h1>
    <p>Sign up as</p>
    <div class="button-group">
      <button onclick="window.location.href='login.php'">Admin</button>
      <button onclick="window.location.href='guestLogin.php'">Fur Parent</button>
    </div>
  </div>
  <footer>
    <p>Saving one pet won't change the world, but for that one pet, the world will change forever.</p>
  </footer>
</body>
</html>

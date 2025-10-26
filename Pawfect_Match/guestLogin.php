<?php
// guestLogin.php
session_start();
$err = '';
if (isset($_POST['username'])) {
    $u = $_POST['username'];
    $p = $_POST['password'];
    // hardcoded credentials
    $GUEST_USER = 'guest';
    $GUEST_PASS = '12345';
    if ($u === $GUEST_USER && $p === $GUEST_PASS) {
        $_SESSION['is_guest'] = true;
        header('Location: guest.php');
        exit;
    } else {
        $err = 'Invalid credentials';
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Guest Login - Pawfect Match</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="guestLogin.css">
</head>
<body>
  <main class="page">
    <section class="hero">
      <div class="brand">
        <h1>Welcome to <span class="accent">Pawfect Match</span></h1>
      </div>

      <div class="login-box">
        <h2 class="panel-title">Welcome, Guest!</h2>
        <p class="subtitle">Login to explore Pawfect Match</p>

        <?php if ($err): ?>
          <div class="error"><?php echo htmlspecialchars($err); ?></div>
        <?php endif; ?>

        <form class="login-form" method="post" novalidate>
          <label for="username">Username</label>
          <input id="username" name="username" type="text" placeholder="Enter username" required>

          <label for="password">Password</label>
          <input id="password" name="password" type="password" placeholder="Enter password" required>

          <button type="submit" class="btn">Login</button>
        </form>

        <a href="index.php" class="back-link">Back to Login Page</a>
      </div>
    </section>
  </main>
</body>
</html>

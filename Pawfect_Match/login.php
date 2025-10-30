<?php
// login.php
session_start();
$err = '';
if (isset($_POST['username'])) {
    $u = $_POST['username'];
    $p = $_POST['password'];

    // hardcoded credentials (change before production)
    $ADMIN_USER = 'PawfectMatchAdmin';
    $ADMIN_PASS = 'Admin12345';

    if ($u === $ADMIN_USER && $p === $ADMIN_PASS) {
        $_SESSION['is_admin'] = true;
        header('Location: adminprofile.php');
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
  <title>Pawfect Match | Admin Login</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="login.css">
</head>
<body>
  <main class="page">
    <section class="hero">
      <div class="brand">
        <h1>Welcome to <span class="accent">Pawfect Match</span></h1>
      </div>

      <div class="login-box">
        <h2 class="panel-title">Hello, Dear Admin!</h2>

        <?php if ($err): ?>
          <div class="error"><?php echo htmlspecialchars($err); ?></div>
        <?php endif; ?>

        <form class="login-form" method="post" novalidate>
          <label for="username">Name</label>
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

<?php
// login.php
session_start();
$err = '';
if (isset($_POST['username'])) {
    $u = $_POST['username'];
    $p = $_POST['password'];
    // hardcoded credentials (change)
    $ADMIN_USER = 'admin';
    $ADMIN_PASS = '12345';
    if ($u === $ADMIN_USER && $p === $ADMIN_PASS) {
        $_SESSION['is_admin'] = true;
        header('Location: admin.php');
        exit;
    } else {
        $err = 'Invalid credentials';
    }
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Admin Login</title></head>
<body>
<h2>Admin Login</h2>
<?php if ($err) echo "<p style='color:red'>".htmlspecialchars($err)."</p>"; ?>
<form method="post">
  <label>Username: <input type="text" name="username" required></label><br><br>
  <label>Password: <input type="password" name="password" required></label><br><br>
  <button type="submit">Login</button>
</form>
<p><a href="index.php">Back to Home</a></p>
</body>
</html>

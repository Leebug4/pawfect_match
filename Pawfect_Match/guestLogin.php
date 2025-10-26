<?php
// guestLogin.php
session_start();
$err = '';
if (isset($_POST['username'])) {
    $u = $_POST['username'];
    $p = $_POST['password'];
    // hardcoded credentials (change)
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
<html>
<head><meta charset="utf-8"><title>Guest Login</title></head>
<body>
<h2>Guest Login</h2>
<?php if ($err) echo "<p style='color:red'>".htmlspecialchars($err)."</p>"; ?>
<form method="post">
  <label>Username: <input type="text" name="username" required></label><br><br>
  <label>Password: <input type="password" name="password" required></label><br><br>
  <button type="submit">Login</button>
</form>
<p><a href="index.php">Back to Login Page</a></p>
</body>
</html>

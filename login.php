<?php
include 'db.php';
session_start();
$msg = "";

if (isset($_POST['submit'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    
    $res = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    if (mysqli_num_rows($res) > 0) {
        $user = mysqli_fetch_assoc($res);
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_logged_in'] = true;
            $_SESSION['user_name'] = $user['fullname'];
            header("Location: index.php");
            exit;
        } else { $msg = "Wrong credentials."; }
    } else { $msg = "User profile non-existent."; }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Sign In - ApexClubVerse</title>
    <link rel="stylesheet" href="style.css"><link rel="stylesheet" href="auth-style.css">
</head>
<body>
<?php include 'header.php'; ?>
<div class="auth-container">
    <h2>Sign In</h2>
    <?php if($msg) echo "<p style='color:red; text-align:center;'>$msg</p>"; ?>
    <form action="login.php" method="POST">
        <div class="form-group"><label>College Email ID</label><input type="email" name="email" required></div>
        <div class="form-group"><label>Password</label><input type="password" name="password" required></div>
        <button type="submit" name="submit" class="btn-auth">Sign In</button>
    </form>
    <p class="hint">New to the portal? <a href="signup.php">Join Portal</a></p>
</div>
<?php include 'footer.php'; ?>
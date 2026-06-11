<?php
include 'db.php';
$msg = "";

if (isset($_POST['submit'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    if (mysqli_num_rows($check) > 0) {
        $msg = "Email already registered.";
    } else {
        if (mysqli_query($conn, "INSERT INTO users (fullname, email, password) VALUES ('$name', '$email', '$pass')")) {
            header("Location: login.php"); exit;
        } else { $msg = "Registration pipeline error."; }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Join Portal - ApexClubVerse</title>
    <link rel="stylesheet" href="style.css"><link rel="stylesheet" href="auth-style.css">
</head>
<body>
<?php include 'header.php'; ?>
<div class="auth-container">
    <h2>Join Portal</h2>
    <?php if($msg) echo "<p style='color:red; text-align:center;'>$msg</p>"; ?>
    <form action="signup.php" method="POST">
        <div class="form-group"><label>Full Name</label><input type="text" name="name" required></div>
        <div class="form-group"><label>College Email ID</label><input type="email" name="email" required></div>
        <div class="form-group"><label>Password</label><input type="password" name="password" required></div>
        <button type="submit" name="submit" class="btn-auth">Register</button>
    </form>
    <p class="hint">Already have an account? <a href="login.php">Sign In</a></p>
</div>
<?php include 'footer.php'; ?>
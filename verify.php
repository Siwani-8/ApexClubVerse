<?php

include 'db.php';

if (!isset($_GET['token'])) {
    die("Invalid verification link.");
}

$token = mysqli_real_escape_string($conn, $_GET['token']);

$result = mysqli_query($conn,
"SELECT *
 FROM users
 WHERE verification_token='$token'
 LIMIT 1");

if(mysqli_num_rows($result) == 0){

    die("Invalid or expired verification link.");

}

$user = mysqli_fetch_assoc($result);

if($user['is_verified'] == 1){

    die("Your account is already verified. You can now log in.");

}

mysqli_query($conn,
"UPDATE users
 SET
 is_verified=1,
 verification_token=NULL
 WHERE id=".$user['id']);

echo "
<!DOCTYPE html>
<html>
<head>
<title>Email Verified</title>
<style>
body{
font-family:Segoe UI;
display:flex;
justify-content:center;
align-items:center;
height:100vh;
background:#f5f5f5;
}
.box{
background:white;
padding:40px;
border-radius:10px;
text-align:center;
box-shadow:0 0 15px rgba(0,0,0,.1);
}
a{
display:inline-block;
margin-top:20px;
padding:10px 20px;
background:#7a1028;
color:white;
text-decoration:none;
border-radius:5px;
}
</style>
</head>
<body>

<div class='box'>
<h2>✅ Email Verified Successfully</h2>

<p>Your ApexClubVerse account is now active.</p>

<a href='login.php'>Login</a>

</div>

</body>
</html>
";
?>
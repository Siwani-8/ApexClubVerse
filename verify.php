<?php

include 'includes/db.php';

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

if($user['email_verified'] == 1){

    die("Your account is already verified. You can now log in.");

}

mysqli_query($conn,
"UPDATE users
 SET
 email_verified=1,
 verification_token=NULL
 WHERE id=".(int)$user['id']);

echo "
<!DOCTYPE html>
<html lang='en'>
<head>
<meta charset='UTF-8'>
<meta name='viewport' content='width=device-width, initial-scale=1.0'>
<title>Email Verified</title>
<style>
*, *::before, *::after { box-sizing: border-box; }
body{
font-family:Segoe UI, sans-serif;
display:flex;
justify-content:center;
align-items:center;
min-height:100vh;
height:auto;
margin:0;
padding:16px;
background:#f5f5f5;
}
.box{
background:white;
width:100%;
max-width:420px;
padding:40px 24px;
border-radius:10px;
text-align:center;
box-shadow:0 0 15px rgba(0,0,0,.1);
}
.box h2{
margin:0 0 12px;
font-size:1.4rem;
line-height:1.3;
}
.box p{
margin:0;
color:#555;
line-height:1.5;
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
@media (max-width: 480px) {
.box { padding: 28px 16px; }
.box h2 { font-size: 1.2rem; }
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

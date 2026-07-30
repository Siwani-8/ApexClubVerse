<?php
$host = "localhost";
$user = "root";
<<<<<<< HEAD
$password = "";
$database = "apex_club_db";

$conn = mysqli_connect($host, $user, $password, $database);
=======
$password = "yunisha";
$database = "apex_club_db";
$port = '3307';
$conn = mysqli_connect($host, $user, $password, $database, $port);
>>>>>>> 5b4dc3c6e71b0ae1cab2540f8e7fcddae5c7a332

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
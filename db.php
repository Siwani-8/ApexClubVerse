<?php
$host = "localhost";
$user = "root";
$password = "yunisha";
$database = "apex_club_db";
$port = '3307';
$conn = mysqli_connect($host, $user, $password, $database, $port);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
<?php
$conn = mysqli_connect("localhost", "root", "yunisha", "apex_club_db", 3307);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
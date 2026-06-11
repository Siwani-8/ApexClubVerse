<?php
$conn = mysqli_connect("localhost", "root", "", "apex_club_db");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
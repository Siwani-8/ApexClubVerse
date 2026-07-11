<?php
$c = mysqli_connect('localhost', 'root', '', 'apex_club_db');
$r = mysqli_query($c, 'DESCRIBE users');
while ($row = mysqli_fetch_assoc($r)) print_r($row);
echo "\nUsers:\n";
$r = mysqli_query($c, 'SELECT * FROM users');
while ($row = mysqli_fetch_assoc($r)) print_r($row);

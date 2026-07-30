<?php
/**
 * One-time migration: adds club_id to users and seeds club admin accounts.
 * Safe to re-run — skips steps that already exist.
 */
$c = mysqli_connect('localhost', 'root', '', 'apex_club_db');
if (!$c) {
    die('Database connection failed.');
}

$col = mysqli_query($c, "SHOW COLUMNS FROM users LIKE 'club_id'");
if (mysqli_num_rows($col) === 0) {
    mysqli_query($c, 'ALTER TABLE users ADD COLUMN club_id INT NULL DEFAULT NULL');
    @mysqli_query($c, 'ALTER TABLE users ADD CONSTRAINT fk_users_club FOREIGN KEY (club_id) REFERENCES clubs(id)');
    echo "Added club_id column.\n";
} else {
    echo "club_id column already exists.\n";
}

$name_col = mysqli_query($c, "SHOW COLUMNS FROM users LIKE 'name'");
if (mysqli_num_rows($name_col) === 0) {
    @mysqli_query($c, "ALTER TABLE users ADD COLUMN name VARCHAR(100) NULL AFTER id");
}

$admins = [
    [1, 'APAC Admin', 'admin.performingarts@apexcollege.edu.np'],
    [2, 'ASLC Admin', 'admin.sports@apexcollege.edu.np'],
    [3, 'ATTC Admin', 'admin.travel@apexcollege.edu.np'],
    [4, 'AMMC Admin', 'admin.media@apexcollege.edu.np'],
    [5, 'AITC Admin', 'admin.it@apexcollege.edu.np'],
    [6, 'HEAT Admin', 'admin.heat@apexcollege.edu.np'],
];

$password = 'ApexAdmin2026!';

foreach ($admins as [$club_id, $name, $email]) {
    $email_safe = mysqli_real_escape_string($c, $email);
    $name_safe = mysqli_real_escape_string($c, $name);
    $pass_safe = mysqli_real_escape_string($c, $password);
    $exists = mysqli_query($c, "SELECT id FROM users WHERE email = '$email_safe'");
    if (mysqli_num_rows($exists) === 0) {
        mysqli_query($c, "INSERT INTO users (name, email, password, role, club_id) VALUES ('$name_safe', '$email_safe', '$pass_safe', 'admin', $club_id)");
        echo "Created admin: $email\n";
    } else {
        mysqli_query($c, "UPDATE users SET role = 'admin', club_id = $club_id, password = '$pass_safe', name = '$name_safe' WHERE email = '$email_safe'");
        echo "Updated admin: $email\n";
    }
}

echo "Migration complete.\n";

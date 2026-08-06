<?php
/**
 * One-time migration: adds club_id to users and seeds club admin accounts.
 * Safe to re-run — skips steps that already exist.
 */
require_once __DIR__ . '/../includes/config.php';

$c = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
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
    [1, 'APAC Admin', 'admin.performingarts@apexcollege.edu.np', 'Apex Performing Arts Club'],
    [2, 'ASLC Admin', 'admin.sports@apexcollege.edu.np', 'Apex Sports and Leadership Club'],
    [3, 'ATTC Admin', 'admin.travel@apexcollege.edu.np', 'Apex Travel and Tourism Club'],
    [4, 'AMMC Admin', 'admin.media@apexcollege.edu.np', 'Apex Media and Marketing Club'],
    [5, 'AITC Admin', 'admin.it@apexcollege.edu.np', 'Apex IT Club'],
    [6, 'HEAT Admin', 'admin.heat@apexcollege.edu.np', 'Apex Health Education and Awareness Team (HEAT)'],
];

$password = getenv('ADMIN_SEED_PASSWORD');
if ($password === false || $password === '') {
    $password = 'Admin@12345';
    echo "Using default seed password Admin@12345 (set ADMIN_SEED_PASSWORD to override).\n";
}

foreach ($admins as [$club_id, $name, $email, $club_name]) {
    $email_safe = mysqli_real_escape_string($c, $email);
    $name_safe = mysqli_real_escape_string($c, $name);
    $club_name_safe = mysqli_real_escape_string($c, $club_name);
    $pass_safe = mysqli_real_escape_string($c, password_hash($password, PASSWORD_DEFAULT));
    $exists = mysqli_query($c, "SELECT id FROM users WHERE email = '$email_safe'");
    if (mysqli_num_rows($exists) === 0) {
        mysqli_query($c, "INSERT INTO users (name, email, password, role, club_id, club_name) VALUES ('$name_safe', '$email_safe', '$pass_safe', 'admin', $club_id, '$club_name_safe')");
        echo "Created admin: $email\n";
    } else {
        mysqli_query($c, "UPDATE users SET role = 'admin', club_id = $club_id, club_name = '$club_name_safe', password = '$pass_safe', name = '$name_safe' WHERE email = '$email_safe'");
        echo "Updated admin: $email\n";
    }
}

echo "Migration complete.\n";

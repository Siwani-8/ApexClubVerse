<?php
session_start();
if (!isset($_SESSION['user_logged_in'])) { header("Location: login.php"); exit; }
include 'db.php';
include 'header.php';

if (!isset($club_id)) { die("Invalid Access Parameter."); }

$club_query = mysqli_query($conn, "SELECT * FROM clubs WHERE id = $club_id");
$club = mysqli_fetch_assoc($club_query);
?>

<style>
    .club-banner { background: var(--primary-crimson); color: white; padding: 4rem 2rem; text-align: center; }
    .club-banner h1 { font-size: 3rem; }
    .club-container { max-width: 900px; margin: 3rem auto; padding: 0 2rem; }
    .content-box { background: white; padding: 3rem; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); font-size: 1.1rem; line-height: 1.8; }
</style>

<div class="club-banner">
    <h1><?php echo $club['name']; ?></h1>
</div>
<div class="club-container">
    <div class="content-box">
        <h2 style="margin-bottom: 1rem; color: var(--primary-crimson);">About Our Community</h2>
        <p><?php echo $club['description']; ?></p>
        
        <h3 style="margin-top: 2.5rem; margin-bottom: 1rem;">Past Highlights</h3>
        <div style="background: #eee; height: 250px; display: flex; align-items: center; justify-content: center; border-radius: 6px; color: #666; font-family: sans-serif;">
            [ Gallery Photo Grid Showcase Placeholder ]
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
<?php
session_start();
if (!isset($_SESSION['user_logged_in'])) { header("Location: login.php"); exit; }
include 'db.php';
include 'header.php';

$events = mysqli_query($conn, "SELECT * FROM events_feed ORDER BY event_date DESC");
?>

<style>
    .container { max-width: 900px; margin: 3rem auto; padding: 0 2rem; }
    .feed-card { background: #ffffff; border-radius: 8px; margin-bottom: 2rem; box-shadow: 0 2px 10px rgba(0,0,0,0.05); overflow: hidden; display: flex; }
    .event-img-box { width: 250px; background: #ccc; min-height: 100%; display: flex; align-items: center; justify-content: center; font-weight: bold; color: #777; font-family: sans-serif;}
    .event-details { padding: 2rem; flex: 1; }
    .event-club { font-family: sans-serif; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1px; color: var(--accent-orange); font-weight: bold; }
    .event-title { font-size: 1.8rem; margin: 0.4rem 0; color: var(--text-dark); }
    .event-date { font-family: sans-serif; font-size: 0.85rem; color: #777; margin-bottom: 1rem; }
    .event-desc { font-family: 'Segoe UI', sans-serif; line-height: 1.6; color: #444; }
</style>

<div class="container">
    <h1 style="font-size: 2.5rem; margin-bottom: 2rem;">Events Feed</h1>
    <?php while($row = mysqli_fetch_assoc($events)) { ?>
        <div class="feed-card">
            <div class="event-img-box">[ Event Image ]</div>
            <div class="event-details">
                <span class="event-club"><?php echo $row['club_name']; ?></span>
                <h2 class="event-title"><?php echo $row['title']; ?></h2>
                <p class="event-date">📅 scheduled for: <?php echo $row['event_date']; ?></p>
                <p class="event-desc"><?php echo $row['description']; ?></p>
            </div>
        </div>
    <?php } ?>
</div>

<?php include 'footer.php'; ?>
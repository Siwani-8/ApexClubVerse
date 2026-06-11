<?php
session_start();

// Gatekeeper: Redirect to login if not authenticated
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

include 'db.php';
include 'header.php';

$message = "";

// Process the vote action
if (isset($_GET['vote_id'])) {
    $vote_id = intval($_GET['vote_id']);
    
    $query = "UPDATE event_options SET votes = votes + 1 WHERE id = $vote_id";
    if (mysqli_query($conn, $query)) {
        $message = "<div class='alert success'>Your vote for the event initiative has been submitted!</div>";
    } else {
        $message = "<div class='alert error'>Voting system error. Please try again.</div>";
    }
}

// Fetch all upvoted items ordered by Club Name
$events = mysqli_query($conn, "SELECT * FROM event_options ORDER BY club_name");
?>

<style>
    .container { max-width: 800px; margin: 4rem auto; padding: 0 2rem; }
    .page-header { margin-bottom: 2.5rem; border-bottom: 1px solid #d0cfca; padding-bottom: 1rem; }
    .page-header h1 { font-size: 2.5rem; color: var(--text-dark); }
    .page-header p { font-family: 'Segoe UI', sans-serif; color: #666; margin-top: 0.5rem; }
    
    .item-card { 
        background: #ffffff; 
        border-radius: 8px; 
        padding: 1.5rem 2rem; 
        margin-bottom: 1.5rem; 
        box-shadow: 0 2px 8px rgba(0,0,0,0.04); 
        display: flex; 
        justify-content: space-between; 
        align-items: center;
        border-left: 5px solid var(--primary-crimson);
    }
    .club-badge { font-family: sans-serif; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1px; color: var(--accent-orange); font-weight: bold; display: block; margin-bottom: 0.3rem; }
    .event-title { font-size: 1.3rem; color: var(--text-dark); }
    
    .vote-section { text-align: right; }
    .vote-count { font-family: sans-serif; font-size: 0.9rem; font-weight: 600; color: #444; display: block; margin-bottom: 0.5rem; }
    .btn-vote { background: var(--primary-crimson); color: white; text-decoration: none; padding: 0.6rem 1.2rem; border-radius: 20px; font-weight: bold; font-family: sans-serif; font-size: 0.85rem; display: inline-block; transition: 0.2s; }
    .btn-vote:hover { background: var(--accent-orange); }
    
    .alert { padding: 1rem; border-radius: 6px; margin-bottom: 1.5rem; font-family: sans-serif; text-align: center; font-weight: bold; }
    .alert.success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .alert.error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
</style>

<div class="container">
    <div class="page-header">
        <h1>Vote for Semester Events</h1>
        <p>Influence your community's agenda. Select which milestone events you want scheduled this semester.</p>
    </div>

    <?php echo $message; ?>

    <div class="voting-list">
        <?php if (mysqli_num_rows($events) > 0): ?>
            <?php while($row = mysqli_fetch_assoc($events)) { ?>
                <div class="item-card">
                    <div>
                        <span class="club-badge"><?php echo $row['club_name']; ?></span>
                        <span class="event-title"><?php echo $row['event_title']; ?></span>
                    </div>
                    <div class="vote-section">
                        <span class="vote-count">Votes: <?php echo $row['votes']; ?></span>
                        <a href="vote-events.php?vote_id=<?php echo $row['id']; ?>" class="btn-vote">Cast Vote</a>
                    </div>
                </div>
            <?php } ?>
        <?php else: ?>
            <p style="font-style: italic; color: #777; text-align: center;">No active event polls matching this criterion are running currently.</p>
        <?php endif; ?>
    </div>
</div>

<?php include 'footer.php'; ?>
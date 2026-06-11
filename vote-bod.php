<?php
session_start();
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}
include 'db.php';

if (isset($_GET['candidate_id'])) {
    $c_id = intval($_GET['candidate_id']);
    mysqli_query($conn, "UPDATE bod_candidates SET votes = votes + 1 WHERE id = $c_id");
    header("Location: vote-bod.php?status=voted");
    exit;
}

$candidates = mysqli_query($conn, "SELECT * FROM bod_candidates ORDER BY club_name");
?>
<!DOCTYPE html>
<html lang="en">
<head><?php
session_start();

if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

include 'db.php';
include 'header.php';

$message = "";

if (isset($_GET['candidate_id'])) {
    $candidate_id = intval($_GET['candidate_id']);
    
    $query = "UPDATE bod_candidates SET votes = votes + 1 WHERE id = $candidate_id";
    if (mysqli_query($conn, $query)) {
        $message = "<div class='alert success'>Ballot counted! Thank you for voting in your student elections.</div>";
    } else {
        $message = "<div class='alert error'>Electoral system pipeline error.</div>";
    }
}

$candidates = mysqli_query($conn, "SELECT * FROM bod_candidates ORDER BY club_name, position");
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
        border-left: 5px solid #2c3539;
    }
    .position-badge { font-family: sans-serif; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1px; color: var(--primary-crimson); font-weight: bold; display: block; margin-bottom: 0.3rem; }
    .candidate-name { font-size: 1.3rem; color: var(--text-dark); font-weight: bold; }
    .club-sub { font-size: 0.9rem; color: #555; font-family: 'Segoe UI', sans-serif; }
    
    .vote-section { text-align: right; }
    .vote-count { font-family: sans-serif; font-size: 0.9rem; font-weight: 600; color: #444; display: block; margin-bottom: 0.5rem; }
    .btn-ballot { background: #2c3539; color: white; text-decoration: none; padding: 0.6rem 1.2rem; border-radius: 20px; font-weight: bold; font-family: sans-serif; font-size: 0.85rem; display: inline-block; transition: 0.2s; }
    .btn-ballot:hover { background: var(--primary-crimson); }
    
    .alert { padding: 1rem; border-radius: 6px; margin-bottom: 1.5rem; font-family: sans-serif; text-align: center; font-weight: bold; }
    .alert.success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
</style>

<div class="container">
    <div class="page-header">
        <h1>Board of Directors (BOD) Voting</h1>
        <p>Participate in your annual democratic process to select the new executive leadership groups.</p>
    </div>

    <?php echo $message; ?>

    <div class="voting-list">
        <?php if (mysqli_num_rows($candidates) > 0): ?>
            <?php while($row = mysqli_fetch_assoc($candidates)) { ?>
                <div class="item-card">
                    <div>
                        <span class="position-badge"><?php echo $row['club_name']; ?> &bull; <?php echo $row['position']; ?> Candidate</span>
                        <span class="candidate-name"><?php echo $row['candidate_name']; ?></span>
                    </div>
                    <div class="vote-section">
                        <span class="vote-count">Tally: <?php echo $row['votes']; ?> votes</span>
                        <a href="vote-bod.php?candidate_id=<?php echo $row['id']; ?>" class="btn-ballot">Vote Ballot</a>
                    </div>
                </div>
            <?php } ?>
        <?php else: ?>
            <p style="font-style: italic; color: #777; text-align: center;">There are no active candidate elections posted at this time.</p>
        <?php endif; ?>
    </div>
</div>

<?php include 'footer.php'; ?>
    <meta charset="UTF-8">
    <title>BOD Elections - Apex Clubs</title>
    <style>
        .container { max-width: 700px; margin: 3rem auto; padding: 2rem; background: white; border-radius: 6px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        h2 { color: var(--dark-red); margin-bottom: 1rem; }
        .item-box { display: flex; justify-content: space-between; align-items: center; background: #f9f9f9; padding: 1rem; margin-bottom: 1rem; border-left: 4px solid var(--primary-red); }
        .vote-btn { background: #27ae60; color: white; text-decoration: none; padding: 0.5rem 1rem; border-radius: 4px; font-weight: bold; }
        .vote-btn:hover { background: #219150; }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container">
    <h2>Board of Directors (BOD) Voting</h2>
    <p style="color:#666; margin-bottom: 1.5rem;">Cast your vote for the next executive committee members.</p>
    
    <?php while($row = mysqli_fetch_assoc($candidates)) { ?>
        <div class="item-box">
            <div>
                <small style="color:var(--primary-red); font-weight:bold; display:block;"><?php echo $row['club_name']; ?> - <?php echo $row['position']; ?></small>
                <strong><?php echo $row['candidate_name']; ?></strong>
            </div>
            <div style="text-align: right;">
                <span style="display:block; margin-bottom:0.3rem; font-size:0.9rem;">Votes: <?php echo $row['votes']; ?></span>
                <a href="vote-bod.php?candidate_id=<?php echo $row['id']; ?>" class="vote-btn">Cast Vote</a>
            </div>
        </div>
    <?php } ?>
</div>
</body>
</html>
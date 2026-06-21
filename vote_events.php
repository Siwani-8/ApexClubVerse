<?php
session_start();
if (!isset($_SESSION['user_logged_in'])) { header("Location: login.php"); exit; }
include 'db.php';
include 'header.php';

$user_email = $_SESSION['user_email'] ?? $_SESSION['user_name'];

// Handle vote submission
if (isset($_POST['vote'])) {
    $poll_id = (int)$_POST['poll_id'];
    $option_id = (int)$_POST['option_id'];

    // Check if user already voted on this poll
    $check = mysqli_query($conn, "SELECT id FROM poll_votes WHERE poll_id = $poll_id AND user_email = '" . mysqli_real_escape_string($conn, $user_email) . "'");

    if (mysqli_num_rows($check) == 0) {
        $email_safe = mysqli_real_escape_string($conn, $user_email);
        mysqli_query($conn, "INSERT INTO poll_votes (poll_id, user_email, option_id) VALUES ($poll_id, '$email_safe', $option_id)");
        mysqli_query($conn, "UPDATE poll_options SET votes = votes + 1 WHERE id = $option_id");
    }
    header("Location: vote-events.php");
    exit;
}

$polls = mysqli_query($conn, "
    SELECT p.*, c.name as club_name 
    FROM polls p 
    JOIN clubs c ON p.club_id = c.id 
    WHERE p.is_active = 1
    ORDER BY p.created_at DESC
");
?>

<style>
    .container { max-width: 800px; margin: 3rem auto; padding: 0 2rem; }
    .page-title { font-size: 2.5rem; color: var(--text-dark); margin-bottom: 0.5rem; }
    .page-subtitle { font-family: sans-serif; color: #777; margin-bottom: 2.5rem; }

    .poll-card { background: var(--card-bg); border: 1px solid #dcdbd7; border-radius: 12px; padding: 2rem; margin-bottom: 2rem; }
    .poll-club-tag { display: inline-block; background: #f0e8e8; color: var(--primary-crimson); font-size: 0.75rem; font-family: sans-serif; font-weight: bold; padding: 0.2rem 0.7rem; border-radius: 20px; margin-bottom: 0.8rem; }
    .poll-question { font-size: 1.3rem; color: var(--text-dark); margin-bottom: 1.5rem; }

    .poll-option { display: block; width: 100%; text-align: left; background: white; border: 1.5px solid #dcdbd7; border-radius: 8px; padding: 0.8rem 1.2rem; margin-bottom: 0.7rem; cursor: pointer; font-family: sans-serif; font-size: 0.95rem; color: var(--text-dark); transition: 0.2s; }
    .poll-option:hover { border-color: var(--primary-crimson); background: #fdf6f6; }

    .result-bar-wrap { margin-bottom: 0.8rem; }
    .result-label { display: flex; justify-content: space-between; font-family: sans-serif; font-size: 0.9rem; margin-bottom: 0.3rem; color: var(--text-dark); }
    .result-track { background: #eee; border-radius: 20px; height: 10px; overflow: hidden; }
    .result-fill { background: var(--primary-crimson); height: 100%; border-radius: 20px; transition: width 0.4s; }

    .voted-badge { display: inline-block; background: #d4edda; color: #155724; font-size: 0.78rem; font-family: sans-serif; font-weight: bold; padding: 0.3rem 0.9rem; border-radius: 20px; margin-bottom: 1rem; }
    .total-votes { font-family: sans-serif; font-size: 0.82rem; color: #999; margin-top: 1rem; }
</style>

<div class="container">
    <h1 class="page-title">Event Vote</h1>
    <p class="page-subtitle">Share your opinion! Vote on themes, topics, and ideas for upcoming club events.</p>

    <?php if(mysqli_num_rows($polls) == 0): ?>
        <p style="font-family:sans-serif; color:#aaa; text-align:center; padding:3rem;">No active polls right now. Check back soon!</p>
    <?php endif; ?>

    <?php while($poll = mysqli_fetch_assoc($polls)):
        $poll_id = $poll['id'];

        // Check if user already voted
        $voted_check = mysqli_query($conn, "SELECT option_id FROM poll_votes WHERE poll_id = $poll_id AND user_email = '" . mysqli_real_escape_string($conn, $user_email) . "'");
        $has_voted = mysqli_num_rows($voted_check) > 0;
        $voted_option_id = $has_voted ? mysqli_fetch_assoc($voted_check)['option_id'] : null;

        $options = mysqli_query($conn, "SELECT * FROM poll_options WHERE poll_id = $poll_id");
        $total_votes = 0;
        $options_data = [];
        while($opt = mysqli_fetch_assoc($options)) {
            $options_data[] = $opt;
            $total_votes += $opt['votes'];
        }
    ?>
    <div class="poll-card">
        <span class="poll-club-tag"><?php echo htmlspecialchars($poll['club_name']); ?></span>
        <h2 class="poll-question"><?php echo htmlspecialchars($poll['question']); ?></h2>

        <?php if($has_voted): ?>
            <span class="voted-badge">✓ You voted</span>
            <?php foreach($options_data as $opt):
                $percent = $total_votes > 0 ? round(($opt['votes'] / $total_votes) * 100) : 0;
            ?>
            <div class="result-bar-wrap">
                <div class="result-label">
                    <span><?php echo htmlspecialchars($opt['option_text']); ?><?php echo $opt['id'] == $voted_option_id ? ' ✓' : ''; ?></span>
                    <span><?php echo $percent; ?>%</span>
                </div>
                <div class="result-track">
                    <div class="result-fill" style="width: <?php echo $percent; ?>%;"></div>
                </div>
            </div>
            <?php endforeach; ?>
            <p class="total-votes"><?php echo $total_votes; ?> total votes</p>
        <?php else: ?>
            <form method="POST">
                <input type="hidden" name="poll_id" value="<?php echo $poll_id; ?>">
                <?php foreach($options_data as $opt): ?>
                <button type="submit" name="option_id" value="<?php echo $opt['id']; ?>" class="poll-option" onclick="this.form.option_id.value='<?php echo $opt['id']; ?>'">
                    <?php echo htmlspecialchars($opt['option_text']); ?>
                </button>
                <?php endforeach; ?>
                <input type="hidden" name="vote" value="1">
            </form>
        <?php endif; ?>
    </div>
    <?php endwhile; ?>
</div>

<?php include 'footer.php'; ?>
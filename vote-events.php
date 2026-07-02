<?php
session_start();
if (!isset($_SESSION['user_logged_in'])) { header("Location: login.php"); exit; }
include 'db.php';
include 'header.php';

$user_email = $_SESSION['user_email'] ?? $_SESSION['user_name'];

if (isset($_POST['vote'])) {
    $poll_id = (int)$_POST['poll_id'];
    $option_id = (int)$_POST['option_id'];
    $check = mysqli_query($conn, "SELECT id FROM poll_votes WHERE poll_id = $poll_id AND user_email = '" . mysqli_real_escape_string($conn, $user_email) . "'");
    if (mysqli_num_rows($check) == 0) {
        $email_safe = mysqli_real_escape_string($conn, $user_email);
        mysqli_query($conn, "INSERT INTO poll_votes (poll_id, user_email, option_id) VALUES ($poll_id, '$email_safe', $option_id)");
        mysqli_query($conn, "UPDATE poll_options SET votes = votes + 1 WHERE id = $option_id");
    }
    header("Location: vote_events.php");
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
    *, *::before, *::after { box-sizing: border-box; }

    /* ── Page ── */
    .vote-page {
        background: #f5f3ef;
        min-height: 100vh;
    }

    /* ── Hero banner ── */
    .vote-hero {
        background: #7a1028;
        padding: 3rem 2rem 3.5rem;
        text-align: center;
        position: relative;
        overflow: hidden;
    }
    .vote-hero::before {
        content: '';
        position: absolute;
        width: 320px; height: 320px;
        border-radius: 50%;
        background: rgba(255,255,255,0.05);
        top: -100px; right: -80px;
        pointer-events: none;
    }
    .vote-hero::after {
        content: '';
        position: absolute;
        width: 220px; height: 220px;
        border-radius: 50%;
        background: rgba(255,255,255,0.04);
        bottom: -80px; left: -50px;
        pointer-events: none;
    }
    .vote-hero-inner { position: relative; z-index: 2; max-width: 600px; margin: 0 auto; }

    .vote-hero-eyebrow {
        display: inline-flex; align-items: center; gap: 6px;
        background: rgba(255,255,255,0.12);
        border: 0.5px solid rgba(255,255,255,0.25);
        border-radius: 20px;
        padding: 5px 14px;
        font-size: 11px; font-weight: 700;
        letter-spacing: 0.08em; text-transform: uppercase;
        color: rgba(255,255,255,0.85);
        font-family: 'Segoe UI', sans-serif;
        margin-bottom: 1rem;
    }
    .vote-hero h1 {
        font-size: 2.4rem; font-weight: 700;
        color: #fff; line-height: 1.15;
        margin-bottom: 0.6rem;
    }
    .vote-hero p {
        color: rgba(255,255,255,0.65);
        font-size: 14px; line-height: 1.65;
        font-family: 'Segoe UI', sans-serif;
        margin-bottom: 2rem;
    }

    /* Stats pill */
    .vote-stats {
        display: inline-flex;
        background: rgba(255,255,255,0.12);
        border: 0.5px solid rgba(255,255,255,0.2);
        border-radius: 12px;
        overflow: hidden;
    }
    .vstat {
        padding: 0.9rem 2rem;
        text-align: center;
        border-right: 0.5px solid rgba(255,255,255,0.15);
    }
    .vstat:last-child { border-right: none; }
    .vstat strong {
        display: block;
        font-size: 1.6rem; font-weight: 700;
        color: #fff; line-height: 1;
        margin-bottom: 3px;
    }
    .vstat span {
        font-family: 'Segoe UI', sans-serif;
        font-size: 10px; font-weight: 600;
        color: rgba(255,255,255,0.5);
        text-transform: uppercase; letter-spacing: 0.08em;
    }

    /* ── Polls section ── */
    .vote-content {
        max-width: 820px;
        margin: 0 auto;
        padding: 2.5rem 2rem 4rem;
    }

    .section-label {
        font-size: 11px; font-weight: 600;
        color: #bbb; text-transform: uppercase;
        letter-spacing: 0.1em;
        font-family: 'Segoe UI', sans-serif;
        margin-bottom: 1.25rem;
    }

    /* Empty state */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        color: #bbb;
        font-family: 'Segoe UI', sans-serif;
        font-size: 14px;
    }
    .empty-state .empty-icon { font-size: 2.5rem; margin-bottom: 0.75rem; display: block; }

    /* ── Poll card ── */
    .poll-card {
        background: #fff;
        border: 0.5px solid #e0ddd6;
        border-radius: 14px;
        overflow: hidden;
        margin-bottom: 1.25rem;
        display: flex;
        transition: box-shadow 0.18s;
    }
    .poll-card:hover { box-shadow: 0 8px 24px rgba(0,0,0,0.08); }

    .poll-side {
        width: 72px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        background: #7a1028;
    }

    /* Accent colours per club index */
    .poll-card:nth-child(6n+1) .poll-side { background: #7a1028; }
    .poll-card:nth-child(6n+2) .poll-side { background: #1a5f9a; }
    .poll-card:nth-child(6n+3) .poll-side { background: #1a7a4a; }
    .poll-card:nth-child(6n+4) .poll-side { background: #6d3a9c; }
    .poll-card:nth-child(6n+5) .poll-side { background: #c75000; }
    .poll-card:nth-child(6n+6) .poll-side { background: #0f6e56; }

    .poll-card:nth-child(6n+1) .poll-club-tag,
    .poll-card:nth-child(6n+1) .result-name.is-leader,
    .poll-card:nth-child(6n+1) .result-fill { color: #7a1028; background: #7a1028; }

    .poll-card:nth-child(6n+2) .poll-club-tag,
    .poll-card:nth-child(6n+2) .result-name.is-leader { color: #1a5f9a; }
    .poll-card:nth-child(6n+2) .result-fill { background: #1a5f9a; }

    .poll-card:nth-child(6n+3) .poll-club-tag,
    .poll-card:nth-child(6n+3) .result-name.is-leader { color: #1a7a4a; }
    .poll-card:nth-child(6n+3) .result-fill { background: #1a7a4a; }

    .poll-card:nth-child(6n+4) .poll-club-tag,
    .poll-card:nth-child(6n+4) .result-name.is-leader { color: #6d3a9c; }
    .poll-card:nth-child(6n+4) .result-fill { background: #6d3a9c; }

    .poll-card:nth-child(6n+5) .poll-club-tag,
    .poll-card:nth-child(6n+5) .result-name.is-leader { color: #c75000; }
    .poll-card:nth-child(6n+5) .result-fill { background: #c75000; }

    .poll-card:nth-child(6n+6) .poll-club-tag,
    .poll-card:nth-child(6n+6) .result-name.is-leader { color: #0f6e56; }
    .poll-card:nth-child(6n+6) .result-fill { background: #0f6e56; }

    .poll-main { padding: 1.5rem 1.75rem; flex: 1; min-width: 0; }

    .poll-club-tag {
        display: inline-block;
        font-size: 10px; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.08em;
        font-family: 'Segoe UI', sans-serif;
        margin-bottom: 0.45rem;
        color: #7a1028;
    }

    .poll-question {
        font-size: 1.05rem; font-weight: 600;
        color: #1a1a1a; line-height: 1.4;
        margin-bottom: 1.2rem;
    }

    /* Voted badge */
    .voted-badge {
        display: inline-flex; align-items: center; gap: 5px;
        background: #e8f6ee;
        border: 0.5px solid #b6dfc5;
        border-radius: 20px;
        padding: 4px 12px;
        font-size: 11px; font-weight: 600;
        color: #1a7a4a;
        font-family: 'Segoe UI', sans-serif;
        margin-bottom: 1.1rem;
    }

    /* Results bars */
    .result-row { margin-bottom: 0.85rem; }
    .result-top {
        display: flex; justify-content: space-between;
        align-items: baseline; margin-bottom: 5px;
    }
    .result-name {
        font-family: 'Segoe UI', sans-serif;
        font-size: 13px; color: #444;
    }
    .result-name.is-leader { font-weight: 700; }
    .result-pct {
        font-size: 13px; font-weight: 700;
        color: #1a1a1a;
        font-family: 'Segoe UI', sans-serif;
    }
    .result-track {
        background: #f0ede7;
        border-radius: 30px; height: 8px;
        overflow: hidden;
    }
    .result-fill {
        height: 100%; border-radius: 30px;
        background: #7a1028;
        transition: width 0.5s ease;
    }
    .total-votes-label {
        font-family: 'Segoe UI', sans-serif;
        font-size: 11px; color: #bbb;
        margin-top: 0.9rem;
    }

    /* Vote buttons */
    .poll-options-row { display: flex; flex-wrap: wrap; gap: 8px; }
    .poll-option {
        background: #f5f3ef;
        border: 0.5px solid #ddd;
        border-radius: 8px;
        padding: 9px 16px;
        font-family: 'Segoe UI', sans-serif;
        font-size: 13px; font-weight: 500;
        color: #333;
        cursor: pointer;
        transition: background 0.15s, border-color 0.15s, transform 0.12s;
    }
    .poll-option:hover {
        background: #7a1028;
        border-color: #7a1028;
        color: #fff;
        transform: translateY(-1px);
    }

    /* Responsive */
    @media (max-width: 600px) {
        .poll-card { flex-direction: column; }
        .poll-side { width: 100%; height: 52px; }
        .vote-stats { flex-direction: column; width: 100%; }
        .vstat { border-right: none; border-bottom: 0.5px solid rgba(255,255,255,0.15); }
        .vstat:last-child { border-bottom: none; }
    }
</style>

<div class="vote-page">

    <!-- Hero -->
    <div class="vote-hero">
        <div class="vote-hero-inner">
            <div class="vote-hero-eyebrow">&#128203; Community polls</div>
            <h1>Event Vote</h1>
            <p>Share your opinion — vote on themes, topics, and ideas for upcoming club events.</p>

            <?php
            $poll_count = mysqli_num_rows($polls);
            mysqli_data_seek($polls, 0);
            $total_all_votes = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(votes) as c FROM poll_options"))['c'] ?? 0;
            ?>

            <div class="vote-stats">
                <div class="vstat">
                    <strong><?php echo $poll_count; ?></strong>
                    <span>Active polls</span>
                </div>
                <div class="vstat">
                    <strong><?php echo $total_all_votes; ?></strong>
                    <span>Total votes</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Polls -->
    <div class="vote-content">

        <?php if($poll_count == 0): ?>
            <div class="empty-state">
                <span class="empty-icon">&#128203;</span>
                No active polls right now. Check back soon!
            </div>
        <?php else: ?>
            <div class="section-label">Active polls &mdash; <?php echo $poll_count; ?> open</div>
        <?php endif; ?>

        <?php
        $club_icons = [
            'Apex Performing Arts Club'                    => '',
            'Apex Sports and Leadership Club'              => '',
            'Apex Travel and Tourism Club'                 => '',
            'Apex Media and Marketing Club'                => '',
            'Apex IT Club'                                 => '',
            'Apex Health Education and Awareness Team (HEAT)' => '',
        ];

        while($poll = mysqli_fetch_assoc($polls)):
            $poll_id = $poll['id'];
            $icon = $club_icons[$poll['club_name']] ?? '🗳️';

            $voted_check = mysqli_query($conn, "SELECT option_id FROM poll_votes WHERE poll_id = $poll_id AND user_email = '" . mysqli_real_escape_string($conn, $user_email) . "'");
            $has_voted = mysqli_num_rows($voted_check) > 0;
            $voted_option_id = $has_voted ? mysqli_fetch_assoc($voted_check)['option_id'] : null;

            $options = mysqli_query($conn, "SELECT * FROM poll_options WHERE poll_id = $poll_id");
            $total_votes = 0; $options_data = []; $max_votes = 0;
            while($opt = mysqli_fetch_assoc($options)) {
                $options_data[] = $opt;
                $total_votes += $opt['votes'];
                if ($opt['votes'] > $max_votes) $max_votes = $opt['votes'];
            }
        ?>
        <div class="poll-card">
            <div class="poll-side"><?php echo $icon; ?></div>
            <div class="poll-main">
                <span class="poll-club-tag"><?php echo htmlspecialchars($poll['club_name']); ?></span>
                <h2 class="poll-question"><?php echo htmlspecialchars($poll['question']); ?></h2>

                <?php if($has_voted): ?>
                    <div class="voted-badge">&#10003; You voted</div>
                    <?php foreach($options_data as $opt):
                        $percent = $total_votes > 0 ? round(($opt['votes'] / $total_votes) * 100) : 0;
                        $is_leader = $opt['votes'] == $max_votes && $max_votes > 0;
                    ?>
                    <div class="result-row">
                        <div class="result-top">
                            <span class="result-name <?php echo $is_leader ? 'is-leader' : ''; ?>">
                                <?php echo htmlspecialchars($opt['option_text']); ?>
                                <?php echo $opt['id'] == $voted_option_id ? ' &#10003;' : ''; ?>
                            </span>
                            <span class="result-pct"><?php echo $percent; ?>%</span>
                        </div>
                        <div class="result-track">
                            <div class="result-fill" style="width:<?php echo $percent; ?>%"></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <p class="total-votes-label"><?php echo $total_votes; ?> total vote<?php echo $total_votes != 1 ? 's' : ''; ?></p>

                <?php else: ?>
                    <form method="POST">
                        <input type="hidden" name="poll_id" value="<?php echo $poll_id; ?>">
                        <input type="hidden" name="vote" value="1">
                        <div class="poll-options-row">
                            <?php foreach($options_data as $opt): ?>
                                <button type="submit" name="option_id" value="<?php echo $opt['id']; ?>" class="poll-option">
                                    <?php echo htmlspecialchars($opt['option_text']); ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
        <?php endwhile; ?>

    </div>
</div>

<?php include 'footer.php'; ?>
<?php
session_start();
include 'db.php';
include 'header.php';

$club_id = isset($_GET['id']) ? (int)$_GET['id'] : 1;

$club_result = mysqli_query($conn, "SELECT * FROM clubs WHERE id = $club_id");
$club = mysqli_fetch_assoc($club_result);

if (!$club) { header("Location: clubs.php"); exit; }

$bod_result = mysqli_query($conn, "SELECT * FROM bod_members WHERE club_id = $club_id");
$boa_result = mysqli_query($conn, "SELECT * FROM boa_members WHERE club_id = $club_id");
?>

<style>
    .container { max-width: 1100px; margin: 3rem auto; padding: 0 2rem; }

    /* Hero */
    .club-hero {
        background: var(--card-bg);
        border-top: 5px solid var(--primary-crimson);
        border-radius: 10px;
        padding: 2.5rem 3rem;
        margin-bottom: 3rem;
        border: 1px solid #dcdbd7;
        border-top: 5px solid var(--primary-crimson);
    }
    .club-hero h1 { font-size: 2.2rem; color: var(--primary-crimson); margin-bottom: 0.5rem; }
    .club-hero p { font-family: 'Segoe UI', sans-serif; color: #555; font-size: 1rem; line-height: 1.7; max-width: 700px; }

    /* Section titles */
    .section-title {
        font-size: 1.6rem;
        color: var(--text-dark);
        margin-bottom: 1.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid var(--primary-crimson);
        display: inline-block;
    }
    .section { margin-bottom: 3.5rem; }

    /* Member cards grid */
    .member-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 1.5rem; }

    .member-card {
        background: var(--card-bg);
        border: 1px solid #dcdbd7;
        border-radius: 10px;
        padding: 1.5rem;
        text-align: center;
        transition: 0.2s;
    }
    .member-card:hover { transform: translateY(-4px); box-shadow: 0 8px 20px rgba(0,0,0,0.08); }

    .member-avatar {
        width: 80px; height: 80px;
        border-radius: 50%;
        background: var(--primary-crimson);
        color: white;
        font-size: 1.8rem;
        font-weight: bold;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        font-family: sans-serif;
    }
    .member-card h3 { font-size: 1rem; color: var(--text-dark); margin-bottom: 0.3rem; }
    .member-position {
        display: inline-block;
        background: #f0e8e8;
        color: var(--primary-crimson);
        font-size: 0.75rem;
        font-family: sans-serif;
        font-weight: bold;
        padding: 0.2rem 0.7rem;
        border-radius: 20px;
        margin-bottom: 0.8rem;
    }
    .member-card p { font-family: 'Segoe UI', sans-serif; font-size: 0.85rem; color: #666; line-height: 1.5; }

    /* BOA card slight difference */
    .boa-card .member-avatar { background: #1e2530; }
    .boa-position {
        display: inline-block;
        background: #e8eaf0;
        color: #1e2530;
        font-size: 0.75rem;
        font-family: sans-serif;
        font-weight: bold;
        padding: 0.2rem 0.7rem;
        border-radius: 20px;
        margin-bottom: 0.8rem;
    }

    /* Back button */
    .back-link {
        display: inline-block;
        margin-bottom: 1.5rem;
        color: var(--accent-orange);
        font-weight: bold;
        font-family: sans-serif;
        font-size: 0.9rem;
        text-decoration: none;
    }
    .back-link:hover { text-decoration: underline; }

    /* CTA */
    .cta-box {
        background: var(--primary-crimson);
        color: white;
        border-radius: 10px;
        padding: 2rem 3rem;
        text-align: center;
        margin-bottom: 3rem;
    }
    .cta-box h2 { font-size: 1.5rem; margin-bottom: 0.5rem; }
    .cta-box p { font-family: sans-serif; font-size: 0.95rem; opacity: 0.9; margin-bottom: 1.2rem; }
    .cta-btn {
        display: inline-block;
        background: white;
        color: var(--primary-crimson);
        padding: 0.7rem 2rem;
        border-radius: 25px;
        font-weight: bold;
        font-family: sans-serif;
        text-decoration: none;
        font-size: 0.9rem;
        transition: 0.2s;
    }
    .cta-btn:hover { background: #f0e8e8; }
</style>

<div class="container">
    <a href="clubs.php" class="back-link">&larr; Back to All Clubs</a>

    <!-- Club Hero -->
    <div class="club-hero">
        <h1><?php echo htmlspecialchars($club['name']); ?></h1>
        <p><?php echo htmlspecialchars($club['description']); ?></p>
    </div>

    <!-- BOD Section -->
    <div class="section">
        <h2 class="section-title">Board of Directors (BOD)</h2>
        <div class="member-grid">
            <?php while($member = mysqli_fetch_assoc($bod_result)) {
                $initials = strtoupper(substr($member['name'], 0, 1));
            ?>
            <div class="member-card">
                <div class="member-avatar"><?php echo $initials; ?></div>
                <h3><?php echo htmlspecialchars($member['name']); ?></h3>
                <span class="member-position"><?php echo htmlspecialchars($member['position']); ?></span>
                <p><?php echo htmlspecialchars($member['bio']); ?></p>
            </div>
            <?php } ?>
        </div>
    </div>

    <!-- BOA Section -->
    <div class="section">
        <h2 class="section-title">Board of Advisors (BOA)</h2>
        <div class="member-grid">
            <?php while($advisor = mysqli_fetch_assoc($boa_result)) {
                $initials = strtoupper(substr($advisor['name'], 0, 1));
            ?>
            <div class="member-card boa-card">
                <div class="member-avatar"><?php echo $initials; ?></div>
                <h3><?php echo htmlspecialchars($advisor['name']); ?></h3>
                <span class="boa-position"><?php echo htmlspecialchars($advisor['title']); ?></span>
                <p><?php echo htmlspecialchars($advisor['expertise']); ?></p>
            </div>
            <?php } ?>
        </div>
    </div>

    <!-- CTA Join -->
    <?php if(isset($_SESSION['user_logged_in'])): ?>
    <div class="cta-box">
        <h2>Want to join <?php echo htmlspecialchars($club['name']); ?>?</h2>
        <p>Apply for the club interview and become a part of our team.</p>
        <a href="registration.php?club_id=<?php echo $club_id; ?>" class="cta-btn">Apply for Interview &rarr;</a>
    </div>
    <?php else: ?>
    <div class="cta-box">
        <h2>Interested in joining?</h2>
        <p>Sign in to apply for a club interview.</p>
        <a href="login.php" class="cta-btn">Sign In to Apply &rarr;</a>
    </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
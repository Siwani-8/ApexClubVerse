<?php
include 'header.php';
include 'db.php';

$events = mysqli_query($conn, "
    SELECT e.*, c.name as club_name 
    FROM events e 
    JOIN clubs c ON e.club_id = c.id 
    ORDER BY e.event_date ASC
");
?>

<style>
    *, *::before, *::after { box-sizing: border-box; }

    /* ── Page background ── */
    .events-page {
        min-height: 100vh;
        background: #7a1028;
        background-image:
            radial-gradient(circle at 15% 20%, rgba(255,255,255,0.06) 0%, transparent 40%),
            radial-gradient(circle at 85% 80%, rgba(0,0,0,0.15) 0%, transparent 40%);
        padding: 3rem 1.5rem 4rem;
        position: relative;
        overflow: hidden;
    }
    .events-page::before {
        content: '';
        position: absolute;
        width: 400px; height: 400px;
        border-radius: 50%;
        background: rgba(255,255,255,0.04);
        top: -100px; right: -100px;
        pointer-events: none;
    }
    .events-page::after {
        content: '';
        position: absolute;
        width: 300px; height: 300px;
        border-radius: 50%;
        background: rgba(0,0,0,0.1);
        bottom: -80px; left: -80px;
        pointer-events: none;
    }

    .events-inner {
        max-width: 880px;
        margin: 0 auto;
        position: relative;
        z-index: 2;
    }

    /* ── Page header ── */
    .page-header { margin-bottom: 2rem; }
    .page-header-eyebrow {
        display: inline-flex; align-items: center; gap: 6px;
        background: rgba(255,255,255,0.12);
        border: 0.5px solid rgba(255,255,255,0.25);
        border-radius: 20px;
        padding: 5px 14px;
        font-size: 11px; font-weight: 700;
        letter-spacing: 0.08em; text-transform: uppercase;
        color: rgba(255,255,255,0.85);
        font-family: 'Segoe UI', sans-serif;
        margin-bottom: 0.75rem;
    }
    .page-title {
        font-size: 2.2rem; font-weight: 700;
        color: #fff; margin-bottom: 0.3rem;
    }
    .page-subtitle {
        color: rgba(255,255,255,0.6);
        font-family: 'Segoe UI', sans-serif;
        font-size: 14px;
    }

    /* ── Feed card ── */
    .feed-card {
        background: #fff;
        border-radius: 14px;
        margin-bottom: 1.1rem;
        overflow: hidden;
        display: flex;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        transition: transform 0.18s, box-shadow 0.18s;
    }
    .feed-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.2);
    }

    .feed-card.status-upcoming  { border-left: 4px solid #7a1028; }
    .feed-card.status-ongoing   { border-left: 4px solid #c47f00; }
    .feed-card.status-completed { border-left: 4px solid #1a7a4a; }

    .event-img-box {
        width: 210px; flex-shrink: 0;
        background: #f0eeea;
        display: flex; flex-direction: column;
        align-items: center; justify-content: center;
        gap: 6px; color: #bbb;
        font-family: 'Segoe UI', sans-serif;
        font-size: 12px;
        overflow: hidden;
    }
    .event-img-box img { width: 100%; height: 100%; object-fit: cover; }
    .event-img-box .ph-icon { font-size: 2rem; opacity: 0.35; }

    .event-details { padding: 1.4rem 1.6rem; flex: 1; min-width: 0; }

    .event-top {
        display: flex; align-items: center;
        gap: 8px; margin-bottom: 0.4rem; flex-wrap: wrap;
    }
    .event-club {
        font-family: 'Segoe UI', sans-serif;
        font-size: 10px; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.08em;
        color: #7a1028;
    }
    .event-status {
        display: inline-block;
        font-size: 11px; font-weight: 600;
        padding: 3px 10px; border-radius: 20px;
        font-family: 'Segoe UI', sans-serif;
    }
    .status-upcoming  { background: #fdecea; color: #7a1028; }
    .status-ongoing   { background: #fff3cd; color: #856404; }
    .status-completed { background: #e8f6ee; color: #1a7a4a; }

    .event-title {
        font-size: 1.15rem; font-weight: 600;
        color: #1a1a1a; margin-bottom: 0.5rem;
        line-height: 1.3;
    }
    .event-date, .event-location {
        font-family: 'Segoe UI', sans-serif;
        font-size: 12px; color: #888;
        margin-bottom: 0.3rem;
    }
    .event-desc {
        font-family: 'Segoe UI', sans-serif;
        font-size: 13px; color: #555;
        line-height: 1.6; margin-top: 0.5rem;
    }

    .empty-state {
        text-align: center; padding: 3rem;
        color: rgba(255,255,255,0.5);
        font-family: 'Segoe UI', sans-serif; font-size: 14px;
    }

    @media (max-width: 600px) {
        .feed-card { flex-direction: column; }
        .event-img-box { width: 100%; height: 140px; }
        .page-title { font-size: 1.7rem; }
        .events-page { padding: 2rem 1rem 3rem; }
    }
</style>

<div class="events-page">
    <div class="events-inner">

        <div class="page-header">
            <div class="page-header-eyebrow">&#128197; Apex College</div>
            <h1 class="page-title">Events Feed</h1>
            <p class="page-subtitle">All upcoming, ongoing, and past events across every campus club.</p>
        </div>

        <?php if(mysqli_num_rows($events) == 0): ?>
            <div class="empty-state">No events found. Check back soon!</div>
        <?php endif; ?>

        <?php while($row = mysqli_fetch_assoc($events)): ?>
        <div class="feed-card status-<?php echo htmlspecialchars($row['status']); ?>">

            <div class="event-img-box">
                <?php if($row['title'] == 'Blood Donation Drive'): ?>
                    <img src="images/blood donation.jpg" alt="Blood Donation Drive">
                <?php elseif($row['title'] == 'Summer Cup'): ?>
                    <img src="images/football.jpg" alt="Summer Cup">
                <?php elseif($row['title'] == 'Apex Smile'): ?>
                    <img src="images/smilee.jpg" alt="Apex Smile">
                <?php elseif($row['title'] == 'Apex Musical Evening'): ?>
                    <img src="images/ame.jpg" alt="Apex Musical Evening">
                <?php elseif($row['title'] == 'Apex Gamers Connect'): ?>
                    <img src="images/gamers.jpg" alt="Apex Gamers Connect">
                <?php elseif($row['title'] == 'Adventurous Apex'): ?>
                    <img src="images/adven.jpg" alt="Adventurous Apex">
                <?php elseif($row['title'] == 'Apex EcoSprint'): ?>
                    <img src="images/ecosprint.jpg" alt="Apex EcoSprint">
                <?php elseif($row['title'] == 'Apex Day'): ?>
                    <img src="images/apexday.jpg" alt="Apex Day">
                <?php elseif($row['title'] == 'Apex Code & Combat'): ?>
                    <img src="images/code.jpg" alt="Apex Code and Combat">
                <?php else: ?>
                    <div class="ph-icon">&#128247;</div>
                    <span>Photo coming soon</span>
                <?php endif; ?>
            </div>

            <div class="event-details">
                <div class="event-top">
                    <span class="event-club"><?php echo htmlspecialchars($row['club_name']); ?></span>
                    <span class="event-status status-<?php echo htmlspecialchars($row['status']); ?>"><?php echo ucfirst($row['status']); ?></span>
                </div>
                <h2 class="event-title"><?php echo htmlspecialchars($row['title']); ?></h2>
                <p class="event-date">&#128197; <?php echo date('d M Y', strtotime($row['event_date'])); ?> &nbsp;|&nbsp; &#128336; <?php echo htmlspecialchars($row['event_time']); ?></p>
                <p class="event-location">&#128205; <?php echo htmlspecialchars($row['location']); ?></p>
                <p class="event-desc"><?php echo htmlspecialchars($row['description']); ?></p>
            </div>

        </div>
        <?php endwhile; ?>

    </div>
</div>

<?php include 'footer.php'; ?>
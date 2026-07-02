<?php 
session_start();
include 'db.php'; 
include 'header.php'; 

$result = mysqli_query($conn, "SELECT * FROM clubs");

// Small per-club identity so each row actually looks like its own club,
// not a repeated card template. Falls back to a default if a club id
// isn't listed here yet.
$club_identity = [
    1 => ['icon' => '🎭', 'color' => '#7a1028', 'tint' => '#f6e6e9'],
    2 => ['icon' => '⚽', 'color' => '#1a5c3a', 'tint' => '#e3f0e8'],
    3 => ['icon' => '✈️', 'color' => '#1a5f9a', 'tint' => '#e2edf5'],
    4 => ['icon' => '📸', 'color' => '#6d3a9c', 'tint' => '#efe6f5'],
    5 => ['icon' => '💻', 'color' => '#c75000', 'tint' => '#fbe8db'],
    6 => ['icon' => '🏥', 'color' => '#0f6e56', 'tint' => '#dff0ec'],
];
$default_identity = ['icon' => '🏛️', 'color' => '#5a5a5a', 'tint' => '#ececec'];
?>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Newsreader:ital,wght@0,500;0,600;1,500&display=swap');

    .clubs-page { background: #faf8f4; }

    /* ── Hero (kept crimson, simplified) ── */
    .clubs-hero {
        background: #7a1028;
        padding: 3rem 2rem 3.5rem;
        position: relative;
        overflow: hidden;
    }
    .clubs-hero::before {
        content: '';
        position: absolute;
        top: -60px; right: -60px;
        width: 280px; height: 280px;
        border-radius: 50%;
        background: rgba(255,255,255,0.05);
        pointer-events: none;
    }
    .clubs-hero::after {
        content: '';
        position: absolute;
        bottom: -80px; left: -40px;
        width: 200px; height: 200px;
        border-radius: 50%;
        background: rgba(255,255,255,0.04);
        pointer-events: none;
    }

    .clubs-hero-inner {
        max-width: 1200px;
        margin: 0 auto;
        position: relative;
        z-index: 2;
    }

    .clubs-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(255,255,255,0.12);
        border: 0.5px solid rgba(255,255,255,0.25);
        border-radius: 20px;
        padding: 5px 14px;
        color: rgba(255,255,255,0.85);
        font-size: 11px;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        margin-bottom: 1rem;
        font-family: 'Segoe UI', sans-serif;
    }

    .clubs-hero h1 {
        font-size: 2.6rem;
        color: #fff;
        font-weight: 700;
        line-height: 1.15;
        margin-bottom: 0.6rem;
    }

    .clubs-hero p {
        color: rgba(255,255,255,0.65);
        font-size: 15px;
        line-height: 1.6;
        max-width: 440px;
        margin-bottom: 1.5rem;
        font-family: 'Segoe UI', sans-serif;
    }
   .clubs-stats {
        display: flex;
        gap: 2rem;
    }

    .clubs-stat-val {
        color: #fff;
        font-size: 1.4rem;
        font-weight: 700;
        display: block;
    }

    .clubs-stat-label {
        color: rgba(255,255,255,0.5);
        font-size: 12px;
        font-family: 'Segoe UI', sans-serif;
    }


    .clubs-hero .kicker {
        font-family: 'Segoe UI', sans-serif;
        font-size: 0.78rem;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: rgba(255,255,255,0.65);
        font-weight: 600;
        margin-bottom: 1rem;
    }
    
    .clubs-hero .tally {
        display: flex;
        gap: 2.2rem;
    }
    .clubs-hero .tally div b {
        display: block;
        font-family: 'Newsreader', Georgia, serif;
        font-size: 1.4rem;
        color: #fff;
    }
    .clubs-hero .tally div span {
        font-family: 'Segoe UI', sans-serif;
        font-size: 0.78rem;
        color: rgba(255,255,255,0.55);
    }

    /* ── Directory list ── */
    .clubs-list {
        max-width: 780px;
        margin: -2rem auto 0;
        padding: 0 1.5rem 5rem;
        position: relative;
        z-index: 3;
    }

    .club-row {
        display: flex;
        gap: 1.2rem;
        align-items: center;
        background: #fff;
        border-radius: 12px;
        border-left: 5px solid var(--club-color, #7a1028);
        box-shadow: 0 2px 10px rgba(30,20,10,0.06);
        padding: 1.3rem 1.4rem;
        margin-bottom: 0.9rem;
        text-decoration: none;
        transition: transform 0.15s, box-shadow 0.15s;
    }
    .club-row:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(30,20,10,0.12);
    }

    .club-row .mark {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
        flex-shrink: 0;
        background: var(--club-tint, #f0eee9);
    }

    .club-row .copy { flex: 1; min-width: 0; }

    .club-row h3 {
        font-family: 'Newsreader', Georgia, serif;
        font-weight: 600;
        font-size: 1.2rem;
        color: #201c17;
        margin-bottom: 0.3rem;
    }

    .club-row p {
        font-family: 'Segoe UI', sans-serif;
        font-size: 0.9rem;
        color: #6b6459;
        line-height: 1.55;
        max-width: 480px;
    }

    .club-row .go {
        flex-shrink: 0;
        font-family: 'Segoe UI', sans-serif;
        font-size: 0.78rem;
        font-weight: 700;
        color: var(--club-color, #7a1028);
        white-space: nowrap;
        border: 1.5px solid var(--club-color, #7a1028);
        padding: 0.5rem 1rem;
        border-radius: 30px;
        transition: background 0.15s, color 0.15s;
    }
    .club-row:hover .go { background: var(--club-color, #7a1028); color: #fff; }

    @media (max-width: 560px) {
        .clubs-hero h1 { font-size: 1.9rem; }
        .clubs-hero .tally { gap: 1.4rem; }
        .club-row { flex-wrap: wrap; gap: 0.9rem; }
        .club-row .go { display: none; }
        .club-row .mark { width: 42px; height: 42px; font-size: 1.1rem; }
    }
</style>

<div class="clubs-page">
    <div class="clubs-hero">
        <div class="clubs-hero-inner">
            <div class="kicker">Apex College</div>
            <h1>A running list of the clubs students have built here.</h1>
            <p>No two are run quite the same way — pick one that matches what you're into, and see who's behind it.</p>
            <div class="tally">
                <div><b><?php echo mysqli_num_rows($result); ?></b><span>Active clubs</span></div>
                <div><b>100+</b><span>Members</span></div>
                <div><b>10+</b><span>Events / year</span></div>
            </div>
        </div>
    </div>

    <div class="clubs-list">
        <?php while($row = mysqli_fetch_assoc($result)) {
            $id = (int)$row['id'];
            $identity = $club_identity[$id] ?? $default_identity;
        ?>
            <a class="club-row" href="club_detail.php?id=<?php echo $id; ?>" style="--club-color: <?php echo $identity['color']; ?>; --club-tint: <?php echo $identity['tint']; ?>;">
                <span class="mark"><?php echo $identity['icon']; ?></span>
                <span class="copy">
                    <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                    <p><?php echo htmlspecialchars($row['description']); ?></p>
                </span>
                <span class="go">View club</span>
            </a>
        <?php } ?>
</div>
    </div>
</div>
</div>
<?php include 'footer.php'; ?>

<?php 
session_start();
include 'includes/db.php'; 
include 'includes/header.php'; 

$result = mysqli_query($conn, "SELECT * FROM clubs");
?>

<style>
    /* ── Page layout ── */
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

    /* ── Clubs section ── */
    .clubs-section {
        background: #f5f3ef;
        padding: 2.5rem 2rem 0rem 2rem; /* Bottom padding set to 0 to remove color gap */
    }

    .clubs-section-inner {
        max-width: 1200px;
        margin: 0 auto;
        padding-bottom: 2.5rem; /* Padding kept inside container for spacing above red block */
    }

    .clubs-section-label {
        font-size: 11px;
        font-weight: 600;
        color: #aaa;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        margin-bottom: 1.25rem;
        font-family: 'Segoe UI', sans-serif;
    }

    /* ── Card grid (3 columns) ── */
    .club-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.5rem;
    }

    .club-card {
        width: 100%;
        background: #fff;
        border: 0.5px solid #e0ddd6;
        border-radius: 14px;
        overflow: hidden;
        cursor: pointer;
        transition: transform 0.18s, box-shadow 0.18s;
        text-decoration: none;
        display: flex;
        flex-direction: column;
    }

    .club-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 28px rgba(0,0,0,0.09);
    }

    /* Accent top borders */
    .club-card-accent {
        height: 5px;
        background: #7a1028;
    }

    .club-card:nth-child(6n+1) .club-card-accent { background: #7a1028; }
    .club-card:nth-child(6n+2) .club-card-accent { background: #1a5f9a; }
    .club-card:nth-child(6n+3) .club-card-accent { background: #1a7a4a; }
    .club-card:nth-child(6n+4) .club-card-accent { background: #6d3a9c; }
    .club-card:nth-child(6n+5) .club-card-accent { background: #c75000; }
    .club-card:nth-child(6n+6) .club-card-accent { background: #0f6e56; }

    .club-card:nth-child(6n+1) .explore-link { color: #7a1028; }
    .club-card:nth-child(6n+2) .explore-link { color: #1a5f9a; }
    .club-card:nth-child(6n+3) .explore-link { color: #1a7a4a; }
    .club-card:nth-child(6n+4) .explore-link { color: #6d3a9c; }
    .club-card:nth-child(6n+5) .explore-link { color: #c75000; }
    .club-card:nth-child(6n+6) .explore-link { color: #0f6e56; }

    .club-card-body {
        padding: 1.1rem 1.25rem 1.25rem;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }

    .club-card-icon {
        width: 100%;
        height: 160px;
        background: transparent;
        border-radius: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1.25rem;
        padding: 10px;
    }

    .club-card-icon img {
        max-width: 90% !important;
        max-height: 140px !important;
        width: auto !important;
        height: auto !important;
        object-fit: contain !important;
        display: block !important;
        margin: 0 auto !important;
    }

    .club-card h3 {
        font-size: 16px;
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 0.4rem;
        line-height: 1.35;
        font-family: 'Segoe UI', sans-serif;
    }

    .club-card p {
        font-family: 'Segoe UI', sans-serif;
        color: #777;
        font-size: 13px;
        line-height: 1.6;
        margin-bottom: 1rem;
        flex-grow: 1;
    }

    .club-card-footer {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        border-top: 0.5px solid #f0ede7;
        padding-top: 0.75rem;
        margin-top: auto;
    }

    .explore-link {
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-family: 'Segoe UI', sans-serif;
    }

    .explore-link::after {
        content: '→';
    }

    /* ── Responsive ── */
    @media (max-width: 992px) {
        .club-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        .clubs-hero { padding: 2.5rem 1.5rem 3rem; }
    }

    @media (max-width: 600px) {
        .clubs-hero { padding: 2rem 1.15rem 2.5rem; }
        .clubs-hero h1 { font-size: 1.9rem; }
        .clubs-hero::before { width: 160px; height: 160px; }
        .clubs-hero::after { width: 120px; height: 120px; }
        .clubs-stats {
            flex-wrap: wrap;
            justify-content: center;
            gap: 1.2rem;
        }
        .club-grid { grid-template-columns: 1fr; }
        .club-card-icon { height: 130px; }
        .club-card-icon img { max-height: 110px !important; }
        .clubs-section { padding: 1.75rem 1.15rem 0; }
    }
</style>

<!-- Hero banner -->
<div class="clubs-hero">
    <div class="clubs-hero-inner">
        <div class="clubs-eyebrow">&#127979; Apex College</div>
        <h1>Find Your Apex Community</h1>
        <p>Explore clubs built around your passions , from arts to tech, sports to health.</p>
        <div class="clubs-stats">
            <div>
                <span class="clubs-stat-val">6</span>
                <span class="clubs-stat-label">Active clubs</span>
            </div>
            <div>
                <span class="clubs-stat-val">100+</span>
                <span class="clubs-stat-label">Members</span>
            </div>
            <div>
                <span class="clubs-stat-val">10+</span>
                <span class="clubs-stat-label">Events / year</span>
            </div>
        </div>
    </div>
</div>

<!-- Clubs grid -->
<div class="clubs-section">
    <div class="clubs-section-inner">
        <div class="clubs-section-label">Apex clubs</div>
        <div class="club-grid">
            <?php while($row = mysqli_fetch_assoc($result)) { ?>
                <a class="club-card" href="<?php echo htmlspecialchars(url('club_detail.php?id=' . $row['id'])); ?>">
                    <div class="club-card-accent"></div>
                    <div class="club-card-body">
                        <div class="club-card-icon">
                            <?php if($row['name'] == 'Apex Performing Arts Club'): ?>
                                <img src="<?php echo htmlspecialchars(url('images/apac.png')); ?>" alt="Apex Performing Arts Club">
                            <?php endif; ?>

                            <?php if($row['name'] == 'Apex Sports and Leadership Club'): ?>
                                <img src="<?php echo htmlspecialchars(url('images/sports.png')); ?>" alt="Apex Sports and Leadership Club">
                            <?php endif; ?>

                            <?php if($row['name'] == 'Apex Travel and Tourism Club'): ?>
                                <img src="<?php echo htmlspecialchars(url('images/travel.png')); ?>" alt="Apex Travel and Tourism Club">
                            <?php endif; ?>

                            <?php if($row['name'] == 'Apex Media and Marketing Club'): ?>
                                <img src="<?php echo htmlspecialchars(url('images/media.png')); ?>" alt="Apex Media and Marketing Club">
                            <?php endif; ?>

                            <?php if($row['name'] == 'Apex IT Club'): ?>
                                <img src="<?php echo htmlspecialchars(url('images/it.png')); ?>" alt="Apex IT Club">
                            <?php endif; ?>

                            <?php if($row['name'] == 'Apex Health Education and Awareness Team (HEAT)'): ?>
                                <img src="<?php echo htmlspecialchars(url('images/heat.png')); ?>" alt="Apex Health Education and Awareness Team (HEAT)">
                            <?php endif; ?>
                        </div>
                        
                        <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                        <p><?php echo htmlspecialchars($row['description']); ?></p>
                        <div class="club-card-footer">
                            <span class="explore-link">Explore club page</span>
                        </div>
                    </div>
                </a>
            <?php } ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
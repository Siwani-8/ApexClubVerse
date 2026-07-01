<?php 
session_start();
include 'db.php'; 
include 'header.php'; 

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
        padding: 2.5rem 2rem;
    }

    .clubs-section-inner {
        max-width: 1200px;
        margin: 0 auto;
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

    /* ── Card grid ── */
    .club-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 1.1rem;
        justify-content: center;
    }

    .club-card {
        flex: 0 1 280px;
    }

    .club-card {
        background: #fff;
        border: 0.5px solid #e0ddd6;
        border-radius: 14px;
        overflow: hidden;
        cursor: pointer;
        transition: transform 0.18s, box-shadow 0.18s;
        text-decoration: none;
        display: block;
    }

    .club-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 28px rgba(0,0,0,0.09);
    }

    /* Colour accent bar at top — cycles through 6 colours */
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

    /* Icon badge colours match accent */
    .club-card:nth-child(6n+1) .club-card-icon { background: #fdecea; color: #7a1028; }
    .club-card:nth-child(6n+2) .club-card-icon { background: #e8f0fb; color: #1a5f9a; }
    .club-card:nth-child(6n+3) .club-card-icon { background: #e8f6ee; color: #1a7a4a; }
    .club-card:nth-child(6n+4) .club-card-icon { background: #f3edfb; color: #6d3a9c; }
    .club-card:nth-child(6n+5) .club-card-icon { background: #fef0e8; color: #c75000; }
    .club-card:nth-child(6n+6) .club-card-icon { background: #e5f4f0; color: #0f6e56; }

    /* explore link colour matches accent */
    .club-card:nth-child(6n+1) .explore-link { color: #7a1028; }
    .club-card:nth-child(6n+2) .explore-link { color: #1a5f9a; }
    .club-card:nth-child(6n+3) .explore-link { color: #1a7a4a; }
    .club-card:nth-child(6n+4) .explore-link { color: #6d3a9c; }
    .club-card:nth-child(6n+5) .explore-link { color: #c75000; }
    .club-card:nth-child(6n+6) .explore-link { color: #0f6e56; }

    .club-card-body {
        padding: 1.1rem 1.25rem 1.25rem;
    }
.club-card-icon {
    width: 100px;
    height: 100px;
    border-radius: 12px;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 15px;
}

.club-card-icon img {
    max-width: 85px;
    max-height: 85px;
    width: auto;
    height: auto;
    object-fit: contain;
    display: block;
}

    

    .club-card h3 {
        font-size: 15px;
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
    }

    .club-card-footer {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        border-top: 0.5px solid #f0ede7;
        padding-top: 0.75rem;
        margin-top: 0.25rem;
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
    @media (max-width: 600px) {
        .clubs-hero h1 { font-size: 1.9rem; }
        .clubs-stats { gap: 1.2rem; }
        .club-grid { grid-template-columns: 1fr; }
    }
</style>

<!-- Hero banner -->
<div class="clubs-hero">
    <div class="clubs-hero-inner">
        <div class="clubs-eyebrow">&#127979; Apex College</div>
        <h1>Find Your Campus Community</h1>
        <p>Explore clubs built around your passions — from arts to tech, sports to health.</p>
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
        <div class="clubs-section-label">Campus clubs</div>
        <div class="club-grid">
            <?php while($row = mysqli_fetch_assoc($result)) { ?>
                <a class="club-card" href="club_detail.php?id=<?php echo $row['id']; ?>">
                    <div class="club-card-accent"></div>
                    <div class="club-card-body">
                        <div class="club-card-icon">
                        <img src="<?php echo htmlspecialchars($row['logo']); ?>" 
                         alt="<?php echo htmlspecialchars($row['name']); ?>">
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

<?php include 'footer.php'; ?>
<?php include 'includes/header.php'; ?>

<style>
    /* ── Reset & base ── */
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    /* ══════════════════ HERO ══════════════════ */
    .hero {
        position: relative;
        min-height: 90vh;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 6rem 2rem 5rem;
        overflow: hidden;
        background: #1c0810;
    }

    .hero-bg-img {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center 30%;
        opacity: 0.55;
    }

    .hero-bg-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(28,8,16,0.55) 0%, rgba(28,8,16,0.75) 55%, rgba(28,8,16,0.95) 100%);
    }

    .hero-inner {
        position: relative;
        z-index: 2;
        max-width: 780px;
        margin: 0 auto;
    }

    .hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        background: rgba(255,255,255,0.1);
        backdrop-filter: blur(6px);
        border: 0.5px solid rgba(255,255,255,0.25);
        border-radius: 30px;
        padding: 6px 16px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: #ffd8c2;
        font-family: 'Segoe UI', sans-serif;
        margin-bottom: 2rem;
    }
    .hero-badge-dot {
        width: 7px; height: 7px;
        border-radius: 50%;
        background: #ff4500;
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.5; transform: scale(1.4); }
    }

    .hero-title {
        font-size: clamp(2.4rem, 5vw, 3.8rem);
        color: #fff;
        line-height: 1.12;
        font-weight: 700;
        margin-bottom: 0.5rem;
        letter-spacing: -0.02em;
    }

    .hero-title .brand {
        display: block;
        background: linear-gradient(100deg, #ff8a3d 0%, #ffd166 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-top: 0.2rem;
    }

    .hero-desc {
        font-size: 1.05rem;
        color: #e9dfda;
        line-height: 1.7;
        margin: 1.75rem auto 2.5rem;
        max-width: 580px;
        font-family: 'Segoe UI', sans-serif;
    }

    .hero-ctas {
        display: flex;
        gap: 12px;
        justify-content: center;
        flex-wrap: wrap;
        margin-bottom: 0;
    }

    .btn-primary {
        background: #ff4500;
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 13px 28px;
        font-size: 14px;
        font-weight: 600;
        font-family: 'Segoe UI', sans-serif;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: background 0.18s, transform 0.15s;
    }
    .btn-primary:hover {
        background: #d63a00;
        transform: translateY(-1px);
    }

    .btn-secondary {
        background: rgba(255,255,255,0.08);
        color: #fff;
        border: 0.5px solid rgba(255,255,255,0.35);
        border-radius: 8px;
        padding: 13px 28px;
        font-size: 14px;
        font-weight: 600;
        font-family: 'Segoe UI', sans-serif;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: border-color 0.18s, transform 0.15s, background 0.18s;
    }
    .btn-secondary:hover {
        border-color: #ff4500;
        background: rgba(255,255,255,0.16);
        transform: translateY(-1px);
    }

    .btn-white {
        background: #fff;
        color: #7a1028;
        border: none;
        border-radius: 8px;
        padding: 12px 26px;
        font-size: 14px;
        font-weight: 600;
        font-family: 'Segoe UI', sans-serif;
        text-decoration: none;
        display: inline-block;
        transition: opacity 0.15s;
    }
    .btn-white:hover { opacity: 0.9; }

    /* ══════════════════ SECTION SHELL ══════════════════ */
    .section {
        padding: 5.5rem 2rem;
        max-width: 1180px;
        margin: 0 auto;
    }
    .section-eyebrow {
        text-align: center;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: #7a1028;
        font-family: 'Segoe UI', sans-serif;
        margin-bottom: 0.8rem;
    }
    .section-title {
        text-align: center;
        font-size: clamp(1.7rem, 3.5vw, 2.4rem);
        font-weight: 700;
        color: #1c1c1c;
        margin-bottom: 0.9rem;
        letter-spacing: -0.01em;
    }
    .section-desc {
        text-align: center;
        font-size: 1rem;
        color: #666;
        max-width: 560px;
        margin: 0 auto 3.5rem;
        line-height: 1.7;
        font-family: 'Segoe UI', sans-serif;
    }

    /* ══════════════════ LIFE AT CLUBVERSE CARDS ══════════════════ */
    .life-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.75rem;
    }
    .life-card {
        position: relative;
        border-radius: 16px;
        overflow: hidden;
        aspect-ratio: 3 / 4;
        text-decoration: none;
        display: block;
        box-shadow: 0 8px 24px rgba(0,0,0,0.1);
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }
    .life-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 16px 32px rgba(0,0,0,0.18);
    }
    .life-card img {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
    }
    .life-card:hover img { transform: scale(1.06); }
    .life-card-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(0,0,0,0) 40%, rgba(20,4,8,0.88) 100%);
    }
    .life-card-content {
        position: absolute;
        left: 0; right: 0; bottom: 0;
        padding: 1.5rem 1.4rem;
        color: #fff;
    }
    .life-card-tag {
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: #ffb88a;
        font-family: 'Segoe UI', sans-serif;
        margin-bottom: 6px;
        display: block;
    }
    .life-card-title {
        font-size: 1.3rem;
        font-weight: 700;
        margin-bottom: 6px;
    }
    .life-card-desc {
        font-size: 0.85rem;
        color: #e5d9d4;
        font-family: 'Segoe UI', sans-serif;
        line-height: 1.5;
        margin-bottom: 10px;
    }
    .life-card-link {
        font-size: 0.82rem;
        font-weight: 700;
        color: #fff;
        font-family: 'Segoe UI', sans-serif;
    }

    /* ══════════════════ NUMBERS BAND ══════════════════ */
    .numbers-band {
        background: #7a1028;
        padding: 4rem 2rem;
    }
    .numbers-inner {
        max-width: 1100px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 2rem;
        text-align: center;
    }
    .numbers-inner .num {
        font-size: 2.4rem;
        font-weight: 700;
        color: #fff;
        line-height: 1;
        margin-bottom: 8px;
    }
    .numbers-inner .label {
        font-size: 12px;
        color: #ffcbb0;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        font-family: 'Segoe UI', sans-serif;
        font-weight: 600;
    }

    /* ══════════════════ GALLERY ══════════════════ */
    .gallery-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        grid-auto-rows: 190px;
        grid-auto-flow: dense;
        gap: 14px;
    }
    .gallery-item {
        position: relative;
        border-radius: 12px;
        overflow: hidden;
    }
    .gallery-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        transition: transform 0.4s ease;
    }
    .gallery-item:hover img { transform: scale(1.08); }
    .gallery-item .gcap {
        position: absolute;
        left: 0; right: 0; bottom: 0;
        padding: 10px 12px;
        font-size: 12px;
        font-weight: 600;
        color: #fff;
        font-family: 'Segoe UI', sans-serif;
        background: linear-gradient(180deg, rgba(0,0,0,0) 0%, rgba(0,0,0,0.7) 100%);
    }
    .gi-1 { grid-column: span 2; grid-row: span 2; }
    .gi-2 { grid-column: span 1; grid-row: span 1; }
    .gi-3 { grid-column: span 1; grid-row: span 1; }
    .gi-4 { grid-column: span 1; grid-row: span 2; }
    .gi-5 { grid-column: span 1; grid-row: span 1; }
    .gi-6 { grid-column: span 1; grid-row: span 1; }
    .gi-7 { grid-column: span 1; grid-row: span 2; }
    .gi-8 { grid-column: span 1; grid-row: span 1; }

    /* ══════════════════ CTA BANNER ══════════════════ */
    .cta-banner {
        margin: 0 auto 5.5rem;
        max-width: 1180px;
        border-radius: 20px;
        background: linear-gradient(110deg, #7a1028 0%, #b23417 100%);
        padding: 3.5rem 3rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1.5rem;
    }
    .cta-banner h3 {
        color: #fff;
        font-size: 1.6rem;
        font-weight: 700;
        margin-bottom: 6px;
    }
    .cta-banner p {
        color: #ffd8c2;
        font-family: 'Segoe UI', sans-serif;
        font-size: 0.95rem;
    }

    /* ── Responsive ── */
    @media (max-width: 900px) {
        .hero { padding: 5rem 1.5rem 4rem; min-height: 80vh; }
        .life-grid { grid-template-columns: 1fr; }
        .numbers-inner { grid-template-columns: repeat(2, 1fr); }
        .gallery-grid { grid-template-columns: repeat(2, 1fr); grid-auto-rows: 160px; }
        .gi-1, .gi-4, .gi-7 { grid-column: span 2; grid-row: span 1; }
        .cta-banner { flex-direction: column; text-align: center; padding: 2.5rem 1.75rem; }
        .section { padding: 4rem 1.5rem; }
    }
    @media (max-width: 600px) {
        .hero { padding: 4rem 1.15rem 3.5rem; min-height: auto; }
        .hero-title { font-size: 2rem; }
        .hero-desc { font-size: 0.95rem; margin: 1.25rem auto 1.75rem; }
        .hero-ctas { flex-direction: column; align-items: stretch; }
        .btn-primary, .btn-secondary { justify-content: center; width: 100%; }
        .gallery-grid { grid-template-columns: 1fr; grid-auto-rows: 160px; gap: 8px; }
        .gi-1, .gi-4, .gi-7 { grid-column: span 1; }
        .numbers-inner { grid-template-columns: 1fr 1fr; gap: 1rem; }
        .section { padding: 3rem 1.15rem; }
        .cta-banner { padding: 2rem 1.25rem; }
    }
    @media (max-width: 400px) {
        .numbers-inner { grid-template-columns: 1fr; }
    }
</style>

<!-- ══════════════════ HERO ══════════════════ -->
<div class="hero">
    <img class="hero-bg-img" src="<?php echo htmlspecialchars(url('images/hero-group.jpeg')); ?>" alt="ApexClubVerse members at Adventurous Apex retreat">
    <div class="hero-bg-overlay"></div>
    <div class="hero-inner">

        <div class="hero-badge">
            <span class="hero-badge-dot"></span>
            SIX CLUBS &middot; ONE COMMUNITY
        </div>

        <h1 class="hero-title">
            Unleash Your Potential at
            <span class="brand">ApexClubVerse</span>
        </h1>

        <p class="hero-desc">
            Join one of Apex College's six prominent clubs. Register for flagship tournaments, submit advisory applications, cast your votes, and shape university culture alongside your peers.
        </p>

        <div class="hero-ctas">
            <a href="<?php echo htmlspecialchars(url('clubs.php')); ?>" class="btn-primary">Browse clubs &rarr;</a>
            <a href="<?php echo htmlspecialchars(url('events.php')); ?>" class="btn-secondary">See events feed</a>
        </div>

    </div>
</div>

<!-- ══════════════════ LIFE AT CLUBVERSE ══════════════════ -->
<div class="section">
    <div class="section-eyebrow">Life at ApexClubVerse</div>
    <h2 class="section-title">Your Adventure Starts Here</h2>
    <p class="section-desc">From flagship retreats to championship nights, our clubs give you a place to lead, compete, and connect.</p>

    <div class="life-grid">
        <a href="<?php echo htmlspecialchars(url('clubs.php')); ?>" class="life-card">
            <img src="<?php echo htmlspecialchars(url('images/clubs-stage.jpeg')); ?>" alt="Club leaders on stage at Apex Day 2026">
            <div class="life-card-overlay"></div>
            <div class="life-card-content">
                <span class="life-card-tag">Student Clubs</span>
                <div class="life-card-title">Six Clubs, One Stage</div>
                <p class="life-card-desc">From Apex Heat to Media &amp; Marketing, find the club that matches your passion.</p>
                <span class="life-card-link">Explore clubs &rarr;</span>
            </div>
        </a>

        <a href="<?php echo htmlspecialchars(url('events.php')); ?>" class="life-card">
            <img src="<?php echo htmlspecialchars(url('images/night-crowd.jpeg')); ?>" alt="Students celebrating at an ApexClubVerse night event">
            <div class="life-card-overlay"></div>
            <div class="life-card-content">
                <span class="life-card-tag">Events &amp; Adventures</span>
                <div class="life-card-title">Nights to Remember</div>
                <p class="life-card-desc">Flagship retreats, tournaments, and celebrations that bring the whole campus together.</p>
                <span class="life-card-link">See events feed &rarr;</span>
            </div>
        </a>

        <a href="<?php echo htmlspecialchars(url('vote-events.php')); ?>" class="life-card">
            <img src="<?php echo htmlspecialchars(url('images/football.jpeg')); ?>" alt="Apex Sports Club football match">
            <div class="life-card-overlay"></div>
            <div class="life-card-content">
                <span class="life-card-tag">Sports &amp; Wellness</span>
                <div class="life-card-title">Compete &amp; Recharge</div>
                <p class="life-card-desc">Cheer on club teams and cast your vote for the next big event on campus.</p>
                <span class="life-card-link">Cast your vote &rarr;</span>
            </div>
        </a>
    </div>
</div>

<!-- ══════════════════ NUMBERS BAND ══════════════════ -->
<div class="numbers-band">
    <div class="numbers-inner">
        <div>
            <div class="num">6</div>
            <div class="label">Active clubs</div>
        </div>
        <div>
            <div class="num">800+</div>
            <div class="label">Members</div>
        </div>
        <div>
            <div class="num">8+</div>
            <div class="label">Events / year</div>
        </div>
        <div>
            <div class="num">100%</div>
            <div class="label">Student-led</div>
        </div>
    </div>
</div>

<!-- ══════════════════ GALLERY ══════════════════ -->
<div class="section">
    <div class="section-eyebrow">Club Moments</div>
    <h2 class="section-title">Highlights from ApexClubVerse</h2>
    <p class="section-desc">Guest talks, wellness sessions, gaming nights, and teams giving it their all   "a look back at our latest moments".</p>

    <div class="gallery-grid">
        <div class="gallery-item gi-1">
            <img src="<?php echo htmlspecialchars(url('images/speaker-man.jpeg')); ?>" alt="Guest speaker addressing ApexClubVerse members">
            <div class="gcap">Guest Speaker Series</div>
        </div>
        <div class="gallery-item gi-2">
            <img src="<?php echo htmlspecialchars(url('images/soundbath.jpeg')); ?>" alt="Apex Heat wellness sound bath session">
            <div class="gcap">Apex Heat Wellness</div>
        </div>
        <div class="gallery-item gi-3">
            <img src="<?php echo htmlspecialchars(url('images/gaming.jpeg')); ?>" alt="Apex gaming club members competing">
            <div class="gcap">Gaming Nights</div>
        </div>
        <div class="gallery-item gi-4">
            <img src="<?php echo htmlspecialchars(url('images/basketball-team.jpeg')); ?>" alt="Apex Sports Club basketball team">
            <div class="gcap">Sports &amp; Leadership Club</div>
        </div>
        <div class="gallery-item gi-5">
            <img src="<?php echo htmlspecialchars(url('images/speaker-woman.jpeg')); ?>" alt="Guest speaker at ApexClubVerse event">
            <div class="gcap">Talks &amp; Panels</div>
        </div>
        <div class="gallery-item gi-6">
            <img src="<?php echo htmlspecialchars(url('images/hero-group.jpeg')); ?>" alt="ApexClubVerse members at Adventurous Apex retreat">
            <div class="gcap">Adventurous Apex Retreat</div>
        </div>
        <div class="gallery-item gi-7">
            <img src="<?php echo htmlspecialchars(url('images/dance.jpeg')); ?>" alt="Apex Performing Arts Club classical dance performance">
            <div class="gcap">Performing Arts Club</div>
        </div>
        <div class="gallery-item gi-8">
            <img src="<?php echo htmlspecialchars(url('images/siwani.jpeg')); ?>" alt="ApexClubVerse members celebrating Apex Day">
            <div class="gcap">Apex Day</div>
        </div>
    </div>
</div>

<!-- ══════════════════ CTA BANNER ══════════════════ -->
<div class="cta-banner">
    <div>
        <h3>Ready to join the ClubVerse?</h3>
        <p>Submit your club intake application and start shaping Apex culture today.</p>
    </div>
    <a href="<?php echo htmlspecialchars(url('registration.php')); ?>" class="btn-white">Get started &rarr;</a>
</div>

</div>
<!-- closes .content-wrapper opened in header.php -->

<?php include 'includes/footer.php'; ?>
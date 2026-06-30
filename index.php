<?php include 'header.php'; ?>

<style>
    /* ── Reset & base ── */
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    /* ── Hero ── */
    .hero {
        min-height: 92vh;
        background: #f5f3ef;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 5rem 2rem 4rem;
        position: relative;
        overflow: hidden;
    }

    /* Decorative background circles */
    .hero::before {
        content: '';
        position: absolute;
        width: 500px; height: 500px;
        border-radius: 50%;
        background: rgba(122, 16, 40, 0.06);
        top: -120px; right: -120px;
        pointer-events: none;
    }
    .hero::after {
        content: '';
        position: absolute;
        width: 340px; height: 340px;
        border-radius: 50%;
        background: rgba(255, 69, 0, 0.05);
        bottom: -100px; left: -80px;
        pointer-events: none;
    }

    /* Decorative dots grid */
    .hero-dots {
        position: absolute;
        inset: 0;
        background-image: radial-gradient(circle, #c8b8b8 1px, transparent 1px);
        background-size: 32px 32px;
        opacity: 0.25;
        pointer-events: none;
    }

    .hero-inner {
        position: relative;
        z-index: 2;
        max-width: 780px;
        margin: 0 auto;
    }

    /* Badge */
    .hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        background: #fff;
        border: 0.5px solid #e0ddd6;
        border-radius: 30px;
        padding: 6px 16px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: #7a1028;
        font-family: 'Segoe UI', sans-serif;
        margin-bottom: 2rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    }
    .hero-badge-dot {
        width: 7px; height: 7px;
        border-radius: 50%;
        background: #7a1028;
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.5; transform: scale(1.4); }
    }

    /* Headline */
    .hero-title {
        font-size: clamp(2.4rem, 5vw, 3.8rem);
        color: #1c1c1c;
        line-height: 1.12;
        font-weight: 700;
        margin-bottom: 0.5rem;
        letter-spacing: -0.02em;
    }

    .hero-title .brand {
        display: block;
        background: linear-gradient(100deg, #7a1028 0%, #d44000 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-top: 0.2rem;
    }

    /* Sub-text */
    .hero-desc {
        font-size: 1.05rem;
        color: #666;
        line-height: 1.7;
        margin: 1.75rem auto 2.5rem;
        max-width: 580px;
        font-family: 'Segoe UI', sans-serif;
    }

    /* CTA buttons */
    .hero-ctas {
        display: flex;
        gap: 12px;
        justify-content: center;
        flex-wrap: wrap;
        margin-bottom: 3.5rem;
    }

    .btn-primary {
        background: #7a1028;
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
        background: #5e0c1e;
        transform: translateY(-1px);
    }

    .btn-secondary {
        background: #fff;
        color: #1c1c1c;
        border: 0.5px solid #d0cdc7;
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
        transition: border-color 0.18s, transform 0.15s;
    }
    .btn-secondary:hover {
        border-color: #7a1028;
        color: #7a1028;
        transform: translateY(-1px);
    }

    /* Stat pills */
    .hero-stats {
        display: flex;
        gap: 0;
        justify-content: center;
        background: #fff;
        border: 0.5px solid #e0ddd6;
        border-radius: 14px;
        padding: 0;
        overflow: hidden;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        width: fit-content;
        margin: 0 auto 3.5rem;
    }

    .hero-stat {
        padding: 1.1rem 2rem;
        text-align: center;
        border-right: 0.5px solid #e0ddd6;
    }
    .hero-stat:last-child { border-right: none; }

    .stat-number {
        font-size: 1.6rem;
        font-weight: 700;
        color: #7a1028;
        display: block;
        line-height: 1;
        margin-bottom: 4px;
    }
    .stat-label {
        font-size: 11px;
        color: #999;
        text-transform: uppercase;
        letter-spacing: 0.07em;
        font-family: 'Segoe UI', sans-serif;
        font-weight: 500;
    }

    /* CTA banner */
    
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

    /* ── Responsive ── */
    @media (max-width: 600px) {
        .hero-stats { flex-direction: column; width: 100%; }
        .hero-stat { border-right: none; border-bottom: 0.5px solid #e0ddd6; }
        .hero-stat:last-child { border-bottom: none; }
        .hero-title { font-size: 2rem; }
    }
</style>

<!-- Hero -->
<div class="hero">
    <div class="hero-dots"></div>
    <div class="hero-inner">

       

        <h1 class="hero-title">
            Unleash Your Potential at
            <span class="brand">ApexClubVerse</span>
        </h1>

        <p class="hero-desc">
            Join one of Apex College's six prominent clubs. Register for flagship tournaments, submit advisory applications, cast your votes, and shape university culture alongside your peers.
        </p>

        <div class="hero-ctas">
            <a href="clubs.php" class="btn-primary">Browse clubs &rarr;</a>
          
        </div>

        <div class="hero-stats">
            <div class="hero-stat">
                <span class="stat-number">6</span>
                <span class="stat-label">Active clubs</span>
            </div>
            <div class="hero-stat">
                <span class="stat-number">800+</span>
                <span class="stat-label">Members</span>
            </div>
            <div class="hero-stat">
                <span class="stat-number">8+</span>
                <span class="stat-label">Events / year</span>
            </div>
            <div class="hero-stat">
                <span class="stat-number">100%</span>
                <span class="stat-label">Student-led</span>
            </div>
        </div>

    </div>
</div>
</div>
<!-- CTA banner -->

<?php include 'footer.php'; ?>
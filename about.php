<?php
include 'header.php';
?>

<style>
    *, *::before, *::after { box-sizing: border-box; }

    /* ── Hero ── */
    .about-hero {
        background: #7a1028;
        background-image:
            radial-gradient(circle at 15% 20%, rgba(255,255,255,0.06) 0%, transparent 40%),
            radial-gradient(circle at 85% 80%, rgba(0,0,0,0.15) 0%, transparent 40%);
        padding: 3.5rem 2rem;
        text-align: center;
        position: relative;
        overflow: hidden;
    }
    .about-hero::before {
        content: '';
        position: absolute;
        width: 350px; height: 350px;
        border-radius: 50%;
        background: rgba(255,255,255,0.04);
        top: -80px; right: -80px;
        pointer-events: none;
    }
    .about-hero::after {
        content: '';
        position: absolute;
        width: 250px; height: 250px;
        border-radius: 50%;
        background: rgba(0,0,0,0.1);
        bottom: -60px; left: -60px;
        pointer-events: none;
    }
    .about-hero-inner {
        position: relative; z-index: 2;
        max-width: 650px; margin: 0 auto;
    }
    .about-badge {
        display: inline-block;
        background: rgba(255,255,255,0.12);
        border: 0.5px solid rgba(255,255,255,0.25);
        border-radius: 20px;
        padding: 5px 16px;
        font-size: 11px; font-weight: 700;
        letter-spacing: 0.08em; text-transform: uppercase;
        color: rgba(255,255,255,0.85);
        font-family: 'Segoe UI', sans-serif;
        margin-bottom: 1rem;
    }
    .about-hero h1 {
        font-size: 2.4rem; font-weight: 700;
        color: #fff; margin-bottom: 0.6rem;
    }
    .about-hero p {
        color: rgba(255,255,255,0.65);
        font-size: 14px; line-height: 1.7;
        font-family: 'Segoe UI', sans-serif;
    }

    /* ── Content ── */
    .about-section {
        background: #f5f3ef;
        padding: 3rem 2rem 4rem;
    }
    .about-inner {
        max-width: 860px;
        margin: 0 auto;
    }

    /* Info cards */
    .info-card {
        background: #fff;
        border: 0.5px solid #e0ddd6;
        border-radius: 14px;
        padding: 1.75rem 2rem;
        margin-bottom: 1.1rem;
        border-left: 4px solid #7a1028;
        transition: transform 0.18s, box-shadow 0.18s;
    }
    .info-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.08);
    }
    .info-card:nth-child(2) { border-left-color: #1a5f9a; }
    .info-card:nth-child(3) { border-left-color: #1a7a4a; }
    .info-card:nth-child(4) { border-left-color: #6d3a9c; }

    .info-card h2 {
        font-size: 1rem; font-weight: 700;
        margin-bottom: 0.6rem; color: #7a1028;
    }
    .info-card:nth-child(2) h2 { color: #1a5f9a; }
    .info-card:nth-child(3) h2 { color: #1a7a4a; }
    .info-card:nth-child(4) h2 { color: #6d3a9c; }

    .info-card p {
        font-family: 'Segoe UI', sans-serif;
        font-size: 14px; color: #555;
        line-height: 1.75; margin: 0;
    }

    /* Clubs */
    .section-label {
        font-size: 11px; font-weight: 700;
        color: #bbb; text-transform: uppercase;
        letter-spacing: 0.1em;
        font-family: 'Segoe UI', sans-serif;
        margin: 2rem 0 1rem;
    }
    .clubs-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 0.85rem;
    }
    .club-item {
        background: #fff;
        border: 0.5px solid #e0ddd6;
        border-radius: 10px;
        padding: 1rem 1.25rem;
        transition: transform 0.15s, box-shadow 0.15s;
    }
    .club-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0,0,0,0.07);
    }
    .club-item h3 {
        font-size: 13px; font-weight: 600;
        color: #1a1a1a; line-height: 1.4;
        font-family: 'Segoe UI', sans-serif;
    }

    @media (max-width: 600px) {
        .about-hero h1 { font-size: 1.9rem; }
        .about-section { padding: 2rem 1rem 3rem; }
    }
</style>

<!-- Hero -->
<div class="about-hero">
    <div class="about-hero-inner">
        <div class="about-badge">Apex College Portal</div>
        <h1>About ApexClubVerse</h1>
        <p>A centralized platform connecting Apex College students with all six campus clubs.</p>
    </div>
</div>

<!-- Content -->
<div class="about-section">
    <div class="about-inner">

        <div class="info-card">
            <h2>What is ApexClubVerse?</h2>
            <p>ApexClubVerse is a unified online portal designed to bring all Apex College clubs together in one place. Instead of searching across social media, WhatsApp groups, and paper forms, students can find everything they need about campus clubs right here — from club details and events to voting and intake applications.</p>
        </div>

        <div class="info-card">
            <h2>Our Mission</h2>
            <p>Our mission is to make student life at Apex College more organized and connected. We aim to promote student engagement, streamline club recruitment, and make club activities transparent and accessible to every student on campus.</p>
        </div>

        <div class="info-card">
            <h2>Interactive Voting</h2>
            <p>One of the key features of ApexClubVerse is its community voting system. Students can vote on which events their clubs should perform next. This helps clubs understand student preferences and plan activities that truly reflect what students want. Each student can vote once per poll using their official Apex College email.</p>
        </div>

        <div class="info-card">
            <h2>Club Intake</h2>
            <p>Students interested in joining a club can submit their application directly through the portal. The intake form collects all necessary information and sends it straight to the club administrators — no paperwork, no hassle.</p>
        </div>

        <div class="section-label">Our 6 Campus Clubs</div>
        <div class="clubs-grid">
            <div class="club-item"><h3>Apex Performing Arts Club</h3></div>
            <div class="club-item"><h3>Apex Sports and Leadership Club</h3></div>
            <div class="club-item"><h3>Apex Travel and Tourism Club</h3></div>
            <div class="club-item"><h3>Apex Media and Marketing Club</h3></div>
            <div class="club-item"><h3>Apex IT Club</h3></div>
            <div class="club-item"><h3>Apex Health Education and Awareness Team (HEAT)</h3></div>
        </div>

    </div>
</div>
</div>

<?php include 'footer.php'; ?>

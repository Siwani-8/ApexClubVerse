<?php include 'header.php'; ?>

<style>
    .hero-section {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 6rem 2rem;
        max-width: 900px;
        margin: 0 auto;
    }
    .badge {
        background-color: #ffdce5;
        color: var(--primary-crimson);
        padding: 0.4rem 1rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: bold;
        letter-spacing: 1px;
        margin-bottom: 2rem;
        font-family: sans-serif;
    }
    .hero-section h1 {
        font-size: 3.5rem;
        color: #2c3539;
        line-height: 1.1;
        margin-bottom: 1rem;
    }
    .hero-section .brand-highlight {
        display: block;
        font-size: 4rem;
        background: linear-gradient(to right, #ff4500, #ff7f50);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-top: 0.5rem;
    }
    .hero-section p {
        font-size: 1.1rem;
        color: #555e6b;
        line-height: 1.6;
        margin-top: 1.5rem;
        font-family: 'Segoe UI', sans-serif;
    }
</style>

<div class="hero-section">
    <div class="badge">✨ DISCOVER CAMPUS LIFE REDEFINED</div>
    <h1>Unleash Your Potential <br>inside <span class="brand-highlight">ApexClubVerse</span></h1>
    <p>Join one of Apex College's six prominent clubs. Register online for flagship tournaments, submit advisory applications, cast your feedback votes, and shape the university culture alongside premier student leaders.</p>
</div>

<?php include 'footer.php'; ?>
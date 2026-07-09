<?php
include 'header.php';
?>

<style>
    *, *::before, *::after { box-sizing: border-box; }

    /* ── Hero ── */
    .contact-hero {
        background: #7a1028;
        background-image:
            radial-gradient(circle at 15% 20%, rgba(255,255,255,0.06) 0%, transparent 40%),
            radial-gradient(circle at 85% 80%, rgba(0,0,0,0.15) 0%, transparent 40%);
        padding: 3.5rem 2rem;
        text-align: center;
        position: relative;
        overflow: hidden;
    }
    .contact-hero::before {
        content: '';
        position: absolute;
        width: 350px; height: 350px;
        border-radius: 50%;
        background: rgba(255,255,255,0.04);
        top: -80px; right: -80px;
        pointer-events: none;
    }
    .contact-hero::after {
        content: '';
        position: absolute;
        width: 250px; height: 250px;
        border-radius: 50%;
        background: rgba(0,0,0,0.1);
        bottom: -60px; left: -60px;
        pointer-events: none;
    }
    .contact-hero-inner {
        position: relative; z-index: 2;
        max-width: 600px; margin: 0 auto;
    }
    .contact-badge {
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
    .contact-hero h1 {
        font-size: 2.4rem; font-weight: 700;
        color: #fff; margin-bottom: 0.6rem;
    }
    .contact-hero p {
        color: rgba(255,255,255,0.65);
        font-size: 14px; line-height: 1.7;
        font-family: 'Segoe UI', sans-serif;
    }

    /* ── Content ── */
    .contact-section {
        background: #f5f3ef;
        padding: 3rem 2rem 4rem;
    }
    .contact-inner {
        max-width: 700px;
        margin: 0 auto;
    }

    /* Info cards */
    .contact-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    .contact-card {
        background: #fff;
        border: 0.5px solid #e0ddd6;
        border-radius: 14px;
        padding: 1.5rem;
        text-align: center;
        transition: transform 0.15s, box-shadow 0.15s;
    }
    .contact-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.08);
    }
    .contact-card-label {
        font-size: 11px; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.08em;
        color: #7a1028;
        font-family: 'Segoe UI', sans-serif;
        margin-bottom: 0.5rem;
    }
    .contact-card p {
        font-family: 'Segoe UI', sans-serif;
        font-size: 14px; color: #444;
        line-height: 1.6; margin: 0;
    }

    /* Description card */
    .desc-card {
        background: #fff;
        border: 0.5px solid #e0ddd6;
        border-left: 4px solid #7a1028;
        border-radius: 14px;
        padding: 1.75rem 2rem;
        margin-bottom: 1.5rem;
    }
    .desc-card h2 {
        font-size: 1rem; font-weight: 700;
        color: #7a1028; margin-bottom: 0.6rem;
    }
    .desc-card p {
        font-family: 'Segoe UI', sans-serif;
        font-size: 14px; color: #555;
        line-height: 1.75; margin: 0;
    }

    .section-label {
        font-size: 11px; font-weight: 700;
        color: #bbb; text-transform: uppercase;
        letter-spacing: 0.1em;
        font-family: 'Segoe UI', sans-serif;
        margin-bottom: 1rem;
    }

    @media (max-width: 600px) {
        .contact-hero h1 { font-size: 1.9rem; }
        .contact-section { padding: 2rem 1rem 3rem; }
    }
</style>

<!-- Hero -->
<div class="contact-hero">
    <div class="contact-hero-inner">
        <div class="contact-badge">Apex College Portal</div>
        <h1>Contact Us</h1>
        <p>Have questions about club registrations, events, or eligibility? We are here to help.</p>
    </div>
</div>

<!-- Content -->
<div class="contact-section">
    <div class="contact-inner">

        <div class="desc-card">
            <h2>Get in Touch</h2>
            <p>Have questions regarding club registrations, eligibility criteria, or event proposals? Drop by the administration block or get in touch through our official channels below. Our student welfare team is always happy to help.</p>
        </div>

        <div class="section-label">Contact Information</div>
        <div class="contact-grid">
            <div class="contact-card">
                <div class="contact-card-label">Location</div>
                <p>Apex College Main Campus, Mid-Baneshwor, Kathmandu</p>
            </div>
            <div class="contact-card">
                <div class="contact-card-label">Email</div>
                <p>info@apexcollege.edu.np</p>
            </div>
            <div class="contact-card">
                <div class="contact-card-label">Phone</div>
                <p>+977-9860390455</p>
            </div>
        </div>

    </div>
</div>
</div>
<?php include 'footer.php'; ?>

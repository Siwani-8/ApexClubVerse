<?php
session_start();
include 'db.php';
include 'header.php';

$club_id = isset($_GET['id']) ? (int)$_GET['id'] : 1;

$club_result = mysqli_query($conn, "SELECT * FROM clubs WHERE id = $club_id");
$club = mysqli_fetch_assoc($club_result);

if (!$club) { header("Location: clubs.php"); exit; }

$bod_result = mysqli_query($conn, "SELECT * FROM bod_members WHERE club_id = $club_id ORDER BY FIELD(position, 'President', 'Vice President', 'Treasurer', 'General Secretary', 'Operations Head')");
$boa_result = mysqli_query($conn, "SELECT * FROM boa_members WHERE club_id = $club_id");

// Club-specific data
$club_data = [
    1 => [
        'tagline' => 'Where Creativity Meets the Stage',
        'about' => 'The Apex Performing Arts Club is a vibrant community dedicated to nurturing artistic talent across theatre, dance, music, and creative expression. We believe the stage is a space for everyone — whether you are a seasoned performer or a first-time explorer. Our club organizes annual productions, open mic nights, inter-college competitions, and workshops led by industry professionals. We aim to build confidence, teamwork, and a deep love for the arts among Apex College students.',
        'events' => ['Annual Drama Night', 'Open Mic Evening', 'Dance Workshop', 'Inter-College Arts Fest'],
        'color' => '#990026',
        'icon' => '🎭'
    ],
    2 => [
        'tagline' => 'Building Leaders Through Sport',
        'about' => 'The Apex Sports and Leadership Club is committed to fostering physical excellence and strong leadership values among students. From organizing inter-department tournaments to leadership boot camps and fitness awareness programs, we create opportunities for students to push their limits and discover their potential. Our club champions the belief that discipline on the field translates to success in every walk of life.',
        'events' => ['Inter-Department Football', 'Leadership Boot Camp', 'Fitness Week', 'Sports Awards Night'],
        'color' => '#1a3a5c',
        'icon' => '⚽'
    ],
    3 => [
        'tagline' => 'Explore. Experience. Discover.',
        'about' => 'The Apex Travel and Tourism Club opens doors to Nepal\'s breathtaking landscapes and rich cultural heritage while broadening students\' global perspective. We organize trekking expeditions, heritage walks, cultural exchange programs, and tourism awareness campaigns. Our members develop practical knowledge in travel management, hospitality, and sustainable tourism while building lifelong memories and friendships along the way.',
        'events' => ['Himalayan Trek', 'Heritage Walk Kathmandu', 'Tourism Awareness Drive', 'Cultural Exchange Program'],
        'color' => '#1a5c3a',
        'icon' => '✈️'
    ],
    4 => [
        'tagline' => 'Create. Communicate. Captivate.',
        'about' => 'The Apex Media and Marketing Club is the creative engine of Apex College. We train students in digital marketing, journalism, photography, videography, and content creation. Through live campaigns, media productions, and marketing challenges, our members gain real-world skills that set them apart in today\'s competitive media landscape. We are storytellers, strategists, and creators united by a passion for impactful communication.',
        'events' => ['Photography Contest', 'Digital Marketing Challenge', 'Media Production Workshop', 'Campus News Show'],
        'color' => '#5c3a1a',
        'icon' => '📸'
    ],
    5 => [
        'tagline' => 'Innovate. Code. Transform.',
        'about' => 'The Apex IT Club is the hub for technology enthusiasts at Apex College. We cultivate a culture of innovation through hackathons, coding bootcamps, cybersecurity workshops, and tech talks by industry experts. Whether you are passionate about web development, artificial intelligence, or ethical hacking, the IT Club provides the platform, mentorship, and community to turn your ideas into reality.',
        'events' => ['Annual Hackathon', 'Cybersecurity Workshop', 'AI & ML Talk', 'Web Dev Bootcamp'],
        'color' => '#2a1a5c',
        'icon' => '💻'
    ],
    6 => [
        'tagline' => 'Empowering Health. Transforming Lives.',
        'about' => 'The Apex Health Education and Awareness Team (HEAT) is dedicated to promoting health literacy, wellness, and preventive care among students and the broader community. We organize blood donation drives, mental health awareness campaigns, free health check-up camps, and nutrition workshops. HEAT believes that a healthy campus is a thriving campus, and every initiative we take is a step toward a healthier, more aware student community.',
        'events' => ['Blood Donation Drive', 'Mental Health Awareness Week', 'Free Health Camp', 'Nutrition Workshop'],
        'color' => '#1a5c50',
        'icon' => '🏥'
    ],
];

$data = $club_data[$club_id] ?? $club_data[1];
?>

<style>
    /* ── Base ── */
    .container { max-width: 1100px; margin: 0 auto; padding: 2rem 2rem 4rem; }

    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 2rem;
        color: var(--accent-orange);
        font-weight: bold;
        font-family: sans-serif;
        font-size: 0.9rem;
        text-decoration: none;
        transition: 0.2s;
    }
    .back-link:hover { gap: 10px; }

    /* ── Hero Banner ── */
    .club-hero {
        position: relative;
        background: var(--text-dark);
        border-radius: 16px;
        padding: 3.5rem 3rem;
        margin-bottom: 3rem;
        overflow: hidden;
        color: white;
    }
    .club-hero::before {
        content: '';
        position: absolute;
        top: -40px; right: -40px;
        width: 280px; height: 280px;
        border-radius: 50%;
        background: <?php echo $data['color']; ?>;
        opacity: 0.15;
    }
    .club-hero::after {
        content: '<?php echo $data['icon']; ?>';
        position: absolute;
        bottom: -10px; right: 3rem;
        font-size: 8rem;
        opacity: 0.08;
        line-height: 1;
    }
    .club-hero-badge {
        display: inline-block;
        background: <?php echo $data['color']; ?>;
        color: white;
        font-size: 0.75rem;
        font-family: sans-serif;
        font-weight: bold;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        padding: 0.3rem 1rem;
        border-radius: 20px;
        margin-bottom: 1rem;
    }
    .club-hero h1 {
        font-size: 2.4rem;
        color: white;
        margin-bottom: 0.4rem;
        line-height: 1.2;
    }
    .club-hero .tagline {
        font-family: sans-serif;
        font-size: 1rem;
        color: rgba(255,255,255,0.6);
        margin-bottom: 1.2rem;
        font-style: italic;
    }
    .club-hero .about-text {
        font-family: 'Segoe UI', sans-serif;
        font-size: 0.97rem;
        color: rgba(255,255,255,0.82);
        line-height: 1.8;
        max-width: 720px;
    }

    /* ── Stats Strip ── */
    .stats-strip {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
        margin-bottom: 3rem;
    }
    .stat-card {
        background: var(--card-bg);
        border: 1px solid #dcdbd7;
        border-radius: 12px;
        padding: 1.5rem;
        text-align: center;
    }
    .stat-card .stat-number {
        font-size: 2rem;
        font-weight: bold;
        color: var(--primary-crimson);
        font-family: sans-serif;
    }
    .stat-card .stat-label {
        font-size: 0.85rem;
        color: #777;
        font-family: sans-serif;
        margin-top: 0.2rem;
    }

    /* ── Section titles ── */
    .section { margin-bottom: 3.5rem; }
    .section-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 2rem;
    }
    .section-header h2 {
        font-size: 1.7rem;
        color: var(--text-dark);
        margin: 0;
    }
    .section-line {
        flex: 1;
        height: 2px;
        background: linear-gradient(to right, var(--primary-crimson), transparent);
        border-radius: 2px;
    }

    /* ── Events Gallery ── */
    .events-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
        gap: 1.2rem;
        margin-bottom: 0.5rem;
    }
    .event-photo-card {
        background: var(--card-bg);
        border: 1px solid #dcdbd7;
        border-radius: 12px;
        overflow: hidden;
        transition: 0.2s;
    }
    .event-photo-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(0,0,0,0.1); }
    .event-photo-placeholder {
        width: 100%;
        height: 160px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 8px;
        background: #f0eeea;
        border-bottom: 1px solid #dcdbd7;
    }
    .event-photo-placeholder .ph-icon { font-size: 2.5rem; opacity: 0.4; }
    .event-photo-placeholder .ph-text {
        font-size: 0.75rem;
        color: #aaa;
        font-family: sans-serif;
        letter-spacing: 0.5px;
    }
    .event-photo-card .event-label {
        padding: 0.8rem 1rem;
        font-family: sans-serif;
        font-size: 0.88rem;
        font-weight: bold;
        color: var(--text-dark);
    }

    /* ── BOD Cards ── */
    .bod-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(190px, 1fr));
        gap: 1.5rem;
    }
    .bod-card {
        background: var(--card-bg);
        border: 1px solid #dcdbd7;
        border-radius: 14px;
        padding: 1.8rem 1.2rem 1.4rem;
        text-align: center;
        transition: 0.2s;
        position: relative;
        overflow: hidden;
    }
    .bod-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 4px;
        background: var(--primary-crimson);
        border-radius: 14px 14px 0 0;
    }
    .bod-card:hover { transform: translateY(-5px); box-shadow: 0 12px 28px rgba(153,0,38,0.12); }

    .bod-avatar {
        width: 75px; height: 75px;
        border-radius: 50%;
        background: var(--primary-crimson);
        color: white;
        font-size: 1.7rem;
        font-weight: bold;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        font-family: sans-serif;
        border: 3px solid #f0e8e8;
    }
    .bod-card h3 {
        font-size: 0.97rem;
        color: var(--text-dark);
        margin-bottom: 0.4rem;
        font-family: sans-serif;
        font-weight: 700;
    }
    .bod-position {
        display: inline-block;
        background: #f0e8e8;
        color: var(--primary-crimson);
        font-size: 0.72rem;
        font-family: sans-serif;
        font-weight: bold;
        padding: 0.25rem 0.8rem;
        border-radius: 20px;
        margin-bottom: 0.8rem;
        letter-spacing: 0.3px;
    }
    .bod-card p {
        font-family: 'Segoe UI', sans-serif;
        font-size: 0.82rem;
        color: #777;
        line-height: 1.5;
    }

    /* ── BOA Cards ── */
    .boa-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 1.5rem;
    }
    .boa-card {
        background: var(--card-bg);
        border: 1px solid #dcdbd7;
        border-radius: 14px;
        padding: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1.2rem;
        transition: 0.2s;
    }
    .boa-card:hover { transform: translateY(-4px); box-shadow: 0 8px 20px rgba(0,0,0,0.08); }
    .boa-avatar {
        width: 65px; height: 65px;
        border-radius: 50%;
        background: var(--text-dark);
        color: white;
        font-size: 1.5rem;
        font-weight: bold;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: sans-serif;
        flex-shrink: 0;
        border: 3px solid #e8eaf0;
    }
    .boa-info h3 {
        font-size: 1rem;
        color: var(--text-dark);
        margin-bottom: 0.3rem;
        font-family: sans-serif;
    }
    .boa-title {
        display: inline-block;
        background: #e8eaf0;
        color: var(--text-dark);
        font-size: 0.72rem;
        font-family: sans-serif;
        font-weight: bold;
        padding: 0.2rem 0.7rem;
        border-radius: 20px;
        margin-bottom: 0.5rem;
    }
    .boa-info p {
        font-family: 'Segoe UI', sans-serif;
        font-size: 0.83rem;
        color: #777;
        line-height: 1.5;
    }

    /* ── CTA ── */
    .cta-box {
        background: var(--text-dark);
        color: white;
        border-radius: 16px;
        padding: 3rem;
        text-align: center;
        position: relative;
        overflow: hidden;
    }
    .cta-box::before {
        content: '';
        position: absolute;
        bottom: -40px; left: -40px;
        width: 200px; height: 200px;
        border-radius: 50%;
        background: <?php echo $data['color']; ?>;
        opacity: 0.2;
    }
    .cta-box h2 { font-size: 1.8rem; margin-bottom: 0.6rem; position: relative; }
    .cta-box p { font-family: sans-serif; font-size: 1rem; opacity: 0.75; margin-bottom: 1.5rem; position: relative; }
    .cta-btn {
        display: inline-block;
        background: var(--primary-crimson);
        color: white;
        padding: 0.85rem 2.5rem;
        border-radius: 30px;
        font-weight: bold;
        font-family: sans-serif;
        text-decoration: none;
        font-size: 0.95rem;
        transition: 0.2s;
        position: relative;
    }
    .cta-btn:hover { background: #73001c; transform: translateY(-2px); }
</style>

<div class="container">
    <a href="clubs.php" class="back-link">&larr; Back to All Clubs</a>

    <!-- Hero -->
    <div class="club-hero">
        <div class="club-hero-badge"><?php echo $data['icon']; ?> Apex College Club</div>
        <h1><?php echo htmlspecialchars($club['name']); ?></h1>
        <div class="tagline"><?php echo $data['tagline']; ?></div>
        <p class="about-text"><?php echo $data['about']; ?></p>
    </div>

    <!-- Stats -->
    <div class="stats-strip">
        <div class="stat-card">
            <div class="stat-number">5</div>
            <div class="stat-label">Executive Members</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">4+</div>
            <div class="stat-label">Events Per Year</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">1</div>
            <div class="stat-label">Faculty Advisor</div>
        </div>
    </div>

    <!-- Events Gallery -->
    <div class="section">
        <div class="section-header">
            <h2>Our Events</h2>
            <div class="section-line"></div>
        </div>
        <div class="events-grid">
            <?php foreach($data['events'] as $event): ?>
            <div class="event-photo-card">
                <div class="event-photo-placeholder">
                    <div class="ph-icon">📷</div>
                    <div class="ph-text">PHOTO COMING SOON</div>
                </div>
                <div class="event-label"><?php echo htmlspecialchars($event); ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- BOD Section -->
    <div class="section">
        <div class="section-header">
            <h2>Board of Directors (BOD)</h2>
            <div class="section-line"></div>
        </div>
        <div class="bod-grid">
            <?php while($member = mysqli_fetch_assoc($bod_result)) {
                $initials = strtoupper(substr($member['name'], 0, 1));
            ?>
            <div class="bod-card">
                <div class="bod-avatar"><?php echo $initials; ?></div>
                <h3><?php echo htmlspecialchars($member['name']); ?></h3>
                <span class="bod-position"><?php echo htmlspecialchars($member['position']); ?></span>
                <p><?php echo htmlspecialchars($member['bio']); ?></p>
            </div>
            <?php } ?>
        </div>
    </div>

    <!-- BOA Section -->
    <div class="section">
        <div class="section-header">
            <h2>Board of Advisors (BOA)</h2>
            <div class="section-line"></div>
        </div>
        <div class="boa-grid">
            <?php while($advisor = mysqli_fetch_assoc($boa_result)) {
                $initials = strtoupper(substr($advisor['name'], 0, 1));
            ?>
            <div class="boa-card">
                <div class="boa-avatar"><?php echo $initials; ?></div>
                <div class="boa-info">
                    <h3><?php echo htmlspecialchars($advisor['name']); ?></h3>
                    <span class="boa-title"><?php echo htmlspecialchars($advisor['title']); ?></span>
                    <p><?php echo htmlspecialchars($advisor['expertise']); ?></p>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>

    <!-- CTA -->
    <?php if(isset($_SESSION['user_logged_in'])): ?>
    <div class="cta-box">
        <h2>Want to join <?php echo htmlspecialchars($club['name']); ?>?</h2>
        <p>Apply for the club interview and become a part of our amazing team.</p>
        <a href="registration.php?club_id=<?php echo $club_id; ?>" class="cta-btn">Apply for Interview &rarr;</a>
    </div>
    <?php else: ?>
    <div class="cta-box">
        <h2>Interested in joining?</h2>
        <p>Sign in with your Apex College email to apply for a club interview.</p>
        <a href="login.php" class="cta-btn">Sign In to Apply &rarr;</a>
    </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
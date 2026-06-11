<nav class="navbar">
    <div class="brand-section">
        <div class="logo-box">A</div>
        <div class="brand-titles">
            <h2>ApexClubVerse</h2>
            <span>INTERNAL PORTAL</span>
        </div>
    </div>
    <ul class="nav-links">
        <li><a href="clubs-dashboard.php">Our Clubs</a></li>
        <li><a href="vote-events.php">Event Voting</a></li>
        <li><a href="vote-bod.php">BOD Voting</a></li>
        <li><a href="registration.php">Club Intake</a></li>
        <li><a href="about.php">About</a></li>
        <li><a href="contact.php">Contact</a></li>
        <li><a href="logout.php" style="color: #cc0000;">Logout (<?php echo $_SESSION['user_name']; ?>)</a></li>
    </ul>
</nav>

<style>
:root {
    --primary-red: #cc0000;
    --dark-red: #990000;
    --bg-light: #f8fafc;
}
* { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Arial, sans-serif; }
body { background-color: var(--bg-light); color: #333; }

.navbar {
    background-color: #ffffff;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 2rem;
    border-bottom: 3px solid var(--primary-red);
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
}
.brand-section { display: flex; align-items: center; gap: 0.8rem; }
.logo-box { background: var(--primary-red); color: white; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; font-weight: bold; border-radius: 6px; }
.brand-titles h2 { font-size: 1rem; color: #1e293b; font-weight: 700; line-height: 1; }
.brand-titles span { font-size: 0.7rem; color: #64748b; font-weight: 600; }

.nav-links { list-style: none; display: flex; gap: 1.2rem; align-items: center; }
.nav-links a { text-decoration: none; color: #475569; font-weight: 600; font-size: 0.9rem; transition: color 0.15s; }
.nav-links a:hover { color: var(--primary-red); }
</style>
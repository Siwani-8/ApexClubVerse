<?php
session_start();
include 'db.php';
include 'header.php';

$events = mysqli_query($conn, "
    SELECT e.*, c.name as club_name 
    FROM events e 
    JOIN clubs c ON e.club_id = c.id 
    ORDER BY e.event_date ASC
");
?>

<style>
    .container { max-width: 900px; margin: 3rem auto; padding: 0 2rem; }
    .page-title { font-size: 2.5rem; margin-bottom: 2rem; color: var(--text-dark); }

    .feed-card { background: #ffffff; border-radius: 10px; margin-bottom: 2rem; box-shadow: 0 2px 10px rgba(0,0,0,0.05); overflow: hidden; display: flex; border-left: 5px solid var(--primary-crimson); }
    .event-img-box { width: 220px; min-height: 100%; background: #f0eeea; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px; color: #aaa; font-family: sans-serif; font-size: 0.8rem; flex-shrink: 0; }
    .event-img-box .ph-icon { font-size: 2.5rem; opacity: 0.4; }

    .event-details { padding: 1.8rem; flex: 1; }
    .event-club { font-family: sans-serif; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1px; color: var(--accent-orange); font-weight: bold; }
    .event-title { font-size: 1.5rem; margin: 0.4rem 0; color: var(--text-dark); }
    .event-date { font-family: sans-serif; font-size: 0.85rem; color: #777; margin-bottom: 0.5rem; }
    .event-location { font-family: sans-serif; font-size: 0.85rem; color: #777; margin-bottom: 1rem; }
    .event-desc { font-family: 'Segoe UI', sans-serif; line-height: 1.6; color: #444; font-size: 0.95rem; }

    .event-status { display: inline-block; font-size: 0.72rem; font-family: sans-serif; font-weight: bold; padding: 0.2rem 0.7rem; border-radius: 20px; margin-left: 0.5rem; }
    .status-upcoming { background: #f0e8e8; color: var(--primary-crimson); }
    .status-ongoing  { background: #fff3cd; color: #856404; }
    .status-completed { background: #d4edda; color: #155724; }

    @media(max-width: 600px) {
        .feed-card { flex-direction: column; }
        .event-img-box { width: 100%; height: 120px; }
    }
</style>

<div class="container">
    <h1 class="page-title">Events Feed</h1>

    <?php if(mysqli_num_rows($events) == 0): ?>
        <p style="font-family:sans-serif; color:#aaa; text-align:center; padding:3rem;">No events found.</p>
    <?php endif; ?>

    <?php while($row = mysqli_fetch_assoc($events)): ?>
<div class="feed-card">
    <div class="event-img-box">
        <?php if($row['title'] == 'Blood Donation Drive'): ?>
            <img src="images/blood donation.jpg" style="width:100%; height:100%; object-fit:cover;">
            
            
        
        <?php endif; ?>
         <?php if($row['title'] == 'Summer Cup'): ?>
            <img src="images/football.jpg" style="width:100%; height:100%; object-fit:cover;">
           
        <?php endif; ?>
    </div>
        <div class="event-details">
            <span class="event-club"><?php echo htmlspecialchars($row['club_name']); ?></span>
            <span class="event-status status-<?php echo $row['status']; ?>"><?php echo ucfirst($row['status']); ?></span>
            <h2 class="event-title"><?php echo htmlspecialchars($row['title']); ?></h2>
            <p class="event-date">📅 <?php echo date('d M Y', strtotime($row['event_date'])); ?> &nbsp;|&nbsp; 🕐 <?php echo htmlspecialchars($row['event_time']); ?></p>
            <p class="event-location">📍 <?php echo htmlspecialchars($row['location']); ?></p>
            <p class="event-desc"><?php echo htmlspecialchars($row['description']); ?></p>
        </div>
    </div>
    <?php endwhile; ?>
</div>

<?php include 'footer.php'; ?>
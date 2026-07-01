<?php
session_start();
include 'db.php';
include 'header.php';

$events = mysqli_query($conn, "
    SELECT e.*, c.name AS club_name
    FROM events e
    JOIN clubs c ON e.club_id = c.id
    ORDER BY e.event_date ASC
");
?>

<style>
*, *::before, *::after{
    box-sizing:border-box;
}

.events-page{
    min-height:100vh;
    background:#7a1028;
    background-image:
        radial-gradient(circle at 15% 20%, rgba(255,255,255,.06) 0%, transparent 40%),
        radial-gradient(circle at 85% 80%, rgba(0,0,0,.15) 0%, transparent 40%);
    padding:3rem 1.5rem 4rem;
}

.events-inner{
    max-width:900px;
    margin:auto;
}

.page-header{
    margin-bottom:2rem;
}

.page-header-eyebrow{
    display:inline-block;
    background:rgba(255,255,255,.12);
    color:#fff;
    padding:6px 14px;
    border-radius:20px;
    font-size:11px;
    font-family:Segoe UI,sans-serif;
    margin-bottom:10px;
}

.page-title{
    color:#fff;
    font-size:2.2rem;
    margin:0;
}

.page-subtitle{
    color:rgba(255,255,255,.7);
    font-family:Segoe UI,sans-serif;
    margin-top:8px;
}

.feed-card{
    display:flex;
    background:#fff;
    border-radius:14px;
    overflow:hidden;
    margin-bottom:22px;
    box-shadow:0 5px 18px rgba(0,0,0,.15);
}

.feed-card.status-upcoming{
    border-left:5px solid #7a1028;
}

.feed-card.status-ongoing{
    border-left:5px solid #d39d00;
}

.feed-card.status-completed{
    border-left:5px solid #18884f;
}

.event-img-box{
    width:220px;
    min-height:180px;
    background:#f3f3f3;
    display:flex;
    justify-content:center;
    align-items:center;
    overflow:hidden;
}

.event-img-box img{
    width:100%;
    height:100%;
    object-fit:cover;
}

.placeholder{
    color:#aaa;
    text-align:center;
    font-family:Segoe UI,sans-serif;
}

.event-details{
    flex:1;
    padding:22px;
}

.event-top{
    display:flex;
    gap:10px;
    align-items:center;
    margin-bottom:8px;
    flex-wrap:wrap;
}

.event-club{
    font-size:11px;
    color:#7a1028;
    font-weight:bold;
    text-transform:uppercase;
    font-family:Segoe UI,sans-serif;
}

.event-status{
    padding:4px 10px;
    border-radius:20px;
    font-size:11px;
    font-family:Segoe UI,sans-serif;
}

.status-upcoming{
    background:#fdeaea;
    color:#7a1028;
}

.status-ongoing{
    background:#fff4d6;
    color:#946000;
}

.status-completed{
    background:#e8f8ef;
    color:#18884f;
}

.event-title{
    margin:8px 0;
    font-size:22px;
}

.event-date,
.event-location{
    color:#666;
    font-size:13px;
    margin:5px 0;
    font-family:Segoe UI,sans-serif;
}

.event-desc{
    margin-top:10px;
    color:#555;
    line-height:1.6;
    font-family:Segoe UI,sans-serif;
}

.empty-state{
    text-align:center;
    color:#fff;
    padding:50px;
    font-size:18px;
}

@media(max-width:700px){

.feed-card{
    flex-direction:column;
}

.event-img-box{
    width:100%;
    height:220px;
}
}
</style>

<div class="events-page">

<div class="events-inner">

<div class="page-header">
<div class="page-header-eyebrow">
🎓 Apex College
</div>

<h1 class="page-title">
Events Feed
</h1>

<p class="page-subtitle">
All upcoming, ongoing, and past events across every campus club.
</p>
</div>

<?php if(mysqli_num_rows($events)==0): ?>

<div class="empty-state">
No events found. Check back soon!
</div>

<?php endif; ?>

<?php while($row=mysqli_fetch_assoc($events)): ?>

<div class="feed-card status-<?php echo htmlspecialchars($row['status']); ?>">

<div class="event-img-box">

<?php
if($row['title']=="Blood Donation Drive"){
?>
<img src="images/blood donation.jpg" alt="Blood Donation Drive">

<?php
}
elseif($row['title']=="Summer Cup"){
?>
<img src="images/football.jpg" alt="Summer Cup">

<?php
}
else{
?>

<div class="placeholder">
📷<br>
Photo Coming Soon
</div>

<?php
}
?>

</div>

<div class="event-details">

<div class="event-top">

<span class="event-club">
<?php echo htmlspecialchars($row['club_name']); ?>
</span>

<span class="event-status status-<?php echo htmlspecialchars($row['status']); ?>">
<?php echo ucfirst($row['status']); ?>
</span>

</div>

<h2 class="event-title">
<?php echo htmlspecialchars($row['title']); ?>
</h2>

<p class="event-date">
📅 <?php echo date("d M Y",strtotime($row['event_date'])); ?>
&nbsp; | &nbsp;
🕐 <?php echo htmlspecialchars($row['event_time']); ?>
</p>

<p class="event-location">
📍 <?php echo htmlspecialchars($row['location']); ?>
</p>

<p class="event-desc">
<?php echo htmlspecialchars($row['description']); ?>
</p>

</div>

</div>

<?php endwhile; ?>

</div>

</div>

<?php include 'footer.php'; ?>
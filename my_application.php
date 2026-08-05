<?php
session_start();
require_once __DIR__ . '/includes/config.php';

if (!isset($_SESSION['user_logged_in'])) {
    redirect('login.php');
}

include 'includes/db.php';
include 'includes/header.php';

$email = mysqli_real_escape_string($conn, $_SESSION['user_email']);

$applications = mysqli_query($conn,
"SELECT *
FROM registrations
WHERE student_email='$email'
ORDER BY applied_at DESC");
?>

<style>
*, *::before, *::after{
    box-sizing:border-box;
}

.application-page{
    flex:1 0 auto;
    background:#f5f3ef;
    padding:3rem 1.5rem;
}

.application-container{
    max-width:1000px;
    margin:0 auto;
}

.page-header{
    background:#fff;
    border:0.5px solid #e0ddd6;
    border-radius:14px;
    padding:24px 30px;
    margin-bottom:25px;
}

.page-header h1{
    margin:0;
    font-size:28px;
    color:#1a1a1a;
}

.page-header p{
    margin-top:8px;
    color:#888;
    font-family:'Segoe UI',sans-serif;
    font-size:14px;
}

.table-card{
    background:#fff;
    border:0.5px solid #e0ddd6;
    border-radius:14px;
    overflow:hidden;
    box-shadow:0 8px 25px rgba(0,0,0,.04);
}

.table-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    flex-wrap:wrap;
    gap:8px;
    padding:18px 24px;
    border-bottom:1px solid #ece8e2;
}

.table-header h2{
    margin:0;
    font-size:18px;
    color:#222;
}

.badge-count{
    background:#fdecea;
    color:#7a1028;
    padding:5px 12px;
    border-radius:20px;
    font-size:12px;
    font-weight:700;
}

table{
    width:100%;
    border-collapse:collapse;
}

th{
    background:#faf9f7;
    padding:14px;
    text-align:left;
    font-size:12px;
    text-transform:uppercase;
    color:#888;
    letter-spacing:.05em;
}

td{
    padding:16px 14px;
    border-top:1px solid #f0ede7;
    font-family:'Segoe UI',sans-serif;
    font-size:14px;
}

tr:hover td{
    background:#fcfbfa;
}

.badge{
    display:inline-block;
    padding:5px 12px;
    border-radius:20px;
    font-size:12px;
    font-weight:600;
}

.pending{
    background:#fff3cd;
    color:#856404;
}

.accepted{
    background:#e8f6ee;
    color:#1a7a4a;
}

.rejected{
    background:#fdecea;
    color:#7a1028;
}

.interview-card{
    line-height:1.6;
}

.interview-card strong{
    color:#7a1028;
}

.empty-box{
    padding:60px;
    text-align:center;
    color:#999;
    font-family:'Segoe UI',sans-serif;
}

@media(max-width:850px){

.application-page{
padding:2rem 1.15rem;
}

.page-header{
padding:20px 18px;
}

.page-header h1{
font-size:1.6rem;
word-break:break-word;
}

table,
thead,
tbody,
th,
td,
tr{
display:block;
}

thead{
display:none;
}

tr{
margin:18px 0;
border:1px solid #ece8e2;
border-radius:12px;
overflow:hidden;
}

td{
padding:12px 14px;
position:relative;
word-break:break-word;
}

td:before{
position:static;
display:block;
margin-bottom:6px;
font-weight:bold;
font-size:11px;
color:#888;
text-transform:uppercase;
letter-spacing:.04em;
max-width:none;
}

td:nth-child(1):before{content:"Club";}
td:nth-child(2):before{content:"Application";}
td:nth-child(3):before{content:"Interview";}
td:nth-child(4):before{content:"Status";}
}

@media(max-width:480px){
.application-page{padding:1.5rem 1rem;}
.page-header{padding:18px 16px;}
.page-header h1{font-size:1.4rem;}
.table-header{padding:14px 16px;}
.empty-box{padding:32px 16px;}
td{font-size:13px;}
}
</style>

<div class="application-page">
    <div class="application-container">

<div class="page-header">
    <h1>📄 My Applications</h1>
    <p>
        Track your club applications, interview schedules and application status.
    </p>
</div>

<?php if(mysqli_num_rows($applications)==0): ?>

<div class="table-card">
    <div class="empty-box">
        <h3>No Applications Yet</h3>
        <p>You haven't applied to any clubs.</p>
    </div>
</div>

<?php else: ?>

    <div class="table-card">

<div class="table-header">
    <h2>Your Applications</h2>

    <span class="badge-count">
        <?php echo mysqli_num_rows($applications); ?> Applications
    </span>
</div>
<table border="1" cellpadding="10" cellspacing="0" width="100%">
<tr>
    <th>Club</th>
    <th>Application Status</th>
    <th>Interview Schedule</th>
    <th>Interview Status</th>
</tr>

<?php while($row=mysqli_fetch_assoc($applications)): ?>

<tr>

<td><?php echo htmlspecialchars($row['selected_club']); ?></td>

<td><span class="badge <?php echo strtolower($row['application_status']); ?>">
    <?php echo htmlspecialchars($row['application_status']); ?>
</span></td>

<td>

<div class="interview-card">

<?php

if($row['interview_status']=="PENDING"){

    echo "<span style='color:#999;'>Not Scheduled</span>";

}else{

    echo "<strong>📅 "
        .date("d M Y",strtotime($row['interview_date']))
        ."</strong><br>";

    echo "🕒 "
        .date("h:i A",strtotime($row['interview_start_time']))
        ." - "
        .date("h:i A",strtotime($row['interview_end_time']))
        ."<br>";

    echo "📍 "
        .htmlspecialchars($row['interview_venue']);

}

?>

</div>

</td>

<td>

<?php

if($row['interview_status']=="SCHEDULED"){
    echo "<span style='color:green;font-weight:bold;'>Scheduled</span>";
}
elseif($row['interview_status']=="RESCHEDULED"){
    echo "<span style='color:orange;font-weight:bold;'>Rescheduled</span>";
}
else{
    echo "<span style='color:#777;'>Pending</span>";
}

?>

</td>

</tr>

<?php endwhile; ?>

</table>
</div>
<?php endif; ?>

</div>

<?php include 'includes/footer.php'; ?>
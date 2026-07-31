<?php
session_start();

if (!isset($_SESSION['user_logged_in'])) {
    header("Location: login.php");
    exit;
}

include 'db.php';
include 'header.php';

$email = mysqli_real_escape_string($conn, $_SESSION['user_email']);

$applications = mysqli_query($conn,
"SELECT *
FROM registrations
WHERE student_email='$email'
ORDER BY applied_at DESC");
?>

<div style="max-width:900px;margin:40px auto;padding:20px;">

<h2>My Applications</h2>

<?php if(mysqli_num_rows($applications)==0): ?>

<p>You haven't applied to any clubs yet.</p>

<?php else: ?>

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

<td><?php echo htmlspecialchars($row['application_status']); ?></td>

<td>

<?php

if($row['interview_status']=="PENDING"){
    echo "Not Scheduled";
}else{

    echo date("d M Y",strtotime($row['interview_date']));
    echo "<br>";
    echo date("h:i A",strtotime($row['interview_start_time']));
    echo " - ";
    echo date("h:i A",strtotime($row['interview_end_time']));
    echo "<br>";
    echo htmlspecialchars($row['interview_venue']);

}

?>

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

<?php endif; ?>

</div>

<?php include 'footer.php'; ?>
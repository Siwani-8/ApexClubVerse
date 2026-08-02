<?php
include 'header.php';
include 'db.php';

if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

if (isset($_POST['update_status'])) {
    $reg_id = (int)$_POST['reg_id'];
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    mysqli_query($conn, "UPDATE registrations SET application_status = '$status' WHERE id = $reg_id");
    header("Location: admin.php?tab=registrations");
    exit;
}

if (isset($_POST['delete_event'])) {

    $club = mysqli_real_escape_string($conn, $_SESSION['club_name']);

    $clubRow = mysqli_fetch_assoc(
        mysqli_query($conn,
        "SELECT id FROM clubs WHERE name='$club'")
    );

    $club_id = $clubRow['id'];

    $event_id = (int)$_POST['event_id'];

    mysqli_query($conn,
        "DELETE FROM events
        WHERE id=$event_id
        AND club_id=$club_id");

    header("Location: admin.php?tab=events");
    exit;
}

if (isset($_POST['add_event'])) {
    $club = mysqli_real_escape_string($conn, $_SESSION['club_name']);

$clubRow = mysqli_fetch_assoc(
mysqli_query($conn,
"SELECT id
FROM clubs
WHERE name='$club'")
);

$club_id = $clubRow['id'];
    $title     = mysqli_real_escape_string($conn, $_POST['title']);
    $desc      = mysqli_real_escape_string($conn, $_POST['description']);
    $date      = mysqli_real_escape_string($conn, $_POST['event_date']);
    $time      = mysqli_real_escape_string($conn, $_POST['event_time']);
    $location  = mysqli_real_escape_string($conn, $_POST['location']);
    $status    = mysqli_real_escape_string($conn, $_POST['status']);
    $image = "";

if(isset($_FILES['event_image']) && $_FILES['event_image']['error']==0){

    if(!is_dir("uploads/events")){
        mkdir("uploads/events",0777,true);
    }

    $filename = time() . "_" . basename($_FILES['event_image']['name']);

    move_uploaded_file(
        $_FILES['event_image']['tmp_name'],
        "uploads/events/".$filename
    );

    $image = "uploads/events/".$filename;
}
    $event_id = (int)($_POST['event_id'] ?? 0);

if($event_id > 0){

    if($image==""){

        mysqli_query($conn,"
        UPDATE events
        SET
        title='$title',
        description='$desc',
        event_date='$date',
        event_time='$time',
        location='$location',
        status='$status'
        WHERE id=$event_id
        ");

    }else{

        mysqli_query($conn,"
        UPDATE events
        SET
        title='$title',
        description='$desc',
        event_date='$date',
        event_time='$time',
        location='$location',
        image='$image',
        status='$status'
        WHERE id=$event_id
        ");

    }

}else{

    mysqli_query($conn,"
    INSERT INTO events
    (
    club_id,
    title,
    description,
    event_date,
    event_time,
    location,
    image,
    status
    )
    VALUES
    (
    $club_id,
    '$title',
    '$desc',
    '$date',
    '$time',
    '$location',
    '$image',
    '$status'
    )");

}
    header("Location: admin.php?tab=events");
    exit;
}

if (isset($_POST['schedule_interviews'])) {
    $club = mysqli_real_escape_string($conn, $_SESSION['club_name']);
    $date = $_POST['interview_date'];
    $start = $_POST['start_time'];
    $end = $_POST['end_time'];
    $venue = mysqli_real_escape_string($conn, $_POST['venue']);

    if (strtotime($start) >= strtotime($end)) {
        header("Location: admin.php?tab=registrations&error=time");
        exit;
    }


    $check = mysqli_query($conn, "SELECT COUNT(*) AS total FROM registrations WHERE selected_club='$club' AND interview_status <> 'PENDING'");
    $already = mysqli_fetch_assoc($check);

    if ($already['total'] > 0) {
        mysqli_query($conn, "UPDATE registrations SET interview_date='$date', interview_start_time='$start', interview_end_time='$end', interview_venue='$venue', is_rescheduled=1, interview_status='RESCHEDULED' WHERE selected_club='$club'");
    } else {
        mysqli_query($conn, "UPDATE registrations SET interview_date='$date', interview_start_time='$start', interview_end_time='$end', interview_venue='$venue', interview_status='SCHEDULED' WHERE selected_club='$club'");
    }

    header("Location: admin.php?tab=registrations");
    exit;
}

if(isset($_POST['create_poll'])){

    $club = mysqli_real_escape_string($conn, $_SESSION['club_name']);

    $clubRow = mysqli_fetch_assoc(
        mysqli_query($conn,
        "SELECT id FROM clubs WHERE name='$club'")
    );

    $club_id = $clubRow['id'];

    $question = mysqli_real_escape_string($conn,$_POST['question']);

    mysqli_query($conn,
"UPDATE polls
SET is_active=0
WHERE club_id=$club_id");

mysqli_query($conn,
"INSERT INTO polls
(club_id,question,is_active)
VALUES
($club_id,'$question',1)");

    $poll_id = mysqli_insert_id($conn);

    foreach($_POST['options'] as $option){

        $option = trim($option);

        if($option!=""){

            $option = mysqli_real_escape_string($conn,$option);

            mysqli_query($conn,
            "INSERT INTO poll_options
            (poll_id,option_text,votes)
            VALUES
            ($poll_id,'$option',0)");
        }
    }

    header("Location: admin.php?tab=votes");
    exit;
}
if(isset($_POST['delete_poll'])){

    $poll_id = (int)$_POST['poll_id'];

    mysqli_query($conn,
    "DELETE FROM poll_options
     WHERE poll_id=$poll_id");

    mysqli_query($conn,
    "DELETE FROM polls
     WHERE id=$poll_id");

    header("Location: admin.php?tab=votes");
    exit;
}
$tab = $_GET['tab'] ?? 'dashboard';
$applications_only = isset($_GET['applications_only']);
if ($applications_only) {
    $tab = 'registrations';
}

$club = mysqli_real_escape_string($conn, $_SESSION['club_name']);

$total_users    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM users WHERE role = 'student'"))['c'];
$total_regs = mysqli_fetch_assoc(
mysqli_query($conn,
"SELECT COUNT(*) as c
FROM registrations
WHERE selected_club='$club'")
)['c'];
$total_events = mysqli_fetch_assoc(
    mysqli_query($conn,
    "SELECT COUNT(*) as c
     FROM events e
     JOIN clubs c ON e.club_id=c.id
     WHERE c.name='$club'")
)['c'];
$total_votes = mysqli_fetch_assoc(
    mysqli_query($conn,
    "SELECT SUM(po.votes) as c
     FROM poll_options po
     JOIN polls p ON po.poll_id=p.id
     JOIN clubs c ON p.club_id=c.id
     WHERE c.name='$club'")
)['c'] ?? 0;
$pending_regs = mysqli_fetch_assoc(
mysqli_query($conn,
"SELECT COUNT(*) as c
FROM registrations
WHERE application_status='Pending'
AND selected_club='$club'")
)['c'];

$clubs = mysqli_query($conn, "SELECT * FROM clubs ORDER BY id");
$edit_event = null;

if(isset($_GET['edit'])){

    $id = (int)$_GET['edit'];

    $result = mysqli_query($conn,
    "SELECT *
    FROM events
    WHERE id=$id");

    $edit_event = mysqli_fetch_assoc($result);

}
?>

<style>
    *, *::before, *::after { box-sizing: border-box; }
    .admin-page { min-height: 100vh; background: #f5f3ef; padding: 2rem; }
    .admin-inner { max-width: 1100px; margin: 0 auto; }
    .admin-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 2rem; }
    .admin-header h1 { font-size: 1.6rem; font-weight: 700; color: #1a1a1a; }
    .admin-header span { font-family: 'Segoe UI', sans-serif; font-size: 13px; color: #999; }
    .stats-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
    .stat-box { background: #fff; border: 0.5px solid #e0ddd6; border-radius: 12px; padding: 1.25rem 1.5rem; display: flex; align-items: center; gap: 1rem; }
    .stat-icon { font-size: 1.8rem; width: 48px; height: 48px; border-radius: 10px; display: flex; align-items: center; justify-content: center; }
    .stat-val { font-size: 1.6rem; font-weight: 700; color: #1a1a1a; display: block; }
    .stat-label { font-size: 11px; color: #999; font-family: 'Segoe UI', sans-serif; text-transform: uppercase; letter-spacing: 0.05em; }
    .tab-row { display: flex; gap: 6px; margin-bottom: 1.5rem; flex-wrap: wrap; }
    .tab-btn { padding: 8px 16px; border-radius: 8px; border: 0.5px solid #ddd; background: #fff; font-family: 'Segoe UI', sans-serif; font-size: 13px; font-weight: 600; color: #555; text-decoration: none; transition: all 0.15s; }
    .tab-btn:hover { border-color: #7a1028; color: #7a1028; }
    .tab-btn.active { background: #7a1028; color: #fff; border-color: #7a1028; }
    .table-box { background: #fff; border: 0.5px solid #e0ddd6; border-radius: 12px; overflow: hidden; }
    .table-box-header { padding: 1rem 1.5rem; border-bottom: 0.5px solid #e0ddd6; display: flex; align-items: center; justify-content: space-between; }
    .table-box-header h2 { font-size: 15px; font-weight: 600; color: #1a1a1a; }
    .badge-count { background: #fdecea; color: #7a1028; font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 20px; font-family: 'Segoe UI', sans-serif; }
    table { width: 100%; border-collapse: collapse; }
    th { background: #f9f8f5; padding: 10px 14px; font-family: 'Segoe UI', sans-serif; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #999; text-align: left; border-bottom: 0.5px solid #e0ddd6; }
    td { padding: 10px 14px; font-family: 'Segoe UI', sans-serif; font-size: 13px; color: #333; border-bottom: 0.5px solid #f0ede7; }
    tr:last-child td { border-bottom: none; }
    tr:hover td { background: #fafaf9; }
    .badge { display: inline-block; font-size: 11px; font-weight: 600; padding: 3px 10px; border-radius: 20px; font-family: 'Segoe UI', sans-serif; }
    .badge-pending  { background: #fff3cd; color: #856404; }
    .badge-accepted { background: #e8f6ee; color: #1a7a4a; }
    .badge-rejected { background: #fdecea; color: #7a1028; }
    .badge-upcoming  { background: #fdecea; color: #7a1028; }
    .badge-ongoing   { background: #fff3cd; color: #856404; }
    .badge-completed { background: #e8f6ee; color: #1a7a4a; }
    .form-box { background: #fff; border: 0.5px solid #e0ddd6; border-radius: 12px; padding: 1.5rem; margin-bottom: 1.5rem; }
    .form-box h2 { font-size: 15px; font-weight: 600; color: #1a1a1a; margin-bottom: 1.25rem; }
    .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; }
    .form-group { display: flex; flex-direction: column; gap: 4px; }
    .form-group label { font-family: 'Segoe UI', sans-serif; font-size: 11px; font-weight: 700; color: #555; text-transform: uppercase; letter-spacing: 0.05em; }
    .form-group input, .form-group select, .form-group textarea { padding: 9px 12px; border: 0.5px solid #ddd; border-radius: 8px; font-size: 13px; font-family: 'Segoe UI', sans-serif; color: #1a1a1a; background: #fafaf9; }
    .form-group input:focus, .form-group select:focus, .form-group textarea:focus { border-color: #7a1028; outline: none; }
    .btn-submit { background: #7a1028; color: #fff; border: none; border-radius: 8px; padding: 10px 22px; font-size: 13px; font-weight: 600; font-family: 'Segoe UI', sans-serif; cursor: pointer; margin-top: 1rem; transition: background 0.15s; }
    .btn-submit:hover { background: #5e0c1e; }
    .btn-delete { background: #fdecea; color: #7a1028; border: 0.5px solid #f5c6cb; border-radius: 6px; padding: 5px 12px; font-size: 12px; font-weight: 600; font-family: 'Segoe UI', sans-serif; cursor: pointer; }
    .btn-delete:hover { background: #7a1028; color: #fff; }
    select.status-select { padding: 4px 8px; border: 0.5px solid #ddd; border-radius: 6px; font-size: 12px; font-family: 'Segoe UI', sans-serif; cursor: pointer; }
    .empty-msg { text-align: center; padding: 2rem; color: #bbb; font-family: 'Segoe UI', sans-serif; font-size: 13px; }
    @media (max-width: 600px) {
        .admin-page { padding: 1rem; }
        .stats-row { grid-template-columns: 1fr 1fr; }
    }
</style>

<div class="admin-page">
    <div class="admin-inner">

        <?php if (!$applications_only): ?>
        <div class="admin-header">
            <h1>&#9881; Admin Panel</h1>
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
        </div>
        <?php endif; ?>

        <?php if (!$applications_only): ?>
        <div class="stats-row">
            <div class="stat-box">
                <div class="stat-icon" style="background:#fdecea;">&#128101;</div>
                <div>
                    <span class="stat-val"><?php echo $total_users; ?></span>
                    <span class="stat-label">Students</span>
                </div>
            </div>
            <div class="stat-box">
                <div class="stat-icon" style="background:#e8f0fb;">&#128203;</div>
                <div>
                    <span class="stat-val"><?php echo $total_regs; ?></span>
                    <span class="stat-label">Applications</span>
                </div>
            </div>
            <div class="stat-box">
                <div class="stat-icon" style="background:#e8f6ee;">&#128197;</div>
                <div>
                    <span class="stat-val"><?php echo $total_events; ?></span>
                    <span class="stat-label">Events</span>
                </div>
            </div>
            <div class="stat-box">
                <div class="stat-icon" style="background:#fef0e8;">&#128313;</div>
                <div>
                    <span class="stat-val"><?php echo $total_votes; ?></span>
                    <span class="stat-label">Total Votes</span>
                </div>
            </div>
            <div class="stat-box">
                <div class="stat-icon" style="background:#fff3cd;">&#9888;</div>
                <div>
                    <span class="stat-val"><?php echo $pending_regs; ?></span>
                    <span class="stat-label">Pending</span>
                </div>
            </div>
        </div>
        <?php endif; ?> 

        <?php if (!$applications_only): ?>
        <div class="tab-row">
            <a href="admin.php?tab=dashboard" class="tab-btn <?php echo $tab=='dashboard' ? 'active':''; ?>">&#127968; Dashboard</a>
            <a href="admin.php?tab=registrations" class="tab-btn <?php echo $tab=='registrations' ? 'active':''; ?>">&#128203; Applications</a>
            <a href="admin.php?tab=events" class="tab-btn <?php echo $tab=='events' ? 'active':''; ?>">&#128197; Events</a>
            <a href="admin.php?tab=votes" class="tab-btn <?php echo $tab=='votes' ? 'active':''; ?>">&#128313; Vote Results</a>
            <a href="admin.php?tab=users" class="tab-btn <?php echo $tab=='users' ? 'active':''; ?>">&#128101; Students</a>
        </div>
        <?php endif; ?>

        <?php if($tab == 'dashboard'): ?>
        <div class="table-box">
            <div class="table-box-header">
                <h2>&#128203; Recent Applications</h2>
                <span class="badge-count"><?php echo $pending_regs; ?> pending</span>
            </div>
            <?php
            $recent = mysqli_query($conn,
            "SELECT *
            FROM registrations
            WHERE selected_club='$club'
            ORDER BY applied_at DESC
            LIMIT 5");
            if(mysqli_num_rows($recent) == 0): ?>
                <div class="empty-msg">No applications yet.</div>
            <?php else: ?>
            <table>
                <tr>
                    <th>Name</th><th>Email</th><th>Club</th><th>Status</th><th>Date</th>
                </tr>
                <?php while($r = mysqli_fetch_assoc($recent)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($r['student_name']); ?></td>
                    <td><?php echo htmlspecialchars($r['student_email']); ?></td>
                    <td><?php echo htmlspecialchars($r['selected_club']); ?></td>
                    <td><span class="badge badge-<?php echo strtolower($r['application_status']); ?>"><?php echo $r['application_status']; ?></span></td>
                    <td><?php echo date('d M Y', strtotime($r['applied_at'])); ?></td>
                </tr>
                <?php endwhile; ?>
            </table>
            <?php endif; ?>
        </div>

        <?php elseif($tab == 'registrations'): ?>
<?php if(isset($_GET['error']) && $_GET['error']=="time"): ?>
<div style="
background:#fdecea;
color:#7a1028;
padding:12px;
margin-bottom:15px;
border-radius:8px;">
End time must be after start time.
</div>
<?php endif; ?>

        <div class="table-box">
            <div class="table-box-header">
                <h2>&#128203; All Club Intake Applications</h2>
                <div style="display:flex;gap:10px;align-items:center;">
                    <span class="badge-count"><?php echo $total_regs; ?> total</span>
                    <button class="btn-submit" type="button" onclick="document.getElementById('scheduleModal').style.display='flex';" style="margin:0;">
                        Schedule Interviews
                    </button>
                </div>
            </div>
            <?php
            $club = mysqli_real_escape_string($conn, $_SESSION['club_name']);
            $regs = mysqli_query($conn,
            "SELECT *
            FROM registrations
            WHERE selected_club='$club'
            ORDER BY applied_at DESC"); 
            if(mysqli_num_rows($regs) == 0): ?>
                <div class="empty-msg">No applications yet.</div>
            <?php else: ?>
            <table>
                <tr>
                    <th>Name</th>
                    <th>Club</th>
                    <th>Status</th>
                    <th>Interview</th>
                    <th>Interview Status</th>
                    <th>Action</th>
                </tr>
                <?php while($r = mysqli_fetch_assoc($regs)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($r['student_name']); ?></td>
                    <td><?php echo htmlspecialchars($r['selected_club']); ?></td>
                    <td>
                        <span class="badge badge-<?php echo strtolower($r['application_status']); ?>">
                            <?php echo $r['application_status']; ?>
                        </span>
                    </td>
                    <td>
                    <?php
                    if($r['interview_status']=="PENDING"){
                        echo "-";
                    }else{
                        echo date("d M Y",strtotime($r['interview_date']));
                        echo "<br>";
                        echo date("h:i A",strtotime($r['interview_start_time']));
                        echo " - ";
                        echo date("h:i A",strtotime($r['interview_end_time']));
                    }
                    ?>
                    </td>
                    <td>
                    <?php
                    if($r['interview_status']=="SCHEDULED"){
                        echo "<span class='badge badge-accepted'>Scheduled</span>";
                    }elseif($r['interview_status']=="RESCHEDULED"){
                        echo "<span class='badge badge-pending'>Rescheduled</span>";
                    }else{
                        echo "<span class='badge badge-pending'>Pending</span>";
                    }
                    ?>
                    </td>
                    <td>
                        <form method="POST" style="display:flex;gap:6px;align-items:center;">
                            <input type="hidden" name="reg_id" value="<?php echo $r['id']; ?>">
                            <select name="status" class="status-select">
                                <option value="Pending" <?php if($r['application_status']=="Pending") echo "selected"; ?>>Pending</option>
                                <option value="Accepted" <?php if($r['application_status']=="Accepted") echo "selected"; ?>>Accepted</option>
                                <option value="Rejected" <?php if($r['application_status']=="Rejected") echo "selected"; ?>>Rejected</option>
                            </select>
                            <button name="update_status" class="btn-submit" style="padding:5px 10px;margin:0;">Save</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
            <?php endif; ?>
        </div>

        <?php elseif($tab == 'events'): ?>
            <?php

$editing = false;

$event = [
    'title' => '',
    'description' => '',
    'event_date' => '',
    'event_time' => '',
    'location' => '',
    'status' => 'upcoming'
];

if(isset($_GET['edit'])){

    $editing = true;

    $id = (int)$_GET['edit'];

    $event = mysqli_fetch_assoc(
        mysqli_query($conn,
        "SELECT *
         FROM events
         WHERE id=$id")
    );

}

?>
        <div class="form-box">
            <h2>
<?php
if($edit_event){
    echo "Edit Event";
}else{
    echo "Add New Event";
}
?>
</h2>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-grid">
                    <div class="form-group">
    <?php
    $clubData = mysqli_fetch_assoc(
    mysqli_query($conn,
    "SELECT id,name
    FROM clubs
    WHERE name='$club'")
    );
    ?>

    <input
    type="hidden"
    name="club_id"
    value="<?php echo $clubData['id']; ?>">

    <input
    type="text"
    value="<?php echo htmlspecialchars($clubData['name']); ?>"
    readonly>
</div>

<!-- ADD THIS -->
<input
type="hidden"
name="event_id"
value="<?php echo $event['id'] ?? ''; ?>">

<div class="form-group">
    <label>Event Title</label>
    <input
    type="text"
    name="title"
    value="<?php echo htmlspecialchars($event['title']); ?>"
    required>
</div>
                    <div class="form-group">
                        <label>Date</label>
                        <input
                        type="date"
                        name="event_date"
                        value="<?php echo $event['event_date']; ?>"
                        required>
                    </div>
                    <div class="form-group">
                        <label>Time</label>
                        <input
                        type="time"
                        name="event_time"
                        value="<?php echo $event['event_time']; ?>"
                        required>
                    </div>
                    <div class="form-group">
                        <label>Location</label>
                        <input
                        type="text"
                        name="location"
                        value="<?php echo htmlspecialchars($event['location']); ?>"
                        required>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status">

                        <option
                        value="upcoming"
                        <?php if(($edit_event['status'] ?? '')=="upcoming") echo "selected"; ?>>
                        Upcoming
                        </option>

                        <option
                        value="ongoing"
                        <?php if(($edit_event['status'] ?? '')=="ongoing") echo "selected"; ?>>
                        Ongoing
                        </option>

                        <option
                        value="completed"
                        <?php if(($edit_event['status'] ?? '')=="completed") echo "selected"; ?>>
                        Completed
                        </option>

                        </select>
                    </div>
                    <div class="form-group" style="grid-column: 1/-1;">
                        <label>Description</label>
                        <textarea
                        name="description"
                        rows="3"><?php echo htmlspecialchars($event['description']); ?></textarea>
                    </div>
                    <div class="form-group" style="grid-column:1/-1;">
    <label>Event Image</label>
    <input type="file" name="event_image" accept="image/*">
</div>
                </div>
                <button
type="submit"
name="add_event"
class="btn-submit">

<?php
if($editing){
    echo "Update Event";
}else{
    echo "Add Event";
}
?>

</button>
            </form>
        </div>

        <div class="table-box">
            <div class="table-box-header">
                <h2>&#128197; All Events</h2>
                <span class="badge-count"><?php echo $total_events; ?> total</span>
            </div>
            <?php
            $events = mysqli_query($conn,
            "SELECT e.*, c.name as club_name
            FROM events e
            JOIN clubs c
            ON e.club_id=c.id
            WHERE c.name='$club'
            ORDER BY e.event_date DESC");
            if(mysqli_num_rows($events) == 0): ?>
                <div class="empty-msg">No events yet.</div>
            <?php else: ?>
            <table>
                <tr>
                    <th>Image</th>
                    <th>Title</th>
                    <th>Club</th>
                    <th>Date</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th>Edit</th>
                    <th>Delete</th>
                </tr>
                <?php while($e = mysqli_fetch_assoc($events)): ?>
                <tr>
                    <td>

<?php
if(!empty($e['image'])){
?>
<img
src="<?php echo htmlspecialchars($e['image']); ?>"
style="width:80px;height:55px;object-fit:cover;border-radius:8px;">
<?php
}else{
    echo "No Image";
}
?>

</td>

<td><?php echo htmlspecialchars($e['title']); ?></td>
                    <td><?php echo htmlspecialchars($e['club_name']); ?></td>
                    <td><?php echo date('d M Y', strtotime($e['event_date'])); ?></td>
                    <td><?php echo htmlspecialchars($e['location']); ?></td>
                    <td><span class="badge badge-<?php echo $e['status']; ?>"><?php echo ucfirst($e['status']); ?></span></td>
                    <td>

<a
href="admin.php?tab=events&edit=<?php echo $e['id']; ?>"
class="btn-submit"
style="padding:6px 12px;margin:0;text-decoration:none;">
Edit
</a>

</td>

<td>

<a
href="admin.php?tab=events&edit=<?php echo $e['id']; ?>"
class="btn-submit"
style="padding:5px 10px;margin-right:5px;text-decoration:none;">
Edit
</a>

<form method="POST" style="display:inline;">
    <input type="hidden" name="event_id" value="<?php echo $e['id']; ?>">
    <button
        type="submit"
        name="delete_event"
        class="btn-delete"
        onclick="return confirm('Delete this event?')">
        Delete
    </button>
</form>

</td>
                </tr>
                <?php endwhile; ?>
            </table>
            <?php endif; ?>
        </div>

        <?php elseif($tab == 'votes'): ?>
        <div class="form-box">
    <h2>&#128221; Create New Poll</h2>

    <form method="POST">

        <div class="form-group">
            <label>Poll Question</label>
            <input
                type="text"
                name="question"
                placeholder="Enter poll question"
                required>
        </div>

        <div class="form-group">
            <label>Option 1</label>
            <input
                type="text"
                name="options[]"
                required>
        </div>

        <div class="form-group">
            <label>Option 2</label>
            <input
                type="text"
                name="options[]"
                required>
        </div>

        <div class="form-group">
            <label>Option 3</label>
            <input
                type="text"
                name="options[]">
        </div>

        <div class="form-group">
            <label>Option 4</label>
            <input
                type="text"
                name="options[]">
        </div>

        <button
            type="submit"
            name="create_poll"
            class="btn-submit">
            Create Poll
        </button>

    </form>
</div>
<?php
        $polls = mysqli_query($conn,
        "SELECT p.*, c.name as club_name
        FROM polls p
        JOIN clubs c
        ON p.club_id=c.id
        WHERE p.is_active=1
        AND c.name='$club'");
        while($poll = mysqli_fetch_assoc($polls)):
            $total_poll_votes = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(votes) as t FROM poll_options WHERE poll_id = " . $poll['id']))['t'] ?? 0;
            $options = mysqli_query($conn, "SELECT * FROM poll_options WHERE poll_id = " . $poll['id'] . " ORDER BY votes DESC");
        ?>
        <div class="table-box" style="margin-bottom:1rem;">
            <div class="table-box-header">

    <h2>
        <?php echo htmlspecialchars($poll['club_name']); ?>
        —
        <?php echo htmlspecialchars($poll['question']); ?>
    </h2>

    <div style="display:flex;gap:10px;align-items:center;">

        <span class="badge-count">
            <?php echo $total_poll_votes; ?> votes
        </span>

        <form method="POST">
            <input type="hidden"
                   name="poll_id"
                   value="<?php echo $poll['id']; ?>">

            <button
                type="submit"
                name="delete_poll"
                class="btn-delete"
                onclick="return confirm('Delete this poll?')">
                Delete Poll
            </button>
        </form>

    </div>

</div>
            <table>
                <tr><th>Option</th><th>Votes</th><th>Percentage</th></tr>
                <?php while($opt = mysqli_fetch_assoc($options)):
                    $pct = $total_poll_votes > 0 ? round(($opt['votes'] / $total_poll_votes) * 100) : 0;
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($opt['option_text']); ?></td>
                    <td><?php echo $opt['votes']; ?></td>
                    <td>
                        <div style="display:flex; align-items:center; gap:8px;">
                            <div style="background:#f0ede7; border-radius:20px; height:8px; width:120px; overflow:hidden;">
                                <div style="background:#7a1028; height:100%; width:<?php echo $pct; ?>%; border-radius:20px;"></div>
                            </div>
                            <span style="font-size:12px; font-weight:700;"><?php echo $pct; ?>%</span>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>
        <?php endwhile; ?>

        <?php elseif($tab == 'users'): ?>
        <div class="table-box">
            <div class="table-box-header">
                <h2>&#128101; Registered Students</h2>
                <span class="badge-count"><?php echo $total_users; ?> total</span>
            </div>
            <?php
            $users = mysqli_query($conn,
            "SELECT DISTINCT u.id, u.name, u.email
            FROM users u
            JOIN registrations r
            ON u.email=r.student_email
            WHERE u.role='student'
            AND r.selected_club='$club'
            ORDER BY u.id DESC");
            if(mysqli_num_rows($users) == 0): ?>
                <div class="empty-msg">No students registered yet.</div>
            <?php else: ?>
            <table>
                <tr><th>#</th><th>Full Name</th><th>Email</th></tr>
                <?php $i = 1; while($u = mysqli_fetch_assoc($users)): ?>
                <tr>
                    <td><?php echo $i++; ?></td>
                    <td><?php echo htmlspecialchars($u['name']); ?></td>
                    <td><?php echo htmlspecialchars($u['email']); ?></td>
                </tr>
                <?php endwhile; ?>
            </table>
            <?php endif; ?>
        </div>
        <?php endif; ?>

    </div>
</div>

<div id="scheduleModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); justify-content:center; align-items:center; z-index:9999;">
    <div style="background:#fff; width:450px; max-width:95%; border-radius:12px; padding:24px;">
        <h2 style="margin-top:0;">Schedule Club Interview</h2>
        <form method="POST">
            <div class="form-group">
                <label>Club</label>
                <input
    type="text"
    name="club"
    value="<?php echo htmlspecialchars($_SESSION['club_name']); ?>"
    readonly>
            </div>
            <div class="form-group">
                <label>Interview Date</label>
                <input type="date" name="interview_date" required>
            </div>
            <div class="form-group">
                <label>Start Time</label>
                <input type="time" name="start_time" required>
            </div>
            <div class="form-group">
                <label>End Time</label>
                <input type="time" name="end_time" required>
            </div>
            <div class="form-group">
                <label>Venue</label>
                <input type="text" name="venue" required>
            </div>
            <div style="display:flex;gap:10px;margin-top:20px;">
                <button class="btn-submit" name="schedule_interviews">Save Schedule</button>
                <button type="button" class="btn-delete" onclick="document.getElementById('scheduleModal').style.display='none';">Cancel</button>
            </div>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>
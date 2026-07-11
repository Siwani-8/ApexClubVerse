<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db.php';
include 'club_admin_helpers.php';

$is_club_admin = is_club_admin();
$admin_club_id = admin_club_id();
$edit_event = null;

if ($is_club_admin) {
    if (isset($_POST['add_event'])) {
        $title    = mysqli_real_escape_string($conn, $_POST['title']);
        $desc     = mysqli_real_escape_string($conn, $_POST['description']);
        $date     = mysqli_real_escape_string($conn, $_POST['event_date']);
        $time     = mysqli_real_escape_string($conn, $_POST['event_time']);
        $location = mysqli_real_escape_string($conn, $_POST['location']);
        $status   = mysqli_real_escape_string($conn, $_POST['status']);
        $image = "";

if(!empty($_FILES['event_image']['name'])){

    $image = time() . "_" . basename($_FILES['event_image']['name']);

    move_uploaded_file(
        $_FILES['event_image']['tmp_name'],
        "images/" . $image
    );
}
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
$admin_club_id,
'$title',
'$desc',
'$date',
'$time',
'$location',
'$image',
'$status'
)");
        header('Location: events.php');
        exit;
    }

    if (isset($_POST['update_event'])) {
        $event_id = (int)$_POST['event_id'];
        if (event_belongs_to_club($conn, $event_id, $admin_club_id)) {
            $title    = mysqli_real_escape_string($conn, $_POST['title']);
            $desc     = mysqli_real_escape_string($conn, $_POST['description']);
            $date     = mysqli_real_escape_string($conn, $_POST['event_date']);
            $time     = mysqli_real_escape_string($conn, $_POST['event_time']);
            $location = mysqli_real_escape_string($conn, $_POST['location']);
            $status   = mysqli_real_escape_string($conn, $_POST['status']);
            $image_sql = "";

if(!empty($_FILES['event_image']['name'])){

    $image = time() . "_" . basename($_FILES['event_image']['name']);

    move_uploaded_file(
        $_FILES['event_image']['tmp_name'],
        "images/" . $image
    );

    $image_sql = ", image='$image'";
}
            mysqli_query($conn,"
UPDATE events SET
title='$title',
description='$desc',
event_date='$date',
event_time='$time',
location='$location'
$image_sql,
status='$status'
WHERE id=$event_id
AND club_id=$admin_club_id
");
        }
        header('Location: events.php');
        exit;
    }

    if (isset($_POST['delete_event'])) {
        $event_id = (int)$_POST['event_id'];
        if (event_belongs_to_club($conn, $event_id, $admin_club_id)) {
            mysqli_query($conn, "DELETE FROM events WHERE id = $event_id AND club_id = $admin_club_id");
        }
        header('Location: events.php');
        exit;
    }

    if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
        $edit_id = (int)$_GET['edit'];
        $edit_res = mysqli_query($conn, "SELECT * FROM events WHERE id = $edit_id AND club_id = $admin_club_id LIMIT 1");
        if ($edit_res && mysqli_num_rows($edit_res) > 0) {
            $edit_event = mysqli_fetch_assoc($edit_res);
        }
    }
}

include 'header.php';

$events = mysqli_query($conn, "
    SELECT e.*, c.name as club_name 
    FROM events e 
    JOIN clubs c ON e.club_id = c.id 
    ORDER BY e.event_date ASC
");

$admin_club_name = '';
if ($is_club_admin) {
    $club_row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT name FROM clubs WHERE id = $admin_club_id LIMIT 1"));
    $admin_club_name = $club_row['name'] ?? '';
}
?>

<style>
    *, *::before, *::after { box-sizing: border-box; }

    /* ── Page background ── */
    .events-page {
        min-height: 100vh;
        background: #7a1028;
        background-image:
            radial-gradient(circle at 15% 20%, rgba(255,255,255,0.06) 0%, transparent 40%),
            radial-gradient(circle at 85% 80%, rgba(0,0,0,0.15) 0%, transparent 40%);
        padding: 3rem 1.5rem 4rem;
        position: relative;
        overflow: hidden;
    }
    .events-page::before {
        content: '';
        position: absolute;
        width: 400px; height: 400px;
        border-radius: 50%;
        background: rgba(255,255,255,0.04);
        top: -100px; right: -100px;
        pointer-events: none;
    }
    .events-page::after {
        content: '';
        position: absolute;
        width: 300px; height: 300px;
        border-radius: 50%;
        background: rgba(0,0,0,0.1);
        bottom: -80px; left: -80px;
        pointer-events: none;
    }

    .events-inner {
        max-width: 880px;
        margin: 0 auto;
        position: relative;
        z-index: 2;
    }

    /* ── Page header ── */
    .page-header { margin-bottom: 2rem; }
    .page-header-eyebrow {
        display: inline-flex; align-items: center; gap: 6px;
        background: rgba(255,255,255,0.12);
        border: 0.5px solid rgba(255,255,255,0.25);
        border-radius: 20px;
        padding: 5px 14px;
        font-size: 11px; font-weight: 700;
        letter-spacing: 0.08em; text-transform: uppercase;
        color: rgba(255,255,255,0.85);
        font-family: 'Segoe UI', sans-serif;
        margin-bottom: 0.75rem;
    }
    .page-title {
        font-size: 2.2rem; font-weight: 700;
        color: #fff; margin-bottom: 0.3rem;
    }
    .page-subtitle {
        color: rgba(255,255,255,0.6);
        font-family: 'Segoe UI', sans-serif;
        font-size: 14px;
    }

    /* ── Admin panel ── */
    .admin-panel {
        background: #fff;
        border-radius: 14px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    }
    .admin-panel h2 {
        font-size: 15px; font-weight: 600;
        color: #1a1a1a; margin-bottom: 1rem;
        font-family: 'Segoe UI', sans-serif;
    }
    .admin-form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 0.85rem;
    }
    .admin-form-grid .full { grid-column: 1 / -1; }
    .admin-form-grid label {
        display: block;
        font-family: 'Segoe UI', sans-serif;
        font-size: 11px; font-weight: 700;
        color: #555; text-transform: uppercase;
        letter-spacing: 0.05em; margin-bottom: 4px;
    }
    .admin-form-grid input,
    .admin-form-grid select,
    .admin-form-grid textarea {
        width: 100%;
        padding: 9px 12px;
        border: 0.5px solid #ddd;
        border-radius: 8px;
        font-size: 13px;
        font-family: 'Segoe UI', sans-serif;
        color: #1a1a1a;
        background: #fafaf9;
    }
    .btn-admin {
        background: #7a1028; color: #fff;
        border: none; border-radius: 8px;
        padding: 9px 18px; font-size: 13px; font-weight: 600;
        font-family: 'Segoe UI', sans-serif; cursor: pointer;
        margin-top: 0.75rem;
    }
    .btn-admin:hover { background: #5e0c1e; }
    .btn-admin-outline {
        background: #fdecea; color: #7a1028;
        border: 0.5px solid #f5c6cb;
        border-radius: 6px; padding: 5px 12px;
        font-size: 12px; font-weight: 600;
        font-family: 'Segoe UI', sans-serif;
        cursor: pointer; text-decoration: none;
        display: inline-block;
    }
    .btn-admin-outline:hover { background: #7a1028; color: #fff; }
    .event-actions {
        display: flex; gap: 6px; flex-wrap: wrap;
        margin-top: 0.75rem;
    }

    /* ── Feed card ── */
    .feed-card {
        background: #fff;
        border-radius: 14px;
        margin-bottom: 1.1rem;
        overflow: hidden;
        display: flex;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        transition: transform 0.18s, box-shadow 0.18s;
    }
    .feed-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.2);
    }

    .feed-card.status-upcoming  { border-left: 4px solid #7a1028; }
    .feed-card.status-ongoing   { border-left: 4px solid #c47f00; }
    .feed-card.status-completed { border-left: 4px solid #1a7a4a; }

    .event-img-box {
        width: 210px; flex-shrink: 0;
        background: #f0eeea;
        display: flex; flex-direction: column;
        align-items: center; justify-content: center;
        gap: 6px; color: #bbb;
        font-family: 'Segoe UI', sans-serif;
        font-size: 12px;
        overflow: hidden;
    }
    .event-img-box img { width: 100%; height: 100%; object-fit: cover; }
    .event-img-box .ph-icon { font-size: 2rem; opacity: 0.35; }

    .event-details { padding: 1.4rem 1.6rem; flex: 1; min-width: 0; }

    .event-top {
        display: flex; align-items: center;
        gap: 8px; margin-bottom: 0.4rem; flex-wrap: wrap;
    }
    .event-club {
        font-family: 'Segoe UI', sans-serif;
        font-size: 10px; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.08em;
        color: #7a1028;
    }
    .event-status {
        display: inline-block;
        font-size: 11px; font-weight: 600;
        padding: 3px 10px; border-radius: 20px;
        font-family: 'Segoe UI', sans-serif;
    }
    .status-upcoming  { background: #fdecea; color: #7a1028; }
    .status-ongoing   { background: #fff3cd; color: #856404; }
    .status-completed { background: #e8f6ee; color: #1a7a4a; }

    .event-title {
        font-size: 1.15rem; font-weight: 600;
        color: #1a1a1a; margin-bottom: 0.5rem;
        line-height: 1.3;
    }
    .event-date, .event-location {
        font-family: 'Segoe UI', sans-serif;
        font-size: 12px; color: #888;
        margin-bottom: 0.3rem;
    }
    .event-desc {
        font-family: 'Segoe UI', sans-serif;
        font-size: 13px; color: #555;
        line-height: 1.6; margin-top: 0.5rem;
    }

    .empty-state {
        text-align: center; padding: 3rem;
        color: rgba(255,255,255,0.5);
        font-family: 'Segoe UI', sans-serif; font-size: 14px;
    }

    @media (max-width: 600px) {
        .feed-card { flex-direction: column; }
        .event-img-box { width: 100%; height: 140px; }
        .page-title { font-size: 1.7rem; }
        .events-page { padding: 2rem 1rem 3rem; }
    }
</style>

<div class="events-page">
    <div class="events-inner">

        <div class="page-header">
            <div class="page-header-eyebrow">&#128197; Apex College</div>
            <h1 class="page-title">Events Feed</h1>
            <p class="page-subtitle">All upcoming, ongoing, and past events across every campus club.</p>
        </div>

        <?php if ($is_club_admin): ?>
        <div class="admin-panel">
            <h2><?php echo $edit_event ? '&#9998; Edit Event' : '&#128197; Add New Event'; ?> &mdash; <?php echo htmlspecialchars($admin_club_name); ?></h2>
            <form method="POST" enctype="multipart/form-data">
                <?php if ($edit_event): ?>
                    <input type="hidden" name="event_id" value="<?php echo (int)$edit_event['id']; ?>">
                <?php endif; ?>
                <div class="admin-form-grid">
                    <div>
                        <label>Event Title</label>
                        <input type="text" name="title" value="<?php echo $edit_event ? htmlspecialchars($edit_event['title']) : ''; ?>" required>
                    </div>
                    <div>
                        <label>Date</label>
                        <input type="date" name="event_date" value="<?php echo $edit_event ? htmlspecialchars($edit_event['event_date']) : ''; ?>" required>
                    </div>
                    <div>
                        <label>Time</label>
                        <input type="time" name="event_time" value="<?php echo $edit_event ? htmlspecialchars($edit_event['event_time']) : ''; ?>" required>
                    </div>
                    <div>
                        <label>Location</label>
                        <input type="text" name="location" value="<?php echo $edit_event ? htmlspecialchars($edit_event['location']) : ''; ?>" required>
                    </div>
                    <div>
    <label>Event Image (Optional)</label>
    <input type="file" name="event_image" accept="image/*">

    <?php if($edit_event && !empty($edit_event['image'])): ?>
        <small>Current Image:
            <?php echo htmlspecialchars($edit_event['image']); ?>
        </small>
    <?php endif; ?>
</div>
                    <div>
                        <label>Status</label>
                        <select name="status">
                            <?php foreach (['upcoming', 'ongoing', 'completed'] as $st): ?>
                            <option value="<?php echo $st; ?>" <?php echo ($edit_event && $edit_event['status'] === $st) ? 'selected' : ''; ?>><?php echo ucfirst($st); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="full">
                        <label>Description</label>
                        <textarea name="description" rows="3"><?php echo $edit_event ? htmlspecialchars($edit_event['description']) : ''; ?></textarea>
                    </div>
                </div>
                <button type="submit" name="<?php echo $edit_event ? 'update_event' : 'add_event'; ?>" class="btn-admin">
                    <?php echo $edit_event ? 'Save Changes' : 'Add Event'; ?>
                </button>
                <?php if ($edit_event): ?>
                    <a href="events.php" class="btn-admin-outline" style="margin-left:8px;">Cancel</a>
                <?php endif; ?>
            </form>
        </div>
        <?php endif; ?>

        <?php if(mysqli_num_rows($events) == 0): ?>
            <div class="empty-state">No events found. Check back soon!</div>
        <?php endif; ?>

        <?php while($row = mysqli_fetch_assoc($events)): ?>
        <div class="feed-card status-<?php echo htmlspecialchars($row['status']); ?>">

            <div class="event-img-box">
                <?php if(!empty($row['image']) && file_exists("images/".$row['image'])): ?>

<img
    src="images/<?php echo htmlspecialchars($row['image']); ?>"
    alt="<?php echo htmlspecialchars($row['title']); ?>">

<?php else: ?>

<div class="ph-icon">&#128247;</div>
<span>Photo coming soon</span>

<?php endif; ?>
            </div>

            <div class="event-details">
                <div class="event-top">
                    <span class="event-club"><?php echo htmlspecialchars($row['club_name']); ?></span>
                    <span class="event-status status-<?php echo htmlspecialchars($row['status']); ?>"><?php echo ucfirst($row['status']); ?></span>
                </div>
                <h2 class="event-title"><?php echo htmlspecialchars($row['title']); ?></h2>
                <p class="event-date">&#128197; <?php echo date('d M Y', strtotime($row['event_date'])); ?> &nbsp;|&nbsp; &#128336; <?php echo htmlspecialchars($row['event_time']); ?></p>
                <p class="event-location">&#128205; <?php echo htmlspecialchars($row['location']); ?></p>
                <p class="event-desc"><?php echo htmlspecialchars($row['description']); ?></p>

                <?php if ($is_club_admin && (int)$row['club_id'] === $admin_club_id): ?>
                <div class="event-actions">
                    <a href="events.php?edit=<?php echo (int)$row['id']; ?>" class="btn-admin-outline">Edit</a>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="event_id" value="<?php echo (int)$row['id']; ?>">
                        <button type="submit" name="delete_event" class="btn-admin-outline" onclick="return confirm('Delete this event?')">Delete</button>
                    </form>
                </div>
                <?php endif; ?>
            </div>

        </div>
        <?php endwhile; ?>

    </div>
</div>

<?php include 'footer.php'; ?>

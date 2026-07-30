<?php
include 'header.php';
include 'db.php';

// Only admin can access
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Handle application status update
if (isset($_POST['update_status'])) {
    $reg_id = (int)$_POST['reg_id'];
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    mysqli_query($conn, "UPDATE registrations SET application_status = '$status' WHERE id = $reg_id");
    header("Location: admin.php?tab=registrations");
    exit;
}

// Handle delete event
if (isset($_POST['delete_event'])) {
    $event_id = (int)$_POST['event_id'];
    mysqli_query($conn, "DELETE FROM events WHERE id = $event_id");
    header("Location: admin.php?tab=events");
    exit;
}

// Handle add event
if (isset($_POST['add_event'])) {
    $club_id   = (int)$_POST['club_id'];
    $title     = mysqli_real_escape_string($conn, $_POST['title']);
    $desc      = mysqli_real_escape_string($conn, $_POST['description']);
    $date      = mysqli_real_escape_string($conn, $_POST['event_date']);
    $time      = mysqli_real_escape_string($conn, $_POST['event_time']);
    $location  = mysqli_real_escape_string($conn, $_POST['location']);
    $status    = mysqli_real_escape_string($conn, $_POST['status']);
    mysqli_query($conn, "INSERT INTO events (club_id, title, description, event_date, event_time, location, status) VALUES ($club_id, '$title', '$desc', '$date', '$time', '$location', '$status')");
    header("Location: admin.php?tab=events");
    exit;
}

$tab = $_GET['tab'] ?? 'dashboard';
$applications_only = isset($_GET['applications_only']);
if ($applications_only) {
    $tab = 'registrations';
}

// Stats
$total_users    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM users WHERE role = 'student'"))['c'];
$total_regs     = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM registrations"))['c'];
$total_events   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM events"))['c'];
$total_votes    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(votes) as c FROM poll_options"))['c'] ?? 0;
$pending_regs   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM registrations WHERE application_status = 'Pending'"))['c'];

$clubs = mysqli_query($conn, "SELECT * FROM clubs ORDER BY id");
?>

<style>
    *, *::before, *::after { box-sizing: border-box; }

    .admin-page {
        min-height: 100vh;
        background: #f5f3ef;
        padding: 2rem;
    }

    .admin-inner { max-width: 1100px; margin: 0 auto; }

    /* Header */
    .admin-header {
        display: flex; align-items: center;
        justify-content: space-between;
        margin-bottom: 2rem;
    }
    .admin-header h1 {
        font-size: 1.6rem; font-weight: 700;
        color: #1a1a1a;
    }
    .admin-header span {
        font-family: 'Segoe UI', sans-serif;
        font-size: 13px; color: #999;
    }

    /* Stats row */
    .stats-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }
    .stat-box {
        background: #fff;
        border: 0.5px solid #e0ddd6;
        border-radius: 12px;
        padding: 1.25rem 1.5rem;
        display: flex; align-items: center; gap: 1rem;
    }
    .stat-icon {
        font-size: 1.8rem;
        width: 48px; height: 48px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
    }
    .stat-val {
        font-size: 1.6rem; font-weight: 700;
        color: #1a1a1a; display: block;
    }
    .stat-label {
        font-size: 11px; color: #999;
        font-family: 'Segoe UI', sans-serif;
        text-transform: uppercase; letter-spacing: 0.05em;
    }

    /* Tabs */
    .tab-row {
        display: flex; gap: 6px;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
    }
    .tab-btn {
        padding: 8px 16px;
        border-radius: 8px;
        border: 0.5px solid #ddd;
        background: #fff;
        font-family: 'Segoe UI', sans-serif;
        font-size: 13px; font-weight: 600;
        color: #555;
        text-decoration: none;
        transition: all 0.15s;
    }
    .tab-btn:hover { border-color: #7a1028; color: #7a1028; }
    .tab-btn.active { background: #7a1028; color: #fff; border-color: #7a1028; }

    /* Table */
    .table-box {
        background: #fff;
        border: 0.5px solid #e0ddd6;
        border-radius: 12px;
        overflow: hidden;
    }
    .table-box-header {
        padding: 1rem 1.5rem;
        border-bottom: 0.5px solid #e0ddd6;
        display: flex; align-items: center;
        justify-content: space-between;
    }
    .table-box-header h2 {
        font-size: 15px; font-weight: 600;
        color: #1a1a1a;
    }
    .badge-count {
        background: #fdecea;
        color: #7a1028;
        font-size: 11px; font-weight: 700;
        padding: 3px 10px; border-radius: 20px;
        font-family: 'Segoe UI', sans-serif;
    }

    table { width: 100%; border-collapse: collapse; }
    th {
        background: #f9f8f5;
        padding: 10px 14px;
        font-family: 'Segoe UI', sans-serif;
        font-size: 11px; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.05em;
        color: #999;
        text-align: left;
        border-bottom: 0.5px solid #e0ddd6;
    }
    td {
        padding: 10px 14px;
        font-family: 'Segoe UI', sans-serif;
        font-size: 13px; color: #333;
        border-bottom: 0.5px solid #f0ede7;
    }
    tr:last-child td { border-bottom: none; }
    tr:hover td { background: #fafaf9; }

    /* Status badges */
    .badge {
        display: inline-block;
        font-size: 11px; font-weight: 600;
        padding: 3px 10px; border-radius: 20px;
        font-family: 'Segoe UI', sans-serif;
    }
    .badge-pending  { background: #fff3cd; color: #856404; }
    .badge-accepted { background: #e8f6ee; color: #1a7a4a; }
    .badge-rejected { background: #fdecea; color: #7a1028; }
    .badge-upcoming  { background: #fdecea; color: #7a1028; }
    .badge-ongoing   { background: #fff3cd; color: #856404; }
    .badge-completed { background: #e8f6ee; color: #1a7a4a; }

    /* Forms */
    .form-box {
        background: #fff;
        border: 0.5px solid #e0ddd6;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    .form-box h2 {
        font-size: 15px; font-weight: 600;
        color: #1a1a1a; margin-bottom: 1.25rem;
    }
    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }
    .form-group { display: flex; flex-direction: column; gap: 4px; }
    .form-group label {
        font-family: 'Segoe UI', sans-serif;
        font-size: 11px; font-weight: 700;
        color: #555; text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .form-group input,
    .form-group select,
    .form-group textarea {
        padding: 9px 12px;
        border: 0.5px solid #ddd;
        border-radius: 8px;
        font-size: 13px;
        font-family: 'Segoe UI', sans-serif;
        color: #1a1a1a;
        background: #fafaf9;
    }
    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        border-color: #7a1028;
        outline: none;
    }
    .btn-submit {
        background: #7a1028; color: #fff;
        border: none; border-radius: 8px;
        padding: 10px 22px;
        font-size: 13px; font-weight: 600;
        font-family: 'Segoe UI', sans-serif;
        cursor: pointer; margin-top: 1rem;
        transition: background 0.15s;
    }
    .btn-submit:hover { background: #5e0c1e; }

    .btn-delete {
        background: #fdecea; color: #7a1028;
        border: 0.5px solid #f5c6cb;
        border-radius: 6px; padding: 5px 12px;
        font-size: 12px; font-weight: 600;
        font-family: 'Segoe UI', sans-serif;
        cursor: pointer;
    }
    .btn-delete:hover { background: #7a1028; color: #fff; }

    select.status-select {
        padding: 4px 8px;
        border: 0.5px solid #ddd;
        border-radius: 6px;
        font-size: 12px;
        font-family: 'Segoe UI', sans-serif;
        cursor: pointer;
    }

    .empty-msg {
        text-align: center; padding: 2rem;
        color: #bbb; font-family: 'Segoe UI', sans-serif;
        font-size: 13px;
    }

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

    <!-- Stats -->
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
    <!-- Tabs -->
     <?php if (!$applications_only): ?>
    <div class="tab-row">
        <a href="admin.php?tab=dashboard"      class="tab-btn <?php echo $tab=='dashboard'      ? 'active':''; ?>">&#127968; Dashboard</a>
        <a href="admin.php?tab=registrations"  class="tab-btn <?php echo $tab=='registrations'  ? 'active':''; ?>">&#128203; Applications</a>
        <a href="admin.php?tab=events"         class="tab-btn <?php echo $tab=='events'         ? 'active':''; ?>">&#128197; Events</a>
        <a href="admin.php?tab=votes"          class="tab-btn <?php echo $tab=='votes'          ? 'active':''; ?>">&#128313; Vote Results</a>
        <a href="admin.php?tab=users"          class="tab-btn <?php echo $tab=='users'          ? 'active':''; ?>">&#128101; Students</a>
    </div>
    <?php endif; ?>
    <?php if($tab == 'dashboard'): ?>
    <!-- DASHBOARD -->
    <div class="table-box">
        <div class="table-box-header">
            <h2>&#128203; Recent Applications</h2>
            <span class="badge-count"><?php echo $pending_regs; ?> pending</span>
        </div>
        <?php
        $recent = mysqli_query($conn, "SELECT * FROM registrations ORDER BY applied_at DESC LIMIT 5");
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
    <!-- APPLICATIONS -->
    <div class="table-box">
        <div class="table-box-header">
            <h2>&#128203; All Club Intake Applications</h2>
            <span class="badge-count"><?php echo $total_regs; ?> total</span>
        </div>
        <?php
        $regs = mysqli_query($conn, "SELECT * FROM registrations ORDER BY applied_at DESC");
        if(mysqli_num_rows($regs) == 0): ?>
            <div class="empty-msg">No applications yet.</div>
        <?php else: ?>
        <table>
            <tr>
                <th>Name</th><th>Email</th><th>Faculty</th><th>Semester</th><th>Club</th><th>Status</th><th>Action</th>
            </tr>
            <?php while($r = mysqli_fetch_assoc($regs)): ?>
            <tr>
                <td><?php echo htmlspecialchars($r['student_name']); ?></td>
                <td><?php echo htmlspecialchars($r['student_email']); ?></td>
                <td><?php echo htmlspecialchars($r['faculty']); ?></td>
                <td><?php echo htmlspecialchars($r['semester']); ?></td>
                <td><?php echo htmlspecialchars($r['selected_club']); ?></td>
                <td><span class="badge badge-<?php echo strtolower($r['application_status']); ?>"><?php echo $r['application_status']; ?></span></td>
                <td>
                    <form method="POST" style="display:flex; gap:6px; align-items:center;">
                        <input type="hidden" name="reg_id" value="<?php echo $r['id']; ?>">
                        <select name="status" class="status-select">
                            <option value="Pending"  <?php echo $r['application_status']=='Pending'  ? 'selected':''; ?>>Pending</option>
                            <option value="Accepted" <?php echo $r['application_status']=='Accepted' ? 'selected':''; ?>>Accepted</option>
                            <option value="Rejected" <?php echo $r['application_status']=='Rejected' ? 'selected':''; ?>>Rejected</option>
                        </select>
                        <button type="submit" name="update_status" class="btn-submit" style="padding:5px 12px; margin-top:0;">Save</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
        <?php endif; ?>
    </div>

    <?php elseif($tab == 'events'): ?>
    <!-- ADD EVENT FORM -->
    <div class="form-box">
        <h2>&#128197; Add New Event</h2>
        <form method="POST">
            <div class="form-grid">
                <div class="form-group">
                    <label>Club</label>
                    <select name="club_id" required>
                        <?php
                        mysqli_data_seek($clubs, 0);
                        while($c = mysqli_fetch_assoc($clubs)): ?>
                            <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Event Title</label>
                    <input type="text" name="title" required>
                </div>
                <div class="form-group">
                    <label>Date</label>
                    <input type="date" name="event_date" required>
                </div>
                <div class="form-group">
                    <label>Time</label>
                    <input type="time" name="event_time" required>
                </div>
                <div class="form-group">
                    <label>Location</label>
                    <input type="text" name="location" required>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="status">
                        <option value="upcoming">Upcoming</option>
                        <option value="ongoing">Ongoing</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>
                <div class="form-group" style="grid-column: 1/-1;">
                    <label>Description</label>
                    <textarea name="description" rows="3"></textarea>
                </div>
            </div>
            <button type="submit" name="add_event" class="btn-submit">Add Event</button>
        </form>
    </div>

    <!-- EVENTS TABLE -->
    <div class="table-box">
        <div class="table-box-header">
            <h2>&#128197; All Events</h2>
            <span class="badge-count"><?php echo $total_events; ?> total</span>
        </div>
        <?php
        $events = mysqli_query($conn, "SELECT e.*, c.name as club_name FROM events e JOIN clubs c ON e.club_id = c.id ORDER BY e.event_date DESC");
        if(mysqli_num_rows($events) == 0): ?>
            <div class="empty-msg">No events yet.</div>
        <?php else: ?>
        <table>
            <tr>
                <th>Title</th><th>Club</th><th>Date</th><th>Location</th><th>Status</th><th>Action</th>
            </tr>
            <?php while($e = mysqli_fetch_assoc($events)): ?>
            <tr>
                <td><?php echo htmlspecialchars($e['title']); ?></td>
                <td><?php echo htmlspecialchars($e['club_name']); ?></td>
                <td><?php echo date('d M Y', strtotime($e['event_date'])); ?></td>
                <td><?php echo htmlspecialchars($e['location']); ?></td>
                <td><span class="badge badge-<?php echo $e['status']; ?>"><?php echo ucfirst($e['status']); ?></span></td>
                <td>
                    <form method="POST">
                        <input type="hidden" name="event_id" value="<?php echo $e['id']; ?>">
                        <button type="submit" name="delete_event" class="btn-delete" onclick="return confirm('Delete this event?')">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
        <?php endif; ?>
    </div>

    <?php elseif($tab == 'votes'): ?>
    <!-- VOTE RESULTS -->
    <?php
    $polls = mysqli_query($conn, "SELECT p.*, c.name as club_name FROM polls p JOIN clubs c ON p.club_id = c.id WHERE p.is_active = 1");
    while($poll = mysqli_fetch_assoc($polls)):
        $total_poll_votes = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(votes) as t FROM poll_options WHERE poll_id = " . $poll['id']))['t'] ?? 0;
        $options = mysqli_query($conn, "SELECT * FROM poll_options WHERE poll_id = " . $poll['id'] . " ORDER BY votes DESC");
    ?>
    <div class="table-box" style="margin-bottom:1rem;">
        <div class="table-box-header">
            <h2><?php echo htmlspecialchars($poll['club_name']); ?> — <?php echo htmlspecialchars($poll['question']); ?></h2>
            <span class="badge-count"><?php echo $total_poll_votes; ?> votes</span>
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
    <!-- STUDENTS -->
    <div class="table-box">
        <div class="table-box-header">
            <h2>&#128101; Registered Students</h2>
            <span class="badge-count"><?php echo $total_users; ?> total</span>
        </div>
        <?php
        $users = mysqli_query($conn, "SELECT id, name, email FROM users WHERE role = 'student' ORDER BY id DESC");
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

<?php include 'footer.php'; ?>
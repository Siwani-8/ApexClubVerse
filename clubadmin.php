<?php
include 'header.php';
include 'db.php';

// Only logged-in club admins can access this page.
// Each club admin only ever sees/edits their own club (my_club_id below) —
// this file is effectively "6 panels" since the data shown is entirely
// determined by which club the logged-in admin belongs to.
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_role'] !== 'club_admin' || empty($_SESSION['club_id'])) {
    header("Location: login.php");
    exit;
}

$my_club_id = (int)$_SESSION['club_id'];

// Keyword used to match this club's applications inside registrations.selected_club
// (registration.php stores short club names like "IT Club", "HEAT", etc.)
$club_keywords = [
    1 => 'Performing Arts Club',
    2 => 'Sports and Leadership Club',
    3 => 'Travel and Tourism Club',
    4 => 'Media and Marketing Club',
    5 => 'IT Club',
    6 => 'HEAT',
];
$my_keyword = $club_keywords[$my_club_id] ?? '';

$club_res = mysqli_query($conn, "SELECT * FROM clubs WHERE id = $my_club_id");
$my_club = mysqli_fetch_assoc($club_res);
if (!$my_club) { die("Your account isn't linked to a valid club. Contact the site admin."); }

// ---------- POST handlers (all scoped to my_club_id) ----------

// Update club profile
if (isset($_POST['update_profile'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    mysqli_query($conn, "UPDATE clubs SET name='$name', description='$desc' WHERE id = $my_club_id");
    header("Location: club_admin.php?tab=profile");
    exit;
}

// Add event
if (isset($_POST['add_event'])) {
    $title    = mysqli_real_escape_string($conn, $_POST['title']);
    $desc     = mysqli_real_escape_string($conn, $_POST['description']);
    $date     = mysqli_real_escape_string($conn, $_POST['event_date']);
    $time     = mysqli_real_escape_string($conn, $_POST['event_time']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $status   = mysqli_real_escape_string($conn, $_POST['status']);
    mysqli_query($conn, "INSERT INTO events (club_id, title, description, event_date, event_time, location, status)
                          VALUES ($my_club_id, '$title', '$desc', '$date', '$time', '$location', '$status')");
    header("Location: club_admin.php?tab=events");
    exit;
}

// Delete event (scoped — can't touch another club's event even by guessing an id)
if (isset($_POST['delete_event'])) {
    $event_id = (int)$_POST['event_id'];
    mysqli_query($conn, "DELETE FROM events WHERE id = $event_id AND club_id = $my_club_id");
    header("Location: club_admin.php?tab=events");
    exit;
}

// Create poll with options
if (isset($_POST['add_poll'])) {
    $question = mysqli_real_escape_string($conn, $_POST['question']);
    $options  = array_filter(array_map('trim', $_POST['options'] ?? []));

    if ($question === '' || count($options) < 2) {
        $poll_error = "A poll needs a question and at least 2 options.";
    } else {
        mysqli_query($conn, "INSERT INTO polls (club_id, question, is_active, created_at)
                              VALUES ($my_club_id, '$question', 1, NOW())");
        $new_poll_id = mysqli_insert_id($conn);
        foreach ($options as $opt) {
            $opt_safe = mysqli_real_escape_string($conn, $opt);
            mysqli_query($conn, "INSERT INTO poll_options (poll_id, option_text, votes)
                                  VALUES ($new_poll_id, '$opt_safe', 0)");
        }
        header("Location: club_admin.php?tab=polls");
        exit;
    }
}

// Toggle poll active/closed (scoped to own club)
if (isset($_POST['toggle_poll'])) {
    $poll_id = (int)$_POST['poll_id'];
    mysqli_query($conn, "UPDATE polls SET is_active = 1 - is_active WHERE id = $poll_id AND club_id = $my_club_id");
    header("Location: club_admin.php?tab=polls");
    exit;
}

// Delete poll (scoped to own club; options/votes cascade if FKs from schema_updates.sql are applied)
if (isset($_POST['delete_poll'])) {
    $poll_id = (int)$_POST['poll_id'];
    $owns = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id FROM polls WHERE id = $poll_id AND club_id = $my_club_id"));
    if ($owns) {
        mysqli_query($conn, "DELETE FROM poll_votes WHERE poll_id = $poll_id");
        mysqli_query($conn, "DELETE FROM poll_options WHERE poll_id = $poll_id");
        mysqli_query($conn, "DELETE FROM polls WHERE id = $poll_id");
    }
    header("Location: club_admin.php?tab=polls");
    exit;
}

// Update an applicant's status (scoped: only rows matching this club's keyword)
if (isset($_POST['update_status'])) {
    $reg_id = (int)$_POST['reg_id'];
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $kw     = mysqli_real_escape_string($conn, $my_keyword);
    mysqli_query($conn, "UPDATE registrations
                          SET application_status = '$status'
                          WHERE id = $reg_id AND selected_club LIKE '%$kw%'");
    header("Location: club_admin.php?tab=applications");
    exit;
}

$tab = $_GET['tab'] ?? 'dashboard';

// ---------- Stats (scoped) ----------
$total_events = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM events WHERE club_id = $my_club_id"))['c'];
$total_polls  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM polls WHERE club_id = $my_club_id"))['c'];
$total_votes  = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT SUM(po.votes) as c FROM poll_options po
    JOIN polls p ON po.poll_id = p.id WHERE p.club_id = $my_club_id"))['c'] ?? 0;

$kw_safe = mysqli_real_escape_string($conn, $my_keyword);
$total_apps   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM registrations WHERE selected_club LIKE '%$kw_safe%'"))['c'];
$pending_apps = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM registrations WHERE selected_club LIKE '%$kw_safe%' AND application_status = 'Pending'"))['c'];
?>

<style>
    *, *::before, *::after { box-sizing: border-box; }

    .admin-page { min-height: 100vh; background: #f5f3ef; padding: 2rem; }
    .admin-inner { max-width: 1100px; margin: 0 auto; }

    .admin-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 2rem; flex-wrap: wrap; gap: 0.5rem; }
    .admin-header h1 { font-size: 1.6rem; font-weight: 700; color: #1a1a1a; }
    .admin-header span { font-family: 'Segoe UI', sans-serif; font-size: 13px; color: #999; }
    .club-chip {
        display: inline-block; background: #fdecea; color: #7a1028;
        font-family: 'Segoe UI', sans-serif; font-size: 11px; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.06em;
        padding: 4px 12px; border-radius: 20px; margin-bottom: 6px;
    }

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
    .badge-pending { background: #fff3cd; color: #856404; }
    .badge-accepted { background: #e8f6ee; color: #1a7a4a; }
    .badge-rejected { background: #fdecea; color: #7a1028; }
    .badge-upcoming { background: #fdecea; color: #7a1028; }
    .badge-ongoing { background: #fff3cd; color: #856404; }
    .badge-completed { background: #e8f6ee; color: #1a7a4a; }
    .badge-active { background: #e8f6ee; color: #1a7a4a; }
    .badge-closed { background: #ececec; color: #777; }

    .form-box { background: #fff; border: 0.5px solid #e0ddd6; border-radius: 12px; padding: 1.5rem; margin-bottom: 1.5rem; }
    .form-box h2 { font-size: 15px; font-weight: 600; color: #1a1a1a; margin-bottom: 1.25rem; }
    .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; }
    .form-group { display: flex; flex-direction: column; gap: 4px; }
    .form-group label { font-family: 'Segoe UI', sans-serif; font-size: 11px; font-weight: 700; color: #555; text-transform: uppercase; letter-spacing: 0.05em; }
    .form-group input, .form-group select, .form-group textarea {
        padding: 9px 12px; border: 0.5px solid #ddd; border-radius: 8px; font-size: 13px;
        font-family: 'Segoe UI', sans-serif; color: #1a1a1a; background: #fafaf9;
    }
    .form-group input:focus, .form-group select:focus, .form-group textarea:focus { border-color: #7a1028; outline: none; }
    .btn-submit { background: #7a1028; color: #fff; border: none; border-radius: 8px; padding: 10px 22px; font-size: 13px; font-weight: 600; font-family: 'Segoe UI', sans-serif; cursor: pointer; margin-top: 1rem; transition: background 0.15s; }
    .btn-submit:hover { background: #5e0c1e; }
    .btn-delete { background: #fdecea; color: #7a1028; border: 0.5px solid #f5c6cb; border-radius: 6px; padding: 5px 12px; font-size: 12px; font-weight: 600; font-family: 'Segoe UI', sans-serif; cursor: pointer; }
    .btn-delete:hover { background: #7a1028; color: #fff; }
    .btn-ghost { background: #fff; color: #555; border: 0.5px solid #ddd; border-radius: 6px; padding: 5px 12px; font-size: 12px; font-weight: 600; font-family: 'Segoe UI', sans-serif; cursor: pointer; }
    .btn-ghost:hover { border-color: #7a1028; color: #7a1028; }
    select.status-select { padding: 4px 8px; border: 0.5px solid #ddd; border-radius: 6px; font-size: 12px; font-family: 'Segoe UI', sans-serif; cursor: pointer; }
    .empty-msg { text-align: center; padding: 2rem; color: #bbb; font-family: 'Segoe UI', sans-serif; font-size: 13px; }
    .alert-error { background: #fdecea; border: 0.5px solid #f5c6cb; color: #7a1028; border-radius: 8px; padding: 10px 14px; font-family: 'Segoe UI', sans-serif; font-size: 13px; margin-bottom: 1.25rem; }

    /* Poll option builder */
    #optionsList { display: flex; flex-direction: column; gap: 8px; margin-bottom: 0.75rem; }
    #optionsList input { padding: 9px 12px; border: 0.5px solid #ddd; border-radius: 8px; font-size: 13px; font-family: 'Segoe UI', sans-serif; }
    .add-option-btn { background: #f5f3ef; border: 0.5px dashed #ccc; border-radius: 8px; padding: 8px 14px; font-size: 12px; font-weight: 600; color: #7a1028; font-family: 'Segoe UI', sans-serif; cursor: pointer; }
    .add-option-btn:hover { background: #fdecea; }

    .result-row { margin-bottom: 0.6rem; }
    .result-top { display: flex; justify-content: space-between; margin-bottom: 4px; font-family: 'Segoe UI', sans-serif; font-size: 12px; color: #444; }
    .result-track { background: #f0ede7; border-radius: 30px; height: 8px; overflow: hidden; }
    .result-fill { height: 100%; border-radius: 30px; background: #7a1028; }

    @media (max-width: 600px) {
        .admin-page { padding: 1rem; }
        .stats-row { grid-template-columns: 1fr 1fr; }
    }
</style>

<div class="admin-page">
<div class="admin-inner">

    <div class="admin-header">
        <div>
            <div class="club-chip"><?php echo htmlspecialchars($my_club['name']); ?></div>
            <h1>&#127917; Club Admin Panel</h1>
        </div>
        <span>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
    </div>

    <div class="stats-row">
        <div class="stat-box">
            <div class="stat-icon" style="background:#e8f6ee;">&#128197;</div>
            <div><span class="stat-val"><?php echo $total_events; ?></span><span class="stat-label">Events</span></div>
        </div>
        <div class="stat-box">
            <div class="stat-icon" style="background:#fef0e8;">&#128313;</div>
            <div><span class="stat-val"><?php echo $total_polls; ?></span><span class="stat-label">Polls</span></div>
        </div>
        <div class="stat-box">
            <div class="stat-icon" style="background:#e8f0fb;">&#9997;</div>
            <div><span class="stat-val"><?php echo $total_votes; ?></span><span class="stat-label">Votes cast</span></div>
        </div>
        <div class="stat-box">
            <div class="stat-icon" style="background:#fdecea;">&#128203;</div>
            <div><span class="stat-val"><?php echo $total_apps; ?></span><span class="stat-label">Applications</span></div>
        </div>
        <div class="stat-box">
            <div class="stat-icon" style="background:#fff3cd;">&#9888;</div>
            <div><span class="stat-val"><?php echo $pending_apps; ?></span><span class="stat-label">Pending</span></div>
        </div>
    </div>

    <div class="tab-row">
        <a href="club_admin.php?tab=dashboard"     class="tab-btn <?php echo $tab=='dashboard'     ? 'active':''; ?>">&#127968; Dashboard</a>
        <a href="club_admin.php?tab=events"        class="tab-btn <?php echo $tab=='events'        ? 'active':''; ?>">&#128197; Events</a>
        <a href="club_admin.php?tab=polls"         class="tab-btn <?php echo $tab=='polls'         ? 'active':''; ?>">&#128313; Polls</a>
        <a href="club_admin.php?tab=applications"  class="tab-btn <?php echo $tab=='applications'  ? 'active':''; ?>">&#128203; Applications</a>
        <a href="club_admin.php?tab=profile"       class="tab-btn <?php echo $tab=='profile'       ? 'active':''; ?>">&#9881; Club Profile</a>
    </div>

    <?php if ($tab == 'dashboard'): ?>
        <div class="table-box">
            <div class="table-box-header">
                <h2>&#128203; Recent Applications</h2>
                <span class="badge-count"><?php echo $pending_apps; ?> pending</span>
            </div>
            <?php
            $recent = mysqli_query($conn, "SELECT * FROM registrations WHERE selected_club LIKE '%$kw_safe%' ORDER BY applied_at DESC LIMIT 5");
            if (mysqli_num_rows($recent) == 0): ?>
                <div class="empty-msg">No applications yet.</div>
            <?php else: ?>
            <table>
                <tr><th>Name</th><th>Email</th><th>Status</th><th>Date</th></tr>
                <?php while ($r = mysqli_fetch_assoc($recent)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($r['student_name']); ?></td>
                    <td><?php echo htmlspecialchars($r['student_email']); ?></td>
                    <td><span class="badge badge-<?php echo strtolower($r['application_status']); ?>"><?php echo $r['application_status']; ?></span></td>
                    <td><?php echo date('d M Y', strtotime($r['applied_at'])); ?></td>
                </tr>
                <?php endwhile; ?>
            </table>
            <?php endif; ?>
        </div>

    <?php elseif ($tab == 'events'): ?>
        <div class="form-box">
            <h2>&#128197; Add New Event</h2>
            <form method="POST">
                <div class="form-grid">
                    <div class="form-group"><label>Event Title</label><input type="text" name="title" required></div>
                    <div class="form-group"><label>Date</label><input type="date" name="event_date" required></div>
                    <div class="form-group"><label>Time</label><input type="time" name="event_time" required></div>
                    <div class="form-group"><label>Location</label><input type="text" name="location" required></div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status">
                            <option value="upcoming">Upcoming</option>
                            <option value="ongoing">Ongoing</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                    <div class="form-group" style="grid-column: 1/-1;"><label>Description</label><textarea name="description" rows="3"></textarea></div>
                </div>
                <button type="submit" name="add_event" class="btn-submit">Add Event</button>
            </form>
        </div>

        <div class="table-box">
            <div class="table-box-header">
                <h2>&#128197; My Events</h2>
                <span class="badge-count"><?php echo $total_events; ?> total</span>
            </div>
            <?php
            $events = mysqli_query($conn, "SELECT * FROM events WHERE club_id = $my_club_id ORDER BY event_date DESC");
            if (mysqli_num_rows($events) == 0): ?>
                <div class="empty-msg">No events yet.</div>
            <?php else: ?>
            <table>
                <tr><th>Title</th><th>Date</th><th>Location</th><th>Status</th><th>Action</th></tr>
                <?php while ($e = mysqli_fetch_assoc($events)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($e['title']); ?></td>
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

    <?php elseif ($tab == 'polls'): ?>
        <div class="form-box">
            <h2>&#128313; Create a New Poll</h2>
            <?php if (!empty($poll_error)): ?><div class="alert-error"><?php echo htmlspecialchars($poll_error); ?></div><?php endif; ?>
            <form method="POST" id="pollForm">
                <div class="form-group" style="margin-bottom:1rem;">
                    <label>Question</label>
                    <input type="text" name="question" placeholder="Which event should we run next?" required>
                </div>
                <label style="font-family:'Segoe UI',sans-serif; font-size:11px; font-weight:700; color:#555; text-transform:uppercase; letter-spacing:0.05em;">Options</label>
                <div id="optionsList">
                    <input type="text" name="options[]" placeholder="Option 1" required>
                    <input type="text" name="options[]" placeholder="Option 2" required>
                </div>
                <button type="button" class="add-option-btn" onclick="addOption()">+ Add another option</button>
                <br>
                <button type="submit" name="add_poll" class="btn-submit">Create Poll</button>
            </form>
        </div>

        <div class="table-box">
            <div class="table-box-header">
                <h2>&#128313; My Polls</h2>
                <span class="badge-count"><?php echo $total_polls; ?> total</span>
            </div>
            <?php
            $polls = mysqli_query($conn, "SELECT * FROM polls WHERE club_id = $my_club_id ORDER BY created_at DESC");
            if (mysqli_num_rows($polls) == 0): ?>
                <div class="empty-msg">No polls yet — create one above.</div>
            <?php else: ?>
                <?php while ($p = mysqli_fetch_assoc($polls)):
                    $opts = mysqli_query($conn, "SELECT * FROM poll_options WHERE poll_id = " . $p['id'] . " ORDER BY votes DESC");
                    $poll_total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(votes) as t FROM poll_options WHERE poll_id = " . $p['id']))['t'] ?? 0;
                ?>
                <div style="padding:1.25rem 1.5rem; border-bottom: 0.5px solid #f0ede7;">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:1rem; margin-bottom:0.75rem; flex-wrap:wrap;">
                        <div>
                            <span class="badge badge-<?php echo $p['is_active'] ? 'active' : 'closed'; ?>"><?php echo $p['is_active'] ? 'Active' : 'Closed'; ?></span>
                            <strong style="display:block; margin-top:6px; font-size:14px;"><?php echo htmlspecialchars($p['question']); ?></strong>
                        </div>
                        <div style="display:flex; gap:6px;">
                            <form method="POST"><input type="hidden" name="poll_id" value="<?php echo $p['id']; ?>"><button type="submit" name="toggle_poll" class="btn-ghost"><?php echo $p['is_active'] ? 'Close' : 'Reopen'; ?></button></form>
                            <form method="POST" onsubmit="return confirm('Delete this poll and all its votes?')"><input type="hidden" name="poll_id" value="<?php echo $p['id']; ?>"><button type="submit" name="delete_poll" class="btn-delete">Delete</button></form>
                        </div>
                    </div>
                    <?php while ($o = mysqli_fetch_assoc($opts)):
                        $pct = $poll_total > 0 ? round(($o['votes'] / $poll_total) * 100) : 0;
                    ?>
                    <div class="result-row">
                        <div class="result-top"><span><?php echo htmlspecialchars($o['option_text']); ?></span><span><?php echo $o['votes']; ?> votes (<?php echo $pct; ?>%)</span></div>
                        <div class="result-track"><div class="result-fill" style="width:<?php echo $pct; ?>%"></div></div>
                    </div>
                    <?php endwhile; ?>
                </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>

    <?php elseif ($tab == 'applications'): ?>
        <div class="table-box">
            <div class="table-box-header">
                <h2>&#128203; Applications for <?php echo htmlspecialchars($my_club['name']); ?></h2>
                <span class="badge-count"><?php echo $total_apps; ?> total</span>
            </div>
            <?php
            $regs = mysqli_query($conn, "SELECT * FROM registrations WHERE selected_club LIKE '%$kw_safe%' ORDER BY applied_at DESC");
            if (mysqli_num_rows($regs) == 0): ?>
                <div class="empty-msg">No applications yet.</div>
            <?php else: ?>
            <table>
                <tr><th>Name</th><th>Email</th><th>Faculty</th><th>Semester</th><th>Status</th><th>Action</th></tr>
                <?php while ($r = mysqli_fetch_assoc($regs)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($r['student_name']); ?></td>
                    <td><?php echo htmlspecialchars($r['student_email']); ?></td>
                    <td><?php echo htmlspecialchars($r['faculty']); ?></td>
                    <td><?php echo htmlspecialchars($r['semester']); ?></td>
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

    <?php elseif ($tab == 'profile'): ?>
        <div class="form-box">
            <h2>&#9881; Club Profile</h2>
            <form method="POST">
                <div class="form-grid">
                    <div class="form-group"><label>Club Name</label><input type="text" name="name" value="<?php echo htmlspecialchars($my_club['name']); ?>" required></div>
                    <div class="form-group" style="grid-column: 1/-1;"><label>Description</label><textarea name="description" rows="5" required><?php echo htmlspecialchars($my_club['description']); ?></textarea></div>
                </div>
                <button type="submit" name="update_profile" class="btn-submit">Save Changes</button>
            </form>
        </div>
    <?php endif; ?>

</div>
</div>

<script>
function addOption() {
    const list = document.getElementById('optionsList');
    const count = list.querySelectorAll('input').length + 1;
    const input = document.createElement('input');
    input.type = 'text';
    input.name = 'options[]';
    input.placeholder = 'Option ' + count;
    list.appendChild(input);
}
</script>

<?php include 'footer.php'; ?>
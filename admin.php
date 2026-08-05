<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/club_admin_helpers.php';

if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_role'] !== 'admin') {
    redirect('login.php');
}

include 'includes/header.php';
// Get the admin's club name using club_id
if (empty($_SESSION['club_name'])) {

    $user_id = (int)$_SESSION['user_id'];

    $clubQuery = mysqli_query(
        $conn,
        "SELECT c.name
         FROM users u
         JOIN clubs c ON u.club_id = c.id
         WHERE u.id = $user_id
         LIMIT 1"
    );

    if ($clubQuery && mysqli_num_rows($clubQuery) > 0) {

        $clubRow = mysqli_fetch_assoc($clubQuery);

        $_SESSION['club_name'] = $clubRow['name'];

    } else {

        die("Error: No club is assigned to this admin.");

    }
}

if (empty($_SESSION['club_id'])) {
    $club = mysqli_real_escape_string($conn, $_SESSION['club_name'] ?? '');
    $idRow = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id FROM clubs WHERE name='$club' LIMIT 1"));
    if ($idRow) {
        $_SESSION['club_id'] = (int)$idRow['id'];
    }
}

if (isset($_POST['update_status'])) {
    $reg_id = (int)$_POST['reg_id'];
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    mysqli_query($conn, "UPDATE registrations SET application_status = '$status' WHERE id = $reg_id");
    redirect('admin.php?tab=registrations');
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

    redirect('admin.php?tab=events');
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

        if(!is_dir(root_path('uploads/events'))){
            mkdir(root_path('uploads/events'),0777,true);
        }

        $filename = time() . "_" . basename($_FILES['event_image']['name']);

        move_uploaded_file(
            $_FILES['event_image']['tmp_name'],
            root_path('uploads/events/' . $filename)
        );

        $image = "uploads/events/".$filename;
    }
    $event_id = (int)($_POST['event_id'] ?? 0);

    if($event_id > 0){
        if (!event_belongs_to_club($conn, $event_id, $club_id)) {
            flash_set('error', 'You can only edit events for your own club.');
            redirect('admin.php?tab=events');
        }

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
            WHERE id=$event_id AND club_id=$club_id
            ");
            flash_set('success', 'Event updated.');

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
            WHERE id=$event_id AND club_id=$club_id
            ");
            flash_set('success', 'Event updated (cover photo replaced).');

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
        flash_set('success', 'Event added.');

    }
    redirect('admin.php?tab=events');
}

if (isset($_POST['schedule_interviews'])) {
    $club = mysqli_real_escape_string($conn, $_SESSION['club_name']);
    $date = $_POST['interview_date'];
    $start = $_POST['start_time'];
    $end = $_POST['end_time'];
    $venue = mysqli_real_escape_string($conn, $_POST['venue']);

    if (strtotime($start) >= strtotime($end)) {
        redirect('admin.php?tab=registrations&error=time');
    }

    $check = mysqli_query($conn, "SELECT COUNT(*) AS total FROM registrations WHERE selected_club='$club' AND interview_status <> 'PENDING'");
    $already = mysqli_fetch_assoc($check);

    if ($already['total'] > 0) {
        mysqli_query($conn, "UPDATE registrations SET interview_date='$date', interview_start_time='$start', interview_end_time='$end', interview_venue='$venue', is_rescheduled=1, interview_status='RESCHEDULED' WHERE selected_club='$club'");
    } else {
        mysqli_query($conn, "UPDATE registrations SET interview_date='$date', interview_start_time='$start', interview_end_time='$end', interview_venue='$venue', interview_status='SCHEDULED' WHERE selected_club='$club'");
    }

    redirect('admin.php?tab=registrations');
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

    redirect('admin.php?tab=votes');
}

if(isset($_POST['delete_poll'])){

    $poll_id = (int)$_POST['poll_id'];

    mysqli_query($conn,
    "DELETE FROM poll_options
     WHERE poll_id=$poll_id");

    mysqli_query($conn,
    "DELETE FROM polls
     WHERE id=$poll_id");

    redirect('admin.php?tab=votes');
}

if(isset($_POST['delete_bod'])){
    $id = (int)$_POST['bod_id'];
    $club_id = admin_club_id();
    if ($club_id && bod_belongs_to_club($conn, $id, $club_id)) {
        mysqli_query($conn, "DELETE FROM bod_members WHERE id=$id AND club_id=$club_id");
        flash_set('success', 'Board member removed.');
    } else {
        flash_set('error', 'You can only manage members of your own club.');
    }
    redirect('admin.php?tab=members');
}

if(isset($_POST['delete_boa'])){
    $id = (int)$_POST['boa_id'];
    $club_id = admin_club_id();
    if ($club_id && boa_belongs_to_club($conn, $id, $club_id)) {
        mysqli_query($conn, "DELETE FROM boa_members WHERE id=$id AND club_id=$club_id");
        flash_set('success', 'Advisor removed.');
    } else {
        flash_set('error', 'You can only manage members of your own club.');
    }
    redirect('admin.php?tab=members');
}

if(isset($_POST['add_bod'])){

    $club = mysqli_real_escape_string($conn,$_SESSION['club_name']);

    $clubRow = mysqli_fetch_assoc(
        mysqli_query($conn,
        "SELECT id FROM clubs WHERE name='$club'")
    );

    $club_id = $clubRow['id'];

    $name = mysqli_real_escape_string($conn,$_POST['name']);
    $position = mysqli_real_escape_string($conn,$_POST['position']);
    $bio = mysqli_real_escape_string($conn,$_POST['bio']);

    $photo = "default.jpg";

    if(isset($_FILES['photo']) && $_FILES['photo']['error']==0){

        $photo = basename($_FILES['photo']['name']);

        move_uploaded_file(
            $_FILES['photo']['tmp_name'],
            root_path("images/members/" . $photo)
        );
    }

    mysqli_query($conn,"
        INSERT INTO bod_members
        (
            club_id,
            name,
            position,
            bio,
            photo
        )
        VALUES
        (
            $club_id,
            '$name',
            '$position',
            '$bio',
            '$photo'
        )
    ");

    redirect('admin.php?tab=members');
}

if(isset($_POST['add_boa'])){

    $club = mysqli_real_escape_string($conn,$_SESSION['club_name']);

    $clubRow=mysqli_fetch_assoc(
    mysqli_query($conn,
    "SELECT id FROM clubs WHERE name='$club'")
    );

    $club_id=$clubRow['id'];

    $name=mysqli_real_escape_string($conn,$_POST['boa_name']);
    $title=mysqli_real_escape_string($conn,$_POST['boa_title']);
    $expertise=mysqli_real_escape_string($conn,$_POST['boa_expertise']);

    $photo="default.jpg";

    if(isset($_FILES['boa_photo']) && $_FILES['boa_photo']['error']==0){

        $photo=time()."_".basename($_FILES['boa_photo']['name']);

        move_uploaded_file(
            $_FILES['boa_photo']['tmp_name'],
            root_path("images/members/" . $photo)
        );

    }

    mysqli_query($conn,"
    INSERT INTO boa_members
    (club_id,name,title,expertise,photo)
    VALUES
    ($club_id,'$name','$title','$expertise','$photo')
    ");

    redirect('admin.php?tab=members');
}

if(isset($_POST['update_bod'])){
    $id = (int)$_POST['member_id'];
    $club_id = admin_club_id();

    if (!$club_id || !bod_belongs_to_club($conn, $id, $club_id)) {
        flash_set('error', 'You can only edit members of your own club.');
        redirect('admin.php?tab=members');
    }

    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $position = mysqli_real_escape_string($conn, $_POST['position']);
    $bio = mysqli_real_escape_string($conn, $_POST['bio']);
    $photo_sql = '';

    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $photo = time() . '_' . basename($_FILES['photo']['name']);
        move_uploaded_file(
            $_FILES['photo']['tmp_name'],
            root_path('images/members/' . $photo)
        );
        $photo_sql = ", photo='$photo'";
    }

    mysqli_query($conn, "
    UPDATE bod_members
    SET name='$name', position='$position', bio='$bio' $photo_sql
    WHERE id=$id AND club_id=$club_id
    ");
    flash_set('success', 'Board member updated' . ($photo_sql ? ' (photo replaced).' : '.'));
    redirect('admin.php?tab=members');
}

if(isset($_POST['update_boa'])){
    $id = (int)$_POST['boa_member_id'];
    $club_id = admin_club_id();

    if (!$club_id || !boa_belongs_to_club($conn, $id, $club_id)) {
        flash_set('error', 'You can only edit members of your own club.');
        redirect('admin.php?tab=members');
    }

    $name = mysqli_real_escape_string($conn, $_POST['boa_name']);
    $title = mysqli_real_escape_string($conn, $_POST['boa_title']);
    $expertise = mysqli_real_escape_string($conn, $_POST['boa_expertise']);

    if (isset($_FILES['boa_photo']) && $_FILES['boa_photo']['error'] == 0) {
        $photo = time() . '_' . basename($_FILES['boa_photo']['name']);
        move_uploaded_file(
            $_FILES['boa_photo']['tmp_name'],
            root_path('images/members/' . $photo)
        );
        mysqli_query($conn, "
        UPDATE boa_members
        SET name='$name', title='$title', expertise='$expertise', photo='$photo'
        WHERE id=$id AND club_id=$club_id
        ");
        flash_set('success', 'Advisor updated (photo replaced).');
    } else {
        mysqli_query($conn, "
        UPDATE boa_members
        SET name='$name', title='$title', expertise='$expertise'
        WHERE id=$id AND club_id=$club_id
        ");
        flash_set('success', 'Advisor updated.');
    }

    redirect('admin.php?tab=members');
}

/* ── Gallery: add edition ── */
if (isset($_POST['add_edition'])) {
    $club_id = admin_club_id();
    $event_id = (int)($_POST['event_id'] ?? 0);
    $title = trim($_POST['edition_title'] ?? '');
    $date = trim($_POST['edition_date'] ?? '');
    $location = trim($_POST['edition_location'] ?? '');
    $desc = trim($_POST['edition_description'] ?? '');

    if ($club_id && $event_id && $title !== '' && event_belongs_to_club($conn, $event_id, $club_id)) {
        $stmt = mysqli_prepare(
            $conn,
            'INSERT INTO event_editions (event_id, title, event_date, location, description) VALUES (?, ?, ?, ?, ?)'
        );
        $dateVal = $date !== '' ? $date : date('Y-m-d');
        mysqli_stmt_bind_param($stmt, 'issss', $event_id, $title, $dateVal, $location, $desc);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        flash_set('success', 'Edition added. You can upload photos now.');
    } else {
        flash_set('error', 'Could not add edition. Check the event and title.');
    }
    redirect('admin.php?tab=gallery&event_id=' . $event_id);
}

/* ── Gallery: upload photo ── */
if (isset($_POST['add_gallery_image'])) {
    $club_id = admin_club_id();
    $event_id = (int)($_POST['event_id'] ?? 0);
    $edition_id = (int)($_POST['edition_id'] ?? 0);
    $caption = trim($_POST['caption'] ?? '');
    $file = $_FILES['gallery_image'] ?? null;
    $redirectEdition = $edition_id;

    $ok = $club_id && $event_id && event_belongs_to_club($conn, $event_id, $club_id);

    // Auto-create a default edition if none was chosen / none exists
    if ($ok && $edition_id <= 0) {
        $existing = mysqli_query(
            $conn,
            "SELECT id FROM event_editions WHERE event_id = $event_id ORDER BY id DESC LIMIT 1"
        );
        $row = $existing ? mysqli_fetch_assoc($existing) : null;
        if ($row) {
            $edition_id = (int)$row['id'];
        } else {
            $ev = mysqli_fetch_assoc(mysqli_query($conn, "SELECT title FROM events WHERE id = $event_id LIMIT 1"));
            $autoTitle = trim(($ev['title'] ?? 'Event') . ' Gallery');
            $today = date('Y-m-d');
            $loc = '';
            $desc = 'Photos uploaded from admin panel';
            $stmt = mysqli_prepare(
                $conn,
                'INSERT INTO event_editions (event_id, title, event_date, location, description) VALUES (?, ?, ?, ?, ?)'
            );
            mysqli_stmt_bind_param($stmt, 'issss', $event_id, $autoTitle, $today, $loc, $desc);
            mysqli_stmt_execute($stmt);
            $edition_id = (int)mysqli_insert_id($conn);
            mysqli_stmt_close($stmt);
        }
        $redirectEdition = $edition_id;
    }

    if ($ok && $edition_id > 0) {
        $chk = mysqli_prepare($conn, 'SELECT id FROM event_editions WHERE id = ? AND event_id = ?');
        mysqli_stmt_bind_param($chk, 'ii', $edition_id, $event_id);
        mysqli_stmt_execute($chk);
        $editionOk = mysqli_stmt_get_result($chk)->fetch_assoc();
        mysqli_stmt_close($chk);
        $ok = (bool)$editionOk;
    } else {
        $ok = false;
    }

    if (!$ok) {
        flash_set('error', 'Could not upload. Select one of your club events first.');
        redirect('admin.php?tab=gallery&event_id=' . $event_id);
    }

    if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        flash_set('error', 'Please choose an image file to upload.');
        redirect('admin.php?tab=gallery&event_id=' . $event_id . '&edition_id=' . $redirectEdition);
    }

    if ($file['size'] > 5 * 1024 * 1024) {
        flash_set('error', 'Image must be smaller than 5 MB.');
        redirect('admin.php?tab=gallery&event_id=' . $event_id . '&edition_id=' . $redirectEdition);
    }

    $allowed = [
        'image/jpeg' => 'jpeg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'image/gif'  => 'gif',
    ];
    $mime = mime_content_type($file['tmp_name']);
    if (!isset($allowed[$mime])) {
        flash_set('error', 'Only JPG, PNG, WEBP, or GIF images are allowed.');
        redirect('admin.php?tab=gallery&event_id=' . $event_id . '&edition_id=' . $redirectEdition);
    }

    $folder = root_path('images/events');
    if (!is_dir($folder)) {
        mkdir($folder, 0775, true);
    }
    $name = 'event_' . $event_id . '_' . $edition_id . '_' . uniqid() . '.' . $allowed[$mime];
    $dbPath = 'images/events/' . $name;
    if (!move_uploaded_file($file['tmp_name'], $folder . DIRECTORY_SEPARATOR . $name)) {
        flash_set('error', 'Upload failed. Check folder permissions on images/events.');
        redirect('admin.php?tab=gallery&event_id=' . $event_id . '&edition_id=' . $redirectEdition);
    }

    $stmt = mysqli_prepare(
        $conn,
        'INSERT INTO event_gallery (event_id, edition_id, image, caption, created_at) VALUES (?, ?, ?, ?, NOW())'
    );
    mysqli_stmt_bind_param($stmt, 'iiss', $event_id, $edition_id, $dbPath, $caption);
    if (mysqli_stmt_execute($stmt)) {
        flash_set('success', 'Photo uploaded successfully. It now appears on the event gallery page.');
    } else {
        flash_set('error', 'Photo saved to disk but could not be saved to the database.');
    }
    mysqli_stmt_close($stmt);
    redirect('admin.php?tab=gallery&event_id=' . $event_id . '&edition_id=' . $redirectEdition);
}

/* ── Gallery: delete photo ── */
if (isset($_POST['delete_gallery_image'])) {
    $club_id = admin_club_id();
    $image_id = (int)($_POST['image_id'] ?? 0);
    $event_id = (int)($_POST['event_id'] ?? 0);

    if ($club_id && $image_id && event_belongs_to_club($conn, $event_id, $club_id)) {
        $stmt = mysqli_prepare(
            $conn,
            'SELECT g.image FROM event_gallery g
             JOIN events e ON e.id = g.event_id
             WHERE g.id = ? AND g.event_id = ? AND e.club_id = ?'
        );
        mysqli_stmt_bind_param($stmt, 'iii', $image_id, $event_id, $club_id);
        mysqli_stmt_execute($stmt);
        $row = mysqli_stmt_get_result($stmt)->fetch_assoc();
        mysqli_stmt_close($stmt);

        if ($row) {
            $del = mysqli_prepare($conn, 'DELETE FROM event_gallery WHERE id = ?');
            mysqli_stmt_bind_param($del, 'i', $image_id);
            mysqli_stmt_execute($del);
            mysqli_stmt_close($del);

            $full = root_path($row['image']);
            if (is_file($full) && strpos(str_replace('\\', '/', $row['image']), 'images/events/') === 0) {
                @unlink($full);
            }
            flash_set('success', 'Photo deleted.');
        } else {
            flash_set('error', 'Photo not found or not allowed for your club.');
        }
    } else {
        flash_set('error', 'Could not delete photo.');
    }
    redirect('admin.php?tab=gallery&event_id=' . $event_id);
}

/* ── Gallery: edit existing photo (caption / replace file / move edition) ── */
if (isset($_POST['update_gallery_image'])) {
    $club_id = admin_club_id();
    $image_id = (int)($_POST['image_id'] ?? 0);
    $event_id = (int)($_POST['event_id'] ?? 0);
    $edition_id = (int)($_POST['edition_id'] ?? 0);
    $caption = trim($_POST['caption'] ?? '');
    $file = $_FILES['gallery_image'] ?? null;

    if (!$club_id || !$image_id || !event_belongs_to_club($conn, $event_id, $club_id)) {
        flash_set('error', 'You can only edit photos for your own club events.');
        redirect('admin.php?tab=gallery&event_id=' . $event_id);
    }

    $stmt = mysqli_prepare(
        $conn,
        'SELECT g.id, g.image, g.edition_id FROM event_gallery g
         JOIN events e ON e.id = g.event_id
         WHERE g.id = ? AND g.event_id = ? AND e.club_id = ?'
    );
    mysqli_stmt_bind_param($stmt, 'iii', $image_id, $event_id, $club_id);
    mysqli_stmt_execute($stmt);
    $existing = mysqli_stmt_get_result($stmt)->fetch_assoc();
    mysqli_stmt_close($stmt);

    if (!$existing) {
        flash_set('error', 'Photo not found for your club.');
        redirect('admin.php?tab=gallery&event_id=' . $event_id);
    }

    if ($edition_id <= 0) {
        $edition_id = (int)$existing['edition_id'];
    } else {
        $chk = mysqli_prepare($conn, 'SELECT id FROM event_editions WHERE id = ? AND event_id = ?');
        mysqli_stmt_bind_param($chk, 'ii', $edition_id, $event_id);
        mysqli_stmt_execute($chk);
        $editionOk = mysqli_stmt_get_result($chk)->fetch_assoc();
        mysqli_stmt_close($chk);
        if (!$editionOk) {
            flash_set('error', 'Invalid edition for this event.');
            redirect('admin.php?tab=gallery&event_id=' . $event_id . '&edit_image=' . $image_id);
        }
    }

    $newPath = $existing['image'];
    $replaced = false;

    if ($file && ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
        if ($file['size'] > 5 * 1024 * 1024) {
            flash_set('error', 'Image must be smaller than 5 MB.');
            redirect('admin.php?tab=gallery&event_id=' . $event_id . '&edit_image=' . $image_id);
        }
        $allowed = [
            'image/jpeg' => 'jpeg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
            'image/gif'  => 'gif',
        ];
        $mime = mime_content_type($file['tmp_name']);
        if (!isset($allowed[$mime])) {
            flash_set('error', 'Only JPG, PNG, WEBP, or GIF images are allowed.');
            redirect('admin.php?tab=gallery&event_id=' . $event_id . '&edit_image=' . $image_id);
        }
        $folder = root_path('images/events');
        if (!is_dir($folder)) {
            mkdir($folder, 0775, true);
        }
        $name = 'event_' . $event_id . '_' . $edition_id . '_' . uniqid() . '.' . $allowed[$mime];
        $dbPath = 'images/events/' . $name;
        if (!move_uploaded_file($file['tmp_name'], $folder . DIRECTORY_SEPARATOR . $name)) {
            flash_set('error', 'Could not replace the image file.');
            redirect('admin.php?tab=gallery&event_id=' . $event_id . '&edit_image=' . $image_id);
        }
        $old = root_path($existing['image']);
        if (is_file($old) && strpos(str_replace('\\', '/', $existing['image']), 'images/events/') === 0) {
            @unlink($old);
        }
        $newPath = $dbPath;
        $replaced = true;
    }

    $upd = mysqli_prepare(
        $conn,
        'UPDATE event_gallery SET edition_id = ?, image = ?, caption = ? WHERE id = ? AND event_id = ?'
    );
    mysqli_stmt_bind_param($upd, 'issii', $edition_id, $newPath, $caption, $image_id, $event_id);
    mysqli_stmt_execute($upd);
    mysqli_stmt_close($upd);

    flash_set('success', $replaced ? 'Photo updated and file replaced.' : 'Photo details updated.');
    redirect('admin.php?tab=gallery&event_id=' . $event_id . '&edition_id=' . $edition_id);
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
$edit_bod = null;
$edit_boa = null;
$edit_gallery = null;
$adminClubIdForEdit = admin_club_id();

if (isset($_GET['edit_bod'])) {
    $id = (int)$_GET['edit_bod'];
    if ($adminClubIdForEdit && bod_belongs_to_club($conn, $id, $adminClubIdForEdit)) {
        $result = mysqli_query($conn, "SELECT * FROM bod_members WHERE id=$id AND club_id=$adminClubIdForEdit");
        $edit_bod = mysqli_fetch_assoc($result);
    }
}

if (isset($_GET['edit_boa'])) {
    $id = (int)$_GET['edit_boa'];
    if ($adminClubIdForEdit && boa_belongs_to_club($conn, $id, $adminClubIdForEdit)) {
        $result = mysqli_query($conn, "SELECT * FROM boa_members WHERE id=$id AND club_id=$adminClubIdForEdit");
        $edit_boa = mysqli_fetch_assoc($result);
    }
}

if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    if ($adminClubIdForEdit && event_belongs_to_club($conn, $id, $adminClubIdForEdit)) {
        $result = mysqli_query($conn, "SELECT * FROM events WHERE id=$id AND club_id=$adminClubIdForEdit");
        $edit_event = mysqli_fetch_assoc($result);
    }
}

if (isset($_GET['edit_image'])) {
    $id = (int)$_GET['edit_image'];
    $evId = (int)($_GET['event_id'] ?? 0);
    if ($adminClubIdForEdit && $id && event_belongs_to_club($conn, $evId, $adminClubIdForEdit)) {
        $stmt = mysqli_prepare(
            $conn,
            'SELECT g.* FROM event_gallery g
             JOIN events e ON e.id = g.event_id
             WHERE g.id = ? AND g.event_id = ? AND e.club_id = ?'
        );
        mysqli_stmt_bind_param($stmt, 'iii', $id, $evId, $adminClubIdForEdit);
        mysqli_stmt_execute($stmt);
        $edit_gallery = mysqli_stmt_get_result($stmt)->fetch_assoc();
        mysqli_stmt_close($stmt);
    }
}
?>

<style>
    *, *::before, *::after { box-sizing: border-box; }
    /* Added margin-bottom: 4rem to create gap above footer */
    .admin-page { min-height: 100vh; background: #f5f3ef; padding: 2rem; margin-bottom: 4rem; }
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
    .table-box { background: #fff; border: 0.5px solid #e0ddd6; border-radius: 12px; overflow-x: auto; -webkit-overflow-scrolling: touch; }
    .table-box-header { padding: 1rem 1.5rem; border-bottom: 0.5px solid #e0ddd6; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem; }
    .table-box-header h2 { font-size: 15px; font-weight: 600; color: #1a1a1a; }
    .badge-count { background: #fdecea; color: #7a1028; font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 20px; font-family: 'Segoe UI', sans-serif; }
    .table-scroll { width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; }
    table { width: 100%; border-collapse: collapse; min-width: 640px; }
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
    @media (max-width: 900px) {
        .admin-page { padding: 1.25rem; }
        .admin-header { flex-direction: column; align-items: flex-start; gap: 0.35rem; }
        .form-grid { grid-template-columns: 1fr 1fr; }
    }

    @media (max-width: 600px) {
        .admin-page { padding: 1rem; margin-bottom: 2rem; }
        .admin-header h1 { font-size: 1.3rem; }
        .stats-row { grid-template-columns: 1fr 1fr; }
        .stat-box { padding: 1rem; gap: 0.75rem; }
        .stat-val { font-size: 1.3rem; }
        .tab-btn { padding: 7px 12px; font-size: 12px; }
        .table-box-header { padding: 0.85rem 1rem; }
        .form-box { padding: 1.1rem; }
        .form-grid { grid-template-columns: 1fr; }
        th, td { padding: 8px 10px; font-size: 12px; }
    }

    @media (max-width: 400px) {
        .stats-row { grid-template-columns: 1fr; }
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

        <?php echo flash_render(); ?>

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
            <a href="admin.php?tab=gallery" class="tab-btn <?php echo $tab=='gallery' ? 'active':''; ?>">&#128247; Gallery</a>
            <a href="admin.php?tab=members" class="tab-btn <?php echo $tab=='members' ? 'active':''; ?>">👥 Members</a>
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
        <div style="background:#fdecea; color:#7a1028; padding:12px; margin-bottom:15px; border-radius:8px;">
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
            'id' => '',
            'title' => '',
            'description' => '',
            'event_date' => '',
            'event_time' => '',
            'location' => '',
            'status' => 'upcoming',
            'image' => '',
        ];

        if (!empty($edit_event)) {
            $editing = true;
            $event = $edit_event;
        }
        ?>
        <div class="form-box">
            <h2>
                <?php
                if($editing){
                    echo "Edit Event (your club only)";
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
                        value="<?php echo $clubData['id'] ?? ''; ?>">

                        <label>Club</label>
                        <input
                        type="text"
                        value="<?php echo htmlspecialchars($clubData['name'] ?? ''); ?>"
                        readonly>
                    </div>

                    <input
                    type="hidden"
                    name="event_id"
                    value="<?php echo htmlspecialchars((string)$event['id']); ?>">

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
                        value="<?php echo htmlspecialchars((string)$event['event_date']); ?>"
                        required>
                    </div>
                    <div class="form-group">
                        <label>Time</label>
                        <input
                        type="time"
                        name="event_time"
                        value="<?php echo htmlspecialchars((string)$event['event_time']); ?>"
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
                            <option value="upcoming" <?php if(($event['status']) == "upcoming") echo "selected"; ?>>Upcoming</option>
                            <option value="ongoing" <?php if(($event['status']) == "ongoing") echo "selected"; ?>>Ongoing</option>
                            <option value="completed" <?php if(($event['status']) == "completed") echo "selected"; ?>>Completed</option>
                        </select>
                    </div>
                    <div class="form-group" style="grid-column: 1/-1;">
                        <label>Description</label>
                        <textarea
                        name="description"
                        rows="3"><?php echo htmlspecialchars($event['description']); ?></textarea>
                    </div>
                    <div class="form-group" style="grid-column:1/-1;">
                        <label><?php echo $editing ? 'Replace cover image (optional)' : 'Event Image'; ?></label>
                        <?php if ($editing && !empty($event['image'])): ?>
                            <div style="margin-bottom:8px;">
                                <img src="<?php echo htmlspecialchars(media_url($event['image'])); ?>"
                                     alt=""
                                     style="width:160px;height:100px;object-fit:cover;border-radius:8px;">
                            </div>
                        <?php endif; ?>
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
                    <th>Gallery</th>
                    <th>Edit</th>
                    <th>Delete</th>
                </tr>
                <?php while($e = mysqli_fetch_assoc($events)): ?>
                <tr>
                    <td>
                    <?php if(!empty($e['image'])): ?>
                        <img src="<?php echo htmlspecialchars(media_url($e['image'])); ?>" style="width:80px;height:55px;object-fit:cover;border-radius:8px;" alt="">
                    <?php else: ?>
                        No Image
                    <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($e['title']); ?></td>
                    <td><?php echo htmlspecialchars($e['club_name']); ?></td>
                    <td><?php echo date('d M Y', strtotime($e['event_date'])); ?></td>
                    <td><?php echo htmlspecialchars($e['location']); ?></td>
                    <td><span class="badge badge-<?php echo $e['status']; ?>"><?php echo ucfirst($e['status']); ?></span></td>
                    <td>
                        <a href="admin.php?tab=gallery&event_id=<?php echo (int)$e['id']; ?>" class="btn-submit" style="padding:6px 12px;margin:0;text-decoration:none;">
                            Photos
                        </a>
                    </td>
                    <td>
                        <a href="admin.php?tab=events&edit=<?php echo $e['id']; ?>" class="btn-submit" style="padding:6px 12px;margin:0;text-decoration:none;">
                            Edit
                        </a>
                    </td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="event_id" value="<?php echo $e['id']; ?>">
                            <button type="submit" name="delete_event" class="btn-delete"
                                    data-confirm="Delete this event? This cannot be undone."
                                    data-confirm-title="Delete event">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
            <?php endif; ?>
        </div>

        <?php elseif($tab == 'gallery'): ?>
        <?php
        $adminClubId = admin_club_id();
        $selected_event_id = (int)($_GET['event_id'] ?? 0);
        $selected_edition_id = (int)($_GET['edition_id'] ?? 0);

        $club_events = mysqli_query(
            $conn,
            "SELECT id, title FROM events WHERE club_id = $adminClubId ORDER BY event_date DESC, id DESC"
        );

        if ($selected_event_id && !event_belongs_to_club($conn, $selected_event_id, $adminClubId)) {
            $selected_event_id = 0;
            $selected_edition_id = 0;
        }

        $editions = [];
        if ($selected_event_id) {
            $edRes = mysqli_query(
                $conn,
                "SELECT * FROM event_editions WHERE event_id = $selected_event_id ORDER BY event_date DESC, id DESC"
            );
            while ($ed = mysqli_fetch_assoc($edRes)) {
                $editions[] = $ed;
            }
            if ($selected_edition_id) {
                $found = false;
                foreach ($editions as $ed) {
                    if ((int)$ed['id'] === $selected_edition_id) {
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    $selected_edition_id = 0;
                }
            }
        }

        $gallery_rows = [];
        if ($selected_event_id) {
            $gSql = "SELECT g.*, ed.title AS edition_title
                     FROM event_gallery g
                     JOIN event_editions ed ON ed.id = g.edition_id
                     WHERE g.event_id = $selected_event_id";
            if ($selected_edition_id) {
                $gSql .= " AND g.edition_id = $selected_edition_id";
            }
            $gSql .= " ORDER BY g.id DESC";
            $gRes = mysqli_query($conn, $gSql);
            while ($g = mysqli_fetch_assoc($gRes)) {
                $gallery_rows[] = $g;
            }
        }
        ?>

        <div class="form-box">
            <h2>&#128247; Upload Event Photos</h2>
            <div class="app-flash app-flash-success" style="margin:0 0 1rem;max-width:none;">
                How to upload: <strong>1)</strong> Select your event &nbsp;→&nbsp;
                <strong>2)</strong> Choose an image file &nbsp;→&nbsp;
                <strong>3)</strong> Click <em>Upload Photo</em>
            </div>
            <p style="font-family:'Segoe UI',sans-serif;font-size:13px;color:#666;margin:0 0 1rem;">
                Signed in as <strong><?php echo htmlspecialchars($_SESSION['club_name'] ?? 'your club'); ?></strong>.
                Photos only apply to this club’s events and show on the club/event gallery pages.
                <?php if ($adminClubId): ?>
                    <a href="<?php echo htmlspecialchars(url('club_detail.php?id=' . $adminClubId)); ?>" target="_blank">Open club page &#8599;</a>
                <?php endif; ?>
            </p>

            <form method="GET" style="margin-bottom:1.25rem;">
                <input type="hidden" name="tab" value="gallery">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Step 1 — Select Event</label>
                        <select name="event_id" required onchange="this.form.submit()">
                            <option value="">— Choose an event —</option>
                            <?php
                            mysqli_data_seek($club_events, 0);
                            while ($ev = mysqli_fetch_assoc($club_events)): ?>
                                <option value="<?php echo (int)$ev['id']; ?>" <?php echo $selected_event_id === (int)$ev['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($ev['title']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
            </form>

            <?php if (!$selected_event_id): ?>
                <div class="empty-msg">Select an event above to enable the upload form.</div>
            <?php else: ?>

            <div style="display:flex;gap:10px;align-items:center;margin-bottom:1rem;flex-wrap:wrap;">
                <a href="<?php echo htmlspecialchars(url('event_gallery.php?id=' . $selected_event_id)); ?>" target="_blank" class="btn-submit" style="margin:0;text-decoration:none;">
                    View public gallery &#8599;
                </a>
            </div>

            <div style="border:2px dashed #c9a4ad;border-radius:12px;padding:1.25rem;background:#fffafb;margin-bottom:1.5rem;">
                <h3 style="font-size:15px;margin:0 0 0.75rem;color:#7a1028;">Step 2 — Upload photo</h3>
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="event_id" value="<?php echo $selected_event_id; ?>">
                    <div class="form-grid">
                        <?php if (count($editions) > 0): ?>
                        <div class="form-group">
                            <label>Edition (optional — auto-created if empty)</label>
                            <select name="edition_id">
                                <option value="0">Auto / latest edition</option>
                                <?php foreach ($editions as $ed): ?>
                                    <option value="<?php echo (int)$ed['id']; ?>" <?php echo $selected_edition_id === (int)$ed['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($ed['title']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php else: ?>
                            <input type="hidden" name="edition_id" value="0">
                        <?php endif; ?>
                        <div class="form-group">
                            <label>Choose image file (JPG, PNG, WEBP — max 5 MB)</label>
                            <input type="file" name="gallery_image" accept=".jpg,.jpeg,.png,.webp,image/*" required
                                   style="padding:10px;border:1px solid #ddd;border-radius:8px;background:#fff;">
                        </div>
                        <div class="form-group" style="grid-column:1/-1;">
                            <label>Caption (optional)</label>
                            <input type="text" name="caption" maxlength="255" placeholder="e.g. Opening ceremony">
                        </div>
                    </div>
                    <button type="submit" name="add_gallery_image" class="btn-submit" style="margin-top:0.75rem;">
                        Upload Photo
                    </button>
                </form>
            </div>

            <details style="margin-top:0.5rem;">
                <summary style="cursor:pointer;font-weight:600;font-family:'Segoe UI',sans-serif;color:#555;">
                    Optional: add a named edition (year / run)
                </summary>
                <form method="POST" style="margin-top:1rem;">
                    <input type="hidden" name="event_id" value="<?php echo $selected_event_id; ?>">
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Edition Title</label>
                            <input type="text" name="edition_title" placeholder="e.g. Apex Day 2026" required>
                        </div>
                        <div class="form-group">
                            <label>Date</label>
                            <input type="date" name="edition_date">
                        </div>
                        <div class="form-group">
                            <label>Location</label>
                            <input type="text" name="edition_location" placeholder="Apex College Premises">
                        </div>
                        <div class="form-group" style="grid-column:1/-1;">
                            <label>Description</label>
                            <textarea name="edition_description" rows="2" placeholder="Optional short description"></textarea>
                        </div>
                    </div>
                    <button type="submit" name="add_edition" class="btn-submit">Add Edition</button>
                </form>
            </details>

            <?php endif; ?>
        </div>

        <?php if ($selected_event_id && !empty($edit_gallery)): ?>
        <div class="form-box" style="border-color:#7a1028;">
            <h2>Edit existing photo</h2>
            <div style="display:flex;gap:1.25rem;flex-wrap:wrap;align-items:flex-start;margin-bottom:1rem;">
                <img src="<?php echo htmlspecialchars(media_url($edit_gallery['image'])); ?>"
                     alt=""
                     style="width:180px;height:120px;object-fit:cover;border-radius:10px;border:1px solid #e0ddd6;">
                <div style="flex:1;min-width:220px;font-family:'Segoe UI',sans-serif;font-size:13px;color:#555;">
                    Replace the file and/or update the caption. Only photos from
                    <strong><?php echo htmlspecialchars($_SESSION['club_name'] ?? 'your club'); ?></strong> can be edited here.
                </div>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="image_id" value="<?php echo (int)$edit_gallery['id']; ?>">
                <input type="hidden" name="event_id" value="<?php echo $selected_event_id; ?>">
                <div class="form-grid">
                    <?php if (count($editions) > 0): ?>
                    <div class="form-group">
                        <label>Edition</label>
                        <select name="edition_id">
                            <?php foreach ($editions as $ed): ?>
                                <option value="<?php echo (int)$ed['id']; ?>"
                                    <?php echo (int)$edit_gallery['edition_id'] === (int)$ed['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($ed['title']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php else: ?>
                        <input type="hidden" name="edition_id" value="<?php echo (int)$edit_gallery['edition_id']; ?>">
                    <?php endif; ?>
                    <div class="form-group">
                        <label>Replace image (optional)</label>
                        <input type="file" name="gallery_image" accept=".jpg,.jpeg,.png,.webp,image/*">
                    </div>
                    <div class="form-group" style="grid-column:1/-1;">
                        <label>Caption</label>
                        <input type="text" name="caption" maxlength="255"
                               value="<?php echo htmlspecialchars($edit_gallery['caption'] ?? ''); ?>">
                    </div>
                </div>
                <div style="display:flex;gap:10px;margin-top:0.75rem;flex-wrap:wrap;">
                    <button type="submit" name="update_gallery_image" class="btn-submit">Save photo changes</button>
                    <a href="admin.php?tab=gallery&event_id=<?php echo $selected_event_id; ?>"
                       class="btn-delete" style="text-decoration:none;display:inline-flex;align-items:center;">Cancel</a>
                </div>
            </form>
        </div>
        <?php endif; ?>

        <?php if ($selected_event_id): ?>
        <div class="table-box" style="margin-top:1.5rem;">
            <div class="table-box-header">
                <h2>Existing gallery images (your club only)</h2>
                <span class="badge-count"><?php echo count($gallery_rows); ?> photos</span>
            </div>
            <?php if (count($gallery_rows) === 0): ?>
                <div class="empty-msg">No photos yet for this event.</div>
            <?php else: ?>
            <table>
                <tr>
                    <th>Photo</th>
                    <th>Edition</th>
                    <th>Caption</th>
                    <th>Added</th>
                    <th>Edit</th>
                    <th>Delete</th>
                </tr>
                <?php foreach ($gallery_rows as $g): ?>
                <tr>
                    <td>
                        <img src="<?php echo htmlspecialchars(media_url($g['image'])); ?>"
                             alt=""
                             style="width:90px;height:60px;object-fit:cover;border-radius:8px;">
                    </td>
                    <td><?php echo htmlspecialchars($g['edition_title']); ?></td>
                    <td><?php echo htmlspecialchars($g['caption'] ?: '—'); ?></td>
                    <td><?php echo date('d M Y', strtotime($g['created_at'])); ?></td>
                    <td>
                        <a href="admin.php?tab=gallery&event_id=<?php echo $selected_event_id; ?>&edit_image=<?php echo (int)$g['id']; ?>"
                           class="btn-submit" style="padding:6px 12px;margin:0;text-decoration:none;">Edit</a>
                    </td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="image_id" value="<?php echo (int)$g['id']; ?>">
                            <input type="hidden" name="event_id" value="<?php echo $selected_event_id; ?>">
                            <button type="submit" name="delete_gallery_image" class="btn-delete"
                                    data-confirm="Delete this photo from the gallery?"
                                    data-confirm-title="Delete photo">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
            <?php endif; ?>
        </div>
        <?php else: ?>
        <?php
        // Club-wide filtered photo overview when no event is selected
        $allClubPhotos = [];
        if ($adminClubId) {
            $allRes = mysqli_query(
                $conn,
                "SELECT g.id, g.event_id, g.image, g.caption, g.created_at,
                        e.title AS event_title, ed.title AS edition_title
                 FROM event_gallery g
                 JOIN events e ON e.id = g.event_id
                 JOIN event_editions ed ON ed.id = g.edition_id
                 WHERE e.club_id = $adminClubId
                 ORDER BY g.id DESC
                 LIMIT 50"
            );
            while ($row = mysqli_fetch_assoc($allRes)) {
                $allClubPhotos[] = $row;
            }
        }
        ?>
        <div class="table-box" style="margin-top:1.5rem;">
            <div class="table-box-header">
                <h2>All photos for <?php echo htmlspecialchars($_SESSION['club_name'] ?? 'your club'); ?></h2>
                <span class="badge-count"><?php echo count($allClubPhotos); ?> shown</span>
            </div>
            <?php if (count($allClubPhotos) === 0): ?>
                <div class="empty-msg">No gallery photos for your club yet. Select an event above to upload.</div>
            <?php else: ?>
            <table>
                <tr>
                    <th>Photo</th>
                    <th>Event</th>
                    <th>Edition</th>
                    <th>Caption</th>
                    <th>Edit</th>
                </tr>
                <?php foreach ($allClubPhotos as $g): ?>
                <tr>
                    <td>
                        <img src="<?php echo htmlspecialchars(media_url($g['image'])); ?>"
                             alt=""
                             style="width:90px;height:60px;object-fit:cover;border-radius:8px;">
                    </td>
                    <td><?php echo htmlspecialchars($g['event_title']); ?></td>
                    <td><?php echo htmlspecialchars($g['edition_title']); ?></td>
                    <td><?php echo htmlspecialchars($g['caption'] ?: '—'); ?></td>
                    <td>
                        <a href="admin.php?tab=gallery&event_id=<?php echo (int)$g['event_id']; ?>&edit_image=<?php echo (int)$g['id']; ?>"
                           class="btn-submit" style="padding:6px 12px;margin:0;text-decoration:none;">Edit</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php elseif($tab == 'votes'): ?>
        <div class="form-box">
            <h2>&#128221; Create New Poll</h2>
            <form method="POST">
                <div class="form-group">
                    <label>Poll Question</label>
                    <input type="text" name="question" placeholder="Enter poll question" required>
                </div>
                <div class="form-group">
                    <label>Option 1</label>
                    <input type="text" name="options[]" required>
                </div>
                <div class="form-group">
                    <label>Option 2</label>
                    <input type="text" name="options[]" required>
                </div>
                <div class="form-group">
                    <label>Option 3</label>
                    <input type="text" name="options[]">
                </div>
                <div class="form-group">
                    <label>Option 4</label>
                    <input type="text" name="options[]">
                </div>
                <button type="submit" name="create_poll" class="btn-submit">
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
                        <input type="hidden" name="poll_id" value="<?php echo $poll['id']; ?>">
                        <button type="submit" name="delete_poll" class="btn-delete"
                                data-confirm="Delete this poll permanently?"
                                data-confirm-title="Delete poll">Delete Poll</button>
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

        <?php elseif($tab == 'members'): ?>
        <div class="form-box">
            <h2>Add New BOD Member</h2>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="name" required>
                </div>
                <div class="form-group">
                    <label>Position</label>
                    <input type="text" name="position" required>
                </div>
                <div class="form-group">
                    <label>Bio</label>
                    <textarea name="bio" rows="4"></textarea>
                </div>
                <div class="form-group">
                    <label>Photo</label>
                    <input type="file" name="photo" required>
                </div>
                <button type="submit" name="add_bod" class="btn-submit">
                    Add Member
                </button>
            </form>
        </div>

        <?php if($edit_bod): ?>
        <div class="form-box">
            <h2>Edit BOD Member</h2>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="member_id" value="<?php echo $edit_bod['id']; ?>">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($edit_bod['name']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Position</label>
                    <input type="text" name="position" value="<?php echo htmlspecialchars($edit_bod['position']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Bio</label>
                    <textarea name="bio" rows="4"><?php echo htmlspecialchars($edit_bod['bio']); ?></textarea>
                </div>
                <div class="form-group">
                    <label>Photo</label>
                    <input type="file" name="photo">
                </div>
                <button type="submit" name="update_bod" class="btn-submit">
                    Update Member
                </button>
            </form>
        </div>
        <?php endif; ?>

        <div class="table-box">
            <div class="table-box-header">
                <h2>👥 Board of Directors</h2>
            </div>
            <?php
            $clubRow = mysqli_fetch_assoc(
                mysqli_query($conn,
                "SELECT id FROM clubs WHERE name='$club'")
            );
            $club_id = $clubRow['id'];
            $bod = mysqli_query($conn, "SELECT * FROM bod_members WHERE club_id=$club_id ORDER BY id");
            ?>
            <table>
                <tr>
                    <th>Photo</th>
                    <th>Name</th>
                    <th>Position</th>
                    <th>Edit</th>
                    <th>Delete</th>
                </tr>
                <?php while($member = mysqli_fetch_assoc($bod)): ?>
                <tr>
                    <td>
                        <img src="<?php echo htmlspecialchars(member_photo_url($member['photo'])); ?>" style="width:80px;height:80px;object-fit:cover;border-radius:50%;" alt="">
                    </td>
                    <td><?php echo htmlspecialchars($member['name']); ?></td>
                    <td><?php echo htmlspecialchars($member['position']); ?></td>
                    <td>
                        <a href="admin.php?tab=members&edit_bod=<?php echo $member['id']; ?>" class="btn-submit">Edit</a>
                    </td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="bod_id" value="<?php echo $member['id']; ?>">
                            <button class="btn-delete" name="delete_bod">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>

        <div class="form-box">
            <h2>Add New BOA Member</h2>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="boa_name" required>
                </div>
                <div class="form-group">
                    <label>Title</label>
                    <input type="text" name="boa_title" required>
                </div>
                <div class="form-group">
                    <label>Expertise</label>
                    <textarea name="boa_expertise" rows="4"></textarea>
                </div>
                <div class="form-group">
                    <label>Photo</label>
                    <input type="file" name="boa_photo" required>
                </div>
                <button type="submit" name="add_boa" class="btn-submit">
                    Add BOA Member
                </button>
            </form>
        </div>

        <?php if($edit_boa): ?>
        <div class="form-box">
            <h2>Edit BOA Member</h2>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="boa_member_id" value="<?php echo $edit_boa['id']; ?>">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="boa_name" value="<?php echo htmlspecialchars($edit_boa['name']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Title</label>
                    <input type="text" name="boa_title" value="<?php echo htmlspecialchars($edit_boa['title']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Expertise</label>
                    <textarea name="boa_expertise" rows="4"><?php echo htmlspecialchars($edit_boa['expertise']); ?></textarea>
                </div>
                <div class="form-group">
                    <label>Photo</label>
                    <input type="file" name="boa_photo">
                </div>
                <button type="submit" name="update_boa" class="btn-submit">
                    Update BOA Member
                </button>
            </form>
        </div>
        <?php endif; ?>

        <div class="table-box">
            <div class="table-box-header">
                <h2>👥 Board of Advisors</h2>
            </div>
            <?php
            $boa = mysqli_query($conn, "SELECT * FROM boa_members WHERE club_id=$club_id ORDER BY id");
            ?>
            <table>
                <tr>
                    <th>Photo</th>
                    <th>Name</th>
                    <th>Title</th>
                    <th>Edit</th>
                    <th>Delete</th>
                </tr>
                <?php while($member=mysqli_fetch_assoc($boa)): ?>
                <tr>
                    <td>
                        <img src="<?php echo htmlspecialchars(member_photo_url($member['photo'])); ?>" style="width:80px;height:80px;object-fit:cover;border-radius:50%;" alt="">
                    </td>
                    <td><?php echo htmlspecialchars($member['name']); ?></td>
                    <td><?php echo htmlspecialchars($member['title']); ?></td>
                    <td>
                        <a href="admin.php?tab=members&edit_boa=<?php echo $member['id']; ?>" class="btn-submit">Edit</a>
                    </td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="boa_id" value="<?php echo $member['id']; ?>">
                            <button type="submit" name="delete_boa" class="btn-delete">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>

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
                <input type="text" name="club" value="<?php echo htmlspecialchars($_SESSION['club_name']); ?>" readonly>
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

<?php include 'includes/footer.php'; ?>
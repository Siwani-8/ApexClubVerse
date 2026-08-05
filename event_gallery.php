<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/includes/db.php';

$isLoggedIn = !empty($_SESSION['user_logged_in']);
$isAdmin = $isLoggedIn && (($_SESSION['user_role'] ?? '') === 'admin');

function h($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function findImage($path) {
    $path = ltrim(str_replace('\\', '/', (string)$path), '/');

    if ($path && is_file(root_path($path))) {
        return url($path);
    }

    /* Fix old names such as apex2023_2.jpg */
    if (preg_match('/apex2023_(\d+)\.(jpg|jpeg|png)$/i', basename($path), $m)) {
        $fallback = 'images/events/apexday' . $m[1] . '.jpeg';
        if (is_file(root_path($fallback))) {
            return url($fallback);
        }
    }

    $resolved = media_url($path);
    return $resolved !== '' ? $resolved : null;
}

$event_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$event_id) {
    exit('Invalid event ID.');
}

$error = '';

/* Add a new image without replacing old images */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_image'])) {

    if (!$isLoggedIn) {
        http_response_code(401);
        $error = 'Please log in as an admin to edit this event.';
    } elseif (!$isAdmin) {
        http_response_code(403);
        $error = 'Only an admin can edit event images.';
    } else {
        $edition_id = (int)($_POST['edition_id'] ?? 0);
        $caption = trim($_POST['caption'] ?? '');
        $file = $_FILES['image'] ?? null;

        $stmt = mysqli_prepare(
            $conn,
            'SELECT id FROM event_editions WHERE id = ? AND event_id = ?'
        );

        mysqli_stmt_bind_param($stmt, 'ii', $edition_id, $event_id);
        mysqli_stmt_execute($stmt);
        $editionExists = mysqli_stmt_get_result($stmt)->fetch_assoc();
        mysqli_stmt_close($stmt);

        if (!$editionExists) {
            $error = 'Invalid event edition.';
        } elseif (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            $error = 'Please select an image.';
        } elseif ($file['size'] > 5 * 1024 * 1024) {
            $error = 'Image must be smaller than 5 MB.';
        } else {
            $allowed = [
                'image/jpeg' => 'jpeg',
                'image/png'  => 'png',
                'image/webp' => 'webp'
            ];

            $mime = mime_content_type($file['tmp_name']);

            if (!isset($allowed[$mime])) {
                $error = 'Only JPG, PNG and WEBP images are allowed.';
            } else {
                $folder = root_path('images/events');

                if (!is_dir($folder)) {
                    mkdir($folder, 0775, true);
                }

                $name = 'event_' . $event_id . '_' . $edition_id . '_' .
                        uniqid() . '.' . $allowed[$mime];

                $dbPath = 'images/events/' . $name;
                $fullPath = $folder . DIRECTORY_SEPARATOR . $name;

                if (!move_uploaded_file($file['tmp_name'], $fullPath)) {
                    $error = 'Image upload failed.';
                } else {
                    $stmt = mysqli_prepare(
                        $conn,
                        'INSERT INTO event_gallery
                        (event_id, edition_id, image, caption, created_at)
                        VALUES (?, ?, ?, ?, NOW())'
                    );

                    mysqli_stmt_bind_param(
                        $stmt,
                        'iiss',
                        $event_id,
                        $edition_id,
                        $dbPath,
                        $caption
                    );

                    if (mysqli_stmt_execute($stmt)) {
                        mysqli_stmt_close($stmt);
                        redirect('event_gallery.php?id=' . $event_id . '&uploaded=1');
                    }

                    mysqli_stmt_close($stmt);

                    if (file_exists($fullPath)) {
                        unlink($fullPath);
                    }

                    $error = 'Could not save the image.';
                }
            }
        }
    }
}

/* Main event */
$stmt = mysqli_prepare(
    $conn,
    'SELECT id, title, description FROM events WHERE id = ?'
);
mysqli_stmt_bind_param($stmt, 'i', $event_id);
mysqli_stmt_execute($stmt);
$event = mysqli_stmt_get_result($stmt)->fetch_assoc();
mysqli_stmt_close($stmt);

if (!$event) {
    exit('Event not found.');
}

/* Event editions */
$stmt = mysqli_prepare(
    $conn,
    'SELECT id, title, event_date, location, description
     FROM event_editions
     WHERE event_id = ?
     ORDER BY event_date DESC'
);
mysqli_stmt_bind_param($stmt, 'i', $event_id);
mysqli_stmt_execute($stmt);
$editions = mysqli_stmt_get_result($stmt)->fetch_all(MYSQLI_ASSOC);
mysqli_stmt_close($stmt);

/* All gallery images fetched once */
$stmt = mysqli_prepare(
    $conn,
    'SELECT edition_id, image, caption
     FROM event_gallery
     WHERE event_id = ?
     ORDER BY id'
);
mysqli_stmt_bind_param($stmt, 'i', $event_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$gallery = [];

while ($photo = mysqli_fetch_assoc($result)) {
    $gallery[$photo['edition_id']][] = $photo;
}

mysqli_stmt_close($stmt);
include __DIR__ . '/includes/header.php';
?>

<style>
*{box-sizing:border-box}
body{background:#f7f8fc}
.event-container{max-width:1100px;margin:auto;padding:40px 20px}
.event-hero{min-height:280px;margin-bottom:45px;border-radius:25px;overflow:hidden;position:relative;background:url('<?php echo h(url('images/events/musical1.jpeg')); ?>') center/cover no-repeat}
.hero-overlay{position:absolute;inset:0;padding:30px;background:rgba(0,0,0,.55);color:#fff;text-align:center;display:flex;flex-direction:column;justify-content:center;align-items:center}
.hero-overlay h1{margin:0 0 12px;font-size:clamp(2rem,5vw,3rem)}
.hero-overlay p{max-width:800px;margin:0;line-height:1.7}
.message{max-width:850px;margin:0 auto 25px;padding:14px 18px;border-radius:10px}
.success{background:#d4edda;color:#155724}
.error{background:#f8d7da;color:#721c24}
.edition{margin-bottom:60px}
.edition-title{text-align:center;margin-bottom:20px}
.edition-title h2{margin-bottom:8px}
.edition-title p{max-width:750px;margin:auto;color:#666}
.event-details{max-width:850px;margin:25px auto;padding:22px 30px;border-radius:20px;background:#fff;box-shadow:0 8px 25px rgba(0,0,0,.08);display:flex;justify-content:center;gap:60px}
.detail{display:flex;align-items:center;gap:15px}
.icon{width:48px;height:48px;border-radius:14px;background:#ffe9ec;display:grid;place-items:center;font-size:21px}
.detail small{display:block;color:#777}
.detail h3{margin:3px 0 0;font-size:18px}
.upload-box{max-width:850px;margin:0 auto 30px;padding:20px;border-radius:18px;background:#fff;box-shadow:0 5px 18px rgba(0,0,0,.07)}
.upload-form{display:grid;grid-template-columns:1fr 1fr auto;gap:12px;align-items:end}
.form-group label{display:block;margin-bottom:6px;font-weight:600}
.form-group input{width:100%;min-height:43px;padding:9px 11px;border:1px solid #ddd;border-radius:9px}
.upload-btn{min-height:43px;padding:10px 20px;border:0;border-radius:9px;color:#fff;background:#ef5b68;cursor:pointer}
.gallery{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:25px}
.gallery-item{padding:10px;border-radius:18px;background:#fff;box-shadow:0 5px 18px rgba(0,0,0,.08)}
.gallery-item img{width:100%;height:280px;display:block;object-fit:cover;border-radius:13px}
.caption{padding:13px 8px 7px;text-align:center;font-size:17px;font-weight:600}
.empty,.missing{padding:25px;border:2px dashed #ddd;border-radius:14px;color:#777;text-align:center;background:#fff}
.missing{min-height:180px;display:grid;place-items:center}
@media(max-width:900px){
    .event-container{padding:28px 16px}
    .event-hero{min-height:220px;border-radius:18px;margin-bottom:30px}
    .hero-overlay{padding:20px 16px}
    .event-details{gap:36px;padding:18px 20px}
    .gallery{grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:18px}
}
@media(max-width:700px){
    .event-container{padding:20px 14px}
    .event-hero{min-height:200px;border-radius:14px}
    .hero-overlay p{font-size:0.92rem;line-height:1.55}
    .event-details{flex-direction:column;gap:20px;padding:16px}
    .upload-form{grid-template-columns:1fr}
    .gallery{grid-template-columns:1fr;gap:16px}
    .gallery-item img{height:220px}
}
@media(max-width:480px){
    .event-container{padding:16px 12px}
    .edition{margin-bottom:40px}
    .detail h3{font-size:16px}
}
</style>

<div class="event-container">

    <?php if (isset($_GET['uploaded'])): ?>
        <div class="message success">Image added successfully.</div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="message error"><?= h($error) ?></div>
    <?php endif; ?>

    <section class="event-hero">
        <div class="hero-overlay">
            <h1><?= h($event['title']) ?></h1>

            <?php if ($event['description']): ?>
                <p><?= nl2br(h($event['description'])) ?></p>
            <?php endif; ?>
        </div>
    </section>

    <?php if (!$editions): ?>
        <div class="empty">No event editions found.</div>
    <?php endif; ?>

    <?php foreach ($editions as $edition): ?>
        <?php
        $edition_id = (int)$edition['id'];
        $photos = $gallery[$edition_id] ?? [];
        ?>

        <section class="edition">

            <div class="edition-title">
                <h2><?= h($edition['title']) ?></h2>

                <?php if ($edition['description']): ?>
                    <p><?= nl2br(h($edition['description'])) ?></p>
                <?php endif; ?>
            </div>

            <div class="event-details">
                <div class="detail">
                    <div class="icon">📅</div>
                    <div>
                        <small>Date</small>
                        <h3><?= h(date('F j, Y', strtotime($edition['event_date']))) ?></h3>
                    </div>
                </div>

                <div class="detail">
                    <div class="icon">📍</div>
                    <div>
                        <small>Location</small>
                        <h3><?= h($edition['location']) ?></h3>
                    </div>
                </div>
            </div>

            <?php if ($isAdmin): ?>

                <div class="upload-box">
                    <h3>Add image to <?= h($edition['title']) ?></h3>

                    <form method="POST"
                          enctype="multipart/form-data"
                          class="upload-form">

                        <input type="hidden"
                               name="edition_id"
                               value="<?= $edition_id ?>">

                        <div class="form-group">
                            <label>Image</label>

                            <input type="file"
                                   name="image"
                                   accept=".jpg,.jpeg,.png,.webp"
                                   required>
                        </div>

                        <div class="form-group">
                            <label>Caption</label>

                            <input type="text"
                                   name="caption"
                                   maxlength="255"
                                   placeholder="Image caption">
                        </div>

                        <button type="submit"
                                name="add_image"
                                class="upload-btn">
                            Add Image
                        </button>
                    </form>
                </div>

            <?php endif; ?>

            <?php if ($photos): ?>
                <div class="gallery">

                    <?php foreach ($photos as $photo): ?>
                        <?php $imagePath = findImage($photo['image']); ?>

                        <div class="gallery-item">

                            <?php if ($imagePath): ?>
                                <img
                                    src="<?= h($imagePath) ?>"
                                    alt="<?= h($photo['caption'] ?: $edition['title']) ?>"
                                    loading="lazy"
                                >
                            <?php else: ?>
                                <div class="missing">
                                    Image not found: <?= h(basename($photo['image'])) ?>
                                </div>
                            <?php endif; ?>

                            <?php if ($photo['caption']): ?>
                                <div class="caption"><?= h($photo['caption']) ?></div>
                            <?php endif; ?>

                        </div>
                    <?php endforeach; ?>

                </div>
            <?php else: ?>
                <div class="empty">No images added for this edition.</div>
            <?php endif; ?>

        </section>
    <?php endforeach; ?>

</div>

<?php include __DIR__ . '/includes/footer.php'; ?>

<?php
session_start();

if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

include 'db.php';
include 'header.php';

$message = "";

if (isset($_POST['apply'])) {
    $name = mysqli_real_escape_string($conn, $_POST['student_name']);
    $email = mysqli_real_escape_string($conn, $_POST['student_email']);
    $faculty = mysqli_real_escape_string($conn, $_POST['faculty']);
    $semester = mysqli_real_escape_string($conn, $_POST['semester']);
    $interest = mysqli_real_escape_string($conn, $_POST['interest']);
    $reasons = mysqli_real_escape_string($conn, $_POST['reasons']);

    $clubs = isset($_POST['selected_club']) ? $_POST['selected_club'] : [];
    if(empty($clubs)){
    $message = "<div class='alert error'>
    Please select at least one club.
    </div>";
}else{
    $success = true;
$inserted = 0;

foreach ($clubs as $club) {

    $club = mysqli_real_escape_string($conn, $club);

    $check = mysqli_query($conn,"
        SELECT id
        FROM registrations
        WHERE student_email='$email'
        AND selected_club='$club'
        LIMIT 1
    ");

    if(mysqli_num_rows($check)==0){

        $query="
        INSERT INTO registrations
        (
            student_name,
            student_email,
            faculty,
            semester,
            selected_club,
            interest,
            reasons
        )
        VALUES
        (
            '$name',
            '$email',
            '$faculty',
            '$semester',
            '$club',
            '$interest',
            '$reasons'
        )";

        if(mysqli_query($conn,$query)){
            $inserted++;
        }else{
            $success=false;
        }
    }
}

if($success){

    if($inserted>0){

        $message="<div class='alert success'>
        ✓ Application submitted successfully!
        </div>";

    }else{

        $message="<div class='alert error'>
        You have already applied for all the selected clubs.
        </div>";

    }

}else{

    $message="<div class='alert error'>
    Something went wrong.
    </div>";

}
    }
}
?>

<style>
    *, *::before, *::after { box-sizing: border-box; }

    /* ── Page background ── */
    .intake-page {
        min-height: 100vh;
        background: #7a1028;
        background-image:
            radial-gradient(circle at 15% 20%, rgba(255,255,255,0.06) 0%, transparent 40%),
            radial-gradient(circle at 85% 80%, rgba(0,0,0,0.15) 0%, transparent 40%);
        padding: 3rem 1.5rem 4rem;
        position: relative;
        overflow: hidden;
    }

    /* Decorative circles */
    .intake-page::before {
        content: '';
        position: absolute;
        width: 400px; height: 400px;
        border-radius: 50%;
        background: rgba(255,255,255,0.04);
        top: -100px; right: -100px;
        pointer-events: none;
    }
    .intake-page::after {
        content: '';
        position: absolute;
        width: 300px; height: 300px;
        border-radius: 50%;
        background: rgba(0,0,0,0.1);
        bottom: -80px; left: -80px;
        pointer-events: none;
    }

    /* ── Form card ── */
    .form-container {
        max-width: 700px;
        margin: 0 auto;
        background: #fff;
        border-radius: 16px;
        padding: 2.5rem 2.5rem 2rem;
        box-shadow: 0 20px 60px rgba(0,0,0,0.25);
        position: relative;
        z-index: 2;
    }

    /* Top accent bar */
    .form-container::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 5px;
        background: linear-gradient(to right, #7a1028, #d44000);
        border-radius: 16px 16px 0 0;
    }

    /* Header */
    .form-header {
        text-align: center;
        margin-bottom: 2rem;
        padding-bottom: 1.5rem;
        border-bottom: 0.5px solid #f0ede7;
    }
    .form-badge {
        display: inline-flex; align-items: center; gap: 6px;
        background: #fdecea;
        border: 0.5px solid #f5c6cb;
        border-radius: 20px;
        padding: 4px 14px;
        font-size: 11px; font-weight: 700;
        color: #7a1028;
        text-transform: uppercase; letter-spacing: 0.08em;
        font-family: 'Segoe UI', sans-serif;
        margin-bottom: 0.75rem;
    }
    .form-container h2 {
        font-size: 1.8rem; font-weight: 700;
        color: #7a1028;
        margin-bottom: 0.4rem;
    }
    .form-container p {
        font-family: 'Segoe UI', sans-serif;
        color: #888; font-size: 13px;
        line-height: 1.6;
    }

    /* ── Form groups ── */
    .form-group { margin-bottom: 1.25rem; }
    .form-group label {
        display: block;
        margin-bottom: 0.4rem;
        font-family: 'Segoe UI', sans-serif;
        font-size: 12px; font-weight: 600;
        color: #333;
        text-transform: uppercase; letter-spacing: 0.05em;
    }
    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 11px 14px;
        border: 0.5px solid #ddd;
        border-radius: 8px;
        font-size: 14px;
        font-family: 'Segoe UI', sans-serif;
        color: #1a1a1a;
        background: #fafaf9;
        transition: border-color 0.15s, box-shadow 0.15s;
    }
    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        border-color: #7a1028;
        box-shadow: 0 0 0 3px rgba(122,16,40,0.1);
        outline: none;
        background: #fff;
    }

    /* Two column layout for name/email */
    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }

    /* ── Club checkboxes ── */
    .club-options {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 8px;
        margin-top: 6px;
    }
    .club-options label {
        display: flex; align-items: center; gap: 8px;
        background: #fafaf9;
        border: 0.5px solid #e0ddd6;
        border-radius: 8px;
        padding: 10px 12px;
        cursor: pointer;
        transition: border-color 0.15s, background 0.15s;
        font-size: 13px; font-weight: 500;
        color: #333;
        text-transform: none; letter-spacing: 0;
    }
    .club-options label:hover {
        border-color: #7a1028;
        background: #fdecea;
    }
    .club-options input[type="checkbox"] {
        width: auto; padding: 0;
        accent-color: #7a1028;
    }

    /* ── Submit button ── */
    .btn-submit {
        width: 100%;
        background: #7a1028;
        color: #fff;
        border: none;
        padding: 13px;
        border-radius: 8px;
        font-size: 14px; font-weight: 600;
        cursor: pointer;
        font-family: 'Segoe UI', sans-serif;
        transition: background 0.18s, transform 0.15s;
        margin-top: 0.5rem;
    }
    .btn-submit:hover {
        background: #5e0c1e;
        transform: translateY(-1px);
    }

    /* ── Alerts ── */
    .alert {
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 1.25rem;
        font-family: 'Segoe UI', sans-serif;
        font-size: 13px; font-weight: 600;
        text-align: center;
    }
    .alert.success { background: #e8f6ee; color: #1a7a4a; border: 0.5px solid #b6dfc5; }
    .alert.error   { background: #fdecea; color: #7a1028; border: 0.5px solid #f5c6cb; }

    /* ── Responsive ── */
    @media (max-width: 600px) {
        .form-row { grid-template-columns: 1fr; }
        .club-options { grid-template-columns: 1fr; }
        .form-container { padding: 1.75rem 1.25rem; }
        .intake-page { padding: 1.5rem 1rem 3rem; }
    }
</style>

<div class="intake-page">
    <div class="form-container">

        <div class="form-header">
            <div class="form-badge">&#128203; Apex College</div>
            <h2>Club Intake Application</h2>
            <p>Submit your onboarding application for active recruitment into our clubs.</p>
        </div>

        <?php echo $message; ?>

        <form action="registration.php" method="POST">

            <div class="form-row">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="student_name" value="<?php echo htmlspecialchars($_SESSION['user_name']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Apex College Email</label>
                    <input type="email" name="student_email" placeholder="name@apexcollege.edu.np" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Faculty</label>
                    <select name="faculty" required>
                        <option value="">Select Faculty</option>
                        <option value="BCSIT">BCSIT</option>
                        <option value="BBA">BBA</option>
                        <option value="BBA-F">BBA-F</option>
                        <option value="BBA-TT">BBA-TT</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Current Semester</label>
                    <select name="semester" required>
                        <option value="">Select Semester</option>
                        <option>1st Semester</option>
                        <option>3rd Semester</option>
                        <option>5th Semester</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Interested Clubs</label>
                <div class="club-options">
                    <label><input type="checkbox" name="selected_club[]" value="Performing Arts Club"> 🎭 Performing Arts Club</label>
                    <label><input type="checkbox" name="selected_club[]" value="Sports and Leadership Club"> 🏆 Sports &amp; Leadership Club</label>
                    <label><input type="checkbox" name="selected_club[]" value="Travel and Tourism Club"> ✈️ Travel &amp; Tourism Club</label>
                    <label><input type="checkbox" name="selected_club[]" value="Media and Marketing Club"> 📢 Media &amp; Marketing Club</label>
                    <label><input type="checkbox" name="selected_club[]" value="IT Club"> 💻 IT Club</label>
                    <label><input type="checkbox" name="selected_club[]" value="HEAT"> ❤️ HEAT Club</label>
                </div>
            </div>

            <div class="form-group">
                <label>Area of Interest</label>
                <textarea name="interest" rows="3" placeholder="Tell us what interests you most about joining these clubs..." required></textarea>
            </div>

            <div class="form-group">
                <label>Motivation &amp; Past Experience</label>
                <textarea name="reasons" rows="4" placeholder="Describe your experience, leadership skills, achievements, or motivation..." required></textarea>
            </div>

            <button type="submit" name="apply" class="btn-submit">Submit Application &rarr;</button>

        </form>
    </div>
</div>

<?php include 'footer.php'; ?>
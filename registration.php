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

    // Fix: selected_club is an array, join it into a string
    $clubs = isset($_POST['selected_club']) ? $_POST['selected_club'] : [];
    $club = mysqli_real_escape_string($conn, implode(', ', $clubs));

    if (empty($clubs)) {
        $message = "<div class='alert error'>Please select at least one club.</div>";
    } else {
        $query = "INSERT INTO registrations (student_name, student_email, faculty, semester, selected_club, interest, reasons) 
                  VALUES ('$name', '$email', '$faculty', '$semester', '$club', '$interest', '$reasons')";
        if (mysqli_query($conn, $query)) {
            $message = "<div class='alert success'>Application submitted successfully! We will contact you soon.</div>";
        } else {
            $message = "<div class='alert error'>Error: " . mysqli_error($conn) . "</div>";
        }
    }
}
?>

<style>
    .form-container { max-width: 550px; margin: 4rem auto; padding: 3rem; background: #ffffff; border-radius: 8px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
    .form-container h2 { font-size: 2rem; color:rgb(176, 14, 14); margin-bottom: 0.5rem; text-align: center; }
    .form-container p { font-family: 'Segoe UI', sans-serif; color: #666; text-align: center; margin-bottom: 2rem; font-size: 0.95rem; }
    
    .form-group { margin-bottom: 1.5rem; }
    .form-group label { display: block; margin-bottom: 0.4rem; font-family: sans-serif; font-size: 0.85rem; font-weight: bold; color: var(--text-dark); }
    .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 0.75rem; border: 1px solid #ccc; border-radius: 6px; font-size: 1rem; font-family: 'Segoe UI', sans-serif; }
    .form-group input:focus, .form-group select:focus, .form-group textarea:focus { border-color: var(--primary-crimson); outline: none; }
    
    .btn-submit { width: 100%; background: var(--primary-crimson); color: white; border: none; padding: 0.8rem; border-radius: 6px; font-size: 1rem; font-weight: bold; cursor: pointer; transition: 0.2s; margin-top: 1rem; font-family: sans-serif; }
    .btn-submit:hover { background: var(--accent-orange); }
    
    .alert { padding: 1rem; border-radius: 6px; margin-bottom: 1.5rem; font-family: sans-serif; text-align: center; font-weight: bold; }
    .alert.success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .alert.error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    .form-container{
    max-width:750px;
    border-radius:20px;
    padding:40px;
    box-shadow:0 15px 40px rgba(0,0,0,0.08);
}

.club-options{
    display:grid;
    grid-template-columns:repeat(2,1fr);
    gap:12px;
    margin-top:10px;
}

.club-options label{
    display:flex;
    align-items:center;
    gap:10px;
    background:#fafafa;
    padding:15px;
    border:2px solid #e5e7eb;
    border-radius:12px;
    cursor:pointer;
    transition:.3s;
    font-weight:500;
}

.club-options label:hover{
    border-color:#9f1239;
    transform:translateY(-2px);
}

.club-options input[type="checkbox"]{
    width:auto;
}

.form-group input,
.form-group select,
.form-group textarea{
    border-radius:12px;
    padding:14px;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus{
    box-shadow:0 0 0 4px rgba(159,18,57,.15);
}

.btn-submit{
    border-radius:12px;
    padding:15px;
    font-size:1rem;
}

.btn-submit:hover{
    transform:translateY(-2px);
}

@media(max-width:768px){

    .club-options{
        grid-template-columns:1fr;
    }

    .form-container{
        margin:2rem 1rem;
        padding:25px;
    }
}
</style>

<div class="form-container">
    <h2>Club Intake Application</h2>
    <p>Submit your onboarding application for active recruitment into our organizations.</p>

    <?php echo $message; ?>

    <form action="registration.php" method="POST">

    <div class="form-group">
        <label>Full Name</label>
        <input type="text"
               name="student_name"
               value="<?php echo $_SESSION['user_name']; ?>"
               required>
    </div>

    <div class="form-group">
        <label>Apex College Email Address</label>
        <input type="email"
               name="student_email"
               placeholder="studentname@apexcollege.edu.np"
               required>
    </div>

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

    <div class="form-group">
        <label>Interested Clubs</label>

        <div class="club-options">

            <label>
                <input type="checkbox"
                       name="selected_club[]"
                       value="Performing Arts Club">
                🎭 Performing Arts Club
            </label>

            <label>
                <input type="checkbox"
                       name="selected_club[]"
                       value="Sports and Leadership Club">
                🏆 Sports & Leadership Club
            </label>

            <label>
                <input type="checkbox"
                       name="selected_club[]"
                       value="Travel and Tourism Club">
                ✈️ Travel & Tourism Club
            </label>

            <label>
                <input type="checkbox"
                       name="selected_club[]"
                       value="Media and Marketing Club">
                📢 Media & Marketing Club
            </label>

            <label>
                <input type="checkbox"
                       name="selected_club[]"
                       value="IT Club">
                💻 IT Club
            </label>

            <label>
                <input type="checkbox"
                       name="selected_club[]"
                       value="HEAT">
                ❤️ HEAT Club
            </label>

        </div>
    </div>

    <div class="form-group">
        <label>Area of Interest</label>

        <textarea
            name="interest"
            rows="3"
            placeholder="Tell us what interests you most about joining these clubs..."
            required></textarea>
    </div>

    <div class="form-group">
        <label>Motivation & Past Experience Statement</label>

        <textarea
            name="reasons"
            rows="5"
            placeholder="Describe your experience, leadership skills, achievements, or motivation..."
            required></textarea>
    </div>

    <button type="submit" name="apply" class="btn-submit">
        Submit Application
    </button>

</form>
</div>
</div>
<?php include 'footer.php'; ?>
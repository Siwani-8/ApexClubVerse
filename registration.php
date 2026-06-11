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
    $club = mysqli_real_escape_string($conn, $_POST['selected_club']);
    $reasons = mysqli_real_escape_string($conn, $_POST['reasons']);

    $query = "INSERT INTO registrations (student_name, student_email, selected_club, reasons) VALUES ('$name', '$email', '$club', '$reasons')";
    if (mysqli_query($conn, $query)) {
        $message = "<div class='alert success'>Application processed! Your file has been channeled to the chosen club dashboard.</div>";
    } else {
        $message = "<div class='alert error'>Execution error during database log routing.</div>";
    }
}
?>

<style>
    .form-container { max-width: 550px; margin: 4rem auto; padding: 3rem; background: #ffffff; border-radius: 8px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
    .form-container h2 { font-size: 2rem; color: var(--text-dark); margin-bottom: 0.5rem; text-align: center; }
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
</style>

<div class="form-container">
    <h2>Club Intake Application</h2>
    <p>Submit your onboarding application for active recruitment into our organizations.</p>

    <?php echo $message; ?>

    <form action="registration.php" method="POST">
        <div class="form-group">
            <label>Full Name</label>
            <!-- Auto-fill profile name string pulled out of existing Session array storage -->
            <input type="text" name="student_name" value="<?php echo $_SESSION['user_name']; ?>" required>
        </div>

        <div class="form-group">
            <label>Apex College Email Address</label>
            <input type="email" name="student_email" placeholder="studentname@apexcollege.edu.np" required>
        </div>

        <div class="form-group">
            <label>Target Student Community</label>
            <select name="selected_club" required>
                <option value="">-- Choose a Group --</option>
                <option value="Sports Club">Sports Club</option>
                <option value="Performing Arts Club">Performing Arts Club</option>
                <option value="Health Club">Health Club</option>
                <option value="IT Club">IT Club</option>
                <option value="Media & Marketing Club">Media & Marketing Club</option>
                <option value="Travel & Tourism Club">Travel & Tourism Club</option>
            </select>
        </div>

        <div class="form-group">
            <label>Motivation & Past Experience Statement</label>
            <textarea name="reasons" rows="5" placeholder="Elaborate briefly on why you would make a valuable contribution to this club's operation..." required></textarea>
        </div>

        <button type="submit" name="apply" class="btn-submit">Submit Registration</button>
    </form>
</div>

<?php include 'footer.php'; ?>
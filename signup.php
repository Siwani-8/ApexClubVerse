<?php
include 'header.php';
include 'db.php';

$msg = "";
$msg_type = "";

if (isset($_POST['submit'])) {
    $name  = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass  = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Only allow @apexcollege.edu.np emails
    if (!str_ends_with($email, '@apexcollege.edu.np')) {
        $msg = "Only Apex College email addresses are allowed (e.g. name@apexcollege.edu.np)";
        $msg_type = "error";
    } else {
        $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
        if (mysqli_num_rows($check) > 0) {
            $msg = "This email is already registered. Please sign in.";
            $msg_type = "error";
        } else {
            if (mysqli_query($conn, "INSERT INTO users (fullname, email, password, role) VALUES ('$name', '$email', '$pass', 'student')")) {
                header("Location: login.php");
                exit;
            } else {
                $msg = "Something went wrong. Please try again.";
                $msg_type = "error";
            }
        }
    }
}
?>

<style>
    *, *::before, *::after { box-sizing: border-box; }

    .signup-page {
        min-height: 100vh;
        background: #7a1028;
        background-image:
            radial-gradient(circle at 15% 20%, rgba(255,255,255,0.06) 0%, transparent 40%),
            radial-gradient(circle at 85% 80%, rgba(0,0,0,0.15) 0%, transparent 40%);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 3rem 1.5rem;
        position: relative;
        overflow: hidden;
    }
    .signup-page::before {
        content: '';
        position: absolute;
        width: 400px; height: 400px;
        border-radius: 50%;
        background: rgba(255,255,255,0.04);
        top: -100px; right: -100px;
        pointer-events: none;
    }
    .signup-page::after {
        content: '';
        position: absolute;
        width: 300px; height: 300px;
        border-radius: 50%;
        background: rgba(0,0,0,0.1);
        bottom: -80px; left: -80px;
        pointer-events: none;
    }

    .signup-card {
        background: #fff;
        border-radius: 16px;
        padding: 2.5rem 2rem 2rem;
        width: 100%;
        max-width: 420px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.25);
        position: relative;
        z-index: 2;
    }
    .signup-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 5px;
        background: linear-gradient(to right, #7a1028, #d44000);
        border-radius: 16px 16px 0 0;
    }

    .signup-header {
        text-align: center;
        margin-bottom: 1.75rem;
        padding-bottom: 1.25rem;
        border-bottom: 0.5px solid #f0ede7;
    }
    .signup-badge {
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
    .signup-card h2 {
        font-size: 1.6rem; font-weight: 700;
        color: #1a1a1a; margin-bottom: 0.3rem;
    }
    .signup-card .signup-sub {
        font-family: 'Segoe UI', sans-serif;
        color: #999; font-size: 13px;
    }

    .alert-error {
        background: #fdecea;
        border: 0.5px solid #f5c6cb;
        border-radius: 8px;
        padding: 10px 14px;
        font-family: 'Segoe UI', sans-serif;
        font-size: 13px; color: #7a1028;
        margin-bottom: 1.25rem;
        text-align: center;
    }

    .form-group { margin-bottom: 1.1rem; }
    .form-group label {
        display: block;
        font-family: 'Segoe UI', sans-serif;
        font-size: 11px; font-weight: 700;
        color: #555;
        text-transform: uppercase; letter-spacing: 0.05em;
        margin-bottom: 0.4rem;
    }
    .form-group input {
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
    .form-group input:focus {
        border-color: #7a1028;
        box-shadow: 0 0 0 3px rgba(122,16,40,0.1);
        outline: none;
        background: #fff;
    }

    .email-hint {
        font-family: 'Segoe UI', sans-serif;
        font-size: 11px; color: #bbb;
        margin-top: 4px;
    }

    .btn-auth {
        width: 100%;
        background: #7a1028;
        color: #fff;
        border: none;
        padding: 12px;
        border-radius: 8px;
        font-size: 14px; font-weight: 600;
        cursor: pointer;
        font-family: 'Segoe UI', sans-serif;
        transition: background 0.18s, transform 0.15s;
        margin-top: 0.5rem;
    }
    .btn-auth:hover {
        background: #5e0c1e;
        transform: translateY(-1px);
    }

    .hint {
        text-align: center;
        font-family: 'Segoe UI', sans-serif;
        font-size: 13px; color: #aaa;
        margin-top: 1.25rem;
    }
    .hint a {
        color: #7a1028; font-weight: 600;
        text-decoration: none;
    }
    .hint a:hover { text-decoration: underline; }

    @media (max-width: 480px) {
        .signup-card { padding: 2rem 1.25rem 1.5rem; }
    }
</style>

<div class="signup-page">
    <div class="signup-card">

        <div class="signup-header">
            <div class="signup-badge">&#127979; Apex College Portal</div>
            <h2>Join Portal</h2>
            <p class="signup-sub">Register with your Apex College email</p>
        </div>

        <?php if($msg): ?>
            <div class="alert-error"><?php echo htmlspecialchars($msg); ?></div>
        <?php endif; ?>

        <form action="signup.php" method="POST">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" placeholder="Enter your full name" required>
            </div>
            <div class="form-group">
                <label>College Email</label>
                <input type="email" name="email" placeholder="name@apexcollege.edu.np" required>
                <div class="email-hint">Must be an @apexcollege.edu.np address</div>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Create a password" required>
            </div>
            <button type="submit" name="submit" class="btn-auth">Create Account &rarr;</button>
        </form>

        <p class="hint">Already have an account? <a href="login.php">Sign In</a></p>

    </div>
</div>

<?php include 'footer.php'; ?>
<?php
include 'includes/header.php';
include 'includes/db.php';

$msg = "";
$msg_type = "";

if (isset($_POST['submit'])) {
    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);
    $pass  = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Only allow @apexcollege.edu.np emails
    if (!str_ends_with($email, '@apexcollege.edu.np')) {
        $msg = "Only Apex College email addresses are allowed (e.g. name@apexcollege.edu.np)";
        $msg_type = "error";
    } else {
        // Check if email already exists (prepared statement)
        $check = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
        mysqli_stmt_bind_param($check, "s", $email);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);

        if (mysqli_stmt_num_rows($check) > 0) {
            $msg = "This email is already registered. Please sign in.";
            $msg_type = "error";
        } else {
            // Insert new user — no email verification required
            $role = 'student';
            $insert = mysqli_prepare($conn, "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($insert, "ssss", $name, $email, $pass, $role);

            if (mysqli_stmt_execute($insert)) {
                $msg = "Registration successful! You can sign in now.";
                $msg_type = "success";
            } else {
                $msg = "Something went wrong. Please try again.";
                $msg_type = "error";
            }
            mysqli_stmt_close($insert);
        }
        mysqli_stmt_close($check);
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
    .alert-success {
        background: #eaf7ee;
        border: 0.5px solid #b7e0c2;
        border-radius: 8px;
        padding: 10px 14px;
        font-family: 'Segoe UI', sans-serif;
        font-size: 13px; color: #1e5c34;
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

    .password-wrapper {
        position: relative;
    }
    .password-wrapper input {
        padding-right: 42px;
    }
    .toggle-password {
        position: absolute;
        top: 50%;
        right: 10px;
        transform: translateY(-50%);
        background: none;
        border: none;
        cursor: pointer;
        padding: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #999;
    }
    .toggle-password:hover { color: #7a1028; }
    .toggle-password svg { width: 18px; height: 18px; }
</style>

<div class="signup-page">
    <div class="signup-card">

        <div class="signup-header">
            <div class="signup-badge">&#127979; Apex College Portal</div>
            <h2>Join Portal</h2>
            <p class="signup-sub">Register with your Apex College email</p>
        </div>

        <?php if($msg): ?>
            <div class="<?php echo $msg_type === 'success' ? 'alert-success' : 'alert-error'; ?>">
                <?php echo htmlspecialchars($msg); ?>
            </div>
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
                <div class="password-wrapper">
                    <input type="password" id="password" name="password" placeholder="Create a password" required>
                    <button type="button" class="toggle-password" onclick="togglePassword('password', this)" aria-label="Show password">
                        <svg class="icon-eye" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                        <svg class="icon-eye-off" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;">
                            <path d="M17.94 17.94A10.94 10.94 0 0 1 12 20c-7 0-11-8-11-8a21.62 21.62 0 0 1 5.06-6.06M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a21.6 21.6 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                            <line x1="1" y1="1" x2="23" y2="23"></line>
                        </svg>
                    </button>
                </div>
            </div>
            <button type="submit" name="submit" class="btn-auth">Create Account &rarr;</button>
        </form>

        <p class="hint">Already have an account? <a href="login.php">Sign In</a></p>

    </div>
</div>

<script>
    function togglePassword(inputId, btn) {
        var input = document.getElementById(inputId);
        var eyeIcon = btn.querySelector('.icon-eye');
        var eyeOffIcon = btn.querySelector('.icon-eye-off');
        if (input.type === 'password') {
            input.type = 'text';
            eyeIcon.style.display = 'none';
            eyeOffIcon.style.display = 'block';
            btn.setAttribute('aria-label', 'Hide password');
        } else {
            input.type = 'password';
            eyeIcon.style.display = 'block';
            eyeOffIcon.style.display = 'none';
            btn.setAttribute('aria-label', 'Show password');
        }
    }
</script>

<?php include 'includes/footer.php'; ?>
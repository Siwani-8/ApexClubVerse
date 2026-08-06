<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'includes/header.php';
include 'includes/db.php';

$msg = "";
$loginSuccess = false;
$redirectUrl = "index.php";
$welcomeName = "";

if (isset($_POST['submit'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    if (!str_ends_with($email, '@apexcollege.edu.np')) {
        $msg = "Only Apex College email addresses are allowed (e.g. name@apexcollege.edu.np)";
    } else {
        $res = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
        if (mysqli_num_rows($res) > 0) {
            $user = mysqli_fetch_assoc($res);
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_logged_in'] = true;
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];

                if ($user['role'] == 'club_admin') {
                    $_SESSION['club_id'] = $user['club_id'];
                }

                if ($user['role'] == 'admin') {
                    $redirectUrl = "admin.php";
                } elseif ($user['role'] == 'club_admin') {
                    $redirectUrl = "club_admin.php";
                } else {
                    $redirectUrl = "index.php";
                }

                $loginSuccess = true;
                $welcomeName = $user['name'];

            } else {
                $msg = "Wrong password. Please try again.";
            }
        } else {
            $msg = "No account found with this email.";
        }
    }
}
?>

<style>
    *, *::before, *::after { box-sizing: border-box; }

    html, body {
        margin: 0;
        padding: 0;
        background: #ebdede;
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }

    .login-page {
        flex: 1; 
        background: #ebdede;
        background-image:
            radial-gradient(circle at 15% 20%, rgba(255,255,255,0.06) 0%, transparent 40%),
            radial-gradient(circle at 85% 80%, rgba(0,0,0,0.15) 0%, transparent 40%);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 4rem 1.5rem; 
        position: relative;
        overflow: hidden;
        margin-bottom: 0;
    }

    footer, .footer, [class*="footer"] {
        margin-top: 0 !important;
        padding-top: 0;
    }

    footer p, .copyright, [class*="copy"] {
        margin: 0;
        padding: 15px 0;
        background: #fff;
    }

    .login-page::before {
        content: '';
        position: absolute;
        width: 400px; height: 400px;
        border-radius: 50%;
        background: rgba(255,255,255,0.04);
        top: -100px; right: -100px;
        pointer-events: none;
    }
    .login-page::after {
        content: '';
        position: absolute;
        width: 300px; height: 300px;
        border-radius: 50%;
        background: rgba(0,0,0,0.1);
        bottom: -80px; left: -80px;
        pointer-events: none;
    }
    .login-card {
        background: #fff;
        border-radius: 16px;
        padding: 2.5rem 2rem 2rem;
        width: 100%;
        max-width: 420px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.25);
        position: relative;
        z-index: 2;
    }
    .login-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 5px;
        background: linear-gradient(to right, #7a1028, #d44000);
        border-radius: 16px 16px 0 0;
    }
    .login-header {
        text-align: center;
        margin-bottom: 1.75rem;
        padding-bottom: 1.25rem;
        border-bottom: 0.5px solid #ebdede;
    }
    .login-badge {
        display: inline-flex; align-items: center; gap: 6px;
        background: #ebdede;
        border: 0.5px solid #ebdede;
        border-radius: 20px;
        padding: 4px 14px;
        font-size: 11px; font-weight: 700;
        color: #7a1028;
        text-transform: uppercase; letter-spacing: 0.08em;
        font-family: 'Segoe UI', sans-serif;
        margin-bottom: 0.75rem;
    }
    .login-card h2 {
        font-size: 1.6rem; font-weight: 700;
        color: #1a1a1a; margin-bottom: 0.3rem;
    }
    .login-card .login-sub {
        font-family: 'Segoe UI', sans-serif;
        color: #999; font-size: 13px;
    }
    .alert-error {
        background: #fdecea;
        border: 0.5px solid #ebdede;
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
    .password-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }
    .password-wrapper input {
        padding-right: 40px;
    }
    .toggle-password {
        position: absolute;
        right: 12px;
        background: none;
        border: none;
        cursor: pointer;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #777;
    }
    .toggle-password:focus {
        outline: none;
    }
    .toggle-password svg {
        width: 20px;
        height: 20px;
        fill: none;
        stroke: currentColor;
        stroke-width: 2;
        stroke-linecap: round;
        stroke-linejoin: round;
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

    /* Welcome popup styles */
    .welcome-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.45);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        animation: overlayFadeIn 0.25s ease;
    }
    .welcome-popup-card {
        background: #fff;
        border-radius: 16px;
        padding: 2.5rem 2rem;
        text-align: center;
        max-width: 360px;
        width: 90%;
        box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        animation: popupPop 0.35s cubic-bezier(.34,1.56,.64,1);
    }
    .welcome-popup-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 56px; height: 56px;
        border-radius: 50%;
        background: #7a1028;
        color: #fff;
        font-size: 26px;
        margin-bottom: 1rem;
    }
    .welcome-popup-card h3 {
        margin: 0 0 6px;
        font-size: 20px;
        color: #1a1a1a;
        font-family: 'Segoe UI', sans-serif;
    }
    .welcome-popup-card p {
        margin: 0;
        font-size: 14px;
        color: #888;
        font-family: 'Segoe UI', sans-serif;
    }
    @keyframes overlayFadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    @keyframes popupPop {
        from { transform: scale(0.8); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }
</style>

<?php if ($loginSuccess): ?>
    <div class="welcome-overlay" id="welcomeOverlay">
        <div class="welcome-popup-card">
            <div class="welcome-popup-icon">&#10003;</div>
            <h3>Welcome<?php echo $welcomeName ? ', ' . htmlspecialchars($welcomeName) : ''; ?>!</h3>            <p>You've successfully signed in. Redirecting...</p>
        </div>
    </div>
    <script>
        setTimeout(function () {
            window.location.href = "<?php echo htmlspecialchars($redirectUrl, ENT_QUOTES); ?>";
        }, 1800);
    </script>
<?php else: ?>

<div class="login-page">
    <div class="login-card">

        <div class="login-header">
            <div class="login-badge">&#127979; Apex College Portal</div>
            <h2>Welcome Back</h2>
            <p class="login-sub">Sign in with your Apex College email</p>
        </div>

        <?php if(!empty($msg)): ?>
            <div class="alert-error"><?php echo htmlspecialchars($msg); ?></div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="form-group">
                <label>College Email</label>
                <input type="email" name="email" placeholder="name@apexcollege.edu.np" required>
                <div class="email-hint">Must be an @apexcollege.edu.np address</div>
            </div>
            <div class="form-group">
                <label>Password</label>
                <div class="password-wrapper">
                    <input type="password" name="password" id="passwordInput" placeholder="Enter your password" required>
                    <button type="button" class="toggle-password" id="togglePasswordBtn" aria-label="Toggle password visibility">
                        <svg id="eyeIcon" viewBox="0 0 24 24">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </button>
                </div>
            </div>
            <button type="submit" name="submit" class="btn-auth">Sign In &rarr;</button>
        </form>

        <p class="hint">New to the club? <a href="signup.php">Join Club</a></p>

    </div>
</div>

<script>
    const togglePasswordBtn = document.getElementById('togglePasswordBtn');
    const passwordInput = document.getElementById('passwordInput');
    const eyeIcon = document.getElementById('eyeIcon');

    const eyeOpenPath = `<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>`;
    const eyeClosedPath = `<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>`;

    togglePasswordBtn.addEventListener('click', function () {
        const isPassword = passwordInput.getAttribute('type') === 'password';
        passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
        eyeIcon.innerHTML = isPassword ? eyeClosedPath : eyeOpenPath;
    });
</script>

<?php endif; ?>

<?php include 'includes/footer.php'; ?>
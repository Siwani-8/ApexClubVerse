<style>
.footer-top{
    background:#7f0d23;
    color:#fff;
    padding:35px 6%;
    margin-top:0;
    position:relative;
    flex-shrink:0;
    width:100%;
}

.footer-container{
    max-width:1200px;
    margin:auto;
    display:flex;
    justify-content:space-between;
    align-items:flex-start;
    gap:30px;
    flex-wrap:wrap;
}

.footer-column{
    flex:1;
    min-width:220px;
}

.footer-logo{
    width:140px;
    height:auto;
    max-width:100%;
    margin-bottom:15px;
    display:block;
    background:#fff;
    border-radius:10px;
    padding:8px 10px;
}

.footer-column h2{
    font-size:22px;
    margin-bottom:15px;
    color:#fff;
    font-weight:bold;
}

.footer-column p{
    margin:8px 0;
    font-size:15px;
    line-height:1.6;
}

.links-grid{
    display:flex;
    gap:50px;
}

.links-grid ul{
    list-style:none;
    margin:0;
    padding:0;
}

.links-grid li{
    margin-bottom:12px;
}

.links-grid a{
    color:#fff;
    text-decoration:none;
    font-size:15px;
    transition:0.3s;
}

.links-grid a:hover{
    opacity:0.8;
}

.contact-btn{
    display:inline-block;
    background:#fff;
    color:#7f0d23;
    text-decoration:none;
    padding:10px 25px;
    border-radius:5px;
    font-weight:bold;
    margin-bottom:15px;
}

.contact-btn:hover{
    background:#f5f5f5;
}

.copyright{
    background:#fff;
    color:#333;
    text-align:center;
    padding:12px;
    font-size:14px;
    position:relative;
    flex-shrink:0;
    width:100%;
}

/* Mobile */
@media(max-width:768px){

    .footer-top{
        padding:28px 5%;
        margin-top:0;
    }

    .footer-container{
        flex-direction:column;
        gap:25px;
        align-items:stretch;
    }

    .links-grid{
        gap:30px;
        flex-wrap:wrap;
    }

    .footer-column{
        width:100%;
        min-width:0;
    }

    .footer-column h2{
        font-size:20px;
    }

    .footer-logo{
        width:120px;
    }

    .contact-btn{
        display:block;
        text-align:center;
        width:100%;
        max-width:280px;
    }
}

@media(max-width:480px){
    .footer-top{
        padding:24px 4%;
    }

    .links-grid{
        display:grid;
        grid-template-columns:1fr 1fr;
        gap:8px 20px;
    }

    .links-grid ul{
        width:100%;
    }

    .app-flash{
        margin-left:10px;
        margin-right:10px;
        max-width:none;
    }

    .confirm-actions{
        flex-direction:column-reverse;
    }

    .confirm-actions button{
        width:100%;
    }

    .copyright{
        padding:12px 16px;
        font-size:12px;
        line-height:1.5;
    }
}
</style>

</div><!-- /.content-wrapper -->

<!-- Footer -->
<section class="footer-top">

    <div class="footer-container">

        <!-- Left Column -->
        <div class="footer-column">


            <img src="<?php echo htmlspecialchars(url('images/logo1.png')); ?>" class="footer-logo" alt="ApexClubVerse Logo">

            <!-- Change path if needed -->
    


            <p>1261 Devkota Sadak</p>
            <p>Mid-Baneshwor, Kathmandu</p>
            <p>Nepal</p>

        </div>

        <!-- Quick Links -->
        <div class="footer-column">

            <h2>QUICK LINKS</h2>

            <div class="links-grid">

                <ul>
                    <li><a href="<?php echo htmlspecialchars(url('index.php')); ?>">Home</a></li>
                    <li><a href="<?php echo htmlspecialchars(url('clubs.php')); ?>">Clubs</a></li>
                    <li><a href="<?php echo htmlspecialchars(url('events.php')); ?>">Events</a></li>
                    <li><a href="<?php echo htmlspecialchars(url('vote-events.php')); ?>">Event Vote</a></li>
                </ul>

                <ul>
                    <li><a href="<?php echo htmlspecialchars(url('about.php')); ?>">About</a></li>
                    <li><a href="<?php echo htmlspecialchars(url('contact.php')); ?>">Contact</a></li>
                    <li><a href="<?php echo htmlspecialchars(url('login.php')); ?>">Login</a></li>
                    <li><a href="<?php echo htmlspecialchars(url('signup.php')); ?>">Register</a></li>
                </ul>

            </div>

        </div>

        <!-- Contact -->
        <div class="footer-column">

            <a href="<?php echo htmlspecialchars(url('contact.php')); ?>" class="contact-btn">Contact Us</a>

            <p>+977-9860390455</p>
            <p>info@apexclubverse.com</p>

        </div>

    </div>

</section>

<footer class="copyright">
    © <?php echo date("Y"); ?> ApexClubVerse - Apex College Student Activity Portal. All Rights Reserved.
</footer>

<style>
.app-flash {
    max-width: 1100px;
    margin: 1rem auto;
    padding: 14px 18px;
    border-radius: 10px;
    font-family: 'Segoe UI', sans-serif;
    font-size: 14px;
    font-weight: 600;
}
.app-flash-success {
    background: #e8f6ee;
    color: #1a7a4a;
    border: 1px solid #b7e4c7;
}
.app-flash-error {
    background: #fdecea;
    color: #7a1028;
    border: 1px solid #f5c2c7;
}
.confirm-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.45);
    z-index: 10000;
    align-items: center;
    justify-content: center;
    padding: 16px;
}
.confirm-overlay.is-open {
    display: flex;
}
.confirm-box {
    background: #fff;
    width: 420px;
    max-width: 100%;
    border-radius: 14px;
    padding: 24px;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.18);
    font-family: 'Segoe UI', sans-serif;
}
.confirm-box h3 {
    margin: 0 0 10px;
    font-size: 1.15rem;
    color: #1a1a1a;
}
.confirm-box p {
    margin: 0 0 20px;
    color: #555;
    font-size: 14px;
    line-height: 1.5;
}
.confirm-actions {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
}
.confirm-actions button {
    border: 0;
    border-radius: 8px;
    padding: 10px 16px;
    font-weight: 600;
    cursor: pointer;
    font-family: 'Segoe UI', sans-serif;
}
.confirm-cancel {
    background: #f0eeea;
    color: #333;
}
.confirm-ok {
    background: #7a1028;
    color: #fff;
}
</style>

<div id="confirmOverlay" class="confirm-overlay" aria-hidden="true">
    <div class="confirm-box" role="dialog" aria-modal="true" aria-labelledby="confirmTitle">
        <h3 id="confirmTitle">Please confirm</h3>
        <p id="confirmMessage">Are you sure?</p>
        <div class="confirm-actions">
            <button type="button" class="confirm-cancel" id="confirmCancel">Cancel</button>
            <button type="button" class="confirm-ok" id="confirmOk">Confirm</button>
        </div>
    </div>
</div>

<script>
(function () {
    var overlay = document.getElementById('confirmOverlay');
    var messageEl = document.getElementById('confirmMessage');
    var titleEl = document.getElementById('confirmTitle');
    var cancelBtn = document.getElementById('confirmCancel');
    var okBtn = document.getElementById('confirmOk');
    var pendingForm = null;
    var pendingButton = null;

    function closeConfirm() {
        overlay.classList.remove('is-open');
        overlay.setAttribute('aria-hidden', 'true');
        pendingForm = null;
        pendingButton = null;
    }

    function openConfirm(message, form, button, title) {
        pendingForm = form;
        pendingButton = button;
        messageEl.textContent = message || 'Are you sure?';
        titleEl.textContent = title || 'Please confirm';
        overlay.classList.add('is-open');
        overlay.setAttribute('aria-hidden', 'false');
        okBtn.focus();
    }

    okBtn.addEventListener('click', function () {
        if (!pendingForm || !pendingButton) return;
        var form = pendingForm;
        var btn = pendingButton;
        closeConfirm();
        btn.setAttribute('data-confirm-skip', '1');
        if (typeof form.requestSubmit === 'function') {
            form.requestSubmit(btn);
        } else {
            var hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = btn.name;
            hidden.value = btn.value || '1';
            form.appendChild(hidden);
            form.submit();
        }
        setTimeout(function () { btn.removeAttribute('data-confirm-skip'); }, 0);
    });

    cancelBtn.addEventListener('click', closeConfirm);
    overlay.addEventListener('click', function (e) {
        if (e.target === overlay) closeConfirm();
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && overlay.classList.contains('is-open')) {
            closeConfirm();
        }
    });

    document.addEventListener('click', function (e) {
        var btn = e.target.closest('[data-confirm]');
        if (!btn || btn.getAttribute('data-confirm-skip') === '1') return;
        var form = btn.closest('form');
        if (!form) return;
        e.preventDefault();
        openConfirm(
            btn.getAttribute('data-confirm'),
            form,
            btn,
            btn.getAttribute('data-confirm-title')
        );
    });
})();
</script>

</body>
</html>
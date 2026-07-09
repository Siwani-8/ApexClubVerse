<style>
.footer-top{
    background:#7f0d23;
    color:#fff;
    padding:35px 6%;
    margin-top:0;
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
    margin-bottom:0;
    display:block;
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
}

/* Mobile */
@media(max-width:768px){

    .footer-container{
        flex-direction:column;
        gap:25px;
    }

    .links-grid{
        gap:30px;
    }

    .footer-column{
        width:100%;
    }

    .footer-column h2{
        font-size:20px;
    }

    .footer-logo{
        width:120px;
    }
}
</style>

<!-- Footer -->
<section class="footer-top">

    <div class="footer-container">

        <!-- Left Column -->
        <div class="footer-column">


            <img src="logo.png" class="footer-logo">

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
                    <li><a href="index.php">Home</a></li>
                    <li><a href="clubs.php">Clubs</a></li>
                    <li><a href="events.php">Events</a></li>
                    <li><a href="vote-events.php">Event Vote</a></li>
                </ul>

                <ul>
                    <li><a href="about.php">About</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php">Register</a></li>
                </ul>

            </div>

        </div>

        <!-- Contact -->
        <div class="footer-column">

            <a href="contact.php" class="contact-btn">Contact Us</a>

            <p>+977-9860390455</p>
            <p>info@apexclubverse.com</p>

        </div>

    </div>
</div>

</section>

<footer class="copyright">
    © <?php echo date("Y"); ?> ApexClubVerse - Apex College Student Activity Portal. All Rights Reserved.
</footer>

</body>
</html>
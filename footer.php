
<style>
.footer-top{
    background:#7f0d23;
    color:#fff;
    padding:60px 8%;
    margin:0;
}

.footer-container{
    max-width:1200px;
    margin:auto;
    display:flex;
    justify-content:space-between;
    gap:60px;
    flex-wrap:wrap;
}

.footer-column{
    flex:1;
    min-width:250px;
}

.footer-logo{
    width:220px;
    margin-bottom:25px;
}

.footer-column h2{
    font-size:32px;
    margin-bottom:25px;
    color:#fff;
}

.footer-column p{
    margin:8px 0;
    line-height:1.8;
}

.links-grid{
    display:flex;
    gap:60px;
}

.links-grid ul{
    list-style:none;
    padding:0;
    margin:0;
}

.links-grid li{
    margin-bottom:15px;
}

.links-grid a{
    color:#fff;
    text-decoration:none;
    transition:.3s;
}

.links-grid a:hover{
    color:#f5d6d6;
    padding-left:5px;
}

.contact-btn{
    display:inline-block;
    margin:25px 0;
    padding:12px 30px;
    background:#fff;
    color:#7f0d23;
    text-decoration:none;
    border-radius:5px;
    font-weight:bold;
}

.contact-btn:hover{
    background:#f3f3f3;
}

.social-icons{
    display:flex;
    gap:20px;
    margin-top:20px;
}

.social-icons a{
    color:#fff;
    font-size:24px;
    transition:.3s;
}

.social-icons a:hover{
    transform:translateY(-3px);
}

.copyright{
    background:#ffffff;
    color:#333;
    text-align:center;
    padding:20px;
    margin:0;
    font-size:16px;
}

/* Responsive */
@media (max-width:768px){

.footer-container{
    flex-direction:column;
}

.links-grid{
    flex-direction:column;
    gap:0;
}

.footer-column{
    margin-bottom:30px;
}

.footer-column h2{
    font-size:26px;
}
}
</style>
<!-- Footer Top -->
<section class="footer-top">

    <div class="footer-container">

        <!-- Left -->
        <div class="footer-column">

            <img src="assets/images/logo.png" class="footer-logo">

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

        <!-- Social -->
        <div class="footer-column">

            <a href="contact.php" class="contact-btn">Contact Us</a>

            <p>
                +977-9860390455<br>
                info@apexclubverse.com
            </p>

        </div>

    </div>

</section>

<footer class="copyright">
    © <?php echo date("Y"); ?> ApexClubVerse - Apex College Student Activity Portal. All Rights Reserved.
</footer></div> 
</body>
</html>
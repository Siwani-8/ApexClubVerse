<style>

<<<<<<< HEAD
.gallery-grid{
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(250px,1fr));
    gap:20px;
=======
.event-hero{
    height:250px;
    margin:30px auto;
    max-weidth:1000px;
    border-radius:30px;
    overflow:hidden;
    position:relative;
    background:url('images/events/musical_banner.jpeg') center/cover;
}

.hero-overlay{
    position:absolute;
    inset:0;
    background:rgba(0,0,0,0.45);
    display:flex;
    flex-direction:column;
    justify-content:center;
    align-items:center;
    text-align:center;
    color:white;
}

.hero-overlay h1{
    font-size:2.8rem;
    margin-bottom:20px;
}

.hero-overlay p{
    font-size:1 rem;
    max-width:800px;
}

.event-info{
    display:flex;
    gap:20px;
    margin:20px 0 30px;
    font-weight:600;
}

.gallery-item{
    background:white;
    border-radius:20px;
    overflow:hidden;
    box-shadow:0 5px 20px rgba(0,0,0,.08);
    transition:0.3s;
}

.gallery-item:hover{
    transform:translateY(-8px);
>>>>>>> 1fee1452aca3ced30870f8c1e91161e1616d80f1
}

.gallery-item img{
    width:100%;
<<<<<<< HEAD
    height:250px;
    object-fit:cover;
    border-radius:10px;
}

.gallery-item{
    background:#fff;
    padding:10px;
    border-radius:10px;
}

</style>

=======
    height:300px;
    object-fit:cover;
    
}

.gallery-caption{
    text-align:center;
    font-size:24px;
    font-weight:600;
    padding:18px;
}
.gallery-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(320px,1fr));
    gap:30px;
    padding:0 40px 50px;
}
.container{
    max-width:1200px;
    margin:0 auto;
    padding:0 40px;
}
.event-details{
    background:white;
    max-width:900px;
    margin:0 auto 50px auto;
    padding:18px 25px;
    border-radius:25px;
    display:flex;
    justify-content:center;
    align-items:center;
    gap:40px;
    box-shadow:0 8px 25px rgba(0,0,0,0.08);
}

.detail-card{
    display:flex;
    align-items:center;
    gap:20px;
}

.detail-icon{
    width:45px;
    height:45px;
    border-radius:15px;
    background:#ffe9ec;
    display:flex;
    justify-content:center;
    align-items:center;
    font-size:20px;
}

.detail-card span{
    color:#777;
    display:block;
    margin-bottom:5px;
}

.detail-card h3{
    margin:0;
    font-size:20px;
}

.detail-divider{
    width:1px;
    height:70px;
    background:#ddd;
}
.page-wrapper{
    max-width:1400px;
    margin:0 auto;
    transform:scale(0.9);
    transform-origin:top center;
}
</style>
<div class="page-wrapper">

    <!-- All your current page content here -->

</div>
>>>>>>> 1fee1452aca3ced30870f8c1e91161e1616d80f1
<?php
include 'db.php';
include 'header.php';

$event_id = (int)$_GET['id'];

$event_query = mysqli_query($conn,
    "SELECT * FROM events WHERE id=$event_id");

$event = mysqli_fetch_assoc($event_query);

$gallery_query = mysqli_query($conn,
    "SELECT * FROM event_gallery WHERE event_id=$event_id");
?>

<div class="container">

<<<<<<< HEAD
<h1><?php echo htmlspecialchars($event['title']); ?></h1>

<p>
<?php echo htmlspecialchars($event['description']); ?>
</p>

<p>
Date:
<?php echo htmlspecialchars($event['event_date']); ?>
</p>

<p>
Location:
<?php echo htmlspecialchars($event['location']); ?>
</p>

=======
<div class="event-hero">
    <div class="hero-overlay">
        <h1><?php echo htmlspecialchars($event['title']); ?></h1>
        <p><?php echo htmlspecialchars($event['description']); ?></p>
    </div>
</div>

<div class="event-details">

    <div class="detail-card">
        <div class="detail-icon">📅</div>

        <div>
            <span>Date</span>
            <h3><?php echo htmlspecialchars($event['event_date']); ?></h3>
        </div>
    </div>

    <div class="detail-divider"></div>

    <div class="detail-card">
        <div class="detail-icon">📍</div>

        <div>
            <span>Location</span>
            <h3><?php echo htmlspecialchars($event['location']); ?></h3>
        </div>
    </div>

</div>
>>>>>>> 1fee1452aca3ced30870f8c1e91161e1616d80f1
<div class="gallery-grid">

<?php while($photo=mysqli_fetch_assoc($gallery_query)){ ?>

<<<<<<< HEAD
    <div class="gallery-item">

        <img src="<?php echo htmlspecialchars($photo['image']); ?>">

        <p>
            <?php echo htmlspecialchars($photo['caption']); ?>
        </p>

    </div>

=======
<div class="gallery-item">

    <img src="<?php echo htmlspecialchars($photo['image']); ?>">

    <div class="gallery-caption">
        <?php echo htmlspecialchars($photo['caption']); ?>
    </div>

</div>

>>>>>>> 1fee1452aca3ced30870f8c1e91161e1616d80f1
<?php } ?>

</div>

</div>

<?php include 'footer.php'; ?>
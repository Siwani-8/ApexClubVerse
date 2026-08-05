<style>

.gallery-grid{
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(250px,1fr));
    gap:20px;
}

.gallery-item img{
    width:100%;
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

<div class="gallery-grid">

<?php while($photo=mysqli_fetch_assoc($gallery_query)){ ?>

    <div class="gallery-item">

        <img src="<?php echo htmlspecialchars($photo['image']); ?>">

        <p>
            <?php echo htmlspecialchars($photo['caption']); ?>
        </p>

    </div>

<?php } ?>

</div>

</div>

<?php include 'footer.php'; ?>
<?php
include 'db.php';
include 'header.php';

$event_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$event_query = mysqli_query($conn,
    "SELECT * FROM events WHERE id=$event_id"
);

$event = mysqli_fetch_assoc($event_query);

if(!$event){
    echo "Event not found";
    exit;
}

$gallery_query = mysqli_query($conn,
    "SELECT * FROM event_gallery WHERE event_id=$event_id"
);
?>

<style>

.container{
    max-width:1200px;
    margin:0 auto;
    padding:40px;
}


/* HERO */

.event-hero{
    height:250px;
    margin:30px auto;
    max-width:1000px;
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
    font-size:1rem;
    max-width:800px;
}



/* EVENT DETAILS */


.event-details{

    background:white;
    max-width:900px;
    margin:0 auto 50px auto;
    padding:25px;
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



/* GALLERY */


.gallery-grid{

    display:grid;

    grid-template-columns:
    repeat(auto-fill,minmax(320px,1fr));

    gap:30px;

    padding-bottom:50px;

}



.gallery-item{

    background:white;

    padding:10px;

    border-radius:20px;

    overflow:hidden;

    box-shadow:
    0 5px 20px rgba(0,0,0,.08);

    transition:.3s;

}



.gallery-item:hover{

    transform:translateY(-8px);

}



.gallery-item img{

    width:100%;

    height:300px;

    object-fit:cover;

    border-radius:15px;

}



.gallery-caption{

    text-align:center;

    font-size:24px;

    font-weight:600;

    padding:18px;

}



.page-wrapper{

    max-width:1400px;

    margin:auto;

}



@media(max-width:700px){

.event-details{

    flex-direction:column;

}


.detail-divider{

    display:none;

}


.hero-overlay h1{

    font-size:2rem;

}


}

</style>



<div class="page-wrapper">


<div class="container">


<div class="event-hero">

<div class="hero-overlay">

<h1>
<?php echo htmlspecialchars($event['title']); ?>
</h1>


<p>
<?php echo htmlspecialchars($event['description']); ?>
</p>


</div>

</div>




<div class="event-details">


<div class="detail-card">

<div class="detail-icon">
📅
</div>


<div>

<span>Date</span>

<h3>
<?php echo htmlspecialchars($event['event_date']); ?>
</h3>

</div>


</div>




<div class="detail-divider"></div>




<div class="detail-card">


<div class="detail-icon">
📍
</div>


<div>

<span>Location</span>

<h3>
<?php echo htmlspecialchars($event['location']); ?>
</h3>

</div>


</div>



</div>





<div class="gallery-grid">


<?php while($photo=mysqli_fetch_assoc($gallery_query)){ ?>


<div class="gallery-item">


<img 
src="<?php echo htmlspecialchars($photo['image']); ?>"
alt="Event Image"
>



<div class="gallery-caption">

<?php echo htmlspecialchars($photo['caption']); ?>

</div>


</div>


<?php } ?>


</div>



</div>


</div>


<?php include 'footer.php'; ?>
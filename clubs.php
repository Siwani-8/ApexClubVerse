<?php 
session_start();
include 'db.php'; 
include 'header.php'; 

$result = mysqli_query($conn, "SELECT * FROM clubs");
?>

<style>
    .container { max-width: 1200px; margin: 3rem auto; padding: 0 2rem; }
    .page-title { font-size: 2.5rem; color: var(--text-dark); margin-bottom: 2rem; border-bottom: 1px solid #ccc; padding-bottom: 0.5rem; }
    .club-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 2rem; }
    .club-card { background: var(--card-bg); border: 1px solid #dcdbd7; border-top: 4px solid var(--primary-crimson); padding: 2rem; border-radius: 8px; cursor: pointer; transition: 0.2s; }
    .club-card:hover { transform: translateY(-4px); box-shadow: 0 8px 20px rgba(0,0,0,0.08); }
    .club-card h3 { font-size: 1.5rem; color: var(--primary-crimson); margin-bottom: 0.8rem; }
    .club-card p { font-family: 'Segoe UI', sans-serif; color: #555; font-size: 0.95rem; line-height: 1.6; }
    .explore-link { display: inline-block; margin-top: 1.5rem; color: var(--accent-orange); font-weight: bold; font-family: sans-serif; font-size: 0.85rem; }
</style>

<div class="container">
    <h1 class="page-title">Campus Clubs</h1>
   <div class="club-grid">
    <?php while($row = mysqli_fetch_assoc($result)) { ?>
        <div class="club-card" onclick="window.location.href='club_detail.php?id=<?php echo $row['id']; ?>'">
                <h3><?php echo $row['name']; ?></h3>
                <p><?php echo $row['description']; ?></p>
                <span class="explore-link">Explore Club Page &rarr;</span>
            </div>
        <?php } ?>
    </div>
</div>

<?php include 'footer.php'; ?>
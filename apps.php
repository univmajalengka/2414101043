<?php include 'includes/header.php'; ?>

<h1 class="page-header">Aplikasi Premium Tersedia</h1>
<p style="text-align: center; max-width: 600px; margin: -1.5rem auto 3rem auto; color: #ccc;">Temukan dan pilih paket langganan terbaik untuk aplikasi produktivitas dan hiburan favorit Anda.</p>

<div class="card-grid">
    <?php
    $sql = "SELECT * FROM apps ORDER BY nama_aplikasi ASC";
    $result = mysqli_query($conn, $sql);
    while ($app = mysqli_fetch_assoc($result)) {
    ?>
    <div class="card">
        <div class="card-header">
            <i class="<?php echo htmlspecialchars($app['icon_class']); ?>"></i>
            <h3><?php echo htmlspecialchars($app['nama_aplikasi']); ?></h3>
        </div>
        <div class="card-body">
            <p><?php echo htmlspecialchars($app['deskripsi']); ?></p>
            
            <?php
            $plan_sql = "SELECT * FROM plans WHERE app_id = {$app['id']} ORDER BY harga ASC";
            $plan_result = mysqli_query($conn, $plan_sql);
            while ($plan = mysqli_fetch_assoc($plan_result)) {
            ?>
            <?php }?>

            <?php 
            if (isset($_SESSION['user_id'])) {
                   echo '<a href="dashboard.php" class="btn btn-primary" style="width: 100%; text-align: center;">Pilih di Dashboard</a>';
            } else {
                    echo '<a href="login.php" class="btn btn-primary" style="width: 100%; text-align: center;">Login untuk berlangganan</a>';
            }
            ?>
        </div>
    </div>
    <?php }?>
</div>

<?php include 'includes/footer.php'; ?>
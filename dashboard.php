<?php
include 'includes/header.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<h1 class="page-header">Selamat Datang, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
<p style="text-align: center; max-width: 600px; margin: -1.5rem auto 3rem auto; color: #ccc;">Silahkan pilih langganan aplikasi yang anda inginkan</p>


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
            
            <a href="checkout.php?app_id=<?php echo $app['id']; ?>" class="btn btn-primary" style="width: 100%; text-align: center;">
                Beli Sekarang
            </a>
        </div>
    </div>
    <?php } ?>
</div>

<?php include 'includes/footer.php'; ?>
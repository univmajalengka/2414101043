<?php
include 'includes/header.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['app_id'])) {
    header("Location: login.php");
    exit();
}

$app_id = (int)$_GET['app_id'];
$user_id = $_SESSION['user_id'];

$app_sql = "SELECT * FROM apps WHERE id = $app_id";
$app_result = mysqli_query($conn, $app_sql);
if (mysqli_num_rows($app_result) == 0) {
    header("Location: dashboard.php");
    exit();
}
$app = mysqli_fetch_assoc($app_result);

$plans_sql = "SELECT * FROM plans WHERE app_id = $app_id ORDER BY harga ASC";
$plans_result = mysqli_query($conn, $plans_sql);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['plan_id']) && isset($_POST['payment_method'])) {
        $plan_id = (int)$_POST['plan_id'];
        
        $order_sql = "INSERT INTO orders (user_id, plan_id, status) VALUES ('$user_id', '$plan_id', 'Proses')";
        if (mysqli_query($conn, $order_sql)) {
            header("Location: orders.php?status=success");
            exit();
        } else {
            $error_msg = "Terjadi kesalahan. Gagal membuat pesanan.";
        }
    } else {
        $error_msg = "Silakan pilih paket langganan dan metode pembayaran.";
    }
}
?>

<div class="checkout-container">
    <div class="checkout-header">
        <i class="<?php echo htmlspecialchars($app['icon_class']); ?>"></i>
        <div>
            <h1>Checkout: <?php echo htmlspecialchars($app['nama_aplikasi']); ?></h1>
            <p><?php echo htmlspecialchars($app['deskripsi']); ?></p>
        </div>
    </div>

    <?php if (isset($error_msg)): ?>
        <p class="error-message"><?php echo $error_msg; ?></p>
    <?php endif; ?>

    <form action="checkout.php?app_id=<?php echo $app_id; ?>" method="POST">
        <div class="checkout-section">
            <h2>1. Pilih Paket Langganan</h2>
            <div class="plan-selection">
                <?php while($plan = mysqli_fetch_assoc($plans_result)): ?>
                <label class="plan-option">
                    <input type="radio" name="plan_id" value="<?php echo $plan['id']; ?>" required>
                    <div class="plan-details">
                        <span class="plan-duration"><?php echo htmlspecialchars($plan['durasi']); ?></span>
                        <span class="plan-price">Rp <?php echo number_format($plan['harga']); ?></span>
                    </div>
                </label>
                <?php endwhile; ?>
            </div>
        </div>

        <div class="checkout-section">
            <h2>2. Pilih Metode Pembayaran</h2>
            <div class="payment-selection">
                <label class="payment-option">
                    <input type="radio" name="payment_method" value="va" required>
                    <i class="fas fa-university"></i>
                    <span>Virtual Account</span>
                </label>
                <label class="payment-option">
                    <input type="radio" name="payment_method" value="ewallet">
                    <i class="fas fa-wallet"></i>
                    <span>E-Wallet (GoPay, OVO)</span>
                </label>
                <label class="payment-option">
                    <input type="radio" name="payment_method" value="cc">
                    <i class="fas fa-credit-card"></i>
                    <span>Kartu Kredit</span>
                </label>
            </div>
        </div>
        
        <button type="submit" class="btn btn-primary btn-checkout">Konfirmasi & Bayar</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
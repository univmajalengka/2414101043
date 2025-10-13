<?php
include 'includes/header.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$limit = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$total_result = mysqli_query($conn, "SELECT COUNT(id) as total FROM orders WHERE user_id = $user_id");
$total_orders = mysqli_fetch_assoc($total_result)['total'];
$total_pages = ceil($total_orders / $limit);

$sql = "SELECT orders.id, apps.nama_aplikasi, plans.durasi, plans.harga, orders.status, orders.tanggal
        FROM orders
        JOIN plans ON orders.plan_id = plans.id
        JOIN apps ON plans.app_id = apps.id
        WHERE orders.user_id = $user_id
        ORDER BY orders.tanggal DESC
        LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $sql);
?>

<?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
    <div class="alert-success">
        Pesanan Anda berhasil dibuat dan sedang diproses!
    </div>
<?php endif; ?>

<h1 class="page-header">Riwayat Pesanan Saya</h1>
<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Aplikasi</th>
                <th>Paket</th>
                <th>Harga</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?php echo date('d M Y, H:i', strtotime($row['tanggal'])); ?></td>
                <td><?php echo htmlspecialchars($row['nama_aplikasi']); ?></td>
                <td><?php echo htmlspecialchars($row['durasi']); ?></td>
                <td>Rp <?php echo number_format($row['harga']); ?></td>
                <td>
                    <span class="status status-<?php echo strtolower($row['status']); ?>">
                        <?php echo htmlspecialchars($row['status']); ?>
                    </span>
                </td>
            </tr>
            <?php endwhile; ?>
            <?php if(mysqli_num_rows($result) == 0): ?>
            <tr>
                <td colspan="5" style="text-align: center;">Anda belum memiliki riwayat pesanan.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="pagination">
    <?php if ($page > 1): ?>
        <a href="orders.php?page=<?php echo $page - 1; ?>">« Prev</a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <a href="orders.php?page=<?php echo $i; ?>" class="<?php if ($i == $page) echo 'active'; ?>">
            <?php echo $i; ?>
        </a>
    <?php endfor; ?>

    <?php if ($page < $total_pages): ?>
        <a href="orders.php?page=<?php echo $page + 1; ?>">Next »</a>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
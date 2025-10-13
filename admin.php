<?php
include 'includes/header.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];
    mysqli_query($conn, "UPDATE orders SET status = '$status' WHERE id = $order_id");
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_app'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama_aplikasi']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $icon = mysqli_real_escape_string($conn, $_POST['icon_class']);
    mysqli_query($conn, "INSERT INTO apps (nama_aplikasi, deskripsi, icon_class) VALUES ('$nama', '$deskripsi', '$icon')");
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_plan'])) {
    $app_id = $_POST['app_id'];
    $durasi = mysqli_real_escape_string($conn, $_POST['durasi']);
    $harga = (int)$_POST['harga'];
    mysqli_query($conn, "INSERT INTO plans (app_id, durasi, harga) VALUES ('$app_id', '$durasi', '$harga')");
}
if (isset($_GET['delete_app'])) {
    $app_id = $_GET['delete_app'];
    mysqli_query($conn, "DELETE FROM apps WHERE id = $app_id");
}
if (isset($_GET['delete_plan'])) {
    $plan_id = $_GET['delete_plan'];
    mysqli_query($conn, "DELETE FROM plans WHERE id = $plan_id");
}

$limit = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$total_result = mysqli_query($conn, "SELECT COUNT(id) as total FROM orders");
$total_orders = mysqli_fetch_assoc($total_result)['total'];
$total_pages = ceil($total_orders / $limit);

$sql_orders = "SELECT orders.id, users.username, apps.nama_aplikasi, plans.durasi, orders.status, orders.tanggal
               FROM orders
               JOIN users ON orders.user_id = users.id
               JOIN plans ON orders.plan_id = plans.id
               JOIN apps ON plans.app_id = apps.id
               ORDER BY orders.id DESC
               LIMIT $limit OFFSET $offset";
$orders_result = mysqli_query($conn, $sql_orders);

$sql_apps = "SELECT * FROM apps ORDER BY nama_aplikasi ASC";
$apps_result = mysqli_query($conn, $sql_apps);
?>

<h1 class="page-header">Dashboard Admin</h1>

<h2>Daftar Pesanan Pengguna</h2>
<div class="table-container" style="margin-bottom: 1rem;">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Pengguna</th>
                <th>Aplikasi</th>
                <th>Paket</th>
                <th>Tanggal</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($orders_result)): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo htmlspecialchars($row['username']); ?></td>
                <td><?php echo htmlspecialchars($row['nama_aplikasi']); ?></td>
                <td><?php echo htmlspecialchars($row['durasi']); ?></td>
                <td><?php echo date('d M Y', strtotime($row['tanggal'])); ?></td>
                <td><span class="status status-<?php echo strtolower($row['status']); ?>"><?php echo $row['status']; ?></span></td>
                <td>
                    <form action="admin.php?page=<?php echo $page; ?>" method="POST" style="display: flex; gap: 5px;">
                        <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                        <select name="status" style="padding: 5px;">
                            <option value="Proses" <?php echo $row['status'] == 'Proses' ? 'selected' : ''; ?>>Proses</option>
                            <option value="Berhasil" <?php echo $row['status'] == 'Berhasil' ? 'selected' : ''; ?>>Berhasil</option>
                            <option value="Gagal" <?php echo $row['status'] == 'Gagal' ? 'selected' : ''; ?>>Gagal</option>
                        </select>
                        <button type="submit" name="update_status" class="btn" style="background: #0275d8; color: #fff; padding: 5px 10px;">Update</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
            <?php if(mysqli_num_rows($orders_result) == 0): ?>
            <tr><td colspan="7" style="text-align:center;">Tidak ada pesanan.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="pagination">
    <?php if ($page > 1): ?>
        <a href="admin.php?page=<?php echo $page - 1; ?>">« Prev</a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <a href="admin.php?page=<?php echo $i; ?>" class="<?php if ($i == $page) echo 'active'; ?>">
            <?php echo $i; ?>
        </a>
    <?php endfor; ?>

    <?php if ($page < $total_pages): ?>
        <a href="admin.php?page=<?php echo $page + 1; ?>">Next »</a>
    <?php endif; ?>
</div>

<h2 style="margin-top: 2rem;">Kelola Aplikasi & Paket Premium</h2>
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
    <div class="form-container" style="margin: 0;">
        <h3>Tambah Aplikasi Baru</h3>
        <form action="admin.php" method="POST">
            <div class="form-group">
                <label>Nama Aplikasi</label>
                <input type="text" name="nama_aplikasi" required>
            </div>
            <div class="form-group">
                <label>Deskripsi Singkat</label>
                <input type="text" name="deskripsi" required>
            </div>
             <div class="form-group">
                <label>icon</label>
                <input type="text" name="icon_class" placeholder="Contoh: fab fa-spotify" required>
            </div>
            <button type="submit" name="add_app" class="btn btn-primary">Tambah Aplikasi</button>
        </form>
    </div>
    <div class="form-container" style="margin: 0;">
        <h3>Tambah Paket Langganan</h3>
        <form action="admin.php" method="POST">
            <div class="form-group">
                <label>Pilih Aplikasi</label>
                <select name="app_id" required>
                    <?php
                    mysqli_data_seek($apps_result, 0);
                    while ($app = mysqli_fetch_assoc($apps_result)) {
                        echo "<option value='{$app['id']}'>" . htmlspecialchars($app['nama_aplikasi']) . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label>Paket & Durasi</label>
                <input type="text" name="durasi" required>
            </div>
             <div class="form-group">
                <label>Harga</label>
                <input type="number" name="harga" required>
            </div>
            <button type="submit" name="add_plan" class="btn btn-primary">Tambah Paket</button>
        </form>
    </div>
</div>

<div class="table-container" style="margin-top: 2rem;">
    <table>
        <thead>
            <tr>
                <th>Aplikasi</th>
                <th>Paket Tersedia</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php
        mysqli_data_seek($apps_result, 0);
        while ($app = mysqli_fetch_assoc($apps_result)):
        ?>
            <tr>
                <td><strong><?php echo htmlspecialchars($app['nama_aplikasi']); ?></strong></td>
                <td>
                <?php
                    $plan_sql = "SELECT * FROM plans WHERE app_id = {$app['id']}";
                    $plan_res = mysqli_query($conn, $plan_sql);
                    while($plan = mysqli_fetch_assoc($plan_res)){
                        echo "<div>" . htmlspecialchars($plan['durasi']) . " (Rp " . number_format($plan['harga']) . ") <a href='admin.php?delete_plan={$plan['id']}' style='color: #d9534f; margin-left: 10px;' onclick='return confirm(\"Yakin hapus paket ini?\")'>Hapus</a></div>";
                    }
                ?>
                </td>
                <td>
                    <a href="admin.php?delete_app=<?php echo $app['id']; ?>" class="btn" style="background-color: #d9534f; color: #fff;" onclick="return confirm('Yakin hapus aplikasi ini? Semua paket terkait akan terhapus.')">Hapus Aplikasi</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

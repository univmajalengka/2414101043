<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$username_sess = $_SESSION['user'];
$queryUser = mysqli_query($koneksi, "SELECT id, username FROM users WHERE username = '$username_sess'");
$userData = mysqli_fetch_assoc($queryUser);
$current_user_id = $userData['id'];

if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_destroy();
    header("Location: login.php");
    exit;
}

$sukses = "";
$error  = "";

if (isset($_SESSION['flash_sukses'])) {
    $sukses = $_SESSION['flash_sukses'];
    unset($_SESSION['flash_sukses']);
}
if (isset($_SESSION['flash_error'])) {
    $error = $_SESSION['flash_error'];
    unset($_SESSION['flash_error']);
}

if (isset($_POST['simpan'])) {
    $id = $_POST['id']; 
    $nama = htmlspecialchars($_POST['nama_pemesan']); 
    $hp = htmlspecialchars($_POST['no_hp']);
    $tgl = $_POST['tanggal_pesan'];
    $waktu = $_POST['waktu_pelaksanaan'];
    $paket = $_POST['id_paket'];
    $peserta = $_POST['jumlah_peserta'];
    $tagihan = $_POST['total_tagihan'];
    if (empty($nama) || empty($hp) || empty($tgl) || empty($waktu) || empty($paket) || empty($peserta)) {
        $_SESSION['flash_error'] = "Gagal! Semua kolom wajib diisi.";
        header("Location: pemesanan.php"); 
        exit;
    }

    $tgl_sekarang = date('Y-m-d');
    if ($tgl < $tgl_sekarang) {
        $_SESSION['flash_error'] = "Gagal! Tanggal pesanan tidak boleh lampau (minimal hari ini).";
        header("Location: pemesanan.php");
        exit;
    }   

    if ($id) {
        $query = "UPDATE pesanan SET nama_pemesan=?, no_hp=?, tanggal_pesan=?, waktu_pelaksanaan=?, id_paket=?, jumlah_peserta=?, total_tagihan=? WHERE id=? AND user_id=?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "sssiiidii", $nama, $hp, $tgl, $waktu, $paket, $peserta, $tagihan, $id, $current_user_id);
    } else {
         $query = "INSERT INTO pesanan (user_id, nama_pemesan, no_hp, tanggal_pesan, waktu_pelaksanaan, id_paket, jumlah_peserta, total_tagihan) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "isssiiid", $current_user_id, $nama, $hp, $tgl, $waktu, $paket, $peserta, $tagihan);
    }

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['flash_sukses'] = "Reservasi atas nama <b>$nama</b> berhasil disimpan!";
        header("Location: pemesanan.php");
        exit;
    } else {
        $error = "Gagal menyimpan data: " . mysqli_error($koneksi);
    }
}

if (isset($_GET['op']) && $_GET['op'] == 'delete') {
    $id = $_GET['id'];
    $query = "DELETE FROM pesanan WHERE id=? AND user_id=?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "ii", $id, $current_user_id);
    
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['flash_sukses'] = "Data reservasi berhasil dihapus!";
        header("Location: pemesanan.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pesanan - Situ Cipanten</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/style.css">
    
    <style>
        body { background-color: #f0f2f5; padding-top: 80px; }
        .card-custom { border: none; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); margin-bottom: 2rem; }
        .card-header-custom { background: linear-gradient(45deg, var(--primary-color), var(--secondary-color)); color: white; border-radius: 15px 15px 0 0; padding: 15px 25px; font-weight: 600; }
        .welcome-banner { background: white; padding: 20px; border-radius: 15px; margin-bottom: 30px; border-left: 5px solid var(--primary-color); display: flex; justify-content: space-between; align-items: center; box-shadow: 0 5px 15px rgba(0,0,0,0.03); }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="bi bi-water"></i> Situ Cipanten
            </a>
            <div class="ms-auto d-flex align-items-center">
                <span class="text-white me-3 d-none d-md-block">Halo, <strong><?= htmlspecialchars($_SESSION['user']) ?></strong></span>
                <a href="pemesanan.php?action=logout" class="btn btn-light btn-sm rounded-pill px-3 text-primary fw-bold">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container admin-header">
        
        <div class="welcome-banner">
            <div>
                <h4 class="mb-1">Selamat Datang Di Form Pemesanan Paket Cipanten</h4>
                <p class="text-muted mb-0">Kelola rencana liburan Anda di Situ Cipanten dengan mudah.</p>
            </div>
            <a href="index.php" class="btn btn-outline-primary"><i class="bi bi-globe"></i> Lihat Website</a>
        </div>

        <?php if($sukses): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> <?= $sukses ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= $error ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-lg-4">
                <div class="card card-custom">
                    <div class="card-header-custom">
                        <i class="bi bi-calendar-plus me-2"></i> Buat Reservasi Baru
                    </div>
                    <div class="card-body">
                        <form action="" method="POST" id="formPesanan">
                            <input type="hidden" name="id" id="id">
                            
                            <div class="mb-3">
                                <label class="form-label">Nama Lengkap (Sesuai KTP)</label>
                                <input type="text" name="nama_pemesan" id="nama_pemesan" class="form-control" placeholder="Contoh: Mulyono" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Nomor WhatsApp</label>
                                <input type="number" name="no_hp" id="no_hp" class="form-control" placeholder="08xxxx" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Rencana Tanggal</label>
                                <input type="date" name="tanggal_pesan" id="tanggal_pesan" class="form-control" required>
                            </div>

                            <div class="row">
                                <div class="col-6 mb-3">
                                    <label class="form-label">Durasi (Hari)</label>
                                    <input type="number" name="waktu_pelaksanaan" id="waktu_pelaksanaan" class="form-control" min="1" value="1" required>
                                </div>
                                <div class="col-6 mb-3">
                                    <label class="form-label">Peserta (Org)</label>
                                    <input type="number" name="jumlah_peserta" id="jumlah_peserta" class="form-control" min="1" value="1" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Pilih Paket Wisata</label>
                                <select name="id_paket" id="id_paket" class="form-select" required>
                                    <option value="" data-harga="0">-- Pilih Paket --</option>
                                    <option value="1" data-harga="350000">Paket 1 - Gazebo Lengkap (Rp 350.000)</option>
                                    <option value="2" data-harga="200000">Paket 2 - Standar (Rp 200.000)</option>
                                    <option value="3" data-harga="100000">Paket 3 - Hemat (Rp 100.000)</option>
                                    <option value="4" data-harga="450000">Paket 4 - Prewedding (Rp 450.000)</option>
                                </select>
                            </div>

                            <div class="card bg-light border-0 mb-3">
                                <div class="card-body p-3">
                                    <small class="text-muted d-block mb-1">Estimasi Biaya:</small>
                                    <div class="input-group input-group-sm mb-2">
                                        <span class="input-group-text bg-transparent border-0 ps-0">Harga Paket:</span>
                                        <input type="text" id="harga_paket_display" class="form-control bg-transparent border-0 text-end fw-bold p-0" readonly value="-">
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center border-top pt-2">
                                        <span class="fw-bold">Total:</span>
                                        <input type="number" name="total_tagihan" id="total_tagihan" class="form-control-plaintext text-end fw-bold text-success fs-5 p-0" readonly value="0">
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" name="simpan" class="btn btn-primary"><i class="bi bi-send-fill me-2"></i> Booking Sekarang</button>
                                <button type="reset" id="btnReset" class="btn btn-outline-secondary">Batal / Reset</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card card-custom">
                    <div class="card-header-custom d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-clock-history me-2"></i> Riwayat Reservasi Saya</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">Detail Booking</th>
                                        <th>Paket</th>
                                        <th>Biaya</th>
                                        <th class="text-end pe-4">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $queryShow = "SELECT * FROM pesanan WHERE user_id = ? ORDER BY id DESC";
                                    $stmtShow = mysqli_prepare($koneksi, $queryShow);
                                    mysqli_stmt_bind_param($stmtShow, "i", $current_user_id);
                                    mysqli_stmt_execute($stmtShow);
                                    $resultShow = mysqli_stmt_get_result($stmtShow);

                                    if (mysqli_num_rows($resultShow) == 0) {
                                        echo "<tr><td colspan='4' class='text-center py-5 text-muted'>Belum ada reservasi. Yuk booking sekarang!</td></tr>";
                                    }

                                    while ($r2 = mysqli_fetch_array($resultShow)) {
                                        $nama_paket = "Paket " . $r2['id_paket'];
                                        $tanggal_indo = date('d F Y', strtotime($r2['tanggal_pesan']));
                                    ?>
                                    <tr>
                                        <td class="ps-4">
                                            <div class="fw-bold text-dark"><?= htmlspecialchars($r2['nama_pemesan']) ?></div>
                                            <small class="text-muted">
                                                <i class="bi bi-calendar-event"></i> <?= $tanggal_indo ?><br>
                                                <i class="bi bi-people"></i> <?= $r2['jumlah_peserta'] ?> Orang &bull; <?= $r2['waktu_pelaksanaan'] ?> Hari &bull; <small class="text-secondary">No. HP: <?= htmlspecialchars($r2['no_hp']) ?>
                                            </small>
                                        </td>
                                        <td><span class="badge bg-info bg-opacity-10 text-info border border-info rounded-pill"><?= $nama_paket ?></span></td>
                                        <td class="fw-bold text-success">Rp <?= number_format($r2['total_tagihan'], 0, ',', '.') ?></td>
                                        <td class="text-end pe-4">
                                            <button type="button" class="btn btn-outline-warning btn-sm border-0" 
                                                title="Edit Reservasi"
                                                onclick="editData(
                                                    '<?= $r2['id'] ?>',
                                                    '<?= $r2['nama_pemesan'] ?>',
                                                    '<?= $r2['no_hp'] ?>',
                                                    '<?= $r2['tanggal_pesan'] ?>',
                                                    '<?= $r2['waktu_pelaksanaan'] ?>',
                                                    '<?= $r2['jumlah_peserta'] ?>',
                                                    '<?= $r2['id_paket'] ?>'
                                                )">
                                                <i class="bi bi-pencil-fill"></i>
                                            </button>
                                            
                                            <a href="pemesanan.php?op=delete&id=<?= $r2['id'] ?>" 
                                               class="btn btn-outline-danger btn-sm border-0" 
                                               title="Batalkan"
                                               onclick="return confirm('Yakin ingin membatalkan reservasi ini?')">
                                                <i class="bi bi-trash-fill"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script/script.js"></script>
</body>
</html>
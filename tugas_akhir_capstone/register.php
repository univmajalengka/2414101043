<?php
session_start();
include 'koneksi.php';

if (isset($_SESSION['login'])) {
    header("Location: pemesanan.php");
    exit;
}

$message = "";
$messageType = "";

if (isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($username) || empty($password)) {
        $message = "Username dan Password wajib diisi!";
        $messageType = "danger";
    } 
    elseif ($password !== $confirm_password) {
        $message = "Konfirmasi password tidak cocok!";
        $messageType = "danger";
    } else {
        $checkQuery = "SELECT id FROM users WHERE username = ?";
        $stmtCheck = mysqli_prepare($koneksi, $checkQuery);
        mysqli_stmt_bind_param($stmtCheck, "s", $username);
        mysqli_stmt_execute($stmtCheck);
        mysqli_stmt_store_result($stmtCheck);

        if (mysqli_stmt_num_rows($stmtCheck) > 0) {
            $message = "Username sudah terdaftar! Silakan gunakan yang lain.";
            $messageType = "danger";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            $insertQuery = "INSERT INTO users (username, password) VALUES (?, ?)";
            $stmtInsert = mysqli_prepare($koneksi, $insertQuery);
            mysqli_stmt_bind_param($stmtInsert, "ss", $username, $hashed_password);

            if (mysqli_stmt_execute($stmtInsert)) {
                $message = "Registrasi Berhasil! Silakan Login.";
                $messageType = "success";
                $username = ""; 
            } else {
                $message = "Terjadi kesalahan sistem: " . mysqli_error($koneksi);
                $messageType = "danger";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - Situ Cipanten</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="login-body">

    <div class="login-wrapper">
        <div class="login-image-side">
            <div class="login-overlay"></div>
            <div class="login-caption">
                <h1>Bergabunglah Bersama Kami</h1>
                <p class="lead">Buat akun untuk mengelola reservasi Situ Cipanten.</p>
            </div>
        </div>

        <div class="login-form-side">
            <div class="login-form-container">
                <div class="text-center mb-4">
                    <a href="index.php" class="brand-logo-login text-decoration-none">
                        <i class="bi bi-water"></i> Situ Cipanten
                    </a>
                    <h4 class="mt-2">Buat Akun Baru</h4>
                </div>

                <?php if($message): ?>
                    <div class="alert alert-<?= $messageType ?> d-flex align-items-center" role="alert">
                        <i class="bi bi-<?= ($messageType == 'success') ? 'check-circle' : 'exclamation-triangle' ?>-fill me-2"></i>
                        <div><?= $message ?></div>
                    </div>
                <?php endif; ?>

                <form action="" method="POST">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="username" name="username" placeholder="Username" value="<?= isset($username) ? htmlspecialchars($username) : '' ?>" required>
                        <label for="username"><i class="bi bi-person me-2"></i>Username Baru</label>
                    </div>
                    
                    <div class="form-floating mb-3">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                        <label for="password"><i class="bi bi-lock me-2"></i>Password</label>
                    </div>

                    <div class="form-floating mb-4">
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Konfirmasi Password" required>
                        <label for="confirm_password"><i class="bi bi-check2-circle me-2"></i>Ulangi Password</label>
                    </div>

                    <div class="d-grid gap-2 mb-4">
                        <button type="submit" name="register" class="btn btn-primary btn-lg btn-login-custom text-white py-3">
                            <i class="bi bi-person-plus-fill me-2"></i> Daftar Sekarang
                        </button>
                    </div>
                </form> <div class="text-center">
                    <p class="text-muted mb-0">Sudah punya akun?</p>
                    <a href="login.php" class="text-primary text-decoration-none fw-bold fs-5">
                        Login Disini <i class="bi bi-arrow-right"></i>
                    </a>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
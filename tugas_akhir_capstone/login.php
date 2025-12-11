<?php
session_start();
include 'koneksi.php';

if (isset($_SESSION['login'])) {
    header("Location: pemesanan.php");
    exit;
}

$error = "";

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = mysqli_prepare($koneksi, "SELECT * FROM users WHERE username = ?");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        if (password_verify($password, $row['password'])) { 
            $_SESSION['login'] = true;
            $_SESSION['user'] = $row['username'];
            header("Location: pemesanan.php");
            exit;
        }
    }
    $error = "Username atau Password salah!";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Situ Cipanten</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="login-body">

    <div class="login-wrapper">
        <div class="login-image-side">
            <div class="login-overlay"></div>
            <div class="login-caption">
                <h1>Welcome Back!</h1>
                <p class="lead">Kelola menu pesanan Situ Cipanten dengan mudah dan aman.</p>
                <p class="small text-white-50">&copy; 2024 Situ Cipanten Management System</p>
            </div>
        </div>

        <div class="login-form-side">
            <div class="login-form-container">
                <div class="text-center mb-5">
                    <a href="index.php" class="brand-logo-login text-decoration-none">
                        <i class="bi bi-water"></i> Situ Cipanten
                    </a>
                    <p class="text-muted">Silakan login untuk melihat pesanan</p>
                </div>

                <?php if($error): ?>
                    <div class="alert alert-danger d-flex align-items-center" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <div><?= $error ?></div>
                    </div>
                <?php endif; ?>

                <form action="" method="POST">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="username" name="username" placeholder="Username" required autofocus>
                        <label for="username"><i class="bi bi-person me-2"></i>Username</label>
                    </div>
                    
                    <div class="form-floating mb-4">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                        <label for="password"><i class="bi bi-lock me-2"></i>Password</label>
                    </div>

                    <div class="d-grid gap-2 mb-4">
                        <button type="submit" name="login" class="btn btn-primary btn-lg btn-login-custom text-white py-3">
                            <i class="bi bi-box-arrow-in-right me-2"></i> Masuk
                        </button>
                    </div>

                    <div class="text-center mb-3">
                        <span class="text-muted">Belum punya akun?</span>
                        <a href="register.php" class="text-primary text-decoration-none fw-bold ms-1">Daftar</a>
                    </div>

                    <div class="text-center">
                        <a href="index.php" class="text-secondary text-decoration-none small">
                            <i class="bi bi-arrow-left me-1"></i> Kembali ke Halaman Utama
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
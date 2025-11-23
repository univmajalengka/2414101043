<?php $body_class = 'auth-page'; ?>
<?php
include 'includes/header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt_check = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt_check->bind_param("ss", $username, $email);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        $error = "Username atau Email sudah digunakan!";
    } else {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $stmt_insert = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt_insert->bind_param("sss", $username, $email, $hashed_password);
        
        if ($stmt_insert->execute()) {
            header("Location: login.php?status=registered");
            exit();
        } else {
            $error = "Gagal mendaftar. Silakan coba lagi.";
        }
        $stmt_insert->close();
    }
    $stmt_check->close();
}
?>

<div class="form-container">
    <h2>Registrasi</h2>
    <?php if (isset($error)): ?>
        <p style="color: red; text-align: center;"><?php echo $error; ?></p>
    <?php endif; ?>
    <form action="register.php" method="POST" id="registerForm">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>
        <div class="form-group">
            <label for="confirm_password">Konfirmasi Password</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
        </div>
        <button type="submit" class="btn btn-primary" style="width: 100%;">Daftar</button>
    </form>
    <p style="text-align: center; margin-top: 1rem;">Sudah punya akun? <a href="login.php" style="color: var(--primary-color);">Login</a></p>
</div>

<?php include 'includes/footer.php'; ?>
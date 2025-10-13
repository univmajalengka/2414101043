<?php $body_class = 'auth-page'; ?>
<?php
include 'includes/header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login_identifier = $_POST['login_identifier'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $login_identifier, $login_identifier);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] == 'admin') {
                header("Location: admin.php");
            } else {
                header("Location: dashboard.php");
            }
            exit();
        } else {
            $error = "Username/Email atau password salah!";
        }
    } else {
        $error = "Username/Email atau password salah!";
    }
    $stmt->close();
}
?>

<div class="form-container">
    <h2>Login</h2>
    <?php if (isset($_GET['status']) && $_GET['status'] == 'registered'): ?>
        <p style="color: green; text-align: center;">Registrasi berhasil! Silakan login.</p>
    <?php endif; ?>
    <?php if (isset($error)): ?>
        <p style="color: red; text-align: center;"><?php echo $error; ?></p>
    <?php endif; ?>
    <form action="login.php" method="POST">
        <div class="form-group">
            <label for="login_identifier">Username atau Email</label>
            <input type="text" id="login_identifier" name="login_identifier" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary" style="width: 100%;">Login</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
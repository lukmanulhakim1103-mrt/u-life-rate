<?php
require 'koneksi.php';
if(isset($_SESSION['admin'])) header("Location: dashboard.php");

if(isset($_POST['login'])) {
    $user = $_POST['username'];
    $pass = $_POST['password'];
    $stmt = $pdo->prepare("SELECT * FROM admin WHERE username = ? AND password = ?");
    $stmt->execute([$user, $pass]);
    if($stmt->rowCount() > 0) {
        $_SESSION['admin'] = $stmt->fetch();
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Username atau kata sandi tidak sesuai.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — U-Life-Rate</title>
    <link rel="stylesheet" href="style.css?v=<?= time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="login-wrapper">
        <div class="login-box">
            <h2><i class="fa-solid fa-star"></i> U-Life-Rate</h2>
            <p>Sistem Manajemen Koleksi Novel, Film, dan Musik</p>

            <?php if(isset($error)): ?>
                <div style="background:#fee2e2; color:#dc2626; padding:10px; border-radius:8px; margin-bottom:15px; font-size:13px;">
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Kata Sandi</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" name="login" class="btn btn-primary btn-block">Masuk</button>
            </form>
        </div>
    </div>
</body>
</html>
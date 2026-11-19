<?php
require 'koneksi.php';
if(!isset($_SESSION['admin'])) header("Location: index.php");

$admin = $_SESSION['admin'];
$mode  = isset($_GET['mode']) ? $_GET['mode'] : 'view';

if(isset($_POST['update_profil'])) {
    $nama = $_POST['nama_lengkap'];
    $email = $_POST['email'];
    $telp = $_POST['no_telp'];
    $deskripsi = $_POST['deskripsi'];
    $foto = $admin['foto'];

    if(isset($_FILES['foto_baru']) && $_FILES['foto_baru']['error'] == 0) {
        if (!is_dir('uploads')) mkdir('uploads', 0777, true);
        $ext = pathinfo($_FILES['foto_baru']['name'], PATHINFO_EXTENSION);
        $nama_file_baru = "uploads/profil_" . time() . "." . $ext;
        if(move_uploaded_file($_FILES['foto_baru']['tmp_name'], $nama_file_baru)) {
            $foto = $nama_file_baru; 
        }
    }

    $stmt = $pdo->prepare("UPDATE admin SET nama_lengkap=?, email=?, no_telp=?, deskripsi=?, foto=? WHERE id=?");
    $stmt->execute([$nama, $email, $telp, $deskripsi, $foto, $admin['id']]);

    $stmt = $pdo->prepare("SELECT * FROM admin WHERE id = ?");
    $stmt->execute([$admin['id']]);
    $_SESSION['admin'] = $stmt->fetch();
    header("Location: profil.php"); exit;
}

$foto_profil = file_exists($admin['foto']) ? htmlspecialchars($admin['foto']) : 'https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_1280.png';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Admin — U-Life-Rate</title>
    <link rel="stylesheet" href="style.css?v=<?= time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="dashboard-layout">
        <div class="sidebar">
            <div class="sidebar-header"><i class="fa-solid fa-star"></i> U-Life-Rate</div>

            <div class="nav-section">Menu Utama</div>
            <ul class="nav-links">
                <li><a href="dashboard.php?page=home" class="nav-item"><i class="fa-solid fa-house"></i> Dashboard</a></li>
                <li><a href="dashboard.php?page=koleksi&tab=semua" class="nav-item"><i class="fa-solid fa-table-columns"></i> Ringkasan Koleksi</a></li>
            </ul>

            <div class="nav-section">Kelola Koleksi</div>
            <ul class="nav-links">
                <li><a href="dashboard.php?page=koleksi&tab=novel" class="nav-item"><i class="fa-solid fa-book"></i> Data Novel</a></li>
                <li><a href="dashboard.php?page=koleksi&tab=film" class="nav-item"><i class="fa-solid fa-film"></i> Data Film</a></li>
                <li><a href="dashboard.php?page=koleksi&tab=music" class="nav-item"><i class="fa-solid fa-music"></i> Data Musik</a></li>
            </ul>

            <div class="nav-section">Data Master</div>
            <ul class="nav-links">
                <li><a href="dashboard.php?page=kategori" class="nav-item"><i class="fa-solid fa-folder-tree"></i> Kategori & Genre</a></li>
            </ul>

            <div class="nav-section">Pengaturan Akun</div>
            <ul class="nav-links">
                <li><a href="profil.php" class="nav-item active"><i class="fa-solid fa-user"></i> Profil Admin</a></li>
            </ul>
        </div>

        <div class="main-wrapper">
            <div class="topbar">
                <strong>Profil Administrator</strong>
                <div class="topbar-actions">
                    <a href="dashboard.php?page=logout" class="btn btn-outline" onclick="return confirm('Apakah Anda yakin ingin keluar dari sistem?')">
                        <i class="fa-solid fa-right-from-bracket"></i> Keluar
                    </a>
                </div>
            </div>

            <div class="content-area">
                <?php if($mode == 'view'): ?>
                    <div class="wa-profile-card">
                        <div class="wa-avatar-section">
                            <img src="<?= $foto_profil ?>" class="wa-avatar" alt="Foto Profil">
                            <div style="margin-top: 8px;">
                                <a href="profil.php?mode=edit" class="btn btn-primary">
                                    <i class="fa-solid fa-pen-to-square"></i> Edit Profil
                                </a>
                            </div>
                        </div>

                        <div class="wa-list">
                            <div class="wa-item">
                                <div class="wa-icon"><i class="fa-solid fa-user"></i></div>
                                <div class="wa-detail">
                                    <div class="wa-label">Nama Lengkap</div>
                                    <div class="wa-value"><?= htmlspecialchars($admin['nama_lengkap']) ?></div>
                                </div>
                            </div>
                            <div class="wa-item">
                                <div class="wa-icon"><i class="fa-solid fa-briefcase"></i></div>
                                <div class="wa-detail">
                                    <div class="wa-label">Jabatan / Peran</div>
                                    <div class="wa-value"><?= htmlspecialchars($admin['deskripsi'] ?: 'Administrator U-Life-Rate') ?></div>
                                </div>
                            </div>
                            <div class="wa-item">
                                <div class="wa-icon"><i class="fa-solid fa-phone"></i></div>
                                <div class="wa-detail">
                                    <div class="wa-label">Nomor Telepon</div>
                                    <div class="wa-value"><?= htmlspecialchars($admin['no_telp'] ?: '-') ?></div>
                                </div>
                            </div>
                            <div class="wa-item">
                                <div class="wa-icon"><i class="fa-solid fa-envelope"></i></div>
                                <div class="wa-detail">
                                    <div class="wa-label">Alamat Email</div>
                                    <div class="wa-value"><?= htmlspecialchars($admin['email'] ?: '-') ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="wa-profile-card" style="padding: 25px;">
                        <h3 style="margin-bottom: 15px;"><i class="fa-solid fa-pen-to-square"></i> Edit Profil</h3>
                        <form method="POST" enctype="multipart/form-data">
                            <div class="form-group">
                                <label>Foto Profil Baru</label>
                                <input type="file" name="foto_baru" class="form-control" accept="image/*">
                            </div>
                            <div class="form-group">
                                <label>Nama Lengkap</label>
                                <input type="text" name="nama_lengkap" value="<?= htmlspecialchars($admin['nama_lengkap']) ?>" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Jabatan / Peran</label>
                                <input type="text" name="deskripsi" value="<?= htmlspecialchars($admin['deskripsi']) ?>" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Nomor Telepon</label>
                                <input type="text" name="no_telp" value="<?= htmlspecialchars($admin['no_telp']) ?>" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Alamat Email</label>
                                <input type="email" name="email" value="<?= htmlspecialchars($admin['email']) ?>" class="form-control" required>
                            </div>
                            <div style="display:flex; gap:10px; margin-top:20px;">
                                <button type="submit" name="update_profil" class="btn btn-primary">Simpan Perubahan</button>
                                <a href="profil.php" class="btn btn-outline">Batal</a>
                            </div>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
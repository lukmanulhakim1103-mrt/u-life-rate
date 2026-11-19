<?php
require 'koneksi.php';
if(!isset($_SESSION['admin'])) header("Location: index.php");

$page = isset($_GET['page']) ? $_GET['page'] : 'home';
$tab  = isset($_GET['tab']) ? $_GET['tab'] : 'semua';

// --- 1. KOSONGKAN SEMUA DATA KOLEKSI ---
if(isset($_POST['hapus_semua_data'])) {
    $pdo->exec("TRUNCATE TABLE novel");
    $pdo->exec("TRUNCATE TABLE film");
    $pdo->exec("TRUNCATE TABLE music");
    echo "<script>alert('Seluruh data Novel, Film, dan Musik berhasil dikosongkan.'); window.location.href='dashboard.php?page=koleksi&tab=semua';</script>";
    exit;
}

// --- 2. CRUD GENRE ---
if(isset($_POST['tambah_genre'])) {
    $stmt = $pdo->prepare("INSERT INTO genre (kategori_id, nama_genre) VALUES (?, ?)");
    $stmt->execute([$_POST['kategori_id'], $_POST['nama_genre']]);
    header("Location: dashboard.php?page=kategori"); exit;
}
if(isset($_GET['hapus_genre'])) {
    $stmt = $pdo->prepare("DELETE FROM genre WHERE id=?");
    $stmt->execute([$_GET['hapus_genre']]);
    header("Location: dashboard.php?page=kategori"); exit;
}

// --- 3. CRUD NOVEL ---
if(isset($_POST['simpan_novel'])) {
    $stmt = $pdo->prepare("INSERT INTO novel (judul, penulis, penerbit, genre_id, jumlah_halaman, tahun_terbit, isbn, rating, sinopsis) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$_POST['judul'], $_POST['penulis'], $_POST['penerbit'], $_POST['genre_id'], $_POST['jumlah_halaman'], $_POST['tahun_terbit'], $_POST['isbn'], $_POST['rating'], $_POST['sinopsis']]);
    header("Location: dashboard.php?page=koleksi&tab=novel"); exit;
}
if(isset($_POST['update_novel'])) {
    $stmt = $pdo->prepare("UPDATE novel SET judul=?, penulis=?, penerbit=?, genre_id=?, jumlah_halaman=?, tahun_terbit=?, isbn=?, rating=?, sinopsis=? WHERE id=?");
    $stmt->execute([$_POST['judul'], $_POST['penulis'], $_POST['penerbit'], $_POST['genre_id'], $_POST['jumlah_halaman'], $_POST['tahun_terbit'], $_POST['isbn'], $_POST['rating'], $_POST['sinopsis'], $_POST['id']]);
    header("Location: dashboard.php?page=koleksi&tab=novel"); exit;
}
if(isset($_GET['hapus_novel'])) {
    $stmt = $pdo->prepare("DELETE FROM novel WHERE id=?");
    $stmt->execute([$_GET['hapus_novel']]);
    header("Location: dashboard.php?page=koleksi&tab=novel"); exit;
}

// --- 4. CRUD FILM ---
if(isset($_POST['simpan_film'])) {
    $stmt = $pdo->prepare("INSERT INTO film (judul, sutradara, studio, genre_id, durasi_menit, tahun_rilis, batas_usia, rating, sinopsis) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$_POST['judul'], $_POST['sutradara'], $_POST['studio'], $_POST['genre_id'], $_POST['durasi_menit'], $_POST['tahun_rilis'], $_POST['batas_usia'], $_POST['rating'], $_POST['sinopsis']]);
    header("Location: dashboard.php?page=koleksi&tab=film"); exit;
}
if(isset($_POST['update_film'])) {
    $stmt = $pdo->prepare("UPDATE film SET judul=?, sutradara=?, studio=?, genre_id=?, durasi_menit=?, tahun_rilis=?, batas_usia=?, rating=?, sinopsis=? WHERE id=?");
    $stmt->execute([$_POST['judul'], $_POST['sutradara'], $_POST['studio'], $_POST['genre_id'], $_POST['durasi_menit'], $_POST['tahun_rilis'], $_POST['batas_usia'], $_POST['rating'], $_POST['sinopsis'], $_POST['id']]);
    header("Location: dashboard.php?page=koleksi&tab=film"); exit;
}
if(isset($_GET['hapus_film'])) {
    $stmt = $pdo->prepare("DELETE FROM film WHERE id=?");
    $stmt->execute([$_GET['hapus_film']]);
    header("Location: dashboard.php?page=koleksi&tab=film"); exit;
}

// --- 5. CRUD MUSIK ---
if(isset($_POST['simpan_music'])) {
    $stmt = $pdo->prepare("INSERT INTO music (judul, penyanyi, album, label_musik, genre_id, durasi, tahun_rilis, rating, sinopsis) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$_POST['judul'], $_POST['penyanyi'], $_POST['album'], $_POST['label_musik'], $_POST['genre_id'], $_POST['durasi'], $_POST['tahun_rilis'], $_POST['rating'], $_POST['sinopsis']]);
    header("Location: dashboard.php?page=koleksi&tab=music"); exit;
}
if(isset($_POST['update_music'])) {
    $stmt = $pdo->prepare("UPDATE music SET judul=?, penyanyi=?, album=?, label_musik=?, genre_id=?, durasi=?, tahun_rilis=?, rating=?, sinopsis=? WHERE id=?");
    $stmt->execute([$_POST['judul'], $_POST['penyanyi'], $_POST['album'], $_POST['label_musik'], $_POST['genre_id'], $_POST['durasi'], $_POST['tahun_rilis'], $_POST['rating'], $_POST['sinopsis'], $_POST['id']]);
    header("Location: dashboard.php?page=koleksi&tab=music"); exit;
}
if(isset($_GET['hapus_music'])) {
    $stmt = $pdo->prepare("DELETE FROM music WHERE id=?");
    $stmt->execute([$_GET['hapus_music']]);
    header("Location: dashboard.php?page=koleksi&tab=music"); exit;
}

if($page == 'logout') { session_destroy(); header("Location: index.php"); exit; }

$genre_novel = $pdo->query("SELECT * FROM genre WHERE kategori_id = 1 ORDER BY id ASC")->fetchAll();
$genre_film  = $pdo->query("SELECT * FROM genre WHERE kategori_id = 2 ORDER BY id ASC")->fetchAll();
$genre_music = $pdo->query("SELECT * FROM genre WHERE kategori_id = 3 ORDER BY id ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>U-Life-Rate — Manajemen Koleksi Media</title>
    <link rel="stylesheet" href="style.css?v=<?= time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
</head>
<body>
    <div class="dashboard-layout">
        <!-- SIDEBAR -->
        <div class="sidebar">
            <div class="sidebar-header"><i class="fa-solid fa-star"></i> U-Life-Rate</div>

            <div class="nav-section">Menu Utama</div>
            <ul class="nav-links">
                <li><a href="dashboard.php?page=home" class="nav-item <?= $page=='home'?'active':'' ?>"><i class="fa-solid fa-house"></i> Dashboard</a></li>
                <li><a href="dashboard.php?page=koleksi&tab=semua" class="nav-item <?= ($page=='koleksi' && $tab=='semua')?'active':'' ?>"><i class="fa-solid fa-table-columns"></i> Ringkasan Koleksi</a></li>
            </ul>

            <div class="nav-section">Kelola Koleksi</div>
            <ul class="nav-links">
                <li><a href="dashboard.php?page=koleksi&tab=novel" class="nav-item <?= (($page=='koleksi' && $tab=='novel') || $page=='form_novel')?'active':'' ?>"><i class="fa-solid fa-book"></i> Data Novel</a></li>
                <li><a href="dashboard.php?page=koleksi&tab=film" class="nav-item <?= (($page=='koleksi' && $tab=='film') || $page=='form_film')?'active':'' ?>"><i class="fa-solid fa-film"></i> Data Film</a></li>
                <li><a href="dashboard.php?page=koleksi&tab=music" class="nav-item <?= (($page=='koleksi' && $tab=='music') || $page=='form_music')?'active':'' ?>"><i class="fa-solid fa-music"></i> Data Musik</a></li>
            </ul>

            <div class="nav-section">Data Master</div>
            <ul class="nav-links">
                <li><a href="dashboard.php?page=kategori" class="nav-item <?= $page=='kategori'?'active':'' ?>"><i class="fa-solid fa-folder-tree"></i> Kategori & Genre</a></li>
            </ul>

            <div class="nav-section">Pengaturan Akun</div>
            <ul class="nav-links">
                <li><a href="profil.php" class="nav-item"><i class="fa-solid fa-user"></i> Profil Admin</a></li>
            </ul>
        </div>

        <!-- KONTEN UTAMA -->
        <div class="main-wrapper">
            <div class="topbar">
                <form action="dashboard.php" method="GET" class="search-form">
                    <input type="hidden" name="page" value="search">
                    <input type="text" name="q" class="form-control" placeholder="Cari ID Genre, Nama Genre, atau Judul..." required>
                    <button type="submit" class="btn btn-primary" title="Cari"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>

                <div class="topbar-actions">
                    <form method="POST" style="display:inline;" onsubmit="return confirm('Apakah Anda yakin ingin mengosongkan seluruh data Novel, Film, dan Musik?');">
                        <button type="submit" name="hapus_semua_data" class="btn btn-danger">
                            <i class="fa-solid fa-trash-can"></i> Kosongkan Data
                        </button>
                    </form>
                    <a href="?page=logout" class="btn btn-outline" onclick="return confirm('Apakah Anda yakin ingin keluar dari sistem?')">
                        <i class="fa-solid fa-right-from-bracket"></i> Keluar
                    </a>
                </div>
            </div>

            <div class="content-area">

                <?php if($page == 'home'): 
                    $t_novel = $pdo->query("SELECT COUNT(*) FROM novel")->fetchColumn();
                    $t_film  = $pdo->query("SELECT COUNT(*) FROM film")->fetchColumn();
                    $t_music = $pdo->query("SELECT COUNT(*) FROM music")->fetchColumn();
                    $t_genre = $pdo->query("SELECT COUNT(*) FROM genre")->fetchColumn();
                ?>
                    <div class="page-title">
                        <h2>Dashboard</h2>
                        <p>Ringkasan statistik koleksi Novel, Film, dan Musik di U-Life-Rate.</p>
                    </div>
                    <div class="grid-stats">
                        <div class="card"><span class="text-muted"><i class="fa-solid fa-book"></i> Total Novel</span><div class="stat-number"><?= $t_novel ?></div></div>
                        <div class="card"><span class="text-muted"><i class="fa-solid fa-film"></i> Total Film</span><div class="stat-number"><?= $t_film ?></div></div>
                        <div class="card"><span class="text-muted"><i class="fa-solid fa-music"></i> Total Musik</span><div class="stat-number"><?= $t_music ?></div></div>
                        <div class="card"><span class="text-muted"><i class="fa-solid fa-tags"></i> Total Genre</span><div class="stat-number"><?= $t_genre ?></div></div>
                    </div>

                <?php elseif($page == 'kategori'): 
                    $kategoris = $pdo->query("SELECT * FROM kategori ORDER BY id ASC")->fetchAll();
                ?>
                    <div class="page-title">
                        <h2>Kategori & Genre</h2>
                        <p>Pengelolaan daftar genre yang dikelompokkan berdasarkan masing-masing kategori media.</p>
                    </div>

                    <div class="grid-side-by-side">
                        <?php foreach($kategoris as $k): 
                            $stmtG = $pdo->prepare("SELECT * FROM genre WHERE kategori_id = ? ORDER BY id ASC");
                            $stmtG->execute([$k['id']]);
                            $list_genre = $stmtG->fetchAll();
                            $icon = ($k['id']==1) ? 'fa-book' : (($k['id']==2) ? 'fa-film' : 'fa-music');
                            $nama_kat = ($k['nama_kategori'] == 'Music') ? 'Musik' : $k['nama_kategori'];
                        ?>
                            <div class="card">
                                <h3 style="display:flex; align-items:center; gap:8px; margin-bottom:4px;">
                                    <i class="fa-solid <?= $icon ?>" style="color:var(--accent);"></i>
                                    Kategori <?= htmlspecialchars($nama_kat) ?>
                                </h3>
                                <p class="text-muted">Daftar genre untuk koleksi <?= htmlspecialchars($nama_kat) ?>:</p>

                                <ul class="genre-list">
                                    <?php foreach($list_genre as $g): ?>
                                        <li class="genre-item">
                                            <div>
                                                <span class="badge-id">ID: <?= $g['id'] ?></span>
                                                <strong style="margin-left:6px;"><?= htmlspecialchars($g['nama_genre']) ?></strong>
                                            </div>
                                            <div style="display:flex; gap:5px;">
                                                <a href="?page=search&q=<?= urlencode($g['nama_genre']) ?>" class="btn btn-outline btn-sm" title="Lihat Koleksi"><i class="fa-solid fa-eye"></i></a>
                                                <a href="?hapus_genre=<?= $g['id'] ?>" onclick="return confirm('Hapus genre ini?')" class="btn btn-danger btn-sm" title="Hapus Genre"><i class="fa-solid fa-trash"></i></a>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>

                                <form method="POST" style="display:flex; gap:6px; margin-top:10px;">
                                    <input type="hidden" name="kategori_id" value="<?= $k['id'] ?>">
                                    <input type="text" name="nama_genre" class="form-control" placeholder="Nama genre <?= htmlspecialchars($nama_kat) ?> baru..." required>
                                    <button type="submit" name="tambah_genre" class="btn btn-primary btn-sm" title="Tambah Genre"><i class="fa-solid fa-plus"></i></button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>

                <?php elseif($page == 'koleksi'): ?>
                    <div class="page-title">
                        <h2>Data Koleksi</h2>
                        <p>Pilih tampilan ringkas untuk melihat seluruh koleksi berdampingan, atau pilih kategori spesifik untuk detail dan ekspor data.</p>
                    </div>

                    <div class="tabs">
                        <a href="?page=koleksi&tab=semua" class="tab-btn <?= $tab=='semua'?'active':'' ?>"><i class="fa-solid fa-table-columns"></i> Ringkasan Semua</a>
                        <a href="?page=koleksi&tab=novel" class="tab-btn <?= $tab=='novel'?'active':'' ?>"><i class="fa-solid fa-book"></i> Novel</a>
                        <a href="?page=koleksi&tab=film" class="tab-btn <?= $tab=='film'?'active':'' ?>"><i class="fa-solid fa-film"></i> Film</a>
                        <a href="?page=koleksi&tab=music" class="tab-btn <?= $tab=='music'?'active':'' ?>"><i class="fa-solid fa-music"></i> Musik</a>
                    </div>

                    <?php if($tab == 'semua'): 
                        $novels = $pdo->query("SELECT n.*, g.nama_genre FROM novel n LEFT JOIN genre g ON n.genre_id = g.id ORDER BY n.id DESC")->fetchAll();
                        $films  = $pdo->query("SELECT f.*, g.nama_genre FROM film f LEFT JOIN genre g ON f.genre_id = g.id ORDER BY f.id DESC")->fetchAll();
                        $musics = $pdo->query("SELECT m.*, g.nama_genre FROM music m LEFT JOIN genre g ON m.genre_id = g.id ORDER BY m.id DESC")->fetchAll();
                    ?>
                        <div class="grid-side-by-side">
                            <div class="card">
                                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                                    <h3><i class="fa-solid fa-book" style="color:var(--accent);"></i> Novel (<?= count($novels) ?>)</h3>
                                    <a href="?page=form_novel" class="btn btn-primary btn-sm" title="Tambah Novel"><i class="fa-solid fa-plus"></i></a>
                                </div>
                                <table>
                                    <thead><tr><th>Judul</th><th>Genre</th><th>Aksi</th></tr></thead>
                                    <tbody>
                                    <?php foreach($novels as $n): ?>
                                    <tr>
                                        <td><strong><?= htmlspecialchars($n['judul']) ?></strong><br><span class="text-muted"><?= htmlspecialchars($n['penulis']) ?> • <?= $n['jumlah_halaman'] ?> hlm</span></td>
                                        <td><span class="badge-id"><?= htmlspecialchars($n['nama_genre'] ?: '-') ?></span></td>
                                        <td><a href="?page=form_novel&id=<?= $n['id'] ?>" class="btn btn-outline btn-sm" title="Ubah"><i class="fa-solid fa-pen"></i></a></td>
                                    </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <div class="card">
                                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                                    <h3><i class="fa-solid fa-film" style="color:var(--accent);"></i> Film (<?= count($films) ?>)</h3>
                                    <a href="?page=form_film" class="btn btn-primary btn-sm" title="Tambah Film"><i class="fa-solid fa-plus"></i></a>
                                </div>
                                <table>
                                    <thead><tr><th>Judul</th><th>Genre</th><th>Aksi</th></tr></thead>
                                    <tbody>
                                    <?php foreach($films as $f): ?>
                                    <tr>
                                        <td><strong><?= htmlspecialchars($f['judul']) ?></strong><br><span class="text-muted"><?= htmlspecialchars($f['sutradara']) ?> • <?= $f['durasi_menit'] ?> menit</span></td>
                                        <td><span class="badge-id"><?= htmlspecialchars($f['nama_genre'] ?: '-') ?></span></td>
                                        <td><a href="?page=form_film&id=<?= $f['id'] ?>" class="btn btn-outline btn-sm" title="Ubah"><i class="fa-solid fa-pen"></i></a></td>
                                    </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <div class="card">
                                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                                    <h3><i class="fa-solid fa-music" style="color:var(--accent);"></i> Musik (<?= count($musics) ?>)</h3>
                                    <a href="?page=form_music" class="btn btn-primary btn-sm" title="Tambah Musik"><i class="fa-solid fa-plus"></i></a>
                                </div>
                                <table>
                                    <thead><tr><th>Judul</th><th>Genre</th><th>Aksi</th></tr></thead>
                                    <tbody>
                                    <?php foreach($musics as $m): ?>
                                    <tr>
                                        <td><strong><?= htmlspecialchars($m['judul']) ?></strong><br><span class="text-muted"><?= htmlspecialchars($m['penyanyi']) ?> • <?= htmlspecialchars($m['durasi']) ?></span></td>
                                        <td><span class="badge-id"><?= htmlspecialchars($m['nama_genre'] ?: '-') ?></span></td>
                                        <td><a href="?page=form_music&id=<?= $m['id'] ?>" class="btn btn-outline btn-sm" title="Ubah"><i class="fa-solid fa-pen"></i></a></td>
                                    </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    <?php elseif($tab == 'novel'): 
                        $novels = $pdo->query("SELECT n.*, g.nama_genre FROM novel n LEFT JOIN genre g ON n.genre_id = g.id ORDER BY n.id DESC")->fetchAll();
                    ?>
                        <div class="card">
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
                                <h3>Daftar Novel</h3>
                                <a href="?page=form_novel" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Tambah Novel</a>
                            </div>
                            <div style="overflow-x:auto;">
                                <table class="table-export">
                                    <thead>
                                        <tr><th>ID</th><th>Judul & Sinopsis</th><th>Genre</th><th>Penulis & Penerbit</th><th>Halaman</th><th>Tahun & ISBN</th><th>Rating</th><th>Aksi</th></tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach($novels as $n): ?>
                                    <tr>
                                        <td><?= $n['id'] ?></td>
                                        <td><strong><?= htmlspecialchars($n['judul']) ?></strong><br><span class="text-muted"><?= htmlspecialchars($n['sinopsis']) ?></span></td>
                                        <td><span class="badge-id">[ID: <?= $n['genre_id'] ?>] <?= htmlspecialchars($n['nama_genre'] ?: '-') ?></span></td>
                                        <td><?= htmlspecialchars($n['penulis']) ?><br><span class="text-muted"><?= htmlspecialchars($n['penerbit']) ?></span></td>
                                        <td><?= $n['jumlah_halaman'] ?> hlm</td>
                                        <td><?= $n['tahun_terbit'] ?><br><span class="text-muted"><?= htmlspecialchars($n['isbn']) ?></span></td>
                                        <td>⭐ <?= htmlspecialchars($n['rating']) ?></td>
                                        <td>
                                            <a href="?page=form_novel&id=<?= $n['id'] ?>" class="btn btn-outline btn-sm" title="Ubah"><i class="fa-solid fa-pen"></i></a>
                                            <a href="?hapus_novel=<?= $n['id'] ?>" onclick="return confirm('Hapus data novel ini?')" class="btn btn-danger btn-sm" title="Hapus"><i class="fa-solid fa-trash"></i></a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    <?php elseif($tab == 'film'): 
                        $films = $pdo->query("SELECT f.*, g.nama_genre FROM film f LEFT JOIN genre g ON f.genre_id = g.id ORDER BY f.id DESC")->fetchAll();
                    ?>
                        <div class="card">
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
                                <h3>Daftar Film</h3>
                                <a href="?page=form_film" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Tambah Film</a>
                            </div>
                            <div style="overflow-x:auto;">
                                <table class="table-export">
                                    <thead>
                                        <tr><th>ID</th><th>Judul & Sinopsis</th><th>Genre</th><th>Sutradara & Studio</th><th>Durasi</th><th>Tahun & Usia</th><th>Rating</th><th>Aksi</th></tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach($films as $f): ?>
                                    <tr>
                                        <td><?= $f['id'] ?></td>
                                        <td><strong><?= htmlspecialchars($f['judul']) ?></strong><br><span class="text-muted"><?= htmlspecialchars($f['sinopsis']) ?></span></td>
                                        <td><span class="badge-id">[ID: <?= $f['genre_id'] ?>] <?= htmlspecialchars($f['nama_genre'] ?: '-') ?></span></td>
                                        <td><?= htmlspecialchars($f['sutradara']) ?><br><span class="text-muted"><?= htmlspecialchars($f['studio']) ?></span></td>
                                        <td><?= $f['durasi_menit'] ?> menit</td>
                                        <td><?= $f['tahun_rilis'] ?> (<?= htmlspecialchars($f['batas_usia']) ?>)</td>
                                        <td>⭐ <?= htmlspecialchars($f['rating']) ?></td>
                                        <td>
                                            <a href="?page=form_film&id=<?= $f['id'] ?>" class="btn btn-outline btn-sm" title="Ubah"><i class="fa-solid fa-pen"></i></a>
                                            <a href="?hapus_film=<?= $f['id'] ?>" onclick="return confirm('Hapus data film ini?')" class="btn btn-danger btn-sm" title="Hapus"><i class="fa-solid fa-trash"></i></a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    <?php else: 
                        $musics = $pdo->query("SELECT m.*, g.nama_genre FROM music m LEFT JOIN genre g ON m.genre_id = g.id ORDER BY m.id DESC")->fetchAll();
                    ?>
                        <div class="card">
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
                                <h3>Daftar Musik</h3>
                                <a href="?page=form_music" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Tambah Musik</a>
                            </div>
                            <div style="overflow-x:auto;">
                                <table class="table-export">
                                    <thead>
                                        <tr><th>ID</th><th>Judul & Deskripsi</th><th>Genre</th><th>Penyanyi & Album</th><th>Label Musik</th><th>Durasi & Tahun</th><th>Rating</th><th>Aksi</th></tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach($musics as $m): ?>
                                    <tr>
                                        <td><?= $m['id'] ?></td>
                                        <td><strong><?= htmlspecialchars($m['judul']) ?></strong><br><span class="text-muted"><?= htmlspecialchars($m['sinopsis']) ?></span></td>
                                        <td><span class="badge-id">[ID: <?= $m['genre_id'] ?>] <?= htmlspecialchars($m['nama_genre'] ?: '-') ?></span></td>
                                        <td><?= htmlspecialchars($m['penyanyi']) ?><br><span class="text-muted">Album: <?= htmlspecialchars($m['album']) ?></span></td>
                                        <td><?= htmlspecialchars($m['label_musik']) ?></td>
                                        <td><?= htmlspecialchars($m['durasi']) ?> (<?= $m['tahun_rilis'] ?>)</td>
                                        <td>⭐ <?= htmlspecialchars($m['rating']) ?></td>
                                        <td>
                                            <a href="?page=form_music&id=<?= $m['id'] ?>" class="btn btn-outline btn-sm" title="Ubah"><i class="fa-solid fa-pen"></i></a>
                                            <a href="?hapus_music=<?= $m['id'] ?>" onclick="return confirm('Hapus data musik ini?')" class="btn btn-danger btn-sm" title="Hapus"><i class="fa-solid fa-trash"></i></a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>

                <?php elseif($page == 'form_novel'): 
                    $isEdit = isset($_GET['id']);
                    $d = ['judul'=>'', 'penulis'=>'', 'penerbit'=>'', 'genre_id'=>'', 'jumlah_halaman'=>'', 'tahun_terbit'=>date('Y'), 'isbn'=>'-', 'rating'=>'9.0/10', 'sinopsis'=>''];
                    if($isEdit) { $s = $pdo->prepare("SELECT * FROM novel WHERE id=?"); $s->execute([$_GET['id']]); $d = $s->fetch(); }
                ?>
                    <div class="page-title"><h2><?= $isEdit ? 'Ubah Data Novel' : 'Tambah Data Novel' ?></h2></div>
                    <form method="POST" class="card">
                        <?php if($isEdit): ?><input type="hidden" name="id" value="<?= $d['id'] ?>"><?php endif; ?>
                        <div class="grid-form">
                            <div class="form-group"><label>Judul Novel</label><input type="text" name="judul" class="form-control" value="<?= htmlspecialchars($d['judul']) ?>" required></div>
                            <div class="form-group"><label>Penulis</label><input type="text" name="penulis" class="form-control" value="<?= htmlspecialchars($d['penulis']) ?>" required></div>
                            <div class="form-group"><label>Penerbit</label><input type="text" name="penerbit" class="form-control" value="<?= htmlspecialchars($d['penerbit']) ?>" required></div>
                            <div class="form-group">
                                <label>Genre Novel</label>
                                <select name="genre_id" class="form-control" required>
                                    <?php foreach($genre_novel as $g): ?>
                                        <option value="<?= $g['id'] ?>" <?= $d['genre_id']==$g['id']?'selected':'' ?>>[ID: <?= $g['id'] ?>] <?= htmlspecialchars($g['nama_genre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group"><label>Jumlah Halaman</label><input type="number" name="jumlah_halaman" class="form-control" value="<?= $d['jumlah_halaman'] ?>" required></div>
                            <div class="form-group"><label>Tahun Terbit</label><input type="number" name="tahun_terbit" class="form-control" value="<?= $d['tahun_terbit'] ?>" required></div>
                            <div class="form-group"><label>ISBN</label><input type="text" name="isbn" class="form-control" value="<?= htmlspecialchars($d['isbn']) ?>" required></div>
                            <div class="form-group"><label>Rating</label><input type="text" name="rating" class="form-control" value="<?= htmlspecialchars($d['rating']) ?>" required></div>
                        </div>
                        <div class="form-group"><label>Sinopsis</label><textarea name="sinopsis" class="form-control" rows="3" required><?= htmlspecialchars($d['sinopsis']) ?></textarea></div>
                        <button type="submit" name="<?= $isEdit ? 'update_novel' : 'simpan_novel' ?>" class="btn btn-primary">Simpan</button>
                        <a href="?page=koleksi&tab=novel" class="btn btn-outline">Batal</a>
                    </form>

                <?php elseif($page == 'form_film'): 
                    $isEdit = isset($_GET['id']);
                    $d = ['judul'=>'', 'sutradara'=>'', 'studio'=>'', 'genre_id'=>'', 'durasi_menit'=>'', 'tahun_rilis'=>date('Y'), 'batas_usia'=>'13+', 'rating'=>'9.0/10', 'sinopsis'=>''];
                    if($isEdit) { $s = $pdo->prepare("SELECT * FROM film WHERE id=?"); $s->execute([$_GET['id']]); $d = $s->fetch(); }
                ?>
                    <div class="page-title"><h2><?= $isEdit ? 'Ubah Data Film' : 'Tambah Data Film' ?></h2></div>
                    <form method="POST" class="card">
                        <?php if($isEdit): ?><input type="hidden" name="id" value="<?= $d['id'] ?>"><?php endif; ?>
                        <div class="grid-form">
                            <div class="form-group"><label>Judul Film</label><input type="text" name="judul" class="form-control" value="<?= htmlspecialchars($d['judul']) ?>" required></div>
                            <div class="form-group"><label>Sutradara</label><input type="text" name="sutradara" class="form-control" value="<?= htmlspecialchars($d['sutradara']) ?>" required></div>
                            <div class="form-group"><label>Studio Produksi</label><input type="text" name="studio" class="form-control" value="<?= htmlspecialchars($d['studio']) ?>" required></div>
                            <div class="form-group">
                                <label>Genre Film</label>
                                <select name="genre_id" class="form-control" required>
                                    <?php foreach($genre_film as $g): ?>
                                        <option value="<?= $g['id'] ?>" <?= $d['genre_id']==$g['id']?'selected':'' ?>>[ID: <?= $g['id'] ?>] <?= htmlspecialchars($g['nama_genre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group"><label>Durasi (Menit)</label><input type="number" name="durasi_menit" class="form-control" value="<?= $d['durasi_menit'] ?>" required></div>
                            <div class="form-group"><label>Tahun Rilis</label><input type="number" name="tahun_rilis" class="form-control" value="<?= $d['tahun_rilis'] ?>" required></div>
                            <div class="form-group"><label>Batas Usia</label><input type="text" name="batas_usia" class="form-control" value="<?= htmlspecialchars($d['batas_usia']) ?>" required></div>
                            <div class="form-group"><label>Rating</label><input type="text" name="rating" class="form-control" value="<?= htmlspecialchars($d['rating']) ?>" required></div>
                        </div>
                        <div class="form-group"><label>Sinopsis</label><textarea name="sinopsis" class="form-control" rows="3" required><?= htmlspecialchars($d['sinopsis']) ?></textarea></div>
                        <button type="submit" name="<?= $isEdit ? 'update_film' : 'simpan_film' ?>" class="btn btn-primary">Simpan</button>
                        <a href="?page=koleksi&tab=film" class="btn btn-outline">Batal</a>
                    </form>

                <?php elseif($page == 'form_music'): 
                    $isEdit = isset($_GET['id']);
                    $d = ['judul'=>'', 'penyanyi'=>'', 'album'=>'', 'label_musik'=>'', 'genre_id'=>'', 'durasi'=>'03:45', 'tahun_rilis'=>date('Y'), 'rating'=>'9.0/10', 'sinopsis'=>''];
                    if($isEdit) { $s = $pdo->prepare("SELECT * FROM music WHERE id=?"); $s->execute([$_GET['id']]); $d = $s->fetch(); }
                ?>
                    <div class="page-title"><h2><?= $isEdit ? 'Ubah Data Musik' : 'Tambah Data Musik' ?></h2></div>
                    <form method="POST" class="card">
                        <?php if($isEdit): ?><input type="hidden" name="id" value="<?= $d['id'] ?>"><?php endif; ?>
                        <div class="grid-form">
                            <div class="form-group"><label>Judul Lagu</label><input type="text" name="judul" class="form-control" value="<?= htmlspecialchars($d['judul']) ?>" required></div>
                            <div class="form-group"><label>Penyanyi / Grup</label><input type="text" name="penyanyi" class="form-control" value="<?= htmlspecialchars($d['penyanyi']) ?>" required></div>
                            <div class="form-group"><label>Nama Album</label><input type="text" name="album" class="form-control" value="<?= htmlspecialchars($d['album']) ?>" required></div>
                            <div class="form-group"><label>Label Musik</label><input type="text" name="label_musik" class="form-control" value="<?= htmlspecialchars($d['label_musik']) ?>" required></div>
                            <div class="form-group">
                                <label>Genre Musik</label>
                                <select name="genre_id" class="form-control" required>
                                    <?php foreach($genre_music as $g): ?>
                                        <option value="<?= $g['id'] ?>" <?= $d['genre_id']==$g['id']?'selected':'' ?>>[ID: <?= $g['id'] ?>] <?= htmlspecialchars($g['nama_genre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group"><label>Durasi (Format MM:SS)</label><input type="text" name="durasi" class="form-control" value="<?= htmlspecialchars($d['durasi']) ?>" required></div>
                            <div class="form-group"><label>Tahun Rilis</label><input type="number" name="tahun_rilis" class="form-control" value="<?= $d['tahun_rilis'] ?>" required></div>
                            <div class="form-group"><label>Rating</label><input type="text" name="rating" class="form-control" value="<?= htmlspecialchars($d['rating']) ?>" required></div>
                        </div>
                        <div class="form-group"><label>Deskripsi</label><textarea name="sinopsis" class="form-control" rows="3" required><?= htmlspecialchars($d['sinopsis']) ?></textarea></div>
                        <button type="submit" name="<?= $isEdit ? 'update_music' : 'simpan_music' ?>" class="btn btn-primary">Simpan</button>
                        <a href="?page=koleksi&tab=music" class="btn btn-outline">Batal</a>
                    </form>

                <?php elseif($page == 'search'): 
                    $q = trim($_GET['q'] ?? '');
                    $s_novel = $pdo->prepare("SELECT n.*, g.nama_genre FROM novel n LEFT JOIN genre g ON n.genre_id = g.id WHERE n.judul LIKE ? OR n.penulis LIKE ? OR g.nama_genre LIKE ? OR n.genre_id = ?");
                    $s_novel->execute(["%$q%", "%$q%", "%$q%", $q]);
                    $h_novel = $s_novel->fetchAll();

                    $s_film = $pdo->prepare("SELECT f.*, g.nama_genre FROM film f LEFT JOIN genre g ON f.genre_id = g.id WHERE f.judul LIKE ? OR f.sutradara LIKE ? OR g.nama_genre LIKE ? OR f.genre_id = ?");
                    $s_film->execute(["%$q%", "%$q%", "%$q%", $q]);
                    $h_film = $s_film->fetchAll();

                    $s_music = $pdo->prepare("SELECT m.*, g.nama_genre FROM music m LEFT JOIN genre g ON m.genre_id = g.id WHERE m.judul LIKE ? OR m.penyanyi LIKE ? OR g.nama_genre LIKE ? OR m.genre_id = ?");
                    $s_music->execute(["%$q%", "%$q%", "%$q%", $q]);
                    $h_music = $s_music->fetchAll();
                ?>
                    <div class="page-title">
                        <h2>Hasil Pencarian</h2>
                        <p>Menampilkan kecocokan untuk kata kunci: <strong>"<?= htmlspecialchars($q) ?>"</strong></p>
                    </div>

                    <div class="grid-side-by-side">
                        <div class="card">
                            <h3><i class="fa-solid fa-book" style="color:var(--accent);"></i> Novel (<?= count($h_novel) ?>)</h3>
                            <table>
                                <tr><th>Judul</th><th>Genre</th><th>Aksi</th></tr>
                                <?php foreach($h_novel as $n): ?>
                                <tr><td><strong><?= htmlspecialchars($n['judul']) ?></strong><br><span class="text-muted"><?= htmlspecialchars($n['penulis']) ?></span></td><td><span class="badge-id"><?= htmlspecialchars($n['nama_genre'] ?: '-') ?></span></td><td><a href="?page=form_novel&id=<?= $n['id'] ?>" class="btn btn-outline btn-sm">Ubah</a></td></tr>
                                <?php endforeach; ?>
                            </table>
                        </div>

                        <div class="card">
                            <h3><i class="fa-solid fa-film" style="color:var(--accent);"></i> Film (<?= count($h_film) ?>)</h3>
                            <table>
                                <tr><th>Judul</th><th>Genre</th><th>Aksi</th></tr>
                                <?php foreach($h_film as $f): ?>
                                <tr><td><strong><?= htmlspecialchars($f['judul']) ?></strong><br><span class="text-muted"><?= htmlspecialchars($f['sutradara']) ?></span></td><td><span class="badge-id"><?= htmlspecialchars($f['nama_genre'] ?: '-') ?></span></td><td><a href="?page=form_film&id=<?= $f['id'] ?>" class="btn btn-outline btn-sm">Ubah</a></td></tr>
                                <?php endforeach; ?>
                            </table>
                        </div>

                        <div class="card">
                            <h3><i class="fa-solid fa-music" style="color:var(--accent);"></i> Musik (<?= count($h_music) ?>)</h3>
                            <table>
                                <tr><th>Judul</th><th>Genre</th><th>Aksi</th></tr>
                                <?php foreach($h_music as $m): ?>
                                <tr><td><strong><?= htmlspecialchars($m['judul']) ?></strong><br><span class="text-muted"><?= htmlspecialchars($m['penyanyi']) ?></span></td><td><span class="badge-id"><?= htmlspecialchars($m['nama_genre'] ?: '-') ?></span></td><td><a href="?page=form_music&id=<?= $m['id'] ?>" class="btn btn-outline btn-sm">Ubah</a></td></tr>
                                <?php endforeach; ?>
                            </table>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.table-export').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    { extend: 'excelHtml5', text: '<i class="fa-solid fa-file-excel" style="color:#16a34a;"></i> Ekspor Excel' },
                    { extend: 'csvHtml5',   text: '<i class="fa-solid fa-file-csv" style="color:#0284c7;"></i> Ekspor CSV' },
                    { extend: 'pdfHtml5',   text: '<i class="fa-solid fa-file-pdf" style="color:#dc2626;"></i> Ekspor PDF' },
                    { extend: 'print',      text: '<i class="fa-solid fa-print" style="color:#d97706;"></i> Cetak' }
                ],
                pageLength: 10,
                language: {
                    search: "Saring Data:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_–_END_ dari _TOTAL_ data",
                    zeroRecords: "Data tidak ditemukan"
                }
            });
        });
    </script>
</body>
</html>
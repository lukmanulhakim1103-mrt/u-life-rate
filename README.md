# U-Life-Rate

Aplikasi web manajemen katalog dan rating untuk koleksi **Novel**, **Film**, dan **Musik** berbasis PHP (PDO) dan MySQL dengan struktur database relasional bertingkat (`kategori` → `genre` → `novel / film / music`).

## Fitur Utama
- Autentikasi Admin berbasis Session PHP.
- CRUD lengkap untuk Data Novel, Film, Musik, serta Kategori & Genre.
- Tampilan ringkasan koleksi berdampingan dan pencarian global (berdasarkan ID Genre, Nama Genre, Judul, atau Kreator).
- Ekspor data tabel ke **Excel**, **CSV**, **PDF**, dan **Cetak** (DataTables).
- Manajemen profil admin terpisah (`profil.php`) dengan fitur unggah foto profil.

## Teknologi
- PHP 8+ (PDO)
- MySQL / MariaDB (XAMPP)
- HTML5 & Pure CSS
- DataTables & FontAwesome CDN

## Struktur File dalam Folder 
u-life-rate/
├── uploads/           # Direktori penyimpanan foto profil admin
├── koneksi.php        # Konfigurasi koneksi PDO ke database db_uliferate
├── index.php          # Halaman autentikasi (Login)
├── dashboard.php      # Halaman utama, CRUD koleksi, kategori/genre, pencarian, & ekspor
├── profil.php         # Halaman tampilan dan edit profil administrator
├── style.css          # Stylesheet antarmuka
├── db_uliferate.sql   # Skema tabel, relasi Foreign Key, dan data awal
├── .gitignore         # Pengecualian file unggahan lokal
└── README.md          # Dokumentasi proyek

## Cara Menjalankan Secara Lokal
1. Clone repositori ini ke folder `htdocs` XAMPP Anda.
2. Jalankan Apache dan MySQL di XAMPP.
3. Buka `phpMyAdmin` lalu impor file `db_uliferate.sql`.
4. Sesuaikan kredensial database pada `koneksi.php` jika diperlukan.
5. Akses melalui browser di `http://localhost/u-life-rate/`.

**Akun Default:**
- **Username:** `admin`
- **Password:** `admin123`
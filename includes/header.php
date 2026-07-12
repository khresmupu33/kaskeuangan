<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KasKeuangan Khresmupu</title>
    <link rel="icon" type="image/png" href="<?php echo $base_url; ?>includes/KasKeuanganKhresmupu.png">
<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f4f7f6;
    color: #333;
    line-height: 1.6;
}

/* Layout Container */
.container {
    width: 90%;
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

/* Navbar */
header {
    background: #2c3e50;
    color: #fff;
    padding: 1rem 0;
    position: relative;
    z-index: 1000;
}

header nav {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

/* Logo / Judul Brand di Navbar (Dibuat Flex agar logo dan teks sejajar) */
.nav-brand {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 1.2rem;
    font-weight: bold;
    color: #fff;
    text-decoration: none;
}

/* Styling Gambar Logo Bulet di Navbar */
.nav-brand img {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid rgba(255, 255, 255, 0.8);
}

header nav ul {
    list-style: none;
    display: flex;
    align-items: center;
    gap: 8px;
}

header nav ul li a, 
header nav ul li .drop-btn {
    color: #fff;
    text-decoration: none;
    font-weight: bold;
    padding: 8px 12px;
    border-radius: 4px;
    transition: background 0.2s;
    display: block;
    background: transparent;
    border: none;
    cursor: pointer;
    font-family: inherit;
    font-size: 14px;
}

header nav ul li a:hover,
header nav ul li .drop-btn:hover {
    background: rgba(255, 255, 255, 0.1);
}

/* Dropdown Container */
.dropdown {
    position: relative;
}

.dropdown-content {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    background: #34495e;
    min-width: 200px;
    box-shadow: 0px 8px 16px rgba(0,0,0,0.2);
    border-radius: 4px;
    list-style: none;
    flex-direction: column;
    padding: 5px 0;
    z-index: 100;
}

.dropdown-content li {
    width: 100%;
}

.dropdown-content li a {
    padding: 10px 15px;
    font-size: 14px;
    border-radius: 0;
    color: #fff;
    text-align: left;
}

.dropdown-content li a:hover {
    background: rgba(255, 255, 255, 0.1);
}

/* Tampilkan dropdown saat aktif via JS */
.dropdown.active .dropdown-content {
    display: flex;
}

/* Tombol Hamburger 3 Bar (Default disembunyikan di Desktop) */
.hamburger {
    display: none;
    flex-direction: column;
    justify-content: space-between;
    width: 30px;
    height: 22px;
    background: transparent;
    border: none;
    cursor: pointer;
    padding: 0;
    z-index: 10;
}

.hamburger span {
    display: block;
    width: 100%;
    height: 3px;
    background: #fff;
    border-radius: 3px;
    transition: all 0.3s ease-in-out;
}

/* Animasi Hamburger ke Huruf 'X' saat Aktif */
.hamburger.active span:nth-child(1) {
    transform: translateY(9.5px) rotate(45deg);
}

.hamburger.active span:nth-child(2) {
    opacity: 0;
}

.hamburger.active span:nth-child(3) {
    transform: translateY(-9.5px) rotate(-45deg);
}

.table-wrap {
    overflow-x: auto;
    max-width: 100%;
}

/* Tabel Styling */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
    background: #fff;
}

table th,
table td {
    border: 1px solid #ddd;
    padding: 10px;
    text-align: left;
    vertical-align: top;
    white-space: nowrap;
}

table th {
    background-color:#2c3e50;
    color: white;
}

/* Form Styling */
.form-group {
    margin-bottom: 15px;
}

label {
    display: block;
    margin-bottom: 5px;
}

input, select, textarea {
    width: 100%;
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

button {
    background: #27ae60;
    color: white;
    padding: 10px 15px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

button:hover {
    background: #219150;
}

.dashboard-cards {
    display: flex;
    gap: 15px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}

.info-card {
    border: 1px solid #ddd;
    padding: 15px;
    border-radius: 10px;
    min-width: 200px;
    background: #fff;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.info-card.total {
    border: 2px solid #333;
    background: #f5f5f5;
}

.info-card h3,
.info-card h4 {
    margin: 0 0 10px 0;
}

.info-card p {
    margin: 5px 0;
}

.target-wrapper {
    display: flex;
    gap: 15px;
    margin-bottom: 30px;
    overflow-x: auto;
    padding-bottom: 10px;
}

.target-card {
    border: 1px solid #ddd;
    padding: 15px;
    border-radius: 10px;
    width: 280px;
    flex-shrink: 0;
    background: #fff;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.target-card.over {
    border-color: #ff4d4d;
    background: #fff1f1;
}

.target-card.normal {
    border-color: #bfe8d7;
    background: #2c3e50;
    color:#fff;
}

.progress-box {
    width: 100%;
    height: 12px;
    background: #e0e0e0;
    border-radius: 999px;
    overflow: hidden;
    margin: 8px 0 10px 0;
}

.progress-bar {
    height: 100%;
    background: #27ae60;
}

.progress-bar.over {
    background: #e74c3c;
}

.small-text {
    font-size: 0.85em;
    color: #fff;
}

.warning-text {
    color: #d63031;
    font-weight: bold;
    font-size: 0.9em;
}

.edit-cell {
    background: #fff8dc;
    outline: none;
    cursor: pointer;
}

.edit-cell:hover {
    background: #fff1b8;
}

.inline-input,
.inline-select {
    width: 100%;
    min-width: 120px;
    box-sizing: border-box;
    padding: 6px 8px;
    border: 1px solid #999;
    border-radius: 6px;
    font-size: 14px;
}

.locked-text {
    color: #bbb;
}

.cell-saving {
    background: #dff9fb !important;
}

.cell-success {
    background: #c7f7d4 !important;
}

.cell-error {
    background: #ffd6d6 !important;
}

/* =========================================
   MEDIA QUERY & HAMBURGER MENU UNTUK HP (<= 768px)
   ========================================= */
@media screen and (max-width: 768px) {
    .container {
        width: 100%;
        padding: 10px;
    }

    .hamburger {
        display: flex;
    }

    header nav {
        flex-wrap: wrap;
    }

    /* Menu turun ke bawah & disembunyikan secara default */
    header nav ul {
        display: none;
        flex-direction: column;
        width: 100%;
        background: #34495e;
        margin-top: 15px;
        padding: 10px 0;
        border-radius: 6px;
        gap: 0;
    }

    header nav ul.show {
        display: flex;
    }

    header nav ul li {
        width: 100%;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }

    header nav ul li:last-child {
        border-bottom: none;
    }

    header nav ul li a, 
    header nav ul li .drop-btn {
        padding: 14px 20px;
        font-size: 15px;
        width: 100%;
        border-radius: 0;
        text-align: left;
    }

    /* Di HP, dropdown diubah jadi statis mengikuti alur list agar mudah diklik */
    .dropdown-content {
        position: static;
        box-shadow: none;
        background: #2c3e50;
        padding-left: 15px;
        display: none;
    }

    .dropdown.active .dropdown-content {
        display: flex;
    }
    
    .dashboard-cards {
        flex-direction: column;
    }

    .info-card {
        min-width: 100%;
    }

    table th, table td {
        padding: 8px;
        font-size: 13px;
    }
}

/* Mengatur kotak card agar bisa scroll Y dengan max-height di Desktop */
.dashboard-cards.dashboard-scroll {
    max-height: 250px;
    overflow-y: auto;
    overflow-x: hidden;
    padding-right: 5px;
}

/* Media Query untuk HP (Layar <= 768px) */
@media screen and (max-width: 768px) {
    .dashboard-cards.dashboard-scroll {
        max-height: 220px;
        flex-direction: column;
        flex-wrap: nowrap;
    }
    
    .dashboard-cards.dashboard-scroll .info-card {
        width: 100%;
        min-width: 100%;
    }
}
/* Styling Utama (Desktop / Default) */
.overview-riwayat-box {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #fdfefe;
    padding: 15px 20px;
    border: 1px solid #e5e8e8;
    border-radius: 8px;
    margin-bottom: 15px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.02);
}

.overview-riwayat-box h3 {
    margin: 0;
    color: #2c3e50;
    font-size: 16px;
}

.overview-riwayat-box a {
    background: #2c3e50;
    color: #fff;
    padding: 8px 16px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 13px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: background 0.2s;
    white-space: nowrap; /* Mencegah teks tombol turun sendiri */
}

.overview-riwayat-box a:hover {
    background: #1a252f;
}

/* =========================================
   MEDIA QUERY UNTUK HP (Layar <= 768px)
   ========================================= */
@media screen and (max-width: 768px) {
    .overview-riwayat-box {
        flex-direction: column; /* Mengubah arah dari sejajar ke bertumpuk ke bawah */
        align-items: flex-start; /* Merapikan teks ke kiri */
        gap: 12px; /* Memberi jarak antara teks dan tombol */
        padding: 12px 15px;
    }

    .overview-riwayat-box h3 {
        font-size: 14px; /* Sedikit memperkecil font di HP agar tidak terlalu penuh */
        line-height: 1.4;
    }

    .overview-riwayat-box a {
        width: 100%; /* Tombol dibuat melebar penuh di HP agar mudah dipencet */
        justify-content: center; /* Teks di dalam tombol diposisikan di tengah */
        padding: 10px 16px;
    }
}
</style>
</head>
<body>
    <header>
        <div class="container">
            <nav>

                <a href="<?php echo $base_url; ?>pages/dashboard.php" class="nav-brand">
                    <img src="<?php echo $base_url; ?>includes/KasKeuanganKhresmupu.png" alt="Logo Kas Keuangan Khresmupu">
                    <span>KasKeuangan Khresmupu</span>
                </a>
                
                <button class="hamburger" id="hamburger-btn" aria-label="Menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>

                <ul id="nav-menu">
                    <li><a href="<?php echo $base_url; ?>pages/dashboard.php">Beranda</a></li>
                    
                    <li class="dropdown" id="dropdown-pencatatan">
                        <button class="drop-btn" onclick="toggleDropdown(event, 'dropdown-pencatatan')">Pencatatan ▾</button>
                        <ul class="dropdown-content">
                            <li><a href="<?php echo $base_url; ?>pages/transaksi/input.php">Catat Transaksi</a></li>
                            <li><a href="<?php echo $base_url; ?>pages/transaksi/tagihan.php">Daftar Tagihan</a></li>
                        </ul>
                    </li>

                    <li class="dropdown" id="dropdown-batasan">
                        <button class="drop-btn" onclick="toggleDropdown(event, 'dropdown-batasan')">Batasan & Alokasi ▾</button>
                        <ul class="dropdown-content">
                            <li><a href="<?php echo $base_url; ?>pages/transaksi/target.php">Target Pengeluaran</a></li>
                            <li><a href="<?php echo $base_url; ?>pages/transaksi/penyisihan_dana.php">Alokasi & Celengan</a></li>
                        </ul>
                    </li>

                    <li class="dropdown" id="dropdown-pengaturan">
                        <button class="drop-btn" onclick="toggleDropdown(event, 'dropdown-pengaturan')">Pengaturan ▾</button>
                        <ul class="dropdown-content">
                            <li><a href="<?php echo $base_url; ?>pages/transaksi/tambah_kategori.php">Jenis Pengeluaran</a></li>
                            <li><a href="<?php echo $base_url; ?>pages/transaksi/tambah_akun.php">Dompet / Rekening</a></li>
                        </ul>
                    </li>
					<li><a href="<?php echo $base_url; ?>pages/tentang.php">Tentang Website</a></li>
                    <li><a href="<?php echo $base_url; ?>index.php" style="color: #e74c3c;">Keluar</a></li>
                </ul>
            </nav>
        </div>
    </header>
    
    <script>
        const hamburgerBtn = document.getElementById('hamburger-btn');
        const navMenu = document.getElementById('nav-menu');

        hamburgerBtn.addEventListener('click', () => {
            hamburgerBtn.classList.toggle('active');
            navMenu.classList.toggle('show');
        });

        // Fungsi Toggle Dropdown
        function toggleDropdown(event, dropdownId) {
            event.stopPropagation();
            const dropdown = document.getElementById(dropdownId);
            
            // Tutup dropdown lain jika ada yang terbuka (khusus desktop)
            document.querySelectorAll('.dropdown').forEach(item => {
                if (item.id !== dropdownId) {
                    item.classList.remove('active');
                }
            });

            dropdown.classList.toggle('active');
        }

        // Klik di luar dropdown untuk menutupnya kembali
        window.addEventListener('click', () => {
            document.querySelectorAll('.dropdown').forEach(item => {
                item.classList.remove('active');
            });
        });
    </script>
    <main class="container" style="padding-top: 20px;">
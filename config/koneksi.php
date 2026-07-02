<?php
// Konfigurasi Database
$host = "sql202.infinityfree.com";
$user = "if0_42313452";
$pass = "Khresmupu190101";
$db   = "if0_42313452_kaskeuangan"; // Pastikan nama ini sama dengan yang Anda buat di MySQL

// Membuat koneksi
$conn = mysqli_connect($host, $user, $pass, $db);

// Cek apakah koneksi berhasil
if (!$conn) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}

// Mengatur charset ke utf8 agar mendukung karakter khusus jika diperlukan
mysqli_set_charset($conn, "utf8");
?>
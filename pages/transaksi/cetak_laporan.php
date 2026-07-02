<?php
session_start();
require_once '../../config/koneksi.php';

if (!isset($_SESSION['user_id'])) exit("Akses ditolak");
$user_id = (int)$_SESSION['user_id'];

// 1. Ambil kondisi filter dari $_GET (sama dengan logic di dashboard)
$where_clauses = ["tr.user_id = $user_id"];
if (!empty($_GET['f_akun'])) $where_clauses[] = "tr.akun_id = " . (int)$_GET['f_akun'];
if (!empty($_GET['f_kategori'])) $where_clauses[] = "tr.kategori_id = " . (int)$_GET['f_kategori'];
if (!empty($_GET['f_tipe'])) {
    if ($_GET['f_tipe'] == 'MASUK') $where_clauses[] = "tr.pemasukan > 0";
    if ($_GET['f_tipe'] == 'KELUAR') $where_clauses[] = "tr.pengeluaran > 0";
}
if (!empty($_GET['f_tgl_awal'])) $where_clauses[] = "tr.tanggal >= '" . mysqli_real_escape_string($conn, $_GET['f_tgl_awal']) . "'";
if (!empty($_GET['f_tgl_akhir'])) $where_clauses[] = "tr.tanggal <= '" . mysqli_real_escape_string($conn, $_GET['f_tgl_akhir']) . "'";

$query = "SELECT tr.*, k.nama_kategori, ak.nama_akun 
          FROM transaksi tr 
          JOIN kategori k ON tr.kategori_id = k.id 
          JOIN akun_pembayaran ak ON tr.akun_id = ak.id 
          WHERE " . implode(' AND ', $where_clauses) . " ORDER BY tr.tanggal ASC";
$res = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Laporan Transaksi</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background: #f2f2f2; }
    </style>
</head>
<body onload="printAndRedirect()">
    <script>
        function printAndRedirect() {
            // Memicu dialog print
            window.print();
            
            // Setelah dialog ditutup (apapun pilihannya: print, save, atau cancel),
            // tunggu sebentar lalu arahkan kembali ke dashboard
            setTimeout(function() {
                window.location.href = '../dashboard.php'; // Sesuaikan path jika berbeda
            }, 500);
        }
    </script>
    <h2>Laporan Transaksi</h2>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Deskripsi</th>
                <th>Kategori</th>
                <th>Akun</th>
                <th>Masuk</th>
                <th>Keluar</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($tr = mysqli_fetch_assoc($res)): ?>
            <tr>
                <td><?= $tr['tanggal'] ?></td>
                <td><?= $tr['deskripsi'] ?></td>
                <td><?= $tr['nama_kategori'] ?></td>
                <td><?= $tr['nama_akun'] ?></td>
                <td><?= number_format($tr['pemasukan'], 0, ',', '.') ?></td>
                <td><?= number_format($tr['pengeluaran'], 0, ',', '.') ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
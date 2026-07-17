<?php
// Aktifkan error reporting untuk debugging jika masih error
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$user_id = (int) $_SESSION['user_id'];
$username = $_SESSION['username'] ?? 'guest';
$base_url = "../";

// PERBAIKAN PATH: Sesuaikan jalur file koneksi agar tidak error 500
// Ubah path ini jika file koneksi Anda berada di folder yang berbeda tingkat
require_once __DIR__ . '/../config/koneksi.php';

$today_dt = new DateTime();
$today_dt->setTime(0, 0, 0);
$today = $today_dt->format('Y-m-d'); // Tanggal hari ini

// Direktori upload bukti di dashboard menggunakan assets/img/
$folder_user = __DIR__ . '/../assets/img/' . $username;
if (!is_dir($folder_user)) {
    @mkdir($folder_user, 0777, true);
}

// Proses Pelunasan / Pembayaran Tagihan dari Dashboard
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['proses_lunas_dash'])) {
    $tagihan_id = (int)$_POST['tagihan_id'];
    $akun_id = (int)$_POST['akun_id'];
    $kategori_id = (int)$_POST['kategori_id'];
    $nominal_bayar = (float)$_POST['nominal_bayar'];
    $tanggal_trx = $today;

    $path_bukti = null;
    if (isset($_FILES['bukti_bayar_lunas_dash']) && $_FILES['bukti_bayar_lunas_dash']['error'] == 0) {
        $target_folder_user = __DIR__ . '/../assets/img/' . $username;

        if (!is_dir($target_folder_user)) {
            mkdir($target_folder_user, 0777, true);
        }

        $zip = new ZipArchive();
        $zip_name = 'bukti_lunas_dash_' . time() . '.zip';
        $zip_path = $target_folder_user . '/' . $zip_name; 

        if ($zip->open($zip_path, ZipArchive::CREATE) === TRUE) {
            $zip->addFile($_FILES['bukti_bayar_lunas_dash']['tmp_name'], $_FILES['bukti_bayar_lunas_dash']['name']);
            $zip->close();
            $path_bukti = $username . '/' . $zip_name;
        }
    }
    $path_bukti_sql = $path_bukti ? "'$path_bukti'" : "NULL";

    $q_tagihan = mysqli_query($conn, "SELECT * FROM tagihan WHERE id = $tagihan_id AND user_id = $user_id");
    $d_tagihan = mysqli_fetch_assoc($q_tagihan);

    if ($d_tagihan) {
        $sisa_sekarang = (float)$d_tagihan['sisa_nominal'];
        $bayar_aktual = min($nominal_bayar, $sisa_sekarang);
        $sisa_baru = $sisa_sekarang - $bayar_aktual;
        
        $status_baru = ($sisa_baru <= 0 || $d_tagihan['jenis'] === 'RUTIN') ? 'LUNAS' : 'AKTIF';
        $nama_tagihan = $d_tagihan['nama_tagihan'];
        $deskripsi_trx = "Pembayaran/Pelunasan Tagihan: " . $nama_tagihan;

        mysqli_query($conn, "INSERT INTO transaksi (user_id, tanggal, kategori_id, akun_id, tipe_transaksi, pemasukan, pengeluaran, path_bukti, deskripsi) 
                             VALUES ($user_id, '$tanggal_trx', $kategori_id, $akun_id, 'NORMAL', 0, $bayar_aktual, $path_bukti_sql, '$deskripsi_trx')");
        
        mysqli_query($conn, "UPDATE akun_pembayaran SET saldo_akhir = saldo_akhir - $bayar_aktual WHERE id = $akun_id");
        mysqli_query($conn, "UPDATE tagihan SET sisa_nominal = $sisa_baru, status = '$status_baru' WHERE id = $tagihan_id");
    }

    echo "<script>alert('Pembayaran/pelunasan berhasil dicatat!'); window.location='dashboard.php';</script>";
    exit;
}

// Proses Aksi Tambah / Kurang Nominal Alokasi dari Dashboard
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah_nominal_dash'])) {
    $alokasi_id = (int)$_POST['alokasi_id'];
    $nominal_tambah = (float)$_POST['nominal_tambah'];

    $q_alok = mysqli_query($conn, "SELECT p.*, a.saldo_akhir FROM penyisihan_dana p JOIN akun_pembayaran a ON p.akun_id = a.id WHERE p.id = $alokasi_id AND p.user_id = $user_id");
    $d_alok = mysqli_fetch_assoc($q_alok);

    if ($d_alok) {
        $new_terkumpul = $d_alok['terkumpul_nominal'] + $nominal_tambah;
        $target = $d_alok['target_nominal'];
        $saldo_aktif = (float)$d_alok['saldo_akhir'];

        if ($new_terkumpul > $saldo_aktif || $target >= (0.70 * $saldo_aktif)) {
            echo "<script>alert('Gagal! Penambahan ini melebihi batas aman saldo akun.'); window.location='dashboard.php';</script>";
            exit;
        }

        $new_status = ($new_terkumpul >= $target) ? 'TERCAPAI' : 'AKTIF';
        mysqli_query($conn, "UPDATE penyisihan_dana SET terkumpul_nominal = $new_terkumpul, status = '$new_status' WHERE id = $alokasi_id AND user_id = $user_id");
        echo "<script>alert('Berhasil menambah nominal alokasi!'); window.location='dashboard.php';</script>";
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['kurang_nominal_dash'])) {
    $alokasi_id = (int)$_POST['alokasi_id'];
    $nominal_kurang = (float)$_POST['nominal_kurang'];

    $q_alok = mysqli_query($conn, "SELECT * FROM penyisihan_dana WHERE id = $alokasi_id AND user_id = $user_id");
    $d_alok = mysqli_fetch_assoc($q_alok);

    if ($d_alok) {
        $new_terkumpul = max(0, $d_alok['terkumpul_nominal'] - $nominal_kurang);
        $target = $d_alok['target_nominal'];
        $new_status = ($new_terkumpul >= $target) ? 'TERCAPAI' : 'AKTIF';

        mysqli_query($conn, "UPDATE penyisihan_dana SET terkumpul_nominal = $new_terkumpul, status = '$new_status' WHERE id = $alokasi_id AND user_id = $user_id");
        echo "<script>alert('Berhasil mengurangi nominal alokasi!'); window.location='dashboard.php';</script>";
        exit;
    }
}

// Ambil daftar akun user
$daftar_akun = [];
$query_akun = mysqli_query($conn, "SELECT id, nama_akun, saldo_akhir FROM akun_pembayaran WHERE user_id = $user_id ORDER BY id ASC");
while ($akun = mysqli_fetch_assoc($query_akun)) {
    $daftar_akun[] = $akun;
}

// Ambil daftar kategori user
$daftar_kategori = [];
$query_kategori = mysqli_query($conn, "SELECT id, nama_kategori FROM kategori WHERE user_id = $user_id ORDER BY nama_kategori ASC");
while ($kategori = mysqli_fetch_assoc($query_kategori)) {
    $daftar_kategori[] = $kategori;
}

$kategori_tagihan_list = [];
$query_kat_tagihan = mysqli_query($conn, "SELECT * FROM kategori WHERE (user_id = '$user_id' OR user_id IS NULL) AND id != 1");
while($k = mysqli_fetch_assoc($query_kat_tagihan)) {
    $kategori_tagihan_list[] = $k;
}

// Hitung saldo awal akun
$saldo_awal_map = [];
$saldo_akhir_map = [];

foreach ($daftar_akun as $akun) {
    $akun_id = (int) $akun['id'];
    $saldo_akhir = (float) $akun['saldo_akhir'];
    $saldo_akhir_map[$akun_id] = $saldo_akhir;

    $q_mutasi = mysqli_query($conn, "
        SELECT  
            COALESCE(SUM(pemasukan), 0) AS total_masuk,
            COALESCE(SUM(pengeluaran), 0) AS total_keluar
        FROM transaksi
        WHERE user_id = $user_id AND akun_id = $akun_id
    ");
    $d_mutasi = mysqli_fetch_assoc($q_mutasi);

    $total_masuk = (float) $d_mutasi['total_masuk'];
    $total_keluar = (float) $d_mutasi['total_keluar'];

    $saldo_awal_map[$akun_id] = $saldo_akhir - ($total_masuk - $total_keluar);
}

// Total saldo bersih
$query_total_saldo = mysqli_query($conn, "
    SELECT COALESCE(SUM(saldo_akhir), 0) AS total_semua_akun
    FROM akun_pembayaran
    WHERE user_id = $user_id
");
$data_total_saldo = mysqli_fetch_assoc($query_total_saldo);
$total_saldo_bersih = (float) ($data_total_saldo['total_semua_akun'] ?? 0);

// Ambil transaksi user
$semua_transaksi = [];
$query_transaksi = mysqli_query($conn, "
    SELECT tr.*, k.nama_kategori, ak.nama_akun
    FROM transaksi tr
    JOIN kategori k ON tr.kategori_id = k.id
    JOIN akun_pembayaran ak ON tr.akun_id = ak.id
    WHERE tr.user_id = $user_id
    ORDER BY tr.tanggal ASC, tr.id ASC
");
while ($row = mysqli_fetch_assoc($query_transaksi)) {
    $semua_transaksi[] = $row;
}

$total_data = count($semua_transaksi);
$batas_edit = max(0, $total_data - 7);

// Alokasi aktif
$alokasi_aktif = [];
$query_alokasi = mysqli_query($conn, "SELECT p.*, a.nama_akun, a.saldo_akhir FROM penyisihan_dana p JOIN akun_pembayaran a ON p.akun_id = a.id WHERE p.user_id = '$user_id' ORDER BY p.id ASC");
while ($row_alok = mysqli_fetch_assoc($query_alokasi)) {
    $alokasi_aktif[] = $row_alok;
}

// Target aktif
$targets = [];
$query_target = mysqli_query($conn, "
    SELECT t.*, k.nama_kategori
    FROM target t
    JOIN kategori k ON t.kategori_id = k.id
    WHERE t.user_id = $user_id AND t.status = 'AKTIF'
    ORDER BY t.tenggat_waktu ASC, t.id ASC
");

while ($target = mysqli_fetch_assoc($query_target)) {
    $id_target = (int) $target['id'];
    $kategori_id = (int) $target['kategori_id'];
    $nominal_maksimal = (float) $target['nominal_maksimal'];
    $original_tenggat = $target['tenggat_waktu'];

    if ($target['periode_target'] == 'BULANAN') {
        $start_date = date('Y-m-d', strtotime($original_tenggat . ' -1 month'));
    } elseif ($target['periode_target'] == 'TAHUNAN') {
        $start_date = date('Y-m-d', strtotime($original_tenggat . ' -1 year'));
    } else {
        $start_date = date('Y-m-d', strtotime($original_tenggat . ' -1 month')); 
    }

    $end_date = $original_tenggat;

    $sql_realisasi = "
        SELECT COALESCE(SUM(pengeluaran), 0) AS realisasi
        FROM transaksi
        WHERE user_id = $user_id
          AND kategori_id = $kategori_id
          AND tanggal > '$start_date' 
          AND tanggal <= '$end_date'
    ";

    $res_realisasi = mysqli_query($conn, $sql_realisasi);
    $data_realisasi = mysqli_fetch_assoc($res_realisasi);
    $realisasi = (float) ($data_realisasi['realisasi'] ?? 0);

    $sisa = $nominal_maksimal - $realisasi;
    $persen = $nominal_maksimal > 0 ? min(($realisasi / $nominal_maksimal) * 100, 100) : 0;
    $is_over = $realisasi > $nominal_maksimal;

    $target['realisasi'] = $realisasi;
    $target['sisa'] = $sisa;
    $target['persen'] = $persen;
    $target['is_over'] = $is_over;
    $targets[] = $target;

    $tenggat_db = new DateTime($original_tenggat);
    $tenggat_db->setTime(0, 0, 0);

    if ($tenggat_db < $today_dt) {
        if ($target['tipe_target'] === 'SEKALI') {
            mysqli_query($conn, "DELETE FROM target WHERE id = $id_target AND user_id = $user_id");
        } elseif ($target['tipe_target'] === 'RUTIN') {
            $new_tenggat = clone $tenggat_db;
            while ($new_tenggat < $today_dt) {
                if ($target['periode_target'] === 'BULANAN') {
                    $new_tenggat->modify('+1 month');
                } elseif ($target['periode_target'] === 'TAHUNAN') {
                    $new_tenggat->modify('+1 year');
                } else {
                    break; 
                }
            }
            $new_date_str = $new_tenggat->format('Y-m-d');
            mysqli_query($conn, "UPDATE target SET tenggat_waktu = '$new_date_str' WHERE id = $id_target AND user_id = $user_id");
        }
    }
}

// Tagihan aktif
$tagihan_aktif = [];
$query_tagihan_dash = mysqli_query($conn, "SELECT * FROM tagihan WHERE user_id = '$user_id' AND status = 'AKTIF' AND sisa_nominal > 0 ORDER BY tenggat_waktu ASC");
while ($row_tag = mysqli_fetch_assoc($query_tagihan_dash)) {
    $tagihan_aktif[] = $row_tag;
}

// Filter transaksi
$where_clauses = ["tr.user_id = $user_id"];

if (!empty($_GET['f_akun'])) $where_clauses[] = "tr.akun_id = " . (int)$_GET['f_akun'];
if (!empty($_GET['f_kategori'])) $where_clauses[] = "tr.kategori_id = " . (int)$_GET['f_kategori'];
if (!empty($_GET['f_tipe'])) {
    if ($_GET['f_tipe'] == 'MASUK') $where_clauses[] = "tr.pemasukan > 0";
    if ($_GET['f_tipe'] == 'KELUAR') $where_clauses[] = "tr.pengeluaran > 0";
}
if (!empty($_GET['f_tgl_awal'])) $where_clauses[] = "tr.tanggal >= '" . mysqli_real_escape_string($conn, $_GET['f_tgl_awal']) . "'";
if (!empty($_GET['f_tgl_akhir'])) $where_clauses[] = "tr.tanggal <= '" . mysqli_real_escape_string($conn, $_GET['f_tgl_akhir']) . "'";

$sql_transaksi = "SELECT tr.*, k.nama_kategori, ak.nama_akun 
                  FROM transaksi tr 
                  JOIN kategori k ON tr.kategori_id = k.id 
                  JOIN akun_pembayaran ak ON tr.akun_id = ak.id 
                  WHERE " . implode(' AND ', $where_clauses) . " 
                  ORDER BY tr.tanggal ASC, tr.id ASC";

$semua_transaksi = [];
$query_transaksi = mysqli_query($conn, $sql_transaksi);
while ($row = mysqli_fetch_assoc($query_transaksi)) {
    $semua_transaksi[] = $row;
}

$current_user_id = $_SESSION['user_id'] ?? null;

if ($current_user_id) {
    $stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
    $stmt->bind_param("i", $current_user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    $manggil_user = $user ? $user['username'] : 'Tamu';
} else {
    $manggil_user = 'Tamu';
}
?>
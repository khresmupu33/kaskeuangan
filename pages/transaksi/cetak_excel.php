<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    exit("Akses ditolak");
}

$user_id = (int) $_SESSION['user_id'];
require_once '../../config/koneksi.php';

// 1. Ambil data akun dan hitung saldo awal
$res_akun = mysqli_query($conn, "SELECT id, nama_akun, saldo_akhir FROM akun_pembayaran WHERE user_id = $user_id");
$daftar_akun = [];
$saldo_saat_ini = [];

while ($akun = mysqli_fetch_assoc($res_akun)) {
    $akun_id = (int) $akun['id'];
    $daftar_akun[] = $akun;
    
    $q_mutasi = mysqli_query($conn, "SELECT SUM(pemasukan) as in_sum, SUM(pengeluaran) as out_sum FROM transaksi WHERE user_id = $user_id AND akun_id = $akun_id");
    $mutasi = mysqli_fetch_assoc($q_mutasi);
    
    $saldo_awal = (float)$akun['saldo_akhir'] - ((float)$mutasi['in_sum'] - (float)$mutasi['out_sum']);
    $saldo_saat_ini[$akun_id] = $saldo_awal;
}

// 2. Ambil transaksi dengan filter
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
          WHERE " . implode(' AND ', $where_clauses) . " ORDER BY tr.tanggal ASC, tr.id ASC";

$res_transaksi = mysqli_query($conn, $query);

// Bersihkan buffer output
if (ob_get_level()) {
    ob_end_clean();
}

$filename = "Laporan_Transaksi_" . date('Y-m-d') . ".xls";
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Pragma: no-cache");
header("Expires: 0");

echo '<'; // Menghindari masalah parser awal
?>
?xml version="1.0" encoding="UTF-8"?>
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
          xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">
  <Styles>
    <Style ss:ID="Header">
      <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
      <Font ss:Bold="1" ss:Color="#FFFFFF"/>
      <Interior ss:Color="#2C3E50" ss:Pattern="Solid"/>
      <Borders>
        <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
        <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
        <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
        <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
      </Borders>
    </Style>
    <Style ss:ID="Title">
      <Font ss:Bold="1" ss:Size="14"/>
    </Style>
    <Style ss:ID="DataCell">
      <Borders>
        <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
        <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
        <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
        <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
      </Borders>
    </Style>
    <Style ss:ID="NumberCell">
      <Alignment ss:Horizontal="Right"/>
      <NumberFormat ss:Format="#,##0"/>
      <Borders>
        <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
        <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
        <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
        <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
      </Borders>
    </Style>
    <Style ss:ID="Footer">
      <Font ss:Bold="1"/>
      <Interior ss:Color="#EAEDED" ss:Pattern="Solid"/>
      <Borders>
        <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
        <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
        <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
        <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
      </Borders>
    </Style>
  </Styles>

  <Worksheet ss:Name="Laporan Transaksi">
    <Table>
      <Row>
        <Cell ss:StyleID="Title"><Data ss:Type="String">Laporan Transaksi KasKeuangan</Data></Cell>
      </Row>
      <Row></Row>
      
      <Row>
        <Cell ss:StyleID="Header"><Data ss:Type="String">Tanggal</Data></Cell>
        <Cell ss:StyleID="Header"><Data ss:Type="String">Deskripsi</Data></Cell>
        <Cell ss:StyleID="Header"><Data ss:Type="String">Kategori</Data></Cell>
        <Cell ss:StyleID="Header"><Data ss:Type="String">Akun</Data></Cell>
        <?php foreach ($daftar_akun as $akun): ?>
          <Cell ss:StyleID="Header"><Data ss:Type="String">Saldo <?= htmlspecialchars($akun['nama_akun']) ?></Data></Cell>
        <?php endforeach; ?>
        <Cell ss:StyleID="Header"><Data ss:Type="String">Masuk</Data></Cell>
        <Cell ss:StyleID="Header"><Data ss:Type="String">Keluar</Data></Cell>
        <Cell ss:StyleID="Header"><Data ss:Type="String">Total</Data></Cell>
      </Row>

      <?php 
      $tot_masuk = 0;
      $tot_keluar = 0;
      while ($tr = mysqli_fetch_assoc($res_transaksi)): 
          $id_akun = (int)$tr['akun_id'];
          $pemasukan = (float)$tr['pemasukan'];
          $pengeluaran = (float)$tr['pengeluaran'];

          $saldo_saat_ini[$id_akun] += ($pemasukan - $pengeluaran);
          $total_global = array_sum($saldo_saat_ini);

          $tot_masuk += $pemasukan;
          $tot_keluar += $pengeluaran;
      ?>
      <Row>
        <Cell ss:StyleID="DataCell"><Data ss:Type="String"><?= $tr['tanggal'] ?></Data></Cell>
        <Cell ss:StyleID="DataCell"><Data ss:Type="String"><?= htmlspecialchars($tr['deskripsi']) ?></Data></Cell>
        <Cell ss:StyleID="DataCell"><Data ss:Type="String"><?= htmlspecialchars($tr['nama_kategori']) ?></Data></Cell>
        <Cell ss:StyleID="DataCell"><Data ss:Type="String"><?= htmlspecialchars($tr['nama_akun']) ?></Data></Cell>
        <?php foreach ($daftar_akun as $akun): ?>
          <Cell ss:StyleID="NumberCell"><Data ss:Type="Number"><?= $saldo_saat_ini[$akun['id']] ?></Data></Cell>
        <?php endforeach; ?>
        <Cell ss:StyleID="NumberCell"><Data ss:Type="Number"><?= $pemasukan ?></Data></Cell>
        <Cell ss:StyleID="NumberCell"><Data ss:Type="Number"><?= $pengeluaran ?></Data></Cell>
        <Cell ss:StyleID="NumberCell"><Data ss:Type="Number"><?= $total_global ?></Data></Cell>
      </Row>
      <?php endwhile; ?>

      <Row>
        <Cell ss:StyleID="Footer"><Data ss:Type="String">TOTAL:</Data></Cell>
        <?php for($i=0; $i < (3 + count($daftar_akun)); $i++): ?>
          <Cell ss:StyleID="Footer"><Data ss:Type="String"></Data></Cell>
        <?php endfor; ?>
        <Cell ss:StyleID="NumberCell"><Data ss:Type="Number"><?= $tot_masuk ?></Data></Cell>
        <Cell ss:StyleID="NumberCell"><Data ss:Type="Number"><?= $tot_keluar ?></Data></Cell>
        <Cell ss:StyleID="Footer"><Data ss:Type="String"></Data></Cell>
      </Row>
    </Table>
  </Worksheet>
</Workbook>
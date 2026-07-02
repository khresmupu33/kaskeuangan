<?php 
session_start();
$base_url = "../../"; 
include '../../includes/header.php'; 
require_once '../../config/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Proses Simpan Target
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah_target'])) {
    $kategori_id = (int)$_POST['kategori_id'];
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $nominal = (float)$_POST['nominal_maksimal'];
    $tenggat = $_POST['tenggat_waktu'];
    $tipe = $_POST['tipe_target'];
    $periode = ($tipe == 'RUTIN') ? $_POST['periode_target'] : NULL; // Periode hanya jika RUTIN

    $query = "INSERT INTO target (user_id, kategori_id, deskripsi, nominal_maksimal, tenggat_waktu, tipe_target, status, periode_target) 
              VALUES ($user_id, $kategori_id, '$deskripsi', $nominal, '$tenggat', '$tipe', 'AKTIF', " . ($periode ? "'$periode'" : "NULL") . ")";
    
    mysqli_query($conn, $query);
    echo "<script>alert('Target berhasil disimpan!'); window.location='target.php';</script>";
}
?>

<div class="container">
    <h2>Pengaturan Target Keuangan</h2>

    <div style="background: #fff; padding: 20px; border: 1px solid #ddd; margin-bottom: 30px;">
        <form method="POST" id="targetForm">
            <div class="form-group">
                <label>Kategori</label>
                <select name="kategori_id" required>
                    <?php 
                    $kat = mysqli_query($conn, "SELECT * FROM kategori WHERE (user_id = '$user_id' OR user_id IS NULL) AND id != 1");
                    while($k = mysqli_fetch_assoc($kat)) echo "<option value='".$k['id']."'>".$k['nama_kategori']."</option>";
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label>Deskripsi</label>
                <input type="text" name="deskripsi" required>
            </div>
            <div class="form-group">
                <label>Nominal Maksimal</label>
                <input type="text" class="format-uang" data-hidden="nominal_maksimal" required placeholder="0">
                <input type="hidden" name="nominal_maksimal" id="nominal_maksimal">
            </div>
            <div class="form-group">
                <label>Tenggat Waktu</label>
                <input type="date" name="tenggat_waktu" required>
            </div>
            <div class="form-group">
                <label>Tipe Target</label>
                <select name="tipe_target" id="tipe_target" onchange="togglePeriode()">
                    <option value="SEKALI">Sekali</option>
                    <option value="RUTIN">Rutin</option>
                </select>
            </div>
            <div class="form-group" id="periode_wrapper" style="display:none;">
                <label>Periode Reset</label>
                <select name="periode_target">
                    <option value="BULANAN">Bulanan</option>
                    <option value="TAHUNAN">Tahunan</option>
                </select>
            </div>
            <button type="submit" name="tambah_target">Simpan Target</button>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>Kategori</th>
                <th>Deskripsi</th>
                <th>Budget</th>
                <th>Realisasi</th>
                <th>Tipe</th>
                <th>Periode</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $query = mysqli_query($conn, "SELECT t.*, k.nama_kategori FROM target t JOIN kategori k ON t.kategori_id = k.id WHERE t.user_id = '$user_id'");
            while($row = mysqli_fetch_assoc($query)): 
                // Logika Reset Otomatis
                if($row['periode_target'] == 'BULANAN') {
                    $sql_real = "SELECT SUM(pengeluaran) FROM transaksi WHERE kategori_id = '{$row['kategori_id']}' AND user_id = '$user_id' AND MONTH(tanggal) = MONTH(CURRENT_DATE()) AND YEAR(tanggal) = YEAR(CURRENT_DATE())";
                } else {
                    $sql_real = "SELECT SUM(pengeluaran) FROM transaksi WHERE kategori_id = '{$row['kategori_id']}' AND user_id = '$user_id' AND YEAR(tanggal) = YEAR(CURRENT_DATE())";
                }
                $realisasi = mysqli_fetch_array(mysqli_query($conn, $sql_real))[0] ?? 0;
            ?>
            <tr>
                <td><?= $row['nama_kategori'] ?></td>
                <td><?= $row['deskripsi'] ?></td>
                <td>Rp <?= number_format($row['nominal_maksimal'], 0, ',', '.') ?></td>
                <td>Rp <?= number_format($realisasi, 0, ',', '.') ?></td>
                <td><?= $row['tipe_target'] ?></td>
                <td><?= $row['periode_target'] ?? '-' ?></td>
                <td><?= $row['status'] ?></td>
                <td><a href="hapus_target.php?id=<?= $row['id'] ?>" style="color:red;" onclick="return confirm('Yakin?')">Hapus</a></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script>
function togglePeriode() {
    const tipe = document.getElementById('tipe_target').value;
    const wrapper = document.getElementById('periode_wrapper');
    wrapper.style.display = (tipe === 'RUTIN') ? 'block' : 'none';
}

// Script Format Uang Anda
document.querySelectorAll('input.format-uang').forEach(input => {
    input.addEventListener('input', function(e) {
        let value = this.value.replace(/[^0-9]/g, "");
        let hiddenInput = document.querySelector('input[name="' + this.getAttribute('data-hidden') + '"]');
        if (hiddenInput) hiddenInput.value = value;
        this.value = value ? value.replace(/\B(?=(\d{3})+(?!\d))/g, ".") : "";
    });
});
</script>

<?php include '../../includes/footer.php'; ?>
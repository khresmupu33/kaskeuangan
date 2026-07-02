</main>
    <footer>
        <div class="container" style="text-align: center; margin-top: 50px; color: #777;">
            <p>&copy; <?php echo date("Y"); ?> Sistem Dokumentasi Keuangan Kas</p>
        </div>
    </footer>
    
    <script >
function toggleTransferField() {
    const tipe = document.getElementById('tipe_transaksi').value;
    const katSelect = document.getElementById('kategori_select');
    const hiddenKat = document.getElementById('hidden_kategori');
    
    // Tampilkan/Sembunyikan opsi "Transfer" (Asumsi ID 1)
    for (let i = 0; i < katSelect.options.length; i++) {
        if (katSelect.options[i].value == '1') {
            if (tipe === 'TRANSFER') {
                katSelect.options[i].style.display = 'block'; // Tampilkan saat transfer
            } else {
                katSelect.options[i].style.display = 'none'; // Sembunyikan saat normal
            }
        }
    }

    if (tipe === 'TRANSFER') {
        // Otomatis pilih kategori Transfer
        for (let i = 0; i < katSelect.options.length; i++) {
            if (katSelect.options[i].value == '1') {
                katSelect.selectedIndex = i;
                hiddenKat.value = '1';
                break;
            }
        }
        katSelect.disabled = true;
        document.getElementById('akun_tujuan_wrapper').style.display = 'block';
        document.getElementById('normal_type').style.display = 'none';
    } else {
        katSelect.disabled = false;
        // Jika sebelumnya terpilih ID 1 (Transfer), paksa pindah ke opsi lain
        if (katSelect.value == '1') {
            katSelect.selectedIndex = 0; 
        }
        hiddenKat.value = katSelect.value;
        document.getElementById('akun_tujuan_wrapper').style.display = 'none';
        document.getElementById('normal_type').style.display = 'block';
    }
}

window.onload = function() {
    setTimeout(toggleTransferField, 100);
};

document.getElementById('transaksiForm').onsubmit = function() {
    document.getElementById('kategori_select').disabled = false;
};

document.querySelectorAll('input.format-uang').forEach(input => {
    input.addEventListener('input', function(e) {
        // 1. Bersihkan semua karakter selain angka
        let value = this.value.replace(/[^0-9]/g, "");
        
        // 2. Ambil target input hidden berdasarkan atribut data-hidden
        let hiddenName = this.getAttribute('data-hidden');
        let hiddenInput = this.parentElement.querySelector('input[name="' + hiddenName + '"]');
        
        if (hiddenInput) {
            hiddenInput.value = value;
        }
        
        // 3. Format tampilan (titik sebagai pemisah ribuan)
        this.value = value ? value.replace(/\B(?=(\d{3})+(?!\d))/g, ".") : "";
    });
});
        
        
function saveData(el, id, kolom) {
    let nilai = el.innerText.replace(/\./g, ''); 
    
    // Path diubah mengarah ke dalam folder pages/
    fetch('pages/update_ajax.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'id=' + id + '&kolom=' + kolom + '&nilai=' + encodeURIComponent(nilai)
    })
    .then(response => response.text())
    .then(data => {
        console.log('Update berhasil');
        // Tambahkan reload agar saldo terhitung ulang secara akurat
        location.reload(); 
    });
    

}</script>
</body>
</html>
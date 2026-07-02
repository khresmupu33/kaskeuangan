<?php
require_once 'config/koneksi.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    
    // Cek apakah username sudah ada
    $check = mysqli_query($conn, "SELECT id FROM users WHERE username = '$username'");
    
    if (mysqli_num_rows($check) == 0) {
        // Simpan user baru
        mysqli_query($conn, "INSERT INTO users (username) VALUES ('$username')");
        $_SESSION['user_id'] = mysqli_insert_id($conn);
        $_SESSION['username'] = $username;
        
        // Arahkan paksa ke setup awal
        header("Location: setup_awal.php");
        exit();
    } else {
        $error = "Username sudah terdaftar. Silakan pilih yang lain.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body style="display:flex; justify-content:center; align-items:center; height:100vh;">
    <div style="width:300px; padding:20px; border:1px solid #ddd; background:#fff; border-radius:8px;">
        <h2 style="text-align:center;">Registrasi</h2>
        <?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
        <form method="POST">
            <div class="form-group">
                <input type="text" name="username" placeholder="Buat Username Baru" required>
            </div>
            <button type="submit" style="width:100%;">Daftar & Setup Akun</button>
        </form>
        <p style="margin-top:10px; text-align:center;"><a href="index.php">Kembali ke Login</a></p>
    </div>
</body>
</html>
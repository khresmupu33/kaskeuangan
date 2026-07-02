<?php 
require_once 'config/koneksi.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $query = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");
    
    if (mysqli_num_rows($query) > 0) {
        $user = mysqli_fetch_assoc($query);
        $_SESSION['user_id'] = $user['id'];
        header("Location: pages/dashboard.php");
    } else {
        $error = "Username tidak terdaftar!";
    }
}
?>
<!DOCTYPE html>
<html>
<head><link rel="stylesheet" href="assets/style.css"></head>
<body style="display:flex; justify-content:center; align-items:center; height:100vh;">
    <div style="width:300px; padding:20px; border:1px solid #ddd; background:#fff; border-radius:8px;">
        <h2 style="text-align:center;">Login</h2>
        <?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
        <form method="POST">
            <div class="form-group"><input type="text" name="username" placeholder="Username" required></div>
            <button type="submit" style="width:100%;">Masuk</button>
        </form>
        <p style="margin-top:10px; text-align:center;">Belum punya akun? <a href="register.php">Daftar</a></p>
    </div>
</body>
</html>
<?php
session_start();
require_once '../../config/koneksi.php';
if (isset($_GET['id']) && isset($_SESSION['user_id'])) {
    $id = (int)$_GET['id'];
    $user_id = (int)$_SESSION['user_id'];
    mysqli_query($conn, "DELETE FROM target WHERE id = $id AND user_id = $user_id");
}
header("Location: target.php");
exit();
?>
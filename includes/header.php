
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Aplikasi Keuangan Kas</title>
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
}

header nav ul {
    list-style: none;
    display: flex;
}

header nav ul li {
    margin-right: 20px;
}

header nav ul li a {
    color: #fff;
    text-decoration: none;
    font-weight: bold;
}

.table-wrap {
    overflow-x: auto;
}
/* Tabel Styling */
table {
    width: 100%;
    border-collapse: collapse;
    margin: 20px 0;
    background: #fff;
}

table th, table td {
    padding: 12px;
    border: 1px solid #ddd;
    text-align: left;
}

table th {
    background-color: #3498db;
    color: white;
}
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}

table th,
table td {
    border: 1px solid #ddd;
    padding: 10px;
    text-align: left;
    vertical-align: top;
    white-space: nowrap;
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
    min-width: 220px;
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
    background: #eefaf5;
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
    color: #555;
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
    </style>
</head>
<body>
    <header>
        <div class="container">
            <nav>
                <ul>
                    <li><a href="<?php echo $base_url; ?>pages/dashboard.php">Dashboard</a></li>
                    <li><a href="<?php echo $base_url; ?>pages/transaksi/input.php">Input Transaksi</a></li>
                    <li><a href="<?php echo $base_url; ?>pages/transaksi/tambah_kategori.php">Input kategori</a></li>
                    <li><a href="<?php echo $base_url; ?>pages/transaksi/tambah_akun.php">Input akun</a></li>
                    <li><a href="<?php echo $base_url; ?>pages/hutang_piutang/list.php">Hutang/Piutang</a></li>
                    <li><a href="<?php echo $base_url; ?>pages/transaksi/target.php">Target</a></li>
                    <li><a href="<?php echo $base_url; ?>index.php">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <main class="container" style="padding-top: 20px;">
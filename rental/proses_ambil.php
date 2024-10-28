<?php
session_start();

// Pastikan pengguna sudah login dan memiliki peran 'user'
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'user') {
    echo '<script>alert("Anda harus login sebagai User untuk mengakses halaman ini."); location.href="login.php";</script>';
    exit();
}

// Menghubungkan ke database
include 'koneksi.php';

// Ambil nopol dan nik dari POST
$nopol = $_POST['nopol'];
$nik = $_POST['nik'];

// Update status transaksi menjadi 'ambil'
$query = "UPDATE tbl_transaksi SET status = 'ambil' WHERE nopol = '$nopol' AND nik = '$nik' AND status = 'approved'";
if (mysqli_query($conn, $query)) {
    echo '<script>alert("Mobil berhasil diambil."); location.href="user.php";</script>';
} else {
    echo "Error: " . mysqli_error($conn);
}
?>

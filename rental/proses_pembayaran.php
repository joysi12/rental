<?php
session_start();

// Pastikan pengguna sudah login
if (!isset($_SESSION['username'])) {
    echo '<script>alert("Anda harus login untuk mengakses halaman ini."); location.href="login.php";</script>';
    exit();
}

// Menghubungkan ke database
include 'koneksi.php';

// Ambil data dari form
$id_transaksi = $_POST['id_transaksi'];
$jenis_pembayaran = '';
$jumlah = 0;

if (isset($_POST['denda'])) {
    $jenis_pembayaran = 'denda';
    $jumlah = str_replace(',', '', $_POST['denda']); // Hapus koma untuk konversi ke decimal
} elseif (isset($_POST['biaya_tambahan'])) {
    $jenis_pembayaran = 'biaya_tambahan';
    $jumlah = str_replace(',', '', $_POST['biaya_tambahan']); // Hapus koma untuk konversi ke decimal
}

// Masukkan data ke tabel lunas
$query = "INSERT INTO lunas (id_transaksi, jenis_pembayaran, jumlah, tanggal) VALUES ('$id_transaksi', '$jenis_pembayaran', '$jumlah', NOW())";

if (mysqli_query($conn, $query)) {
    echo '<script>alert("Pembayaran berhasil dilakukan."); location.href="pembayaran.php";</script>';
} else {
    echo '<script>alert("Gagal melakukan pembayaran: ' . mysqli_error($conn) . '"); location.href="pembayaran.php";</script>';
}

// Tutup koneksi database
mysqli_close($conn);
?>

<?php
session_start();
include 'koneksi.php';

// Ambil nik dari sesi
$nik = $_SESSION['nik']; // Pastikan session nik sudah di-set saat login
$nopol = isset($_POST['nopol']) ? $_POST['nopol'] : null;
$tgl_booking = date('Y-m-d'); // Tanggal booking saat ini
$tgl_ambil = isset($_POST['tgl_ambil']) ? $_POST['tgl_ambil'] : null;
$tgl_kembali = isset($_POST['tgl_kembali']) ? $_POST['tgl_kembali'] : null;
$supir = isset($_POST['supir']) ? 1 : 0; // 1 jika checked, 0 jika tidak
$total = isset($_POST['total']) ? $_POST['total'] : 0; // Total biaya
$dp = isset($_POST['dp']) ? $_POST['dp'] : 0; // Down Payment
$kekurangan = isset($_POST['kekurangan']) ? $_POST['kekurangan'] : 0; // Kekurangan

// Simpan data ke tbl_transaksi
$query = "INSERT INTO tbl_transaksi (nik, nopol, tgl_booking, tgl_ambil, tgl_kembali, supir, total, downpayment, kekurangan, status) VALUES ('$nik', '$nopol', '$tgl_booking', '$tgl_ambil', '$tgl_kembali', '$supir', '$total', '$dp', '$kekurangan', 'booking')";

if (mysqli_query($conn, $query)) {
    echo '<script>alert("Permintaan rental berhasil. Menunggu konfirmasi petugas."); location.href="user.php";</script>';
} else {
    echo '<script>alert("Permintaan rental tidak berhasil."); location.href="user.php";</script>';
}
?>

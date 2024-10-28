<?php
session_start();
include 'koneksi.php';

// Pastikan user adalah petugas
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'petugas') {
    echo '<script>alert("Anda harus login sebagai Petugas untuk mengakses halaman ini."); location.href="login.php";</script>';
    exit();
}

// Ambil ID transaksi dari URL
if (isset($_GET['id_transaksi']) && isset($_GET['aksi'])) {
    $id_transaksi = $_GET['id_transaksi'];
    $aksi = $_GET['aksi'];

    if ($aksi === 'disetujui') {
        // Update status transaksi menjadi 'approved'
        $update_query = "UPDATE tbl_transaksi SET status = 'approved' WHERE id_transaksi = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("i", $id_transaksi);

        if ($stmt->execute()) {
            echo '<script>alert("Transaksi disetujui."); location.href="petugas.php";</script>';
        } else {
            echo '<script>alert("Gagal mengubah status transaksi."); location.href="petugas.php";</script>';
        }
    }
} else {
    echo '<script>alert("Data tidak valid."); location.href="petugas.php";</script>';
}
?>

<?php
session_start();
include 'koneksi.php';

// Pastikan hanya admin yang bisa menghapus mobil
if ($_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Debugging: Memastikan bahwa `nopol` dikirim melalui POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['nopol'])) {
        $nopol = mysqli_real_escape_string($conn, $_POST['nopol']);
        
        // Debugging untuk memastikan `nopol` sudah diterima
        echo "Nopol yang diterima: " . $nopol;
        
        // Query untuk menghapus mobil
        $sql = "DELETE FROM tbl_mobil WHERE nopol = '$nopol'";

        // Debugging untuk melihat query yang dijalankan
        echo "<br>Query yang dijalankan: " . $sql;
        
        // Eksekusi query
        if ($conn->query($sql) === TRUE) {
            // Redirect setelah berhasil menghapus mobil
            header("Location: daftarmobil.php?message=Mobil berhasil dihapus");
            exit;
        } else {
            // Menampilkan error jika query gagal
            echo "Error: " . $conn->error;
        }
    } else {
        echo "Nopol tidak ditemukan di POST";
    }
}
?>

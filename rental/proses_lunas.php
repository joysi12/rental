<?php
session_start();
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nopol = $_POST['nopol']; // Dapatkan nopol dari form
    $nik = $_POST['nik']; // Dapatkan nik dari form
    $nominal = $_POST['nominal']; // Dapatkan nominal dari form

    // Debug: tampilkan nilai nopol dan nik
    echo "nopol: $nopol, nik: $nik, nominal: $nominal<br>";

    // Cek apakah mobil sudah pernah dicicil
    $queryCek = "SELECT * FROM tbl_bayar WHERE nopol = '$nopol' AND nik = '$nik'";
    $resultCek = mysqli_query($conn, $queryCek);

    if (!$resultCek) {
        echo "Error: " . mysqli_error($conn);
        exit();
    }

    // Ambil data transaksi untuk menghitung kekurangan
    $queryTransaksi = "SELECT * FROM tbl_transaksi WHERE nopol = '$nopol' AND nik = '$nik'";
    $resultTransaksi = mysqli_query($conn, $queryTransaksi);

    if (!$resultTransaksi) {
        echo "Error: " . mysqli_error($conn);
        exit();
    }

    $transaksi = mysqli_fetch_assoc($resultTransaksi);
    $kekurangan = $transaksi['kekurangan'];

    // Jika ada cicilan sebelumnya
    if (mysqli_num_rows($resultCek) > 0) {
        // Mengupdate total bayar
        $bayar = mysqli_fetch_assoc($resultCek);
        $totalBayar = $bayar['total_bayar'] + $nominal;

        // Update ke tbl_bayar
        $updateQuery = "UPDATE tbl_bayar SET total_bayar = $totalBayar WHERE nopol = '$nopol' AND nik = '$nik'";
        if (mysqli_query($conn, $updateQuery)) {
            echo "Pembayaran cicilan berhasil diupdate.<br>";
        } else {
            echo "Error saat memperbarui pembayaran: " . mysqli_error($conn);
            exit();
        }
    } else {
        // Insert ke tbl_bayar untuk cicilan pertama
        $insertQuery = "INSERT INTO tbl_bayar (nopol, nik, tgl_bayar, total_bayar, status) VALUES ('$nopol', '$nik', CURDATE(), $nominal, 'belum lunas')";
        if (mysqli_query($conn, $insertQuery)) {
            echo "Cicilan pertama berhasil ditambahkan.<br>";
        } else {
            echo "Error saat mencatat pembayaran: " . mysqli_error($conn);
            exit();
        }
    }

    // Hitung sisa kekurangan setelah pembayaran
    $newKekurangan = $kekurangan - $nominal;

    // Update kekurangan di tbl_transaksi tanpa mengubah status mobil
    $updateTransaksi = "UPDATE tbl_transaksi SET kekurangan = $newKekurangan WHERE nopol = '$nopol' AND nik = '$nik'";
    mysqli_query($conn, $updateTransaksi);

    if ($newKekurangan <= 0) {
        // Jika sudah lunas, update status di tbl_bayar menjadi lunas
        $updateBayar = "UPDATE tbl_bayar SET status = 'lunas' WHERE nopol = '$nopol' AND nik = '$nik'";
        mysqli_query($conn, $updateBayar);
        
        echo "Pembayaran cicilan lunas.<br>";
    } else {
        echo "Sisa kekurangan setelah pembayaran: Rp " . number_format($newKekurangan, 2) . "<br>";
    }

    // Kembali ke koleksi.php
    header('Location: koleksi.php');
    exit();
}
?>

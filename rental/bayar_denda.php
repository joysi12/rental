<?php
session_start();

// Pastikan pengguna sudah login dan memiliki peran 'user' atau 'petugas'
if (!isset($_SESSION['username']) || !in_array($_SESSION['role'], ['user', 'petugas'])) {
    echo '<script>alert("Anda harus login sebagai User atau Petugas untuk mengakses halaman ini."); location.href="login.php";</script>';
    exit();
}

// Menghubungkan ke database
include 'koneksi.php';

// Ambil NIK dari session untuk user
$nik = $_SESSION['role'] === 'user' ? $_SESSION['nik'] : null;

// Query untuk mengambil data denda dan biaya tambahan
$query = "
    SELECT t.nopol, k.tgl_kembali, k.denda, k.biaya_tambahan, k.id_kembali, t.kekurangan
    FROM tbl_kembali k
    JOIN tbl_transaksi t ON k.id_transaksi = t.id_transaksi
    WHERE t.nik " . ($nik ? "= '$nik'" : "IS NOT NULL") . " AND t.status = 'kembali'
";

$result = mysqli_query($conn, $query);

// Inisialisasi total
$denda_data = []; // Array untuk menyimpan data denda dan biaya

// Mengumpulkan data denda dan biaya tambahan
while ($row = mysqli_fetch_assoc($result)) {
    $denda_data[] = $row; // Simpan semua data denda dan biaya ke dalam array
}

// Proses pembayaran jika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Mengambil id_kembali dari input
    $id_kembali = $_POST['id_kembali'];
    $nominal_pembayaran = str_replace(',', '', $_POST['nominal_pembayaran']); // Hapus koma untuk konversi ke decimal

    // Cek apakah id_kembali ada di tbl_kembali
    $checkQuery = "SELECT * FROM tbl_kembali WHERE id_kembali = '$id_kembali'";
    $checkResult = mysqli_query($conn, $checkQuery);

    if (mysqli_num_rows($checkResult) > 0) {
        // Ambil data denda dan biaya untuk ID Kembali
        $dendaRow = mysqli_fetch_assoc($checkResult);
        $totalPembayaran = $dendaRow['denda'] + $dendaRow['biaya_tambahan'] + $dendaRow['kekurangan'];

        // Cek apakah nominal pembayaran cukup
        if ($nominal_pembayaran >= $totalPembayaran) {
            // Masukkan data ke tabel tbl_bayar
            $insertQuery = "
                INSERT INTO tbl_bayar (nik, id_kembali, nopol, tgl_bayar, total_bayar, status) 
                VALUES ('$nik', '$id_kembali', '{$dendaRow['nopol']}', NOW(), '$totalPembayaran', 'lunas')
            ";

            if (mysqli_query($conn, $insertQuery)) {
                // Update status di tbl_transaksi menjadi 'lunas'
                $updateStatusQuery = "UPDATE tbl_transaksi SET status = 'lunas' WHERE id_transaksi = (SELECT id_transaksi FROM tbl_kembali WHERE id_kembali = '$id_kembali')";
                mysqli_query($conn, $updateStatusQuery);

                echo '<script>alert("Pembayaran berhasil dilakukan."); location.href="bayar_denda.php";</script>';
            } else {
                echo '<script>alert("Gagal melakukan pembayaran: ' . mysqli_error($conn) . '");</script>';
            }
        } else {
            echo '<script>alert("Nominal pembayaran tidak mencukupi.");</script>';
        }
    } else {
        echo '<script>alert("ID Kembali tidak valid. Pembayaran tidak dapat diproses."); location.href="bayar_denda.php";</script>';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Denda dan Kerusakan</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 50px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #333;
            font-size: 24px;
            margin-bottom: 20px;
        }

        p {
            color: #555;
            font-size: 16px;
            margin: 10px 0;
        }

        label {
            display: block;
            color: #555;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"], input[type="number"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 16px;
            background-color: #f9f9f9;
        }

        input[type="submit"] {
            display: inline-block;
            width: 100%;
            background-color: #28a745;
            color: #fff;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            text-align: center;
            transition: background-color 0.3s;
        }

        input[type="submit"]:hover {
            background-color: #218838;
        }

        hr {
            border: 1px solid #ddd;
            margin: 20px 0;
        }

        @media (max-width: 768px) {
            .container {
                width: 90%;
                margin: 20px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Pembayaran Denda dan Kerusakan</h1>

    <?php if (!empty($denda_data)): ?>
        <?php foreach ($denda_data as $row): ?>
            <form method="post">
                <p><strong>Nomor Polisi:</strong> <?php echo htmlspecialchars($row['nopol']); ?></p>
                <p><strong>Tanggal Kembali:</strong> <?php echo htmlspecialchars($row['tgl_kembali']); ?></p>
                <p><strong>Total Denda:</strong> Rp <input type="text" id="total_denda" value="<?php echo number_format($row['denda'], 2); ?>" readonly></p>
                <p><strong>Total Biaya Tambahan:</strong> Rp <input type="text" id="total_biaya_tambahan" value="<?php echo number_format($row['biaya_tambahan'], 2); ?>" readonly></p>
                <p><strong>Total Kekurangan Sewa:</strong> Rp <input type="text" id="total_kekurangan" value="<?php echo number_format($row['kekurangan'], 2); ?>" readonly></p>
                <p><strong>Total Pembayaran:</strong> Rp <input type="text" id="total_bayar" name="total_bayar" value="<?php echo number_format($row['denda'] + $row['biaya_tambahan'] + $row['kekurangan'], 2); ?>" readonly></p>

                <label for="nominal_pembayaran">Masukkan Nominal Pembayaran:</label>
                <input type="number" name="nominal_pembayaran" id="nominal_pembayaran" required placeholder="Masukkan jumlah yang ingin dibayar" min="0" step="0.01" value="<?php echo number_format($row['denda'] + $row['biaya_tambahan'] + $row['kekurangan'], 2); ?>">

                <input type="hidden" name="id_kembali" value="<?php echo $row['id_kembali']; ?>">
                <input type="hidden" name="nopol" value="<?php echo htmlspecialchars($row['nopol']); ?>">

                <input type="submit" value="Bayar Denda dan Biaya Tambahan">
                <hr>
            </form>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Tidak ada denda atau biaya tambahan yang perlu dibayar.</p>
    <?php endif; ?>
</div>

</body>
</html>

<?php
// Tutup koneksi database
mysqli_close($conn);
?>

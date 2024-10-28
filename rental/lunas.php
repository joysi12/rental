<?php
session_start();

// Pastikan pengguna sudah login dan memiliki peran 'petugas' atau 'admin'
if (!isset($_SESSION['username']) || ($_SESSION['role'] !== 'petugas' && $_SESSION['role'] !== 'admin')) {
    echo '<script>alert("Anda harus login sebagai Petugas atau Admin untuk mengakses halaman ini."); location.href="login.php";</script>';
    exit();
}

// Menghubungkan ke database
include 'koneksi.php';

// Query untuk mengambil data pembayaran dari tabel tbl_bayar yang sudah lunas
$query = "
    SELECT DISTINCT b.id_bayar, b.nik, b.id_kembali, b.tgl_bayar, b.total_bayar, k.denda, k.biaya_tambahan
    FROM tbl_bayar b
    JOIN tbl_kembali k ON b.id_kembali = k.id_kembali
    WHERE b.status = 'lunas'
";

$result = mysqli_query($conn, $query);

// Cek apakah query berhasil
if (!$result) {
    die("Query gagal: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pembayaran Lunas</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 20px;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .print-button {
            display: block;
            margin: 20px auto;
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        .print-button:hover {
            background-color: #0056b3;
        }

        @media (max-width: 768px) {
            table {
                font-size: 14px;
            }
        }
    </style>
    <script>
        // Fungsi untuk mencetak halaman
        function printPage() {
            window.print();
        }
    </script>
</head>
<body>

<h1>Riwayat Pembayaran Lunas</h1>

<table>
    <thead>
        <tr>
            <th>ID Bayar</th>
            <th>NIK User</th>
            <th>ID Kembali</th>
            <th>Tanggal Bayar</th>
            <th>Denda</th>
            <th>Biaya Tambahan</th>
            <th>Total Pembayaran</th>
        </tr>
    </thead>
    <tbody>
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id_bayar']); ?></td>
                    <td><?php echo htmlspecialchars($row['nik']); ?></td>
                    <td><?php echo htmlspecialchars($row['id_kembali']); ?></td>
                    <td><?php echo htmlspecialchars($row['tgl_bayar']); ?></td>
                    <td><?php echo 'Rp ' . number_format($row['denda'], 2); ?></td>
                    <td><?php echo 'Rp ' . number_format($row['biaya_tambahan'], 2); ?></td>
                    <td><?php echo 'Rp ' . number_format($row['total_bayar'], 2); ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="7">Tidak ada pembayaran yang tercatat.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<button class="print-button" onclick="printPage()">Cetak Laporan</button>

</body>
</html>

<?php
// Tutup koneksi database
mysqli_close($conn);
?>

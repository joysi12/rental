<?php
session_start();

// Pastikan pengguna sudah login dan memiliki peran 'petugas'
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'petugas') {
    echo '<script>alert("Anda harus login sebagai Petugas untuk mengakses halaman ini."); location.href="login.php";</script>';
    exit();
}

// Menghubungkan ke database
include 'koneksi.php';

// Query untuk mengambil data mobil yang telah dikembalikan
$query = "
    SELECT t.nopol, t.nik, k.tgl_kembali, k.kondisi_mobil, k.denda, k.biaya_tambahan
    FROM tbl_kembali k
    JOIN tbl_transaksi t ON k.id_transaksi = t.id_transaksi
    WHERE t.status = 'kembali'
";

// Eksekusi query
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
    <title>Mobil yang Telah Dikembalikan</title>
    <link rel="stylesheet" href="styles.css">
    <style>
    body {
        font-family: 'Arial', sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 20px;
    }

    h1 {
        color: #333;
        text-align: center;
        margin-bottom: 20px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin: 20px 0;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        overflow: hidden;
    }

    table,
    th,
    td {
        border: 1px solid #ddd;
    }

    th,
    td {
        padding: 12px;
        text-align: center;
        font-size: 16px;
    }

    th {
        background-color: #007bff;
        color: white;
    }

    tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    tr:hover {
        background-color: #e7f3ff;
        transition: background-color 0.3s ease;
    }

    .btn {
        display: inline-block;
        padding: 8px 12px;
        margin-top: 10px;
        background-color: #007bff;
        color: white;
        text-decoration: none;
        border-radius: 5px;
        transition: background-color 0.3s ease;
    }

    .btn:hover {
        background-color: #0056b3;
    }
    </style>
</head>

<body>

    <h1>Daftar Mobil yang Telah Dikembalikan</h1>

    <table>
        <thead>
            <tr>
                <th>Nomor Polisi (Nopol)</th>
                <th>NIK User</th>
                <th>Tanggal Kembali</th>
                <th>Kondisi Mobil</th>
                <th>Denda</th>
                <th>Biaya Tambahan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['nopol']); ?></td>
                <td><?php echo htmlspecialchars($row['nik']); ?></td>
                <td><?php echo htmlspecialchars($row['tgl_kembali']); ?></td>
                <td><?php echo htmlspecialchars($row['kondisi_mobil']); ?></td>
                <td><?php echo 'Rp ' . number_format($row['denda'], 2, ',', '.'); ?></td>
                <td><?php echo 'Rp ' . number_format($row['biaya_tambahan'], 2, ',', '.'); ?></td>
                <td>
                    <a href="kelola_pengembalian.php?nopol=<?php echo urlencode($row['nopol']); ?>&nik=<?php echo urlencode($row['nik']); ?>" class="btn">Kelola Pengembalian</a>
                </td>
            </tr>
            <?php endwhile; ?>
            <?php else: ?>
            <tr>
                <td colspan="7">Tidak ada mobil yang telah dikembalikan.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>

</body>

</html>

<?php
// Tutup koneksi database
mysqli_close($conn);
?>

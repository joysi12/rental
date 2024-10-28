<?php
session_start();

// Pastikan pengguna sudah login dan memiliki peran 'petugas'
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'petugas') {
    echo '<script>alert("Anda harus login sebagai Petugas untuk mengakses halaman ini."); location.href="login.php";</script>';
    exit();
}

// Menghubungkan ke database
include 'koneksi.php';

// Mendapatkan tanggal hari ini
$today = date('Y-m-d');

// Query untuk memperbarui status transaksi yang sudah melewati tanggal kembali
$update_query = "
    UPDATE tbl_transaksi t
    SET t.status = 'kembali'
    WHERE t.tgl_kembali <= '$today' AND t.status != 'kembali'
";

if (!mysqli_query($conn, $update_query)) {
    die('Error saat memperbarui status transaksi: ' . mysqli_error($conn));
}

// Query untuk mengambil semua mobil yang disewa oleh pengguna
$query = "
    SELECT t.id_transaksi, t.nopol, t.nik, t.tgl_booking, t.tgl_ambil, t.tgl_kembali, t.supir, t.total, t.downpayment, t.kekurangan, t.status, m.brand, m.type, m.foto
    FROM tbl_transaksi t
    JOIN tbl_mobil m ON t.nopol = m.nopol
    WHERE t.status IN ('approved', 'ambil')
";

// Mengeksekusi query dan memeriksa apakah berhasil
$result = mysqli_query($conn, $query);
if (!$result) {
    die('Error saat mengambil data mobil: ' . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Koleksi - Mobil yang Dipinjam</title>
    <style>
        /* Global Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            background-color: #f0f2f5;
            color: #333;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Header Styles */
        header {
            background-color: #0056b3;
            color: #fff;
            padding: 20px 0;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .navbar ul {
            list-style-type: none;
            padding: 0;
        }

        .navbar ul li {
            display: inline;
            margin: 0 20px;
        }

        .navbar ul li a {
            color: #fff;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s;
        }

        .navbar ul li a:hover {
            color: #ffd700;
        }

        /* Main Content Styles */
        .main-content {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 30px;
        }

        .book-list {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }

        /* Card Styles */
        .card {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 300px;
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .card img {
            width: 100%;
            height: 200px;
            border-radius: 10px 10px 0 0;
            object-fit: cover;
        }

        .card h3 {
            margin: 15px 0 10px;
            font-size: 1.25em;
            color: #0056b3;
        }

        .card p {
            margin: 5px 0;
            color: #555;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        /* Button Styles */
        .pinjam-button {
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 12px 18px;
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.3s, transform 0.3s;
        }

        .pinjam-button:hover {
            background-color: #218838;
            transform: translateY(-2px);
        }

        /* Footer Styles */
        footer {
            background-color: #0056b3;
            color: white;
            text-align: center;
            padding: 20px;
            position: relative;
            bottom: 0;
            width: 100%;
            margin-top: auto;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <header>
        <div class="navbar">
            <h1>Koleksi Mobil yang Dipinjam oleh Pengguna</h1>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="koleksi_petugas.php">Koleksi</a></li>
                    <li><a href="bayar_denda.php">Pembayaran</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <div class="main-content">
        <section class="book-list">
            <?php
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $denda = 0;
                    if ($row['status'] == 'ambil' && $row['tgl_kembali'] < $today) {
                        $tanggal_kembali = new DateTime($row['tgl_kembali']);
                        $tanggal_sekarang = new DateTime($today);
                        $selisih = $tanggal_sekarang->diff($tanggal_kembali)->days;
                        $denda = $selisih * 100000; // Rp 100.000 per hari
                    }

                    echo "<div class='card'>";
                    echo "<img src='" . $row['foto'] . "' alt='Gambar Mobil'>";
                    echo "<h3>" . $row['brand'] . " " . $row['type'] . "</h3>";
                    echo "<p>Nopol: " . $row['nopol'] . "</p>";
                    echo "<p>NIK Penyewa: " . $row['nik'] . "</p>";
                    echo "<p>Tanggal Booking: " . $row['tgl_booking'] . "</p>";
                    echo "<p>Tanggal Ambil: " . $row['tgl_ambil'] . "</p>";
                    echo "<p>Total Sewa: Rp " . number_format($row['total'], 2) . "</p>";
                    echo "<p>Uang Muka: Rp " . number_format($row['downpayment'], 2) . "</p>";
                    
                    if ($denda > 0) {
                        echo "<p style='color: red;'>Denda: Rp " . number_format($denda, 2) . "</p>";
                    }

                    if ($row['status'] == 'approved') {
                        echo "<p>Status: Menunggu Diambil</p>";
                    } elseif ($row['status'] == 'ambil') {
                        echo "<p>Status: Mobil Digunakan</p>";
                        echo "
                            <a href='kembali.php?nopol=" . $row['nopol'] . "&nik=" . $row['nik'] . "' class='pinjam-button'>Kembalikan Mobil</a>
                        ";
                    }

                    echo "</div>";
                }
            } else {
                echo "<p>Tidak ada mobil yang sedang dipinjam.</p>";
            }
            ?>
        </section>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; 2024 Rental Cars. All Rights Reserved.</p>
    </footer>
</body>
</html>

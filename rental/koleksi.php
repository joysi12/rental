<?php
session_start();

// Pastikan pengguna sudah login dan memiliki peran 'user'
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'user') {
    echo '<script>alert("Anda harus login sebagai User untuk mengakses halaman ini."); location.href="login.php";</script>';
    exit();
}

// Cek apakah 'nik' ada dalam session
if (!isset($_SESSION['nik'])) {
    echo '<script>alert("NIK tidak ditemukan. Silakan login kembali."); location.href="login.php";</script>';
    exit();
}

// Menghubungkan ke database
include 'koneksi.php';

// Mendapatkan 'nik' dari session
$nik = $_SESSION['nik'];

// Cek apakah ada mobil yang harus dikembalikan secara otomatis
$today = date('Y-m-d');

// Query untuk memperbarui status yang sudah lewat tanggal kembalinya
$update_query = "
    UPDATE tbl_transaksi t
    SET t.status = 'kembali'
    WHERE EXISTS (
        SELECT 1 FROM tbl_kembali k
        WHERE t.id_transaksi = k.id_transaksi
        AND k.tgl_kembali <= '$today'
        AND t.status != 'kembali'
    )
";

mysqli_query($conn, $update_query);

// Query untuk mengambil mobil yang disewa oleh user dan status pembayaran
$query = "
    SELECT t.nopol, m.brand, m.type, m.foto, t.total, t.downpayment, t.kekurangan, t.status, b.status AS status_bayar, k.tgl_kembali
    FROM tbl_transaksi t
    JOIN tbl_mobil m ON t.nopol = m.nopol
    LEFT JOIN tbl_bayar b ON t.id_transaksi = b.id_kembali
    LEFT JOIN tbl_kembali k ON t.id_transaksi = k.id_transaksi
    WHERE t.nik = '$nik' AND t.status IN ('approved', 'ambil')
";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Koleksi - Mobil yang Disewa</title>
    <link rel="stylesheet" href="styles.css"> <!-- Sesuaikan dengan file CSS Anda -->
</head>
<body>
    <!-- Navigation Bar -->
    <header>
        <div class="navbar">
            <h1>Koleksi Mobil yang Disewa</h1>
            <nav>
                <ul>
                    <li><a href="index.php">home</a></li>
                    <li><a href="koleksi.php">Koleksi</a></li>
                    <li><a href="bayar_denda.php">pembayaran</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Mobil List -->
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
                echo "<p>Total Sewa: Rp " . number_format($row['total'], 2) . "</p>";
                echo "<p>Uang Muka: Rp " . number_format($row['downpayment'], 2) . "</p>";
                
                if ($denda > 0) {
                    echo "<p style='color: red;'>Denda: Rp " . number_format($denda, 2) . "</p>";
                }

                if ($row['status'] == 'approved') {
                    echo "<p>Status: Menunggu Diambil</p>";
                    echo "
                        <form action='proses_ambil.php' method='post'>
                            <input type='hidden' name='nopol' value='" . $row['nopol'] . "'>
                            <input type='hidden' name='nik' value='" . $nik . "'>
                            <button class='pinjam-button' type='submit'>Ambil</button>
                        </form>
                    ";
                } elseif ($row['status'] == 'ambil') {
                    echo "<p>Status: Mobil Digunakan</p>";
                    echo "
                        <a href='kembali.php?nopol=" . $row['nopol'] . "&nik=" . $nik . "' class='pinjam-button'>Kembalikan Mobil</a>
                    ";
                }

                echo "</div>";
            }
        } else {
            echo "<p>Tidak ada mobil yang disewa.</p>";
        }
        ?>
    </section>

    <footer>
        <p>&copy; 2024 Rental Cars. All Rights Reserved.</p>
    </footer>
</body>

<style>
    /* Global Styles */
body {
    font-family: 'Helvetica Neue', Arial, sans-serif;
    background-color: #f0f2f5;
    color: #333;
    margin: 0;
    padding: 0;
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
    color: #ffd700; /* Gold color on hover */
}

/* Main Content Styles */
.book-list {
    padding: 30px;
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 20px; /* Adds space between cards */
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
    height: 200px; /* Fixed height for uniformity */
    border-radius: 10px 10px 0 0;
    object-fit: cover; /* Ensures images cover the area */
}

.card h3 {
    margin: 15px 0 10px;
    font-size: 1.25em; /* Larger heading */
    color: #0056b3; /* Brand color */
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
    font-size: 1em; /* Increase font size */
    transition: background-color 0.3s, transform 0.3s;
}

.pinjam-button:hover {
    background-color: #218838;
    transform: translateY(-2px);
}

/* Footer Styles */
footer {
    text-align: center;
    padding: 20px;
    background-color: #0056b3;
    color: white;
    position: relative;
    bottom: 0;
    width: 100%;
    margin-top: 20px; /* Spacing above footer */
}

</style>
</html>

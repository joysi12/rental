<?php
session_start();

if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}

$username = $_SESSION['username'];
$role = $_SESSION['role'];

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rental Mobil</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
    /* Global Styles */
    body {
        margin: 0;
        font-family: 'Roboto', sans-serif;
        background: linear-gradient(to right, #e0eafc, #cfdef3);
        color: #333;
    }

    a {
        text-decoration: none;
        color: inherit;
    }

    .container {
        width: 90%;
        margin: 0 auto;
    }

    /* Navbar */
    .navbar {
        background: #2c3e50;
        padding: 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .user-info {
        color: white;
        font-size: 1.2rem;
        font-weight: bold;
    }

    .nav-links {
        display: flex;
        align-items: center;
    }

    .nav-links ul {
        list-style: none;
        display: flex;
        margin: 0;
        padding: 0;
    }

    .nav-links ul li {
        margin-left: 20px;
    }

    .nav-links ul li a {
        color: white;
        padding: 10px 15px;
        border-radius: 8px;
        transition: background-color 0.3s, transform 0.2s;
        position: relative;
    }

    .nav-links ul li a::after {
        content: '';
        position: absolute;
        width: 0;
        height: 2px;
        background: #e74c3c;
        left: 50%;
        bottom: -5px;
        transition: width 0.3s ease, left 0.3s ease;
    }

    .nav-links ul li a:hover::after {
        width: 100%;
        left: 0;
    }

    .btn-logout {
        background-color: #e74c3c;
        padding: 10px 15px;
        border-radius: 8px;
        color: white;
        transition: background-color 0.3s, transform 0.2s;
        margin-left: 20px;
    }

    .btn-logout:hover {
        background-color: #c0392b;
        transform: translateY(-2px);
    }

    /* Hero Section */
    .hero {
        background: linear-gradient(135deg, #3498db, #2980b9);
        color: white;
        padding: 120px 0;
        text-align: center;
        clip-path: ellipse(75% 100% at center top);
    }

    .hero h1 {
        font-size: 3rem;
        margin-bottom: 10px;
        animation: fadeInDown 1s ease-in-out;
    }

    .hero p {
        font-size: 1.2rem;
        animation: fadeIn 1.5s ease-in-out;
    }

    .btn-primary {
        background: #ffffff;
        color: #3498db;
        padding: 10px 20px;
        border-radius: 8px;
        margin-top: 20px;
        transition: background-color 0.3s, color 0.3s;
        display: inline-block;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        animation: fadeInUp 1.5s ease-in-out;
    }

    .btn-primary:hover {
        background-color: #3498db;
        color: white;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Services Section */
    .services {
        background: #ffffff;
        padding: 60px 0;
        text-align: center;
        display: flex;
        justify-content: space-around;
        flex-wrap: wrap;
    }

    .service-box {
        background: #f4f6f9;
        padding: 30px;
        width: 22%;
        margin: 15px;
        border-radius: 15px;
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s;
    }

    .service-box:hover {
        transform: translateY(-5px);
    }

    .icon {
        font-size: 3rem;
        color: #3498db;
        margin-bottom: 15px;
    }

    .service-box h3 {
        font-size: 1.5rem;
        margin-bottom: 10px;
        color: #333;
    }

    .service-box p {
        color: #666;
    }

    /* Footer */
    .footer {
        background: #2c3e50;
        color: white;
        padding: 20px 0;
        text-align: center;
        border-top-left-radius: 15px;
        border-top-right-radius: 15px;
    }
    </style>
</head>

<body>
    <!-- Navigation Section -->
    <nav class="navbar">
        <div class="container">
            <div class="user-info">Logged in as: <?php echo htmlspecialchars($username); ?></div>
            <div class="nav-links">
                <ul>
                    <?php if ($role == 'admin'): ?>
                    <li><a href="daftarmobil.php">Daftar Mobil</a></li>
                    <li><a href="tambahmobil.php">Tambah Mobil</a></li>
                    <li><a href="lunas.php">Laporan</a></li>
                    <li><a href="laporan_transaksi.php">Laporan Transaksi</a></li>
                    <li><a href="tambah_member.php">Tambah Member</a></li>
                    <li><a href="tambah_petugas.php">Tambah Petugas</a></li>
                    <?php endif; ?>
                    <?php if ($role == 'petugas'): ?>
                    <li><a href="petugas.php">Transaksi</a></li>
                    <li><a href="koleksi_petugas.php">Dipinjam</a></li>
                    <li><a href="mobil_kembali.php">Pengembalian</a></li>
                    <li><a href="lunas.php">Laporan</a></li>
                    <li><a href="bayar_denda.php">Pembayaran</a></li>
                    <?php endif; ?>
                    <?php if ($role == 'user'): ?>
                    <li><a href="koleksi.php">Penyewaan</a></li>
                    <li><a href="user.php"> Sewa</a></li>
                    <li><a href="bayar_denda.php">Pembayaran</a></li>
                    <?php endif; ?>
                </ul>
                <a href="?logout=true" class="btn-logout">Logout</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container hero-content">
            <h1>Selamat Datang di Rental Mobil</h1>
            <p>Gunakan menu di atas untuk menjelajahi pilihan mobil yang tersedia dan melakukan pemesanan.</p>
            <a href="user.php" class="btn-primary">Jelajahi Mobil</a>
        </div>
    </section>

    <!-- Services Section -->
    <section class="services">
        <div class="container">
            <div class="service-box">
                <i class="icon fas fa-car"></i>
                <h3>Mobil Tersedia</h3>
                <p>Lihat daftar mobil yang tersedia untuk disewa.</p>
            </div>
            <div class="service-box">
                <i class="icon fas fa-calendar-alt"></i>
                <h3>Pemesanan Mudah</h3>
                <p>Pesan mobil dalam hitungan menit dengan sistem pemesanan yang cepat.</p>
            </div>
            <div class="service-box">
                <i class="icon fas fa-history"></i>
                <h3>Riwayat Penyewaan</h3>
                <p>Kelola dan lihat riwayat penyewaan mobil Anda.</p>
            </div>
            <div class="service-box">
                <i class="icon fas fa-user-cog"></i>
                <h3>Dukungan Pelanggan</h3>
                <p>Hubungi kami untuk bantuan dan pertanyaan seputar penyewaan.</p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2024 Rental Mobil. All rights reserved.</p>
    </footer>
</body>

</html>
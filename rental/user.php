<?php
session_start();

// Cek apakah pengguna telah login dan memiliki role 'user'
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'user') {
    echo '<script>alert("Anda harus login sebagai User untuk mengakses halaman ini."); location.href="login.php";</script>';
    exit();
}

include 'koneksi.php'; // Menghubungkan ke database

// Dapatkan nik dari session jika ada
$nik = isset($_SESSION['id_user']) ? $_SESSION['id_user'] : null; // Menggunakan null jika tidak ada

// Query untuk mengambil semua mobil
$query = "SELECT * FROM tbl_mobil";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User - Daftar Mobil</title>
    <link rel="stylesheet" href="styles.css"> <!-- Tambahkan link CSS -->
</head>
<body>
    <!-- Navigation Bar -->
    <header>
        <div class="navbar">
            <h1>Daftar Mobil</h1>
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
        // Menampilkan daftar mobil dari database
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                // Cek status mobil di semua transaksi
                $nopol = $row['nopol'];
                $status_query = "SELECT status FROM tbl_transaksi WHERE nopol='$nopol' AND (status='approved' OR status='ambil')";
                $status_result = mysqli_query($conn, $status_query);

                $is_rented = mysqli_num_rows($status_result) > 0; // Jika ada transaksi, maka mobil sedang disewa
                
                echo "<div class='card'>";
                echo "<img src='" . $row['foto'] . "' alt='Gambar Mobil'>";
                echo "<h3>" . $row['brand'] . " " . $row['type'] . "</h3>";
                echo "<p>Tahun: " . $row['tahun'] . "</p>";
                echo "<p>Harga sewa per hari: Rp " . number_format($row['harga'], 2) . "</p>";

                // Tampilkan tombol berdasarkan apakah mobil sedang disewa atau tidak
                if ($is_rented) {
                    echo "<button class='pinjam-button disabled-button' disabled>Disewa</button>";
                } else {
                    echo "<form action='form_sewa.php?nopol=" . $row['nopol'] . "' method='get'>
                            <input type='hidden' name='nopol' value='" . $row['nopol'] . "'>
                            <button class='pinjam-button' type='submit'>Sewa Mobil</button>
                          </form>";
                }

                echo "</div>"; // Tutup div.card
            }
        } else {
            echo "<p>Tidak ada mobil yang ditemukan</p>";
        }
        ?>
    </section>

    <footer>
        <p>&copy; 2024 Rental Cars. All Rights Reserved.</p>
    </footer>
</body>
<style>
/* Global Styles */
/* Global Styles */
/* Global Styles */
body {
    font-family: 'Roboto', sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f4f4f4; /* Light background color */
    display: flex;
    flex-direction: column;
    min-height: 100vh; /* Full height for flex container */
}

header {
    background-color: #2c3e50; /* Darker shade for the header */
    color: #ffffff;
    padding: 10px 20px; /* Added padding */
    display: flex; /* Use flexbox for header */
    align-items: center; /* Center items vertically */
}

/* Navbar Styles */
.navbar {
    display: flex;
    justify-content: space-between; /* Space between title and nav */
    align-items: center; /* Center items vertically */
    width: 100%; /* Full width for navbar */
}

.navbar h1 {
    font-size: 1.8rem; /* Smaller font size for title */
    margin: 0; /* Remove margin */
    flex-grow: 1; /* Allow title to take space */
    text-align: left; /* Align text to the left */
}

.navbar nav {
    display: flex; /* Use flex for nav items */
}

.navbar ul {
    list-style-type: none;
    padding: 0;
    margin: 0;
    display: flex; /* Align items horizontally */
}

.navbar ul li {
    margin: 0 15px; /* Space between items */
}

.navbar ul li a {
    color: #ecf0f1; /* Light color for better contrast */
    text-decoration: none;
    padding: 10px 15px; /* Smaller padding */
    border-radius: 5px; /* Rounded corners */
    transition: background-color 0.3s, color 0.3s; /* Smooth transitions */
}

.navbar ul li a:hover {
    background-color: #f39c12; /* Orange color on hover */
    color: white; /* Change text color on hover */
}

/* Main Content */
.book-list {
    padding: 25px;
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    flex-grow: 1; /* Allow this section to grow */
}

.card {
    background-color: #ffffff;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    margin: 15px;
    padding: 20px;
    width: 300px; /* Set a fixed width for uniformity */
    transition: box-shadow 0.3s, transform 0.3s;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.card img {
    width: 100%;
    height: 200px; /* Adjusted height for uniformity */
    border-radius: 10px;
    object-fit: cover; /* Maintain aspect ratio */
}

.card h3 {
    margin: 15px 0 10px;
}

.card p {
    margin: 5px 0;
    color: #555;
}

.card:hover {
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    transform: translateY(-2px); /* Slight upward movement on hover */
}

/* Button Styles */
.pinjam-button {
    margin-top: 15px;
    padding: 10px 20px;
    background-color: #28a745;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
    transition: background-color 0.3s, transform 0.3s; /* Add transition */
}

.pinjam-button:hover {
    background-color: #218838;
    transform: translateY(-2px); /* Slight upward movement on hover */
}

.disabled-button {
    background-color: grey;
    cursor: not-allowed;
}

/* Footer Styles */
footer {
    text-align: center;
    padding: 15px 0;
    background-color: #2c3e50;
    color: white;
    margin-top: auto; /* Push footer to the bottom */
    width: 100%; /* Full width */
}

</style>
</html>

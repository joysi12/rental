<?php
session_start();
include 'koneksi.php';

// Cek apakah pengguna adalah admin
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    echo '<script>alert("Anda harus login sebagai Admin untuk mengakses halaman ini."); location.href="login.php";</script>';
    exit();
}

// Jika ada permintaan untuk menghapus mobil
if (isset($_GET['delete'])) {
    $nopol = $_GET['delete'];

    // Query untuk menghapus mobil dari tbl_mobil
    $delete_query = "DELETE FROM tbl_mobil WHERE nopol = '$nopol'";
    if (mysqli_query($conn, $delete_query)) {
        echo "<script>alert('Mobil berhasil dihapus.'); location.href='daftarmobil.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus mobil.');</script>";
    }
}

// Jika ada permintaan untuk mengedit mobil
if (isset($_POST['update'])) {
    $nopol = $_POST['nopol'];
    $brand = $_POST['brand'];
    $type = $_POST['type'];
    $tahun = $_POST['tahun'];
    $harga = $_POST['harga'];

    // Query untuk memperbarui data mobil
    $update_query = "UPDATE tbl_mobil SET brand='$brand', type='$type', tahun='$tahun', harga='$harga' WHERE nopol='$nopol'";
    if (mysqli_query($conn, $update_query)) {
        echo "<script>alert('Mobil berhasil diperbarui.'); location.href='daftarmobil.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui mobil.');</script>";
    }
}

// Ambil data mobil dari database
$query = "SELECT * FROM tbl_mobil";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Mobil</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Gaya CSS untuk daftar mobil */
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        header {
            background-color: #0056b3;
            color: #fff;
            padding: 15px 0;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .navbar ul {
            list-style-type: none;
            padding: 0;
        }

        .navbar ul li {
            display: inline;
            margin: 0 15px;
        }

        .navbar ul li a {
            color: white;
            text-decoration: none;
        }

        .car-list {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            padding: 20px;
        }

        .card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            margin: 15px;
            padding: 20px;
            width: 300px;
            text-align: center;
            transition: transform 0.2s;
        }

        .card:hover {
            transform: scale(1.05); /* Zoom effect on hover */
        }

        .card img {
            width: 100%;
            height: 200px;
            border-radius: 10px;
            object-fit: cover; /* Maintain aspect ratio */
        }

        .card h3 {
            margin: 15px 0 10px;
        }

        .delete-button {
            background-color: #e74c3c;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
            display: inline-block;
            margin-top: 10px;
        }

        .delete-button:hover {
            background-color: #c0392b;
        }

        .edit-button {
            background-color: #3498db;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
            display: inline-block;
            margin-top: 10px;
        }

        .edit-button:hover {
            background-color: #2980b9;
        }

        footer {
            text-align: center;
            padding: 10px;
            background-color: #0056b3;
            color: white;
            position: fixed;
            bottom: 0;
            width: 100%;
            flex-shrink: 0;
        }

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.6);
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fff;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            animation: modalAnim 0.3s ease;
        }

        @keyframes modalAnim {
            from { opacity: 0; transform: translateY(-30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        label {
            display: block;
            margin-top: 10px;
        }

        input[type="text"],
        input[type="number"] {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button[type="submit"] {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 15px;
            width: 100%;
        }

        button[type="submit"]:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
<header>
    <div class="navbar">
        <h1>Daftar Mobil</h1>
        <nav>
            <ul>
                <li><a href="index.php">Kembali</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </div>
</header>

<section class="car-list">
    <?php
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<div class='card'>";
            echo "<img src='" . $row['foto'] . "' alt='Gambar Mobil'>";
            echo "<h3>" . $row['brand'] . " " . $row['type'] . "</h3>";
            echo "<p>Nopol: " . $row['nopol'] . "</p>";
            echo "<p>Tahun: " . $row['tahun'] . "</p>";
            echo "<p>Harga: Rp " . number_format($row['harga'], 2) . "</p>";

            // Tombol Hapus Mobil
            echo "<a href='daftarmobil.php?delete=" . $row['nopol'] . "' class='delete-button' onclick='return confirm(\"Apakah Anda yakin ingin menghapus mobil ini?\");'>Hapus</a>";

            // Tombol Edit Mobil
            echo "<button class='edit-button' onclick='openModal(\"" . $row['nopol'] . "\", \"" . $row['brand'] . "\", \"" . $row['type'] . "\", " . $row['tahun'] . ", " . $row['harga'] . ");'>Edit</button>";

            echo "</div>";
        }
    } else {
        echo "<p>Tidak ada mobil yang tersedia.</p>";
    }
    ?>
</section>

<!-- Modal untuk Edit Mobil -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Edit Mobil</h2>
        <form id="editForm" method="post" action="">
            <input type="hidden" name="nopol" id="nopol" value="">
            <label for="brand">Brand:</label>
            <input type="text" name="brand" id="brand" required>
            <label for="type">Type:</label>
            <input type="text" name="type" id="type" required>
            <label for="tahun">Tahun:</label>
            <input type="number" name="tahun" id="tahun" required>
            <label for="harga">Harga:</label>
            <input type="number" name="harga" id="harga" required>
            <button type="submit" name="update">Perbarui</button>
        </form>
    </div>
</div>

<footer>
    <p>&copy; 2024 Rental Mobil</p>
</footer>

<script>
    // Fungsi untuk membuka modal
    function openModal(nopol, brand, type, tahun, harga) {
        document.getElementById('nopol').value = nopol;
        document.getElementById('brand').value = brand;
        document.getElementById('type').value = type;
        document.getElementById('tahun').value = tahun;
        document.getElementById('harga').value = harga;

        document.getElementById('editModal').style.display = "block";
    }

    // Fungsi untuk menutup modal
    function closeModal() {
        document.getElementById('editModal').style.display = "none";
    }

    // Tutup modal jika pengguna mengklik di luar modal
    window.onclick = function(event) {
        const modal = document.getElementById('editModal');
        if (event.target == modal) {
            closeModal();
        }
    }
</script>
</body>
</html>

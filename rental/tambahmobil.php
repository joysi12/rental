<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    echo '<script>alert("Anda harus login sebagai Admin untuk mengakses halaman ini."); location.href="login.php";</script>';
    exit();
}

// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "rental");

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $nopol = $_POST['nopol'];
    $brand = $_POST['brand'];
    $type = $_POST['type'];
    $tahun = $_POST['tahun'];
    $harga = $_POST['harga'];
    $status = $_POST['status'];

    // Folder tempat menyimpan foto
    $target_dir = "upload/";
    $target_file = $target_dir . basename($_FILES["foto"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Cek apakah file gambar adalah gambar sungguhan
    $check = getimagesize($_FILES["foto"]["tmp_name"]);
    if ($check === false) {
        echo '<script>alert("File bukan gambar."); location.href="tambahmobil.php";</script>';
        $uploadOk = 0;
    }

    // Cek ukuran file (5 MB max)
    if ($_FILES["foto"]["size"] > 5000000) {
        echo '<script>alert("Maaf, file Anda terlalu besar."); location.href="tambahmobil.php";</script>';
        $uploadOk = 0;
    }

    // Hanya izinkan format JPEG
    if ($imageFileType != "jpg" && $imageFileType != "jpeg") {
        echo '<script>alert("Maaf, hanya file JPEG yang diperbolehkan."); location.href="tambahmobil.php";</script>';
        $uploadOk = 0;
    }

    // Cek apakah $uploadOk di-set ke 0 oleh kesalahan
    if ($uploadOk == 0) {
        echo '<script>alert("Maaf, file Anda tidak diunggah."); location.href="tambahmobil.php";</script>';
    } else {
        if (move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
            // Query untuk menambahkan mobil ke database
            $query = "INSERT INTO tbl_mobil (nopol, brand, type, tahun, harga, foto, status) 
                      VALUES ('$nopol', '$brand', '$type', '$tahun', '$harga', '$target_file', '$status')";
            if (mysqli_query($conn, $query)) {
                echo '<script>alert("Mobil berhasil ditambahkan."); location.href="tambahmobil.php";</script>';
            } else {
                echo '<script>alert("Terjadi kesalahan saat menambahkan mobil: ' . mysqli_error($conn) . '"); location.href="tambahmobil.php";</script>';
            }
        } else {
            echo '<script>alert("Maaf, terjadi kesalahan saat mengunggah file Anda."); location.href="tambahmobil.php";</script>';
        }
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Mobil</title>
    <link rel="stylesheet" href="styles.css">
    <style>
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

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 90%;
            margin: 0 auto;
        }

        .navbar h1 {
            font-size: 1.8rem;
            margin: 0;
        }

        .navbar ul {
            list-style-type: none;
            padding: 0;
            display: flex;
            gap: 15px; /* Space between links */
        }

        .navbar ul li a {
            color: white;
            text-decoration: none;
            transition: color 0.3s;
        }

        .navbar ul li a:hover {
            color: #ffd700; /* Gold color on hover */
        }

        .form-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: calc(80vh - 60px);
            margin: 20px 0;
        }

        form {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 500px; /* Limit form width */
        }

        label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold; /* Bold labels */
        }

        input[type="text"],
        input[type="number"],
        input[type="file"],
        select {
            padding: 10px;
            width: 100%; /* Full width */
            font-size: 16px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1); /* Inner shadow */
            transition: border-color 0.3s; /* Transition for border */
        }

        input[type="text"]:focus,
        input[type="number"]:focus,
        select:focus {
            border-color: #0056b3; /* Border color on focus */
            outline: none; /* Remove default outline */
        }

        button {
            padding: 12px 20px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s, transform 0.3s; /* Transition effects */
            width: 100%; /* Full width */
        }

        button:hover {
            background-color: #218838;
            transform: translateY(-2px); /* Slight lift effect */
        }

        footer {
            text-align: center;
            padding: 10px;
            background-color: #0056b3;
            color: white;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <header>
        <div class="navbar">
            <h1>Tambah Mobil</h1>
            <nav>
                <ul>
                    <li><a href="index.php">Kembali</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <section class="form-container">
        <form action="tambahmobil.php" method="post" enctype="multipart/form-data">
            <label for="nopol">Nomor Polisi:</label>
            <input type="text" name="nopol" required>

            <label for="brand">Brand:</label>
            <input type="text" name="brand" required>

            <label for="type">Type:</label>
            <input type="text" name="type" required>

            <label for="tahun">Tahun:</label>
            <input type="number" name="tahun" required>

            <label for="harga">Harga:</label>
            <input type="number" name="harga" required>

            <label for="foto">Foto:</label>
            <input type="file" name="foto" accept="image/*" required>

            <label for="status">Ketersediaan:</label>
            <select name="status" required>
                <option value="tersedia">Tersedia</option>
                <option value="tidak tersedia">Tidak Tersedia</option>
            </select>

            <button type="submit">Tambah Mobil</button>
        </form>
    </section>
    <footer>
        <p>&copy; 2024 Rental Cars. All Rights Reserved.</p>
    </footer>
</body>
</html>

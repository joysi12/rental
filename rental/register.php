<?php
session_start();
include 'koneksi.php';

$error = "";

// Jika form register disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nik = mysqli_real_escape_string($conn, $_POST['nik']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $jk = mysqli_real_escape_string($conn, $_POST['jk']);
    $telp = mysqli_real_escape_string($conn, $_POST['telp']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Hash password sebelum disimpan

    // Cek apakah username sudah digunakan
    $sql_check = "SELECT * FROM tbl_member WHERE user = '$username'";
    $result_check = $conn->query($sql_check);

    if ($result_check->num_rows == 0) {
        // Insert data ke tbl_member
        $sql = "INSERT INTO tbl_member (nik, nama, jk, telp, alamat, user, pass, role) 
                VALUES ('$nik', '$nama', '$jk', '$telp', '$alamat', '$username', '$hashed_password', 'user')";

        if ($conn->query($sql) === TRUE) {
            header("Location: login.php");
            exit;
        } else {
            $error = "Gagal mendaftarkan pengguna: " . $conn->error;
        }
    } else {
        $error = "Username sudah digunakan!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>
<body>
    <div class="container">
        <h2>Register</h2>
        <form method="POST" action="">
            <input type="text" name="nik" placeholder="NIK" required>
            <input type="text" name="nama" placeholder="Nama Lengkap" required>
            <select name="jk" required>
                <option value="">Pilih Jenis Kelamin</option>
                <option value="L">Laki-Laki</option>
                <option value="P">Perempuan</option>
            </select>
            <input type="text" name="telp" placeholder="Nomor Telepon" required>
            <textarea name="alamat" placeholder="Alamat" required></textarea>
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Register</button>
        </form>
        <p>Sudah punya akun? <a href="login.php">Login disini</a></p>
        <?php
        if ($error) {
            echo "<p class='error'>$error</p>";
        }
        ?>
    </div>
    <style>
        body { 
            font-family: 'Arial', sans-serif; 
            background: #f3f4f6; 
            color: #333; 
        }
        .container {
            width: 100%;
            max-width: 400px;
            margin: 50px auto;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center; 
        }
        input[type="text"],
        input[type="password"],
        select,
        textarea {
            width: calc(100% - 20px);
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px; 
        }
        button {
            background-color: #007BFF;
            color: white;
            padding: 12px; 
            border: none; 
            border-radius: 5px; 
            cursor: pointer; 
            width: 100%; 
        }
        button:hover { 
            background-color: #0056b3; 
        }
        .error { 
            color: #e74c3c; 
        }
    </style>
</body>
</html>

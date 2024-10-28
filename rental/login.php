<?php
session_start();
include 'koneksi.php'; // Sesuaikan nama file koneksi Anda

$error = "";

// Jika form login disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Menggunakan mysqli_real_escape_string untuk menghindari SQL injection
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Cek username di tbl_user (untuk admin atau petugas)
    $sql_user = "SELECT * FROM tbl_user WHERE user = '$username'";
    $result_user = $conn->query($sql_user);

    if ($result_user->num_rows > 0) {
        $row_user = $result_user->fetch_assoc();

        // Verifikasi password yang di-hash
        if (password_verify($password, $row_user['pass'])) {
            // Set sesi setelah login berhasil
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $row_user['lvl'];
            $_SESSION['nik'] = $row_user['nik']; // Simpan NIK jika ada kolom ini

            // Arahkan semua user ke index.php
            header("Location: index.php");
            exit;
        } else {
            $error = "Password salah!";
        }
    } else {
        // Jika tidak ditemukan di tbl_user, cek di tbl_member (untuk user biasa)
        $sql_member = "SELECT * FROM tbl_member WHERE user = '$username'";
        $result_member = $conn->query($sql_member);

        if ($result_member->num_rows > 0) {
            $row_member = $result_member->fetch_assoc();

            // Verifikasi password yang di-hash
            if (password_verify($password, $row_member['pass'])) {
                // Set sesi setelah login berhasil
                $_SESSION['loggedin'] = true;
                $_SESSION['username'] = $username;
                $_SESSION['role'] = 'user'; // Tetapkan role sebagai user
                $_SESSION['nik'] = $row_member['nik']; // Simpan NIK dari tbl_member

                // Arahkan user ke index.php
                header("Location: index.php");
                exit;
            } else {
                $error = "Password salah!";
            }
        } else {
            $error = "Username tidak ditemukan!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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
        input[type="password"] {
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
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <form method="POST" action="">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <p>Belum punya akun? <a href="register.php">Register disini</a></p>
        <?php
        if ($error) {
            echo "<p class='error'>$error</p>";
        }
        ?>
    </div>
</body>
</html>

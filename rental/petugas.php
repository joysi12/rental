<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'petugas') {
    echo '<script>alert("Anda harus login sebagai Petugas untuk mengakses halaman ini."); location.href="login.php";</script>';
    exit();
}

// Query untuk mengambil daftar transaksi dengan status 'booking'
$query = "SELECT t.id_transaksi, m.nama AS user_name, t.nopol, t.tgl_booking, t.status 
          FROM tbl_transaksi t 
          JOIN tbl_member m ON t.nik = m.nik 
          WHERE t.status = 'booking'";

$result = mysqli_query($conn, $query);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Transaksi</title>
    <style>
        /* Global Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f2f5;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        header {
            background-color: #4c6ef5;
            color: #fff;
            padding: 20px;
            text-align: center;
            position: relative;
            z-index: 1;
        }

        header h1 {
            font-size: 2.5rem;
            margin: 0;
        }

        header::before {
            content: '';
            position: absolute;
            bottom: -20px;
            left: 0;
            width: 100%;
            height: 50px;
            background-color: #2f3b8e;
            clip-path: polygon(0 100%, 100% 0, 100% 100%);
            z-index: -1;
        }

        /* Main Content */
        .form-section {
            flex: 1;
            padding: 40px;
            margin: 30px auto;
            max-width: 1200px;
            background-color: #fff;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border-radius: 15px;
            position: relative;
            overflow: hidden;
        }

        .form-section h2 {
            font-size: 2rem;
            color: #333;
            margin-bottom: 30px;
            position: relative;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
            background-color: #fff;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        }

        th, td {
            padding: 15px 20px;
            text-align: center;
            font-size: 1rem;
        }

        th {
            background-color: #4c6ef5;
            color: #fff;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        td {
            background-color: #f8f9fa;
            border-bottom: 1px solid #e0e0e0;
            transition: background-color 0.3s ease;
        }

        tr:hover td {
            background-color: #e9ecef;
        }

        a {
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 50px;
            background-color: #38c172;
            color: white;
            font-weight: bold;
            text-transform: uppercase;
            transition: background-color 0.3s ease, transform 0.2s ease;
            display: inline-block;
        }

        a:hover {
            background-color: #2fa564;
            transform: scale(1.05);
        }

        /* Footer */
        footer {
            text-align: center;
            padding: 20px;
            background-color: #4c6ef5;
            color: white;
            position: sticky;
            bottom: 0;
            width: 100%;
            z-index: 1;
        }

        footer p {
            margin: 0;
            font-size: 0.9rem;
        }

        footer::before {
            content: '';
            position: absolute;
            top: -20px;
            left: 0;
            width: 100%;
            height: 50px;
            background-color: #2f3b8e;
            clip-path: polygon(100% 0, 0 100%, 0 0);
            z-index: -1;
        }

        /* Interactive shapes */
        .shape {
            position: absolute;
            z-index: -1;
            border-radius: 50%;
            opacity: 0.15;
            filter: blur(20px);
        }

        .shape.one {
            width: 150px;
            height: 150px;
            background-color: #ff6b6b;
            top: 10%;
            left: -50px;
        }

        .shape.two {
            width: 250px;
            height: 250px;
            background-color: #48dbfb;
            bottom: 5%;
            right: -80px;
        }

        .shape.three {
            width: 100px;
            height: 100px;
            background-color: #5f27cd;
            bottom: 20%;
            left: -40px;
        }
    </style>
</head>
<body>

    <header>
        <h1>Konfirmasi Transaksi</h1>
    </header>

    <div class="form-section">
        <h2>Daftar Permintaan Transaksi</h2>
        <table>
            <tr>
                <th>Nama User</th>
                <th>Nopol</th>
                <th>Tanggal Booking</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
            <?php if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?php echo $row['user_name']; ?></td>
                    <td><?php echo $row['nopol']; ?></td>
                    <td><?php echo $row['tgl_booking']; ?></td>
                    <td><?php echo $row['status']; ?></td>
                    <td>
                        <a href="proses_konfirmasi.php?id_transaksi=<?php echo $row['id_transaksi']; ?>&aksi=disetujui">Setujui</a>
                    </td>
                </tr>
            <?php }
            } else { ?>
                <tr>
                    <td colspan="5">Tidak ada permintaan transaksi yang menunggu konfirmasi.</td>
                </tr>
            <?php } ?>
        </table>
    </div>

    <!-- Decorative shapes -->
    <div class="shape one"></div>
    <div class="shape two"></div>
    <div class="shape three"></div>

    <footer>
        <p>&copy; 2024 Rental Cars. All Rights Reserved.</p>
    </footer>

</body>
</html>

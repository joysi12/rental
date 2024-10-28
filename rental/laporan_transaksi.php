<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    echo '<script>alert("Anda harus login untuk mengakses halaman ini."); location.href="login.php";</script>';
    exit();
}

// Database connection
include 'koneksi.php';

// Fetch transaction data
$query = "SELECT 
            id_transaksi,
            nik,
            nopol,
            tgl_booking,
            tgl_ambil,
            tgl_kembali,
            IF(supir = 1, 'Yes', 'No') AS driver_needed,
            total,
            downpayment,
            kekurangan,
            status
          FROM 
            tbl_transaksi";

$result = mysqli_query($conn, $query);

// Prepare report summary
$total_transactions = mysqli_num_rows($result);
$total_amount = 0;
$total_downpayment = 0;
$total_outstanding = 0;

while ($row = mysqli_fetch_assoc($result)) {
    $total_amount += $row['total'];
    $total_downpayment += $row['downpayment'];
    $total_outstanding += $row['kekurangan'];
}

// Reset result pointer for detailed records
mysqli_data_seek($result, 0);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Transaksi</title>
    <style>
    body {
        font-family: 'Arial', sans-serif;
        margin: 20px;
        padding: 20px;
        background-color: #f4f6f9;
        color: #333;
    }

    h1 {
        text-align: center;
        color: #0056b3;
        margin-bottom: 20px;
    }

    .summary {
        margin-top: 20px;
        padding: 10px;
        background-color: #e7f1ff;
        border: 1px solid #b3d4ff;
        border-radius: 5px;
        font-size: 1.1em;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    th,
    td {
        border: 1px solid #ddd;
        padding: 10px;
        text-align: left;
    }

    th {
        background-color: #0056b3;
        color: white;
    }

    tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    .btn-print {
        background-color: #007bff;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        font-size: 1em;
        cursor: pointer;
        margin-bottom: 20px;
        display: block;
        width: 100%;
        max-width: 200px;
        margin-left: auto;
        margin-right: auto;
        transition: background-color 0.3s;
    }

    .btn-print:hover {
        background-color: #0069d9;
    }

    @media print {
        .btn-print {
            display: none;
            /* Hide the print button during print */
        }

        body {
            margin: 0;
            padding: 10px;
        }

        table {
            box-shadow: none;
        }
    }
    </style>
    <script>
    function printReport() {
        window.print();
    }
    </script>
</head>

<body>
    <h1>Laporan Transaksi</h1>
    <p>Tanggal: <?= date('Y-m-d') ?></p>

    <button class="btn-print" onclick="printReport()">Cetak Laporan</button>

    <div class="summary">
        <strong>Ringkasan Transaksi:</strong><br>
        Total Transaksi: <?= $total_transactions ?><br>
        Total Jumlah: <?= number_format($total_amount, 2) ?> IDR<br>
        Total Uang Muka: <?= number_format($total_downpayment, 2) ?> IDR<br>
        Total Kekurangan: <?= number_format($total_outstanding, 2) ?> IDR<br>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID Transaksi</th>
                <th>NIK</th>
                <th>No. Polisi</th>
                <th>Tanggal Booking</th>
                <th>Tanggal Ambil</th>
                <th>Tanggal Kembali</th>
                <th>Supir</th>
                <th>Total</th>
                <th>Down Payment</th>
                <th>Kekurangan</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?= $row['id_transaksi'] ?></td>
                <td><?= $row['nik'] ?></td>
                <td><?= $row['nopol'] ?></td>
                <td><?= $row['tgl_booking'] ?></td>
                <td><?= $row['tgl_ambil'] ?></td>
                <td><?= $row['tgl_kembali'] ?></td>
                <td><?= $row['driver_needed'] ?></td>
                <td><?= number_format($row['total'], 2) ?> IDR</td>
                <td><?= number_format($row['downpayment'], 2) ?> IDR</td>
                <td><?= number_format($row['kekurangan'], 2) ?> IDR</td>
                <td><?= $row['status'] ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</body>

</html>
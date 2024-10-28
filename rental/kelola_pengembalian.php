<?php
session_start();

// Pastikan pengguna sudah login dan memiliki peran 'petugas'
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'petugas') {
    echo '<script>alert("Anda harus login sebagai Petugas untuk mengakses halaman ini."); location.href="login.php";</script>';
    exit();
}

// Menghubungkan ke database
include 'koneksi.php';

// Ambil data dari query string
$nopol = isset($_GET['nopol']) ? $_GET['nopol'] : '';
$nik = isset($_GET['nik']) ? $_GET['nik'] : '';

// Cek data transaksi dan pengembalian
$stmt = $conn->prepare("
    SELECT t.nopol, t.nik, t.tgl_kembali AS tgl_sewa, k.tgl_kembali, 
           k.kondisi_mobil, k.denda, k.biaya_tambahan, k.id_transaksi
    FROM tbl_kembali k
    JOIN tbl_transaksi t ON k.id_transaksi = t.id_transaksi
    WHERE t.nopol = ? AND t.nik = ? AND t.status = 'kembali'
");
$stmt->bind_param("ss", $nopol, $nik);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    echo '<script>alert("Data tidak ditemukan."); location.href="mobil_kembali.php";</script>';
    exit();
}

// Ambil nilai denda dari database langsung
$denda = $data['denda']; // Tidak ada perhitungan ulang, hanya ambil dari database

// Jika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validasi input
    $biaya_tambahan = htmlspecialchars($_POST['biaya_tambahan']);
    
    // Pastikan biaya tambahan adalah angka dan tidak negatif
    if (!is_numeric($biaya_tambahan) || $biaya_tambahan < 0) {
        echo '<script>alert("Biaya tambahan harus berupa angka non-negatif.");</script>';
    } else {
        $id_transaksi = $data['id_transaksi']; // Ambil id_transaksi

        // Update data denda dan biaya tambahan
        $update_query = "
            UPDATE tbl_kembali
            SET denda = ?, biaya_tambahan = ?
            WHERE id_transaksi = ?
        ";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("ddi", $denda, $biaya_tambahan, $id_transaksi);

        if ($update_stmt->execute()) {
            echo '<script>alert("Biaya tambahan dan denda berhasil disimpan."); location.href="mobil_kembali.php";</script>';
        } else {
            echo '<script>alert("Gagal menyimpan data: ' . mysqli_error($conn) . '");</script>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pengembalian</title>
    <link rel="stylesheet" href="styles.css">
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f6f9;
        margin: 0;
        padding: 0;
    }

    .container {
        width: 600px;
        margin: 50px auto;
        background-color: #fff;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    h1 {
        text-align: center;
        color: #333;
        font-size: 24px;
        margin-bottom: 20px;
    }

    label {
        display: block;
        color: #555;
        margin-bottom: 10px;
        font-weight: bold;
    }

    input[type="text"],
    input[type="number"],
    textarea {
        width: 100%;
        padding: 10px;
        margin-bottom: 20px;
        border: 1px solid #ddd;
        border-radius: 5px;
        box-sizing: border-box;
        font-size: 16px;
    }

    input[type="text"]:disabled,
    textarea:disabled {
        background-color: #f5f5f5;
        color: #888;
    }

    .btn {
        display: inline-block;
        width: 100%;
        background-color: #007BFF;
        color: #fff;
        padding: 12px 20px;
        border: none;
        border-radius: 5px;
        font-size: 16px;
        cursor: pointer;
        text-align: center;
        transition: background-color 0.3s;
    }

    .btn:hover {
        background-color: #0056b3;
    }
    </style>
</head>

<body>

    <div class="container">
        <h1>Kelola Pengembalian Mobil</h1>

        <form method="post">
            <div class="form-group">
                <label>Nomor Polisi (Nopol):</label>
                <input type="text" value="<?php echo htmlspecialchars($data['nopol']); ?>" disabled>
            </div>

            <div class="form-group">
                <label>NIK User:</label>
                <input type="text" value="<?php echo htmlspecialchars($data['nik']); ?>" disabled>
            </div>

            <div class="form-group">
                <label>Tanggal Kembali:</label>
                <input type="text" value="<?php echo htmlspecialchars($data['tgl_kembali']); ?>" disabled>
            </div>

            <div class="form-group">
                <label>Kondisi Mobil:</label>
                <textarea disabled><?php echo htmlspecialchars($data['kondisi_mobil']); ?></textarea>
            </div>

            <div class="form-group">
                <label>Denda:</label>
                <input type="text" value="Rp <?php echo number_format($denda, 2); ?>" disabled>
            </div>

            <div class="form-group">
                <label for="biaya_tambahan">Biaya Kerusakan / Tambahan:</label>
                <input type="number" id="biaya_tambahan" name="biaya_tambahan" required>
            </div>

            <button type="submit" class="btn">Simpan Biaya Tambahan dan Denda</button>
        </form>
    </div>

</body>

</html>

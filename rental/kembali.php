<?php
session_start();

// Pastikan pengguna sudah login dan memiliki peran 'user' atau 'petugas'
if (!isset($_SESSION['username']) || !in_array($_SESSION['role'], ['user', 'petugas'])) {
    echo '<script>alert("Anda harus login sebagai User atau Petugas untuk mengakses halaman ini."); location.href="login.php";</script>';
    exit();
}

// Cek apakah pengguna adalah 'user', jika ya, periksa NIK
if ($_SESSION['role'] === 'user' && !isset($_SESSION['nik'])) {
    echo '<script>alert("NIK tidak ditemukan. Silakan login kembali."); location.href="login.php";</script>';
    exit();
}

// Menghubungkan ke database
include 'koneksi.php';

// Mendapatkan 'nopol' dan 'nik' dari query string
$nopol = isset($_GET['nopol']) ? $_GET['nopol'] : '';
$nik = $_SESSION['nik'] ?? null; // Jika tidak ada nik, set null untuk petugas

// Mendapatkan tanggal hari ini
$today = date('Y-m-d');

// Ambil data transaksi untuk 'user' dengan NIK
if ($_SESSION['role'] === 'user') {
    $query = "
        SELECT t.tgl_kembali, t.total
        FROM tbl_transaksi t
        WHERE t.nopol = '$nopol' AND t.nik = '$nik' AND t.status = 'ambil'
    ";
} else {
    // Jika petugas, ambil transaksi berdasarkan 'nopol' saja (atau sesuai logika lain)
    $query = "
        SELECT t.tgl_kembali, t.total
        FROM tbl_transaksi t
        WHERE t.nopol = '$nopol' AND t.status = 'ambil'
    ";
}

$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);

if (!$row) {
    echo '<script>alert("Data transaksi tidak ditemukan."); location.href="user.php";</script>';
    exit();
}

// Mendapatkan tanggal kembali dan biaya transaksi
$tanggal_kembali = $row['tgl_kembali'];
$total_transaksi = $row['total'];

// Tentukan denda per hari
$denda_per_hari = 50000; // contoh denda Rp 50.000 per hari jika telat

// Cek apakah form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Mendapatkan data dari POST
    $tgl_kembali = $_POST['tgl_kembali'];
    $kondisi_mobil = $_POST['kondisi_mobil'];
    $denda = $_POST['denda'];

    // Simpan data pengembalian ke tabel tbl_kembali
    $insert_query = "
        INSERT INTO tbl_kembali (id_transaksi, tgl_kembali, kondisi_mobil, denda)
        VALUES ((SELECT id_transaksi FROM tbl_transaksi WHERE nopol = '$nopol' " . ($nik ? "AND nik = '$nik'" : "") . " AND status = 'ambil'), '$tgl_kembali', '$kondisi_mobil', '$denda')
    ";

    if (mysqli_query($conn, $insert_query)) {
        // Update status transaksi menjadi 'kembali'
        $update_query = "
            UPDATE tbl_transaksi
            SET status = 'kembali'
            WHERE nopol = '$nopol' " . ($nik ? "AND nik = '$nik'" : "") . " AND status = 'ambil'
        ";
        mysqli_query($conn, $update_query);

        // Tampilkan pesan sukses
        echo "<script>alert('Mobil berhasil dikembalikan.'); location.href='user.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengembalian Mobil</title>
    <link rel="stylesheet" href="styles.css"> <!-- Sesuaikan dengan file CSS Anda -->
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
    }

    .container {
        background-color: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        width: 400px;
        text-align: center;
    }

    h1 {
        margin-bottom: 20px;
        color: #333;
    }

    label {
        display: block;
        margin: 15px 0 5px;
        color: #555;
        text-align: left;
    }

    input[type="date"],
    textarea {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
        margin-bottom: 15px;
        box-sizing: border-box;
    }

    input[type="submit"] {
        background-color: #28a745;
        color: white;
        border: none;
        padding: 10px;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.3s;
        width: 100%;
    }

    input[type="submit"]:hover {
        background-color: #218838;
    }

    footer {
        margin-top: 20px;
        color: #777;
    }

    .info {
        margin: 10px 0;
    }

    .info strong {
        display: block;
        color: #333;
    }
    </style>
</head>

<body>
    <div class="container">
        <h1>Form Pengembalian Mobil</h1>

        <form action="kembali.php?nopol=<?php echo $nopol; ?>" method="post">
            <div class="info">
                <strong>Tanggal Kembali Seharusnya:</strong> <?php echo $tanggal_kembali; ?>
            </div>

            <label for="tgl_kembali">Tanggal Kembali:</label>
            <input type="date" id="tgl_kembali" name="tgl_kembali" required>

            <label for="kondisi_mobil">Kondisi Mobil:</label>
            <textarea id="kondisi_mobil" name="kondisi_mobil" rows="4" required></textarea>

            <div class="info">
                <strong>Denda Keterlambatan:</strong> Rp <span id="denda-display">0</span>
            </div>

            <input type="hidden" id="denda" name="denda" value="0">

            <input type="submit" value="Kembalikan Mobil">
        </form>
    </div>

    <script>
    document.getElementById('tgl_kembali').addEventListener('change', function() {
        // Tanggal kembali yang seharusnya
        const tglKembaliSeharusnya = new Date('<?php echo $tanggal_kembali; ?>');

        // Tanggal pengembalian yang dipilih
        const tglKembali = new Date(this.value);

        // Denda per hari
        const dendaPerHari = <?php echo $denda_per_hari; ?>;

        // Hitung perbedaan hari
        const diffTime = tglKembali - tglKembaliSeharusnya;
        const daysLate = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

        let denda = 0;
        if (daysLate > 0) {
            denda = daysLate * dendaPerHari;
        }

        // Tampilkan denda
        document.getElementById('denda-display').textContent = denda.toLocaleString('id-ID');

        // Set nilai denda di input hidden
        document.getElementById('denda').value = denda;
    });
    </script>
</body>

</html>
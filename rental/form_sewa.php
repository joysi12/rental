<?php
session_start();
include 'koneksi.php';

// Mengambil data nopol dari GET
$nopol = isset($_GET['nopol']) ? $_GET['nopol'] : '';
$nik = isset($_SESSION['nik']) ? $_SESSION['nik'] : '';

// Ambil harga sewa mobil berdasarkan nopol
$harga_sewa = 0;
if ($nopol) {
    $query = "SELECT harga FROM tbl_mobil WHERE nopol='$nopol'";
    $result = mysqli_query($conn, $query);
    if ($row = mysqli_fetch_assoc($result)) {
        $harga_sewa = $row['harga'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Sewa Mobil</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: #f3f4f6;
            color: #333;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 50px auto;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        h2 {
            margin-bottom: 20px;
        }
        input[type="text"],
        input[type="number"],
        input[type="date"],
        input[type="checkbox"] {
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
    </style>
</head>
<body>
    <div class="container">
        <h2>Form Sewa Mobil</h2>
        <form method="POST" action="proses_sewa.php">
            <input type="hidden" name="nik" value="<?php echo $nik; ?>">
            <input type="hidden" name="nopol" value="<?php echo $nopol; ?>">
            
            <label for="tgl_ambil">Tanggal Ambil:</label>
            <input type="date" id="tgl_ambil" name="tgl_ambil" required>
            
            <label for="tgl_kembali">Batas Tanggal Kembali:</label>
            <input type="date" id="tgl_kembali" name="tgl_kembali" required>
            
            <label>Supir (Tambah Rp 100.000/hari):</label>
            <input type="checkbox" id="supir" name="supir">

            <label>Total Biaya:</label>
            <input type="text" id="total" name="total" value="0" readonly>

            <label>Down Payment (DP):</label>
            <input type="number" id="dp" name="dp" value="0" required oninput="updateKekurangan()">

            <label>Kekurangan:</label>
            <input type="text" id="kekurangan" name="kekurangan" value="0" readonly>

            <button type="submit">Sewa Mobil</button>
        </form>
    </div>

    <script>
        const hargaSewa = <?php echo $harga_sewa; ?>; // Harga sewa per hari
        const supirBiaya = 100000; // Biaya supir per hari
        const dpInput = document.getElementById('dp');
        const totalInput = document.getElementById('total');
        const kekuranganInput = document.getElementById('kekurangan');
        const tglAmbilInput = document.getElementById('tgl_ambil');
        const tglKembaliInput = document.getElementById('tgl_kembali');
        const supirCheckbox = document.getElementById('supir');

        function updateTotal() {
            const tglAmbil = new Date(tglAmbilInput.value);
            const tglKembali = new Date(tglKembaliInput.value);
            if (tglAmbil && tglKembali) {
                const selisihHari = Math.ceil((tglKembali - tglAmbil) / (1000 * 60 * 60 * 24));
                if (selisihHari > 0) {
                    let total = hargaSewa * selisihHari;
                    if (supirCheckbox.checked) {
                        total += supirBiaya * selisihHari; // Tambah biaya supir jika dicentang
                    }
                    totalInput.value = total; // Update total
                    updateKekurangan(); // Update kekurangan
                } else {
                    totalInput.value = 0;
                }
            } else {
                totalInput.value = 0;
            }
        }

        function updateKekurangan() {
            const total = parseFloat(totalInput.value) || 0;
            const dp = parseFloat(dpInput.value) || 0;
            const kekurangan = total - dp;
            kekuranganInput.value = kekurangan < 0 ? 0 : kekurangan; // Kekurangan tidak boleh negatif
        }

        tglAmbilInput.addEventListener('change', updateTotal);
        tglKembaliInput.addEventListener('change', updateTotal);
        supirCheckbox.addEventListener('change', updateTotal);
    </script>
</body>
</html>

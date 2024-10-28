<?php
$host = "localhost"; // Server database (biasanya localhost)
$username = "root"; // Username database Anda
$password = ""; // Password database Anda
$database = "rental"; // Nama database yang digunakan

// Membuat koneksi ke database
$conn = new mysqli($host, $username, $password, $database);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi ke database gagal: " . $conn->connect_error);
}
?>

<?php
// File: config/database.php
// Fungsi: Menghubungkan PHP dengan database MySQL

// Informasi database
$host = 'localhost';        // Server database (localhost karena di komputer sendiri)
$username = 'root';         // Username MySQL (default: root)
$password = '';             // Password MySQL (default: kosong)
$database = 'sipb-upnvj'; // Nama database yang tadi dibuat

// Membuat koneksi
$conn = mysqli_connect($host, $username, $password, $database);

// Cek apakah koneksi berhasil
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Set charset agar tidak error karakter Indonesia
mysqli_set_charset($conn, "utf8");

// JANGAN TUTUP KONEKSI DI SINI, nanti ditutup di masing-masing file
?>

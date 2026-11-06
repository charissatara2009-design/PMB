<?php // Awal kode PHP

$host = "localhost"; // Alamat server database
$user = "root"; // Username database
$password = ""; // Password database (kosong)
$database = "pmb_online"; // Nama database yang dipakai

$conn = mysqli_connect($host, $user, $password, $database); // Buat koneksi ke database MySQL

if (!$conn) { // Kalau koneksi gagal
    die("Koneksi database gagal: " . mysqli_connect_error()); // Hentikan halaman dan tampilkan pesan error koneksi
} // Akhir if

mysqli_set_charset($conn, "utf8mb4"); // Set encoding karakter utf8mb4 (mendukung emoji)
// Akhir kode PHP ?>
<?php // Awal kode PHP

if (session_status() === PHP_SESSION_NONE) { // Kalau sesi belum dimulai
    session_start(); // Mulai sesi (tempat menyimpan data login)
} // Akhir if

function cekLogin() // Fungsi: cek apakah user sudah login
{ // Awal isi fungsi
    if (!isset($_SESSION['user_id'])) { // Kalau data user_id belum ada di sesi (belum login)
        header("Location: ../index.php"); // Arahkan ke halaman login
        exit; // Hentikan proses halaman
    } // Akhir if
} // Akhir fungsi cekLogin

function cekAdmin() // Fungsi: khusus halaman admin
{ // Awal isi fungsi
    cekLogin(); // Pastikan sudah login dulu

    if ($_SESSION['role'] !== 'admin') { // Kalau role di sesi bukan admin
        session_unset(); // Hapus semua data sesi
        session_destroy(); // Hancurkan sesi (otomatis logout)
        header("Location: ../index.php"); // Arahkan ke halaman login
        exit; // Hentikan proses halaman
    } // Akhir if
} // Akhir fungsi cekAdmin

function cekUser() // Fungsi: khusus halaman user (calon maba)
{ // Awal isi fungsi
    cekLogin(); // Pastikan sudah login dulu

    if ($_SESSION['role'] !== 'user') { // Kalau role di sesi bukan user
        session_unset(); // Hapus semua data sesi
        session_destroy(); // Hancurkan sesi (otomatis logout)
        header("Location: ../index.php"); // Arahkan ke halaman login
        exit; // Hentikan proses halaman
    } // Akhir if
} // Akhir fungsi cekUser
// Akhir kode PHP ?>
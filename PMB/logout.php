<?php // Awal blok kode PHP

session_start(); // Mulai sesi (tempat menyimpan data login)

// Hapus semua data session
session_unset(); // Hapus semua data di dalam sesi

// Hancurkan session
session_destroy(); // Hancurkan sesi (logout)

// Kembali ke halaman login
header("Location: index.php"); // Arahkan ke halaman index.php
exit; // Hentikan proses halaman di sini

 // Akhir blok kode PHP ?>
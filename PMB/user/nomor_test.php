<?php // Awal blok kode PHP

require_once "../config/auth.php"; // Muat file pengecekan login (auth)
require_once "../config/database.php"; // Muat file koneksi database

cekUser(); // Pastikan user sudah login, kalau belum diarahkan keluar

$user_id = $_SESSION['user_id']; // Ambil ID user yang sedang login dari session

// Query SQL: ambil data dari tabel users milik user yang login
$query = "SELECT nama, nomor_test, prodi, status_test
          FROM users
          WHERE id = ?";

$stmt = mysqli_prepare($conn, $query); // Siapkan query (prepared statement) agar aman dari SQL injection

mysqli_stmt_bind_param( // Isi tanda tanya (?) pada query dengan nilai yang aman
    $stmt, // Statement query yang sudah disiapkan
    "i", // Tipe data tiap nilai (i = angka bulat, d = desimal, s = teks)
    $user_id // ID user yang sedang login
); // Akhir pemanggilan fungsi

mysqli_stmt_execute($stmt); // Jalankan query

$result = mysqli_stmt_get_result($stmt); // Ambil hasil query

$user = mysqli_fetch_assoc($result); // Ambil satu baris hasil query dalam bentuk array

 // Akhir blok kode PHP ?>

<!DOCTYPE html> <!-- Deklarasi tipe dokumen HTML5 -->
<html lang="id"> <!-- Tag akar halaman, bahasa Indonesia -->

<head> <!-- Awal bagian head (pengaturan halaman) -->

    <meta charset="UTF-8"> <!-- Set encoding karakter UTF-8 -->

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    > <!-- Agar tampilan menyesuaikan lebar layar HP -->

    <title>Nomor Test - Universitas NaraDipa</title> <!-- Judul yang tampil di tab browser berisi "Nomor Test - Universitas NaraDipa" -->

    <link
        rel="stylesheet"
        href="../css/style.css"
    > <!-- Memuat file CSS utama -->

</head> <!-- Tutup bagian head -->

<body> <!-- Awal isi halaman yang tampil -->

<div class="dashboard"> <!-- Wadah: pembungkus layout dashboard -->


    <!-- SIDEBAR -->

    <aside class="sidebar"> <!-- Sidebar: menu samping -->

        <div class="sidebar-logo"> <!-- Wadah: logo di sidebar -->

            <div class="logo-icon"> <!-- Wadah: ikon logo -->
                🎓 <!-- Ikon emoji penghias -->
            </div> <!-- Tutup ikon logo -->

            <div> <!-- Wadah -->

                <h2>Universitas NaraDipa</h2> <!-- Judul bagian berisi "Universitas NaraDipa" -->

                <span> <!-- Teks keterangan -->
                    Calon Mahasiswa <!-- Teks keterangan -->
                </span> <!-- Tutup teks keterangan -->

            </div> <!-- Tutup wadah -->

        </div> <!-- Tutup logo di sidebar -->


        <nav class="sidebar-menu"> <!-- Navigasi: daftar menu navigasi -->

            <a href="dashboard.php"> <!-- Link ke Dashboard -->

                🏠 <!-- Ikon emoji penghias -->

                <span> <!-- Teks keterangan -->
                    Dashboard <!-- Teks keterangan -->
                </span> <!-- Tutup teks keterangan -->

            </a> <!-- Tutup link Dashboard -->


            <a
                href="nomor_test.php"
                class="active"
            > <!-- Link menu Nomor Test (menu aktif) -->

                📝 <!-- Ikon emoji penghias -->

                <span> <!-- Teks keterangan -->
                    Nomor Test <!-- Teks keterangan -->
                </span> <!-- Tutup teks keterangan -->

            </a> <!-- Tutup link Nomor Test -->


            <a href="hasil_test.php"> <!-- Link ke Hasil Test -->

                📊 <!-- Ikon emoji penghias -->

                <span> <!-- Teks keterangan -->
                    Hasil Test <!-- Teks keterangan -->
                </span> <!-- Tutup teks keterangan -->

            </a> <!-- Tutup link Hasil Test -->


            <a href="daftar_ulang.php"> <!-- Link ke Daftar Ulang -->

                📋 <!-- Ikon emoji penghias -->

                <span> <!-- Teks keterangan -->
                    Daftar Ulang <!-- Teks keterangan -->
                </span> <!-- Tutup teks keterangan -->

            </a> <!-- Tutup link Daftar Ulang -->


            <a href="kartu_mahasiswa.php"> <!-- Link ke Kartu Mahasiswa -->

                🪪 <!-- Ikon emoji penghias -->

                <span> <!-- Teks keterangan -->
                    Kartu Mahasiswa <!-- Teks keterangan -->
                </span> <!-- Tutup teks keterangan -->

            </a> <!-- Tutup link Kartu Mahasiswa -->


            <a href="nomor_induk.php"> <!-- Link ke Nomor Induk -->

                🎓 <!-- Ikon emoji penghias -->

                <span> <!-- Teks keterangan -->
                    Nomor Induk <!-- Teks keterangan -->
                </span> <!-- Tutup teks keterangan -->

            </a> <!-- Tutup link Nomor Induk -->

        </nav> <!-- Tutup daftar menu navigasi -->


        <div class="sidebar-bottom"> <!-- Wadah: bagian bawah sidebar -->

            <a
                href="../logout.php"
                class="logout"
            > <!-- Link logout -->

                🚪 <!-- Ikon emoji penghias -->

                <span> <!-- Teks keterangan -->
                    Logout <!-- Teks keterangan -->
                </span> <!-- Tutup teks keterangan -->

            </a> <!-- Tutup link logout -->

        </div> <!-- Tutup bagian bawah sidebar -->

    </aside> <!-- Tutup menu samping -->


    <!-- MAIN CONTENT -->

    <main class="main-content"> <!-- Konten utama halaman -->


        <!-- HEADER -->

        <header class="dashboard-header"> <!-- Header halaman -->

            <div> <!-- Wadah -->

                <h1> <!-- Judul utama -->
                    Nomor Test <!-- Teks judul halaman -->
                </h1> <!-- Tutup judul utama -->

                <p> <!-- Paragraf -->
                    Nomor test calon mahasiswa baru <!-- Teks paragraf -->
                </p> <!-- Tutup paragraf -->

            </div> <!-- Tutup wadah -->


            <div class="user-info"> <!-- Wadah: info user di header -->

                <div class="user-avatar"> <!-- Wadah: avatar user -->
                    👤 <!-- Ikon emoji penghias -->
                </div> <!-- Tutup avatar user -->

                <div> <!-- Wadah -->

                    <strong> <!-- Teks tebal -->
                        <?= htmlspecialchars($user['nama']);  // Tampilkan nama lengkap (di-escape agar aman dari XSS) ?>
                    </strong> <!-- Tutup teks tebal -->

                    <span> <!-- Teks keterangan -->
                        Calon Mahasiswa <!-- Teks keterangan -->
                    </span> <!-- Tutup teks keterangan -->

                </div> <!-- Tutup wadah -->

            </div> <!-- Tutup info user di header -->

        </header> <!-- Tutup header halaman -->


        <!-- NOMOR TEST -->

        <section class="test-number-card"> <!-- Bagian: kartu nomor test -->

            <div class="test-icon"> <!-- Wadah: ikon nomor test -->
                📝 <!-- Ikon emoji penghias -->
            </div> <!-- Tutup ikon nomor test -->


            <p class="test-label"> <!-- Paragraf: label nomor test -->
                NOMOR TEST ANDA <!-- Teks paragraf -->
            </p> <!-- Tutup label nomor test -->


            <h2> <!-- Judul bagian -->
                <?= htmlspecialchars($user['nomor_test'] ?? '-');  // Tampilkan nomor test (di-escape agar aman dari XSS), tampil '-' jika kosong ?>
            </h2> <!-- Tutup judul bagian -->


            <p class="test-info"> <!-- Paragraf: info nomor test -->
                Gunakan nomor test ini saat mengikuti <!-- Teks paragraf -->
                proses seleksi mahasiswa baru. <!-- Teks paragraf -->
            </p> <!-- Tutup info nomor test -->

        </section> <!-- Tutup kartu nomor test -->


        <!-- DATA TEST -->

        <section class="content-card"> <!-- Bagian: kartu konten -->

            <div class="section-title"> <!-- Wadah: judul bagian -->

                <div> <!-- Wadah -->

                    <h2> <!-- Judul bagian -->
                        Informasi Pendaftaran <!-- Teks judul bagian -->
                    </h2> <!-- Tutup judul bagian -->

                    <p> <!-- Paragraf -->
                        Data yang digunakan untuk proses seleksi <!-- Teks paragraf -->
                    </p> <!-- Tutup paragraf -->

                </div> <!-- Tutup wadah -->

            </div> <!-- Tutup judul bagian -->


            <div class="data-grid"> <!-- Wadah: grid data -->

                <div class="data-item"> <!-- Wadah: satu item data -->

                    <span> <!-- Teks keterangan -->
                        Nama Lengkap <!-- Teks keterangan -->
                    </span> <!-- Tutup teks keterangan -->

                    <strong> <!-- Teks tebal -->
                        <?= htmlspecialchars($user['nama']);  // Tampilkan nama lengkap (di-escape agar aman dari XSS) ?>
                    </strong> <!-- Tutup teks tebal -->

                </div> <!-- Tutup satu item data -->


                <div class="data-item"> <!-- Wadah: satu item data -->

                    <span> <!-- Teks keterangan -->
                        Program Studi <!-- Teks keterangan -->
                    </span> <!-- Tutup teks keterangan -->

                    <strong> <!-- Teks tebal -->
                        <?= htmlspecialchars($user['prodi'] ?? '-');  // Tampilkan program studi (di-escape agar aman dari XSS), tampil '-' jika kosong ?>
                    </strong> <!-- Tutup teks tebal -->

                </div> <!-- Tutup satu item data -->


                <div class="data-item"> <!-- Wadah: satu item data -->

                    <span> <!-- Teks keterangan -->
                        Nomor Test <!-- Teks keterangan -->
                    </span> <!-- Tutup teks keterangan -->

                    <strong> <!-- Teks tebal -->
                        <?= htmlspecialchars($user['nomor_test'] ?? '-');  // Tampilkan nomor test (di-escape agar aman dari XSS), tampil '-' jika kosong ?>
                    </strong> <!-- Tutup teks tebal -->

                </div> <!-- Tutup satu item data -->


                <div class="data-item"> <!-- Wadah: satu item data -->

                    <span> <!-- Teks keterangan -->
                        Status Test <!-- Teks keterangan -->
                    </span> <!-- Tutup teks keterangan -->

                    <strong> <!-- Teks tebal -->
                        <?= htmlspecialchars($user['status_test']);  // Tampilkan status test (di-escape agar aman dari XSS) ?>
                    </strong> <!-- Tutup teks tebal -->

                </div> <!-- Tutup satu item data -->

            </div> <!-- Tutup grid data -->

        </section> <!-- Tutup kartu konten -->


        <!-- PETUNJUK -->

        <section class="content-card"> <!-- Bagian: kartu konten -->

            <div class="section-title"> <!-- Wadah: judul bagian -->

                <div> <!-- Wadah -->

                    <h2> <!-- Judul bagian -->
                        Informasi <!-- Teks judul bagian -->
                    </h2> <!-- Tutup judul bagian -->

                </div> <!-- Tutup wadah -->

            </div> <!-- Tutup judul bagian -->


            <div class="info-box"> <!-- Wadah: kotak informasi -->

                <p> <!-- Paragraf -->
                    Pastikan kamu menyimpan nomor test <!-- Teks paragraf -->
                    dengan baik karena nomor ini digunakan <!-- Teks paragraf -->
                    dalam proses seleksi PMB. <!-- Teks paragraf -->
                </p> <!-- Tutup paragraf -->

            </div> <!-- Tutup kotak informasi -->

        </section> <!-- Tutup kartu konten -->

    </main> <!-- Tutup konten utama halaman -->

</div> <!-- Tutup pembungkus layout dashboard -->

</body> <!-- Tutup isi halaman (body) -->

</html> <!-- Tutup tag html -->
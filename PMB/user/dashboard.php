<?php // Awal blok kode PHP

require_once "../config/auth.php"; // Muat file pengecekan login (auth)
require_once "../config/database.php"; // Muat file koneksi database

cekUser(); // Pastikan user sudah login, kalau belum diarahkan keluar

$user_id = $_SESSION['user_id']; // Ambil ID user yang sedang login dari session

$query = "SELECT * FROM users WHERE id = ?"; // Query SQL: ambil semua data user yang sedang login

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

    <title>Dashboard User - Universitas NaraDipa</title> <!-- Judul yang tampil di tab browser berisi "Dashboard User - Universitas NaraDipa" -->

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
                <span>Calon Mahasiswa</span> <!-- Teks keterangan berisi "Calon Mahasiswa" -->
            </div> <!-- Tutup wadah -->

        </div> <!-- Tutup logo di sidebar -->


        <nav class="sidebar-menu"> <!-- Navigasi: daftar menu navigasi -->

            <a
                href="dashboard.php"
                class="active"
            > <!-- Link menu Dashboard (menu aktif) -->
                🏠 <!-- Ikon emoji penghias -->
                <span>Dashboard</span> <!-- Teks keterangan berisi "Dashboard" -->
            </a> <!-- Tutup link Dashboard -->


            <a href="nomor_test.php"> <!-- Link ke Nomor Test -->

                📝 <!-- Ikon emoji penghias -->
                <span>Nomor Test</span> <!-- Teks keterangan berisi "Nomor Test" -->

            </a> <!-- Tutup link Nomor Test -->


            <a href="test.php"> <!-- Link ke Test PMB -->

                📖 <!-- Ikon emoji penghias -->
                <span>Test PMB</span> <!-- Teks keterangan berisi "Test PMB" -->

            </a> <!-- Tutup link Test PMB -->


            <a href="hasil_test.php"> <!-- Link ke Hasil Test -->

                📊 <!-- Ikon emoji penghias -->
                <span>Hasil Test</span> <!-- Teks keterangan berisi "Hasil Test" -->

            </a> <!-- Tutup link Hasil Test -->


            <a href="daftar_ulang.php"> <!-- Link ke Daftar Ulang -->

                📋 <!-- Ikon emoji penghias -->
                <span>Daftar Ulang</span> <!-- Teks keterangan berisi "Daftar Ulang" -->

            </a> <!-- Tutup link Daftar Ulang -->


            <a href="kartu_mahasiswa.php"> <!-- Link ke Kartu Mahasiswa -->

                🪪 <!-- Ikon emoji penghias -->
                <span>Kartu Mahasiswa</span> <!-- Teks keterangan berisi "Kartu Mahasiswa" -->

            </a> <!-- Tutup link Kartu Mahasiswa -->


            <a href="nomor_induk.php"> <!-- Link ke Nomor Induk -->

                🎓 <!-- Ikon emoji penghias -->
                <span>Nomor Induk</span> <!-- Teks keterangan berisi "Nomor Induk" -->

            </a> <!-- Tutup link Nomor Induk -->

        </nav> <!-- Tutup daftar menu navigasi -->


        <div class="sidebar-bottom"> <!-- Wadah: bagian bawah sidebar -->

            <a
                href="../logout.php"
                class="logout"
            > <!-- Link logout -->

                🚪 <!-- Ikon emoji penghias -->
                <span>Logout</span> <!-- Teks keterangan berisi "Logout" -->

            </a> <!-- Tutup link logout -->

        </div> <!-- Tutup bagian bawah sidebar -->

    </aside> <!-- Tutup menu samping -->


    <!-- MAIN CONTENT -->

    <main class="main-content"> <!-- Konten utama halaman -->


        <!-- HEADER -->

        <header class="dashboard-header"> <!-- Header halaman -->

            <div> <!-- Wadah -->

                <h1> <!-- Judul utama -->
                    Dashboard <!-- Teks judul halaman -->
                </h1> <!-- Tutup judul utama -->

                <p> <!-- Paragraf -->
                    Selamat datang di Universitas NaraDipa <!-- Teks paragraf -->
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


        <!-- WELCOME -->

        <section class="welcome-card"> <!-- Bagian: kartu sambutan -->

            <div> <!-- Wadah -->

                <h2> <!-- Judul bagian -->
                    Halo, <?= htmlspecialchars($user['nama']); ?>! 👋 <!-- Tampilkan nama lengkap (di-escape agar aman dari XSS) setelah teks "Halo," -->
                </h2> <!-- Tutup judul bagian -->

                <p> <!-- Paragraf -->
                    Selamat datang di halaman pendaftaran <!-- Teks paragraf -->
                    mahasiswa baru. <!-- Teks paragraf -->
                </p> <!-- Tutup paragraf -->

            </div> <!-- Tutup wadah -->

            <div class="welcome-icon"> <!-- Wadah: ikon sambutan -->
                🎓 <!-- Ikon emoji penghias -->
            </div> <!-- Tutup ikon sambutan -->

        </section> <!-- Tutup kartu sambutan -->


        <!-- STATUS -->

        <section class="dashboard-cards"> <!-- Bagian: deretan kartu status -->


            <!-- NOMOR TEST -->

            <div class="dashboard-card"> <!-- Wadah: kartu status -->

                <div class="card-icon"> <!-- Wadah: ikon kartu -->
                    📝 <!-- Ikon emoji penghias -->
                </div> <!-- Tutup ikon kartu -->

                <div> <!-- Wadah -->

                    <span> <!-- Teks keterangan -->
                        Nomor Test <!-- Teks keterangan -->
                    </span> <!-- Tutup teks keterangan -->

                    <div style="display:flex; align-items:center; gap:6px;"> <!-- Wadah (ada style inline) -->

                        <h3 id="nomor-test-value"> <!-- Sub-judul -->
                            <?= htmlspecialchars($user['nomor_test'] ?? '-');  // Tampilkan nomor test (di-escape agar aman dari XSS), tampil '-' jika kosong ?>
                        </h3> <!-- Tutup sub-judul -->

                        <?php if (!empty($user['nomor_test'])):  // Tampilkan tombol salin hanya jika nomor test ada ?>

                            <button
                                type="button"
                                onclick="salinNomorTest()"
                                title="Salin nomor test"
                                style="background:none; border:none; cursor:pointer; font-size:16px; padding:0; line-height:1;"
                            > <!-- Tombol untuk menyalin nomor test -->
                                📋 <!-- Ikon emoji penghias -->
                            </button> <!-- Tutup tombol -->

                        <?php endif;  // Akhir blok if ?>

                    </div> <!-- Tutup wadah -->

                </div> <!-- Tutup wadah -->

            </div> <!-- Tutup kartu status -->


            <!-- STATUS TEST -->

            <div class="dashboard-card"> <!-- Wadah: kartu status -->

                <div class="card-icon"> <!-- Wadah: ikon kartu -->
                    📊 <!-- Ikon emoji penghias -->
                </div> <!-- Tutup ikon kartu -->

                <div> <!-- Wadah -->

                    <span> <!-- Teks keterangan -->
                        Status Test <!-- Teks keterangan -->
                    </span> <!-- Tutup teks keterangan -->

                    <h3> <!-- Sub-judul -->
                        <?= htmlspecialchars($user['status_test']);  // Tampilkan status test (di-escape agar aman dari XSS) ?>
                    </h3> <!-- Tutup sub-judul -->

                </div> <!-- Tutup wadah -->

            </div> <!-- Tutup kartu status -->


            <!-- DAFTAR ULANG -->

            <div class="dashboard-card"> <!-- Wadah: kartu status -->

                <div class="card-icon"> <!-- Wadah: ikon kartu -->
                    📋 <!-- Ikon emoji penghias -->
                </div> <!-- Tutup ikon kartu -->

                <div> <!-- Wadah -->

                    <span> <!-- Teks keterangan -->
                        Daftar Ulang <!-- Teks keterangan -->
                    </span> <!-- Tutup teks keterangan -->

                    <h3> <!-- Sub-judul -->
                        <?= htmlspecialchars($user['status_daftar_ulang']);  // Tampilkan status daftar ulang (di-escape agar aman dari XSS) ?>
                    </h3> <!-- Tutup sub-judul -->

                </div> <!-- Tutup wadah -->

            </div> <!-- Tutup kartu status -->


            <!-- NIM -->

            <div class="dashboard-card"> <!-- Wadah: kartu status -->

                <div class="card-icon"> <!-- Wadah: ikon kartu -->
                    🎓 <!-- Ikon emoji penghias -->
                </div> <!-- Tutup ikon kartu -->

                <div> <!-- Wadah -->

                    <span> <!-- Teks keterangan -->
                        Nomor Induk Mahasiswa <!-- Teks keterangan -->
                    </span> <!-- Tutup teks keterangan -->

                    <h3> <!-- Sub-judul -->
                        <?= htmlspecialchars($user['nim'] ?? '-');  // Tampilkan NIM (di-escape agar aman dari XSS), tampil '-' jika kosong ?>
                    </h3> <!-- Tutup sub-judul -->

                </div> <!-- Tutup wadah -->

            </div> <!-- Tutup kartu status -->

        </section> <!-- Tutup deretan kartu status -->


        <!-- DATA PENDAFTAR -->

        <section class="content-card"> <!-- Bagian: kartu konten -->

            <div class="section-title"> <!-- Wadah: judul bagian -->

                <div> <!-- Wadah -->

                    <h2> <!-- Judul bagian -->
                        Data Pendaftaran <!-- Teks judul bagian -->
                    </h2> <!-- Tutup judul bagian -->

                    <p> <!-- Paragraf -->
                        Informasi data calon mahasiswa <!-- Teks paragraf -->
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
                        Email <!-- Teks keterangan -->
                    </span> <!-- Tutup teks keterangan -->

                    <strong> <!-- Teks tebal -->
                        <?= htmlspecialchars($user['email']);  // Tampilkan email (di-escape agar aman dari XSS) ?>
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
                        Username <!-- Teks keterangan -->
                    </span> <!-- Tutup teks keterangan -->

                    <strong> <!-- Teks tebal -->
                        <?= htmlspecialchars($user['username']);  // Tampilkan username (di-escape agar aman dari XSS) ?>
                    </strong> <!-- Tutup teks tebal -->

                </div> <!-- Tutup satu item data -->

            </div> <!-- Tutup grid data -->

        </section> <!-- Tutup kartu konten -->


        <!-- QUICK MENU -->

        <section class="content-card"> <!-- Bagian: kartu konten -->

            <div class="section-title"> <!-- Wadah: judul bagian -->

                <div> <!-- Wadah -->

                    <h2> <!-- Judul bagian -->
                        Menu Pendaftaran <!-- Teks judul bagian -->
                    </h2> <!-- Tutup judul bagian -->

                    <p> <!-- Paragraf -->
                        Akses fitur PMB dengan mudah <!-- Teks paragraf -->
                    </p> <!-- Tutup paragraf -->

                </div> <!-- Tutup wadah -->

            </div> <!-- Tutup judul bagian -->


            <div class="quick-menu"> <!-- Wadah: menu cepat -->


                <a href="test.php"> <!-- Link ke Test PMB -->

                    <div class="quick-icon"> <!-- Wadah: ikon menu cepat -->
                        📖 <!-- Ikon emoji penghias -->
                    </div> <!-- Tutup ikon menu cepat -->

                    <div> <!-- Wadah -->

                        <strong> <!-- Teks tebal -->
                            Test PMB <!-- Teks tebal -->
                        </strong> <!-- Tutup teks tebal -->

                        <span> <!-- Teks keterangan -->
                            Kerjakan soal sesuai prodi <!-- Teks keterangan -->
                        </span> <!-- Tutup teks keterangan -->

                    </div> <!-- Tutup wadah -->

                </a> <!-- Tutup link Test PMB -->


                <a href="nomor_test.php"> <!-- Link ke Nomor Test -->

                    <div class="quick-icon"> <!-- Wadah: ikon menu cepat -->
                        📝 <!-- Ikon emoji penghias -->
                    </div> <!-- Tutup ikon menu cepat -->

                    <div> <!-- Wadah -->

                        <strong> <!-- Teks tebal -->
                            Nomor Test <!-- Teks tebal -->
                        </strong> <!-- Tutup teks tebal -->

                        <span> <!-- Teks keterangan -->
                            Lihat nomor test <!-- Teks keterangan -->
                        </span> <!-- Tutup teks keterangan -->

                    </div> <!-- Tutup wadah -->

                </a> <!-- Tutup link Nomor Test -->


                <a href="hasil_test.php"> <!-- Link ke Hasil Test -->

                    <div class="quick-icon"> <!-- Wadah: ikon menu cepat -->
                        📊 <!-- Ikon emoji penghias -->
                    </div> <!-- Tutup ikon menu cepat -->

                    <div> <!-- Wadah -->

                        <strong> <!-- Teks tebal -->
                            Hasil Test <!-- Teks tebal -->
                        </strong> <!-- Tutup teks tebal -->

                        <span> <!-- Teks keterangan -->
                            Lihat hasil test <!-- Teks keterangan -->
                        </span> <!-- Tutup teks keterangan -->

                    </div> <!-- Tutup wadah -->

                </a> <!-- Tutup link Hasil Test -->


                <a href="daftar_ulang.php"> <!-- Link ke Daftar Ulang -->

                    <div class="quick-icon"> <!-- Wadah: ikon menu cepat -->
                        📋 <!-- Ikon emoji penghias -->
                    </div> <!-- Tutup ikon menu cepat -->

                    <div> <!-- Wadah -->

                        <strong> <!-- Teks tebal -->
                            Daftar Ulang <!-- Teks tebal -->
                        </strong> <!-- Tutup teks tebal -->

                        <span> <!-- Teks keterangan -->
                            Melakukan daftar ulang <!-- Teks keterangan -->
                        </span> <!-- Tutup teks keterangan -->

                    </div> <!-- Tutup wadah -->

                </a> <!-- Tutup link Daftar Ulang -->


                <a href="nomor_induk.php"> <!-- Link ke Nomor Induk -->

                    <div class="quick-icon"> <!-- Wadah: ikon menu cepat -->
                        🎓 <!-- Ikon emoji penghias -->
                    </div> <!-- Tutup ikon menu cepat -->

                    <div> <!-- Wadah -->

                        <strong> <!-- Teks tebal -->
                            Nomor Induk <!-- Teks tebal -->
                        </strong> <!-- Tutup teks tebal -->

                        <span> <!-- Teks keterangan -->
                            Lihat nomor induk <!-- Teks keterangan -->
                        </span> <!-- Tutup teks keterangan -->

                    </div> <!-- Tutup wadah -->

                </a> <!-- Tutup link Nomor Induk -->

            </div> <!-- Tutup menu cepat -->

        </section> <!-- Tutup kartu konten -->

    </main> <!-- Tutup konten utama halaman -->

</div> <!-- Tutup pembungkus layout dashboard -->

<script> // Awal blok JavaScript

function salinNomorTest() // Fungsi: salin nomor test ke clipboard
{ // Awal blok kode
    const teks = document.getElementById('nomor-test-value').innerText.trim(); // Ambil teks nomor test dari halaman

    if (navigator.clipboard && navigator.clipboard.writeText) { // Kalau browser mendukung clipboard modern

        navigator.clipboard.writeText(teks).then(function () { // Salin teks, lalu kalau berhasil:
            alert('Nomor test berhasil disalin: ' + teks); // Beri tahu bahwa nomor berhasil disalin
        }).catch(function () { // Kalau gagal menyalin:
            salinFallback(teks); // Pakai cara salin cadangan
        }); // Akhir fungsi callback

    } else { // Kalau browser tidak mendukung:

        salinFallback(teks); // Pakai cara salin cadangan

    } // Akhir blok kode
} // Akhir blok kode

function salinFallback(teks) // Fungsi: cara salin cadangan untuk browser lama
{ // Awal blok kode
    const input = document.createElement('textarea'); // Buat kotak teks sementara
    input.value = teks; // Isi dengan teks yang mau disalin
    document.body.appendChild(input); // Pasang ke halaman
    input.select(); // Blok seluruh teksnya
    document.execCommand('copy'); // Perintah salin
    document.body.removeChild(input); // Hapus kotak teks sementara
    alert('Nomor test berhasil disalin: ' + teks); // Beri tahu bahwa nomor berhasil disalin
} // Akhir blok kode

</script> <!-- Tutup blok JavaScript -->

</body> <!-- Tutup isi halaman (body) -->

</html> <!-- Tutup tag html -->
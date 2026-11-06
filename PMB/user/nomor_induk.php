<?php // Awal blok kode PHP

require_once "../config/auth.php"; // Muat file pengecekan login (auth)
require_once "../config/database.php"; // Muat file koneksi database

cekUser(); // Pastikan user sudah login, kalau belum diarahkan keluar

$user_id = $_SESSION['user_id']; // Ambil ID user yang sedang login dari session


// ==================================================
// AMBIL DATA USER
// ==================================================

// Query SQL: ambil data dari tabel users digabung dengan tabel daftar_ulang milik user yang login, ambil yang paling baru saja
$query = "SELECT
              users.nama,
              users.nomor_test,
              users.prodi,
              users.status_test,
              users.status_daftar_ulang,
              users.nim,

              daftar_ulang.status AS status_daftar_ulang_detail

          FROM users

          LEFT JOIN daftar_ulang
          ON users.id = daftar_ulang.user_id

          WHERE users.id = ?

          ORDER BY daftar_ulang.id DESC

          LIMIT 1";


$stmt = mysqli_prepare( // Siapkan query (prepared statement) agar aman dari SQL injection
    $conn, // Koneksi database
    $query // Teks query SQL yang akan dijalankan
); // Akhir pemanggilan fungsi

mysqli_stmt_bind_param( // Isi tanda tanya (?) pada query dengan nilai yang aman
    $stmt, // Statement query yang sudah disiapkan
    "i", // Tipe data tiap nilai (i = angka bulat, d = desimal, s = teks)
    $user_id // ID user yang sedang login
); // Akhir pemanggilan fungsi

mysqli_stmt_execute( // Jalankan query
    $stmt // Statement query yang sudah disiapkan
); // Akhir pemanggilan fungsi

$result = mysqli_stmt_get_result( // Ambil hasil query
    $stmt // Statement query yang sudah disiapkan
); // Akhir pemanggilan fungsi

$user = mysqli_fetch_assoc( // Ambil satu baris hasil query dalam bentuk array
    $result // Hasil query
); // Akhir pemanggilan fungsi


// ==================================================
// CEK DATA USER
// ==================================================

if (!$user) { // Kalau data user tidak ditemukan

    // Tampilkan alert "Data user tidak ditemukan!" lalu pindah ke dashboard.php
    echo "<script>
            alert('Data user tidak ditemukan!');
            window.location='dashboard.php';
          </script>";

    exit; // Hentikan proses halaman di sini

} // Akhir blok if


// ==================================================
// CEK APAKAH DAFTAR ULANG SUDAH DITERIMA
// ==================================================

$daftar_ulang_diterima = // Cek apakah daftar ulang sudah diterima (true/false)
    $user['status_daftar_ulang_detail'] // Data status_daftar_ulang_detail dari array user
    === 'Diterima'; // sama dengan 'Diterima'

$nim_tersedia = // Cek apakah NIM sudah terisi (true/false)
    !empty($user['nim']); // dan NIM sudah terisi

 // Akhir blok kode PHP ?>

<!DOCTYPE html> <!-- Deklarasi tipe dokumen HTML5 -->
<html lang="id"> <!-- Tag akar halaman, bahasa Indonesia -->

<head> <!-- Awal bagian head (pengaturan halaman) -->

    <meta charset="UTF-8"> <!-- Set encoding karakter UTF-8 -->

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    > <!-- Agar tampilan menyesuaikan lebar layar HP -->

    <title> 
        Nomor Induk - Universitas NaraDipa 
    </title> <!-- Tutup judul tab -->

    <link
        rel="stylesheet"
        href="../css/style.css"
    > <!-- Memuat file CSS utama -->

</head> <!-- Tutup bagian head -->


<body> <!-- Awal isi halaman yang tampil -->

<div class="dashboard"> <!-- Wadah: pembungkus layout dashboard -->


    <!-- ==================================================
         SIDEBAR
    ================================================== -->

    <aside class="sidebar"> <!-- Sidebar: menu samping -->

        <div class="sidebar-logo"> <!-- Wadah: logo di sidebar -->

            <div class="logo-icon"> <!-- Wadah: ikon logo -->
                🎓 <!-- Ikon emoji penghias -->
            </div> <!-- Tutup ikon logo -->

            <div> <!-- Wadah -->

                <h2> <!-- Judul bagian -->
                    Universitas NaraDipa <!-- Teks judul bagian -->
                </h2> <!-- Tutup judul bagian -->

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


            <a href="nomor_test.php"> <!-- Link ke Nomor Test -->

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


            <a
                href="nomor_induk.php"
                class="active"
            > <!-- Link menu Nomor Induk (menu aktif) -->

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



    <!-- ==================================================
         MAIN CONTENT
    ================================================== -->

    <main class="main-content"> <!-- Konten utama halaman -->


        <!-- HEADER -->

        <header class="dashboard-header"> <!-- Header halaman -->

            <div> <!-- Wadah -->

                <h1> <!-- Judul utama -->
                    Nomor Induk Mahasiswa <!-- Teks judul halaman -->
                </h1> <!-- Tutup judul utama -->

                <p> <!-- Paragraf -->
                    Informasi Nomor Induk Mahasiswa kamu <!-- Teks paragraf -->
                </p> <!-- Tutup paragraf -->

            </div> <!-- Tutup wadah -->


            <div class="user-info"> <!-- Wadah: info user di header -->

                <div class="user-avatar"> <!-- Wadah: avatar user -->
                    👤 <!-- Ikon emoji penghias -->
                </div> <!-- Tutup avatar user -->

                <div> <!-- Wadah -->

                    <strong> <!-- Teks tebal -->

                        <?= htmlspecialchars( // Tampilkan teks berikut dengan aman (di-escape dari XSS)
                            $user['nama'] // Data nama lengkap dari array user
                        );  // Akhir pemanggilan fungsi ?>

                    </strong> <!-- Tutup teks tebal -->

                    <span> <!-- Teks keterangan -->
                        Calon Mahasiswa <!-- Teks keterangan -->
                    </span> <!-- Tutup teks keterangan -->

                </div> <!-- Tutup wadah -->

            </div> <!-- Tutup info user di header -->

        </header> <!-- Tutup header halaman -->



        <!-- ==================================================
             NIM SUDAH TERSEDIA
        ================================================== -->

        <?php if ( // Mulai pengecekan kondisi (if)
            $daftar_ulang_diterima && // Daftar ulang diterima dan
            $nim_tersedia // NIM sudah ada
        ):  // Akhir kondisi, lanjut ke tampilan HTML ?>


            <section class="nim-card"> <!-- Bagian: kartu NIM -->

                <div class="nim-icon"> <!-- Wadah: ikon NIM -->
                    🎓 <!-- Ikon emoji penghias -->
                </div> <!-- Tutup ikon NIM -->

                <p class="nim-label"> <!-- Paragraf: label NIM -->
                    NOMOR INDUK MAHASISWA <!-- Teks paragraf -->
                </p> <!-- Tutup label NIM -->

                <h2> <!-- Judul bagian -->

                    <?= htmlspecialchars( // Tampilkan teks berikut dengan aman (di-escape dari XSS)
                        $user['nim'] // Data NIM dari array user
                    );  // Akhir pemanggilan fungsi ?>

                </h2> <!-- Tutup judul bagian -->

                <p class="nim-info"> <!-- Paragraf: info NIM -->

                    Selamat! <!-- Teks paragraf -->
                    Daftar ulang kamu telah diterima <!-- Teks paragraf -->
                    dan kamu telah mendapatkan <!-- Teks paragraf -->
                    Nomor Induk Mahasiswa. <!-- Teks paragraf -->

                </p> <!-- Tutup info NIM -->

            </section> <!-- Tutup kartu NIM -->



            <!-- DATA MAHASISWA -->

            <section class="content-card"> <!-- Bagian: kartu konten -->

                <div class="section-title"> <!-- Wadah: judul bagian -->

                    <div> <!-- Wadah -->

                        <h2> <!-- Judul bagian -->
                            Data Mahasiswa <!-- Teks judul bagian -->
                        </h2> <!-- Tutup judul bagian -->

                        <p> <!-- Paragraf -->
                            Informasi data mahasiswa <!-- Teks paragraf -->
                        </p> <!-- Tutup paragraf -->

                    </div> <!-- Tutup wadah -->

                </div> <!-- Tutup judul bagian -->


                <div class="data-grid"> <!-- Wadah: grid data -->


                    <div class="data-item"> <!-- Wadah: satu item data -->

                        <span> <!-- Teks keterangan -->
                            Nama Lengkap <!-- Teks keterangan -->
                        </span> <!-- Tutup teks keterangan -->

                        <strong> <!-- Teks tebal -->

                            <?= htmlspecialchars( // Tampilkan teks berikut dengan aman (di-escape dari XSS)
                                $user['nama'] // Data nama lengkap dari array user
                            );  // Akhir pemanggilan fungsi ?>

                        </strong> <!-- Tutup teks tebal -->

                    </div> <!-- Tutup satu item data -->


                    <div class="data-item"> <!-- Wadah: satu item data -->

                        <span> <!-- Teks keterangan -->
                            Nomor Test <!-- Teks keterangan -->
                        </span> <!-- Tutup teks keterangan -->

                        <strong> <!-- Teks tebal -->

                            <?= htmlspecialchars( // Tampilkan teks berikut dengan aman (di-escape dari XSS)
                                $user['nomor_test'] // Data nomor test dari array user
                                ?? '-' // kalau kosong pakai '-'
                            );  // Akhir pemanggilan fungsi ?>

                        </strong> <!-- Tutup teks tebal -->

                    </div> <!-- Tutup satu item data -->


                    <div class="data-item"> <!-- Wadah: satu item data -->

                        <span> <!-- Teks keterangan -->
                            Program Studi <!-- Teks keterangan -->
                        </span> <!-- Tutup teks keterangan -->

                        <strong> <!-- Teks tebal -->

                            <?= htmlspecialchars( // Tampilkan teks berikut dengan aman (di-escape dari XSS)
                                $user['prodi'] // Data program studi dari array user
                                ?? '-' // kalau kosong pakai '-'
                            );  // Akhir pemanggilan fungsi ?>

                        </strong> <!-- Tutup teks tebal -->

                    </div> <!-- Tutup satu item data -->


                    <div class="data-item"> <!-- Wadah: satu item data -->

                        <span> <!-- Teks keterangan -->
                            Nomor Induk Mahasiswa <!-- Teks keterangan -->
                        </span> <!-- Tutup teks keterangan -->

                        <strong> <!-- Teks tebal -->

                            <?= htmlspecialchars( // Tampilkan teks berikut dengan aman (di-escape dari XSS)
                                $user['nim'] // Data NIM dari array user
                            );  // Akhir pemanggilan fungsi ?>

                        </strong> <!-- Tutup teks tebal -->

                    </div> <!-- Tutup satu item data -->


                </div> <!-- Tutup grid data -->

            </section> <!-- Tutup kartu konten -->


        <?php else:  // Kalau kondisi di atas tidak terpenuhi ?>


            <!-- ==================================================
                 NIM BELUM TERSEDIA
            ================================================== -->

            <section class="empty-card"> <!-- Bagian: kartu kosong -->

                <div class="empty-icon"> <!-- Wadah: ikon kartu kosong -->
                    🎓 <!-- Ikon emoji penghias -->
                </div> <!-- Tutup ikon kartu kosong -->


                <?php if ( // Mulai pengecekan kondisi (if)
                    $user['status_test'] !== 'Lulus' // Status test bukan 'Lulus'
                ):  // Akhir kondisi, lanjut ke tampilan HTML ?>


                    <h2> <!-- Judul bagian -->
                        Nomor Induk Belum Tersedia <!-- Teks judul bagian -->
                    </h2> <!-- Tutup judul bagian -->

                    <p> <!-- Paragraf -->
                        Kamu belum dinyatakan lulus <!-- Teks paragraf -->
                        pada test PMB. <!-- Teks paragraf -->
                    </p> <!-- Tutup paragraf -->


                    <a
                        href="hasil_test.php"
                        class="btn-primary"
                    > <!-- Tombol utama menuju Hasil Test -->

                        Lihat Hasil Test <!-- Teks pada link/tombol -->

                    </a> <!-- Tutup link Hasil Test -->


                <?php elseif ( // Kalau belum, cek kondisi lain (elseif)
                    !$user['status_daftar_ulang_detail'] // Belum ada data daftar ulang
                ):  // Akhir kondisi, lanjut ke tampilan HTML ?>


                    <h2> <!-- Judul bagian -->
                        Belum Melakukan Daftar Ulang <!-- Teks judul bagian -->
                    </h2> <!-- Tutup judul bagian -->

                    <p> <!-- Paragraf -->
                        Silakan lakukan daftar ulang <!-- Teks paragraf -->
                        terlebih dahulu. <!-- Teks paragraf -->
                    </p> <!-- Tutup paragraf -->


                    <a
                        href="daftar_ulang.php"
                        class="btn-primary"
                    > <!-- Tombol utama menuju Daftar Ulang -->

                        Daftar Ulang <!-- Teks pada link/tombol -->

                    </a> <!-- Tutup link Daftar Ulang -->


                <?php elseif ( // Kalau belum, cek kondisi lain (elseif)
                    $user['status_daftar_ulang_detail'] // Data status_daftar_ulang_detail dari array user
                    === 'Menunggu' // sama dengan 'Menunggu'
                ):  // Akhir kondisi, lanjut ke tampilan HTML ?>


                    <h2> <!-- Judul bagian -->
                        Menunggu Verifikasi <!-- Teks judul bagian -->
                    </h2> <!-- Tutup judul bagian -->

                    <p> <!-- Paragraf -->
                        Daftar ulang kamu sedang <!-- Teks paragraf -->
                        diperiksa oleh Admin. <!-- Teks paragraf -->
                    </p> <!-- Tutup paragraf -->


                    <a
                        href="daftar_ulang.php"
                        class="btn-primary"
                    > <!-- Tombol utama menuju Daftar Ulang -->

                        Lihat Status <!-- Teks pada link/tombol -->

                    </a> <!-- Tutup link Daftar Ulang -->


                <?php elseif ( // Kalau belum, cek kondisi lain (elseif)
                    $user['status_daftar_ulang_detail'] // Data status_daftar_ulang_detail dari array user
                    === 'Ditolak' // sama dengan 'Ditolak'
                ):  // Akhir kondisi, lanjut ke tampilan HTML ?>


                    <h2> <!-- Judul bagian -->
                        Daftar Ulang Ditolak <!-- Teks judul bagian -->
                    </h2> <!-- Tutup judul bagian -->

                    <p> <!-- Paragraf -->
                        Daftar ulang kamu ditolak. <!-- Teks paragraf -->
                        Silakan hubungi Admin untuk <!-- Teks paragraf -->
                        informasi lebih lanjut. <!-- Teks paragraf -->
                    </p> <!-- Tutup paragraf -->


                    <a
                        href="daftar_ulang.php"
                        class="btn-primary"
                    > <!-- Tombol utama menuju Daftar Ulang -->

                        Lihat Status <!-- Teks pada link/tombol -->

                    </a> <!-- Tutup link Daftar Ulang -->


                <?php else:  // Kalau kondisi di atas tidak terpenuhi ?>


                    <h2> <!-- Judul bagian -->
                        Nomor Induk Belum Tersedia <!-- Teks judul bagian -->
                    </h2> <!-- Tutup judul bagian -->

                    <p> <!-- Paragraf -->
                        Nomor Induk Mahasiswa belum <!-- Teks paragraf -->
                        diberikan oleh Admin. <!-- Teks paragraf -->
                    </p> <!-- Tutup paragraf -->


                    <a
                        href="dashboard.php"
                        class="btn-primary"
                    > <!-- Tombol utama menuju Dashboard -->

                        Kembali ke Dashboard <!-- Teks pada link/tombol -->

                    </a> <!-- Tutup link Dashboard -->


                <?php endif;  // Akhir blok if ?>


            </section> <!-- Tutup kartu kosong -->


        <?php endif;  // Akhir blok if ?>


    </main> <!-- Tutup konten utama halaman -->

</div> <!-- Tutup pembungkus layout dashboard -->

</body> <!-- Tutup isi halaman (body) -->

</html> <!-- Tutup tag html -->
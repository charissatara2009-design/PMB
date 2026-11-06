<?php // Awal blok kode PHP

require_once "../config/auth.php"; // Muat file pengecekan login (auth)
require_once "../config/database.php"; // Muat file koneksi database

cekAdmin(); // Pastikan yang masuk adalah admin, kalau bukan diarahkan keluar


// ==================================================
// PROSES TERIMA DAFTAR ULANG
// ==================================================

if (isset($_POST['terima'])) { // Jalankan jika tombol Terima ditekan

    $daftar_id = $_POST['daftar_id']; // Ambil daftar id dari form
    $user_id = $_POST['user_id']; // Ambil user id dari form
    $nomor_test = trim($_POST['nomor_test']); // Ambil nomor test dari form

    // NIM otomatis = nomor test tanpa awalan "TEST"
    // contoh: TEST20261014044851  ->  20261014044851
    $nim = preg_replace('/^TEST/i', '', $nomor_test); // NIM = nomor test tanpa awalan 'TEST'


    // NIM otomatis diambil dari nomor test, tidak perlu input manual

    if ($nim === '') { // Kalau NIM kosong

        // Tampilkan alert "Nomor Test peserta ini tidak ditemukan, tidak bisa membuat NIM!" lalu pindah ke daftar_ulang.php
        echo "<script>

                alert('Nomor Test peserta ini tidak ditemukan, tidak bisa membuat NIM!');

                window.location='daftar_ulang.php';

              </script>";

        exit; // Hentikan proses halaman di sini
    } // Akhir blok if


    // ==================================================
    // UPDATE DAFTAR ULANG
    // ==================================================

    // Query SQL: ubah data tabel daftar_ulang (status = 'Diterima')
    $query_update = "UPDATE daftar_ulang
                     SET status = 'Diterima'
                     WHERE id = ?";

    $stmt_update = mysqli_prepare( // Siapkan query (prepared statement) agar aman dari SQL injection
        $conn, // Koneksi database
        $query_update // Teks query SQL yang akan dijalankan
    ); // Akhir pemanggilan fungsi

    mysqli_stmt_bind_param( // Isi tanda tanya (?) pada query dengan nilai yang aman
        $stmt_update, // Statement query yang sudah disiapkan
        "i", // Tipe data tiap nilai (i = angka bulat, d = desimal, s = teks)
        $daftar_id // ID data daftar ulang
    ); // Akhir pemanggilan fungsi

    $berhasil_update = // Simpan hasil eksekusi update (berhasil/gagal)
        mysqli_stmt_execute( // Jalankan query
            $stmt_update // Statement query yang sudah disiapkan
        ); // Akhir pemanggilan fungsi


    // ==================================================
    // UPDATE USER + NIM
    // ==================================================

    if ($berhasil_update) { // Kalau update berhasil

        // Query SQL: ubah data tabel users (nim = ?, status_daftar_ulang = 'Sudah')
        $query_user = "UPDATE users
                       SET
                           nim = ?,
                           status_daftar_ulang = 'Sudah'
                       WHERE id = ?";

        $stmt_user = mysqli_prepare( // Siapkan query (prepared statement) agar aman dari SQL injection
            $conn, // Koneksi database
            $query_user // Teks query SQL yang akan dijalankan
        ); // Akhir pemanggilan fungsi

        mysqli_stmt_bind_param( // Isi tanda tanya (?) pada query dengan nilai yang aman
            $stmt_user, // Statement query yang sudah disiapkan
            "si", // Tipe data tiap nilai (i = angka bulat, d = desimal, s = teks)
            $nim, // Nilai: NIM
            $user_id // ID user yang sedang login
        ); // Akhir pemanggilan fungsi

        mysqli_stmt_execute( // Jalankan query
            $stmt_user // Statement query yang sudah disiapkan
        ); // Akhir pemanggilan fungsi


        // Tampilkan alert "Daftar ulang diterima dan NIM berhasil diberikan!" lalu pindah ke daftar_ulang.php
        echo "<script>

                alert(
                    'Daftar ulang diterima dan NIM berhasil diberikan!'
                );

                window.location='daftar_ulang.php';

              </script>";

        exit; // Hentikan proses halaman di sini
    } // Akhir blok if

} // Akhir blok if


// ==================================================
// PROSES TOLAK DAFTAR ULANG
// ==================================================

if (isset($_POST['tolak'])) { // Jalankan jika tombol Tolak ditekan

    $daftar_id = $_POST['daftar_id']; // Ambil daftar id dari form
    $user_id = $_POST['user_id']; // Ambil user id dari form


    // ==================================================
    // UPDATE STATUS DAFTAR ULANG
    // ==================================================

    // Query SQL: ubah data tabel daftar_ulang (status = 'Ditolak')
    $query_update = "UPDATE daftar_ulang
                     SET status = 'Ditolak'
                     WHERE id = ?";

    $stmt_update = mysqli_prepare( // Siapkan query (prepared statement) agar aman dari SQL injection
        $conn, // Koneksi database
        $query_update // Teks query SQL yang akan dijalankan
    ); // Akhir pemanggilan fungsi

    mysqli_stmt_bind_param( // Isi tanda tanya (?) pada query dengan nilai yang aman
        $stmt_update, // Statement query yang sudah disiapkan
        "i", // Tipe data tiap nilai (i = angka bulat, d = desimal, s = teks)
        $daftar_id // ID data daftar ulang
    ); // Akhir pemanggilan fungsi

    $berhasil_update = // Simpan hasil eksekusi update (berhasil/gagal)
        mysqli_stmt_execute( // Jalankan query
            $stmt_update // Statement query yang sudah disiapkan
        ); // Akhir pemanggilan fungsi


    // ==================================================
    // UPDATE STATUS USER
    // ==================================================

    if ($berhasil_update) { // Kalau update berhasil

        // Query SQL: ubah data tabel users (status_daftar_ulang = 'Belum', nim = NULL)
        $query_user = "UPDATE users
                       SET
                           status_daftar_ulang = 'Belum',
                           nim = NULL
                       WHERE id = ?";

        $stmt_user = mysqli_prepare( // Siapkan query (prepared statement) agar aman dari SQL injection
            $conn, // Koneksi database
            $query_user // Teks query SQL yang akan dijalankan
        ); // Akhir pemanggilan fungsi

        mysqli_stmt_bind_param( // Isi tanda tanya (?) pada query dengan nilai yang aman
            $stmt_user, // Statement query yang sudah disiapkan
            "i", // Tipe data tiap nilai (i = angka bulat, d = desimal, s = teks)
            $user_id // ID user yang sedang login
        ); // Akhir pemanggilan fungsi

        mysqli_stmt_execute( // Jalankan query
            $stmt_user // Statement query yang sudah disiapkan
        ); // Akhir pemanggilan fungsi


        // Tampilkan alert "Daftar ulang ditolak." lalu pindah ke daftar_ulang.php
        echo "<script>

                alert(
                    'Daftar ulang ditolak.'
                );

                window.location='daftar_ulang.php';

              </script>";

        exit; // Hentikan proses halaman di sini
    } // Akhir blok if

} // Akhir blok if


// ==================================================
// STATISTIK
// ==================================================

$query_menunggu = mysqli_query( // Jalankan query hitung yang berstatus Menunggu
    $conn, // Koneksi database
    // Query SQL: hitung data daftar ulang berstatus 'Menunggu'
    "SELECT COUNT(*) AS total
     FROM daftar_ulang
     WHERE status = 'Menunggu'"
); // Akhir pemanggilan fungsi

$total_menunggu = // Simpan jumlah yang Menunggu
    mysqli_fetch_assoc( // Ambil satu baris soal
        $query_menunggu // Teks query SQL yang akan dijalankan
    )['total']; // Ambil kolom total dari hasil


$query_diterima = mysqli_query( // Jalankan query hitung yang berstatus Diterima
    $conn, // Koneksi database
    // Query SQL: hitung data daftar ulang berstatus 'Diterima'
    "SELECT COUNT(*) AS total
     FROM daftar_ulang
     WHERE status = 'Diterima'"
); // Akhir pemanggilan fungsi

$total_diterima = // Simpan jumlah yang Diterima
    mysqli_fetch_assoc( // Ambil satu baris soal
        $query_diterima // Teks query SQL yang akan dijalankan
    )['total']; // Ambil kolom total dari hasil


$query_ditolak = mysqli_query( // Jalankan query hitung yang berstatus Ditolak
    $conn, // Koneksi database
    // Query SQL: hitung data daftar ulang berstatus 'Ditolak'
    "SELECT COUNT(*) AS total
     FROM daftar_ulang
     WHERE status = 'Ditolak'"
); // Akhir pemanggilan fungsi

$total_ditolak = // Simpan jumlah yang Ditolak
    mysqli_fetch_assoc( // Ambil satu baris soal
        $query_ditolak // Teks query SQL yang akan dijalankan
    )['total']; // Ambil kolom total dari hasil


// ==================================================
// AMBIL DATA DAFTAR ULANG
// ==================================================

// Query SQL: ambil data dari tabel daftar_ulang
$query = "SELECT
              daftar_ulang.id AS daftar_id,
              daftar_ulang.user_id,
              daftar_ulang.tanggal_daftar_ulang,
              daftar_ulang.status,

              users.nama,
              users.nomor_test,
              users.prodi,
              users.status_test,
              users.nim

          FROM daftar_ulang

          INNER JOIN users
          ON daftar_ulang.user_id = users.id

          ORDER BY daftar_ulang.id DESC";


$result = mysqli_query( // Jalankan query dan simpan hasilnya
    $conn, // Koneksi database
    $query // Teks query SQL yang akan dijalankan
); // Akhir pemanggilan fungsi

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
        Daftar Ulang - Universitas NaraDipa 
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
                    Administrator <!-- Teks keterangan -->
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


            <a href="data_maba.php"> <!-- Link ke Data Calon Maba -->

                👥 <!-- Ikon emoji penghias -->

                <span> <!-- Teks keterangan -->
                    Data Calon Maba <!-- Teks keterangan -->
                </span> <!-- Tutup teks keterangan -->

            </a> <!-- Tutup link Data Calon Maba -->


            <a href="data_soal.php"> <!-- Link ke Data Soal Test -->

                📝 <!-- Ikon emoji penghias -->

                <span> <!-- Teks keterangan -->
                    Data Soal Test <!-- Teks keterangan -->
                </span> <!-- Tutup teks keterangan -->

            </a> <!-- Tutup link Data Soal Test -->


            <a href="hasil_test.php"> <!-- Link ke Hasil Test -->

                📊 <!-- Ikon emoji penghias -->

                <span> <!-- Teks keterangan -->
                    Hasil Test <!-- Teks keterangan -->
                </span> <!-- Tutup teks keterangan -->

            </a> <!-- Tutup link Hasil Test -->


            <a
                href="daftar_ulang.php"
                class="active"
            > <!-- Link menu Daftar Ulang (menu aktif) -->

                📋 <!-- Ikon emoji penghias -->

                <span> <!-- Teks keterangan -->
                    Daftar Ulang <!-- Teks keterangan -->
                </span> <!-- Tutup teks keterangan -->

            </a> <!-- Tutup link Daftar Ulang -->


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
                    Daftar Ulang <!-- Teks judul halaman -->
                </h1> <!-- Tutup judul utama -->

                <p> <!-- Paragraf -->
                    Kelola daftar ulang calon mahasiswa <!-- Teks paragraf -->
                </p> <!-- Tutup paragraf -->

            </div> <!-- Tutup wadah -->


            <div class="user-info"> <!-- Wadah: info user di header -->

                <div class="user-avatar"> <!-- Wadah: avatar user -->
                    👨‍💼 <!-- Ikon emoji penghias -->
                </div> <!-- Tutup avatar user -->

                <div> <!-- Wadah -->

                    <strong> <!-- Teks tebal -->

                        <?= htmlspecialchars( // Tampilkan teks berikut dengan aman (di-escape dari XSS)
                            $_SESSION['nama'] // Data nama lengkap dari array _SESSION
                        );  // Akhir pemanggilan fungsi ?>

                    </strong> <!-- Tutup teks tebal -->

                    <span> <!-- Teks keterangan -->
                        Administrator <!-- Teks keterangan -->
                    </span> <!-- Tutup teks keterangan -->

                </div> <!-- Tutup wadah -->

            </div> <!-- Tutup info user di header -->

        </header> <!-- Tutup header halaman -->



        <!-- ==================================================
             STATISTIK
        ================================================== -->

        <div class="stats-grid"> <!-- Wadah: deretan statistik -->


            <!-- MENUNGGU -->

            <div class="stat-card"> <!-- Wadah: kartu statistik -->

                <div class="stat-icon"> <!-- Wadah: ikon statistik -->
                    ⏳ <!-- Ikon emoji penghias -->
                </div> <!-- Tutup ikon statistik -->

                <div> <!-- Wadah -->

                    <span> <!-- Teks keterangan -->
                        Menunggu <!-- Teks keterangan -->
                    </span> <!-- Tutup teks keterangan -->

                    <h2> <!-- Judul bagian -->
                        <?= $total_menunggu;  // Tampilkan isi variabel $total_menunggu ?>
                    </h2> <!-- Tutup judul bagian -->

                    <small> <!-- Teks kecil -->
                        Pendaftar <!-- Teks kecil -->
                    </small> <!-- Tutup teks kecil -->

                </div> <!-- Tutup wadah -->

            </div> <!-- Tutup kartu statistik -->


            <!-- DITERIMA -->

            <div class="stat-card"> <!-- Wadah: kartu statistik -->

                <div class="stat-icon"> <!-- Wadah: ikon statistik -->
                    ✓ <!-- Ikon emoji penghias -->
                </div> <!-- Tutup ikon statistik -->

                <div> <!-- Wadah -->

                    <span> <!-- Teks keterangan -->
                        Diterima <!-- Teks keterangan -->
                    </span> <!-- Tutup teks keterangan -->

                    <h2> <!-- Judul bagian -->
                        <?= $total_diterima;  // Tampilkan isi variabel $total_diterima ?>
                    </h2> <!-- Tutup judul bagian -->

                    <small> <!-- Teks kecil -->
                        Pendaftar <!-- Teks kecil -->
                    </small> <!-- Tutup teks kecil -->

                </div> <!-- Tutup wadah -->

            </div> <!-- Tutup kartu statistik -->


            <!-- DITOLAK -->

            <div class="stat-card"> <!-- Wadah: kartu statistik -->

                <div class="stat-icon"> <!-- Wadah: ikon statistik -->
                    ✕ <!-- Ikon emoji penghias -->
                </div> <!-- Tutup ikon statistik -->

                <div> <!-- Wadah -->

                    <span> <!-- Teks keterangan -->
                        Ditolak <!-- Teks keterangan -->
                    </span> <!-- Tutup teks keterangan -->

                    <h2> <!-- Judul bagian -->
                        <?= $total_ditolak;  // Tampilkan isi variabel $total_ditolak ?>
                    </h2> <!-- Tutup judul bagian -->

                    <small> <!-- Teks kecil -->
                        Pendaftar <!-- Teks kecil -->
                    </small> <!-- Tutup teks kecil -->

                </div> <!-- Tutup wadah -->

            </div> <!-- Tutup kartu statistik -->


        </div> <!-- Tutup deretan statistik -->



        <!-- ==================================================
             DATA DAFTAR ULANG
        ================================================== -->

        <section class="content-card"> <!-- Bagian: kartu konten -->


            <div class="section-title"> <!-- Wadah: judul bagian -->

                <div> <!-- Wadah -->

                    <h2> <!-- Judul bagian -->
                        Data Daftar Ulang <!-- Teks judul bagian -->
                    </h2> <!-- Tutup judul bagian -->

                    <p> <!-- Paragraf -->
                        Daftar calon mahasiswa yang melakukan <!-- Teks paragraf -->
                        daftar ulang <!-- Teks paragraf -->
                    </p> <!-- Tutup paragraf -->

                </div> <!-- Tutup wadah -->

            </div> <!-- Tutup judul bagian -->



            <div class="table-container"> <!-- Wadah: pembungkus tabel -->

                <table> <!-- Tabel -->

                    <thead> <!-- Kepala tabel -->

                        <tr> <!-- Baris tabel -->

                            <th> <!-- Judul kolom -->
                                No <!-- Teks yang ditampilkan di halaman -->
                            </th> <!-- Tutup judul kolom -->

                            <th> <!-- Judul kolom -->
                                Nomor Test <!-- Teks yang ditampilkan di halaman -->
                            </th> <!-- Tutup judul kolom -->

                            <th> <!-- Judul kolom -->
                                Nama <!-- Teks yang ditampilkan di halaman -->
                            </th> <!-- Tutup judul kolom -->

                            <th> <!-- Judul kolom -->
                                Program Studi <!-- Teks yang ditampilkan di halaman -->
                            </th> <!-- Tutup judul kolom -->

                            <th> <!-- Judul kolom -->
                                Tanggal <!-- Teks yang ditampilkan di halaman -->
                            </th> <!-- Tutup judul kolom -->

                            <th> <!-- Judul kolom -->
                                Status <!-- Teks yang ditampilkan di halaman -->
                            </th> <!-- Tutup judul kolom -->

                            <th> <!-- Judul kolom -->
                                NIM <!-- Teks yang ditampilkan di halaman -->
                            </th> <!-- Tutup judul kolom -->

                            <th> <!-- Judul kolom -->
                                Aksi <!-- Teks yang ditampilkan di halaman -->
                            </th> <!-- Tutup judul kolom -->

                        </tr> <!-- Tutup baris tabel -->

                    </thead> <!-- Tutup kepala tabel -->


                    <tbody> <!-- Isi tabel -->

                    <?php // Awal blok kode PHP

                    $no = 1; // Nomor urut mulai dari 1

                    if ( // Mulai pengecekan kondisi (if)
                        mysqli_num_rows( // Hitung jumlah baris hasil
                            $result // Hasil query
                        ) > 0 // lebih dari 0 (ada data)
                    ): // Akhir kondisi, lanjut ke tampilan HTML

                        while ( // Ulangi selama masih ada baris
                            $row = // Ambil satu baris data
                            mysqli_fetch_assoc( // Ambil satu baris soal
                                $result // Hasil query
                            ) // Penutup tanda kurung
                        ): // Akhir kondisi, lanjut ke tampilan HTML

                     // Akhir blok kode PHP ?>

                        <tr> <!-- Baris tabel -->


                            <!-- NO -->

                            <td> <!-- Sel tabel -->
                                <?= $no++;  // Tampilkan isi variabel $no lalu tambah 1 ?>
                            </td> <!-- Tutup sel tabel -->


                            <!-- NOMOR TEST -->

                            <td> <!-- Sel tabel -->

                                <strong> <!-- Teks tebal -->

                                    <?= htmlspecialchars( // Tampilkan teks berikut dengan aman (di-escape dari XSS)
                                        $row['nomor_test'] // Data nomor test dari array row
                                        ?? '-' // kalau kosong pakai '-'
                                    );  // Akhir pemanggilan fungsi ?>

                                </strong> <!-- Tutup teks tebal -->

                            </td> <!-- Tutup sel tabel -->


                            <!-- NAMA -->

                            <td> <!-- Sel tabel -->

                                <?= htmlspecialchars( // Tampilkan teks berikut dengan aman (di-escape dari XSS)
                                    $row['nama'] // Data nama lengkap dari array row
                                );  // Akhir pemanggilan fungsi ?>

                            </td> <!-- Tutup sel tabel -->


                            <!-- PRODI -->

                            <td> <!-- Sel tabel -->

                                <?= htmlspecialchars( // Tampilkan teks berikut dengan aman (di-escape dari XSS)
                                    $row['prodi'] // Data program studi dari array row
                                    ?? '-' // kalau kosong pakai '-'
                                );  // Akhir pemanggilan fungsi ?>

                            </td> <!-- Tutup sel tabel -->


                            <!-- TANGGAL -->

                            <td> <!-- Sel tabel -->

                                <?= date( // Tampilkan tanggal dengan format tertentu
                                    'd M Y', // Format: tanggal, bulan singkat, tahun
                                    strtotime( // Ubah teks tanggal jadi angka waktu
                                        $row[ // Ambil dari baris data
                                            'tanggal_daftar_ulang' // kolom tanggal daftar ulang
                                        ] // Penutup kurung siku
                                    ) // Penutup tanda kurung
                                );  // Akhir pemanggilan fungsi ?>

                            </td> <!-- Tutup sel tabel -->


                            <!-- STATUS -->

                            <td> <!-- Sel tabel -->

                                <?php if ( // Mulai pengecekan kondisi (if)
                                    $row['status'] // Data status dari array row
                                    === 'Menunggu' // sama dengan 'Menunggu'
                                ):  // Akhir kondisi, lanjut ke tampilan HTML ?>

                                    <span class="badge warning"> <!-- Teks keterangan: label status -->
                                        ⏳ Menunggu <!-- Teks keterangan -->
                                    </span> <!-- Tutup label status -->

                                <?php elseif ( // Kalau belum, cek kondisi lain (elseif)
                                    $row['status'] // Data status dari array row
                                    === 'Diterima' // sama dengan 'Diterima'
                                ):  // Akhir kondisi, lanjut ke tampilan HTML ?>

                                    <span class="badge success"> <!-- Teks keterangan: label status -->
                                        ✓ Diterima <!-- Teks keterangan -->
                                    </span> <!-- Tutup label status -->

                                <?php else:  // Kalau kondisi di atas tidak terpenuhi ?>

                                    <span class="badge danger"> <!-- Teks keterangan: label status -->
                                        ✕ Ditolak <!-- Teks keterangan -->
                                    </span> <!-- Tutup label status -->

                                <?php endif;  // Akhir blok if ?>

                            </td> <!-- Tutup sel tabel -->


                            <!-- NIM -->

                            <td> <!-- Sel tabel -->

                                <?php if ( // Mulai pengecekan kondisi (if)
                                    !empty( // Tidak kosong:
                                        $row['nim'] // Data NIM dari array row
                                    ) // Penutup tanda kurung
                                ):  // Akhir kondisi, lanjut ke tampilan HTML ?>

                                    <strong> <!-- Teks tebal -->

                                        <?= htmlspecialchars( // Tampilkan teks berikut dengan aman (di-escape dari XSS)
                                            $row['nim'] // Data NIM dari array row
                                        );  // Akhir pemanggilan fungsi ?>

                                    </strong> <!-- Tutup teks tebal -->

                                <?php else:  // Kalau kondisi di atas tidak terpenuhi ?>

                                    - <!-- Ikon emoji penghias -->

                                <?php endif;  // Akhir blok if ?>

                            </td> <!-- Tutup sel tabel -->


                            <!-- AKSI -->

                            <td> <!-- Sel tabel -->


                                <?php if ( // Mulai pengecekan kondisi (if)
                                    $row['status'] // Data status dari array row
                                    === 'Menunggu' // sama dengan 'Menunggu'
                                ):  // Akhir kondisi, lanjut ke tampilan HTML ?>


                                    <!-- TERIMA -->

                                    <form
                                        method="POST"
                                        style="margin-bottom:8px;"
                                        onsubmit="return konfirmasiTerima();"
                                    > <!-- Form kirim data (method POST), konfirmasi dulu sebelum kirim -->

                                        <input
                                            type="hidden"
                                            name="daftar_id"
                                            value="<?= $row['daftar_id']; ?>"
                                        > <!-- Input hidden "daftar_id" -->

                                        <input
                                            type="hidden"
                                            name="user_id"
                                            value="<?= $row['user_id']; ?>"
                                        > <!-- Input hidden "user_id" -->

                                        <input
                                            type="hidden"
                                            name="nomor_test"
                                            value="<?= htmlspecialchars($row['nomor_test'] ?? ''); ?>"
                                        > <!-- Input hidden "nomor_test" -->


                                        <button
                                            type="submit"
                                            name="terima"
                                            class="btn-edit"
                                        > <!-- Tombol kirim form (terima) -->

                                            ✓ Terima <!-- Teks pada tombol -->

                                        </button> <!-- Tutup tombol -->

                                    </form> <!-- Tutup form -->


                                    <!-- TOLAK -->

                                    <form
                                        method="POST"
                                        onsubmit="return konfirmasiTolak();"
                                    > <!-- Form kirim data (method POST), konfirmasi dulu sebelum kirim -->

                                        <input
                                            type="hidden"
                                            name="daftar_id"
                                            value="<?= $row['daftar_id']; ?>"
                                        > <!-- Input hidden "daftar_id" -->

                                        <input
                                            type="hidden"
                                            name="user_id"
                                            value="<?= $row['user_id']; ?>"
                                        > <!-- Input hidden "user_id" -->


                                        <button
                                            type="submit"
                                            name="tolak"
                                            class="btn-delete"
                                        > <!-- Tombol kirim form (tolak) -->

                                            ✕ Tolak <!-- Teks pada tombol -->

                                        </button> <!-- Tutup tombol -->

                                    </form> <!-- Tutup form -->


                                <?php elseif ( // Kalau belum, cek kondisi lain (elseif)
                                    $row['status'] // Data status dari array row
                                    === 'Diterima' // sama dengan 'Diterima'
                                ):  // Akhir kondisi, lanjut ke tampilan HTML ?>


                                    <span class="badge success"> <!-- Teks keterangan: label status -->

                                        ✓ Selesai <!-- Teks keterangan -->

                                    </span> <!-- Tutup label status -->


                                <?php else:  // Kalau kondisi di atas tidak terpenuhi ?>


                                    <span class="badge danger"> <!-- Teks keterangan: label status -->

                                        Ditolak <!-- Teks keterangan -->

                                    </span> <!-- Tutup label status -->


                                <?php endif;  // Akhir blok if ?>


                            </td> <!-- Tutup sel tabel -->


                        </tr> <!-- Tutup baris tabel -->

                    <?php // Awal blok kode PHP

                        endwhile; // Akhir perulangan while

                    else: // Kalau kondisi di atas tidak terpenuhi

                     // Akhir blok kode PHP ?>

                        <tr> <!-- Baris tabel -->

                            <td
                                colspan="8"
                                class="empty-table"
                            > <!-- Sel tabel: pesan tabel kosong -->

                                Belum ada data daftar ulang. <!-- Teks yang ditampilkan di halaman -->

                            </td> <!-- Tutup pesan tabel kosong -->

                        </tr> <!-- Tutup baris tabel -->

                    <?php endif;  // Akhir blok if ?>

                    </tbody> <!-- Tutup isi tabel -->

                </table> <!-- Tutup tabel -->

            </div> <!-- Tutup pembungkus tabel -->


        </section> <!-- Tutup kartu konten -->


    </main> <!-- Tutup konten utama halaman -->

</div> <!-- Tutup pembungkus layout dashboard -->



<!-- ==================================================
     JAVASCRIPT
================================================== -->

<script> // Awal blok JavaScript

function konfirmasiTerima() // Fungsi konfirmasiTerima: minta konfirmasi (OK/Batal)
{ // Awal blok kode
    return confirm( // Tampilkan kotak konfirmasi OK/Batal
        "Yakin ingin menerima daftar ulang ini?" // Teks pertanyaan konfirmasi
    ); // Akhir pemanggilan fungsi
} // Akhir blok kode


function konfirmasiTolak() // Fungsi konfirmasiTolak: minta konfirmasi (OK/Batal)
{ // Awal blok kode
    return confirm( // Tampilkan kotak konfirmasi OK/Batal
        "Yakin ingin menolak daftar ulang ini?" // Teks pertanyaan konfirmasi
    ); // Akhir pemanggilan fungsi
} // Akhir blok kode

</script> <!-- Tutup blok JavaScript -->


</body> <!-- Tutup isi halaman (body) -->

</html> <!-- Tutup tag html -->
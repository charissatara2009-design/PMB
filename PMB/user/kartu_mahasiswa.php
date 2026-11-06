<?php // Awal blok kode PHP

require_once "../config/auth.php"; // Muat file pengecekan login (auth)
require_once "../config/database.php"; // Muat file koneksi database

cekUser(); // Pastikan user sudah login, kalau belum diarahkan keluar

$user_id = $_SESSION['user_id']; // Ambil ID user yang sedang login dari session


// ==================================================
// AMBIL DATA USER
// ==================================================

// Query SQL: ambil data dari tabel users milik user yang login
$query_user = "SELECT
                    nama,
                    prodi,
                    status_daftar_ulang,
                    nim
               FROM users
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

$result_user = mysqli_stmt_get_result( // Ambil hasil query
    $stmt_user // Statement query yang sudah disiapkan
); // Akhir pemanggilan fungsi

$user = mysqli_fetch_assoc( // Ambil satu baris hasil query dalam bentuk array
    $result_user // Hasil query
); // Akhir pemanggilan fungsi


if (!$user) { // Kalau data user tidak ditemukan

    // Tampilkan alert "Data user tidak ditemukan!" lalu pindah ke dashboard.php
    echo "<script>
            alert('Data user tidak ditemukan!');
            window.location='dashboard.php';
          </script>";

    exit; // Hentikan proses halaman di sini
} // Akhir blok if


// ==================================================
// AMBIL DATA DAFTAR ULANG
// ==================================================

// Query SQL: ambil data dari tabel daftar_ulang milik user yang login, ambil yang paling baru saja
$query_daftar = "SELECT
                     tanggal_daftar_ulang,
                     status,
                     foto,
                     email
                 FROM daftar_ulang
                 WHERE user_id = ?
                 ORDER BY id DESC
                 LIMIT 1";

$stmt_daftar = mysqli_prepare( // Siapkan query (prepared statement) agar aman dari SQL injection
    $conn, // Koneksi database
    $query_daftar // Teks query SQL yang akan dijalankan
); // Akhir pemanggilan fungsi

mysqli_stmt_bind_param( // Isi tanda tanya (?) pada query dengan nilai yang aman
    $stmt_daftar, // Statement query yang sudah disiapkan
    "i", // Tipe data tiap nilai (i = angka bulat, d = desimal, s = teks)
    $user_id // ID user yang sedang login
); // Akhir pemanggilan fungsi

mysqli_stmt_execute( // Jalankan query
    $stmt_daftar // Statement query yang sudah disiapkan
); // Akhir pemanggilan fungsi

$result_daftar = mysqli_stmt_get_result( // Ambil hasil query
    $stmt_daftar // Statement query yang sudah disiapkan
); // Akhir pemanggilan fungsi

$daftar_ulang = mysqli_fetch_assoc( // Ambil satu baris hasil query dalam bentuk array
    $result_daftar // Hasil query
); // Akhir pemanggilan fungsi


// ==================================================
// KALAU BELUM DAFTAR ULANG SAMA SEKALI
// ==================================================

if (!$daftar_ulang) { // Kalau belum pernah daftar ulang

    // Tampilkan alert "Kamu belum melakukan daftar ulang. Silakan isi data daftar ulang terlebih dahulu." lalu pindah ke daftar_ulang.php
    echo "<script>
            alert('Kamu belum melakukan daftar ulang. Silakan isi data daftar ulang terlebih dahulu.');
            window.location='daftar_ulang.php';
          </script>";

    exit; // Hentikan proses halaman di sini
} // Akhir blok if


$status_daftar = $daftar_ulang['status']; // Simpan status daftar ulang ke variabel

$sudah_diterima = // Tentukan apakah sudah diterima (true/false)
    $status_daftar === 'Diterima' // Status daftar ulang 'Diterima'
    && // dan
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
        Kartu Mahasiswa - Universitas NaraDipa 
    </title> <!-- Tutup judul tab -->

    <link
        rel="stylesheet"
        href="../css/style.css"
    > <!-- Memuat file CSS utama -->

    <style> /* Awal blok CSS (gaya khusus halaman ini) */

        .ktm-wrapper { /* Gaya untuk pembungkus kartu KTM */
            overflow-x: auto; /* Boleh digeser ke samping jika kepanjangan */
            margin-top: 16px; /* Jarak luar atas 16px */
        } /* Akhir aturan CSS */

        .ktm-card { /* Gaya untuk kartu KTM */
            position: relative; /* Posisi elemen: relative */
            display: flex; /* Susun isi dengan flexbox */
            width: 760px; /* Lebar 760px */
            height: 290px; /* Tinggi 290px */
            border-radius: 24px; /* Sudut melengkung 24px */
            overflow: hidden; /* Potong isi yang keluar dari kotak */
            box-shadow: 0 14px 36px rgba(0,0,0,0.2); /* Bayangan 0 14px 36px rgba(0,0,0,0.2) */
            background: #fff; /* Latar belakang #fff */
            font-family: Arial, Helvetica, sans-serif; /* Jenis huruf Arial, Helvetica, sans-serif */
        } /* Akhir aturan CSS */

        .ktm-left { /* Gaya untuk sisi kiri KTM */
            position: relative; /* Posisi elemen: relative */
            width: 270px; /* Lebar 270px */
            flex-shrink: 0; /* Jangan mengecil saat ruang sempit */
            height: 290px; /* Tinggi 290px */
            background: /* Latar belakang berlapis (dua lapisan di bawah) */
                radial-gradient(rgba(255,255,255,0.09) 1px, transparent 1.5px) 0 0 / 16px 16px, /* Lapisan 1: pola titik-titik halus */
                linear-gradient(160deg, #0a2a54 0%, #14417f 55%, #0a2a54 100%); /* Lapisan 2: gradasi warna biru tua */
            color: #fff; /* Warna teks #fff */
            padding: 26px 22px; /* Jarak dalam elemen 26px 22px */
            display: flex; /* Susun isi dengan flexbox */
            flex-direction: column; /* Susun isi ke bawah (kolom) */
            justify-content: space-between; /* Posisi isi searah baris: space-between */
            clip-path: path('M0,0 L270,0 C200,67 200,103 270,140 C338,172 338,207 270,290 L0,290 Z'); /* Potong bentuk kotak menjadi lengkung gelombang */
        } /* Akhir aturan CSS */

        .ktm-left::after { /* Gaya untuk sisi kiri KTM (elemen tambahan setelah isi) */
            content: "🎓"; /* Isi elemen tambahan: "🎓" */
            position: absolute; /* Posisi elemen: absolute */
            right: -22px; /* Jarak dari kanan -22px */
            bottom: -30px; /* Jarak dari bawah -30px */
            font-size: 150px; /* Ukuran huruf 150px */
            opacity: 0.08; /* Transparansi 0.08 */
            transform: rotate(-12deg); /* Ubah bentuk/putar elemen: rotate(-12deg) */
            pointer-events: none; /* Abaikan klik (tidak bisa diklik) */
        } /* Akhir aturan CSS */

        .ktm-logo-wrap { /* Gaya untuk area logo KTM */
            display: flex; /* Susun isi dengan flexbox */
            align-items: center; /* Posisi isi tegak lurus: center */
            gap: 8px; /* Jarak antar elemen 8px */
        } /* Akhir aturan CSS */

        .ktm-logo-badge { /* Gaya untuk lencana logo KTM */
            width: 40px; /* Lebar 40px */
            height: 40px; /* Tinggi 40px */
            border-radius: 50%; /* Sudut melengkung 50% */
            background: rgba(255,255,255,0.15); /* Latar belakang rgba(255,255,255,0.15) */
            border: 1px solid rgba(255,255,255,0.45); /* Garis tepi 1px solid rgba(255,255,255,0.45) */
            display: flex; /* Susun isi dengan flexbox */
            align-items: center; /* Posisi isi tegak lurus: center */
            justify-content: center; /* Posisi isi searah baris: center */
            font-size: 20px; /* Ukuran huruf 20px */
            flex-shrink: 0; /* Jangan mengecil saat ruang sempit */
        } /* Akhir aturan CSS */

        .ktm-logo-text { /* Gaya untuk teks logo KTM */
            font-size: 11px; /* Ukuran huruf 11px */
            line-height: 1.3; /* Tinggi baris 1.3 */
            opacity: 0.85; /* Transparansi 0.85 */
            letter-spacing: .3px; /* Jarak antar huruf .3px */
        } /* Akhir aturan CSS */

        .ktm-masa-berlaku { /* Gaya untuk masa berlaku KTM */
            position: relative; /* Posisi elemen: relative */
            z-index: 1; /* Urutan tumpukan 1 (makin besar makin di atas) */
            font-size: 12px; /* Ukuran huruf 12px */
            line-height: 1.5; /* Tinggi baris 1.5 */
            opacity: 0.9; /* Transparansi 0.9 */
            display: flex; /* Susun isi dengan flexbox */
            align-items: center; /* Posisi isi tegak lurus: center */
            gap: 8px; /* Jarak antar elemen 8px */
        } /* Akhir aturan CSS */

        .ktm-masa-icon { /* Gaya untuk ikon masa berlaku */
            width: 18px; /* Lebar 18px */
            height: 18px; /* Tinggi 18px */
            border: 2px solid rgba(255,255,255,.6); /* Garis tepi 2px solid rgba(255,255,255,.6) */
            border-radius: 50%; /* Sudut melengkung 50% */
            flex-shrink: 0; /* Jangan mengecil saat ruang sempit */
        } /* Akhir aturan CSS */

        .ktm-right { /* Gaya untuk sisi kanan KTM */
            position: relative; /* Posisi elemen: relative */
            flex: 1; /* Isi sisa ruang yang tersedia */
            background: #fff; /* Latar belakang #fff */
            padding: 28px 32px; /* Jarak dalam elemen 28px 32px */
            overflow: hidden; /* Potong isi yang keluar dari kotak */
        } /* Akhir aturan CSS */

        .ktm-right::before { /* Gaya untuk sisi kanan KTM (elemen tambahan sebelum isi) */
            content: ""; /* Isi elemen tambahan: "" */
            position: absolute; /* Posisi elemen: absolute */
            top: 0; /* Jarak dari atas 0 */
            left: 0; /* Jarak dari kiri 0 */
            right: 0; /* Jarak dari kanan 0 */
            height: 5px; /* Tinggi 5px */
            background: linear-gradient(90deg, #0a2a54, #b23a55, #0a2a54); /* Latar belakang linear-gradient(90deg, #0a2a54, #b23a55, #0a2a54) */
        } /* Akhir aturan CSS */

        .ktm-header span { /* Gaya untuk header KTM - elemen span */
            font-size: 11px; /* Ukuran huruf 11px */
            color: #8a93a3; /* Warna teks #8a93a3 */
            text-transform: uppercase; /* Ubah teks jadi huruf kapital */
            letter-spacing: 1.5px; /* Jarak antar huruf 1.5px */
        } /* Akhir aturan CSS */

        .ktm-header h2 { /* Gaya untuk header KTM - elemen h2 */
            margin: 4px 0 20px 0; /* Jarak luar elemen 4px 0 20px 0 */
            color: #0a2a54; /* Warna teks #0a2a54 */
            font-size: 23px; /* Ukuran huruf 23px */
            font-weight: 700; /* Ketebalan huruf 700 */
        } /* Akhir aturan CSS */

        .ktm-body { /* Gaya untuk isi KTM */
            display: flex; /* Susun isi dengan flexbox */
            justify-content: space-between; /* Posisi isi searah baris: space-between */
            align-items: flex-start; /* Posisi isi tegak lurus: flex-start */
            gap: 20px; /* Jarak antar elemen 20px */
        } /* Akhir aturan CSS */

        .ktm-detail { /* Gaya untuk rincian data KTM */
            display: flex; /* Susun isi dengan flexbox */
            flex-direction: column; /* Susun isi ke bawah (kolom) */
            gap: 13px; /* Jarak antar elemen 13px */
        } /* Akhir aturan CSS */

        .ktm-field span { /* Gaya untuk satu baris data KTM - elemen span */
            display: block; /* Tampil sebagai blok */
            color: #8a93a3; /* Warna teks #8a93a3 */
            font-size: 11px; /* Ukuran huruf 11px */
            text-transform: uppercase; /* Ubah teks jadi huruf kapital */
            letter-spacing: .5px; /* Jarak antar huruf .5px */
        } /* Akhir aturan CSS */

        .ktm-field strong { /* Gaya untuk satu baris data KTM - elemen strong */
            color: #1a1a1a; /* Warna teks #1a1a1a */
            font-size: 16px; /* Ukuran huruf 16px */
            font-weight: 700; /* Ketebalan huruf 700 */
        } /* Akhir aturan CSS */

        .ktm-photo { /* Gaya untuk foto KTM */
            width: 112px; /* Lebar 112px */
            height: 142px; /* Tinggi 142px */
            border-radius: 12px; /* Sudut melengkung 12px */
            overflow: hidden; /* Potong isi yang keluar dari kotak */
            background: #eee; /* Latar belakang #eee */
            border: 4px solid #fff; /* Garis tepi 4px solid #fff */
            box-shadow: 0 4px 14px rgba(0,0,0,.2); /* Bayangan 0 4px 14px rgba(0,0,0,.2) */
            flex-shrink: 0; /* Jangan mengecil saat ruang sempit */
            margin-top: 4px; /* Jarak luar atas 4px */
            display: flex; /* Susun isi dengan flexbox */
            align-items: center; /* Posisi isi tegak lurus: center */
            justify-content: center; /* Posisi isi searah baris: center */
        } /* Akhir aturan CSS */

        .ktm-photo img { /* Gaya untuk foto KTM - elemen img */
            width: 100%; /* Lebar 100% */
            height: 100%; /* Tinggi 100% */
            object-fit: cover; /* Gambar menutupi area tanpa gepeng */
        } /* Akhir aturan CSS */

        .ktm-photo-placeholder { /* Gaya untuk foto pengganti */
            font-size: 40px; /* Ukuran huruf 40px */
            color: #aaa; /* Warna teks #aaa */
        } /* Akhir aturan CSS */

        @media (max-width: 800px) { /* Aturan khusus layar kecil (maks 800px) */
            .ktm-card { /* Gaya untuk kartu KTM */
                width: 640px; /* Lebar 640px */
            } /* Akhir aturan CSS */
        } /* Akhir aturan CSS */

        .ktm-logo-badge { /* Gaya untuk lencana logo KTM */
            font-size: 0; /* Ukuran huruf 0 */
            background: #ffffff url("data:image/png;base64,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") center / 82% no-repeat; /* Latar belakang gambar logo (data base64) di tengah */
            border: 2px solid rgba(255,255,255,0.75); /* Garis tepi 2px solid rgba(255,255,255,0.75) */
        } /* Akhir aturan CSS */

    </style> <!-- Tutup blok CSS -->

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


            <a
                href="kartu_mahasiswa.php"
                class="active"
            > <!-- Link menu Kartu Mahasiswa (menu aktif) -->

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



    <!-- ==================================================
         MAIN CONTENT
    ================================================== -->

    <main class="main-content"> <!-- Konten utama halaman -->


        <!-- HEADER -->

        <header class="dashboard-header"> <!-- Header halaman -->


            <div> <!-- Wadah -->

                <h1> <!-- Judul utama -->
                    Kartu Mahasiswa <!-- Teks judul halaman -->
                </h1> <!-- Tutup judul utama -->

                <p> <!-- Paragraf -->
                    Kartu Tanda Mahasiswa digital kamu <!-- Teks paragraf -->
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


        <!-- KARTU -->

        <section class="content-card"> <!-- Bagian: kartu konten -->

            <div class="section-title"> <!-- Wadah: judul bagian -->

                <div> <!-- Wadah -->

                    <h2> <!-- Judul bagian -->
                        🎓 Kartu Tanda Mahasiswa <!-- Teks judul bagian -->
                    </h2> <!-- Tutup judul bagian -->

                    <p> <!-- Paragraf -->

                        <?php if ($sudah_diterima):  // Kalau sudah diterima ?>

                            Tunjukkan kartu ini sebagai bukti <!-- Teks paragraf -->
                            kamu sudah terdaftar sebagai <!-- Teks paragraf -->
                            mahasiswa. <!-- Teks paragraf -->

                        <?php elseif ($status_daftar === 'Ditolak'):  // Kalau status 'Ditolak' ?>

                            Status daftar ulang kamu. <!-- Teks paragraf -->

                        <?php else:  // Kalau kondisi di atas tidak terpenuhi ?>

                            Status daftar ulang kamu saat ini. <!-- Teks paragraf -->

                        <?php endif;  // Akhir blok if ?>

                    </p> <!-- Tutup paragraf -->

                </div> <!-- Tutup wadah -->

                <a
                    href="daftar_ulang.php"
                    class="btn-edit"
                > <!-- Tombol menuju Daftar Ulang -->
                    ← Kembali ke Daftar Ulang <!-- Teks pada link/tombol -->
                </a> <!-- Tutup link Daftar Ulang -->

            </div> <!-- Tutup judul bagian -->


            <?php if ($status_daftar === 'Menunggu'):  // Kalau status 'Menunggu' ?>


                <!-- ==================================================
                     STATUS: MENUNGGU KONFIRMASI ADMIN
                ================================================== -->

                <div class="info-box"> <!-- Wadah: kotak informasi -->

                    <h3> <!-- Sub-judul -->
                        ⏳ Menunggu Konfirmasi <!-- Teks sub-judul -->
                    </h3> <!-- Tutup sub-judul -->

                    <p> <!-- Paragraf -->
                        Data daftar ulang kamu sudah <!-- Teks paragraf -->
                        terkirim dan sedang diperiksa <!-- Teks paragraf -->
                        oleh Admin. Kartu Tanda Mahasiswa <!-- Teks paragraf -->
                        akan muncul otomatis di halaman <!-- Teks paragraf -->
                        ini setelah daftar ulang kamu <!-- Teks paragraf -->
                        disetujui. <!-- Teks paragraf -->
                    </p> <!-- Tutup paragraf -->

                </div> <!-- Tutup kotak informasi -->


            <?php elseif ($status_daftar === 'Ditolak'):  // Kalau status 'Ditolak' ?>


                <!-- ==================================================
                     STATUS: DITOLAK
                ================================================== -->

                <div class="info-box"> <!-- Wadah: kotak informasi -->

                    <h3> <!-- Sub-judul -->
                        ✕ Daftar Ulang Ditolak <!-- Teks sub-judul -->
                    </h3> <!-- Tutup sub-judul -->

                    <p> <!-- Paragraf -->
                        Maaf, daftar ulang kamu ditolak <!-- Teks paragraf -->
                        oleh Admin sehingga Kartu Tanda <!-- Teks paragraf -->
                        Mahasiswa belum bisa diterbitkan. <!-- Teks paragraf -->
                        Silakan hubungi Admin untuk info <!-- Teks paragraf -->
                        lebih lanjut atau lakukan daftar <!-- Teks paragraf -->
                        ulang kembali. <!-- Teks paragraf -->
                    </p> <!-- Tutup paragraf -->

                    <br> <!-- Baris baru (jarak) -->

                    <a
                        href="daftar_ulang.php"
                        class="btn-primary"
                    > <!-- Tombol utama menuju Daftar Ulang -->
                        Isi Ulang Data Daftar Ulang <!-- Teks pada link/tombol -->
                    </a> <!-- Tutup link Daftar Ulang -->

                </div> <!-- Tutup kotak informasi -->


            <?php else:  // Kalau kondisi di atas tidak terpenuhi ?>


            <!-- ==================================================
                 KARTU TANDA MAHASISWA DIGITAL
            ================================================== -->

            <div class="ktm-wrapper"> <!-- Wadah: pembungkus kartu KTM -->

            <div class="ktm-card"> <!-- Wadah: kartu KTM -->

                <div class="ktm-left"> <!-- Wadah: sisi kiri KTM -->

                    <div class="ktm-logo-wrap"> <!-- Wadah: area logo KTM -->
                        <div class="ktm-logo-badge"></div> <!-- Wadah: lencana logo KTM -->
                        <div class="ktm-logo-text"> <!-- Wadah: teks logo KTM -->
                            Universitas<br>NaraDipa <!-- Baris baru (jarak) -->
                        </div> <!-- Tutup teks logo KTM -->
                    </div> <!-- Tutup area logo KTM -->

                    <div class="ktm-masa-berlaku"> <!-- Wadah: masa berlaku KTM -->
                        <span class="ktm-masa-icon"></span> <!-- Teks keterangan: ikon masa berlaku -->
                        <span> <!-- Teks keterangan -->
                            Masa berlaku kartu<br> <!-- Baris baru (jarak) -->
                            <strong> <!-- Teks tebal -->
                                <?= date( // Tampilkan tanggal dengan format tertentu
                                    'F Y', // Format: nama bulan dan tahun
                                    strtotime( // Ubah teks tanggal jadi angka waktu
                                        '+4 years', // Tambah 4 tahun (masa berlaku kartu)
                                        strtotime( // Ubah teks tanggal jadi angka waktu
                                            $daftar_ulang['tanggal_daftar_ulang'] // Data tanggal daftar ulang dari array daftar_ulang
                                        ) // Penutup tanda kurung
                                    ) // Penutup tanda kurung
                                );  // Akhir pemanggilan fungsi ?>
                            </strong> <!-- Tutup teks tebal -->
                        </span> <!-- Tutup teks keterangan -->
                    </div> <!-- Tutup masa berlaku KTM -->

                </div> <!-- Tutup sisi kiri KTM -->

                <div class="ktm-right"> <!-- Wadah: sisi kanan KTM -->

                    <div class="ktm-header"> <!-- Wadah: header KTM -->
                        <span>Universitas NaraDipa</span> <!-- Teks keterangan berisi "Universitas NaraDipa" -->
                        <h2>Kartu Tanda Mahasiswa</h2> <!-- Judul bagian berisi "Kartu Tanda Mahasiswa" -->
                    </div> <!-- Tutup header KTM -->

                    <div class="ktm-body"> <!-- Wadah: isi KTM -->

                        <div class="ktm-detail"> <!-- Wadah: rincian data KTM -->

                            <div class="ktm-field"> <!-- Wadah: satu baris data KTM -->
                                <span>Nama :</span> <!-- Teks keterangan berisi "Nama :" -->
                                <strong><?= htmlspecialchars($user['nama']); ?></strong> <!-- Teks tebal, tampilkan nama lengkap (di-escape agar aman dari XSS) -->
                            </div> <!-- Tutup satu baris data KTM -->

                            <div class="ktm-field"> <!-- Wadah: satu baris data KTM -->
                                <span>NIM :</span> <!-- Teks keterangan berisi "NIM :" -->
                                <strong><?= htmlspecialchars($user['nim']); ?></strong> <!-- Teks tebal, tampilkan NIM (di-escape agar aman dari XSS) -->
                            </div> <!-- Tutup satu baris data KTM -->

                            <div class="ktm-field"> <!-- Wadah: satu baris data KTM -->
                                <span>Program Studi :</span> <!-- Teks keterangan berisi "Program Studi :" -->
                                <strong><?= htmlspecialchars($user['prodi'] ?? '-'); ?></strong> <!-- Teks tebal, tampilkan program studi (di-escape agar aman dari XSS), tampil '-' jika kosong -->
                            </div> <!-- Tutup satu baris data KTM -->

                            <div class="ktm-field"> <!-- Wadah: satu baris data KTM -->
                                <span>Email :</span> <!-- Teks keterangan berisi "Email :" -->
                                <strong><?= htmlspecialchars($daftar_ulang['email'] ?? '-'); ?></strong> <!-- Teks tebal, tampilkan email (di-escape agar aman dari XSS), tampil '-' jika kosong -->
                            </div> <!-- Tutup satu baris data KTM -->

                        </div> <!-- Tutup rincian data KTM -->

                        <div class="ktm-photo"> <!-- Wadah: foto KTM -->

                            <?php if (!empty($daftar_ulang['foto'])):  // Kalau foto ada, tampilkan fotonya ?>

                                <img
                                    src="../uploads/foto_mahasiswa/<?= htmlspecialchars($daftar_ulang['foto']); ?>"
                                    alt="Foto Mahasiswa"
                                > <!-- Gambar foto mahasiswa -->

                            <?php else:  // Kalau kondisi di atas tidak terpenuhi ?>

                                <div class="ktm-photo-placeholder">👤</div> <!-- Wadah: foto pengganti berisi "👤" -->

                            <?php endif;  // Akhir blok if ?>

                        </div> <!-- Tutup foto KTM -->

                    </div> <!-- Tutup isi KTM -->

                </div> <!-- Tutup sisi kanan KTM -->

            </div> <!-- Tutup kartu KTM -->

            </div> <!-- Tutup pembungkus kartu KTM -->


            <?php endif;  // Akhir blok if ?>

        </section> <!-- Tutup kartu konten -->

    </main> <!-- Tutup konten utama halaman -->

</div> <!-- Tutup pembungkus layout dashboard -->

</body> <!-- Tutup isi halaman (body) -->

</html> <!-- Tutup tag html -->

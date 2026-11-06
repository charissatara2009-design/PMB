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
                    nomor_test,
                    prodi,
                    status_test,
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
// CEK DATA DAFTAR ULANG
// ==================================================

// Query SQL: ambil data dari tabel daftar_ulang milik user yang login, ambil yang paling baru saja
$query_daftar = "SELECT
                     id,
                     tanggal_daftar_ulang,
                     status,
                     no_hp,
                     foto,
                     tempat_lahir,
                     tanggal_lahir,
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
// PROSES DAFTAR ULANG
// ==================================================

if (isset($_POST['daftar_ulang'])) { // Jalankan proses kalau tombol Daftar Ulang ditekan


    // Hanya user yang lulus yang boleh daftar ulang

    if ($user['status_test'] !== 'Lulus') { // Kalau status test bukan 'Lulus'

        // Tampilkan alert "Kamu belum dinyatakan lulus sehingga belum dapat melakukan daftar ulang." lalu pindah ke daftar_ulang.php
        echo "<script>

                alert(
                    'Kamu belum dinyatakan lulus sehingga belum dapat melakukan daftar ulang.'
                );

                window.location='daftar_ulang.php';

              </script>";

        exit; // Hentikan proses halaman di sini
    } // Akhir blok if


    // Cek apakah sudah pernah melakukan daftar ulang

    if ($daftar_ulang) { // Kalau sudah pernah daftar ulang

        // Tampilkan alert "Kamu sudah melakukan daftar ulang." lalu pindah ke daftar_ulang.php
        echo "<script>

                alert(
                    'Kamu sudah melakukan daftar ulang.'
                );

                window.location='daftar_ulang.php';

              </script>";

        exit; // Hentikan proses halaman di sini
    } // Akhir blok if


    // ==================================================
    // VALIDASI FORM TAMBAHAN
    // ==================================================

    $no_hp        = trim($_POST['no_hp'] ?? ''); // Ambil nomor HP dari form (spasi dibuang)
    $tempat_lahir = trim($_POST['tempat_lahir'] ?? ''); // Ambil tempat lahir dari form
    $tanggal_lahir = trim($_POST['tanggal_lahir'] ?? ''); // Ambil tanggal lahir dari form
    $email        = trim($_POST['email'] ?? ''); // Ambil email dari form

    if ( // Mulai pengecekan kondisi (if)
        $no_hp === '' || // Nomor HP kosong, atau
        $tempat_lahir === '' || // tempat lahir kosong, atau
        $tanggal_lahir === '' || // tanggal lahir kosong, atau
        $email === '' // email kosong
    ) { // Akhir kondisi; blok di bawah jalan jika terpenuhi

        // Tampilkan alert "Semua data wajib diisi sebelum daftar ulang!" lalu pindah ke daftar_ulang.php
        echo "<script>
                alert('Semua data wajib diisi sebelum daftar ulang!');
                window.location='daftar_ulang.php';
              </script>";

        exit; // Hentikan proses halaman di sini
    } // Akhir blok if

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { // Kalau format email tidak valid

        // Tampilkan alert "Format email tidak valid!" lalu pindah ke daftar_ulang.php
        echo "<script>
                alert('Format email tidak valid!');
                window.location='daftar_ulang.php';
              </script>";

        exit; // Hentikan proses halaman di sini
    } // Akhir blok if


    // ==================================================
    // UPLOAD FOTO MAHASISWA
    // ==================================================

    if ( // Mulai pengecekan kondisi (if)
        !isset($_FILES['foto']) || // Foto tidak dikirim, atau
        $_FILES['foto']['error'] !== UPLOAD_ERR_OK // upload foto mengalami error
    ) { // Akhir kondisi; blok di bawah jalan jika terpenuhi

        // Tampilkan alert "Foto mahasiswa wajib diunggah!" lalu pindah ke daftar_ulang.php
        echo "<script>
                alert('Foto mahasiswa wajib diunggah!');
                window.location='daftar_ulang.php';
              </script>";

        exit; // Hentikan proses halaman di sini
    } // Akhir blok if

    $allowed_ext = ['jpg', 'jpeg', 'png']; // Daftar ekstensi foto yang diizinkan

    $ext = strtolower( // Ambil ekstensi file foto dalam huruf kecil
        pathinfo( // Ambil informasi dari nama file
            $_FILES['foto']['name'], // Nama file foto yang diunggah
            PATHINFO_EXTENSION // Yang diambil hanya ekstensinya (jpg, png, dst)
        ) // Penutup tanda kurung
    ); // Akhir pemanggilan fungsi

    if (!in_array($ext, $allowed_ext)) { // Kalau ekstensi tidak termasuk yang diizinkan

        // Tampilkan alert "Format foto harus JPG atau PNG!" lalu pindah ke daftar_ulang.php
        echo "<script>
                alert('Format foto harus JPG atau PNG!');
                window.location='daftar_ulang.php';
              </script>";

        exit; // Hentikan proses halaman di sini
    } // Akhir blok if

    if ($_FILES['foto']['size'] > 2 * 1024 * 1024) { // Kalau ukuran foto lebih dari 2 MB

        // Tampilkan alert "Ukuran foto maksimal 2MB!" lalu pindah ke daftar_ulang.php
        echo "<script>
                alert('Ukuran foto maksimal 2MB!');
                window.location='daftar_ulang.php';
              </script>";

        exit; // Hentikan proses halaman di sini
    } // Akhir blok if

    $upload_dir = "../uploads/foto_mahasiswa/"; // Folder tujuan penyimpanan foto

    if (!is_dir($upload_dir)) { // Kalau folder belum ada
        mkdir($upload_dir, 0755, true); // Buat foldernya
    } // Akhir blok if

    $nama_file = "foto_" . $user_id . "_" . time() . "." . $ext; // Buat nama file unik: foto_IDuser_waktu.ekstensi

    $target_path = $upload_dir . $nama_file; // Gabungkan folder dan nama file jadi lokasi simpan

    if ( // Mulai pengecekan kondisi (if)
        !move_uploaded_file( // Gagal memindahkan file ke folder tujuan?
            $_FILES['foto']['tmp_name'], // Lokasi file sementara hasil upload
            $target_path // Lokasi tujuan penyimpanan
        ) // Penutup tanda kurung
    ) { // Akhir kondisi; blok di bawah jalan jika terpenuhi

        // Tampilkan alert "Gagal mengunggah foto!" lalu pindah ke daftar_ulang.php
        echo "<script>
                alert('Gagal mengunggah foto!');
                window.location='daftar_ulang.php';
              </script>";

        exit; // Hentikan proses halaman di sini
    } // Akhir blok if


    // ==================================================
    // SIMPAN DAFTAR ULANG
    // ==================================================

    // Query SQL: simpan data baru ke tabel daftar_ulang dengan status awal 'Menunggu'
    $query_insert = "INSERT INTO daftar_ulang
                     (
                         user_id,
                         no_hp,
                         foto,
                         tempat_lahir,
                         tanggal_lahir,
                         email,
                         status
                     )
                     VALUES (?, ?, ?, ?, ?, ?, 'Menunggu')";

    $stmt_insert = mysqli_prepare( // Siapkan query (prepared statement) agar aman dari SQL injection
        $conn, // Koneksi database
        $query_insert // Teks query SQL yang akan dijalankan
    ); // Akhir pemanggilan fungsi

    mysqli_stmt_bind_param( // Isi tanda tanya (?) pada query dengan nilai yang aman
        $stmt_insert, // Statement query yang sudah disiapkan
        "isssss", // Tipe data tiap nilai (i = angka bulat, d = desimal, s = teks)
        $user_id, // ID user yang sedang login
        $no_hp, // Nilai: nomor HP
        $nama_file, // Nilai: nama file foto
        $tempat_lahir, // Nilai: tempat lahir
        $tanggal_lahir, // Nilai: tanggal lahir
        $email // Nilai: email
    ); // Akhir pemanggilan fungsi


    if ( // Mulai pengecekan kondisi (if)
        mysqli_stmt_execute( // Jalankan query
            $stmt_insert // Statement query yang sudah disiapkan
        ) // Penutup tanda kurung
    ) { // Akhir kondisi; blok di bawah jalan jika terpenuhi


        // ==================================================
        // UPDATE STATUS DI USERS
        // ==================================================

        // Query SQL: ubah data tabel users (status_daftar_ulang = 'Sudah')
        $query_update = "UPDATE users
                         SET status_daftar_ulang = 'Sudah'
                         WHERE id = ?";

        $stmt_update = mysqli_prepare( // Siapkan query (prepared statement) agar aman dari SQL injection
            $conn, // Koneksi database
            $query_update // Teks query SQL yang akan dijalankan
        ); // Akhir pemanggilan fungsi

        mysqli_stmt_bind_param( // Isi tanda tanya (?) pada query dengan nilai yang aman
            $stmt_update, // Statement query yang sudah disiapkan
            "i", // Tipe data tiap nilai (i = angka bulat, d = desimal, s = teks)
            $user_id // ID user yang sedang login
        ); // Akhir pemanggilan fungsi

        mysqli_stmt_execute( // Jalankan query
            $stmt_update // Statement query yang sudah disiapkan
        ); // Akhir pemanggilan fungsi


        // Tampilkan alert "Daftar ulang berhasil dikirim!" lalu pindah ke kartu_mahasiswa.php
        echo "<script>

                alert(
                    'Daftar ulang berhasil dikirim!'
                );

                window.location='kartu_mahasiswa.php';

              </script>";

        exit; // Hentikan proses halaman di sini

    } else { // Kalau kondisi di atas tidak terpenuhi

        // Tampilkan alert "Daftar ulang gagal dikirim!"
        echo "<script>

                alert(
                    'Daftar ulang gagal dikirim!'
                );

              </script>";

    } // Akhir blok else

} // Akhir blok if

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

    <style> /* Awal blok CSS (gaya khusus halaman ini) */

        .daftarulang-form .data-item { /* Gaya untuk satu item data */
            display: flex; /* Susun isi dengan flexbox */
            flex-direction: column; /* Susun isi ke bawah (kolom) */
            gap: 8px; /* Jarak antar elemen 8px */
            padding: 16px 18px 18px; /* Jarak dalam elemen 16px 18px 18px */
        } /* Akhir aturan CSS */

        .daftarulang-form .data-item label { /* Gaya untuk satu item data - elemen label */
            font-size: 12px; /* Ukuran huruf 12px */
            font-weight: 700; /* Ketebalan huruf 700 */
            letter-spacing: .4px; /* Jarak antar huruf .4px */
            text-transform: uppercase; /* Ubah teks jadi huruf kapital */
            color: #8a2740; /* Warna teks #8a2740 */
        } /* Akhir aturan CSS */

        .daftarulang-form .data-item input { /* Gaya untuk satu item data - elemen input */
            width: 100%; /* Lebar 100% */
            padding: 12px 14px; /* Jarak dalam elemen 12px 14px */
            border: 1.5px solid #f0e2e6; /* Garis tepi 1.5px solid #f0e2e6 */
            border-radius: 12px; /* Sudut melengkung 12px */
            background: #ffffff; /* Latar belakang #ffffff */
            color: #2b2229; /* Warna teks #2b2229 */
            font-size: 14px; /* Ukuran huruf 14px */
            font-family: inherit; /* Jenis huruf inherit */
            outline: none; /* Hilangkan garis fokus bawaan browser */
            transition: border-color 0.2s, box-shadow 0.2s; /* Perubahan halus (animasi) pada border-color 0.2s, box-shadow 0.2s */
        } /* Akhir aturan CSS */

        .daftarulang-form .data-item input::placeholder { /* Gaya untuk satu item data - elemen input (teks petunjuk) */
            color: #b9aeb2; /* Warna teks #b9aeb2 */
        } /* Akhir aturan CSS */

        .daftarulang-form .data-item input:focus { /* Gaya untuk satu item data - elemen input (saat dipilih/fokus) */
            border-color: #b23a55; /* Warna garis tepi #b23a55 */
            box-shadow: 0 0 0 4px rgba(178, 58, 85, 0.12); /* Bayangan 0 0 0 4px rgba(178, 58, 85, 0.12) */
        } /* Akhir aturan CSS */

        .daftarulang-form .data-item input[readonly] { /* Gaya untuk satu item data - elemen input[readonly] (saat hanya baca) */
            background: #f6f1f3; /* Latar belakang #f6f1f3 */
            color: #6b5c61; /* Warna teks #6b5c61 */
            cursor: not-allowed; /* Kursor tanda dilarang */
        } /* Akhir aturan CSS */

        .daftarulang-form .daftarulang-full { /* Gaya untuk kolom selebar penuh */
            grid-column: 1 / -1; /* Bentang selebar semua kolom */
        } /* Akhir aturan CSS */

        .daftarulang-form input[type="file"] { /* Gaya untuk form daftar ulang - elemen input[type="file"] */
            padding: 10px; /* Jarak dalam elemen 10px */
            border-style: dashed; /* Gaya garis tepi dashed */
            background: #fffafb; /* Latar belakang #fffafb */
            cursor: pointer; /* Kursor berbentuk tangan (bisa diklik) */
        } /* Akhir aturan CSS */

        .daftarulang-form input[type="file"]::file-selector-button { /* Gaya untuk form daftar ulang - elemen input[type="file"] (tombol pilih file) */
            margin-right: 12px; /* Jarak luar kanan 12px */
            padding: 8px 16px; /* Jarak dalam elemen 8px 16px */
            border: none; /* Tanpa garis tepi */
            border-radius: 8px; /* Sudut melengkung 8px */
            background: linear-gradient(135deg, #b23a55, #8a2740); /* Latar belakang linear-gradient(135deg, #b23a55, #8a2740) */
            color: #ffffff; /* Warna teks #ffffff */
            font-weight: 600; /* Ketebalan huruf 600 */
            font-size: 13px; /* Ukuran huruf 13px */
            font-family: inherit; /* Jenis huruf inherit */
            cursor: pointer; /* Kursor berbentuk tangan (bisa diklik) */
        } /* Akhir aturan CSS */

        .daftarulang-form .daftarulang-hint { /* Gaya untuk petunjuk kecil */
            font-size: 12px; /* Ukuran huruf 12px */
            color: #8f8690; /* Warna teks #8f8690 */
            text-transform: none; /* Teks tampil apa adanya */
            letter-spacing: 0; /* Jarak antar huruf 0 */
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


            <a
                href="daftar_ulang.php"
                class="active"
            > <!-- Link menu Daftar Ulang (menu aktif) -->

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
                    Proses daftar ulang calon mahasiswa <!-- Teks paragraf -->
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
             STATUS TEST
        ================================================== -->

        <?php if ( // Mulai pengecekan kondisi (if)
            $user['status_test'] !== 'Lulus' // Status test bukan 'Lulus'
        ):  // Akhir kondisi, lanjut ke tampilan HTML ?>


            <section class="content-card"> <!-- Bagian: kartu konten -->

                <div class="info-box"> <!-- Wadah: kotak informasi -->

                    <h3> <!-- Sub-judul -->
                        🔒 Daftar Ulang Belum Tersedia <!-- Teks sub-judul -->
                    </h3> <!-- Tutup sub-judul -->

                    <p> <!-- Paragraf -->

                        Daftar ulang hanya dapat dilakukan <!-- Teks paragraf -->
                        oleh calon mahasiswa yang dinyatakan <!-- Teks paragraf -->
                        <strong>Lulus</strong> pada test PMB. <!-- Teks tebal berisi "Lulus pada test PMB." -->

                    </p> <!-- Tutup paragraf -->

                    <br> <!-- Baris baru (jarak) -->

                    <p> <!-- Paragraf -->

                        Status test kamu: <!-- Teks paragraf -->

                        <strong> <!-- Teks tebal -->

                            <?= htmlspecialchars( // Tampilkan teks berikut dengan aman (di-escape dari XSS)
                                $user['status_test'] // Data status test dari array user
                            );  // Akhir pemanggilan fungsi ?>

                        </strong> <!-- Tutup teks tebal -->

                    </p> <!-- Tutup paragraf -->

                </div> <!-- Tutup kotak informasi -->

            </section> <!-- Tutup kartu konten -->


        <?php else:  // Kalau kondisi di atas tidak terpenuhi ?>


            <!-- ==================================================
                 DATA CALON MAHASISWA
            ================================================== -->

            <section class="content-card"> <!-- Bagian: kartu konten -->


                <div class="section-title"> <!-- Wadah: judul bagian -->

                    <div> <!-- Wadah -->

                        <h2> <!-- Judul bagian -->
                            Data Calon Mahasiswa <!-- Teks judul bagian -->
                        </h2> <!-- Tutup judul bagian -->

                        <p> <!-- Paragraf -->
                            Pastikan data kamu sudah benar <!-- Teks paragraf -->
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
                                $user['nomor_test'] ?? '-' // Data nomor test dari array user, pakai '-' jika kosong
                            );  // Akhir pemanggilan fungsi ?>

                        </strong> <!-- Tutup teks tebal -->

                    </div> <!-- Tutup satu item data -->


                    <div class="data-item"> <!-- Wadah: satu item data -->

                        <span> <!-- Teks keterangan -->
                            Program Studi <!-- Teks keterangan -->
                        </span> <!-- Tutup teks keterangan -->

                        <strong> <!-- Teks tebal -->

                            <?= htmlspecialchars( // Tampilkan teks berikut dengan aman (di-escape dari XSS)
                                $user['prodi'] ?? '-' // Data program studi dari array user, pakai '-' jika kosong
                            );  // Akhir pemanggilan fungsi ?>

                        </strong> <!-- Tutup teks tebal -->

                    </div> <!-- Tutup satu item data -->


                    <div class="data-item"> <!-- Wadah: satu item data -->

                        <span> <!-- Teks keterangan -->
                            Status Test <!-- Teks keterangan -->
                        </span> <!-- Tutup teks keterangan -->

                        <strong> <!-- Teks tebal -->
                            Lulus <!-- Teks tebal -->
                        </strong> <!-- Tutup teks tebal -->

                    </div> <!-- Tutup satu item data -->


                </div> <!-- Tutup grid data -->

            </section> <!-- Tutup kartu konten -->



            <!-- ==================================================
                 STATUS DAFTAR ULANG
            ================================================== -->

            <section class="content-card"> <!-- Bagian: kartu konten -->


                <div class="section-title"> <!-- Wadah: judul bagian -->

                    <div> <!-- Wadah -->

                        <h2> <!-- Judul bagian -->
                            Status Daftar Ulang <!-- Teks judul bagian -->
                        </h2> <!-- Tutup judul bagian -->

                        <p> <!-- Paragraf -->
                            Informasi proses daftar ulang kamu <!-- Teks paragraf -->
                        </p> <!-- Tutup paragraf -->

                    </div> <!-- Tutup wadah -->

                </div> <!-- Tutup judul bagian -->


                <?php if (!$daftar_ulang):  // Kalau belum pernah daftar ulang, tampilkan form ?>


                    <!-- BELUM DAFTAR ULANG -->

                    <div class="info-box"> <!-- Wadah: kotak informasi -->

                        <h3> <!-- Sub-judul -->
                            📋 Kamu Belum Daftar Ulang <!-- Teks sub-judul -->
                        </h3> <!-- Tutup sub-judul -->

                        <p> <!-- Paragraf -->

                            Silakan lakukan daftar ulang <!-- Teks paragraf -->
                            untuk melanjutkan proses <!-- Teks paragraf -->
                            penerimaan mahasiswa baru. <!-- Teks paragraf -->

                        </p> <!-- Tutup paragraf -->


                        <br> <!-- Baris baru (jarak) -->


                        <form
                            method="POST"
                            enctype="multipart/form-data"
                            class="daftarulang-form"
                            onsubmit="return konfirmasiDaftarUlang();"
                        > <!-- Form kirim data (method POST), bisa upload file, konfirmasi dulu sebelum kirim -->

                            <div class="data-grid"> <!-- Wadah: grid data -->

                                <div class="data-item"> <!-- Wadah: satu item data -->
                                    <label for="nama_lengkap">Nama Lengkap</label> <!-- Label untuk kolom nama_lengkap berisi "Nama Lengkap" -->
                                    <input
                                        type="text"
                                        id="nama_lengkap"
                                        value="<?= htmlspecialchars($user['nama']); ?>"
                                        readonly
                                    > <!-- Input text "nama_lengkap" (hanya baca) -->
                                </div> <!-- Tutup satu item data -->

                                <div class="data-item"> <!-- Wadah: satu item data -->
                                    <label for="prodi">Program Studi</label> <!-- Label untuk kolom prodi berisi "Program Studi" -->
                                    <input
                                        type="text"
                                        id="prodi"
                                        value="<?= htmlspecialchars($user['prodi'] ?? '-'); ?>"
                                        readonly
                                    > <!-- Input text "prodi" (hanya baca) -->
                                </div> <!-- Tutup satu item data -->

                                <div class="data-item"> <!-- Wadah: satu item data -->
                                    <label for="no_hp">Nomor HP</label> <!-- Label untuk kolom no_hp berisi "Nomor HP" -->
                                    <input
                                        type="text"
                                        id="no_hp"
                                        name="no_hp"
                                        placeholder="Contoh: 081234567890"
                                        required
                                    > <!-- Input text "no_hp" (wajib diisi) -->
                                </div> <!-- Tutup satu item data -->

                                <div class="data-item"> <!-- Wadah: satu item data -->
                                    <label for="email">Email</label> <!-- Label untuk kolom email berisi "Email" -->
                                    <input
                                        type="email"
                                        id="email"
                                        name="email"
                                        placeholder="nama@email.com"
                                        required
                                    > <!-- Input email "email" (wajib diisi) -->
                                </div> <!-- Tutup satu item data -->

                                <div class="data-item"> <!-- Wadah: satu item data -->
                                    <label for="tempat_lahir">Tempat Lahir</label> <!-- Label untuk kolom tempat_lahir berisi "Tempat Lahir" -->
                                    <input
                                        type="text"
                                        id="tempat_lahir"
                                        name="tempat_lahir"
                                        placeholder="Contoh: Jakarta"
                                        required
                                    > <!-- Input text "tempat_lahir" (wajib diisi) -->
                                </div> <!-- Tutup satu item data -->

                                <div class="data-item"> <!-- Wadah: satu item data -->
                                    <label for="tanggal_lahir">Tanggal Lahir</label> <!-- Label untuk kolom tanggal_lahir berisi "Tanggal Lahir" -->
                                    <input
                                        type="date"
                                        id="tanggal_lahir"
                                        name="tanggal_lahir"
                                        required
                                    > <!-- Input date "tanggal_lahir" (wajib diisi) -->
                                </div> <!-- Tutup satu item data -->

                                <div class="data-item daftarulang-full"> <!-- Wadah: satu item data -->
                                    <label for="foto">Upload Foto Mahasiswa</label> <!-- Label untuk kolom foto berisi "Upload Foto Mahasiswa" -->
                                    <input
                                        type="file"
                                        id="foto"
                                        name="foto"
                                        accept=".jpg,.jpeg,.png"
                                        required
                                    > <!-- Input unggah file foto (JPG/PNG) (wajib diisi) -->
                                    <span class="daftarulang-hint"> <!-- Teks keterangan: petunjuk kecil -->
                                        Format JPG/PNG, maks 2MB, latar belakang polos. <!-- Teks keterangan -->
                                    </span> <!-- Tutup petunjuk kecil -->
                                </div> <!-- Tutup satu item data -->

                            </div> <!-- Tutup grid data -->

                            <br> <!-- Baris baru (jarak) -->

                            <button
                                type="submit"
                                name="daftar_ulang"
                                class="btn-primary"
                            > <!-- Tombol kirim form (daftar_ulang) -->

                                ✓ Daftar Ulang Sekarang <!-- Teks pada tombol -->

                            </button> <!-- Tutup tombol -->

                        </form> <!-- Tutup form -->

                    </div> <!-- Tutup kotak informasi -->


                <?php else:  // Kalau kondisi di atas tidak terpenuhi ?>


                    <!-- SUDAH DAFTAR ULANG -->

                    <div class="result-card"> <!-- Wadah: kartu hasil -->


                        <div class="result-icon"> <!-- Wadah: ikon hasil -->

                            <?php // Awal blok kode PHP

                            if ( // Mulai pengecekan kondisi (if)
                                $daftar_ulang['status'] // Data status dari array daftar_ulang
                                === 'Menunggu' // sama dengan 'Menunggu'
                            ) { // Akhir kondisi; blok di bawah jalan jika terpenuhi

                                echo "⏳"; // Tampilkan ikon jam pasir

                            } elseif ( // Kalau belum, cek kondisi lain (elseif)
                                $daftar_ulang['status'] // Data status dari array daftar_ulang
                                === 'Diterima' // sama dengan 'Diterima'
                            ) { // Akhir kondisi; blok di bawah jalan jika terpenuhi

                                echo "🎉"; // Tampilkan ikon perayaan

                            } else { // Kalau kondisi di atas tidak terpenuhi

                                echo "❌"; // Tampilkan ikon silang

                            } // Akhir blok else

                             // Akhir blok kode PHP ?>

                        </div> <!-- Tutup ikon hasil -->


                        <p class="result-label"> <!-- Paragraf: label hasil -->
                            STATUS DAFTAR ULANG <!-- Teks paragraf -->
                        </p> <!-- Tutup label hasil -->


                        <?php if ( // Mulai pengecekan kondisi (if)
                            $daftar_ulang['status'] // Data status dari array daftar_ulang
                            === 'Menunggu' // sama dengan 'Menunggu'
                        ):  // Akhir kondisi, lanjut ke tampilan HTML ?>

                            <h2 class="result-pending"> <!-- Judul bagian: teks hasil menunggu -->
                                MENUNGGU <!-- Teks judul bagian -->
                            </h2> <!-- Tutup teks hasil menunggu -->

                            <p> <!-- Paragraf -->
                                Daftar ulang kamu sedang <!-- Teks paragraf -->
                                diperiksa oleh Admin. <!-- Teks paragraf -->
                            </p> <!-- Tutup paragraf -->


                        <?php elseif ( // Kalau belum, cek kondisi lain (elseif)
                            $daftar_ulang['status'] // Data status dari array daftar_ulang
                            === 'Diterima' // sama dengan 'Diterima'
                        ):  // Akhir kondisi, lanjut ke tampilan HTML ?>

                            <h2 class="result-success"> <!-- Judul bagian: teks hasil sukses -->
                                DITERIMA <!-- Teks judul bagian -->
                            </h2> <!-- Tutup teks hasil sukses -->

                            <p> <!-- Paragraf -->
                                Selamat! Daftar ulang kamu <!-- Teks paragraf -->
                                telah diterima. <!-- Teks paragraf -->
                            </p> <!-- Tutup paragraf -->


                        <?php else:  // Kalau kondisi di atas tidak terpenuhi ?>

                            <h2 class="result-danger"> <!-- Judul bagian: teks hasil gagal -->
                                DITOLAK <!-- Teks judul bagian -->
                            </h2> <!-- Tutup teks hasil gagal -->

                            <p> <!-- Paragraf -->
                                Daftar ulang kamu ditolak. <!-- Teks paragraf -->
                                Silakan hubungi Admin untuk <!-- Teks paragraf -->
                                informasi lebih lanjut. <!-- Teks paragraf -->
                            </p> <!-- Tutup paragraf -->

                        <?php endif;  // Akhir blok if ?>


                    </div> <!-- Tutup kartu hasil -->


                    <!-- TANGGAL -->

                    <div class="data-grid"> <!-- Wadah: grid data -->


                        <div class="data-item"> <!-- Wadah: satu item data -->

                            <span> <!-- Teks keterangan -->
                                Tanggal Daftar Ulang <!-- Teks keterangan -->
                            </span> <!-- Tutup teks keterangan -->

                            <strong> <!-- Teks tebal -->

                                <?= date( // Tampilkan tanggal dengan format tertentu
                                    'd F Y, H:i', // Format: tanggal bulan tahun, jam:menit
                                    strtotime( // Ubah teks tanggal jadi angka waktu
                                        $daftar_ulang[ // Ambil dari data daftar ulang
                                            'tanggal_daftar_ulang' // kolom tanggal daftar ulang
                                        ] // Penutup kurung siku
                                    ) // Penutup tanda kurung
                                );  // Akhir pemanggilan fungsi ?>

                            </strong> <!-- Tutup teks tebal -->

                        </div> <!-- Tutup satu item data -->


                        <div class="data-item"> <!-- Wadah: satu item data -->

                            <span> <!-- Teks keterangan -->
                                Status <!-- Teks keterangan -->
                            </span> <!-- Tutup teks keterangan -->

                            <strong> <!-- Teks tebal -->

                                <?= htmlspecialchars( // Tampilkan teks berikut dengan aman (di-escape dari XSS)
                                    $daftar_ulang['status'] // Data status dari array daftar_ulang
                                );  // Akhir pemanggilan fungsi ?>

                            </strong> <!-- Tutup teks tebal -->

                        </div> <!-- Tutup satu item data -->


                    </div> <!-- Tutup grid data -->


                    <?php if ( // Mulai pengecekan kondisi (if)
                        $daftar_ulang['status'] // Data status dari array daftar_ulang
                        === 'Diterima' // sama dengan 'Diterima'
                        && // dan
                        !empty($user['nim']) // NIM sudah terisi
                    ):  // Akhir kondisi, lanjut ke tampilan HTML ?>

                        <div class="info-box"> <!-- Wadah: kotak informasi -->

                            <h3> <!-- Sub-judul -->
                                🎓 Selamat! <!-- Teks sub-judul -->
                            </h3> <!-- Tutup sub-judul -->

                            <p> <!-- Paragraf -->
                                Kamu sudah diterima sebagai <!-- Teks paragraf -->
                                mahasiswa baru. Kartu Tanda <!-- Teks paragraf -->
                                Mahasiswa digital kamu sudah <!-- Teks paragraf -->
                                aktif dan bisa dilihat di <!-- Teks paragraf -->
                                halaman khusus. <!-- Teks paragraf -->
                            </p> <!-- Tutup paragraf -->

                            <br> <!-- Baris baru (jarak) -->

                            <a
                                href="kartu_mahasiswa.php"
                                class="btn-primary"
                            > <!-- Tombol utama menuju Kartu Mahasiswa -->

                                🎓 Lihat Kartu Mahasiswa <!-- Teks pada link/tombol -->

                            </a> <!-- Tutup link Kartu Mahasiswa -->

                            <a
                                href="nomor_induk.php"
                                class="btn-edit"
                                style="margin-left:8px;"
                            > <!-- Tombol menuju Nomor Induk -->

                                Lihat Nomor Induk <!-- Teks pada link/tombol -->

                            </a> <!-- Tutup link Nomor Induk -->

                        </div> <!-- Tutup kotak informasi -->

                    <?php endif;  // Akhir blok if ?>


                <?php endif;  // Akhir blok if ?>


            </section> <!-- Tutup kartu konten -->


        <?php endif;  // Akhir blok if ?>


    </main> <!-- Tutup konten utama halaman -->

</div> <!-- Tutup pembungkus layout dashboard -->



<script> // Awal blok JavaScript

function konfirmasiDaftarUlang() // Fungsi: minta konfirmasi sebelum daftar ulang
{ // Awal blok kode
    return confirm( // Tampilkan kotak konfirmasi OK/Batal
        "Apakah kamu yakin ingin melakukan daftar ulang?" // Teks pertanyaannya
    ); // Akhir pemanggilan fungsi
} // Akhir blok kode

</script> <!-- Tutup blok JavaScript -->


</body> <!-- Tutup isi halaman (body) -->

</html> <!-- Tutup tag html -->
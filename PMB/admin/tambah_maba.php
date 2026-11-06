<?php // Awal blok kode PHP

require_once "../config/auth.php"; // Muat file pengecekan login (auth)
require_once "../config/database.php"; // Muat file koneksi database

cekAdmin(); // Pastikan yang masuk adalah admin, kalau bukan diarahkan keluar


// ==================================================
// TAMBAH DATA CALON MABA
// ==================================================

if (isset($_POST['tambah'])) { // Jalankan jika tombol Simpan ditekan

    $nama = $_POST['nama']; // Ambil nama dari form
    $nik = $_POST['nik']; // Ambil nik dari form
    $email = $_POST['email']; // Ambil email dari form
    $no_hp = $_POST['no_hp']; // Ambil no hp dari form
    $asal_sekolah = $_POST['asal_sekolah']; // Ambil asal sekolah dari form
    $prodi = $_POST['prodi']; // Ambil prodi dari form
    $username = $_POST['username']; // Ambil username dari form
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Ambil password dari form lalu di-hash

    // Cek username
    $cek = mysqli_prepare( // Siapkan query (prepared statement) agar aman dari SQL injection
        $conn, // Koneksi database
        "SELECT id FROM users WHERE username = ?" // Query SQL: ambil data dari tabel users dengan username tertentu (cek apakah sudah dipakai)
    ); // Akhir pemanggilan fungsi

    mysqli_stmt_bind_param( // Isi tanda tanya (?) pada query dengan nilai yang aman
        $cek, // Statement pengecekan username
        "s", // Tipe data tiap nilai (i = angka bulat, d = desimal, s = teks)
        $username // Nilai: username
    ); // Akhir pemanggilan fungsi

    mysqli_stmt_execute($cek); // Jalankan query

    $hasil_cek = mysqli_stmt_get_result($cek); // Ambil hasil query

    if (mysqli_num_rows($hasil_cek) > 0) { // Kalau username sudah dipakai

        // Tampilkan alert "Username sudah digunakan!" lalu pindah ke tambah_maba.php
        echo "<script>
                alert('Username sudah digunakan!');
                window.location='tambah_maba.php';
              </script>";

        exit; // Hentikan proses halaman di sini
    } // Akhir blok if


    // Buat nomor test otomatis
    $nomor_test = "TEST" . date("YmdHis"); // Buat nomor test otomatis: TEST + tanggal & jam


    // Query SQL: simpan data baru ke tabel users
    $query = "INSERT INTO users
              (
                  nama,
                  nik,
                  email,
                  no_hp,
                  asal_sekolah,
                  prodi,
                  username,
                  password,
                  role,
                  nomor_test,
                  status_test,
                  status_daftar_ulang
              )
              VALUES
              (
                  ?, ?, ?, ?, ?, ?, ?, ?, 'user', ?, 'Belum Test', 'Belum'
              )";


    $stmt = mysqli_prepare( // Siapkan query (prepared statement) agar aman dari SQL injection
        $conn, // Koneksi database
        $query // Teks query SQL yang akan dijalankan
    ); // Akhir pemanggilan fungsi


    mysqli_stmt_bind_param( // Isi tanda tanya (?) pada query dengan nilai yang aman
        $stmt, // Statement query yang sudah disiapkan
        "sssssssss", // Tipe data tiap nilai (i = angka bulat, d = desimal, s = teks)
        $nama, // Nilai: nama
        $nik, // Nilai: nik
        $email, // Nilai: email
        $no_hp, // Nilai: nomor HP
        $asal_sekolah, // Nilai: asal sekolah
        $prodi, // Nilai: prodi
        $username, // Nilai: username
        $password, // Nilai: password
        $nomor_test // Nilai: nomor test
    ); // Akhir pemanggilan fungsi


    if (mysqli_stmt_execute($stmt)) { // Kalau data berhasil disimpan

        // Tampilkan alert "Data calon maba berhasil ditambahkan!" lalu pindah ke data_maba.php
        echo "<script>
                alert('Data calon maba berhasil ditambahkan!');
                window.location='data_maba.php';
              </script>";

        exit; // Hentikan proses halaman di sini

    } else { // Kalau kondisi di atas tidak terpenuhi

        // Tampilkan alert "Data gagal ditambahkan!" lalu pindah ke tambah_maba.php
        echo "<script>
                alert('Data gagal ditambahkan!');
                window.location='tambah_maba.php';
              </script>";

        exit; // Hentikan proses halaman di sini
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
        Tambah Calon Maba - Universitas NaraDipa 
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
    =================================================== -->

    <aside class="sidebar"> <!-- Sidebar: menu samping -->


        <!-- LOGO -->

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


        <!-- MENU -->

        <nav class="sidebar-menu"> <!-- Navigasi: daftar menu navigasi -->


            <a href="dashboard.php"> <!-- Link ke Dashboard -->

                🏠 <!-- Ikon emoji penghias -->

                <span> <!-- Teks keterangan -->
                    Dashboard <!-- Teks keterangan -->
                </span> <!-- Tutup teks keterangan -->

            </a> <!-- Tutup link Dashboard -->


            <a
                href="data_maba.php"
                class="active"
            > <!-- Link menu Data Calon Maba (menu aktif) -->

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


            <a href="daftar_ulang.php"> <!-- Link ke Daftar Ulang -->

                📋 <!-- Ikon emoji penghias -->

                <span> <!-- Teks keterangan -->
                    Daftar Ulang <!-- Teks keterangan -->
                </span> <!-- Tutup teks keterangan -->

            </a> <!-- Tutup link Daftar Ulang -->


        </nav> <!-- Tutup daftar menu navigasi -->


        <!-- LOGOUT -->

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
    =================================================== -->

    <main class="main-content"> <!-- Konten utama halaman -->


        <!-- HEADER -->

        <header class="dashboard-header"> <!-- Header halaman -->

            <div> <!-- Wadah -->

                <h1> <!-- Judul utama -->
                    Tambah Calon Maba <!-- Teks judul halaman -->
                </h1> <!-- Tutup judul utama -->

                <p> <!-- Paragraf -->
                    Isi data calon mahasiswa baru <!-- Teks paragraf -->
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
             FORM TAMBAH
        =================================================== -->

        <section class="content-card"> <!-- Bagian: kartu konten -->

            <div class="section-title"> <!-- Wadah: judul bagian -->

                <div> <!-- Wadah -->

                    <h2> <!-- Judul bagian -->
                        Tambah Calon Mahasiswa <!-- Teks judul bagian -->
                    </h2> <!-- Tutup judul bagian -->

                    <p> <!-- Paragraf -->
                        Lengkapi data di bawah untuk mendaftarkan <!-- Teks paragraf -->
                        calon mahasiswa baru secara manual <!-- Teks paragraf -->
                    </p> <!-- Tutup paragraf -->

                </div> <!-- Tutup wadah -->

                <a
                    href="data_maba.php"
                    class="btn-edit"
                > <!-- Tombol menuju Data Calon Maba -->
                    ← Kembali <!-- Teks pada link/tombol -->
                </a> <!-- Tutup link Data Calon Maba -->

            </div> <!-- Tutup judul bagian -->


            <form
                method="POST"
                action=""
            > <!-- Form kirim data (method POST) -->


                <div class="form-grid"> <!-- Wadah: grid form -->


                    <!-- NAMA -->

                    <div class="input-group"> <!-- Wadah: kelompok input -->

                        <label> <!-- Label -->
                            Nama Lengkap <!-- Teks label -->
                        </label> <!-- Tutup label -->

                        <input
                            type="text"
                            name="nama"
                            placeholder="Masukkan nama lengkap"
                            required
                        > <!-- Input text "nama" (wajib diisi) -->

                    </div> <!-- Tutup kelompok input -->


                    <!-- NIK -->

                    <div class="input-group"> <!-- Wadah: kelompok input -->

                        <label> <!-- Label -->
                            NIK <!-- Teks label -->
                        </label> <!-- Tutup label -->

                        <input
                            type="text"
                            name="nik"
                            maxlength="16"
                            placeholder="Masukkan NIK"
                            required
                        > <!-- Input text "nik" (wajib diisi) -->

                    </div> <!-- Tutup kelompok input -->


                    <!-- EMAIL -->

                    <div class="input-group"> <!-- Wadah: kelompok input -->

                        <label> <!-- Label -->
                            Email <!-- Teks label -->
                        </label> <!-- Tutup label -->

                        <input
                            type="email"
                            name="email"
                            placeholder="Masukkan email"
                            required
                        > <!-- Input email "email" (wajib diisi) -->

                    </div> <!-- Tutup kelompok input -->


                    <!-- NO HP -->

                    <div class="input-group"> <!-- Wadah: kelompok input -->

                        <label> <!-- Label -->
                            Nomor HP <!-- Teks label -->
                        </label> <!-- Tutup label -->

                        <input
                            type="text"
                            name="no_hp"
                            placeholder="Masukkan nomor HP"
                            required
                        > <!-- Input text "no_hp" (wajib diisi) -->

                    </div> <!-- Tutup kelompok input -->


                    <!-- ASAL SEKOLAH -->

                    <div class="input-group"> <!-- Wadah: kelompok input -->

                        <label> <!-- Label -->
                            Asal Sekolah <!-- Teks label -->
                        </label> <!-- Tutup label -->

                        <input
                            type="text"
                            name="asal_sekolah"
                            placeholder="Masukkan asal sekolah"
                            required
                        > <!-- Input text "asal_sekolah" (wajib diisi) -->

                    </div> <!-- Tutup kelompok input -->


                    <!-- PRODI -->

                    <div class="input-group"> <!-- Wadah: kelompok input -->

                        <label> <!-- Label -->
                            Program Studi <!-- Teks label -->
                        </label> <!-- Tutup label -->

                        <select
                            name="prodi"
                            required
                        > <!-- Kotak pilihan (dropdown) -->

                            <option value=""> <!-- Satu pilihan -->
                                Pilih Program Studi <!-- Teks yang ditampilkan di halaman -->
                            </option> <!-- Tutup satu pilihan -->

                            <option value="Teknik Informatika"> <!-- Satu pilihan -->
                                Teknik Informatika <!-- Teks yang ditampilkan di halaman -->
                            </option> <!-- Tutup satu pilihan -->

                            <option value="Sistem Informasi"> <!-- Satu pilihan -->
                                Sistem Informasi <!-- Teks yang ditampilkan di halaman -->
                            </option> <!-- Tutup satu pilihan -->

                            <option value="Manajemen"> <!-- Satu pilihan -->
                                Manajemen <!-- Teks yang ditampilkan di halaman -->
                            </option> <!-- Tutup satu pilihan -->

                            <option value="Akuntansi"> <!-- Satu pilihan -->
                                Akuntansi <!-- Teks yang ditampilkan di halaman -->
                            </option> <!-- Tutup satu pilihan -->

                            <option value="Ilmu Komunikasi"> <!-- Satu pilihan -->
                                Ilmu Komunikasi <!-- Teks yang ditampilkan di halaman -->
                            </option> <!-- Tutup satu pilihan -->

                        </select> <!-- Tutup kotak pilihan (dropdown) -->

                    </div> <!-- Tutup kelompok input -->


                    <!-- USERNAME -->

                    <div class="input-group"> <!-- Wadah: kelompok input -->

                        <label> <!-- Label -->
                            Username <!-- Teks label -->
                        </label> <!-- Tutup label -->

                        <input
                            type="text"
                            name="username"
                            placeholder="Buat username"
                            required
                        > <!-- Input text "username" (wajib diisi) -->

                    </div> <!-- Tutup kelompok input -->


                    <!-- PASSWORD -->

                    <div class="input-group"> <!-- Wadah: kelompok input -->

                        <label> <!-- Label -->
                            Password <!-- Teks label -->
                        </label> <!-- Tutup label -->

                        <input
                            type="password"
                            name="password"
                            placeholder="Buat password"
                            required
                        > <!-- Input password "password" (wajib diisi) -->

                    </div> <!-- Tutup kelompok input -->

                </div> <!-- Tutup grid form -->


                <div class="form-buttons"> <!-- Wadah: tombol form -->

                    <button
                        type="submit"
                        name="tambah"
                        class="btn-primary"
                    > <!-- Tombol kirim form (tambah) -->
                        Simpan Data <!-- Teks pada tombol -->
                    </button> <!-- Tutup tombol -->


                    <a
                        href="data_maba.php"
                        class="btn-secondary"
                    > <!-- Link ke Data Calon Maba -->
                        Batal <!-- Teks pada link/tombol -->
                    </a> <!-- Tutup link Data Calon Maba -->

                </div> <!-- Tutup tombol form -->

            </form> <!-- Tutup form -->

        </section> <!-- Tutup kartu konten -->

    </main> <!-- Tutup konten utama halaman -->

</div> <!-- Tutup pembungkus layout dashboard -->

</body> <!-- Tutup isi halaman (body) -->

</html> <!-- Tutup tag html -->

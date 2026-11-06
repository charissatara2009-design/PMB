<?php // Awal blok kode PHP

require_once "../config/auth.php"; // Muat file pengecekan login (auth)
require_once "../config/database.php"; // Muat file koneksi database

cekAdmin(); // Pastikan yang masuk adalah admin, kalau bukan diarahkan keluar


// ==================================================
// CEK ID
// ==================================================

if (!isset($_GET['id'])) { // Kalau parameter id tidak ada di URL

    header("Location: data_maba.php"); // Arahkan ke halaman data calon maba
    exit; // Hentikan proses halaman di sini

} // Akhir blok if

$id = $_GET['id']; // Ambil ID dari URL


// ==================================================
// AMBIL DATA CALON MABA
// ==================================================

// Query SQL: ambil data dari tabel users berdasarkan ID, khusus calon maba (role user)
$query = "SELECT *
          FROM users
          WHERE id = ?
          AND role = 'user'";

$stmt = mysqli_prepare($conn, $query); // Siapkan query (prepared statement) agar aman dari SQL injection

mysqli_stmt_bind_param( // Isi tanda tanya (?) pada query dengan nilai yang aman
    $stmt, // Statement query yang sudah disiapkan
    "i", // Tipe data tiap nilai (i = angka bulat, d = desimal, s = teks)
    $id // ID data
); // Akhir pemanggilan fungsi

mysqli_stmt_execute($stmt); // Jalankan query

$result = mysqli_stmt_get_result($stmt); // Ambil hasil query

$data = mysqli_fetch_assoc($result); // Ambil satu baris hasil query dalam bentuk array


// Kalau data tidak ditemukan
if (!$data) { // Kalau data tidak ditemukan

    // Tampilkan alert "Data calon mahasiswa tidak ditemukan!" lalu pindah ke data_maba.php
    echo "<script>
            alert('Data calon mahasiswa tidak ditemukan!');
            window.location='data_maba.php';
          </script>";

    exit; // Hentikan proses halaman di sini

} // Akhir blok if


// ==================================================
// PROSES UPDATE DATA
// ==================================================

if (isset($_POST['update'])) { // Jalankan jika tombol Simpan Perubahan ditekan

    $nama = $_POST['nama']; // Ambil nama dari form
    $nik = $_POST['nik']; // Ambil nik dari form
    $email = $_POST['email']; // Ambil email dari form
    $no_hp = $_POST['no_hp']; // Ambil no hp dari form
    $asal_sekolah = $_POST['asal_sekolah']; // Ambil asal sekolah dari form
    $prodi = $_POST['prodi']; // Ambil prodi dari form
    $username = $_POST['username']; // Ambil username dari form
    $status_test = $_POST['status_test']; // Ambil status test dari form
    $status_daftar_ulang = $_POST['status_daftar_ulang']; // Ambil status daftar ulang dari form
    $nim = $_POST['nim']; // Ambil nim dari form


    // ==============================================
    // UPDATE PASSWORD JIKA DIISI
    // ==============================================

    if (!empty($_POST['password'])) { // Kalau password baru diisi

        $password = password_hash( // Acak (hash) password agar aman disimpan
            $_POST['password'], // Data password dari array _POST
            PASSWORD_DEFAULT // Pakai algoritma hash bawaan PHP
        ); // Akhir pemanggilan fungsi

        // Query SQL: ubah data tabel users (nama = ?, nik = ?, email = ?, no_hp = ?, asal_sekolah = ?, prodi = ?, username = ?, password = ?, status_test = ?, status_daftar_ulang = ?, nim = ?)
        $query_update = "UPDATE users SET
                            nama = ?,
                            nik = ?,
                            email = ?,
                            no_hp = ?,
                            asal_sekolah = ?,
                            prodi = ?,
                            username = ?,
                            password = ?,
                            status_test = ?,
                            status_daftar_ulang = ?,
                            nim = ?
                         WHERE id = ?";

        $stmt_update = mysqli_prepare( // Siapkan query (prepared statement) agar aman dari SQL injection
            $conn, // Koneksi database
            $query_update // Teks query SQL yang akan dijalankan
        ); // Akhir pemanggilan fungsi

        mysqli_stmt_bind_param( // Isi tanda tanya (?) pada query dengan nilai yang aman
            $stmt_update, // Statement query yang sudah disiapkan
            "sssssssssssi", // Tipe data tiap nilai (i = angka bulat, d = desimal, s = teks)
            $nama, // Nilai: nama
            $nik, // Nilai: nik
            $email, // Nilai: email
            $no_hp, // Nilai: nomor HP
            $asal_sekolah, // Nilai: asal sekolah
            $prodi, // Nilai: prodi
            $username, // Nilai: username
            $password, // Nilai: password
            $status_test, // Nilai: status test
            $status_daftar_ulang, // Nilai: status daftar ulang
            $nim, // Nilai: NIM
            $id // ID data
        ); // Akhir pemanggilan fungsi

    } else { // Kalau kondisi di atas tidak terpenuhi

        // ==========================================
        // UPDATE TANPA PASSWORD
        // ==========================================

        // Query SQL: ubah data tabel users (nama = ?, nik = ?, email = ?, no_hp = ?, asal_sekolah = ?, prodi = ?, username = ?, status_test = ?, status_daftar_ulang = ?, nim = ?)
        $query_update = "UPDATE users SET
                            nama = ?,
                            nik = ?,
                            email = ?,
                            no_hp = ?,
                            asal_sekolah = ?,
                            prodi = ?,
                            username = ?,
                            status_test = ?,
                            status_daftar_ulang = ?,
                            nim = ?
                         WHERE id = ?";

        $stmt_update = mysqli_prepare( // Siapkan query (prepared statement) agar aman dari SQL injection
            $conn, // Koneksi database
            $query_update // Teks query SQL yang akan dijalankan
        ); // Akhir pemanggilan fungsi

        mysqli_stmt_bind_param( // Isi tanda tanya (?) pada query dengan nilai yang aman
            $stmt_update, // Statement query yang sudah disiapkan
            "ssssssssssi", // Tipe data tiap nilai (i = angka bulat, d = desimal, s = teks)
            $nama, // Nilai: nama
            $nik, // Nilai: nik
            $email, // Nilai: email
            $no_hp, // Nilai: nomor HP
            $asal_sekolah, // Nilai: asal sekolah
            $prodi, // Nilai: prodi
            $username, // Nilai: username
            $status_test, // Nilai: status test
            $status_daftar_ulang, // Nilai: status daftar ulang
            $nim, // Nilai: NIM
            $id // ID data
        ); // Akhir pemanggilan fungsi

    } // Akhir blok else


    // ==============================================
    // JALANKAN UPDATE
    // ==============================================

    if (mysqli_stmt_execute($stmt_update)) { // Kalau update berhasil dijalankan

        // Tampilkan alert "Data berhasil diperbarui!" lalu pindah ke data_maba.php
        echo "<script>
                alert('Data berhasil diperbarui!');
                window.location='data_maba.php';
              </script>";

        exit; // Hentikan proses halaman di sini

    } else { // Kalau kondisi di atas tidak terpenuhi

        // Tampilkan alert "Data gagal diperbarui!"
        echo "<script>
                alert('Data gagal diperbarui!');
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

    <title> <!-- Judul yang tampil di tab browser -->
        Edit Calon Maba - Universitas NaraDipa <!-- Judul yang tampil di tab browser -->
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
                    Edit Calon Maba <!-- Teks judul halaman -->
                </h1> <!-- Tutup judul utama -->

                <p> <!-- Paragraf -->
                    Perbarui data calon mahasiswa <!-- Teks paragraf -->
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
             FORM EDIT
        =================================================== -->

        <section class="content-card"> <!-- Bagian: kartu konten -->


            <div class="section-title"> <!-- Wadah: judul bagian -->

                <div> <!-- Wadah -->

                    <h2> <!-- Judul bagian -->
                        Edit Data <!-- Teks judul bagian -->
                    </h2> <!-- Tutup judul bagian -->

                    <p> <!-- Paragraf -->
                        Silakan ubah data yang diperlukan <!-- Teks paragraf -->
                    </p> <!-- Tutup paragraf -->

                </div> <!-- Tutup wadah -->


                <a
                    href="data_maba.php"
                    class="btn-secondary"
                > <!-- Link ke Data Calon Maba -->
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
                            value="<?= htmlspecialchars(
                                $data['nama']
                            ); ?>"
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
                            value="<?= htmlspecialchars(
                                $data['nik']
                            ); ?>"
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
                            value="<?= htmlspecialchars(
                                $data['email']
                            ); ?>"
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
                            value="<?= htmlspecialchars(
                                $data['no_hp']
                            ); ?>"
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
                            value="<?= htmlspecialchars(
                                $data['asal_sekolah']
                            ); ?>"
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

                            <option
                                value="Teknik Informatika"
                                <?= $data['prodi']
                                    === 'Teknik Informatika'
                                    ? 'selected'
                                    : ''; ?>
                            > <!-- Satu pilihan -->
                                Teknik Informatika <!-- Teks yang ditampilkan di halaman -->
                            </option> <!-- Tutup satu pilihan -->

                            <option
                                value="Sistem Informasi"
                                <?= $data['prodi']
                                    === 'Sistem Informasi'
                                    ? 'selected'
                                    : ''; ?>
                            > <!-- Satu pilihan -->
                                Sistem Informasi <!-- Teks yang ditampilkan di halaman -->
                            </option> <!-- Tutup satu pilihan -->

                            <option
                                value="Manajemen"
                                <?= $data['prodi']
                                    === 'Manajemen'
                                    ? 'selected'
                                    : ''; ?>
                            > <!-- Satu pilihan -->
                                Manajemen <!-- Teks yang ditampilkan di halaman -->
                            </option> <!-- Tutup satu pilihan -->

                            <option
                                value="Akuntansi"
                                <?= $data['prodi']
                                    === 'Akuntansi'
                                    ? 'selected'
                                    : ''; ?>
                            > <!-- Satu pilihan -->
                                Akuntansi <!-- Teks yang ditampilkan di halaman -->
                            </option> <!-- Tutup satu pilihan -->

                            <option
                                value="Ilmu Komunikasi"
                                <?= $data['prodi']
                                    === 'Ilmu Komunikasi'
                                    ? 'selected'
                                    : ''; ?>
                            > <!-- Satu pilihan -->
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
                            value="<?= htmlspecialchars(
                                $data['username']
                            ); ?>"
                            required
                        > <!-- Input text "username" (wajib diisi) -->

                    </div> <!-- Tutup kelompok input -->


                    <!-- PASSWORD -->

                    <div class="input-group"> <!-- Wadah: kelompok input -->

                        <label> <!-- Label -->
                            Password Baru <!-- Teks label -->
                        </label> <!-- Tutup label -->

                        <input
                            type="password"
                            name="password"
                            placeholder="Kosongkan jika tidak diubah"
                        > <!-- Input password "password" -->

                    </div> <!-- Tutup kelompok input -->


                    <!-- STATUS TEST -->

                    <div class="input-group"> <!-- Wadah: kelompok input -->

                        <label> <!-- Label -->
                            Status Test <!-- Teks label -->
                        </label> <!-- Tutup label -->

                        <select
                            name="status_test"
                            required
                        > <!-- Kotak pilihan (dropdown) -->

                            <option
                                value="Belum Test"
                                <?= $data['status_test']
                                    === 'Belum Test'
                                    ? 'selected'
                                    : ''; ?>
                            > <!-- Satu pilihan -->
                                Belum Test <!-- Teks yang ditampilkan di halaman -->
                            </option> <!-- Tutup satu pilihan -->

                            <option
                                value="Lulus"
                                <?= $data['status_test']
                                    === 'Lulus'
                                    ? 'selected'
                                    : ''; ?>
                            > <!-- Satu pilihan -->
                                Lulus <!-- Teks yang ditampilkan di halaman -->
                            </option> <!-- Tutup satu pilihan -->

                            <option
                                value="Tidak Lulus"
                                <?= $data['status_test']
                                    === 'Tidak Lulus'
                                    ? 'selected'
                                    : ''; ?>
                            > <!-- Satu pilihan -->
                                Tidak Lulus <!-- Teks yang ditampilkan di halaman -->
                            </option> <!-- Tutup satu pilihan -->

                        </select> <!-- Tutup kotak pilihan (dropdown) -->

                    </div> <!-- Tutup kelompok input -->


                    <!-- DAFTAR ULANG -->

                    <div class="input-group"> <!-- Wadah: kelompok input -->

                        <label> <!-- Label -->
                            Status Daftar Ulang <!-- Teks label -->
                        </label> <!-- Tutup label -->

                        <select
                            name="status_daftar_ulang"
                            required
                        > <!-- Kotak pilihan (dropdown) -->

                            <option
                                value="Belum"
                                <?= $data[
                                    'status_daftar_ulang'
                                ] === 'Belum'
                                    ? 'selected'
                                    : ''; ?>
                            > <!-- Satu pilihan -->
                                Belum <!-- Teks yang ditampilkan di halaman -->
                            </option> <!-- Tutup satu pilihan -->

                            <option
                                value="Sudah"
                                <?= $data[
                                    'status_daftar_ulang'
                                ] === 'Sudah'
                                    ? 'selected'
                                    : ''; ?>
                            > <!-- Satu pilihan -->
                                Sudah <!-- Teks yang ditampilkan di halaman -->
                            </option> <!-- Tutup satu pilihan -->

                        </select> <!-- Tutup kotak pilihan (dropdown) -->

                    </div> <!-- Tutup kelompok input -->


                    <!-- NIM -->

                    <div class="input-group"> <!-- Wadah: kelompok input -->

                        <label> <!-- Label -->
                            Nomor Induk Mahasiswa <!-- Teks label -->
                        </label> <!-- Tutup label -->

                        <input
                            type="text"
                            name="nim"
                            value="<?= htmlspecialchars(
                                $data['nim'] ?? ''
                            ); ?>"
                            placeholder="Masukkan NIM jika sudah tersedia"
                        > <!-- Input text "nim" -->

                    </div> <!-- Tutup kelompok input -->

                </div> <!-- Tutup grid form -->


                <!-- BUTTON -->

                <div class="form-buttons"> <!-- Wadah: tombol form -->

                    <button
                        type="submit"
                        name="update"
                        class="btn-primary"
                    > <!-- Tombol kirim form (update) -->
                        💾 Simpan Perubahan <!-- Teks pada tombol -->
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
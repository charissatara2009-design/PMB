<?php // Awal blok kode PHP

require_once "../config/auth.php"; // Muat file pengecekan login (auth)
require_once "../config/database.php"; // Muat file koneksi database

cekAdmin(); // Pastikan yang masuk adalah admin, kalau bukan diarahkan keluar


// ==================================================
// CEK ID SOAL
// ==================================================

if (!isset($_GET['id'])) { // Kalau parameter id tidak ada di URL

    header("Location: data_soal.php"); // Arahkan ke halaman data soal
    exit; // Hentikan proses halaman di sini

} // Akhir blok if

$id = $_GET['id']; // Ambil ID dari URL


// ==================================================
// AMBIL DATA SOAL
// ==================================================

// Query SQL: ambil data dari tabel soal berdasarkan ID
$query = "SELECT *
          FROM soal
          WHERE id = ?";

$stmt = mysqli_prepare($conn, $query); // Siapkan query (prepared statement) agar aman dari SQL injection

mysqli_stmt_bind_param( // Isi tanda tanya (?) pada query dengan nilai yang aman
    $stmt, // Statement query yang sudah disiapkan
    "i", // Tipe data tiap nilai (i = angka bulat, d = desimal, s = teks)
    $id // ID data
); // Akhir pemanggilan fungsi

mysqli_stmt_execute($stmt); // Jalankan query

$result = mysqli_stmt_get_result($stmt); // Ambil hasil query

$data = mysqli_fetch_assoc($result); // Ambil satu baris hasil query dalam bentuk array


// JIKA SOAL TIDAK DITEMUKAN

if (!$data) { // Kalau data tidak ditemukan

    // Tampilkan alert "Soal tidak ditemukan!" lalu pindah ke data_soal.php
    echo "<script>
            alert('Soal tidak ditemukan!');
            window.location='data_soal.php';
          </script>";

    exit; // Hentikan proses halaman di sini

} // Akhir blok if


// ==================================================
// PROSES UPDATE SOAL
// ==================================================

if (isset($_POST['update'])) { // Jalankan jika tombol Simpan Perubahan ditekan

    $soal = $_POST['soal']; // Ambil soal dari form
    $prodi = $_POST['prodi']; // Ambil prodi dari form
    $pilihan_a = $_POST['pilihan_a']; // Ambil pilihan a dari form
    $pilihan_b = $_POST['pilihan_b']; // Ambil pilihan b dari form
    $pilihan_c = $_POST['pilihan_c']; // Ambil pilihan c dari form
    $pilihan_d = $_POST['pilihan_d']; // Ambil pilihan d dari form
    $jawaban_benar = $_POST['jawaban_benar']; // Ambil jawaban benar dari form


    // Query SQL: ubah data tabel soal (prodi = ?, soal = ?, pilihan_a = ?, pilihan_b = ?, pilihan_c = ?, pilihan_d = ?, jawaban_benar = ?)
    $query_update = "UPDATE soal SET

                        prodi = ?,
                        soal = ?,
                        pilihan_a = ?,
                        pilihan_b = ?,
                        pilihan_c = ?,
                        pilihan_d = ?,
                        jawaban_benar = ?

                     WHERE id = ?";


    $stmt_update = mysqli_prepare( // Siapkan query (prepared statement) agar aman dari SQL injection
        $conn, // Koneksi database
        $query_update // Teks query SQL yang akan dijalankan
    ); // Akhir pemanggilan fungsi


    mysqli_stmt_bind_param( // Isi tanda tanya (?) pada query dengan nilai yang aman
        $stmt_update, // Statement query yang sudah disiapkan
        "sssssssi", // Tipe data tiap nilai (i = angka bulat, d = desimal, s = teks)
        $prodi, // Nilai: prodi
        $soal, // Nilai: soal
        $pilihan_a, // Nilai: pilihan a
        $pilihan_b, // Nilai: pilihan b
        $pilihan_c, // Nilai: pilihan c
        $pilihan_d, // Nilai: pilihan d
        $jawaban_benar, // Nilai: jawaban benar
        $id // ID data
    ); // Akhir pemanggilan fungsi


    if (mysqli_stmt_execute($stmt_update)) { // Kalau update berhasil dijalankan

        // Tampilkan alert "Soal berhasil diperbarui!" lalu pindah ke data_soal.php
        echo "<script>

                alert('Soal berhasil diperbarui!');

                window.location='data_soal.php';

              </script>";

        exit; // Hentikan proses halaman di sini

    } else { // Kalau kondisi di atas tidak terpenuhi

        // Tampilkan alert "Soal gagal diperbarui!"
        echo "<script>

                alert('Soal gagal diperbarui!');

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
        Edit Soal Test - Universitas NaraDipa <!-- Judul yang tampil di tab browser -->
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


            <a href="data_maba.php"> <!-- Link ke Data Calon Maba -->

                👥 <!-- Ikon emoji penghias -->

                <span> <!-- Teks keterangan -->
                    Data Calon Maba <!-- Teks keterangan -->
                </span> <!-- Tutup teks keterangan -->

            </a> <!-- Tutup link Data Calon Maba -->


            <a
                href="data_soal.php"
                class="active"
            > <!-- Link menu Data Soal Test (menu aktif) -->

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
    ================================================== -->

    <main class="main-content"> <!-- Konten utama halaman -->


        <!-- HEADER -->

        <header class="dashboard-header"> <!-- Header halaman -->


            <div> <!-- Wadah -->

                <h1> <!-- Judul utama -->
                    Edit Soal Test <!-- Teks judul halaman -->
                </h1> <!-- Tutup judul utama -->

                <p> <!-- Paragraf -->
                    Perbarui data soal test <!-- Teks paragraf -->
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
             FORM EDIT SOAL
        ================================================== -->

        <section class="content-card"> <!-- Bagian: kartu konten -->


            <!-- JUDUL -->

            <div class="section-title"> <!-- Wadah: judul bagian -->


                <div> <!-- Wadah -->

                    <h2> <!-- Judul bagian -->
                        Edit Soal <!-- Teks judul bagian -->
                    </h2> <!-- Tutup judul bagian -->

                    <p> <!-- Paragraf -->
                        Ubah pertanyaan dan pilihan jawaban <!-- Teks paragraf -->
                    </p> <!-- Tutup paragraf -->

                </div> <!-- Tutup wadah -->


                <a
                    href="data_soal.php"
                    class="btn-secondary"
                > <!-- Link ke Data Soal Test -->

                    ← Kembali <!-- Teks pada link/tombol -->

                </a> <!-- Tutup link Data Soal Test -->

            </div> <!-- Tutup judul bagian -->



            <!-- FORM -->

            <form
                method="POST"
                action=""
            > <!-- Form kirim data (method POST) -->

                <div class="input-group"> <!-- Wadah: kelompok input -->

                    <label> <!-- Label -->
                        Program Studi <!-- Teks label -->
                    </label> <!-- Tutup label -->

                    <select
                        name="prodi"
                        required
                    > <!-- Kotak pilihan (dropdown) -->

                        <?php // Awal blok kode PHP
                        $daftar_prodi = array( // Daftar program studi untuk pilihan
                            'Semua', // Pilihan 'Semua' prodi
                            'Teknik Informatika', // Prodi: Teknik Informatika
                            'Sistem Informasi', // Prodi: Sistem Informasi
                            'Manajemen', // Prodi: Manajemen
                            'Akuntansi', // Prodi: Akuntansi
                            'Ilmu Komunikasi' // Prodi: Ilmu Komunikasi
                        ); // Akhir pemanggilan fungsi

                        foreach ($daftar_prodi as $opsi_prodi): // Ulangi untuk setiap pilihan prodi
                         // Akhir blok kode PHP ?>

                            <option
                                value="<?= htmlspecialchars($opsi_prodi); ?>"
                                <?= $data['prodi'] === $opsi_prodi
                                    ? 'selected'
                                    : ''; ?>
                            > <!-- Satu pilihan -->
                                <?= $opsi_prodi === 'Semua' // Kalau prodinya 'Semua'
                                    ? 'Semua Prodi' // tampilkan 'Semua Prodi'
                                    : htmlspecialchars($opsi_prodi);  // kalau bukan, tampilkan nama prodinya ?>
                            </option> <!-- Tutup satu pilihan -->

                        <?php endforeach;  // Akhir perulangan foreach ?>

                    </select> <!-- Tutup kotak pilihan (dropdown) -->

                </div> <!-- Tutup kelompok input -->


                <!-- PERTANYAAN -->

                <div class="input-group"> <!-- Wadah: kelompok input -->

                    <label> <!-- Label -->
                        Pertanyaan <!-- Teks label -->
                    </label> <!-- Tutup label -->

                    <textarea
                        name="soal"
                        rows="5"
                        placeholder="Masukkan pertanyaan"
                        required
                    ><?= htmlspecialchars( <!-- Kotak teks panjang -->
                        $data['soal'] // Data teks soal dari array data
                    ); ?></textarea> <!-- Tutup kotak teks panjang -->

                </div> <!-- Tutup kelompok input -->



                <!-- PILIHAN -->

                <div class="form-grid"> <!-- Wadah: grid form -->


                    <!-- A -->

                    <div class="input-group"> <!-- Wadah: kelompok input -->

                        <label> <!-- Label -->
                            Pilihan A <!-- Teks label -->
                        </label> <!-- Tutup label -->

                        <input
                            type="text"
                            name="pilihan_a"
                            value="<?= htmlspecialchars(
                                $data['pilihan_a']
                            ); ?>"
                            required
                        > <!-- Input text "pilihan_a" (wajib diisi) -->

                    </div> <!-- Tutup kelompok input -->



                    <!-- B -->

                    <div class="input-group"> <!-- Wadah: kelompok input -->

                        <label> <!-- Label -->
                            Pilihan B <!-- Teks label -->
                        </label> <!-- Tutup label -->

                        <input
                            type="text"
                            name="pilihan_b"
                            value="<?= htmlspecialchars(
                                $data['pilihan_b']
                            ); ?>"
                            required
                        > <!-- Input text "pilihan_b" (wajib diisi) -->

                    </div> <!-- Tutup kelompok input -->



                    <!-- C -->

                    <div class="input-group"> <!-- Wadah: kelompok input -->

                        <label> <!-- Label -->
                            Pilihan C <!-- Teks label -->
                        </label> <!-- Tutup label -->

                        <input
                            type="text"
                            name="pilihan_c"
                            value="<?= htmlspecialchars(
                                $data['pilihan_c']
                            ); ?>"
                            required
                        > <!-- Input text "pilihan_c" (wajib diisi) -->

                    </div> <!-- Tutup kelompok input -->



                    <!-- D -->

                    <div class="input-group"> <!-- Wadah: kelompok input -->

                        <label> <!-- Label -->
                            Pilihan D <!-- Teks label -->
                        </label> <!-- Tutup label -->

                        <input
                            type="text"
                            name="pilihan_d"
                            value="<?= htmlspecialchars(
                                $data['pilihan_d']
                            ); ?>"
                            required
                        > <!-- Input text "pilihan_d" (wajib diisi) -->

                    </div> <!-- Tutup kelompok input -->



                    <!-- JAWABAN BENAR -->

                    <div class="input-group"> <!-- Wadah: kelompok input -->

                        <label> <!-- Label -->
                            Jawaban Benar <!-- Teks label -->
                        </label> <!-- Tutup label -->

                        <select
                            name="jawaban_benar"
                            required
                        > <!-- Kotak pilihan (dropdown) -->


                            <option
                                value="A"
                                <?= $data[
                                    'jawaban_benar'
                                ] === 'A'
                                    ? 'selected'
                                    : ''; ?>
                            > <!-- Satu pilihan -->
                                A <!-- Teks yang ditampilkan di halaman -->
                            </option> <!-- Tutup satu pilihan -->


                            <option
                                value="B"
                                <?= $data[
                                    'jawaban_benar'
                                ] === 'B'
                                    ? 'selected'
                                    : ''; ?>
                            > <!-- Satu pilihan -->
                                B <!-- Teks yang ditampilkan di halaman -->
                            </option> <!-- Tutup satu pilihan -->


                            <option
                                value="C"
                                <?= $data[
                                    'jawaban_benar'
                                ] === 'C'
                                    ? 'selected'
                                    : ''; ?>
                            > <!-- Satu pilihan -->
                                C <!-- Teks yang ditampilkan di halaman -->
                            </option> <!-- Tutup satu pilihan -->


                            <option
                                value="D"
                                <?= $data[
                                    'jawaban_benar'
                                ] === 'D'
                                    ? 'selected'
                                    : ''; ?>
                            > <!-- Satu pilihan -->
                                D <!-- Teks yang ditampilkan di halaman -->
                            </option> <!-- Tutup satu pilihan -->


                        </select> <!-- Tutup kotak pilihan (dropdown) -->

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
                        href="data_soal.php"
                        class="btn-secondary"
                    > <!-- Link ke Data Soal Test -->

                        Batal <!-- Teks pada link/tombol -->

                    </a> <!-- Tutup link Data Soal Test -->


                </div> <!-- Tutup tombol form -->


            </form> <!-- Tutup form -->


        </section> <!-- Tutup kartu konten -->


    </main> <!-- Tutup konten utama halaman -->

</div> <!-- Tutup pembungkus layout dashboard -->

</body> <!-- Tutup isi halaman (body) -->

</html> <!-- Tutup tag html -->
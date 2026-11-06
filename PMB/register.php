<?php // Awal blok kode PHP

require_once "config/database.php"; // Muat file koneksi database

if (isset($_POST['register'])) { // Jalankan jika tombol Daftar ditekan

    $nama = trim($_POST['nama']); // Ambil nama dari form (spasi di awal/akhir dibuang)
    $nik = trim($_POST['nik']); // Ambil nik dari form (spasi di awal/akhir dibuang)
    $email = trim($_POST['email']); // Ambil email dari form (spasi di awal/akhir dibuang)
    $no_hp = trim($_POST['no_hp']); // Ambil no hp dari form (spasi di awal/akhir dibuang)
    $asal_sekolah = trim($_POST['asal_sekolah']); // Ambil asal sekolah dari form (spasi di awal/akhir dibuang)
    $prodi = trim($_POST['prodi']); // Ambil prodi dari form (spasi di awal/akhir dibuang)
    $username = trim($_POST['username']); // Ambil username dari form (spasi di awal/akhir dibuang)
    $password = $_POST['password']; // Ambil password dari form (tidak di-trim)

    // Role otomatis sebagai user
    $role = "user"; // Role otomatis user (calon maba)

    // Cek username
    $cek_username = mysqli_prepare( // Siapkan query (prepared statement) agar aman dari SQL injection
        $conn, // Koneksi database
        "SELECT id FROM users WHERE username = ?" // Query SQL: ambil data dari tabel users dengan username tertentu (cek apakah sudah dipakai)
    ); // Akhir pemanggilan fungsi

    mysqli_stmt_bind_param( // Isi tanda tanya (?) pada query dengan nilai yang aman
        $cek_username, // Statement pengecekan username
        "s", // Tipe data tiap nilai (i = angka bulat, d = desimal, s = teks)
        $username // Nilai: username
    ); // Akhir pemanggilan fungsi

    mysqli_stmt_execute($cek_username); // Jalankan query

    $hasil_username = mysqli_stmt_get_result($cek_username); // Ambil hasil query

    if (mysqli_num_rows($hasil_username) > 0) { // Kalau username sudah dipakai

        // Tampilkan alert "Username sudah digunakan!" lalu pindah ke register.php
        echo "<script>
                alert('Username sudah digunakan!');
                window.location='register.php';
              </script>";

        exit; // Hentikan proses halaman di sini
    } // Akhir blok if


    // Cek email
    $cek_email = mysqli_prepare( // Siapkan query (prepared statement) agar aman dari SQL injection
        $conn, // Koneksi database
        "SELECT id FROM users WHERE email = ?" // Query SQL: ambil data dari tabel users
    ); // Akhir pemanggilan fungsi

    mysqli_stmt_bind_param( // Isi tanda tanya (?) pada query dengan nilai yang aman
        $cek_email, // Statement pengecekan email
        "s", // Tipe data tiap nilai (i = angka bulat, d = desimal, s = teks)
        $email // Nilai: email
    ); // Akhir pemanggilan fungsi

    mysqli_stmt_execute($cek_email); // Jalankan query

    $hasil_email = mysqli_stmt_get_result($cek_email); // Ambil hasil query

    if (mysqli_num_rows($hasil_email) > 0) { // Kalau email sudah dipakai

        // Tampilkan alert "Email sudah digunakan!" lalu pindah ke register.php
        echo "<script>
                alert('Email sudah digunakan!');
                window.location='register.php';
              </script>";

        exit; // Hentikan proses halaman di sini
    } // Akhir blok if


    // Cek NIK
    $cek_nik = mysqli_prepare( // Siapkan query (prepared statement) agar aman dari SQL injection
        $conn, // Koneksi database
        "SELECT id FROM users WHERE nik = ?" // Query SQL: ambil data dari tabel users
    ); // Akhir pemanggilan fungsi

    mysqli_stmt_bind_param( // Isi tanda tanya (?) pada query dengan nilai yang aman
        $cek_nik, // Statement pengecekan NIK
        "s", // Tipe data tiap nilai (i = angka bulat, d = desimal, s = teks)
        $nik // Nilai: nik
    ); // Akhir pemanggilan fungsi

    mysqli_stmt_execute($cek_nik); // Jalankan query

    $hasil_nik = mysqli_stmt_get_result($cek_nik); // Ambil hasil query

    if (mysqli_num_rows($hasil_nik) > 0) { // Kalau NIK sudah terdaftar

        // Tampilkan alert "NIK sudah terdaftar!" lalu pindah ke register.php
        echo "<script>
                alert('NIK sudah terdaftar!');
                window.location='register.php';
              </script>";

        exit; // Hentikan proses halaman di sini
    } // Akhir blok if


    // Password dibuat hash
    $password_hash = password_hash( // Acak (hash) password agar aman disimpan
        $password, // Password yang akan di-hash
        PASSWORD_DEFAULT // Pakai algoritma hash bawaan PHP
    ); // Akhir pemanggilan fungsi


    // Membuat nomor test otomatis
    $nomor_test = "TEST" . date("YmdHis"); // Buat nomor test otomatis: TEST + tanggal & jam


    // Status awal
    $status_test = "Belum Test"; // Status test awal: 'Belum Test'
    $status_daftar_ulang = "Belum"; // Status daftar ulang awal: 'Belum'


    // Simpan data
    // Query SQL: simpan data baru ke tabel users
    $query = "INSERT INTO users
    (
        nomor_test,
        nama,
        nik,
        email,
        no_hp,
        asal_sekolah,
        prodi,
        username,
        password,
        role,
        status_test,
        status_daftar_ulang
    )
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";


    $stmt = mysqli_prepare( // Siapkan query (prepared statement) agar aman dari SQL injection
        $conn, // Koneksi database
        $query // Teks query SQL yang akan dijalankan
    ); // Akhir pemanggilan fungsi


    mysqli_stmt_bind_param( // Isi tanda tanya (?) pada query dengan nilai yang aman
        $stmt, // Statement query yang sudah disiapkan
        "ssssssssssss", // Tipe data tiap nilai (i = angka bulat, d = desimal, s = teks)
        $nomor_test, // Nilai: nomor test
        $nama, // Nilai: nama
        $nik, // Nilai: NIK
        $email, // Nilai: email
        $no_hp, // Nilai: nomor HP
        $asal_sekolah, // Nilai: asal sekolah
        $prodi, // Nilai: prodi
        $username, // Nilai: username
        $password_hash, // Nilai: password yang sudah di-hash
        $role, // Nilai: role
        $status_test, // Nilai: status test
        $status_daftar_ulang // Nilai: status daftar ulang
    ); // Akhir pemanggilan fungsi


    if (mysqli_stmt_execute($stmt)) { // Kalau data berhasil disimpan

        // Tampilkan alert "Registrasi berhasil! Nomor test kamu: $nomor_test" lalu pindah ke index.php
        echo "<script>
                alert('Registrasi berhasil! Nomor test kamu: $nomor_test');
                window.location='index.php';
              </script>";

        exit; // Hentikan proses halaman di sini

    } else { // Kalau kondisi di atas tidak terpenuhi

        // Tampilkan alert "Registrasi gagal!" lalu pindah ke register.php
        echo "<script>
                alert('Registrasi gagal!');
                window.location='register.php';
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

    <title>Registrasi - Universitas NaraDipa</title> <!-- Judul yang tampil di tab browser berisi "Registrasi - Universitas NaraDipa" -->

    <link
        rel="stylesheet"
        href="css/style.css"
    > <!-- Memuat file CSS utama -->

</head> <!-- Tutup bagian head -->

<body> <!-- Awal isi halaman yang tampil -->

<div class="register-page"> <!-- Wadah: halaman registrasi -->

    <div class="register-box"> <!-- Wadah: kotak registrasi -->


        <!-- HEADER -->

        <div class="register-header"> <!-- Wadah: header registrasi -->

            <div class="logo-icon"> <!-- Wadah: ikon logo -->
                🎓 <!-- Ikon emoji penghias -->
            </div> <!-- Tutup ikon logo -->

            <h1> <!-- Judul utama -->
                Registrasi Calon Mahasiswa <!-- Teks judul halaman -->
            </h1> <!-- Tutup judul utama -->

            <p> <!-- Paragraf -->
                Silakan isi data diri untuk membuat akun <!-- Teks paragraf -->
            </p> <!-- Tutup paragraf -->

        </div> <!-- Tutup header registrasi -->


        <!-- FORM -->

        <form
            action="register.php"
            method="POST"
        > <!-- Form kirim data (method POST) -->

            <div class="form-grid"> <!-- Wadah: grid form -->


                <div class="input-group"> <!-- Wadah: kelompok input -->

                    <label for="nama"> <!-- Label untuk kolom nama -->
                        Nama Lengkap <!-- Teks label -->
                    </label> <!-- Tutup label -->

                    <input
                        type="text"
                        id="nama"
                        name="nama"
                        placeholder="Masukkan nama lengkap"
                        required
                    > <!-- Input text "nama" (wajib diisi) -->

                </div> <!-- Tutup kelompok input -->


                <div class="input-group"> <!-- Wadah: kelompok input -->

                    <label for="nik"> <!-- Label untuk kolom nik -->
                        NIK <!-- Teks label -->
                    </label> <!-- Tutup label -->

                    <input
                        type="text"
                        id="nik"
                        name="nik"
                        placeholder="Masukkan NIK"
                        maxlength="16"
                        required
                    > <!-- Input text "nik" (wajib diisi) -->

                </div> <!-- Tutup kelompok input -->


                <div class="input-group"> <!-- Wadah: kelompok input -->

                    <label for="email"> <!-- Label untuk kolom email -->
                        Email <!-- Teks label -->
                    </label> <!-- Tutup label -->

                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="Masukkan email"
                        required
                    > <!-- Input email "email" (wajib diisi) -->

                </div> <!-- Tutup kelompok input -->


                <div class="input-group"> <!-- Wadah: kelompok input -->

                    <label for="no_hp"> <!-- Label untuk kolom no_hp -->
                        Nomor HP <!-- Teks label -->
                    </label> <!-- Tutup label -->

                    <input
                        type="text"
                        id="no_hp"
                        name="no_hp"
                        placeholder="Masukkan nomor HP"
                        required
                    > <!-- Input text "no_hp" (wajib diisi) -->

                </div> <!-- Tutup kelompok input -->


                <div class="input-group"> <!-- Wadah: kelompok input -->

                    <label for="asal_sekolah"> <!-- Label untuk kolom asal_sekolah -->
                        Asal Sekolah <!-- Teks label -->
                    </label> <!-- Tutup label -->

                    <input
                        type="text"
                        id="asal_sekolah"
                        name="asal_sekolah"
                        placeholder="Masukkan asal sekolah"
                        required
                    > <!-- Input text "asal_sekolah" (wajib diisi) -->

                </div> <!-- Tutup kelompok input -->


                <div class="input-group"> <!-- Wadah: kelompok input -->

                    <label for="prodi"> <!-- Label untuk kolom prodi -->
                        Program Studi <!-- Teks label -->
                    </label> <!-- Tutup label -->

                    <select
                        id="prodi"
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


                <div class="input-group"> <!-- Wadah: kelompok input -->

                    <label for="username"> <!-- Label untuk kolom username -->
                        Username <!-- Teks label -->
                    </label> <!-- Tutup label -->

                    <input
                        type="text"
                        id="username"
                        name="username"
                        placeholder="Buat username"
                        required
                    > <!-- Input text "username" (wajib diisi) -->

                </div> <!-- Tutup kelompok input -->


                <div class="input-group"> <!-- Wadah: kelompok input -->

                    <label for="password"> <!-- Label untuk kolom password -->
                        Password <!-- Teks label -->
                    </label> <!-- Tutup label -->

                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Buat password"
                        required
                    > <!-- Input password "password" (wajib diisi) -->

                </div> <!-- Tutup kelompok input -->

            </div> <!-- Tutup grid form -->


            <button
                type="submit"
                name="register"
                class="btn-login"
            > <!-- Tombol kirim form (register) -->
                Daftar Sekarang <!-- Teks pada tombol -->
            </button> <!-- Tutup tombol -->

        </form> <!-- Tutup form -->


        <div class="register-link"> <!-- Wadah: link ke login -->

            <p> <!-- Paragraf -->

                Sudah memiliki akun? <!-- Teks paragraf -->

                <a href="index.php"> <!-- Link ke index.php -->
                    Login <!-- Teks pada link/tombol -->
                </a> <!-- Tutup link index.php -->

            </p> <!-- Tutup paragraf -->

        </div> <!-- Tutup link ke login -->

    </div> <!-- Tutup kotak registrasi -->

</div> <!-- Tutup halaman registrasi -->

</body> <!-- Tutup isi halaman (body) -->

</html> <!-- Tutup tag html -->
<?php // Awal blok kode PHP

require_once "../config/auth.php"; // Muat file pengecekan login (auth)
require_once "../config/database.php"; // Muat file koneksi database

cekUser(); // Pastikan user sudah login, kalau belum diarahkan keluar

$user_id = $_SESSION['user_id']; // Ambil ID user yang sedang login dari session


// ==================================================
// AMBIL DATA USER
// ==================================================

// Query SQL: ambil data dari tabel users milik user yang login
$query_user = "SELECT nama, nomor_test, prodi, status_test
               FROM users
               WHERE id = ?";

$stmt_user = mysqli_prepare($conn, $query_user); // Siapkan query (prepared statement) agar aman dari SQL injection

mysqli_stmt_bind_param( // Isi tanda tanya (?) pada query dengan nilai yang aman
    $stmt_user, // Statement query yang sudah disiapkan
    "i", // Tipe data tiap nilai (i = angka bulat, d = desimal, s = teks)
    $user_id // ID user yang sedang login
); // Akhir pemanggilan fungsi

mysqli_stmt_execute($stmt_user); // Jalankan query

$result_user = mysqli_stmt_get_result($stmt_user); // Ambil hasil query

$user = mysqli_fetch_assoc($result_user); // Ambil satu baris hasil query dalam bentuk array


if (!$user) { // Kalau data user tidak ditemukan

    // Tampilkan alert "Data user tidak ditemukan!" lalu pindah ke dashboard.php
    echo "<script>
            alert('Data user tidak ditemukan!');
            window.location='dashboard.php';
          </script>";

    exit; // Hentikan proses halaman di sini
} // Akhir blok if

if (trim($user['prodi'] ?? '') === '') { // Kalau prodi belum dipilih
    // Tampilkan alert "Program studi belum dipilih." lalu pindah ke dashboard.php
    echo "<script>
            alert('Program studi belum dipilih.');
            window.location='dashboard.php';
          </script>";
    exit; // Hentikan proses halaman di sini
} // Akhir blok if

if ($user['status_test'] !== 'Belum Test') { // Kalau test sudah pernah dikerjakan (status bukan 'Belum Test')
    header('Location: hasil_test.php'); // Arahkan ke halaman hasil test
    exit; // Hentikan proses halaman di sini
} // Akhir blok if


// ==================================================
// VERIFIKASI NOMOR UJIAN
// ==================================================
// Soal tidak langsung ditampilkan. Peserta wajib
// memasukkan nomor ujian miliknya sendiri terlebih
// dahulu. Berlaku untuk semua program studi.
// ==================================================

$verified = // Cek apakah nomor ujian sudah diverifikasi
    isset($_SESSION['test_verified']) // Session verifikasi ada
    && // dan
    $_SESSION['test_verified'] === true; // dan nilainya true

if (isset($_POST['verifikasi_nomor'])) { // Kalau form verifikasi nomor ujian dikirim

    $nomor_input = trim($_POST['nomor_ujian'] ?? ''); // Ambil nomor ujian yang diketik user

    if ( // Mulai pengecekan kondisi (if)
        $nomor_input !== '' // Input tidak kosong
        && // dan
        $nomor_input === trim($user['nomor_test'] ?? '') // dan sama dengan nomor test milik user
    ) { // Akhir kondisi; blok di bawah jalan jika terpenuhi

        $_SESSION['test_verified'] = true; // Tandai di session bahwa verifikasi berhasil

        header('Location: test.php'); // Muat ulang halaman test (soal akan tampil)
        exit; // Hentikan proses halaman di sini

    } else { // Kalau kondisi di atas tidak terpenuhi

        // Tampilkan alert "Nomor ujian yang kamu masukkan salah. Silakan periksa kembali." lalu pindah ke test.php
        echo "<script>
                alert('Nomor ujian yang kamu masukkan salah. Silakan periksa kembali.');
                window.location='test.php';
              </script>";

        exit; // Hentikan proses halaman di sini
    } // Akhir blok else
} // Akhir blok if


// ==================================================
// TIMER UJIAN (30 MENIT)
// ==================================================
// Waktu mulai disimpan di database (bukan di browser),
// jadi refresh halaman / logout-login tidak mereset waktu.
// Jika waktu habis sebelum test dikirim, peserta otomatis
// dinyatakan gagal dan tidak lulus.
// ==================================================

$durasi_test = 30 * 60; // dalam detik


// Buat kolom penyimpan waktu mulai jika belum ada
$cek_kolom = mysqli_query( // Jalankan query langsung untuk cek kolom di tabel users
    $conn, // Koneksi database
    "SHOW COLUMNS FROM users LIKE 'waktu_mulai_test'" // Query SQL: cek apakah kolom waktu_mulai_test sudah ada di tabel users
); // Akhir pemanggilan fungsi

if (mysqli_num_rows($cek_kolom) === 0) { // Kalau kolomnya belum ada

    mysqli_query( // Jalankan query langsung
        $conn, // Koneksi database
        // Query SQL: tambah kolom waktu_mulai_test (tipe DATETIME) ke tabel users
        "ALTER TABLE users
         ADD COLUMN waktu_mulai_test DATETIME NULL"
    ); // Akhir pemanggilan fungsi
} // Akhir blok if


// Hitung waktu yang sudah terpakai (selisih dihitung oleh MySQL)
$stmt_waktu = mysqli_prepare( // Siapkan query (prepared statement) agar aman dari SQL injection
    $conn, // Koneksi database
    // Query SQL: hitung selisih detik sejak waktu mulai test sampai sekarang
    "SELECT TIMESTAMPDIFF(SECOND, waktu_mulai_test, NOW()) AS terpakai
     FROM users
     WHERE id = ?"
); // Akhir pemanggilan fungsi

mysqli_stmt_bind_param($stmt_waktu, "i", $user_id); // Isi tanda tanya (?) pada query dengan nilai yang aman
mysqli_stmt_execute($stmt_waktu); // Jalankan query

$data_waktu = mysqli_fetch_assoc( // Ambil satu baris hasil query dalam bentuk array
    mysqli_stmt_get_result($stmt_waktu) // Hasil query waktu terpakai
); // Akhir pemanggilan fungsi

$sudah_mulai = ($data_waktu['terpakai'] !== null); // Cek apakah test sudah pernah dimulai

$sisa_detik = $sudah_mulai // Hitung sisa waktu (detik)
    ? $durasi_test - (int) $data_waktu['terpakai'] // Kalau sudah mulai: durasi dikurangi waktu terpakai
    : $durasi_test; // Kalau belum mulai: sisa waktu = durasi penuh


// Toleransi 5 detik untuk pengiriman jawaban di detik-detik terakhir
$toleransi = isset($_POST['submit_test']) ? 5 : 0; // Toleransi 5 detik jika sedang kirim jawaban, selain itu 0

if ($sudah_mulai && ($sisa_detik + $toleransi) <= 0) { // Kalau sudah mulai dan waktu habis (termasuk toleransi)

    // Tandai gagal (hanya jika masih "Belum Test", mencegah proses ganda)
    $stmt_gagal = mysqli_prepare( // Siapkan query (prepared statement) agar aman dari SQL injection
        $conn, // Koneksi database
        // Query SQL: ubah data tabel users (status_test = 'Tidak Lulus')
        "UPDATE users
         SET status_test = 'Tidak Lulus'
         WHERE id = ?
         AND status_test = 'Belum Test'"
    ); // Akhir pemanggilan fungsi

    mysqli_stmt_bind_param($stmt_gagal, "i", $user_id); // Isi tanda tanya (?) pada query dengan nilai yang aman
    mysqli_stmt_execute($stmt_gagal); // Jalankan query

    if (mysqli_stmt_affected_rows($stmt_gagal) === 1) { // Kalau ada 1 baris yang berhasil diubah

        // Hitung jumlah soal prodi untuk dicatat di hasil
        $stmt_hitung = mysqli_prepare( // Siapkan query (prepared statement) agar aman dari SQL injection
            $conn, // Koneksi database
            // Query SQL: hitung jumlah soal untuk prodi user (atau soal "Semua")
            "SELECT COUNT(*) AS total
             FROM soal
             WHERE prodi = ?
                OR prodi = 'Semua'"
        ); // Akhir pemanggilan fungsi

        mysqli_stmt_bind_param($stmt_hitung, "s", $user['prodi']); // Isi tanda tanya (?) pada query dengan nilai yang aman
        mysqli_stmt_execute($stmt_hitung); // Jalankan query

        $total_soal_prodi = (int) mysqli_fetch_assoc( // Ambil jumlah soal prodi sebagai angka
            mysqli_stmt_get_result($stmt_hitung) // Hasil query hitung soal
        )['total']; // Ambil kolom total dari hasil

        $benar_gagal = 0; // Jawaban benar dicatat 0
        $nilai_gagal = 0; // Nilai dicatat 0
        $status_gagal = "Tidak Lulus"; // Status dicatat 'Tidak Lulus'

        $stmt_hasil_gagal = mysqli_prepare( // Siapkan query (prepared statement) agar aman dari SQL injection
            $conn, // Koneksi database
            // Query SQL: simpan data baru ke tabel hasil_test
            "INSERT INTO hasil_test
             (user_id, jumlah_soal, jawaban_benar, nilai, status)
             VALUES (?, ?, ?, ?, ?)"
        ); // Akhir pemanggilan fungsi

        mysqli_stmt_bind_param( // Isi tanda tanya (?) pada query dengan nilai yang aman
            $stmt_hasil_gagal, // Statement query yang sudah disiapkan
            "iiids", // Tipe data tiap nilai (i = angka bulat, d = desimal, s = teks)
            $user_id, // ID user yang sedang login
            $total_soal_prodi, // Nilai: jumlah soal
            $benar_gagal, // Nilai: jawaban benar
            $nilai_gagal, // Nilai: nilai akhir
            $status_gagal // Nilai: status
        ); // Akhir pemanggilan fungsi

        mysqli_stmt_execute($stmt_hasil_gagal); // Jalankan query
    } // Akhir blok if

    unset($_SESSION['test_verified']); // Hapus tanda verifikasi dari session

    // Tampilkan alert "Waktu ujian (30 menit) telah habis. Kamu dinyatakan gagal mengikuti ujian dan tidak lulus." lalu pindah ke hasil_test.php
    echo "<script>
            alert('Waktu ujian (30 menit) telah habis. Kamu dinyatakan gagal mengikuti ujian dan tidak lulus.');
            window.location='hasil_test.php';
          </script>";

    exit; // Hentikan proses halaman di sini
} // Akhir blok if


// ==================================================
// AMBIL SOAL SESUAI PRODI USER
// ==================================================
// Hanya dijalankan setelah nomor ujian terverifikasi
// ==================================================

$jumlah_soal = 0; // Awalnya jumlah soal 0
$result_soal = null; // Awalnya hasil soal kosong

if ($verified) { // Kalau nomor ujian sudah terverifikasi

    // Query SQL: ambil semua soal sesuai prodi user (atau "Semua"), urut dari id terkecil
    $query_soal = "SELECT *
                   FROM soal
                   WHERE prodi = ?
                      OR prodi = 'Semua'
                   ORDER BY id ASC";

    $stmt_soal = mysqli_prepare($conn, $query_soal); // Siapkan query (prepared statement) agar aman dari SQL injection
    mysqli_stmt_bind_param($stmt_soal, "s", $user['prodi']); // Isi tanda tanya (?) pada query dengan nilai yang aman
    mysqli_stmt_execute($stmt_soal); // Jalankan query
    $result_soal = mysqli_stmt_get_result($stmt_soal); // Ambil hasil query

    $jumlah_soal = mysqli_num_rows($result_soal); // Hitung banyaknya soal yang didapat


    // ==================================================
    // CEK APAKAH ADA SOAL
    // ==================================================

    if ($jumlah_soal < 1) { // Kalau tidak ada soal sama sekali

        // Tampilkan alert "Soal untuk prodi kamu belum tersedia. Silakan hubungi Admin." lalu pindah ke dashboard.php
        echo "<script>
                alert('Soal untuk prodi kamu belum tersedia. Silakan hubungi Admin.');
                window.location='dashboard.php';
              </script>";

        exit; // Hentikan proses halaman di sini
    } // Akhir blok if

} // Akhir blok if


// ==================================================
// MULAI HITUNG MUNDUR
// ==================================================
// Dimulai saat peserta sudah lolos verifikasi nomor
// ujian dan masuk ke halaman soal.
// ==================================================

if ($verified && !$sudah_mulai) { // Kalau sudah verifikasi tapi timer belum jalan

    $stmt_mulai = mysqli_prepare( // Siapkan query (prepared statement) agar aman dari SQL injection
        $conn, // Koneksi database
        // Query SQL: ubah data tabel users (waktu_mulai_test = NOW())
        "UPDATE users
         SET waktu_mulai_test = NOW()
         WHERE id = ?
         AND waktu_mulai_test IS NULL"
    ); // Akhir pemanggilan fungsi

    mysqli_stmt_bind_param($stmt_mulai, "i", $user_id); // Isi tanda tanya (?) pada query dengan nilai yang aman
    mysqli_stmt_execute($stmt_mulai); // Jalankan query

    $sudah_mulai = true; // Tandai test sudah dimulai
    $sisa_detik = $durasi_test; // Sisa waktu = durasi penuh
} // Akhir blok if


// ==================================================
// PROSES JAWABAN
// ==================================================

if ($verified && isset($_POST['submit_test'])) { // Kalau sudah verifikasi dan tombol kirim test ditekan

    $jawaban_user = $_POST['jawaban'] ?? array(); // Ambil semua jawaban dari form

    $jawaban_benar = 0; // Mulai hitung jawaban benar dari 0
    $jumlah_dijawab = 0; // Mulai hitung soal terjawab dari 0


    // ==============================================
    // CEK SETIAP JAWABAN
    // ==============================================

    mysqli_data_seek($result_soal, 0); // Kembali ke soal pertama

    while ( // Ulangi selama masih ada baris
        $soal = mysqli_fetch_assoc($result_soal) // Ambil satu baris hasil query dalam bentuk array
    ) { // Akhir kondisi; blok di bawah jalan jika terpenuhi

        $id_soal = $soal['id']; // Ambil ID soal

        $jawaban_benar_soal = // Ambil kunci jawaban soal (huruf besar)
            strtoupper( // Ubah jadi huruf besar
                trim( // Buang spasi di awal/akhir
                    $soal['jawaban_benar'] // Kunci jawaban benar dari soal ini
                ) // Penutup tanda kurung
            ); // Akhir pemanggilan fungsi


        if ( // Mulai pengecekan kondisi (if)
            isset( // Cek apakah ada:
                $jawaban_user[$id_soal] // jawaban user untuk soal ini
            ) // Penutup tanda kurung
        ) { // Akhir kondisi; blok di bawah jalan jika terpenuhi

            $jumlah_dijawab++; // Tambah hitungan soal terjawab

            $jawaban = // Ambil jawaban user (huruf besar)
                strtoupper( // Ubah jadi huruf besar
                    trim( // Buang spasi di awal/akhir
                        $jawaban_user[$id_soal] // jawaban user untuk soal ini
                    ) // Penutup tanda kurung
                ); // Akhir pemanggilan fungsi


            if ( // Mulai pengecekan kondisi (if)
                $jawaban === // Jawaban user sama persis dengan
                $jawaban_benar_soal // kunci jawaban
            ) { // Akhir kondisi; blok di bawah jalan jika terpenuhi

                $jawaban_benar++; // Tambah hitungan jawaban benar

            } // Akhir blok if

        } // Akhir blok if

    } // Akhir blok perulangan while

    if ($jumlah_dijawab !== $jumlah_soal) { // Kalau ada soal yang belum dijawab
        // Tampilkan alert "Semua soal wajib dijawab sebelum test dikirim." lalu pindah ke test.php
        echo "<script>
                alert('Semua soal wajib dijawab sebelum test dikirim.');
                window.location='test.php';
              </script>";
        exit; // Hentikan proses halaman di sini
    } // Akhir blok if


    // ==============================================
    // HITUNG NILAI
    // ==============================================

    $nilai = ( // Hitung nilai persen
        $jawaban_benar / // Jawaban benar dibagi
        $jumlah_soal // jumlah soal
    ) * 100; // lalu dikali 100


    $nilai = round( // Bulatkan nilai
        $nilai, // Nilai yang dibulatkan
        2 // 2 angka di belakang koma
    ); // Akhir pemanggilan fungsi


    // ==============================================
    // TENTUKAN STATUS
    // ==============================================
    // Batas kelulusan = 75
    // ==============================================

    if ($nilai >= 75) { // Kalau nilai 75 atau lebih

        $status = "Lulus"; // Status 'Lulus'

    } else { // Kalau kondisi di atas tidak terpenuhi

        $status = "Tidak Lulus"; // Status 'Tidak Lulus'

    } // Akhir blok else


    // ==============================================
    // SIMPAN KE TABEL HASIL_TEST
    // ==============================================

    // Query SQL: simpan data baru ke tabel hasil_test
    $query_hasil = "INSERT INTO hasil_test
                    (
                        user_id,
                        jumlah_soal,
                        jawaban_benar,
                        nilai,
                        status
                    )
                    VALUES (?, ?, ?, ?, ?)";

    $stmt_hasil = mysqli_prepare( // Siapkan query (prepared statement) agar aman dari SQL injection
        $conn, // Koneksi database
        $query_hasil // Teks query SQL yang akan dijalankan
    ); // Akhir pemanggilan fungsi


    mysqli_stmt_bind_param( // Isi tanda tanya (?) pada query dengan nilai yang aman
        $stmt_hasil, // Statement query yang sudah disiapkan
        "iiids", // Tipe data tiap nilai (i = angka bulat, d = desimal, s = teks)
        $user_id, // ID user yang sedang login
        $jumlah_soal, // Nilai: jumlah soal
        $jawaban_benar, // Nilai: jawaban benar
        $nilai, // Nilai yang dibulatkan
        $status // Nilai: status
    ); // Akhir pemanggilan fungsi


    if ( // Mulai pengecekan kondisi (if)
        mysqli_stmt_execute( // Jalankan query
            $stmt_hasil // Statement query yang sudah disiapkan
        ) // Penutup tanda kurung
    ) { // Akhir kondisi; blok di bawah jalan jika terpenuhi


        // ==========================================
        // UPDATE STATUS TEST DI USERS
        // ==========================================

        // Query SQL: ubah data tabel users (status_test = ?)
        $query_update = "UPDATE users
                         SET status_test = ?
                         WHERE id = ?";

        $stmt_update = mysqli_prepare( // Siapkan query (prepared statement) agar aman dari SQL injection
            $conn, // Koneksi database
            $query_update // Teks query SQL yang akan dijalankan
        ); // Akhir pemanggilan fungsi


        mysqli_stmt_bind_param( // Isi tanda tanya (?) pada query dengan nilai yang aman
            $stmt_update, // Statement query yang sudah disiapkan
            "si", // Tipe data tiap nilai (i = angka bulat, d = desimal, s = teks)
            $status, // Nilai: status
            $user_id // ID user yang sedang login
        ); // Akhir pemanggilan fungsi


        mysqli_stmt_execute( // Jalankan query
            $stmt_update // Statement query yang sudah disiapkan
        ); // Akhir pemanggilan fungsi


        // ==========================================
        // PINDAH KE HASIL TEST
        // ==========================================

        unset($_SESSION['test_verified']); // Hapus tanda verifikasi dari session

        // Tampilkan alert "Test selesai! Jawaban kamu sudah dikirim dan akan diperiksa oleh Admin." lalu pindah ke hasil_test.php
        echo "<script>

                alert(
                    'Test selesai! Jawaban kamu sudah dikirim dan akan diperiksa oleh Admin.'
                );

                window.location='hasil_test.php';

              </script>";

        exit; // Hentikan proses halaman di sini

    } else { // Kalau kondisi di atas tidak terpenuhi

        // Tampilkan alert "Hasil test gagal disimpan!"
        echo "<script>

                alert(
                    'Hasil test gagal disimpan!'
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

    <title> <!-- Judul yang tampil di tab browser -->
        Test PMB - Universitas NaraDipa <!-- Judul yang tampil di tab browser -->
    </title> <!-- Tutup judul tab -->

    <link
        rel="stylesheet"
        href="../css/style.css"
    > <!-- Memuat file CSS utama -->

    <style> /* Awal blok CSS (gaya khusus halaman ini) */

        .timer-ujian { /* Gaya untuk timer ujian */
            position: sticky; /* Posisi elemen: sticky */
            top: 14px; /* Jarak dari atas 14px */
            z-index: 20; /* Urutan tumpukan 20 (makin besar makin di atas) */
            display: flex; /* Susun isi dengan flexbox */
            align-items: center; /* Posisi isi tegak lurus: center */
            justify-content: space-between; /* Posisi isi searah baris: space-between */
            gap: 14px; /* Jarak antar elemen 14px */
            margin-bottom: 22px; /* Jarak luar bawah 22px */
            padding: 14px 24px; /* Jarak dalam elemen 14px 24px */
            border-radius: 16px; /* Sudut melengkung 16px */
            background: linear-gradient(135deg, #8a2740, #b23a55); /* Latar belakang linear-gradient(135deg, #8a2740, #b23a55) */
            color: #ffffff; /* Warna teks #ffffff */
            box-shadow: 0 10px 24px rgba(138, 39, 64, 0.3); /* Bayangan 0 10px 24px rgba(138, 39, 64, 0.3) */
        } /* Akhir aturan CSS */

        .timer-ujian .timer-label { /* Gaya untuk label timer */
            display: block; /* Tampil sebagai blok */
            font-size: 13px; /* Ukuran huruf 13px */
            font-weight: 700; /* Ketebalan huruf 700 */
        } /* Akhir aturan CSS */

        .timer-ujian .timer-note { /* Gaya untuk catatan timer */
            display: block; /* Tampil sebagai blok */
            margin-top: 3px; /* Jarak luar atas 3px */
            font-size: 11.5px; /* Ukuran huruf 11.5px */
            opacity: 0.85; /* Transparansi 0.85 */
        } /* Akhir aturan CSS */

        .timer-ujian strong { /* Gaya untuk timer ujian - elemen strong */
            font-size: 28px; /* Ukuran huruf 28px */
            letter-spacing: 2px; /* Jarak antar huruf 2px */
            font-variant-numeric: tabular-nums; /* Angka berlebar sama agar timer tidak bergeser */
        } /* Akhir aturan CSS */

        .timer-ujian.hampir-habis { /* Gaya untuk timer hampir habis */
            background: linear-gradient(135deg, #b3261e, #e0463c); /* Latar belakang linear-gradient(135deg, #b3261e, #e0463c) */
            animation: timerKedip 1s ease-in-out infinite; /* Jalankan animasi timerKedip 1s ease-in-out infinite */
        } /* Akhir aturan CSS */

        @keyframes timerKedip { /* Definisi animasi berkedip */
            50% { opacity: 0.8; } /* Pada titik 50% animasi, transparansi jadi 0.8 */
        } /* Akhir aturan CSS */

    </style> <!-- Tutup blok CSS -->

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
                    Calon Mahasiswa <!-- Teks keterangan -->
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


            <a href="nomor_test.php"> <!-- Link ke Nomor Test -->

                📝 <!-- Ikon emoji penghias -->

                <span> <!-- Teks keterangan -->
                    Nomor Test <!-- Teks keterangan -->
                </span> <!-- Tutup teks keterangan -->

            </a> <!-- Tutup link Nomor Test -->


            <a
                href="test.php"
                class="active"
            > <!-- Link menu Test PMB (menu aktif) -->

                📖 <!-- Ikon emoji penghias -->

                <span> <!-- Teks keterangan -->
                    Test PMB <!-- Teks keterangan -->
                </span> <!-- Tutup teks keterangan -->

            </a> <!-- Tutup link Test PMB -->


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
                    Test PMB <!-- Teks judul halaman -->
                </h1> <!-- Tutup judul utama -->

                <p> <!-- Paragraf -->
                    Silakan kerjakan soal dengan teliti <!-- Teks paragraf -->
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



        <?php if ($verified):  // Kalau sudah verifikasi ?>

        <!-- ==================================================
             TIMER UJIAN
        ================================================== -->

        <div id="timerUjian" class="timer-ujian"> <!-- Wadah: timer ujian -->

            <div> <!-- Wadah -->
                <span class="timer-label">⏱ Sisa waktu pengerjaan</span> <!-- Teks keterangan: label timer berisi "⏱ Sisa waktu pengerjaan" -->
                <small class="timer-note"> <!-- Teks kecil: catatan timer -->
                    Test otomatis dinyatakan gagal jika waktu habis. <!-- Teks kecil -->
                </small> <!-- Tutup catatan timer -->
            </div> <!-- Tutup wadah -->

            <strong id="timerTeks">30:00</strong> <!-- Teks tebal berisi "30:00" -->

        </div> <!-- Tutup timer ujian -->


        <!-- ==================================================
             INFORMASI TEST
        ================================================== -->

        <section class="content-card"> <!-- Bagian: kartu konten -->


            <div class="section-title"> <!-- Wadah: judul bagian -->

                <div> <!-- Wadah -->

                    <h2> <!-- Judul bagian -->
                        Test Seleksi PMB <!-- Teks judul bagian -->
                    </h2> <!-- Tutup judul bagian -->

                    <p> <!-- Paragraf -->
                        Jawablah seluruh pertanyaan berikut <!-- Teks paragraf -->
                        dengan benar. <!-- Teks paragraf -->
                    </p> <!-- Tutup paragraf -->

                </div> <!-- Tutup wadah -->

            </div> <!-- Tutup judul bagian -->


            <div class="info-box"> <!-- Wadah: kotak informasi -->

                <p> <!-- Paragraf -->

                    <strong> <!-- Teks tebal -->
                        Nama: <!-- Teks tebal -->
                    </strong> <!-- Tutup teks tebal -->

                    <?= htmlspecialchars( // Tampilkan teks berikut dengan aman (di-escape dari XSS)
                        $user['nama'] // Data nama lengkap dari array user
                    );  // Akhir pemanggilan fungsi ?>

                </p> <!-- Tutup paragraf -->


                <p> <!-- Paragraf -->

                    <strong> <!-- Teks tebal -->
                        Nomor Test: <!-- Teks tebal -->
                    </strong> <!-- Tutup teks tebal -->

                    <?= htmlspecialchars( // Tampilkan teks berikut dengan aman (di-escape dari XSS)
                        $user['nomor_test'] ?? '-' // Data nomor test dari array user, pakai '-' jika kosong
                    );  // Akhir pemanggilan fungsi ?>

                </p> <!-- Tutup paragraf -->


                <p> <!-- Paragraf -->

                    <strong> <!-- Teks tebal -->
                        Program Studi: <!-- Teks tebal -->
                    </strong> <!-- Tutup teks tebal -->

                    <?= htmlspecialchars( // Tampilkan teks berikut dengan aman (di-escape dari XSS)
                        $user['prodi'] ?? '-' // Data program studi dari array user, pakai '-' jika kosong
                    );  // Akhir pemanggilan fungsi ?>

                </p> <!-- Tutup paragraf -->


                <p> <!-- Paragraf -->

                    <strong> <!-- Teks tebal -->
                        Jumlah Soal: <!-- Teks tebal -->
                    </strong> <!-- Tutup teks tebal -->

                    <?= $jumlah_soal;  // Cetak jumlah soal ?>

                    soal <!-- Teks yang ditampilkan di halaman -->

                </p> <!-- Tutup paragraf -->


            </div> <!-- Tutup kotak informasi -->

        </section> <!-- Tutup kartu konten -->



        <!-- ==================================================
             FORM TEST
        ================================================== -->

        <form
            method="POST"
            action=""
            onsubmit="return konfirmasiTest();"
        > <!-- Form kirim data (method POST), konfirmasi dulu sebelum kirim -->


            <?php // Awal blok kode PHP

            mysqli_data_seek( // Kembali ke soal pertama
                $result_soal, // Hasil query
                0 // Posisi baris ke-0 (pertama)
            ); // Akhir pemanggilan fungsi

            $nomor = 1; // Nomor soal mulai dari 1


            while ( // Ulangi selama masih ada baris
                $soal = // Ambil satu soal
                mysqli_fetch_assoc( // Ambil satu baris soal
                    $result_soal // Hasil query
                ) // Penutup tanda kurung
            ): // Akhir kondisi, lanjut ke tampilan HTML

             // Akhir blok kode PHP ?>


                <!-- ==================================================
                     SOAL
                ================================================== -->

                <section class="question-card"> <!-- Bagian: kartu soal -->


                    <div class="question-header"> <!-- Wadah: header soal -->

                        <span class="question-number"> <!-- Teks keterangan: nomor soal -->

                            <?= $nomor++;  // Cetak nomor soal lalu tambah 1 ?>

                        </span> <!-- Tutup nomor soal -->


                        <h3> <!-- Sub-judul -->

                            <?= htmlspecialchars( // Tampilkan teks berikut dengan aman (di-escape dari XSS)
                                $soal['soal'] // Data teks soal dari array soal
                            );  // Akhir pemanggilan fungsi ?>

                        </h3> <!-- Tutup sub-judul -->

                    </div> <!-- Tutup header soal -->



                    <!-- PILIHAN A -->

                    <label class="answer-option"> <!-- Satu pilihan jawaban (bisa diklik) -->

                        <input
                            type="radio"
                            name="jawaban[<?= $soal['id']; ?>]"
                            value="A"
                            required
                        > <!-- Pilihan radio jawaban A (wajib diisi) -->

                        <span> <!-- Teks keterangan -->

                            <strong> <!-- Teks tebal -->
                                A. <!-- Teks tebal -->
                            </strong> <!-- Tutup teks tebal -->

                            <?= htmlspecialchars( // Tampilkan teks berikut dengan aman (di-escape dari XSS)
                                $soal['pilihan_a'] // Data pilihan A dari array soal
                            );  // Akhir pemanggilan fungsi ?>

                        </span> <!-- Tutup teks keterangan -->

                    </label> <!-- Tutup pilihan jawaban -->



                    <!-- PILIHAN B -->

                    <label class="answer-option"> <!-- Satu pilihan jawaban (bisa diklik) -->

                        <input
                            type="radio"
                            name="jawaban[<?= $soal['id']; ?>]"
                            value="B"
                        > <!-- Pilihan radio jawaban B -->

                        <span> <!-- Teks keterangan -->

                            <strong> <!-- Teks tebal -->
                                B. <!-- Teks tebal -->
                            </strong> <!-- Tutup teks tebal -->

                            <?= htmlspecialchars( // Tampilkan teks berikut dengan aman (di-escape dari XSS)
                                $soal['pilihan_b'] // Data pilihan B dari array soal
                            );  // Akhir pemanggilan fungsi ?>

                        </span> <!-- Tutup teks keterangan -->

                    </label> <!-- Tutup pilihan jawaban -->



                    <!-- PILIHAN C -->

                    <label class="answer-option"> <!-- Satu pilihan jawaban (bisa diklik) -->

                        <input
                            type="radio"
                            name="jawaban[<?= $soal['id']; ?>]"
                            value="C"
                        > <!-- Pilihan radio jawaban C -->

                        <span> <!-- Teks keterangan -->

                            <strong> <!-- Teks tebal -->
                                C. <!-- Teks tebal -->
                            </strong> <!-- Tutup teks tebal -->

                            <?= htmlspecialchars( // Tampilkan teks berikut dengan aman (di-escape dari XSS)
                                $soal['pilihan_c'] // Data pilihan C dari array soal
                            );  // Akhir pemanggilan fungsi ?>

                        </span> <!-- Tutup teks keterangan -->

                    </label> <!-- Tutup pilihan jawaban -->



                    <!-- PILIHAN D -->

                    <label class="answer-option"> <!-- Satu pilihan jawaban (bisa diklik) -->

                        <input
                            type="radio"
                            name="jawaban[<?= $soal['id']; ?>]"
                            value="D"
                        > <!-- Pilihan radio jawaban D -->

                        <span> <!-- Teks keterangan -->

                            <strong> <!-- Teks tebal -->
                                D. <!-- Teks tebal -->
                            </strong> <!-- Tutup teks tebal -->

                            <?= htmlspecialchars( // Tampilkan teks berikut dengan aman (di-escape dari XSS)
                                $soal['pilihan_d'] // Data pilihan D dari array soal
                            );  // Akhir pemanggilan fungsi ?>

                        </span> <!-- Tutup teks keterangan -->

                    </label> <!-- Tutup pilihan jawaban -->


                </section> <!-- Tutup kartu soal -->


            <?php endwhile;  // Akhir perulangan while ?>



            <!-- ==================================================
                 SUBMIT
            ================================================== -->

            <section class="content-card test-submit"> <!-- Bagian: kartu konten -->


                <div class="info-box"> <!-- Wadah: kotak informasi -->

                    <h3> <!-- Sub-judul -->
                        ⚠️ Sebelum Mengirim <!-- Teks sub-judul -->
                    </h3> <!-- Tutup sub-judul -->

                    <p> <!-- Paragraf -->
                        Pastikan semua jawaban sudah dipilih. <!-- Teks paragraf -->
                        Setelah dikirim, jawaban akan diperiksa <!-- Teks paragraf -->
                        dan hasil test akan dihitung otomatis. <!-- Teks paragraf -->
                    </p> <!-- Tutup paragraf -->

                </div> <!-- Tutup kotak informasi -->


                <button
                    type="submit"
                    name="submit_test"
                    class="btn-primary"
                > <!-- Tombol kirim form (submit_test) -->

                    ✓ Selesai & Lihat Hasil <!-- Teks pada tombol -->

                </button> <!-- Tutup tombol -->


            </section> <!-- Tutup kartu konten -->


        </form> <!-- Tutup form -->


        <?php else:  // Kalau kondisi di atas tidak terpenuhi ?>


        <!-- ==================================================
             VERIFIKASI NOMOR UJIAN
        ================================================== -->

        <section class="content-card"> <!-- Bagian: kartu konten -->


            <div class="section-title"> <!-- Wadah: judul bagian -->

                <div> <!-- Wadah -->

                    <h2> <!-- Judul bagian -->
                        Verifikasi Nomor Ujian <!-- Teks judul bagian -->
                    </h2> <!-- Tutup judul bagian -->

                    <p> <!-- Paragraf -->
                        Masukkan nomor ujian kamu untuk <!-- Teks paragraf -->
                        memulai Test PMB. <!-- Teks paragraf -->
                    </p> <!-- Tutup paragraf -->

                </div> <!-- Tutup wadah -->

            </div> <!-- Tutup judul bagian -->


            <div class="info-box"> <!-- Wadah: kotak informasi -->

                <p> <!-- Paragraf -->
                    Soal test hanya dapat diakses setelah <!-- Teks paragraf -->
                    kamu memasukkan nomor ujian dengan benar. <!-- Teks paragraf -->
                    Nomor ujian bisa dilihat pada halaman <!-- Teks paragraf -->
                    <strong>Nomor Test</strong>. <!-- Teks tebal berisi "Nomor Test." -->
                </p> <!-- Tutup paragraf -->

            </div> <!-- Tutup kotak informasi -->


            <br> <!-- Baris baru (jarak) -->


            <form method="POST" action=""> <!-- Form kirim data (method POST) -->

                <div class="data-item"> <!-- Wadah: satu item data -->

                    <label for="nomor_ujian"> <!-- Label untuk kolom nomor_ujian -->
                        Nomor Ujian <!-- Teks label -->
                    </label> <!-- Tutup label -->

                    <input
                        type="text"
                        id="nomor_ujian"
                        name="nomor_ujian"
                        placeholder="Masukkan nomor ujian kamu"
                        required
                        autofocus
                    > <!-- Input text "nomor_ujian" (wajib diisi, fokus otomatis) -->

                </div> <!-- Tutup satu item data -->

                <br> <!-- Baris baru (jarak) -->

                <button
                    type="submit"
                    name="verifikasi_nomor"
                    class="btn-primary"
                > <!-- Tombol kirim form (verifikasi_nomor) -->

                    ✓ Mulai Test <!-- Teks pada tombol -->

                </button> <!-- Tutup tombol -->

            </form> <!-- Tutup form -->


        </section> <!-- Tutup kartu konten -->


        <?php endif;  // Akhir blok if ?>


    </main> <!-- Tutup konten utama halaman -->

</div> <!-- Tutup pembungkus layout dashboard -->



<!-- ==================================================
     JAVASCRIPT
================================================== -->

<script> // Awal blok JavaScript

function konfirmasiTest() // Fungsi: minta konfirmasi sebelum kirim test
{ // Awal blok kode
    return confirm( // Tampilkan kotak konfirmasi OK/Batal
        "Apakah kamu yakin semua jawaban sudah benar dan ingin mengirim test?" // Teks pertanyaannya
    ); // Akhir pemanggilan fungsi
} // Akhir blok kode


<?php if ($verified):  // Kalau sudah verifikasi ?>
var sisaDetikAwal = <?= (int) $sisa_detik; ?>; // Cetak nilai dari PHP ke JavaScript: sisa waktu awal dalam detik

(function () { // Mulai fungsi yang langsung dijalankan

    var teks = document.getElementById("timerTeks"); // Ambil elemen teks timer
    var bar = document.getElementById("timerUjian"); // Ambil elemen kotak timer

    if (!teks) { // Kalau elemen timer tidak ada
        return; // Hentikan fungsi
    } // Akhir blok kode

    var berakhir = Date.now() + (sisaDetikAwal * 1000); // Hitung waktu berakhir (sekarang + sisa waktu)
    var sudahHabis = false; // Penanda: waktu belum habis
    var pewaktu; // Tempat menyimpan pewaktu (interval)

    function tampilkan() { // Fungsi: perbarui tampilan timer

        var sisa = Math.max( // Hitung sisa detik (tidak boleh negatif)
            0, // Batas minimum 0
            Math.ceil((berakhir - Date.now()) / 1000) // Selisih waktu berakhir dan sekarang, dalam detik
        ); // Akhir pemanggilan fungsi

        var menit = Math.floor(sisa / 60); // Ambil jumlah menit
        var detik = sisa % 60; // Ambil sisa detik

        teks.textContent = // Tulis teks timer (format MM:DD)
            (menit < 10 ? "0" : "") + menit + ":" + // Menit, tambah angka 0 di depan jika kurang dari 10
            (detik < 10 ? "0" : "") + detik; // Detik, tambah angka 0 di depan jika kurang dari 10

        if (sisa <= 300) { // Kalau sisa waktu 5 menit atau kurang
            bar.classList.add("hampir-habis"); // Ubah warna timer jadi merah berkedip
        } // Akhir blok kode

        if (sisa <= 0 && !sudahHabis) { // Kalau waktu habis dan belum diproses

            sudahHabis = true; // Tandai sudah habis
            clearInterval(pewaktu); // Hentikan pewaktu

            teks.textContent = "Waktu habis"; // Tampilkan tulisan Waktu habis

            // Server yang menentukan hasil akhir (gagal & tidak lulus)
            setTimeout(function () { // Tunggu sebentar, lalu:
                window.location.href = "test.php"; // Muat ulang halaman agar server memproses hasil
            }, 1500); // Jeda 1,5 detik
        } // Akhir blok kode
    } // Akhir blok kode

    pewaktu = setInterval(tampilkan, 1000); // Jalankan tampilkan() tiap 1 detik
    tampilkan(); // Tampilkan timer sekarang juga

})(); // Akhir fungsi yang langsung dijalankan
<?php endif;  // Akhir blok if ?>

</script> <!-- Tutup blok JavaScript -->


</body> <!-- Tutup isi halaman (body) -->

</html> <!-- Tutup tag html -->
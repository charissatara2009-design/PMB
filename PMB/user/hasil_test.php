<?php // Awal blok kode PHP

require_once "../config/auth.php"; // Muat file pengecekan login (auth)
require_once "../config/database.php"; // Muat file koneksi database

cekUser(); // Pastikan user sudah login, kalau belum diarahkan keluar

$user_id = $_SESSION['user_id']; // Ambil ID user yang sedang login dari session


// ==================================================
// AMBIL DATA USER + HASIL TEST
// ==================================================

// Query SQL: ambil data dari tabel users digabung dengan tabel hasil_test milik user yang login, ambil yang paling baru saja
$query = "SELECT
              users.nama,
              users.nomor_test,
              users.prodi,
              hasil_test.jumlah_soal,
              hasil_test.jawaban_benar,
              hasil_test.nilai,
              hasil_test.status,
              hasil_test.tanggal_test
          FROM users
          LEFT JOIN hasil_test
          ON users.id = hasil_test.user_id
          WHERE users.id = ?
          ORDER BY hasil_test.tanggal_test DESC
          LIMIT 1";

$stmt = mysqli_prepare($conn, $query); // Siapkan query (prepared statement) agar aman dari SQL injection

mysqli_stmt_bind_param( // Isi tanda tanya (?) pada query dengan nilai yang aman
    $stmt, // Statement query yang sudah disiapkan
    "i", // Tipe data tiap nilai (i = angka bulat, d = desimal, s = teks)
    $user_id // ID user yang sedang login
); // Akhir pemanggilan fungsi

mysqli_stmt_execute($stmt); // Jalankan query

$result = mysqli_stmt_get_result($stmt); // Ambil hasil query

$data = mysqli_fetch_assoc($result); // Ambil satu baris hasil query dalam bentuk array


// ==================================================
// CEK DATA
// ==================================================

if (!$data) { // Kalau data tidak ditemukan

    // Tampilkan alert "Data user tidak ditemukan!" lalu pindah ke dashboard.php
    echo "<script>
            alert('Data user tidak ditemukan!');
            window.location='dashboard.php';
          </script>";

    exit; // Hentikan proses halaman di sini
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
        Hasil Test - Universitas NaraDipa 
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
                href="hasil_test.php"
                class="active"
            > <!-- Link menu Hasil Test (menu aktif) -->

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
                    Hasil Test <!-- Teks judul halaman -->
                </h1> <!-- Tutup judul utama -->

                <p> <!-- Paragraf -->
                    Lihat hasil seleksi calon mahasiswa <!-- Teks paragraf -->
                </p> <!-- Tutup paragraf -->

            </div> <!-- Tutup wadah -->


            <div class="user-info"> <!-- Wadah: info user di header -->

                <div class="user-avatar"> <!-- Wadah: avatar user -->
                    👤 <!-- Ikon emoji penghias -->
                </div> <!-- Tutup avatar user -->

                <div> <!-- Wadah -->

                    <strong> <!-- Teks tebal -->

                        <?= htmlspecialchars( // Tampilkan teks berikut dengan aman (di-escape dari XSS)
                            $data['nama'] // Data nama lengkap dari array data
                        );  // Akhir pemanggilan fungsi ?>

                    </strong> <!-- Tutup teks tebal -->

                    <span> <!-- Teks keterangan -->
                        Calon Mahasiswa <!-- Teks keterangan -->
                    </span> <!-- Tutup teks keterangan -->

                </div> <!-- Tutup wadah -->

            </div> <!-- Tutup info user di header -->

        </header> <!-- Tutup header halaman -->



        <!-- ==================================================
             HASIL TEST
        ================================================== -->

        <section class="result-card"> <!-- Bagian: kartu hasil -->


            <div class="result-icon"> <!-- Wadah: ikon hasil -->

                <?php // Awal blok kode PHP

                if ( // Mulai pengecekan kondisi (if)
                    $data['status'] === 'Lulus' // Status 'Lulus'
                ) { // Akhir kondisi; blok di bawah jalan jika terpenuhi

                    echo "🎉"; // Tampilkan ikon perayaan

                } elseif ( // Kalau belum, cek kondisi lain (elseif)
                    $data['status'] === 'Tidak Lulus' // Status 'Tidak Lulus'
                ) { // Akhir kondisi; blok di bawah jalan jika terpenuhi

                    echo "😔"; // Tampilkan ikon sedih

                } else { // Kalau kondisi di atas tidak terpenuhi

                    echo "⏳"; // Tampilkan ikon jam pasir

                } // Akhir blok else

                 // Akhir blok kode PHP ?>

            </div> <!-- Tutup ikon hasil -->


            <p class="result-label"> <!-- Paragraf: label hasil -->
                HASIL TEST ANDA <!-- Teks paragraf -->
            </p> <!-- Tutup label hasil -->


            <?php // Awal blok kode PHP

            if ( // Mulai pengecekan kondisi (if)
                $data['status'] === 'Lulus' // Status 'Lulus'
            ): // Akhir kondisi, lanjut ke tampilan HTML

             // Akhir blok kode PHP ?>

                <h2 class="result-success"> <!-- Judul bagian: teks hasil sukses -->
                    LULUS <!-- Teks judul bagian -->
                </h2> <!-- Tutup teks hasil sukses -->

                <p> <!-- Paragraf -->
                    Selamat! Kamu dinyatakan <!-- Teks paragraf -->
                    <strong>lulus</strong> <!-- Teks tebal berisi "lulus" -->
                    dalam seleksi PMB. <!-- Teks yang ditampilkan di halaman -->
                </p> <!-- Tutup paragraf -->


            <?php elseif ( // Kalau belum, cek kondisi lain (elseif)
                $data['status'] === 'Tidak Lulus' // Status 'Tidak Lulus'
            ):  // Akhir kondisi, lanjut ke tampilan HTML ?>

                <h2 class="result-danger"> <!-- Judul bagian: teks hasil gagal -->
                    TIDAK LULUS <!-- Teks judul bagian -->
                </h2> <!-- Tutup teks hasil gagal -->

                <p> <!-- Paragraf -->
                    Mohon maaf, kamu belum dinyatakan <!-- Teks paragraf -->
                    lulus dalam seleksi PMB. <!-- Teks paragraf -->
                </p> <!-- Tutup paragraf -->


            <?php else:  // Kalau kondisi di atas tidak terpenuhi ?>

                <h2 class="result-pending"> <!-- Judul bagian: teks hasil menunggu -->
                    BELUM ADA HASIL <!-- Teks judul bagian -->
                </h2> <!-- Tutup teks hasil menunggu -->

                <p> <!-- Paragraf -->
                    Hasil test kamu belum tersedia. <!-- Teks paragraf -->
                    Silakan menunggu informasi selanjutnya. <!-- Teks paragraf -->
                </p> <!-- Tutup paragraf -->

            <?php endif;  // Akhir blok if ?>


        </section> <!-- Tutup kartu hasil -->



        <!-- ==================================================
             DETAIL HASIL TEST
        ================================================== -->

        <section class="content-card"> <!-- Bagian: kartu konten -->


            <div class="section-title"> <!-- Wadah: judul bagian -->

                <div> <!-- Wadah -->

                    <h2> <!-- Judul bagian -->
                        Detail Hasil Test <!-- Teks judul bagian -->
                    </h2> <!-- Tutup judul bagian -->

                    <p> <!-- Paragraf -->
                        Informasi hasil seleksi kamu <!-- Teks paragraf -->
                    </p> <!-- Tutup paragraf -->

                </div> <!-- Tutup wadah -->

            </div> <!-- Tutup judul bagian -->


            <div class="data-grid"> <!-- Wadah: grid data -->


                <!-- NAMA -->

                <div class="data-item"> <!-- Wadah: satu item data -->

                    <span> <!-- Teks keterangan -->
                        Nama Lengkap <!-- Teks keterangan -->
                    </span> <!-- Tutup teks keterangan -->

                    <strong> <!-- Teks tebal -->

                        <?= htmlspecialchars( // Tampilkan teks berikut dengan aman (di-escape dari XSS)
                            $data['nama'] // Data nama lengkap dari array data
                        );  // Akhir pemanggilan fungsi ?>

                    </strong> <!-- Tutup teks tebal -->

                </div> <!-- Tutup satu item data -->


                <!-- NOMOR TEST -->

                <div class="data-item"> <!-- Wadah: satu item data -->

                    <span> <!-- Teks keterangan -->
                        Nomor Test <!-- Teks keterangan -->
                    </span> <!-- Tutup teks keterangan -->

                    <strong> <!-- Teks tebal -->

                        <?= htmlspecialchars( // Tampilkan teks berikut dengan aman (di-escape dari XSS)
                            $data['nomor_test'] ?? '-' // Data nomor test dari array data, pakai '-' jika kosong
                        );  // Akhir pemanggilan fungsi ?>

                    </strong> <!-- Tutup teks tebal -->

                </div> <!-- Tutup satu item data -->


                <!-- PROGRAM STUDI -->

                <div class="data-item"> <!-- Wadah: satu item data -->

                    <span> <!-- Teks keterangan -->
                        Program Studi <!-- Teks keterangan -->
                    </span> <!-- Tutup teks keterangan -->

                    <strong> <!-- Teks tebal -->

                        <?= htmlspecialchars( // Tampilkan teks berikut dengan aman (di-escape dari XSS)
                            $data['prodi'] ?? '-' // Data program studi dari array data, pakai '-' jika kosong
                        );  // Akhir pemanggilan fungsi ?>

                    </strong> <!-- Tutup teks tebal -->

                </div> <!-- Tutup satu item data -->


                <!-- JUMLAH SOAL -->

                <div class="data-item"> <!-- Wadah: satu item data -->

                    <span> <!-- Teks keterangan -->
                        Jumlah Soal <!-- Teks keterangan -->
                    </span> <!-- Tutup teks keterangan -->

                    <strong> <!-- Teks tebal -->

                        <?= htmlspecialchars( // Tampilkan teks berikut dengan aman (di-escape dari XSS)
                            $data['jumlah_soal'] ?? '0' // Data jumlah soal dari array data, pakai '0' jika kosong
                        );  // Akhir pemanggilan fungsi ?>

                        Soal <!-- Teks tebal -->

                    </strong> <!-- Tutup teks tebal -->

                </div> <!-- Tutup satu item data -->


                <!-- JAWABAN BENAR -->

                <div class="data-item"> <!-- Wadah: satu item data -->

                    <span> <!-- Teks keterangan -->
                        Jawaban Benar <!-- Teks keterangan -->
                    </span> <!-- Tutup teks keterangan -->

                    <strong> <!-- Teks tebal -->

                        <?= htmlspecialchars( // Tampilkan teks berikut dengan aman (di-escape dari XSS)
                            $data['jawaban_benar'] ?? '0' // Data jumlah jawaban benar dari array data, pakai '0' jika kosong
                        );  // Akhir pemanggilan fungsi ?>

                        Jawaban <!-- Teks tebal -->

                    </strong> <!-- Tutup teks tebal -->

                </div> <!-- Tutup satu item data -->


                <!-- NILAI -->

                <div class="data-item"> <!-- Wadah: satu item data -->

                    <span> <!-- Teks keterangan -->
                        Nilai <!-- Teks keterangan -->
                    </span> <!-- Tutup teks keterangan -->

                    <strong> <!-- Teks tebal -->

                        <?= htmlspecialchars( // Tampilkan teks berikut dengan aman (di-escape dari XSS)
                            $data['nilai'] ?? '0.00' // Data nilai dari array data, pakai '0.00' jika kosong
                        );  // Akhir pemanggilan fungsi ?>

                    </strong> <!-- Tutup teks tebal -->

                </div> <!-- Tutup satu item data -->


                <!-- STATUS -->

                <div class="data-item"> <!-- Wadah: satu item data -->

                    <span> <!-- Teks keterangan -->
                        Status <!-- Teks keterangan -->
                    </span> <!-- Tutup teks keterangan -->

                    <strong> <!-- Teks tebal -->

                        <?= htmlspecialchars( // Tampilkan teks berikut dengan aman (di-escape dari XSS)
                            $data['status'] ?? 'Belum Ada' // Data status dari array data, pakai 'Belum Ada' jika kosong
                        );  // Akhir pemanggilan fungsi ?>

                    </strong> <!-- Tutup teks tebal -->

                </div> <!-- Tutup satu item data -->


                <!-- TANGGAL TEST -->

                <div class="data-item"> <!-- Wadah: satu item data -->

                    <span> <!-- Teks keterangan -->
                        Tanggal Test <!-- Teks keterangan -->
                    </span> <!-- Tutup teks keterangan -->

                    <strong> <!-- Teks tebal -->

                        <?php // Awal blok kode PHP

                        if ( // Mulai pengecekan kondisi (if)
                            !empty( // Tidak kosong:
                                $data['tanggal_test'] // Data tanggal test dari array data
                            ) // Penutup tanda kurung
                        ) { // Akhir kondisi; blok di bawah jalan jika terpenuhi

                            echo date( // Tampilkan tanggal dengan format tertentu
                                'd F Y, H:i', // Format: tanggal bulan tahun, jam:menit
                                strtotime( // Ubah teks tanggal jadi angka waktu
                                    $data['tanggal_test'] // Data tanggal test dari array data
                                ) // Penutup tanda kurung
                            ); // Akhir pemanggilan fungsi

                        } else { // Kalau kondisi di atas tidak terpenuhi

                            echo "-"; // Tampilkan tanda strip

                        } // Akhir blok else

                         // Akhir blok kode PHP ?>

                    </strong> <!-- Tutup teks tebal -->

                </div> <!-- Tutup satu item data -->


            </div> <!-- Tutup grid data -->

        </section> <!-- Tutup kartu konten -->



        <!-- ==================================================
             INFORMASI SELANJUTNYA
        ================================================== -->

        <?php // Awal blok kode PHP

        if ( // Mulai pengecekan kondisi (if)
            $data['status'] === 'Lulus' // Status 'Lulus'
        ): // Akhir kondisi, lanjut ke tampilan HTML

         // Akhir blok kode PHP ?>

            <section class="content-card"> <!-- Bagian: kartu konten -->

                <div class="info-box"> <!-- Wadah: kotak informasi -->

                    <h3> <!-- Sub-judul -->
                        🎓 Selanjutnya <!-- Teks sub-judul -->
                    </h3> <!-- Tutup sub-judul -->

                    <p> <!-- Paragraf -->
                        Selamat! Kamu dapat melanjutkan <!-- Teks paragraf -->
                        ke tahap <strong>Daftar Ulang</strong> <!-- Teks tebal berisi "ke tahap Daftar Ulang" -->
                        untuk menyelesaikan proses <!-- Teks yang ditampilkan di halaman -->
                        pendaftaran mahasiswa baru. <!-- Teks yang ditampilkan di halaman -->
                    </p> <!-- Tutup paragraf -->

                    <br> <!-- Baris baru (jarak) -->

                    <a
                        href="daftar_ulang.php"
                        class="btn-primary"
                    > <!-- Tombol utama menuju Daftar Ulang -->
                        Daftar Ulang <!-- Teks pada link/tombol -->
                    </a> <!-- Tutup link Daftar Ulang -->

                </div> <!-- Tutup kotak informasi -->

            </section> <!-- Tutup kartu konten -->

        <?php endif;  // Akhir blok if ?>


    </main> <!-- Tutup konten utama halaman -->

</div> <!-- Tutup pembungkus layout dashboard -->

</body> <!-- Tutup isi halaman (body) -->

</html> <!-- Tutup tag html -->
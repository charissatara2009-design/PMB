<?php // Awal blok kode PHP

require_once "../config/auth.php"; // Muat file pengecekan login (auth)
require_once "../config/database.php"; // Muat file koneksi database

cekAdmin(); // Pastikan yang masuk adalah admin, kalau bukan diarahkan keluar


// ==========================================
// TOTAL CALON MABA
// ==========================================

$query_total = "SELECT COUNT(*) AS total FROM users WHERE role = 'user'"; // Query SQL: hitung jumlah calon maba

$result_total = mysqli_query($conn, $query_total); // Jalankan query

$data_total = mysqli_fetch_assoc($result_total); // Ambil satu baris hasil query dalam bentuk array

$total_maba = $data_total['total']; // Ambil nilai total dari hasil query


// ==========================================
// TOTAL LULUS
// ==========================================

// Query SQL: hitung jumlah calon maba yang Lulus test
$query_lulus = "SELECT COUNT(*) AS total
                FROM users
                WHERE role = 'user'
                AND status_test = 'Lulus'";

$result_lulus = mysqli_query($conn, $query_lulus); // Jalankan query

$data_lulus = mysqli_fetch_assoc($result_lulus); // Ambil satu baris hasil query dalam bentuk array

$total_lulus = $data_lulus['total']; // Ambil nilai total dari hasil query


// ==========================================
// TOTAL TIDAK LULUS
// ==========================================

// Query SQL: hitung jumlah calon maba yang Tidak Lulus
$query_tidak_lulus = "SELECT COUNT(*) AS total
                      FROM users
                      WHERE role = 'user'
                      AND status_test = 'Tidak Lulus'";

$result_tidak_lulus = mysqli_query($conn, $query_tidak_lulus); // Jalankan query

$data_tidak_lulus = mysqli_fetch_assoc($result_tidak_lulus); // Ambil satu baris hasil query dalam bentuk array

$total_tidak_lulus = $data_tidak_lulus['total']; // Ambil nilai total dari hasil query


// ==========================================
// TOTAL DAFTAR ULANG
// ==========================================

// Query SQL: hitung jumlah calon maba yang sudah daftar ulang
$query_daftar_ulang = "SELECT COUNT(*) AS total
                       FROM users
                       WHERE role = 'user'
                       AND status_daftar_ulang = 'Sudah'";

$result_daftar_ulang = mysqli_query( // Jalankan query hitung daftar ulang
    $conn, // Koneksi database
    $query_daftar_ulang // Teks query SQL yang akan dijalankan
); // Akhir pemanggilan fungsi

$data_daftar_ulang = mysqli_fetch_assoc( // Ambil satu baris hasil query dalam bentuk array
    $result_daftar_ulang // Hasil query
); // Akhir pemanggilan fungsi

$total_daftar_ulang = $data_daftar_ulang['total']; // Ambil nilai total dari hasil query


// ==========================================
// DATA USER TERBARU
// ==========================================

// Query SQL: ambil data dari tabel users, khusus calon maba (role user), yang terbaru dulu, maksimal 5 data
$query_terbaru = "SELECT *
                  FROM users
                  WHERE role = 'user'
                  ORDER BY created_at DESC
                  LIMIT 5";

$result_terbaru = mysqli_query( // Jalankan query data terbaru
    $conn, // Koneksi database
    $query_terbaru // Teks query SQL yang akan dijalankan
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
        Dashboard Admin - Universitas NaraDipa 
    </title> <!-- Tutup judul tab -->

    <link
        rel="stylesheet"
        href="../css/style.css"
    > <!-- Memuat file CSS utama -->

</head> <!-- Tutup bagian head -->

<body> <!-- Awal isi halaman yang tampil -->

<div class="dashboard"> <!-- Wadah: pembungkus layout dashboard -->


    <!-- =====================================
         SIDEBAR
    ====================================== -->

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


            <a
                href="dashboard.php"
                class="active"
            > <!-- Link menu Dashboard (menu aktif) -->

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



    <!-- =====================================
         MAIN CONTENT
    ====================================== -->

    <main class="main-content"> <!-- Konten utama halaman -->


        <!-- HEADER -->

        <header class="dashboard-header"> <!-- Header halaman -->

            <div> <!-- Wadah -->

                <h1> <!-- Judul utama -->
                    Dashboard <!-- Teks judul halaman -->
                </h1> <!-- Tutup judul utama -->

                <p> <!-- Paragraf -->
                    Selamat datang di halaman administrator PMB <!-- Teks paragraf -->
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



        <!-- =====================================
             STATISTIK
        ====================================== -->

        <section class="dashboard-cards"> <!-- Bagian: deretan kartu status -->


            <!-- TOTAL MABA -->

            <div class="dashboard-card"> <!-- Wadah: kartu status -->

                <div class="card-icon"> <!-- Wadah: ikon kartu -->
                    👥 <!-- Ikon emoji penghias -->
                </div> <!-- Tutup ikon kartu -->

                <div> <!-- Wadah -->

                    <span> <!-- Teks keterangan -->
                        Total Calon Maba <!-- Teks keterangan -->
                    </span> <!-- Tutup teks keterangan -->

                    <h3> <!-- Sub-judul -->
                        <?= $total_maba;  // Tampilkan isi variabel $total_maba ?>
                    </h3> <!-- Tutup sub-judul -->

                </div> <!-- Tutup wadah -->

            </div> <!-- Tutup kartu status -->


            <!-- LULUS -->

            <div class="dashboard-card"> <!-- Wadah: kartu status -->

                <div class="card-icon"> <!-- Wadah: ikon kartu -->
                    🎉 <!-- Ikon emoji penghias -->
                </div> <!-- Tutup ikon kartu -->

                <div> <!-- Wadah -->

                    <span> <!-- Teks keterangan -->
                        Lulus Test <!-- Teks keterangan -->
                    </span> <!-- Tutup teks keterangan -->

                    <h3> <!-- Sub-judul -->
                        <?= $total_lulus;  // Tampilkan isi variabel $total_lulus ?>
                    </h3> <!-- Tutup sub-judul -->

                </div> <!-- Tutup wadah -->

            </div> <!-- Tutup kartu status -->


            <!-- TIDAK LULUS -->

            <div class="dashboard-card"> <!-- Wadah: kartu status -->

                <div class="card-icon"> <!-- Wadah: ikon kartu -->
                    📄 <!-- Ikon emoji penghias -->
                </div> <!-- Tutup ikon kartu -->

                <div> <!-- Wadah -->

                    <span> <!-- Teks keterangan -->
                        Tidak Lulus <!-- Teks keterangan -->
                    </span> <!-- Tutup teks keterangan -->

                    <h3> <!-- Sub-judul -->
                        <?= $total_tidak_lulus;  // Tampilkan isi variabel $total_tidak_lulus ?>
                    </h3> <!-- Tutup sub-judul -->

                </div> <!-- Tutup wadah -->

            </div> <!-- Tutup kartu status -->


            <!-- DAFTAR ULANG -->

            <div class="dashboard-card"> <!-- Wadah: kartu status -->

                <div class="card-icon"> <!-- Wadah: ikon kartu -->
                    📋 <!-- Ikon emoji penghias -->
                </div> <!-- Tutup ikon kartu -->

                <div> <!-- Wadah -->

                    <span> <!-- Teks keterangan -->
                        Daftar Ulang <!-- Teks keterangan -->
                    </span> <!-- Tutup teks keterangan -->

                    <h3> <!-- Sub-judul -->
                        <?= $total_daftar_ulang;  // Tampilkan isi variabel $total_daftar_ulang ?>
                    </h3> <!-- Tutup sub-judul -->

                </div> <!-- Tutup wadah -->

            </div> <!-- Tutup kartu status -->

        </section> <!-- Tutup deretan kartu status -->



        <!-- =====================================
             PENDAFTARAN TERBARU
        ====================================== -->

        <section class="content-card"> <!-- Bagian: kartu konten -->


            <div class="section-title"> <!-- Wadah: judul bagian -->

                <div> <!-- Wadah -->

                    <h2> <!-- Judul bagian -->
                        Pendaftaran Terbaru <!-- Teks judul bagian -->
                    </h2> <!-- Tutup judul bagian -->

                    <p> <!-- Paragraf -->
                        Calon mahasiswa yang baru melakukan <!-- Teks paragraf -->
                        pendaftaran <!-- Teks paragraf -->
                    </p> <!-- Tutup paragraf -->

                </div> <!-- Tutup wadah -->


                <a
                    href="data_maba.php"
                    class="see-all"
                > <!-- Link ke Data Calon Maba -->
                    Lihat Semua → <!-- Teks pada link/tombol -->
                </a> <!-- Tutup link Data Calon Maba -->

            </div> <!-- Tutup judul bagian -->



            <!-- TABLE -->

            <div class="table-container"> <!-- Wadah: pembungkus tabel -->

                <table> <!-- Tabel -->

                    <thead> <!-- Kepala tabel -->

                        <tr> <!-- Baris tabel -->

                            <th> <!-- Judul kolom -->
                                No <!-- Teks yang ditampilkan di halaman -->
                            </th> <!-- Tutup judul kolom -->

                            <th> <!-- Judul kolom -->
                                Nama <!-- Teks yang ditampilkan di halaman -->
                            </th> <!-- Tutup judul kolom -->

                            <th> <!-- Judul kolom -->
                                Nomor Test <!-- Teks yang ditampilkan di halaman -->
                            </th> <!-- Tutup judul kolom -->

                            <th> <!-- Judul kolom -->
                                Program Studi <!-- Teks yang ditampilkan di halaman -->
                            </th> <!-- Tutup judul kolom -->

                            <th> <!-- Judul kolom -->
                                Status <!-- Teks yang ditampilkan di halaman -->
                            </th> <!-- Tutup judul kolom -->

                            <th> <!-- Judul kolom -->
                                Tanggal Daftar <!-- Teks yang ditampilkan di halaman -->
                            </th> <!-- Tutup judul kolom -->

                        </tr> <!-- Tutup baris tabel -->

                    </thead> <!-- Tutup kepala tabel -->


                    <tbody> <!-- Isi tabel -->

                    <?php // Awal blok kode PHP

                    $no = 1; // Nomor urut mulai dari 1

                    if (mysqli_num_rows($result_terbaru) > 0): // Kalau ada data terbaru

                        while ( // Ulangi selama masih ada baris
                            $row = // Ambil satu baris data
                            mysqli_fetch_assoc( // Ambil satu baris soal
                                $result_terbaru // Hasil query
                            ) // Penutup tanda kurung
                        ): // Akhir kondisi, lanjut ke tampilan HTML

                     // Akhir blok kode PHP ?>

                        <tr> <!-- Baris tabel -->

                            <td> <!-- Sel tabel -->
                                <?= $no++;  // Tampilkan isi variabel $no lalu tambah 1 ?>
                            </td> <!-- Tutup sel tabel -->


                            <td> <!-- Sel tabel -->

                                <strong> <!-- Teks tebal -->
                                    <?= htmlspecialchars( // Tampilkan teks berikut dengan aman (di-escape dari XSS)
                                        $row['nama'] // Data nama lengkap dari array row
                                    );  // Akhir pemanggilan fungsi ?>
                                </strong> <!-- Tutup teks tebal -->

                            </td> <!-- Tutup sel tabel -->


                            <td> <!-- Sel tabel -->

                                <?= htmlspecialchars( // Tampilkan teks berikut dengan aman (di-escape dari XSS)
                                    $row['nomor_test'] // Data nomor test dari array row
                                );  // Akhir pemanggilan fungsi ?>

                            </td> <!-- Tutup sel tabel -->


                            <td> <!-- Sel tabel -->

                                <?= htmlspecialchars( // Tampilkan teks berikut dengan aman (di-escape dari XSS)
                                    $row['prodi'] ?? '-' // Data program studi dari array row, pakai '-' jika kosong
                                );  // Akhir pemanggilan fungsi ?>

                            </td> <!-- Tutup sel tabel -->


                            <td> <!-- Sel tabel -->

                                <?php if ( // Mulai pengecekan kondisi (if)
                                    $row['status_test'] // Data status test dari array row
                                    === 'Lulus' // sama dengan 'Lulus'
                                ):  // Akhir kondisi, lanjut ke tampilan HTML ?>

                                    <span class="badge success"> <!-- Teks keterangan: label status -->
                                        Lulus <!-- Teks keterangan -->
                                    </span> <!-- Tutup label status -->

                                <?php elseif ( // Kalau belum, cek kondisi lain (elseif)
                                    $row['status_test'] // Data status test dari array row
                                    === 'Tidak Lulus' // sama dengan 'Tidak Lulus'
                                ):  // Akhir kondisi, lanjut ke tampilan HTML ?>

                                    <span class="badge danger"> <!-- Teks keterangan: label status -->
                                        Tidak Lulus <!-- Teks keterangan -->
                                    </span> <!-- Tutup label status -->

                                <?php else:  // Kalau kondisi di atas tidak terpenuhi ?>

                                    <span class="badge warning"> <!-- Teks keterangan: label status -->
                                        Belum Test <!-- Teks keterangan -->
                                    </span> <!-- Tutup label status -->

                                <?php endif;  // Akhir blok if ?>

                            </td> <!-- Tutup sel tabel -->


                            <td> <!-- Sel tabel -->

                                <?= date( // Tampilkan tanggal dengan format tertentu
                                    'd M Y', // Format: tanggal, bulan singkat, tahun
                                    strtotime( // Ubah teks tanggal jadi angka waktu
                                        $row['created_at'] // Data created_at dari array row
                                    ) // Penutup tanda kurung
                                );  // Akhir pemanggilan fungsi ?>

                            </td> <!-- Tutup sel tabel -->

                        </tr> <!-- Tutup baris tabel -->


                    <?php // Awal blok kode PHP

                        endwhile; // Akhir perulangan while

                    else: // Kalau kondisi di atas tidak terpenuhi

                     // Akhir blok kode PHP ?>

                        <tr> <!-- Baris tabel -->

                            <td
                                colspan="6"
                                class="empty-table"
                            > <!-- Sel tabel: pesan tabel kosong -->

                                Belum ada calon mahasiswa <!-- Teks yang ditampilkan di halaman -->
                                yang mendaftar. <!-- Teks yang ditampilkan di halaman -->

                            </td> <!-- Tutup pesan tabel kosong -->

                        </tr> <!-- Tutup baris tabel -->

                    <?php endif;  // Akhir blok if ?>

                    </tbody> <!-- Tutup isi tabel -->

                </table> <!-- Tutup tabel -->

            </div> <!-- Tutup pembungkus tabel -->

        </section> <!-- Tutup kartu konten -->



        <!-- =====================================
             MENU CEPAT
        ====================================== -->

        <section class="content-card"> <!-- Bagian: kartu konten -->


            <div class="section-title"> <!-- Wadah: judul bagian -->

                <div> <!-- Wadah -->

                    <h2> <!-- Judul bagian -->
                        Menu Pengelolaan <!-- Teks judul bagian -->
                    </h2> <!-- Tutup judul bagian -->

                    <p> <!-- Paragraf -->
                        Kelola data PMB dengan mudah <!-- Teks paragraf -->
                    </p> <!-- Tutup paragraf -->

                </div> <!-- Tutup wadah -->

            </div> <!-- Tutup judul bagian -->



            <div class="quick-menu"> <!-- Wadah: menu cepat -->


                <a href="data_maba.php"> <!-- Link ke Data Calon Maba -->

                    <div class="quick-icon"> <!-- Wadah: ikon menu cepat -->
                        👥 <!-- Ikon emoji penghias -->
                    </div> <!-- Tutup ikon menu cepat -->

                    <div> <!-- Wadah -->

                        <strong> <!-- Teks tebal -->
                            Data Calon Maba <!-- Teks tebal -->
                        </strong> <!-- Tutup teks tebal -->

                        <span> <!-- Teks keterangan -->
                            Kelola data calon mahasiswa <!-- Teks keterangan -->
                        </span> <!-- Tutup teks keterangan -->

                    </div> <!-- Tutup wadah -->

                </a> <!-- Tutup link Data Calon Maba -->



                <a href="data_soal.php"> <!-- Link ke Data Soal Test -->

                    <div class="quick-icon"> <!-- Wadah: ikon menu cepat -->
                        📝 <!-- Ikon emoji penghias -->
                    </div> <!-- Tutup ikon menu cepat -->

                    <div> <!-- Wadah -->

                        <strong> <!-- Teks tebal -->
                            Data Soal Test <!-- Teks tebal -->
                        </strong> <!-- Tutup teks tebal -->

                        <span> <!-- Teks keterangan -->
                            Kelola soal ujian <!-- Teks keterangan -->
                        </span> <!-- Tutup teks keterangan -->

                    </div> <!-- Tutup wadah -->

                </a> <!-- Tutup link Data Soal Test -->



                <a href="hasil_test.php"> <!-- Link ke Hasil Test -->

                    <div class="quick-icon"> <!-- Wadah: ikon menu cepat -->
                        📊 <!-- Ikon emoji penghias -->
                    </div> <!-- Tutup ikon menu cepat -->

                    <div> <!-- Wadah -->

                        <strong> <!-- Teks tebal -->
                            Hasil Test <!-- Teks tebal -->
                        </strong> <!-- Tutup teks tebal -->

                        <span> <!-- Teks keterangan -->
                            Kelola hasil test <!-- Teks keterangan -->
                        </span> <!-- Tutup teks keterangan -->

                    </div> <!-- Tutup wadah -->

                </a> <!-- Tutup link Hasil Test -->



                <a href="daftar_ulang.php"> <!-- Link ke Daftar Ulang -->

                    <div class="quick-icon"> <!-- Wadah: ikon menu cepat -->
                        📋 <!-- Ikon emoji penghias -->
                    </div> <!-- Tutup ikon menu cepat -->

                    <div> <!-- Wadah -->

                        <strong> <!-- Teks tebal -->
                            Daftar Ulang <!-- Teks tebal -->
                        </strong> <!-- Tutup teks tebal -->

                        <span> <!-- Teks keterangan -->
                            Kelola daftar ulang <!-- Teks keterangan -->
                        </span> <!-- Tutup teks keterangan -->

                    </div> <!-- Tutup wadah -->

                </a> <!-- Tutup link Daftar Ulang -->

            </div> <!-- Tutup menu cepat -->

        </section> <!-- Tutup kartu konten -->

    </main> <!-- Tutup konten utama halaman -->

</div> <!-- Tutup pembungkus layout dashboard -->

</body> <!-- Tutup isi halaman (body) -->

</html> <!-- Tutup tag html -->
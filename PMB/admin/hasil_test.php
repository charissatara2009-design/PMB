<?php // Awal blok kode PHP

require_once "../config/auth.php"; // Muat file pengecekan login (auth)
require_once "../config/database.php"; // Muat file koneksi database

cekAdmin(); // Pastikan yang masuk adalah admin, kalau bukan diarahkan keluar


// ==================================================
// HITUNG JUMLAH LULUS DAN TIDAK LULUS
// ==================================================

$query_lulus = mysqli_query( // Jalankan query dan simpan hasilnya
    $conn, // Koneksi database
    // Query SQL: hitung jumlah calon maba yang Lulus test
    "SELECT COUNT(*) AS total
     FROM users
     WHERE role = 'user'
     AND status_test = 'Lulus'"
); // Akhir pemanggilan fungsi

$total_lulus = mysqli_fetch_assoc($query_lulus)['total']; // Ambil satu baris hasil query dalam bentuk array


$query_tidak_lulus = mysqli_query( // Jalankan query dan simpan hasilnya
    $conn, // Koneksi database
    // Query SQL: hitung jumlah calon maba yang Tidak Lulus
    "SELECT COUNT(*) AS total
     FROM users
     WHERE role = 'user'
     AND status_test = 'Tidak Lulus'"
); // Akhir pemanggilan fungsi

$total_tidak_lulus = mysqli_fetch_assoc( // Ambil satu baris hasil query dalam bentuk array
    $query_tidak_lulus // Teks query SQL yang akan dijalankan
)['total']; // Ambil kolom total dari hasil


// ==================================================
// AMBIL DATA HASIL TEST
// ==================================================

// Query SQL: ambil data dari tabel users, khusus calon maba (role user), hanya yang sudah Lulus/Tidak Lulus, yang terbaru dulu
$query = "SELECT
              id,
              nomor_test,
              nama,
              email,
              prodi,
              status_test,
              created_at
          FROM users
          WHERE role = 'user'
          AND status_test IN ('Lulus', 'Tidak Lulus')
          ORDER BY created_at DESC";

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


            <a href="data_soal.php"> <!-- Link ke Data Soal Test -->

                📝 <!-- Ikon emoji penghias -->

                <span> <!-- Teks keterangan -->
                    Data Soal Test <!-- Teks keterangan -->
                </span> <!-- Tutup teks keterangan -->

            </a> <!-- Tutup link Data Soal Test -->


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
                    Data calon mahasiswa yang telah mengikuti test <!-- Teks paragraf -->
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


            <!-- LULUS -->

            <div class="stat-card"> <!-- Wadah: kartu statistik -->

                <div class="stat-icon"> <!-- Wadah: ikon statistik -->
                    ✓ <!-- Ikon emoji penghias -->
                </div> <!-- Tutup ikon statistik -->

                <div> <!-- Wadah -->

                    <span> <!-- Teks keterangan -->
                        Lulus <!-- Teks keterangan -->
                    </span> <!-- Tutup teks keterangan -->

                    <h2> <!-- Judul bagian -->
                        <?= $total_lulus;  // Tampilkan isi variabel $total_lulus ?>
                    </h2> <!-- Tutup judul bagian -->

                    <small> <!-- Teks kecil -->
                        Calon mahasiswa <!-- Teks kecil -->
                    </small> <!-- Tutup teks kecil -->

                </div> <!-- Tutup wadah -->

            </div> <!-- Tutup kartu statistik -->



            <!-- TIDAK LULUS -->

            <div class="stat-card"> <!-- Wadah: kartu statistik -->

                <div class="stat-icon"> <!-- Wadah: ikon statistik -->
                    ✕ <!-- Ikon emoji penghias -->
                </div> <!-- Tutup ikon statistik -->

                <div> <!-- Wadah -->

                    <span> <!-- Teks keterangan -->
                        Tidak Lulus <!-- Teks keterangan -->
                    </span> <!-- Tutup teks keterangan -->

                    <h2> <!-- Judul bagian -->
                        <?= $total_tidak_lulus;  // Tampilkan isi variabel $total_tidak_lulus ?>
                    </h2> <!-- Tutup judul bagian -->

                    <small> <!-- Teks kecil -->
                        Calon mahasiswa <!-- Teks kecil -->
                    </small> <!-- Tutup teks kecil -->

                </div> <!-- Tutup wadah -->

            </div> <!-- Tutup kartu statistik -->


        </div> <!-- Tutup deretan statistik -->



        <!-- ==================================================
             TABEL HASIL TEST
        ================================================== -->

        <section class="content-card"> <!-- Bagian: kartu konten -->


            <div class="section-title"> <!-- Wadah: judul bagian -->

                <div> <!-- Wadah -->

                    <h2> <!-- Judul bagian -->
                        Daftar Hasil Test <!-- Teks judul bagian -->
                    </h2> <!-- Tutup judul bagian -->

                    <p> <!-- Paragraf -->
                        Daftar calon mahasiswa yang telah mendapatkan <!-- Teks paragraf -->
                        hasil test <!-- Teks paragraf -->
                    </p> <!-- Tutup paragraf -->

                </div> <!-- Tutup wadah -->

            </div> <!-- Tutup judul bagian -->



            <!-- TABEL -->

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
                                Email <!-- Teks yang ditampilkan di halaman -->
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

                    if (mysqli_num_rows($result) > 0): // Kalau ada data

                        while ( // Ulangi selama masih ada baris
                            $row = // Ambil satu baris data
                            mysqli_fetch_assoc($result) // Ambil satu baris dari hasil query
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
                                        $row['nomor_test'] ?? '-' // Data nomor test dari array row, pakai '-' jika kosong
                                    );  // Akhir pemanggilan fungsi ?>

                                </strong> <!-- Tutup teks tebal -->

                            </td> <!-- Tutup sel tabel -->


                            <!-- NAMA -->

                            <td> <!-- Sel tabel -->

                                <?= htmlspecialchars( // Tampilkan teks berikut dengan aman (di-escape dari XSS)
                                    $row['nama'] // Data nama lengkap dari array row
                                );  // Akhir pemanggilan fungsi ?>

                            </td> <!-- Tutup sel tabel -->


                            <!-- EMAIL -->

                            <td> <!-- Sel tabel -->

                                <?= htmlspecialchars( // Tampilkan teks berikut dengan aman (di-escape dari XSS)
                                    $row['email'] // Data email dari array row
                                );  // Akhir pemanggilan fungsi ?>

                            </td> <!-- Tutup sel tabel -->


                            <!-- PRODI -->

                            <td> <!-- Sel tabel -->

                                <?= htmlspecialchars( // Tampilkan teks berikut dengan aman (di-escape dari XSS)
                                    $row['prodi'] ?? '-' // Data program studi dari array row, pakai '-' jika kosong
                                );  // Akhir pemanggilan fungsi ?>

                            </td> <!-- Tutup sel tabel -->


                            <!-- STATUS -->

                            <td> <!-- Sel tabel -->

                                <?php // Awal blok kode PHP

                                if ( // Mulai pengecekan kondisi (if)
                                    $row['status_test'] // Data status test dari array row
                                    === 'Lulus' // sama dengan 'Lulus'
                                ): // Akhir kondisi, lanjut ke tampilan HTML

                                 // Akhir blok kode PHP ?>

                                    <span class="badge success"> <!-- Teks keterangan: label status -->

                                        ✓ Lulus <!-- Teks keterangan -->

                                    </span> <!-- Tutup label status -->

                                <?php else:  // Kalau kondisi di atas tidak terpenuhi ?>

                                    <span class="badge danger"> <!-- Teks keterangan: label status -->

                                        ✕ Tidak Lulus <!-- Teks keterangan -->

                                    </span> <!-- Tutup label status -->

                                <?php endif;  // Akhir blok if ?>

                            </td> <!-- Tutup sel tabel -->


                            <!-- TANGGAL -->

                            <td> <!-- Sel tabel -->

                                <?= !empty( // Tidak kosong:
                                    $row['created_at'] // Data created_at dari array row
                                ) // Penutup tanda kurung
                                    ? date( // Kalau ada: tampilkan tanggal dengan format tertentu
                                        'd M Y', // Format: tanggal, bulan singkat, tahun
                                        strtotime( // Ubah teks tanggal jadi angka waktu
                                            $row['created_at'] // Data created_at dari array row
                                        ) // Penutup tanda kurung
                                    ) // Penutup tanda kurung
                                    : '-'; // Kalau kosong: tampilkan '-'
                                 // Akhir blok kode PHP ?>

                            </td> <!-- Tutup sel tabel -->


                        </tr> <!-- Tutup baris tabel -->

                    <?php // Awal blok kode PHP

                        endwhile; // Akhir perulangan while

                    else: // Kalau kondisi di atas tidak terpenuhi

                     // Akhir blok kode PHP ?>

                        <tr> <!-- Baris tabel -->

                            <td
                                colspan="7"
                                class="empty-table"
                            > <!-- Sel tabel: pesan tabel kosong -->

                                Belum ada hasil test. <!-- Teks yang ditampilkan di halaman -->

                            </td> <!-- Tutup pesan tabel kosong -->

                        </tr> <!-- Tutup baris tabel -->

                    <?php endif;  // Akhir blok if ?>

                    </tbody> <!-- Tutup isi tabel -->

                </table> <!-- Tutup tabel -->

            </div> <!-- Tutup pembungkus tabel -->


        </section> <!-- Tutup kartu konten -->


    </main> <!-- Tutup konten utama halaman -->

</div> <!-- Tutup pembungkus layout dashboard -->

</body> <!-- Tutup isi halaman (body) -->

</html> <!-- Tutup tag html -->
<?php // Awal blok kode PHP

require_once "../config/auth.php"; // Muat file pengecekan login (auth)
require_once "../config/database.php"; // Muat file koneksi database

cekAdmin(); // Pastikan yang masuk adalah admin, kalau bukan diarahkan keluar


// ==================================================
// HAPUS DATA
// ==================================================

if (isset($_GET['hapus'])) { // Jalankan jika ada perintah hapus di URL

    $id = $_GET['hapus']; // Ambil ID data yang mau dihapus dari URL

    $stmt = mysqli_prepare( // Siapkan query (prepared statement) agar aman dari SQL injection
        $conn, // Koneksi database
        "DELETE FROM users WHERE id = ? AND role = 'user'" // Query SQL: hapus data dari tabel users (hanya yang role-nya user)
    ); // Akhir pemanggilan fungsi

    mysqli_stmt_bind_param( // Isi tanda tanya (?) pada query dengan nilai yang aman
        $stmt, // Statement query yang sudah disiapkan
        "i", // Tipe data tiap nilai (i = angka bulat, d = desimal, s = teks)
        $id // ID data
    ); // Akhir pemanggilan fungsi

    mysqli_stmt_execute($stmt); // Jalankan query


    // Tampilkan alert "Data berhasil dihapus!" lalu pindah ke data_maba.php
    echo "<script>
            alert('Data berhasil dihapus!');
            window.location='data_maba.php';
          </script>";

    exit; // Hentikan proses halaman di sini
} // Akhir blok if


// ==================================================
// AMBIL DATA USER
// ==================================================

// Query SQL: ambil data dari tabel users, khusus calon maba (role user), yang terbaru dulu
$query = "SELECT *
          FROM users
          WHERE role = 'user'
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
        Data Calon Maba - Universitas NaraDipa 
    </title> <!-- Tutup judul tab -->

    <link
        rel="stylesheet"
        href="../css/style.css"
    > <!-- Memuat file CSS utama -->

    <style> /* Awal blok CSS (gaya khusus halaman ini) */

        .action-buttons { /* Gaya untuk tombol aksi */
            display: flex; /* Susun isi dengan flexbox */
            align-items: center; /* Posisi isi tegak lurus: center */
            gap: 10px; /* Jarak antar elemen 10px */
            flex-wrap: wrap; /* Boleh turun ke baris baru jika sempit */
        } /* Akhir aturan CSS */

        .action-buttons .btn-edit, /* Selektor yang berbagi gaya dengan selektor berikutnya */
        .action-buttons .btn-delete { /* Gaya untuk tombol hapus */
            display: inline-block; /* Tipe tampilan: inline-block */
            padding: 6px 14px; /* Jarak dalam elemen 6px 14px */
            border-radius: 8px; /* Sudut melengkung 8px */
            font-size: 13px; /* Ukuran huruf 13px */
            font-weight: 600; /* Ketebalan huruf 600 */
            text-decoration: none; /* Hilangkan garis bawah link */
            white-space: nowrap; /* Teks tidak boleh turun baris */
            line-height: 1.4; /* Tinggi baris 1.4 */
        } /* Akhir aturan CSS */

        .action-buttons .btn-edit { /* Gaya untuk tombol sekunder */
            background: #e7f4ec; /* Latar belakang #e7f4ec */
            color: #1f8a4c; /* Warna teks #1f8a4c */
        } /* Akhir aturan CSS */

        .action-buttons .btn-delete { /* Gaya untuk tombol hapus */
            background: #fdeaea; /* Latar belakang #fdeaea */
            color: #d93b3b; /* Warna teks #d93b3b */
        } /* Akhir aturan CSS */

        .action-buttons .btn-edit:hover, /* Selektor yang berbagi gaya dengan selektor berikutnya */
        .action-buttons .btn-delete:hover { /* Gaya untuk tombol hapus (saat kursor diarahkan) */
            opacity: 0.85; /* Transparansi 0.85 */
        } /* Akhir aturan CSS */

    </style> <!-- Tutup blok CSS -->

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
                    Data Calon Maba <!-- Teks judul halaman -->
                </h1> <!-- Tutup judul utama -->

                <p> <!-- Paragraf -->
                    Kelola data calon mahasiswa baru <!-- Teks paragraf -->
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
             TOMBOL TAMBAH
        =================================================== -->

        <section class="content-card"> <!-- Bagian: kartu konten -->


            <div class="section-title"> <!-- Wadah: judul bagian -->

                <div> <!-- Wadah -->

                    <h2> <!-- Judul bagian -->
                        Daftar Calon Mahasiswa <!-- Teks judul bagian -->
                    </h2> <!-- Tutup judul bagian -->

                    <p> <!-- Paragraf -->
                        Data seluruh calon mahasiswa yang telah <!-- Teks paragraf -->
                        mendaftar <!-- Teks paragraf -->
                    </p> <!-- Tutup paragraf -->

                </div> <!-- Tutup wadah -->


                <a
                    href="tambah_maba.php"
                    class="btn-primary"
                > <!-- Tombol utama menuju Tambah Calon Maba -->

                    + Tambah Calon Maba <!-- Teks pada link/tombol -->

                </a> <!-- Tutup link Tambah Calon Maba -->

            </div> <!-- Tutup judul bagian -->


            <!-- ==================================================
                 TABEL DATA
            =================================================== -->

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
                                NIK <!-- Teks yang ditampilkan di halaman -->
                            </th> <!-- Tutup judul kolom -->

                            <th> <!-- Judul kolom -->
                                Nomor Test <!-- Teks yang ditampilkan di halaman -->
                            </th> <!-- Tutup judul kolom -->

                            <th> <!-- Judul kolom -->
                                Program Studi <!-- Teks yang ditampilkan di halaman -->
                            </th> <!-- Tutup judul kolom -->

                            <th> <!-- Judul kolom -->
                                Status Test <!-- Teks yang ditampilkan di halaman -->
                            </th> <!-- Tutup judul kolom -->

                            <th> <!-- Judul kolom -->
                                Daftar Ulang <!-- Teks yang ditampilkan di halaman -->
                            </th> <!-- Tutup judul kolom -->

                            <th> <!-- Judul kolom -->
                                Aksi <!-- Teks yang ditampilkan di halaman -->
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
                                    $row['nik'] // Data nik dari array row
                                );  // Akhir pemanggilan fungsi ?>

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

                                <?php // Awal blok kode PHP

                                if ( // Mulai pengecekan kondisi (if)
                                    $row['status_test'] // Data status test dari array row
                                    === 'Lulus' // sama dengan 'Lulus'
                                ): // Akhir kondisi, lanjut ke tampilan HTML

                                 // Akhir blok kode PHP ?>

                                    <span class="badge success"> <!-- Teks keterangan: label status -->
                                        Lulus <!-- Teks keterangan -->
                                    </span> <!-- Tutup label status -->

                                <?php // Awal blok kode PHP

                                elseif ( // Kalau belum, cek kondisi lain (elseif)
                                    $row['status_test'] // Data status test dari array row
                                    === 'Tidak Lulus' // sama dengan 'Tidak Lulus'
                                ): // Akhir kondisi, lanjut ke tampilan HTML

                                 // Akhir blok kode PHP ?>

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

                                <?php // Awal blok kode PHP

                                if ( // Mulai pengecekan kondisi (if)
                                    $row[ // Ambil dari baris data
                                        'status_daftar_ulang' // kolom status daftar ulang
                                    ] === 'Sudah' // sama dengan 'Sudah'
                                ): // Akhir kondisi, lanjut ke tampilan HTML

                                 // Akhir blok kode PHP ?>

                                    <span class="badge success"> <!-- Teks keterangan: label status -->
                                        Sudah <!-- Teks keterangan -->
                                    </span> <!-- Tutup label status -->

                                <?php else:  // Kalau kondisi di atas tidak terpenuhi ?>

                                    <span class="badge warning"> <!-- Teks keterangan: label status -->
                                        Belum <!-- Teks keterangan -->
                                    </span> <!-- Tutup label status -->

                                <?php endif;  // Akhir blok if ?>

                            </td> <!-- Tutup sel tabel -->


                            <td> <!-- Sel tabel -->

                                <div class="action-buttons"> <!-- Wadah: tombol aksi -->


                                    <a
                                        href="edit_maba.php?id=<?= $row['id']; ?>"
                                        class="btn-edit"
                                    > <!-- Tombol menuju Edit Calon Maba (dengan ID data) -->
                                        Edit <!-- Teks pada link/tombol -->
                                    </a> <!-- Tutup link Edit Calon Maba (dengan ID data) -->


                                    <a
                                        href="data_maba.php?hapus=<?= $row['id']; ?>"
                                        class="btn-delete"
                                        onclick="return confirm(
                                            'Yakin ingin menghapus data ini?'
                                        );"
                                    > <!-- Link ke Data Calon Maba (perintah hapus data) -->
                                        Hapus <!-- Teks pada link/tombol -->
                                    </a> <!-- Tutup link Data Calon Maba (perintah hapus data) -->

                                </div> <!-- Tutup tombol aksi -->

                            </td> <!-- Tutup sel tabel -->

                        </tr> <!-- Tutup baris tabel -->


                    <?php // Awal blok kode PHP

                        endwhile; // Akhir perulangan while

                    else: // Kalau kondisi di atas tidak terpenuhi

                     // Akhir blok kode PHP ?>

                        <tr> <!-- Baris tabel -->

                            <td
                                colspan="8"
                                class="empty-table"
                            > <!-- Sel tabel: pesan tabel kosong -->

                                Belum ada data calon mahasiswa. <!-- Teks yang ditampilkan di halaman -->

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
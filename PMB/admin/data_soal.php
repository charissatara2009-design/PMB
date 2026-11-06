<?php // Awal blok kode PHP

require_once "../config/auth.php"; // Muat file pengecekan login (auth)
require_once "../config/database.php"; // Muat file koneksi database

cekAdmin(); // Pastikan yang masuk adalah admin, kalau bukan diarahkan keluar


// ==================================================
// HAPUS SOAL
// ==================================================

if (isset($_GET['hapus'])) { // Jalankan jika ada perintah hapus di URL

    $id = $_GET['hapus']; // Ambil ID data yang mau dihapus dari URL

    $stmt = mysqli_prepare( // Siapkan query (prepared statement) agar aman dari SQL injection
        $conn, // Koneksi database
        "DELETE FROM soal WHERE id = ?" // Query SQL: hapus data dari tabel soal
    ); // Akhir pemanggilan fungsi

    mysqli_stmt_bind_param( // Isi tanda tanya (?) pada query dengan nilai yang aman
        $stmt, // Statement query yang sudah disiapkan
        "i", // Tipe data tiap nilai (i = angka bulat, d = desimal, s = teks)
        $id // ID data
    ); // Akhir pemanggilan fungsi

    mysqli_stmt_execute($stmt); // Jalankan query

    // Tampilkan alert "Soal berhasil dihapus!" lalu pindah ke data_soal.php
    echo "<script>
            alert('Soal berhasil dihapus!');
            window.location='data_soal.php';
          </script>";

    exit; // Hentikan proses halaman di sini
} // Akhir blok if


// ==================================================
// DAFTAR PRODI (UNTUK FILTER)
// ==================================================

$daftar_prodi = [ // Daftar program studi (untuk filter)
    'Teknik Informatika', // Prodi: Teknik Informatika
    'Sistem Informasi', // Prodi: Sistem Informasi
    'Manajemen', // Prodi: Manajemen
    'Akuntansi', // Prodi: Akuntansi
    'Ilmu Komunikasi', // Prodi: Ilmu Komunikasi
]; // Akhir daftar


// ==================================================
// FILTER KATEGORI BIDANG STUDI
// ==================================================

$filter_prodi = trim($_GET['filter_prodi'] ?? ''); // Ambil prodi yang dipilih sebagai filter dari URL


// ==================================================
// HITUNG JUMLAH SOAL PER PRODI (UNTUK CHIP FILTER)
// ==================================================

$jumlah_per_prodi = []; // Siapkan array jumlah soal per prodi
$total_soal = 0; // Total soal mulai dari 0

$query_hitung = mysqli_query( // Jalankan query hitung soal per prodi
    $conn, // Koneksi database
    // Query SQL: hitung jumlah soal untuk tiap prodi
    "SELECT prodi, COUNT(*) AS jumlah
     FROM soal
     GROUP BY prodi"
); // Akhir pemanggilan fungsi

while ($baris_hitung = mysqli_fetch_assoc($query_hitung)) { // Ulangi untuk setiap baris hasil hitungan

    $jumlah_per_prodi[$baris_hitung['prodi']] = (int) $baris_hitung['jumlah']; // Simpan jumlah soal untuk prodi ini

    $total_soal += (int) $baris_hitung['jumlah']; // Tambahkan ke total soal
} // Akhir blok perulangan while


// ==================================================
// AMBIL DATA SOAL
// ==================================================

if ($filter_prodi !== '') { // Kalau filter prodi dipilih

    // Query SQL: ambil soal untuk prodi yang difilter, terbaru dulu
    $query = "SELECT *
              FROM soal
              WHERE prodi = ?
              ORDER BY id DESC";

    $stmt_list = mysqli_prepare($conn, $query); // Siapkan query (prepared statement) agar aman dari SQL injection

    mysqli_stmt_bind_param( // Isi tanda tanya (?) pada query dengan nilai yang aman
        $stmt_list, // Statement query yang sudah disiapkan
        "s", // Tipe data tiap nilai (i = angka bulat, d = desimal, s = teks)
        $filter_prodi // Prodi yang difilter
    ); // Akhir pemanggilan fungsi

    mysqli_stmt_execute($stmt_list); // Jalankan query

    $result = mysqli_stmt_get_result($stmt_list); // Ambil hasil query

} else { // Kalau kondisi di atas tidak terpenuhi

    // Query SQL: ambil semua soal, prodi "Semua" ditaruh di atas, lalu urut per prodi dan soal terbaru
    $query = "SELECT *
              FROM soal
              ORDER BY
                  CASE WHEN prodi = 'Semua' THEN 0 ELSE 1 END,
                  prodi ASC,
                  id DESC";

    $result = mysqli_query( // Jalankan query dan simpan hasilnya
        $conn, // Koneksi database
        $query // Teks query SQL yang akan dijalankan
    ); // Akhir pemanggilan fungsi

} // Akhir blok else


// ==================================================
// KELOMPOKKAN SOAL PER PRODI
// ==================================================

$soal_per_prodi = []; // Siapkan array soal per prodi

while ( // Ulangi selama masih ada baris
    $row = // Ambil satu baris data
    mysqli_fetch_assoc($result) // Ambil satu baris dari hasil query
) { // Akhir kondisi; blok di bawah jalan jika terpenuhi

    $soal_per_prodi[$row['prodi']][] = $row; // Kelompokkan soal berdasarkan prodi

} // Akhir blok perulangan while

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
        Data Soal Test - Universitas NaraDipa
</title>

    <link
        rel="stylesheet"
        href="../css/style.css"
    > <!-- Memuat file CSS utama -->

    <style> /* Awal blok CSS (gaya khusus halaman ini) */

        .soalfilter-bar { /* Gaya untuk .soalfilter-bar */
            display: flex; /* Susun isi dengan flexbox */
            flex-wrap: wrap; /* Boleh turun ke baris baru jika sempit */
            gap: 10px; /* Jarak antar elemen 10px */
            margin-bottom: 24px; /* Jarak luar bawah 24px */
        } /* Akhir aturan CSS */

        .soalfilter-chip { /* Gaya untuk .soalfilter-chip */
            display: inline-block; /* Tipe tampilan: inline-block */
            padding: 9px 18px; /* Jarak dalam elemen 9px 18px */
            border-radius: 999px; /* Sudut melengkung 999px */
            border: 1px solid #ebe3e6; /* Garis tepi 1px solid #ebe3e6 */
            background: #ffffff; /* Latar belakang #ffffff */
            color: #2b2229; /* Warna teks #2b2229 */
            font-size: 13.5px; /* Ukuran huruf 13.5px */
            font-weight: 600; /* Ketebalan huruf 600 */
            white-space: nowrap; /* Teks tidak boleh turun baris */
            transition: background 0.2s, color 0.2s, border-color 0.2s, transform 0.15s; /* Perubahan halus (animasi) pada background 0.2s, color 0.2s, border-color 0.2s, transform 0.15s */
        } /* Akhir aturan CSS */

        .soalfilter-chip:hover { /* Gaya untuk .soalfilter-chip:hover (saat kursor diarahkan) */
            border-color: #b23a55; /* Warna garis tepi #b23a55 */
            color: #8a2740; /* Warna teks #8a2740 */
            transform: translateY(-1px); /* Ubah bentuk/putar elemen: translateY(-1px) */
        } /* Akhir aturan CSS */

        .soalfilter-chip.active { /* Gaya untuk menu aktif */
            background: #8a2740; /* Latar belakang #8a2740 */
            border-color: #8a2740; /* Warna garis tepi #8a2740 */
            color: #ffffff; /* Warna teks #ffffff */
            box-shadow: 0 6px 14px rgba(138, 39, 64, 0.25); /* Bayangan 0 6px 14px rgba(138, 39, 64, 0.25) */
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
    =================================================== -->

    <main class="main-content"> <!-- Konten utama halaman -->


        <!-- HEADER -->

        <header class="dashboard-header"> <!-- Header halaman -->

            <div> <!-- Wadah -->

                <h1> <!-- Judul utama -->
                    Data Soal Test <!-- Teks judul halaman -->
                </h1> <!-- Tutup judul utama -->

                <p> <!-- Paragraf -->
                    Kelola soal test calon mahasiswa <!-- Teks paragraf -->
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
             DATA SOAL
        =================================================== -->

        <section class="content-card"> <!-- Bagian: kartu konten -->


            <!-- JUDUL -->

            <div class="section-title"> <!-- Wadah: judul bagian -->

                <div> <!-- Wadah -->

                    <h2> <!-- Judul bagian -->
                        Daftar Soal Test <!-- Teks judul bagian -->
                    </h2> <!-- Tutup judul bagian -->

                    <p> <!-- Paragraf -->
                        Kelola seluruh soal ujian PMB <!-- Teks paragraf -->
                    </p> <!-- Tutup paragraf -->

                </div> <!-- Tutup wadah -->


                <div style="display:flex; align-items:center; gap:10px;"> <!-- Wadah (ada style inline) -->

                    <a
                        href="tambah_soal.php"
                        class="btn-primary"
                    > <!-- Tombol utama menuju Tambah Soal -->

                        + Tambah Soal <!-- Teks pada link/tombol -->

                    </a> <!-- Tutup link Tambah Soal -->

                </div> <!-- Tutup wadah -->

            </div> <!-- Tutup judul bagian -->


            <!-- ==================================================
                 FILTER KATEGORI (CHIP)
            =================================================== -->

            <div class="soalfilter-bar"> <!-- Wadah -->

                <a
                    href="data_soal.php"
                    class="soalfilter-chip <?= $filter_prodi === '' ? 'active' : ''; ?>"
                > <!-- Link ke Data Soal Test -->
                    Semua (<?= $total_soal; ?>) <!-- Tampilkan isi variabel $total_soal setelah teks "Semua (" -->
                </a> <!-- Tutup link Data Soal Test -->

                <a
                    href="data_soal.php?filter_prodi=Semua"
                    class="soalfilter-chip <?= $filter_prodi === 'Semua' ? 'active' : ''; ?>"
                > <!-- Link ke Data Soal Test (dengan ID data) -->
                    Umum / Semua Prodi (<?= $jumlah_per_prodi['Semua'] ?? 0; ?>) <!-- Tampilkan nilai dari PHP setelah teks "Umum / Semua Prodi (" -->
                </a> <!-- Tutup link Data Soal Test (dengan ID data) -->

                <?php foreach ($daftar_prodi as $prodi_opsi):  // Ulangi untuk setiap prodi ?>

                    <a
                        href="data_soal.php?filter_prodi=<?= urlencode($prodi_opsi); ?>"
                        class="soalfilter-chip <?= $filter_prodi === $prodi_opsi ? 'active' : ''; ?>"
                    > <!-- Link ke Data Soal Test (dengan ID data) -->
                        <?= htmlspecialchars($prodi_opsi);  // Tampilkan isi $prodi_opsi (di-escape agar aman dari XSS) ?>
                        (<?= $jumlah_per_prodi[$prodi_opsi] ?? 0; ?>) <!-- Tampilkan nilai dari PHP setelah teks "(" -->
                    </a> <!-- Tutup link Data Soal Test (dengan ID data) -->

                <?php endforeach;  // Akhir perulangan foreach ?>

            </div> <!-- Tutup wadah -->



            <!-- ==================================================
                 LIST SOAL
            =================================================== -->

            <div class="question-list"> <!-- Wadah -->

                <?php if (empty($soal_per_prodi)):  // Kalau belum ada soal sama sekali ?>

                    <div class="empty-card"> <!-- Wadah: kartu kosong -->

                        <div class="empty-icon"> <!-- Wadah: ikon kartu kosong -->
                            📝 <!-- Ikon emoji penghias -->
                        </div> <!-- Tutup ikon kartu kosong -->

                        <h2> <!-- Judul bagian -->
                            Belum Ada Soal <!-- Teks judul bagian -->
                        </h2> <!-- Tutup judul bagian -->

                        <p> <!-- Paragraf -->
                            Silakan tambahkan soal test <!-- Teks paragraf -->
                            menggunakan tombol Tambah Soal. <!-- Teks paragraf -->
                        </p> <!-- Tutup paragraf -->

                    </div> <!-- Tutup kartu kosong -->

                <?php else:  // Kalau kondisi di atas tidak terpenuhi ?>

                    <?php foreach ($soal_per_prodi as $nama_prodi => $daftar_soal_prodi):  // Ulangi untuk tiap kelompok prodi ?>

                        <div
                            class="prodi-group"
                            style="margin-bottom:28px;"
                        > <!-- Wadah (ada style inline) -->

                            <h3
                                class="prodi-group-title"
                                style="
                                    background:#f5f0f0;
                                    padding:10px 16px;
                                    border-radius:10px;
                                    margin-bottom:14px;
                                    display:flex;
                                    align-items:center;
                                    justify-content:space-between;
                                "
                            > <!-- Sub-judul -->

                                <span> <!-- Teks keterangan -->
                                    📂 <!-- Ikon emoji penghias -->
                                    <?= htmlspecialchars($nama_prodi);  // Tampilkan isi $nama_prodi (di-escape agar aman dari XSS) ?>
                                </span> <!-- Tutup teks keterangan -->

                                <span style="font-size:12px; font-weight:400; color:#888;"> <!-- Teks keterangan -->
                                    <?= count($daftar_soal_prodi); ?> soal // Tampilkan jumlah soal pada prodi ini
                                </span> <!-- Tutup teks keterangan -->

                            </h3> <!-- Tutup sub-judul -->


                            <?php $no = 1;  // Nomor urut mulai dari 1 ?>

                            <?php foreach ($daftar_soal_prodi as $row):  // Ulangi untuk setiap soal di prodi ini ?>

                                <div class="question-card"> <!-- Wadah: kartu soal -->


                                    <!-- NOMOR -->

                                    <div class="question-number"> <!-- Wadah: nomor soal -->

                                        <?= $no++;  // Tampilkan isi variabel $no lalu tambah 1 ?>

                                    </div> <!-- Tutup nomor soal -->


                                    <!-- ISI SOAL -->

                                    <div class="question-content"> <!-- Wadah -->


                                        <h3> <!-- Sub-judul -->

                                            <?= htmlspecialchars( // Tampilkan teks berikut dengan aman (di-escape dari XSS)
                                                $row['soal'] // Data teks soal dari array row
                                            );  // Akhir pemanggilan fungsi ?>

                                        </h3> <!-- Tutup sub-judul -->


                                        <!-- PILIHAN -->

                                        <div class="option-list"> <!-- Wadah -->


                                            <div> <!-- Wadah -->

                                                <strong> <!-- Teks tebal -->
                                                    A. <!-- Teks tebal -->
                                                </strong> <!-- Tutup teks tebal -->

                                                <?= htmlspecialchars( // Tampilkan teks berikut dengan aman (di-escape dari XSS)
                                                    $row['pilihan_a'] // Data pilihan A dari array row
                                                );  // Akhir pemanggilan fungsi ?>

                                            </div> <!-- Tutup wadah -->


                                            <div> <!-- Wadah -->

                                                <strong> <!-- Teks tebal -->
                                                    B. <!-- Teks tebal -->
                                                </strong> <!-- Tutup teks tebal -->

                                                <?= htmlspecialchars( // Tampilkan teks berikut dengan aman (di-escape dari XSS)
                                                    $row['pilihan_b'] // Data pilihan B dari array row
                                                );  // Akhir pemanggilan fungsi ?>

                                            </div> <!-- Tutup wadah -->


                                            <div> <!-- Wadah -->

                                                <strong> <!-- Teks tebal -->
                                                    C. <!-- Teks tebal -->
                                                </strong> <!-- Tutup teks tebal -->

                                                <?= htmlspecialchars( // Tampilkan teks berikut dengan aman (di-escape dari XSS)
                                                    $row['pilihan_c'] // Data pilihan C dari array row
                                                );  // Akhir pemanggilan fungsi ?>

                                            </div> <!-- Tutup wadah -->


                                            <div> <!-- Wadah -->

                                                <strong> <!-- Teks tebal -->
                                                    D. <!-- Teks tebal -->
                                                </strong> <!-- Tutup teks tebal -->

                                                <?= htmlspecialchars( // Tampilkan teks berikut dengan aman (di-escape dari XSS)
                                                    $row['pilihan_d'] // Data pilihan D dari array row
                                                );  // Akhir pemanggilan fungsi ?>

                                            </div> <!-- Tutup wadah -->

                                        </div> <!-- Tutup wadah -->


                                        <!-- JAWABAN -->

                                        <div class="correct-answer"> <!-- Wadah -->

                                            Jawaban benar: <!-- Teks yang ditampilkan di halaman -->

                                            <strong> <!-- Teks tebal -->
                                                <?= htmlspecialchars( // Tampilkan teks berikut dengan aman (di-escape dari XSS)
                                                    $row['jawaban_benar'] // Data jumlah jawaban benar dari array row
                                                );  // Akhir pemanggilan fungsi ?>
                                            </strong> <!-- Tutup teks tebal -->

                                        </div> <!-- Tutup wadah -->


                                        <!-- AKSI -->

                                        <div class="question-actions"> <!-- Wadah -->


                                            <a
                                                href="edit_soal.php?id=<?= $row['id']; ?>"
                                                class="btn-edit"
                                            > <!-- Tombol menuju Edit Soal (dengan ID data) -->
                                                Edit <!-- Teks pada link/tombol -->
                                            </a> <!-- Tutup link Edit Soal (dengan ID data) -->


                                            <a
                                                href="data_soal.php?hapus=<?= $row['id']; ?>&filter_prodi=<?= urlencode($filter_prodi); ?>"
                                                class="btn-delete"
                                                onclick="return confirm(
                                                    'Yakin ingin menghapus soal ini?'
                                                );"
                                            > <!-- Link ke Data Soal Test (perintah hapus data) -->
                                                Hapus <!-- Teks pada link/tombol -->
                                            </a> <!-- Tutup link Data Soal Test (perintah hapus data) -->


                                        </div> <!-- Tutup wadah -->

                                    </div> <!-- Tutup wadah -->

                                </div> <!-- Tutup kartu soal -->

                            <?php endforeach;  // Akhir perulangan foreach ?>

                        </div> <!-- Tutup wadah -->

                    <?php endforeach;  // Akhir perulangan foreach ?>

                <?php endif;  // Akhir blok if ?>

            </div> <!-- Tutup wadah -->

        </section> <!-- Tutup kartu konten -->

    </main> <!-- Tutup konten utama halaman -->

</div> <!-- Tutup pembungkus layout dashboard -->


</body> <!-- Tutup isi halaman (body) -->

</html> <!-- Tutup tag html -->
<!DOCTYPE html> <!-- Deklarasi tipe dokumen HTML5 -->
<html lang="id"> <!-- Tag akar halaman, bahasa Indonesia -->

<head> <!-- Awal bagian head (pengaturan halaman) -->
    <meta charset="UTF-8"> <!-- Set encoding karakter UTF-8 -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Agar tampilan menyesuaikan lebar layar HP -->
    <meta name="description" content="Universitas NaraDipa - Penerimaan Mahasiswa Baru"> <!-- Deskripsi halaman (dibaca mesin pencari seperti Google) -->
    <title>Universitas NaraDipa | Penerimaan Mahasiswa Baru</title> <!-- Judul yang tampil di tab browser berisi "Universitas NaraDipa | Penerimaan Mahasiswa Baru" -->

    <link rel="stylesheet" href="css/style.css"> <!-- Memuat file CSS utama -->
</head> <!-- Tutup bagian head -->

<body class="landing-page"> <!-- Awal isi halaman yang tampil -->
    <header class="landing-header"> <!-- Header landing -->
        <a class="landing-brand" href="#beranda" aria-label="Universitas NaraDipa"> <!-- Link ke #beranda -->
            <span class="brand-mark"></span> <!-- Teks keterangan -->
            <span>Universitas NaraDipa</span> <!-- Teks keterangan berisi "Universitas NaraDipa" -->
        </a> <!-- Tutup link #beranda -->

        <nav class="landing-nav" aria-label="Navigasi utama"> <!-- Navigasi -->
            <a class="active" href="#beranda">Beranda</a> <!-- Link menu #beranda (menu aktif) berisi "Beranda" -->
            <a href="#tentang">Tentang</a> <!-- Link ke #tentang berisi "Tentang" -->
            <a href="#program-studi">Bidang Studi</a> <!-- Link ke #program-studi berisi "Bidang Studi" -->
            <a href="#fasilitas">Fasilitas</a> <!-- Link ke #fasilitas berisi "Fasilitas" -->
            <a href="#kontak">Kontak</a> <!-- Link ke #kontak berisi "Kontak" -->
        </nav> <!-- Tutup navigasi -->

        <a class="header-login" href="index.php">Login</a> <!-- Link ke index.php berisi "Login" -->
    </header> <!-- Tutup header landing -->

    <main> <!-- Konten utama -->
        <section class="landing-hero" id="beranda"> <!-- Bagian -->
            <div class="hero-copy"> <!-- Wadah -->
                <p class="eyebrow">Penerimaan mahasiswa baru</p> <!-- Paragraf berisi "Penerimaan mahasiswa baru" -->
                <h1>Raih Masa Depan<br><em>Bersama Kami</em></h1> <!-- Judul utama (berisi teks dan elemen bersarang di dalamnya) -->
                <p class="hero-description">Bergabunglah dengan universitas kami dan wujudkan impianmu melalui pendidikan berkualitas, lingkungan yang inspiratif, serta peluang tanpa batas.</p> <!-- Paragraf berisi "Bergabunglah dengan universitas kami dan wujudkan impianmu melalui pendidikan berkualitas, lingkungan yang inspiratif, serta peluang tanpa batas." -->
                <a class="landing-button" href="index.php">Mulai Pendaftaran <span aria-hidden="true">&rarr;</span></a> <!-- Link ke index.php (berisi teks dan elemen bersarang di dalamnya) -->
            </div> <!-- Tutup wadah -->
            <div class="hero-visual" role="img" aria-label="Gedung universitas"></div> <!-- Wadah -->
        </section> <!-- Tutup bagian -->

        <section class="landing-section about-section" id="tentang"> <!-- Bagian -->
            <div class="section-photo about-photo" role="img" aria-label="Gedung universitas dan pepohonan"></div> <!-- Wadah -->
            <div class="about-copy"> <!-- Wadah -->
                <p class="eyebrow">Tentang kami</p> <!-- Paragraf berisi "Tentang kami" -->
                <h2>Universitas yang<br><span>Berkomitmen untuk Masa Depan</span></h2> <!-- Judul bagian (berisi teks dan elemen bersarang di dalamnya) -->
                <p>Kami adalah perguruan tinggi yang berkomitmen memberikan pendidikan berkualitas, mengembangkan potensi mahasiswa, serta menciptakan lingkungan akademik yang inovatif, inklusif, dan berdaya saing global.</p> <!-- Paragraf berisi "Kami adalah perguruan tinggi yang berkomitmen memberikan pendidikan berkualitas, mengembangkan potensi mahasiswa, serta menciptakan lingkungan akademik yang inovatif, inklusif, dan berdaya saing global." -->
                <div class="value-list"> <!-- Wadah -->
                    <div> <!-- Wadah -->
                        <strong aria-hidden="true">01</strong> <!-- Teks tebal berisi "01" -->
                        <h3>Pendidikan Berkualitas</h3> <!-- Sub-judul berisi "Pendidikan Berkualitas" -->
                        <p>Kurikulum yang relevan dengan kebutuhan industri.</p> <!-- Paragraf berisi "Kurikulum yang relevan dengan kebutuhan industri." -->
                    </div> <!-- Tutup wadah -->
                    <div> <!-- Wadah -->
                        <strong aria-hidden="true">02</strong> <!-- Teks tebal berisi "02" -->
                        <h3>Dosen Profesional</h3> <!-- Sub-judul berisi "Dosen Profesional" -->
                        <p>Didukung oleh tenaga pengajar berpengalaman.</p> <!-- Paragraf berisi "Didukung oleh tenaga pengajar berpengalaman." -->
                    </div> <!-- Tutup wadah -->
                    <div> <!-- Wadah -->
                        <strong aria-hidden="true">03</strong> <!-- Teks tebal berisi "03" -->
                        <h3>Peluang Karir</h3> <!-- Sub-judul berisi "Peluang Karir" -->
                        <p>Mempersiapkan lulusan yang siap bersaing di dunia kerja.</p> <!-- Paragraf berisi "Mempersiapkan lulusan yang siap bersaing di dunia kerja." -->
                    </div> <!-- Tutup wadah -->
                </div> <!-- Tutup wadah -->
            </div> <!-- Tutup wadah -->
        </section> <!-- Tutup bagian -->

        <section class="study-section" id="program-studi"> <!-- Bagian -->
            <div class="landing-section section-heading"> <!-- Wadah -->
                <p class="eyebrow">Program studi</p> <!-- Paragraf berisi "Program studi" -->
                <h2>Pilihan Program Studi</h2> <!-- Judul bagian berisi "Pilihan Program Studi" -->
                <p>Temukan program studi yang sesuai dengan minat dan potensimu.</p> <!-- Paragraf berisi "Temukan program studi yang sesuai dengan minat dan potensimu." -->
            </div> <!-- Tutup wadah -->
            <div class="landing-section study-grid"> <!-- Wadah -->
                <article class="study-card"> <!-- article -->
                    <span class="study-number">01</span> <!-- Teks keterangan berisi "01" -->
                    <h3>Fakultas Teknologi</h3> <!-- Sub-judul berisi "Fakultas Teknologi" -->
                    <p>Sistem Informasi, Teknik Informatika, dan lainnya.</p> <!-- Paragraf berisi "Sistem Informasi, Teknik Informatika, dan lainnya." -->
                </article> <!-- Tutup article -->
                <article class="study-card"> <!-- article -->
                    <span class="study-number">02</span> <!-- Teks keterangan berisi "02" -->
                    <h3>Fakultas Ekonomi</h3> <!-- Sub-judul berisi "Fakultas Ekonomi" -->
                    <p>Manajemen, Akuntansi, dan lainnya.</p> <!-- Paragraf berisi "Manajemen, Akuntansi, dan lainnya." -->
                </article> <!-- Tutup article -->
                <article class="study-card"> <!-- article -->
                    <span class="study-number">03</span> <!-- Teks keterangan berisi "03" -->
                    <h3>Fakultas Ilmu Sosial</h3> <!-- Sub-judul berisi "Fakultas Ilmu Sosial" -->
                    <p>Ilmu Komunikasi, Hubungan Internasional, dan lainnya.</p> <!-- Paragraf berisi "Ilmu Komunikasi, Hubungan Internasional, dan lainnya." -->
                </article> <!-- Tutup article -->
                <article class="study-card"> <!-- article -->
                    <span class="study-number">04</span> <!-- Teks keterangan berisi "04" -->
                    <h3>Fakultas Kesehatan</h3> <!-- Sub-judul berisi "Fakultas Kesehatan" -->
                    <p>Keperawatan, Kesehatan Masyarakat, dan lainnya.</p> <!-- Paragraf berisi "Keperawatan, Kesehatan Masyarakat, dan lainnya." -->
                </article> <!-- Tutup article -->
            </div> <!-- Tutup wadah -->
        </section> <!-- Tutup bagian -->

        <section class="landing-section facilities-section" id="fasilitas"> <!-- Bagian -->
            <div class="facilities-copy"> <!-- Wadah -->
                <p class="eyebrow">Fasilitas</p> <!-- Paragraf berisi "Fasilitas" -->
                <h2>Fasilitas Kampus</h2> <!-- Judul bagian berisi "Fasilitas Kampus" -->
                <p>Kami menyediakan berbagai fasilitas modern untuk mendukung proses belajar dan pengembangan diri mahasiswa.</p> <!-- Paragraf berisi "Kami menyediakan berbagai fasilitas modern untuk mendukung proses belajar dan pengembangan diri mahasiswa." -->
            </div> <!-- Tutup wadah -->
            <div class="facility-grid"> <!-- Wadah -->
                <article class="facility-card"> <!-- article -->
                    <div class="facility-photo library-photo" role="img" aria-label="Perpustakaan kampus"></div> <!-- Wadah -->
                    <h3>Perpustakaan</h3> <!-- Sub-judul berisi "Perpustakaan" -->
                </article> <!-- Tutup article -->
                <article class="facility-card"> <!-- article -->
                    <div class="facility-photo lab-photo" role="img" aria-label="Laboratorium komputer"></div> <!-- Wadah -->
                    <h3>Laboratorium</h3> <!-- Sub-judul berisi "Laboratorium" -->
                </article> <!-- Tutup article -->
                <article class="facility-card"> <!-- article -->
                    <div class="facility-photo center-photo" role="img" aria-label="Student center"></div> <!-- Wadah -->
                    <h3>Student Center</h3> <!-- Sub-judul berisi "Student Center" -->
                </article> <!-- Tutup article -->
                <article class="facility-card"> <!-- article -->
                    <div class="facility-photo sport-photo" role="img" aria-label="Sarana olahraga kampus"></div> <!-- Wadah -->
                    <h3>Sarana Olahraga</h3> <!-- Sub-judul berisi "Sarana Olahraga" -->
                </article> <!-- Tutup article -->
            </div> <!-- Tutup wadah -->
        </section> <!-- Tutup bagian -->

        <section class="stats-section" aria-label="Statistik universitas"> <!-- Bagian -->
            <div><strong>10+</strong><span>Program Studi</span></div> <!-- Wadah (berisi teks dan elemen bersarang di dalamnya) -->
            <div><strong>1000+</strong><span>Mahasiswa</span></div> <!-- Wadah (berisi teks dan elemen bersarang di dalamnya) -->
            <div><strong>50+</strong><span>Dosen</span></div> <!-- Wadah (berisi teks dan elemen bersarang di dalamnya) -->
            <div><strong>20+</strong><span>Prestasi Nasional</span></div> <!-- Wadah (berisi teks dan elemen bersarang di dalamnya) -->
        </section> <!-- Tutup bagian -->

        <section class="contact-section" id="kontak"> <!-- Bagian -->
            <p class="eyebrow">Mulai langkahmu</p> <!-- Paragraf berisi "Mulai langkahmu" -->
            <h2>Siap Memulai Perjalananmu?</h2> <!-- Judul bagian berisi "Siap Memulai Perjalananmu?" -->
            <p>Daftarkan diri sekarang dan jadilah bagian dari keluarga besar universitas kami.</p> <!-- Paragraf berisi "Daftarkan diri sekarang dan jadilah bagian dari keluarga besar universitas kami." -->
            <a class="landing-button" href="index.php">Mulai Pendaftaran <span aria-hidden="true">&rarr;</span></a> <!-- Link ke index.php (berisi teks dan elemen bersarang di dalamnya) -->
            <div class="contact-details"> <!-- Wadah -->
                <span>pmb@universitas.ac.id</span> <!-- Teks keterangan berisi "pmb@universitas.ac.id" -->
                <span>+62 812 3456 7890</span> <!-- Teks keterangan berisi "+62 812 3456 7890" -->
                <span>Jl. Pendidikan No. 1, Indonesia</span> <!-- Teks keterangan berisi "Jl. Pendidikan No. 1, Indonesia" -->
            </div> <!-- Tutup wadah -->
        </section> <!-- Tutup bagian -->
    </main> <!-- Tutup konten utama -->

    <footer class="landing-footer"> <!-- footer -->
        <strong>Universitas NaraDipa</strong> <!-- Teks tebal berisi "Universitas NaraDipa" -->
        <span>&copy; 2026 Universitas NaraDipa. All Rights Reserved.</span> <!-- Teks keterangan berisi "&copy; 2026 Universitas NaraDipa. All Rights Reserved." -->
    </footer> <!-- Tutup footer -->

    <script> // Awal blok JavaScript
        const landingNavLinks = document.querySelectorAll('.landing-nav a'); // Ambil semua link menu navigasi

        landingNavLinks.forEach((link) => { // Untuk setiap link menu:
            link.addEventListener('click', () => { // Saat link diklik:
                landingNavLinks.forEach((navLink) => navLink.classList.remove('active')); // Hapus penanda menu aktif dari semua link
                link.classList.add('active'); // Tandai link yang diklik sebagai menu aktif
            }); // Akhir fungsi callback
        }); // Akhir fungsi callback
    </script> <!-- Tutup blok JavaScript -->
</body> <!-- Tutup isi halaman (body) -->

</html> <!-- Tutup tag html -->

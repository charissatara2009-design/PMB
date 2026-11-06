<?php // Awal blok kode PHP

session_start(); // Mulai sesi (tempat menyimpan data login)

require_once "config/database.php"; // Muat file koneksi database

if ($_SERVER["REQUEST_METHOD"] == "POST") { // Jalankan jika form dikirim dengan method POST

    session_unset(); // Hapus semua data di dalam sesi

    $username = trim($_POST['username']); // Ambil username dari form (spasi di awal/akhir dibuang)
    $password = $_POST['password']; // Ambil password dari form (tidak di-trim)
        $role = $_POST['role']; // Ambil role dari form

        if ($username == "" || $password == "" || $role == "") { // Kalau username, password, atau role ada yang kosong

        // Tampilkan alert "Username, password, dan role wajib diisi!" lalu pindah ke index.php
        echo "<script>
                                alert('Username, password, dan role wajib diisi!');
                window.location='index.php';
              </script>";
        exit; // Hentikan proses halaman di sini
    } // Akhir blok if


    // Query SQL: ambil data dari tabel users berdasarkan username dan role yang diisi (untuk login), maksimal 1 data
    $query = "SELECT * FROM users 
              WHERE username = ? 
              AND role = ?
              LIMIT 1";

    $stmt = mysqli_prepare($conn, $query); // Siapkan query (prepared statement) agar aman dari SQL injection

    mysqli_stmt_bind_param( // Isi tanda tanya (?) pada query dengan nilai yang aman
        $stmt, // Statement query yang sudah disiapkan
        "ss", // Tipe data tiap nilai (i = angka bulat, d = desimal, s = teks)
        $username, // Nilai: username
        $role // Nilai: role
    ); // Akhir pemanggilan fungsi

    mysqli_stmt_execute($stmt); // Jalankan query

    $result = mysqli_stmt_get_result($stmt); // Ambil hasil query


    if (mysqli_num_rows($result) == 1) { // Kalau tepat 1 user ditemukan

        $user = mysqli_fetch_assoc($result); // Ambil satu baris hasil query dalam bentuk array


        if (password_verify($password, $user['password'])) { // Kalau password cocok dengan hash di database

            session_regenerate_id(true); // Ganti ID sesi agar lebih aman dari pembajakan sesi

            $_SESSION['user_id'] = $user['id']; // Simpan ID user ke sesi
            $_SESSION['username'] = $user['username']; // Simpan username ke sesi
            $_SESSION['nama'] = $user['nama']; // Simpan nama ke sesi
            $_SESSION['role'] = $user['role']; // Simpan role (admin/user) ke sesi


            if ($user['role'] == 'admin') { // Kalau role-nya admin

                header("Location: admin/dashboard.php"); // Arahkan ke halaman admin/dashboard.php
                exit; // Hentikan proses halaman di sini

            } else { // Kalau kondisi di atas tidak terpenuhi

                header("Location: user/dashboard.php"); // Arahkan ke halaman user/dashboard.php
                exit; // Hentikan proses halaman di sini
            } // Akhir blok else

        } else { // Kalau kondisi di atas tidak terpenuhi

            // Tampilkan alert "Password salah!" lalu pindah ke index.php
            echo "<script>
                    alert('Password salah!');
                    window.location='index.php';
                  </script>";
            exit; // Hentikan proses halaman di sini
        } // Akhir blok else

    } else { // Kalau kondisi di atas tidak terpenuhi

        // Tampilkan alert "Username atau role tidak ditemukan!" lalu pindah ke index.php
        echo "<script>
                alert('Username atau role tidak ditemukan!');
                window.location='index.php';
              </script>";
        exit; // Hentikan proses halaman di sini
    } // Akhir blok else
} // Akhir blok if

 // Akhir blok kode PHP ?>
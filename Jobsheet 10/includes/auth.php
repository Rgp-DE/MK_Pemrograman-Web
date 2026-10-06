<?php

/*
|--------------------------------------------------------------------------
| Pastikan Session Aktif
|--------------------------------------------------------------------------
*/

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


/*
|--------------------------------------------------------------------------
| Cek Status Login
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['user_id'])) {

    /*
     * Simpan pesan untuk ditampilkan
     * setelah pengguna diarahkan ke halaman login.
     */

    $_SESSION['flash'] = [
        'type' => 'error',
        'pesan' => 'Silakan login terlebih dahulu.'
    ];


    /*
     * Kembali ke halaman login.
     *
     * File yang menggunakan auth.php
     * berada di folder anggota/ atau buku/,
     * sehingga ../auth/login.php adalah path yang benar.
     */

    header('Location: ../auth/login.php');
    exit;
}
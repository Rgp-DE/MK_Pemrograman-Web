<?php

session_start();

require_once __DIR__ . '/../includes/koneksi.php';

$nama = trim($_POST['nama'] ?? '');
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

$errors = [];


/*
|--------------------------------------------------------------------------
| Validasi Nama
|--------------------------------------------------------------------------
*/

if ($nama === '') {

    $errors[] = "Nama wajib diisi.";

}


/*
|--------------------------------------------------------------------------
| Validasi Username
|--------------------------------------------------------------------------
*/

if ($username === '') {

    $errors[] = "Username wajib diisi.";

} elseif (!preg_match('/^[A-Za-z0-9_-]+$/', $username)) {

    $errors[] =
        "Username hanya boleh berisi huruf, angka, underscore (_), dan tanda hubung (-).";

}


/*
|--------------------------------------------------------------------------
| Validasi Password
|--------------------------------------------------------------------------
*/

if ($password === '') {

    $errors[] = "Password wajib diisi.";

} elseif (strlen($password) < 6) {

    $errors[] =
        "Password minimal harus 6 karakter.";

}


/*
|--------------------------------------------------------------------------
| Jika Validasi Gagal
|--------------------------------------------------------------------------
*/

if (!empty($errors)) {

    $_SESSION['flash'] = [
        'type' => 'error',
        'pesan' => implode(' ', $errors)
    ];

    header('Location: register.php');
    exit;

}


/*
|--------------------------------------------------------------------------
| Simpan User
|--------------------------------------------------------------------------
*/

try {

    /*
     * Password tidak disimpan dalam bentuk plaintext.
     * password_hash() membuat password menjadi hash.
     */

    $passwordHash = password_hash(
        $password,
        PASSWORD_DEFAULT
    );


    $stmt = $pdo->prepare("
        INSERT INTO users (
            nama,
            username,
            password,
            role
        )
        VALUES (
            :nama,
            :username,
            :password,
            :role
        )
    ");


    $stmt->execute([

        'nama' => $nama,

        'username' => $username,

        'password' => $passwordHash,

        'role' => 'petugas'

    ]);


    /*
     * Jika berhasil, arahkan ke halaman login.
     */

    $_SESSION['flash'] = [

        'type' => 'success',

        'pesan' =>
            'Registrasi berhasil. Silakan login menggunakan akun yang telah dibuat.'

    ];

    header('Location: login.php');
    exit;


} catch (PDOException $e) {

    /*
     * PostgreSQL error code 23505 =
     * unique_violation.
     *
     * Digunakan untuk menangani username
     * yang sudah digunakan.
     */

    if (
        isset($e->errorInfo[0]) &&
        $e->errorInfo[0] === '23505'
    ) {

        $_SESSION['flash'] = [

            'type' => 'error',

            'pesan' =>
                'Username sudah digunakan. Silakan gunakan username lain.'

        ];

    } else {

        $_SESSION['flash'] = [

            'type' => 'error',

            'pesan' =>
                'Terjadi kesalahan saat membuat akun.'

        ];

    }

    header('Location: register.php');
    exit;

}
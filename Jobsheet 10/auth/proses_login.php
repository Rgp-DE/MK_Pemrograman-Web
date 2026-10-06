<?php

session_start();

require_once __DIR__ . '/../includes/koneksi.php';

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

/*
|--------------------------------------------------------------------------
| Validasi Input
|--------------------------------------------------------------------------
*/

if ($username === '' || $password === '') {

    $_SESSION['flash'] = [
        'type' => 'error',
        'pesan' => 'Username dan password wajib diisi.'
    ];

    header('Location: login.php');
    exit;
}


/*
|--------------------------------------------------------------------------
| Cari User Berdasarkan Username
|--------------------------------------------------------------------------
*/

try {

    $stmt = $pdo->prepare("
        SELECT
            id,
            nama,
            username,
            password,
            role
        FROM users
        WHERE username = :username
    ");

    $stmt->execute([
        'username' => $username
    ]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);


    /*
    |--------------------------------------------------------------------------
    | Cek User dan Password
    |--------------------------------------------------------------------------
    */

    if (
        !$user ||
        !password_verify($password, $user['password'])
    ) {

        $_SESSION['flash'] = [
            'type' => 'error',
            'pesan' => 'Username atau password salah.'
        ];

        header('Location: login.php');
        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | Login Berhasil
    |--------------------------------------------------------------------------
    */

    session_regenerate_id(true);

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['nama'] = $user['nama'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];


    $_SESSION['flash'] = [
        'type' => 'success',
        'pesan' => 'Login berhasil. Selamat datang, ' . $user['nama'] . '!'
    ];


    header('Location: ../index.php');
    exit;


} catch (PDOException $e) {

    $_SESSION['flash'] = [
        'type' => 'error',
        'pesan' => 'Terjadi kesalahan saat proses login.'
    ];

    header('Location: login.php');
    exit;
}
<?php

session_start();

require_once __DIR__ . '/../includes/koneksi.php';


$username = trim(
    $_POST['username'] ?? ''
);

$password = $_POST['password'] ?? '';

$rememberMe = isset(
    $_POST['remember_me']
);


if (
    $username === '' ||
    $password === ''
) {

    $_SESSION['flash'] = [
        'type' => 'error',
        'pesan' =>
            'Username dan password wajib diisi.'
    ];

    header('Location: login.php');

    exit;
}


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


    $user = $stmt->fetch(
        PDO::FETCH_ASSOC
    );


    if (
        !$user ||
        !password_verify(
            $password,
            $user['password']
        )
    ) {

        $_SESSION['flash'] = [
            'type' => 'error',
            'pesan' =>
                'Username atau password salah.'
        ];

        header('Location: login.php');

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | Regenerasi session setelah login
    |--------------------------------------------------------------------------
    */

    session_regenerate_id(true);


    $_SESSION['user_id'] =
        $user['id'];

    $_SESSION['nama'] =
        $user['nama'];

    $_SESSION['username'] =
        $user['username'];

    $_SESSION['role'] =
        $user['role'];


    /*
    |--------------------------------------------------------------------------
    | Remember Me
    |--------------------------------------------------------------------------
    */

    if ($rememberMe) {

        $token = bin2hex(
            random_bytes(32)
        );


        $tokenHash = hash(
            'sha256',
            $token
        );


        $expires = time() + (
            60 * 60 * 24 * 30
        );


        $stmtToken = $pdo->prepare("
            UPDATE users
            SET
                remember_token_hash = :token_hash,
                remember_token_expires =
                    TO_TIMESTAMP(:expires)
            WHERE id = :id
        ");


        $stmtToken->execute([
            'token_hash' =>
                $tokenHash,

            'expires' =>
                $expires,

            'id' =>
                $user['id']
        ]);


        setcookie(
            'remember_token',
            $token,
            [
                'expires' =>
                    $expires,

                'path' =>
                    '/',

                'secure' =>
                    isset($_SERVER['HTTPS']) &&
                    $_SERVER['HTTPS'] !== 'off',

                'httponly' =>
                    true,

                'samesite' =>
                    'Lax'
            ]
        );

    }


    $_SESSION['flash'] = [
        'type' => 'success',
        'pesan' =>
            'Login berhasil. Selamat datang, ' .
            $user['nama'] .
            '!'
    ];


    header(
        'Location: ../index.php'
    );

    exit;


} catch (PDOException $e) {

    $_SESSION['flash'] = [
        'type' => 'error',
        'pesan' =>
            'Terjadi kesalahan saat proses login.'
    ];

    header('Location: login.php');

    exit;
}
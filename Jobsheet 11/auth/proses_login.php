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


/*
|--------------------------------------------------------------------------
| Konfigurasi percobaan login
|--------------------------------------------------------------------------
*/

$maxAttempts = 3;

$lockDuration = 5 * 60;


/*
|--------------------------------------------------------------------------
| Validasi input kosong
|--------------------------------------------------------------------------
*/

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


/*
|--------------------------------------------------------------------------
| Siapkan data percobaan berdasarkan username
|--------------------------------------------------------------------------
*/

if (
    !isset(
        $_SESSION['login_attempts']
    )
) {

    $_SESSION['login_attempts'] = [];
}


if (
    !isset(
        $_SESSION['login_attempts'][$username]
    )
) {

    $_SESSION['login_attempts'][$username] = [
        'count' => 0,
        'locked_until' => 0
    ];
}


$attemptData =
    &$_SESSION['login_attempts'][$username];


/*
|--------------------------------------------------------------------------
| Cek apakah username sedang diblokir
|--------------------------------------------------------------------------
*/

if (
    $attemptData['locked_until'] > time()
) {

    $remaining =
        $attemptData['locked_until'] - time();


    $remainingMinutes =
        ceil(
            $remaining / 60
        );


    $_SESSION['flash'] = [
        'type' => 'error',
        'pesan' =>
            'Terlalu banyak percobaan login gagal. ' .
            'Silakan coba lagi dalam ' .
            $remainingMinutes .
            ' menit.'
    ];


    header('Location: login.php');

    exit;
}


/*
|--------------------------------------------------------------------------
| Jika waktu blokir sudah selesai,
| reset status blokir
|--------------------------------------------------------------------------
*/

if (
    $attemptData['locked_until'] <= time() &&
    $attemptData['count'] >= $maxAttempts
) {

    $attemptData['count'] = 0;

    $attemptData['locked_until'] = 0;
}


try {

    /*
    |--------------------------------------------------------------------------
    | Ambil data user
    |--------------------------------------------------------------------------
    */

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
        'username' =>
            $username
    ]);


    $user = $stmt->fetch(
        PDO::FETCH_ASSOC
    );


    /*
    |--------------------------------------------------------------------------
    | Verifikasi username dan password
    |--------------------------------------------------------------------------
    */

    if (
        !$user ||
        !password_verify(
            $password,
            $user['password']
        )
    ) {

        $attemptData['count']++;


        /*
        |--------------------------------------------------------------------------
        | Blokir setelah mencapai batas
        |--------------------------------------------------------------------------
        */

        if (
            $attemptData['count'] >=
            $maxAttempts
        ) {

            $attemptData['locked_until'] =
                time() +
                $lockDuration;


            $_SESSION['flash'] = [
                'type' => 'error',
                'pesan' =>
                    'Terlalu banyak percobaan login gagal. ' .
                    'Username diblokir sementara selama 5 menit.'
            ];

        } else {

            $remainingAttempts =
                $maxAttempts -
                $attemptData['count'];


            $_SESSION['flash'] = [
                'type' => 'error',
                'pesan' =>
                    'Username atau password salah. ' .
                    'Sisa percobaan: ' .
                    $remainingAttempts .
                    '.'
            ];
        }


        header(
            'Location: login.php'
        );

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | Login berhasil
    |--------------------------------------------------------------------------
    */

    $attemptData['count'] = 0;

    $attemptData['locked_until'] = 0;


    /*
    |--------------------------------------------------------------------------
    | Regenerasi session
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


        $expires =
            time() +
            (
                60 *
                60 *
                24 *
                30
            );


        $stmtToken = $pdo->prepare("
            UPDATE users
            SET
                remember_token_hash =
                    :token_hash,
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


    /*
    |--------------------------------------------------------------------------
    | Pesan login berhasil
    |--------------------------------------------------------------------------
    */

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

    header(
        'Location: login.php'
    );

    exit;
}
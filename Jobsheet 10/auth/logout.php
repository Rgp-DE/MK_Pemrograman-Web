<?php

session_start();

require_once __DIR__ . '/../includes/koneksi.php';


$userId = $_SESSION['user_id'] ?? null;


/*
|--------------------------------------------------------------------------
| Hapus token Remember Me dari database
|--------------------------------------------------------------------------
*/

if ($userId) {

    $stmt = $pdo->prepare("
        UPDATE users
        SET
            remember_token_hash = NULL,
            remember_token_expires = NULL
        WHERE id = :id
    ");


    $stmt->execute([
        'id' => $userId
    ]);
}


/*
|--------------------------------------------------------------------------
| Hapus cookie Remember Me
|--------------------------------------------------------------------------
*/

setcookie(
    'remember_token',
    '',
    [
        'expires' => time() - 3600,
        'path' => '/',
        'secure' =>
            isset($_SERVER['HTTPS']) &&
            $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Lax'
    ]
);


/*
|--------------------------------------------------------------------------
| Hapus session
|--------------------------------------------------------------------------
*/

$_SESSION = [];


if (
    ini_get("session.use_cookies")
) {

    $params =
        session_get_cookie_params();


    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}


session_destroy();


session_start();


$_SESSION['flash'] = [
    'type' => 'success',
    'pesan' =>
        'Anda berhasil logout.'
];


header(
    'Location: ../index.php'
);

exit;
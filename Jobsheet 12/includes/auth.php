<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


/*
|--------------------------------------------------------------------------
| Jika belum login, coba Remember Me
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['user_id'])) {

    $rememberToken =
        $_COOKIE['remember_token'] ?? '';


    if ($rememberToken !== '') {

        require_once __DIR__ . '/koneksi.php';


        $tokenHash = hash(
            'sha256',
            $rememberToken
        );


        try {

            $stmt = $pdo->prepare("
                SELECT
                    id,
                    nama,
                    username,
                    role
                FROM users
                WHERE
                    remember_token_hash =
                        :token_hash
                    AND remember_token_expires >
                        NOW()
            ");


            $stmt->execute([
                'token_hash' =>
                    $tokenHash
            ]);


            $user = $stmt->fetch(
                PDO::FETCH_ASSOC
            );


            if ($user) {

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
                | Buat token baru
                |--------------------------------------------------------------------------
                */

                $newToken = bin2hex(
                    random_bytes(32)
                );


                $newTokenHash = hash(
                    'sha256',
                    $newToken
                );


                $newExpires = time() + (
                    60 * 60 * 24 * 30
                );


                $updateToken = $pdo->prepare("
                    UPDATE users
                    SET
                        remember_token_hash =
                            :token_hash,
                        remember_token_expires =
                            TO_TIMESTAMP(:expires)
                    WHERE id = :id
                ");


                $updateToken->execute([
                    'token_hash' =>
                        $newTokenHash,

                    'expires' =>
                        $newExpires,

                    'id' =>
                        $user['id']
                ]);


                setcookie(
                    'remember_token',
                    $newToken,
                    [
                        'expires' =>
                            $newExpires,

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

        } catch (PDOException $e) {

            // Jika token gagal diverifikasi,
            // lanjutkan ke proses login normal.

        }

    }

}


/*
|--------------------------------------------------------------------------
| Tetap wajib login jika session belum terbentuk
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['user_id'])) {

    $_SESSION['flash'] = [
        'type' => 'error',
        'pesan' =>
            'Silakan login terlebih dahulu.'
    ];

    header(
        'Location: ../auth/login.php'
    );

    exit;
}
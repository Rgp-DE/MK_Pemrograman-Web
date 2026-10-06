<?php

require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/koneksi.php';


/*
|--------------------------------------------------------------------------
| Delete harus menggunakan POST
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    $_SESSION['flash'] = [

        'type' =>
            'error',

        'pesan' =>
            'Metode request tidak valid.'

    ];

    header('Location: list.php');
    exit;
}


/*
|--------------------------------------------------------------------------
| Ambil ID
|--------------------------------------------------------------------------
*/

$id = filter_input(
    INPUT_POST,
    'id',
    FILTER_VALIDATE_INT
);


if (!$id || $id < 1) {

    $_SESSION['flash'] = [

        'type' =>
            'error',

        'pesan' =>
            'ID anggota tidak valid.'

    ];

    header('Location: list.php');
    exit;
}


/*
|--------------------------------------------------------------------------
| Hapus Data
|--------------------------------------------------------------------------
*/

try {

    $stmt = $pdo->prepare("
        DELETE FROM anggota
        WHERE id = :id
    ");


    $stmt->execute([
        'id' => $id
    ]);


    if ($stmt->rowCount() > 0) {

        $_SESSION['flash'] = [

            'type' =>
                'success',

            'pesan' =>
                'Data anggota berhasil dihapus.'

        ];

    } else {

        $_SESSION['flash'] = [

            'type' =>
                'error',

            'pesan' =>
                'Data anggota tidak ditemukan.'

        ];

    }


} catch (PDOException $e) {

    $_SESSION['flash'] = [

        'type' =>
            'error',

        'pesan' =>
            'Terjadi kesalahan saat menghapus data anggota.'

    ];

}


header(
    'Location: list.php'
);

exit;
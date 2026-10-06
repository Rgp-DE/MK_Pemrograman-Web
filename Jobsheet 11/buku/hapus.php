<?php

require __DIR__ . '/../includes/auth.php';


/*
|--------------------------------------------------------------------------
| Role Check
|--------------------------------------------------------------------------
*/

if ($_SESSION['role'] !== 'admin') {

    $_SESSION['flash'] = [
        'type' => 'error',
        'pesan' =>
            'Akses ditolak. Hanya admin yang dapat menghapus data buku.'
    ];

    header('Location: list.php');

    exit;
}


require __DIR__ . '/../includes/koneksi.php';


/*
|--------------------------------------------------------------------------
| Hanya izinkan POST
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    $_SESSION['flash'] = [
        'type' => 'error',
        'pesan' =>
            'Metode request tidak diperbolehkan.'
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


if (!$id) {

    $_SESSION['flash'] = [
        'type' => 'error',
        'pesan' =>
            'ID buku tidak valid.'
    ];

    header('Location: list.php');

    exit;
}


/*
|--------------------------------------------------------------------------
| Hapus data
|--------------------------------------------------------------------------
*/

try {

    $stmt = $pdo->prepare("
        DELETE FROM buku
        WHERE id = :id
    ");


    $stmt->execute([
        'id' => $id
    ]);


    if (
        $stmt->rowCount() === 0
    ) {

        $_SESSION['flash'] = [
            'type' => 'error',
            'pesan' =>
                'Data buku tidak ditemukan.'
        ];

    } else {

        $_SESSION['flash'] = [
            'type' => 'success',
            'pesan' =>
                'Data buku berhasil dihapus.'
        ];

    }


} catch (PDOException $e) {

    $_SESSION['flash'] = [
        'type' => 'error',
        'pesan' =>
            'Terjadi kesalahan saat menghapus data buku.'
    ];

}


header(
    'Location: list.php'
);

exit;
<?php

require __DIR__ . '/../includes/auth.php';


if ($_SESSION['role'] !== 'admin') {

    $_SESSION['flash'] = [
        'type' => 'error',
        'pesan' =>
            'Akses ditolak. Hanya admin yang dapat menghapus data anggota.'
    ];

    header('Location: list.php');

    exit;
}


require __DIR__ . '/../includes/koneksi.php';


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    $_SESSION['flash'] = [
        'type' => 'error',
        'pesan' =>
            'Metode request tidak diperbolehkan.'
    ];

    header('Location: list.php');

    exit;
}


$id = filter_input(
    INPUT_POST,
    'id',
    FILTER_VALIDATE_INT
);


if (!$id) {

    $_SESSION['flash'] = [
        'type' => 'error',
        'pesan' =>
            'ID anggota tidak valid.'
    ];

    header('Location: list.php');

    exit;
}


try {

    $stmt = $pdo->prepare("
        DELETE FROM anggota
        WHERE id = :id
    ");


    $stmt->execute([
        'id' => $id
    ]);


    if ($stmt->rowCount() === 0) {

        $_SESSION['flash'] = [
            'type' => 'error',
            'pesan' =>
                'Data anggota tidak ditemukan.'
        ];

    } else {

        $_SESSION['flash'] = [
            'type' => 'success',
            'pesan' =>
                'Data anggota berhasil dihapus.'
        ];

    }


} catch (PDOException $e) {

    $_SESSION['flash'] = [
        'type' => 'error',
        'pesan' =>
            'Terjadi kesalahan saat menghapus data anggota.'
    ];

}


header(
    'Location: list.php'
);

exit;
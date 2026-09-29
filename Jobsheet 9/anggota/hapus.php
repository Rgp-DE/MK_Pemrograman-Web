<?php

session_start();

require_once __DIR__ . '/../includes/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

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
        'pesan' => 'ID anggota tidak valid.'
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

    if ($stmt->rowCount() > 0) {

        $_SESSION['flash'] = [
            'type' => 'success',
            'pesan' => 'Anggota berhasil dihapus.'
        ];

    } else {

        $_SESSION['flash'] = [
            'type' => 'error',
            'pesan' => 'Data anggota tidak ditemukan.'
        ];
    }

} catch (PDOException $e) {

    $_SESSION['flash'] = [
        'type' => 'error',
        'pesan' => 'Anggota gagal dihapus.'
    ];
}

header('Location: list.php');
exit;
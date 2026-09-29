<?php

session_start();

require_once __DIR__ . '/../includes/koneksi.php';

$id = filter_input(
    INPUT_POST,
    'id',
    FILTER_VALIDATE_INT
);

$nama = trim($_POST['nama'] ?? '');
$noAnggota = trim($_POST['no_anggota'] ?? '');
$alamat = trim($_POST['alamat'] ?? '');
$noHp = trim($_POST['no_hp'] ?? '');
$email = trim($_POST['email'] ?? '');

$errors = [];

if (!$id) {
    $errors[] = "ID anggota tidak valid.";
}

if ($nama === '') {
    $errors[] = "Nama wajib diisi.";
}

if ($noAnggota === '') {

    $errors[] = "No. Anggota wajib diisi.";

} elseif (!preg_match('/^[A-Za-z0-9-]+$/', $noAnggota)) {

    $errors[] =
        "No. Anggota hanya boleh berisi huruf, angka, dan tanda hubung (-).";
}

if ($email === '') {

    $errors[] = "Email wajib diisi.";

} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

    $errors[] = "Format email tidak valid.";
}

if (
    $noHp !== '' &&
    !preg_match('/^[0-9+\-\s]+$/', $noHp)
) {

    $errors[] =
        "No. HP hanya boleh berisi angka, spasi, tanda plus (+), dan tanda hubung (-).";
}

if (!empty($errors)) {

    $_SESSION['flash'] = [
        'type' => 'error',
        'pesan' => implode(' ', $errors)
    ];

    header('Location: edit.php?id=' . (int) $id);
    exit;
}

try {

    $stmt = $pdo->prepare("
        UPDATE anggota
        SET
            nama = :nama,
            no_anggota = :no_anggota,
            alamat = :alamat,
            no_hp = :no_hp,
            email = :email
        WHERE id = :id
    ");

    $stmt->execute([
        'nama' => $nama,
        'no_anggota' => $noAnggota,
        'alamat' => $alamat,
        'no_hp' => $noHp,
        'email' => $email,
        'id' => $id
    ]);

    $_SESSION['flash'] = [
        'type' => 'success',
        'pesan' => 'Anggota berhasil diperbarui.'
    ];

} catch (PDOException $e) {

    if (
        isset($e->errorInfo[0]) &&
        $e->errorInfo[0] === '23505'
    ) {

        $_SESSION['flash'] = [
            'type' => 'error',
            'pesan' =>
                'No. Anggota sudah dipakai, gunakan nomor lain.'
        ];

    } else {

        $_SESSION['flash'] = [
            'type' => 'error',
            'pesan' =>
                'Terjadi kesalahan saat memperbarui data anggota.'
        ];
    }

}

header('Location: list.php');
exit;
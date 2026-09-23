<?php

session_start();

$nama = trim($_POST['nama'] ?? '');
$noAnggota = trim($_POST['no_anggota'] ?? '');
$alamat = trim($_POST['alamat'] ?? '');
$noHp = trim($_POST['no_hp'] ?? '');
$email = trim($_POST['email'] ?? '');

$errors = [];

/*
 * Validasi Nama
 */
if ($nama === '') {
    $errors[] = "Nama wajib diisi.";
}

/*
 * Validasi Nomor Anggota
 */
if ($noAnggota === '') {
    $errors[] = "No. Anggota wajib diisi.";
}

/*
 * Validasi Email
 */
if ($email === '') {

    $errors[] = "Email wajib diisi.";

} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

    $errors[] = "Format email tidak valid.";

}

/*
 * Validasi Nomor HP
 * Jika diisi, hanya boleh mengandung angka,
 * spasi, tanda +, dan tanda hubung.
 */
if (
    $noHp !== '' &&
    !preg_match('/^[0-9+\-\s]+$/', $noHp)
) {
    $errors[] =
        "No. HP hanya boleh berisi angka, spasi, tanda plus (+), dan tanda hubung (-).";
}

/*
 * Jika terdapat error
 */
if (!empty($errors)) {

    $_SESSION['flash'] = [
        'type' => 'error',
        'pesan' => implode(' ', $errors)
    ];

    header('Location: tambah.php');
    exit;
}

/*
 * Membuat array anggota jika belum tersedia
 */
if (!isset($_SESSION['anggota'])) {
    $_SESSION['anggota'] = [];
}

/*
 * Menambahkan data anggota ke Session
 */
$_SESSION['anggota'][] = [
    'no_anggota' => $noAnggota,
    'nama' => $nama,
    'alamat' => $alamat,
    'no_hp' => $noHp,
    'email' => $email
];

/*
 * Flash message berhasil
 */
$_SESSION['flash'] = [
    'type' => 'success',
    'pesan' => 'Anggota berhasil ditambahkan.'
];

/*
 * Kembali ke daftar anggota
 */
header('Location: list.php');
exit;
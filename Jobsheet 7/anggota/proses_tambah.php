<?php

session_start();

/* ===== Ambil data dari form ===== */
$nama = trim($_POST['nama'] ?? '');
$noAnggota = trim($_POST['no_anggota'] ?? '');
$alamat = trim($_POST['alamat'] ?? '');
$noHp = trim($_POST['no_hp'] ?? '');
$email = trim($_POST['email'] ?? '');

/* ===== Menampung pesan error ===== */
$errors = [];

/* ===== Validasi Nama ===== */
if ($nama === '') {
    $errors[] = "Nama wajib diisi.";
}

/* ===== Validasi No. Anggota ===== */
if ($noAnggota === '') {
    $errors[] = "No. Anggota wajib diisi.";
} elseif (!preg_match('/^[A-Za-z0-9-]+$/', $noAnggota)) {
    $errors[] =
        "No. Anggota hanya boleh berisi huruf, angka, dan tanda hubung (-).";
}

/* ===== Validasi Email ===== */
if ($email === '') {
    $errors[] = "Email wajib diisi.";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Format email tidak valid.";
}

/* ===== Validasi No. HP ===== */
if (
    $noHp !== '' &&
    !preg_match('/^[0-9+\-\s]+$/', $noHp)
) {
    $errors[] =
        "No. HP hanya boleh berisi angka, spasi, tanda plus (+), dan tanda hubung (-).";
}

/* ===== Jika terdapat error ===== */
if (!empty($errors)) {

    $_SESSION['flash'] = [
        'type' => 'error',
        'pesan' => implode(' ', $errors)
    ];

    header('Location: tambah.php');
    exit;
}

/* ===== Siapkan session anggota ===== */
if (!isset($_SESSION['anggota'])) {
    $_SESSION['anggota'] = [];
}

/* ===== Simpan data anggota ===== */
$_SESSION['anggota'][] = [
    'no_anggota' => $noAnggota,
    'nama' => $nama,
    'alamat' => $alamat,
    'no_hp' => $noHp,
    'email' => $email
];

/* ===== Flash message berhasil ===== */
$_SESSION['flash'] = [
    'type' => 'success',
    'pesan' => 'Anggota berhasil ditambahkan.'
];

/* ===== Kembali ke daftar anggota ===== */
header('Location: list.php');
exit;
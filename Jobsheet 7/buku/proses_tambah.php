<?php

session_start();

$judul = trim($_POST['judul'] ?? '');
$pengarang = trim($_POST['pengarang'] ?? '');
$tahun = $_POST['tahun'] ?? '';
$isbn = trim($_POST['isbn'] ?? '');
$stok = $_POST['stok'] ?? '';
$kategori = trim($_POST['kategori'] ?? '');

$errors = [];

/*
 * Validasi Judul
 */
if ($judul === '') {
    $errors[] = "Judul wajib diisi.";
}

/*
 * Validasi Pengarang
 */
if ($pengarang === '') {
    $errors[] = "Pengarang wajib diisi.";
}

/*
 * Validasi Tahun
 */
if (
    !is_numeric($tahun) ||
    $tahun < 1900 ||
    $tahun > 2026
) {
    $errors[] = "Tahun harus di antara 1900-2026.";
}

/*
 * Validasi Stok
 */
if (
    !is_numeric($stok) ||
    $stok < 0
) {
    $errors[] = "Stok tidak boleh negatif.";
}

/*
 * Validasi ISBN
 * ISBN boleh kosong.
 * Jika diisi, hanya boleh angka dan tanda hubung.
 */
if (
    $isbn !== '' &&
    !preg_match('/^[0-9-]+$/', $isbn)
) {
    $errors[] =
        "ISBN hanya boleh berisi angka dan tanda hubung (-).";
}

/*
 * Validasi Kategori
 */
$kategoriValid = [
    'fiksi',
    'non-fiksi',
    'referensi'
];

if (!in_array($kategori, $kategoriValid, true)) {
    $errors[] = "Kategori tidak valid.";
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
 * Membuat array buku jika belum tersedia
 */
if (!isset($_SESSION['buku'])) {
    $_SESSION['buku'] = [];
}

/*
 * Menambahkan data buku ke Session
 */
$_SESSION['buku'][] = [
    'judul' => $judul,
    'pengarang' => $pengarang,
    'tahun' => (int) $tahun,
    'isbn' => $isbn,
    'stok' => (int) $stok,
    'kategori' => $kategori
];

/*
 * Flash message berhasil
 */
$_SESSION['flash'] = [
    'type' => 'success',
    'pesan' => 'Buku berhasil ditambahkan.'
];

/*
 * Kembali ke daftar buku
 */
header('Location: list.php');
exit;
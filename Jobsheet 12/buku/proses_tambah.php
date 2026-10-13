<?php

require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/koneksi.php';


/*
|--------------------------------------------------------------------------
| Ambil Data Form
|--------------------------------------------------------------------------
*/

$judul = trim(
    $_POST['judul'] ?? ''
);

$pengarang = trim(
    $_POST['pengarang'] ?? ''
);

$tahun = trim(
    $_POST['tahun'] ?? ''
);

$isbn = trim(
    $_POST['isbn'] ?? ''
);

$stok = trim(
    $_POST['stok'] ?? ''
);

$kategori = trim(
    $_POST['kategori'] ?? ''
);


$errors = [];


/*
|--------------------------------------------------------------------------
| Validasi Judul
|--------------------------------------------------------------------------
*/

if ($judul === '') {

    $errors[] =
        'Judul wajib diisi.';

}


/*
|--------------------------------------------------------------------------
| Validasi Pengarang
|--------------------------------------------------------------------------
*/

if ($pengarang === '') {

    $errors[] =
        'Pengarang wajib diisi.';

}


/*
|--------------------------------------------------------------------------
| Validasi Tahun
|--------------------------------------------------------------------------
*/

if ($tahun === '') {

    $errors[] =
        'Tahun wajib diisi.';

} elseif (
    !is_numeric($tahun)
) {

    $errors[] =
        'Tahun harus berupa angka.';

} elseif (
    (int) $tahun < 1900 ||
    (int) $tahun > 2026
) {

    $errors[] =
        'Tahun harus berada antara 1900 dan 2026.';

}


/*
|--------------------------------------------------------------------------
| Validasi Stok
|--------------------------------------------------------------------------
*/

if ($stok === '') {

    $errors[] =
        'Stok wajib diisi.';

} elseif (
    !is_numeric($stok)
) {

    $errors[] =
        'Stok harus berupa angka.';

} elseif (
    (int) $stok < 0
) {

    $errors[] =
        'Stok tidak boleh kurang dari 0.';

}


/*
|--------------------------------------------------------------------------
| Validasi ISBN
|--------------------------------------------------------------------------
*/

if (
    $isbn !== '' &&
    !preg_match(
        '/^[0-9-]+$/',
        $isbn
    )
) {

    $errors[] =
        'ISBN hanya boleh berisi angka dan tanda hubung (-).';

}


/*
|--------------------------------------------------------------------------
| Validasi Kategori
|--------------------------------------------------------------------------
*/

$kategoriValid = [
    'fiksi',
    'non-fiksi',
    'referensi'
];


if (
    !in_array(
        $kategori,
        $kategoriValid,
        true
    )
) {

    $errors[] =
        'Kategori tidak valid.';

}


/*
|--------------------------------------------------------------------------
| Jika Validasi Gagal
|--------------------------------------------------------------------------
*/

if (!empty($errors)) {

    $_SESSION['flash'] = [

        'type' =>
            'error',

        'pesan' =>
            implode(
                ' ',
                $errors
            )

    ];


    header(
        'Location: tambah.php'
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| Simpan ke Database
|--------------------------------------------------------------------------
*/

try {

    $stmt = $pdo->prepare("
        INSERT INTO buku (
            judul,
            pengarang,
            tahun,
            isbn,
            stok,
            kategori
        )
        VALUES (
            :judul,
            :pengarang,
            :tahun,
            :isbn,
            :stok,
            :kategori
        )
    ");


    $stmt->execute([

        'judul' =>
            $judul,

        'pengarang' =>
            $pengarang,

        'tahun' =>
            (int) $tahun,

        'isbn' =>
            $isbn !== ''
                ? $isbn
                : null,

        'stok' =>
            (int) $stok,

        'kategori' =>
            $kategori

    ]);


    $_SESSION['flash'] = [

        'type' =>
            'success',

        'pesan' =>
            'Buku berhasil ditambahkan.'

    ];


} catch (PDOException $e) {

    $_SESSION['flash'] = [

        'type' =>
            'error',

        'pesan' =>
            'Terjadi kesalahan saat menyimpan data buku.'

    ];

}


header(
    'Location: list.php'
);

exit;
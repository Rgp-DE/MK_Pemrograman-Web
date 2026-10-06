<?php

require __DIR__ . '/../includes/auth.php';

require __DIR__ . '/../includes/koneksi.php';


/*
|--------------------------------------------------------------------------
| Ambil Data Form
|--------------------------------------------------------------------------
*/

$nama = trim(
    $_POST['nama'] ?? ''
);

$noAnggota = trim(
    $_POST['no_anggota'] ?? ''
);

$alamat = trim(
    $_POST['alamat'] ?? ''
);

$noHp = trim(
    $_POST['no_hp'] ?? ''
);

$email = trim(
    $_POST['email'] ?? ''
);


$errors = [];


/*
|--------------------------------------------------------------------------
| Validasi Nama
|--------------------------------------------------------------------------
*/

if ($nama === '') {

    $errors[] =
        "Nama wajib diisi.";

}


/*
|--------------------------------------------------------------------------
| Validasi No. Anggota
|--------------------------------------------------------------------------
*/

if ($noAnggota === '') {

    $errors[] =
        "No. Anggota wajib diisi.";

} elseif (
    !preg_match(
        '/^[A-Za-z0-9-]+$/',
        $noAnggota
    )
) {

    $errors[] =
        "No. Anggota hanya boleh berisi huruf, angka, dan tanda hubung (-).";

}


/*
|--------------------------------------------------------------------------
| Validasi Email
|--------------------------------------------------------------------------
*/

if ($email === '') {

    $errors[] =
        "Email wajib diisi.";

} elseif (
    !filter_var(
        $email,
        FILTER_VALIDATE_EMAIL
    )
) {

    $errors[] =
        "Format email tidak valid.";

}


/*
|--------------------------------------------------------------------------
| Validasi No. HP
|--------------------------------------------------------------------------
*/

if (
    $noHp !== '' &&
    !preg_match(
        '/^[0-9+\-\s]+$/',
        $noHp
    )
) {

    $errors[] =
        "No. HP hanya boleh berisi angka, spasi, tanda plus (+), dan tanda hubung (-).";

}


/*
|--------------------------------------------------------------------------
| Jika Validasi Gagal
|--------------------------------------------------------------------------
*/

if (!empty($errors)) {

    $_SESSION['flash'] = [

        'type' => 'error',

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
        INSERT INTO anggota (
            nama,
            no_anggota,
            alamat,
            no_hp,
            email
        )
        VALUES (
            :nama,
            :no_anggota,
            :alamat,
            :no_hp,
            :email
        )
    ");


    $stmt->execute([

        'nama' =>
            $nama,

        'no_anggota' =>
            $noAnggota,

        'alamat' =>
            $alamat,

        'no_hp' =>
            $noHp,

        'email' =>
            $email

    ]);


    $_SESSION['flash'] = [

        'type' => 'success',

        'pesan' =>
            'Anggota berhasil ditambahkan.'

    ];


} catch (PDOException $e) {


    /*
    |--------------------------------------------------------------------------
    | PostgreSQL UNIQUE violation
    |--------------------------------------------------------------------------
    */

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
                'Terjadi kesalahan saat menyimpan data anggota.'

        ];

    }

}


header(
    'Location: list.php'
);

exit;
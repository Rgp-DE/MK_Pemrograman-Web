<?php

require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/koneksi.php';


/*
|--------------------------------------------------------------------------
| Ambil Data dari Form
|--------------------------------------------------------------------------
*/

$id = filter_input(
    INPUT_POST,
    'id',
    FILTER_VALIDATE_INT
);

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
| Validasi ID
|--------------------------------------------------------------------------
*/

if (!$id || $id < 1) {

    $errors[] =
        'ID anggota tidak valid.';

}


/*
|--------------------------------------------------------------------------
| Validasi Nama
|--------------------------------------------------------------------------
*/

if ($nama === '') {

    $errors[] =
        'Nama wajib diisi.';

}


/*
|--------------------------------------------------------------------------
| Validasi No. Anggota
|--------------------------------------------------------------------------
*/

if ($noAnggota === '') {

    $errors[] =
        'No. Anggota wajib diisi.';

} elseif (
    !preg_match(
        '/^[A-Za-z0-9-]+$/',
        $noAnggota
    )
) {

    $errors[] =
        'No. Anggota hanya boleh berisi huruf, angka, dan tanda hubung (-).';

}


/*
|--------------------------------------------------------------------------
| Validasi Email
|--------------------------------------------------------------------------
*/

if ($email === '') {

    $errors[] =
        'Email wajib diisi.';

} elseif (
    !filter_var(
        $email,
        FILTER_VALIDATE_EMAIL
    )
) {

    $errors[] =
        'Format email tidak valid.';

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
        'No. HP hanya boleh berisi angka, spasi, tanda plus (+), dan tanda hubung (-).';

}


/*
|--------------------------------------------------------------------------
| Jika Validasi Gagal
|--------------------------------------------------------------------------
*/

if (!empty($errors)) {

    $_SESSION['flash'] = [
        'type' => 'error',
        'pesan' => implode(' ', $errors)
    ];

    header(
        'Location: edit.php?id=' . $id
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| Update Database
|--------------------------------------------------------------------------
*/

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

        'nama' =>
            $nama,

        'no_anggota' =>
            $noAnggota,

        'alamat' =>
            $alamat,

        'no_hp' =>
            $noHp,

        'email' =>
            $email,

        'id' =>
            $id

    ]);


    $_SESSION['flash'] = [

        'type' =>
            'success',

        'pesan' =>
            'Data anggota berhasil diperbarui.'

    ];


} catch (PDOException $e) {


    /*
    |--------------------------------------------------------------------------
    | UNIQUE violation
    |--------------------------------------------------------------------------
    */

    if (
        isset($e->errorInfo[0]) &&
        $e->errorInfo[0] === '23505'
    ) {

        $_SESSION['flash'] = [

            'type' =>
                'error',

            'pesan' =>
                'No. Anggota sudah dipakai oleh anggota lain.'

        ];


        header(
            'Location: edit.php?id=' . $id
        );

        exit;

    }


    $_SESSION['flash'] = [

        'type' =>
            'error',

        'pesan' =>
            'Terjadi kesalahan saat memperbarui data anggota.'

    ];

}


header(
    'Location: list.php'
);

exit;
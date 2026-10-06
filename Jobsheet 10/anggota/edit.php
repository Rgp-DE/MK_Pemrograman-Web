<?php

require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/koneksi.php';


/*
|--------------------------------------------------------------------------
| Ambil ID
|--------------------------------------------------------------------------
*/

$id = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT
);

if (!$id || $id < 1) {

    $_SESSION['flash'] = [
        'type' => 'error',
        'pesan' => 'ID anggota tidak valid.'
    ];

    header('Location: list.php');
    exit;
}


/*
|--------------------------------------------------------------------------
| Ambil Data Anggota
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT
        id,
        nama,
        no_anggota,
        alamat,
        no_hp,
        email
    FROM anggota
    WHERE id = :id
");

$stmt->execute([
    'id' => $id
]);

$anggota = $stmt->fetch(PDO::FETCH_ASSOC);


/*
|--------------------------------------------------------------------------
| Jika Data Tidak Ditemukan
|--------------------------------------------------------------------------
*/

if (!$anggota) {

    $_SESSION['flash'] = [
        'type' => 'error',
        'pesan' => 'Data anggota tidak ditemukan.'
    ];

    header('Location: list.php');
    exit;
}


$page_title = "Edit Anggota";

include __DIR__ . '/../includes/header.php';

?>

<section>

    <h2>Edit Anggota</h2>

    <form
        id="form-edit"
        method="post"
        action="proses_edit.php">


        <input
            type="hidden"
            name="id"
            value="<?php echo (int) $anggota['id']; ?>">


        <p>

            <label for="nama">
                Nama
            </label>

            <br>

            <input
                type="text"
                id="nama"
                name="nama"
                value="<?php echo htmlspecialchars($anggota['nama']); ?>"
                required>

        </p>


        <p>

            <label for="no_anggota">
                No. Anggota
            </label>

            <br>

            <input
                type="text"
                id="no_anggota"
                name="no_anggota"
                value="<?php echo htmlspecialchars($anggota['no_anggota']); ?>"
                required>

        </p>


        <p>

            <label for="alamat">
                Alamat
            </label>

            <br>

            <input
                type="text"
                id="alamat"
                name="alamat"
                value="<?php echo htmlspecialchars($anggota['alamat'] ?? ''); ?>">

        </p>


        <p>

            <label for="no_hp">
                No. HP
            </label>

            <br>

            <input
                type="text"
                id="no_hp"
                name="no_hp"
                value="<?php echo htmlspecialchars($anggota['no_hp'] ?? ''); ?>">

        </p>


        <p>

            <label for="email">
                Email
            </label>

            <br>

            <input
                type="email"
                id="email"
                name="email"
                value="<?php echo htmlspecialchars($anggota['email']); ?>"
                required>

        </p>


        <p>

            <button
                type="submit"
                class="btn-edit">

                Simpan Perubahan

            </button>


            <a
                href="list.php"
                class="btn-detail">

                Batal

            </a>

        </p>


    </form>

</section>


<?php include __DIR__ . '/../includes/footer.php'; ?>
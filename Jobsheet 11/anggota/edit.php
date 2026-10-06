<?php

require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/koneksi.php';

/*
|--------------------------------------------------------------------------
| RBAC
|--------------------------------------------------------------------------
*/

if (
    ($_SESSION['role'] ?? '') !== 'admin'
) {

    $_SESSION['flash'] = [
        'type' => 'error',
        'pesan' =>
            'Akses ditolak. Hanya admin yang dapat mengedit data anggota.'
    ];

    header('Location: list.php');

    exit;
}

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
        'pesan' =>
            'ID anggota tidak valid.'
    ];

    header('Location: list.php');

    exit;
}

/*
|--------------------------------------------------------------------------
| Ambil data anggota
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

$anggota =
    $stmt->fetch(
        PDO::FETCH_ASSOC
    );

if (!$anggota) {

    $_SESSION['flash'] = [
        'type' => 'error',
        'pesan' =>
            'Data anggota tidak ditemukan.'
    ];

    header('Location: list.php');

    exit;
}

$page_title = "Edit Anggota";

include __DIR__ . '/../includes/header.php';

?>

<section class="form-section">

    <h2>
        Edit Anggota
    </h2>

    <p>
        Silakan ubah data anggota yang dipilih.
    </p>

    <form
        action="proses_edit.php"
        method="post"
        id="form-edit">

        <input
            type="hidden"
            name="id"
            value="<?php echo e($anggota['id']); ?>">

        <p>

            <label for="nama">
                Nama
            </label>

            <input
                type="text"
                id="nama"
                name="nama"
                value="<?php echo e($anggota['nama']); ?>"
                required>

        </p>

        <p>

            <label for="no_anggota">
                No. Anggota
            </label>

            <input
                type="text"
                id="no_anggota"
                name="no_anggota"
                value="<?php echo e($anggota['no_anggota']); ?>"
                required>

        </p>

        <p>

            <label for="alamat">
                Alamat
            </label>

            <textarea
                id="alamat"
                name="alamat"
                rows="4"><?php echo e($anggota['alamat'] ?? ''); ?></textarea>

        </p>

        <p>

            <label for="no_hp">
                No. HP
            </label>

            <input
                type="text"
                id="no_hp"
                name="no_hp"
                value="<?php echo e($anggota['no_hp'] ?? ''); ?>">

        </p>

        <p>

            <label for="email">
                Email
            </label>

            <input
                type="email"
                id="email"
                name="email"
                value="<?php echo e($anggota['email']); ?>"
                required>

        </p>

        <div class="form-actions">

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

        </div>

    </form>

</section>

<?php

include __DIR__ . '/../includes/footer.php';

?>
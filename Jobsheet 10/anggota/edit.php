<?php

require __DIR__ . '/../includes/auth.php';


if ($_SESSION['role'] !== 'admin') {

    $_SESSION['flash'] = [
        'type' => 'error',
        'pesan' =>
            'Akses ditolak. Hanya admin yang dapat mengedit data anggota.'
    ];

    header('Location: list.php');

    exit;
}


require __DIR__ . '/../includes/koneksi.php';


$page_title = "Edit Anggota";


$id = filter_input(
    INPUT_GET,
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


$anggota = $stmt->fetch(
    PDO::FETCH_ASSOC
);


if (!$anggota) {

    $_SESSION['flash'] = [
        'type' => 'error',
        'pesan' => 'Data anggota tidak ditemukan.'
    ];

    header('Location: list.php');

    exit;
}


$flash = $_SESSION['flash'] ?? null;

unset(
    $_SESSION['flash']
);


include __DIR__ . '/../includes/header.php';

?>


<section class="form-section">

    <h2>Edit Anggota</h2>


    <?php if ($flash): ?>

        <div
            class="alert <?php echo htmlspecialchars(
                $flash['type']
            ); ?>">

            <?php echo htmlspecialchars(
                $flash['pesan']
            ); ?>

        </div>

    <?php endif; ?>


    <form
        action="proses_edit.php"
        method="post"
        id="form-edit">


        <input
            type="hidden"
            name="id"
            value="<?php echo (int) $anggota['id']; ?>">


        <div class="form-group">

            <label for="nama">
                Nama
            </label>

            <input
                type="text"
                id="nama"
                name="nama"
                value="<?php echo htmlspecialchars(
                    $anggota['nama']
                ); ?>"
                required>

        </div>


        <div class="form-group">

            <label for="no_anggota">
                No. Anggota
            </label>

            <input
                type="text"
                id="no_anggota"
                name="no_anggota"
                value="<?php echo htmlspecialchars(
                    $anggota['no_anggota']
                ); ?>"
                required>

        </div>


        <div class="form-group">

            <label for="alamat">
                Alamat
            </label>

            <textarea
                id="alamat"
                name="alamat"
                rows="4"><?php echo htmlspecialchars(
                    $anggota['alamat'] ?? ''
                ); ?></textarea>

        </div>


        <div class="form-group">

            <label for="no_hp">
                No. HP
            </label>

            <input
                type="text"
                id="no_hp"
                name="no_hp"
                value="<?php echo htmlspecialchars(
                    $anggota['no_hp'] ?? ''
                ); ?>">

        </div>


        <div class="form-group">

            <label for="email">
                Email
            </label>

            <input
                type="email"
                id="email"
                name="email"
                value="<?php echo htmlspecialchars(
                    $anggota['email']
                ); ?>"
                required>

        </div>


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


<?php include __DIR__ . '/../includes/footer.php'; ?>
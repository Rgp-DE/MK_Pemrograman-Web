<?php

$page_title = "Edit Anggota";

require_once __DIR__ . '/../includes/koneksi.php';

include __DIR__ . '/../includes/header.php';

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
        no_anggota,
        nama,
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

if (!$anggota) {

    $_SESSION['flash'] = [
        'type' => 'error',
        'pesan' => 'Data anggota tidak ditemukan.'
    ];

    header('Location: list.php');
    exit;
}

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>

<section>

    <h2>Edit Anggota</h2>

    <?php if ($flash): ?>

        <p class="flash flash-<?php echo htmlspecialchars($flash['type']); ?>">
            <?php echo htmlspecialchars($flash['pesan']); ?>
        </p>

    <?php endif; ?>

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

            <input
                type="email"
                id="email"
                name="email"
                value="<?php echo htmlspecialchars($anggota['email']); ?>"
                required>

        </p>

        <p>

            <button type="submit">
                Simpan Perubahan
            </button>

            <a href="list.php">
                Batal
            </a>

        </p>

    </form>

</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
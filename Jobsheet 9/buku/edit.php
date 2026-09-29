<?php

$page_title = "Edit Buku";

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
        'pesan' => 'ID buku tidak valid.'
    ];

    header('Location: list.php');
    exit;
}

$stmt = $pdo->prepare("
    SELECT
        id,
        judul,
        pengarang,
        tahun,
        isbn,
        stok,
        kategori
    FROM buku
    WHERE id = :id
");

$stmt->execute([
    'id' => $id
]);

$buku = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$buku) {
    $_SESSION['flash'] = [
        'type' => 'error',
        'pesan' => 'Data buku tidak ditemukan.'
    ];

    header('Location: list.php');
    exit;
}

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>

<section>

    <h2>Edit Buku</h2>

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
            value="<?php echo (int) $buku['id']; ?>">

        <p>

            <label for="judul">
                Judul
            </label>

            <input
                type="text"
                id="judul"
                name="judul"
                value="<?php echo htmlspecialchars($buku['judul']); ?>"
                required>

        </p>

        <p>

            <label for="pengarang">
                Pengarang
            </label>

            <input
                type="text"
                id="pengarang"
                name="pengarang"
                value="<?php echo htmlspecialchars($buku['pengarang']); ?>"
                required>

        </p>

        <p>

            <label for="tahun">
                Tahun Terbit
            </label>

            <input
                type="number"
                id="tahun"
                name="tahun"
                min="1900"
                max="2026"
                value="<?php echo htmlspecialchars($buku['tahun']); ?>"
                required>

        </p>

        <p>

            <label for="isbn">
                ISBN
            </label>

            <input
                type="text"
                id="isbn"
                name="isbn"
                value="<?php echo htmlspecialchars($buku['isbn'] ?? ''); ?>"
                placeholder="Contoh: 978-602-1234-56-7">

        </p>

        <p>

            <label for="stok">
                Stok
            </label>

            <input
                type="number"
                id="stok"
                name="stok"
                min="0"
                value="<?php echo htmlspecialchars($buku['stok']); ?>"
                required>

        </p>

        <p>

            <label for="kategori">
                Kategori
            </label>

            <select
                id="kategori"
                name="kategori">

                <option
                    value="fiksi"
                    <?php echo $buku['kategori'] === 'fiksi' ? 'selected' : ''; ?>>
                    Fiksi
                </option>

                <option
                    value="non-fiksi"
                    <?php echo $buku['kategori'] === 'non-fiksi' ? 'selected' : ''; ?>>
                    Non-Fiksi
                </option>

                <option
                    value="referensi"
                    <?php echo $buku['kategori'] === 'referensi' ? 'selected' : ''; ?>>
                    Referensi
                </option>

            </select>

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
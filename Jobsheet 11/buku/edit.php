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
            'Akses ditolak. Hanya admin yang dapat mengedit data buku.'
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
            'ID buku tidak valid.'
    ];

    header('Location: list.php');

    exit;
}

/*
|--------------------------------------------------------------------------
| Ambil data buku
|--------------------------------------------------------------------------
*/

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

$buku = $stmt->fetch(
    PDO::FETCH_ASSOC
);

if (!$buku) {

    $_SESSION['flash'] = [
        'type' => 'error',
        'pesan' =>
            'Data buku tidak ditemukan.'
    ];

    header('Location: list.php');

    exit;
}

$page_title = "Edit Buku";

include __DIR__ . '/../includes/header.php';

?>

<section class="form-section">

    <h2>
        Edit Buku
    </h2>

    <p>
        Silakan ubah data buku yang dipilih.
    </p>

    <form
        action="proses_edit.php"
        method="post"
        id="form-edit">

        <input
            type="hidden"
            name="id"
            value="<?php echo e($buku['id']); ?>">

        <p>

            <label for="judul">
                Judul
            </label>

            <input
                type="text"
                id="judul"
                name="judul"
                value="<?php echo e($buku['judul']); ?>"
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
                value="<?php echo e($buku['pengarang']); ?>"
                required>

        </p>

        <p>

            <label for="tahun">
                Tahun
            </label>

            <input
                type="number"
                id="tahun"
                name="tahun"
                min="1900"
                max="2026"
                value="<?php echo e($buku['tahun']); ?>"
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
                value="<?php echo e($buku['isbn'] ?? ''); ?>"
                placeholder="Contoh: 978-602-1234">

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
                value="<?php echo e($buku['stok']); ?>"
                required>

        </p>

        <p>

            <label for="kategori">
                Kategori
            </label>

            <select
                id="kategori"
                name="kategori"
                required>

                <option value="">
                    -- Pilih Kategori --
                </option>

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
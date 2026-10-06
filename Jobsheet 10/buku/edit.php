<?php

require __DIR__ . '/../includes/auth.php';


/*
|--------------------------------------------------------------------------
| Role Check
|--------------------------------------------------------------------------
*/

if ($_SESSION['role'] !== 'admin') {

    $_SESSION['flash'] = [
        'type' => 'error',
        'pesan' =>
            'Akses ditolak. Hanya admin yang dapat mengedit data buku.'
    ];

    header('Location: list.php');

    exit;
}


require __DIR__ . '/../includes/koneksi.php';


$page_title = "Edit Buku";


$id = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT
);


if (!$id) {

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


$flash = $_SESSION['flash'] ?? null;

unset(
    $_SESSION['flash']
);


include __DIR__ . '/../includes/header.php';

?>


<section class="form-section">

    <h2>Edit Buku</h2>


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
            value="<?php echo (int) $buku['id']; ?>">


        <div class="form-group">

            <label for="judul">
                Judul
            </label>

            <input
                type="text"
                id="judul"
                name="judul"
                value="<?php echo htmlspecialchars(
                    $buku['judul']
                ); ?>"
                required>

        </div>


        <div class="form-group">

            <label for="pengarang">
                Pengarang
            </label>

            <input
                type="text"
                id="pengarang"
                name="pengarang"
                value="<?php echo htmlspecialchars(
                    $buku['pengarang']
                ); ?>"
                required>

        </div>


        <div class="form-group">

            <label for="tahun">
                Tahun
            </label>

            <input
                type="number"
                id="tahun"
                name="tahun"
                value="<?php echo htmlspecialchars(
                    $buku['tahun']
                ); ?>"
                required>

        </div>


        <div class="form-group">

            <label for="isbn">
                ISBN
            </label>

            <input
                type="text"
                id="isbn"
                name="isbn"
                value="<?php echo htmlspecialchars(
                    $buku['isbn'] ?? ''
                ); ?>"
                placeholder="Contoh: 978-602-1234">

        </div>


        <div class="form-group">

            <label for="stok">
                Stok
            </label>

            <input
                type="number"
                id="stok"
                name="stok"
                min="0"
                value="<?php echo htmlspecialchars(
                    $buku['stok']
                ); ?>"
                required>

        </div>


        <div class="form-group">

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
                    <?php echo $buku['kategori'] === 'fiksi'
                        ? 'selected'
                        : ''; ?>>

                    Fiksi

                </option>


                <option
                    value="non-fiksi"
                    <?php echo $buku['kategori'] === 'non-fiksi'
                        ? 'selected'
                        : ''; ?>>

                    Non-Fiksi

                </option>


                <option
                    value="referensi"
                    <?php echo $buku['kategori'] === 'referensi'
                        ? 'selected'
                        : ''; ?>>

                    Referensi

                </option>


                <!--
                Kategori lama dari data migrasi JS6
                -->

                <?php
                $kategoriLama = [
                    'Novel',
                    'Sejarah',
                    'Pengembangan Diri'
                ];
                ?>


                <?php foreach (
                    $kategoriLama as $kategori
                ): ?>

                    <option
                        value="<?php echo htmlspecialchars(
                            $kategori
                        ); ?>"
                        <?php echo $buku['kategori'] === $kategori
                            ? 'selected'
                            : ''; ?>>

                        <?php echo htmlspecialchars(
                            $kategori
                        ); ?>

                    </option>

                <?php endforeach; ?>


            </select>

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
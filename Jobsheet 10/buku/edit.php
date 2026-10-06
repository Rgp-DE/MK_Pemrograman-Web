<?php

require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/koneksi.php';


/*
|--------------------------------------------------------------------------
| Ambil ID Buku
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
        'pesan' => 'ID buku tidak valid.'
    ];

    header('Location: list.php');
    exit;
}


/*
|--------------------------------------------------------------------------
| Ambil Data Buku
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
        kategori,
        tanggal_ditambahkan
    FROM buku
    WHERE id = :id
");

$stmt->execute([
    'id' => $id
]);

$buku = $stmt->fetch(PDO::FETCH_ASSOC);


/*
|--------------------------------------------------------------------------
| Jika Buku Tidak Ditemukan
|--------------------------------------------------------------------------
*/

if (!$buku) {

    $_SESSION['flash'] = [
        'type' => 'error',
        'pesan' => 'Data buku tidak ditemukan.'
    ];

    header('Location: list.php');
    exit;
}


$page_title = "Edit Buku";

include __DIR__ . '/../includes/header.php';

?>

<section>

    <h2>Edit Buku</h2>


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

            <br>

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

            <br>

            <input
                type="text"
                id="pengarang"
                name="pengarang"
                value="<?php echo htmlspecialchars($buku['pengarang']); ?>"
                required>

        </p>


        <p>

            <label for="tahun">
                Tahun
            </label>

            <br>

            <input
                type="number"
                id="tahun"
                name="tahun"
                min="1900"
                max="2026"
                value="<?php echo (int) $buku['tahun']; ?>"
                required>

        </p>


        <p>

            <label for="isbn">
                ISBN
            </label>

            <br>

            <input
                type="text"
                id="isbn"
                name="isbn"
                value="<?php echo htmlspecialchars($buku['isbn'] ?? ''); ?>"
                placeholder="Contoh: 978-602-1234">

        </p>


        <p>

            <label for="stok">
                Stok
            </label>

            <br>

            <input
                type="number"
                id="stok"
                name="stok"
                min="0"
                value="<?php echo (int) $buku['stok']; ?>"
                required>

        </p>


        <p>

            <label for="kategori">
                Kategori
            </label>

            <br>

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

                <?php
                /*
                 * Data lama hasil migrasi JS8 menggunakan
                 * kategori seperti Novel, Sejarah, dan
                 * Pengembangan Diri.
                 *
                 * Opsi ini dibuat agar data lama tetap
                 * bisa ditampilkan saat diedit.
                 */
                ?>

                <?php
                $kategoriLama = [
                    'Novel',
                    'Sejarah',
                    'Pengembangan Diri'
                ];

                if (
                    !in_array(
                        $buku['kategori'],
                        [
                            'fiksi',
                            'non-fiksi',
                            'referensi'
                        ],
                        true
                    ) &&
                    in_array(
                        $buku['kategori'],
                        $kategoriLama,
                        true
                    )
                ):
                ?>

                    <option
                        value="<?php echo htmlspecialchars($buku['kategori']); ?>"
                        selected>

                        <?php
                        echo htmlspecialchars(
                            $buku['kategori']
                        );
                        ?>

                    </option>

                <?php endif; ?>

            </select>

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


<?php

include __DIR__ . '/../includes/footer.php';

?>
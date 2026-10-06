<?php

require __DIR__ . '/../includes/auth.php';

$page_title = "Tambah Buku";

include __DIR__ . '/../includes/header.php';

$flash = $_SESSION['flash'] ?? null;

unset(
    $_SESSION['flash']
);

?>

<section>

    <h2>Tambah Buku</h2>


    <?php if ($flash): ?>

        <p class="flash flash-<?php echo htmlspecialchars($flash['type']); ?>">

            <?php
            echo htmlspecialchars(
                $flash['pesan']
            );
            ?>

        </p>

    <?php endif; ?>


    <form
        id="form-tambah"
        method="post"
        action="proses_tambah.php">


        <p>

            <label for="judul">
                Judul
            </label>

            <br>

            <input
                type="text"
                id="judul"
                name="judul"
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
                value="0"
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

                <option value="fiksi">
                    Fiksi
                </option>

                <option value="non-fiksi">
                    Non-Fiksi
                </option>

                <option value="referensi">
                    Referensi
                </option>

            </select>

        </p>


        <p>

            <button
                type="submit">

                Simpan

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
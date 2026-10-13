<?php

require __DIR__ . '/../includes/auth.php';

$page_title = "Tambah Buku";

include __DIR__ . '/../includes/header.php';

?>

<section class="form-section">

    <h2>
        Tambah Buku
    </h2>

    <p>
        Silakan isi data buku yang ingin ditambahkan.
    </p>

    <form
        action="proses_tambah.php"
        method="post">

        <p>

            <label for="judul">
                Judul
            </label>

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

            <input
                type="number"
                id="stok"
                name="stok"
                min="0"
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

        <div class="form-actions">

            <button
                type="submit"
                class="btn-edit">

                Simpan

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
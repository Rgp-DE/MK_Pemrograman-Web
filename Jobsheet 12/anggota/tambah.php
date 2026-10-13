<?php

require __DIR__ . '/../includes/auth.php';

$page_title = "Tambah Anggota";

include __DIR__ . '/../includes/header.php';

?>

<section class="form-section">

    <h2>
        Tambah Anggota
    </h2>

    <p>
        Silakan isi data anggota yang ingin ditambahkan.
    </p>

    <form
        action="proses_tambah.php"
        method="post">

        <p>

            <label for="nama">
                Nama
            </label>

            <input
                type="text"
                id="nama"
                name="nama"
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
                required
                placeholder="Contoh: AGT-001">

        </p>

        <p>

            <label for="alamat">
                Alamat
            </label>

            <textarea
                id="alamat"
                name="alamat"
                rows="4"></textarea>

        </p>

        <p>

            <label for="no_hp">
                No. HP
            </label>

            <input
                type="text"
                id="no_hp"
                name="no_hp"
                placeholder="Contoh: 08123456789">

        </p>

        <p>

            <label for="email">
                Email
            </label>

            <input
                type="email"
                id="email"
                name="email"
                required
                placeholder="Contoh: nama@email.com">

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
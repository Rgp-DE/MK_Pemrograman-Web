<?php

require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/koneksi.php';

$page_title = "Daftar Anggota";

/*
|--------------------------------------------------------------------------
| Ambil keyword pencarian
|--------------------------------------------------------------------------
*/

$keyword = trim(
    $_GET['keyword'] ?? ''
);

/*
|--------------------------------------------------------------------------
| Cek role
|--------------------------------------------------------------------------
*/

$isAdmin =
    ($_SESSION['role'] ?? '') === 'admin';

/*
|--------------------------------------------------------------------------
| Ambil data anggota
|--------------------------------------------------------------------------
*/

if ($keyword !== '') {

    $stmt = $pdo->prepare("
        SELECT
            id,
            nama,
            no_anggota,
            alamat,
            no_hp,
            email
        FROM anggota
        WHERE
            nama ILIKE :keyword
            OR no_anggota ILIKE :keyword
        ORDER BY id DESC
    ");

    $stmt->execute([
        'keyword' =>
            '%' . $keyword . '%'
    ]);

} else {

    $stmt = $pdo->query("
        SELECT
            id,
            nama,
            no_anggota,
            alamat,
            no_hp,
            email
        FROM anggota
        ORDER BY id DESC
    ");
}

$daftarAnggota =
    $stmt->fetchAll(
        PDO::FETCH_ASSOC
    );

/*
|--------------------------------------------------------------------------
| Flash message
|--------------------------------------------------------------------------
*/

$flash =
    $_SESSION['flash'] ?? null;

unset(
    $_SESSION['flash']
);

include __DIR__ . '/../includes/header.php';

?>

<section>

    <h2>
        Daftar Anggota
    </h2>

    <?php if ($flash): ?>

        <p
            class="flash flash-<?php echo e($flash['type']); ?>">

            <?php echo e($flash['pesan']); ?>

        </p>

    <?php endif; ?>

    <div class="search-box">

        <form
            method="get"
            action="list.php">

            <label for="search-input">

                Cari Nama atau No. Anggota

            </label>

            <input
                type="text"
                id="search-input"
                name="keyword"
                data-server-search="true"
                value="<?php echo e($keyword); ?>"
                placeholder="Ketik nama atau no. anggota...">

            <button type="submit">
                Cari
            </button>

            <?php if ($keyword !== ''): ?>

                <a href="list.php">
                    Reset
                </a>

            <?php endif; ?>

        </form>

    </div>

    <p class="table-counter">

        <?php if ($keyword !== ''): ?>

            Menampilkan
            <?php echo e(count($daftarAnggota)); ?>
            dari
            <?php echo e(count($daftarAnggota)); ?>
            hasil pencarian

        <?php else: ?>

            Menampilkan
            <?php echo e(count($daftarAnggota)); ?>
            anggota

        <?php endif; ?>

    </p>

    <div class="table-responsive">

        <table>

            <thead>

                <tr>

                    <th>
                        No. Anggota
                    </th>

                    <th>
                        Nama
                    </th>

                    <th>
                        Alamat
                    </th>

                    <th>
                        No. HP
                    </th>

                    <th>
                        Email
                    </th>

                    <th>
                        Aksi
                    </th>

                </tr>

            </thead>

            <tbody>

                <?php if (empty($daftarAnggota)): ?>

                    <tr>

                        <td colspan="6">

                            Tidak ada data anggota
                            yang ditemukan.

                        </td>

                    </tr>

                <?php else: ?>

                    <?php foreach (
                        $daftarAnggota as $anggota
                    ): ?>

                        <tr>

                            <td>

                                <?php
                                echo e(
                                    $anggota['no_anggota']
                                );
                                ?>

                            </td>

                            <td>

                                <?php
                                echo e(
                                    $anggota['nama']
                                );
                                ?>

                            </td>

                            <td>

                                <?php
                                echo e(
                                    $anggota['alamat'] ?? '-'
                                );
                                ?>

                            </td>

                            <td>

                                <?php
                                echo e(
                                    $anggota['no_hp'] ?? '-'
                                );
                                ?>

                            </td>

                            <td>

                                <?php
                                echo e(
                                    $anggota['email']
                                );
                                ?>

                            </td>

                            <td>

                                <?php if ($isAdmin): ?>

                                    <a
                                        href="edit.php?id=<?php echo e((int) $anggota['id']); ?>"
                                        class="btn-edit">

                                        Edit

                                    </a>

                                <?php endif; ?>

                                <button
                                    type="button"
                                    class="btn-detail">

                                    Detail

                                </button>

                                <?php if ($isAdmin): ?>

                                    <form
                                        method="post"
                                        action="hapus.php"
                                        class="form-hapus">

                                        <input
                                            type="hidden"
                                            name="id"
                                            value="<?php echo e((int) $anggota['id']); ?>">

                                        <button
                                            type="submit"
                                            class="btn-hapus">

                                            Hapus

                                        </button>

                                    </form>

                                <?php endif; ?>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php endif; ?>

            </tbody>

        </table>

    </div>

</section>

<?php

include __DIR__ . '/../includes/footer.php';

?>
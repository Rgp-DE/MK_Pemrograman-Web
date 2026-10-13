<?php

require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/koneksi.php';

$page_title = "Daftar Buku";

$keyword = trim(
    $_GET['keyword'] ?? ''
);

$perPage = 10;

$page = filter_input(
    INPUT_GET,
    'page',
    FILTER_VALIDATE_INT
);

if (!$page || $page < 1) {
    $page = 1;
}

$isAdmin =
    ($_SESSION['role'] ?? '') === 'admin';

/*
|--------------------------------------------------------------------------
| Hitung total data
|--------------------------------------------------------------------------
*/

if ($keyword !== '') {

    $stmtCount = $pdo->prepare("
        SELECT COUNT(*)
        FROM buku
        WHERE
            judul ILIKE :keyword
            OR pengarang ILIKE :keyword
    ");

    $stmtCount->execute([
        'keyword' =>
            '%' . $keyword . '%'
    ]);

    $totalBuku =
        (int) $stmtCount->fetchColumn();

} else {

    $totalBuku =
        (int) $pdo
            ->query("
                SELECT COUNT(*)
                FROM buku
            ")
            ->fetchColumn();
}

/*
|--------------------------------------------------------------------------
| Pagination
|--------------------------------------------------------------------------
*/

$totalPages = max(
    1,
    (int) ceil(
        $totalBuku / $perPage
    )
);

if ($page > $totalPages) {
    $page = $totalPages;
}

$offset =
    ($page - 1) * $perPage;

/*
|--------------------------------------------------------------------------
| Ambil data buku
|--------------------------------------------------------------------------
*/

if ($keyword !== '') {

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
        WHERE
            judul ILIKE :keyword
            OR pengarang ILIKE :keyword
        ORDER BY id DESC
        LIMIT :limit
        OFFSET :offset
    ");

    $stmt->bindValue(
        ':keyword',
        '%' . $keyword . '%',
        PDO::PARAM_STR
    );

} else {

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
        ORDER BY id DESC
        LIMIT :limit
        OFFSET :offset
    ");
}

$stmt->bindValue(
    ':limit',
    $perPage,
    PDO::PARAM_INT
);

$stmt->bindValue(
    ':offset',
    $offset,
    PDO::PARAM_INT
);

$stmt->execute();

$daftarBuku =
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
        Daftar Buku
    </h2>

    <?php if ($flash): ?>

        <p
            class="flash flash-<?php echo e($flash['type']); ?>">

            <?php echo e($flash['pesan']); ?>

        </p>

    <?php endif; ?>

    <!--
    ============================================================
    FORM PENCARIAN
    ============================================================

    Form menggunakan GET karena hanya digunakan
    untuk membaca/mencari data.

    Tidak menggunakan CSRF token.
    -->

    <div class="search-box">

        <form
            method="get"
            action="list.php">

            <label for="search-input">

                Cari Judul atau Pengarang

            </label>

            <input
                type="text"
                id="search-input"
                name="keyword"
                data-server-search="true"
                value="<?php echo e($keyword); ?>"
                placeholder="Ketik judul atau pengarang...">

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

    <!--
    ============================================================
    COUNTER
    ============================================================
    -->

    <p class="table-counter">

        <?php if ($keyword !== ''): ?>

            Menampilkan
            <?php echo e(count($daftarBuku)); ?>
            dari
            <?php echo e($totalBuku); ?>
            hasil pencarian

        <?php else: ?>

            Menampilkan
            <?php echo e(count($daftarBuku)); ?>
            dari
            <?php echo e($totalBuku); ?>
            buku

        <?php endif; ?>

    </p>

    <!--
    ============================================================
    TABEL BUKU
    ============================================================
    -->

    <div class="table-responsive">

        <table>

            <thead>

                <tr>

                    <th>
                        Judul
                    </th>

                    <th>
                        Pengarang
                    </th>

                    <th>
                        Tahun
                    </th>

                    <th>
                        ISBN
                    </th>

                    <th>
                        Stok
                    </th>

                    <th>
                        Kategori
                    </th>

                    <th>
                        Tanggal Ditambahkan
                    </th>

                    <th>
                        Aksi
                    </th>

                </tr>

            </thead>

            <tbody>

                <?php if (empty($daftarBuku)): ?>

                    <tr>

                        <td colspan="8">

                            Tidak ada data buku
                            yang ditemukan.

                        </td>

                    </tr>

                <?php else: ?>

                    <?php foreach ($daftarBuku as $buku): ?>

                        <tr>

                            <td>

                                <?php
                                echo e(
                                    $buku['judul']
                                );
                                ?>

                            </td>

                            <td>

                                <?php
                                echo e(
                                    $buku['pengarang']
                                );
                                ?>

                            </td>

                            <td>

                                <?php
                                echo e(
                                    $buku['tahun']
                                );
                                ?>

                            </td>

                            <td>

                                <?php
                                echo e(
                                    $buku['isbn'] ?? '-'
                                );
                                ?>

                            </td>

                            <td>

                                <?php
                                echo e(
                                    $buku['stok']
                                );
                                ?>

                            </td>

                            <td>

                                <?php
                                echo e(
                                    $buku['kategori'] ?? '-'
                                );
                                ?>

                            </td>

                            <td>

                                <?php

                                echo e(
                                    date(
                                        'd-m-Y H:i',
                                        strtotime(
                                            $buku[
                                                'tanggal_ditambahkan'
                                            ]
                                        )
                                    )
                                );

                                ?>

                            </td>

                            <td>

                                <!--
                                ====================================================
                                EDIT
                                ====================================================
                                Hanya admin yang dapat Edit.
                                -->

                                <?php if ($isAdmin): ?>

                                    <a
                                        href="edit.php?id=<?php echo e((int) $buku['id']); ?>"
                                        class="btn-edit">

                                        Edit

                                    </a>

                                <?php endif; ?>

                                <!--
                                ====================================================
                                DETAIL
                                ====================================================
                                -->

                                <button
                                    type="button"
                                    class="btn-detail">

                                    Detail

                                </button>

                                <!--
                                ====================================================
                                HAPUS
                                ====================================================
                                Hanya admin yang dapat Delete.
                                ====================================================
                                -->

                                <?php if ($isAdmin): ?>

                                    <form
                                        method="post"
                                        action="hapus.php"
                                        class="form-hapus">

                                        <input
                                            type="hidden"
                                            name="id"
                                            value="<?php echo e((int) $buku['id']); ?>">

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

    <!--
    ============================================================
    PAGINATION
    ============================================================
    -->

    <?php if ($totalPages > 1): ?>

        <nav class="pagination">

            <?php if ($page > 1): ?>

                <a
                    href="list.php?page=<?php echo e($page - 1); ?><?php echo $keyword !== '' ? '&keyword=' . urlencode($keyword) : ''; ?>">

                    &laquo; Sebelumnya

                </a>

            <?php endif; ?>

            <?php for (
                $i = 1;
                $i <= $totalPages;
                $i++
            ): ?>

                <a
                    href="list.php?page=<?php echo e($i); ?><?php echo $keyword !== '' ? '&keyword=' . urlencode($keyword) : ''; ?>"
                    class="<?php echo $i === $page ? 'active' : ''; ?>">

                    <?php echo e($i); ?>

                </a>

            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>

                <a
                    href="list.php?page=<?php echo e($page + 1); ?><?php echo $keyword !== '' ? '&keyword=' . urlencode($keyword) : ''; ?>">

                    Berikutnya &raquo;

                </a>

            <?php endif; ?>

        </nav>

    <?php endif; ?>

</section>

<?php

include __DIR__ . '/../includes/footer.php';

?>
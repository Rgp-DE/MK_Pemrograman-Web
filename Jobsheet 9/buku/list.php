<?php

$page_title = "Daftar Buku";

require_once __DIR__ . '/../includes/koneksi.php';

$keyword = trim($_GET['keyword'] ?? '');

/*
 * Modifikasi No.2
 * Menentukan jumlah data buku yang ditampilkan
 * pada setiap halaman.
 */
$perPage = 10;

$page = filter_input(
    INPUT_GET,
    'page',
    FILTER_VALIDATE_INT
);

if (!$page || $page < 1) {
    $page = 1;
}

/*
 * Menghitung total data buku.
 *
 * Modifikasi No.3:
 * Pencarian dilakukan pada kolom judul
 * dan pengarang.
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
        'keyword' => '%' . $keyword . '%'
    ]);

    $totalBuku = (int) $stmtCount->fetchColumn();

} else {

    $totalBuku = (int) $pdo
        ->query("SELECT COUNT(*) FROM buku")
        ->fetchColumn();
}

/*
 * Menghitung jumlah halaman.
 */
$totalPages = max(
    1,
    (int) ceil($totalBuku / $perPage)
);

/*
 * Jika halaman yang diminta melebihi
 * jumlah halaman yang tersedia,
 * gunakan halaman terakhir.
 */
if ($page > $totalPages) {
    $page = $totalPages;
}

/*
 * Menghitung posisi awal data.
 */
$offset = ($page - 1) * $perPage;

/*
 * Mengambil data buku berdasarkan halaman.
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
}

$daftarBuku = $stmt->fetchAll(PDO::FETCH_ASSOC);

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

include __DIR__ . '/../includes/header.php';
?>

<section>

    <h2>Daftar Buku</h2>

    <?php if ($flash): ?>

        <p class="flash flash-<?php echo htmlspecialchars($flash['type']); ?>">
            <?php echo htmlspecialchars($flash['pesan']); ?>
        </p>

    <?php endif; ?>


    <div class="search-box">

        <form method="get" action="list.php">

            <label for="search-input">
                Cari Judul atau Pengarang
            </label>

            <input
                type="text"
                id="search-input"
                name="keyword"
                data-server-search="true"
                value="<?php echo htmlspecialchars($keyword); ?>"
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


    <p class="table-counter">

        Menampilkan
        <?php echo count($daftarBuku); ?>
        dari
        <?php echo $totalBuku; ?>
        buku

        <?php if ($keyword !== ''): ?>

            untuk pencarian
            "<?php echo htmlspecialchars($keyword); ?>"

        <?php endif; ?>

    </p>


    <div class="table-responsive">

        <table>

            <thead>

                <tr>
                    <th>Judul</th>
                    <th>Pengarang</th>
                    <th>Tahun</th>
                    <th>ISBN</th>
                    <th>Stok</th>
                    <th>Kategori</th>
                    <th>Tanggal Ditambahkan</th>
                    <th>Aksi</th>
                </tr>

            </thead>


            <tbody>

                <?php if (empty($daftarBuku)): ?>

                    <tr>

                        <td colspan="8">
                            Tidak ada data buku.
                        </td>

                    </tr>

                <?php else: ?>

                    <?php foreach ($daftarBuku as $buku): ?>

                        <tr>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $buku['judul']
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $buku['pengarang']
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $buku['tahun']
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $buku['isbn'] ?? '-'
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $buku['stok']
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $buku['kategori'] ?? '-'
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $buku['tanggal_ditambahkan'] ?? '-'
                                );
                                ?>
                            </td>

                            <td>

                                <a
                                    href="edit.php?id=<?php echo (int) $buku['id']; ?>"
                                    class="btn-edit">
                                    Edit
                                </a>


                                <button
                                    type="button"
                                    class="btn-detail">
                                    Detail
                                </button>


                                <form
                                    class="form-hapus"
                                    method="post"
                                    action="hapus.php">

                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?php echo (int) $buku['id']; ?>">

                                    <button
                                        type="submit"
                                        class="btn-hapus">
                                        Hapus
                                    </button>

                                </form>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php endif; ?>

            </tbody>

        </table>

    </div>


    <?php if ($totalPages > 1): ?>

        <div class="pagination">

            <?php if ($page > 1): ?>

                <a
                    href="?<?php
                    echo http_build_query([
                        'keyword' => $keyword,
                        'page' => $page - 1
                    ]);
                    ?>">
                    &laquo; Sebelumnya
                </a>

            <?php endif; ?>


            <?php for (
                $i = 1;
                $i <= $totalPages;
                $i++
            ): ?>

                <?php if ($i === $page): ?>

                    <span class="active">
                        <?php echo $i; ?>
                    </span>

                <?php else: ?>

                    <a
                        href="?<?php
                        echo http_build_query([
                            'keyword' => $keyword,
                            'page' => $i
                        ]);
                        ?>">
                        <?php echo $i; ?>
                    </a>

                <?php endif; ?>

            <?php endfor; ?>


            <?php if ($page < $totalPages): ?>

                <a
                    href="?<?php
                    echo http_build_query([
                        'keyword' => $keyword,
                        'page' => $page + 1
                    ]);
                    ?>">
                    Berikutnya &raquo;
                </a>

            <?php endif; ?>

        </div>

    <?php endif; ?>

</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
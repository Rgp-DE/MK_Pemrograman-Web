<?php

require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/koneksi.php';

$page_title = "Daftar Anggota";

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$perPage = 5;

$page = max(
    1,
    (int) ($_GET['page'] ?? 1)
);

$offset = ($page - 1) * $perPage;

$keyword = trim(
    $_GET['q'] ?? ''
);


if ($keyword !== '') {

    $hitung = $pdo->prepare("
        SELECT COUNT(*)
        FROM anggota
        WHERE nama ILIKE :kw
    ");

    $hitung->execute([
        'kw' => '%' . $keyword . '%'
    ]);

    $totalRows = (int) $hitung->fetchColumn();


    $stmt = $pdo->prepare("
        SELECT
            id,
            nama,
            no_anggota,
            alamat,
            no_hp,
            email
        FROM anggota
        WHERE nama ILIKE :kw
        ORDER BY id DESC
        LIMIT :limit
        OFFSET :offset
    ");

    $stmt->bindValue(
        ':kw',
        '%' . $keyword . '%',
        PDO::PARAM_STR
    );

} else {

    $totalRows = (int) $pdo
        ->query("
            SELECT COUNT(*)
            FROM anggota
        ")
        ->fetchColumn();


    $stmt = $pdo->prepare("
        SELECT
            id,
            nama,
            no_anggota,
            alamat,
            no_hp,
            email
        FROM anggota
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

$daftarAnggota = $stmt->fetchAll(
    PDO::FETCH_ASSOC
);


$totalPages = max(
    1,
    (int) ceil(
        $totalRows / $perPage
    )
);


if ($page > $totalPages) {
    $page = $totalPages;
}


include __DIR__ . '/../includes/header.php';

?>

<section>

    <h2>Daftar Anggota</h2>


    <?php if ($flash): ?>

        <p class="flash flash-<?php echo htmlspecialchars(
            $flash['type']
        ); ?>">

            <?php echo htmlspecialchars(
                $flash['pesan']
            ); ?>

        </p>

    <?php endif; ?>


    <div class="search-box">

        <form
            method="get"
            action="list.php">

            <label for="search-input">
                Cari Nama Anggota
            </label>

            <input
                type="text"
                id="search-input"
                name="q"
                value="<?php echo htmlspecialchars(
                    $keyword
                ); ?>"
                placeholder="Ketik nama anggota...">

            <button
                type="submit">
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
            <?php echo count($daftarAnggota); ?>
            dari
            <?php echo $totalRows; ?>
            hasil pencarian

        <?php else: ?>

            Menampilkan
            <?php echo count($daftarAnggota); ?>
            dari
            <?php echo $totalRows; ?>
            anggota

        <?php endif; ?>

    </p>


    <div class="table-responsive">

        <table>

            <thead>

                <tr>

                    <th>No. Anggota</th>

                    <th>Nama</th>

                    <th>Alamat</th>

                    <th>No. HP</th>

                    <th>Email</th>

                    <th>Aksi</th>

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
                                <?php echo htmlspecialchars(
                                    $anggota['no_anggota']
                                ); ?>
                            </td>


                            <td>
                                <?php echo htmlspecialchars(
                                    $anggota['nama']
                                ); ?>
                            </td>


                            <td>
                                <?php echo htmlspecialchars(
                                    $anggota['alamat'] ?? '-'
                                ); ?>
                            </td>


                            <td>
                                <?php echo htmlspecialchars(
                                    $anggota['no_hp'] ?? '-'
                                ); ?>
                            </td>


                            <td>
                                <?php echo htmlspecialchars(
                                    $anggota['email']
                                ); ?>
                            </td>


                            <td>


                                <?php if (
                                    $_SESSION['role'] === 'admin'
                                ): ?>

                                    <a
                                        href="edit.php?id=<?php echo (int) $anggota['id']; ?>"
                                        class="btn-edit">

                                        Edit

                                    </a>

                                <?php endif; ?>


                                <button
                                    type="button"
                                    class="btn-detail">

                                    Detail

                                </button>


                                <?php if (
                                    $_SESSION['role'] === 'admin'
                                ): ?>

                                    <form
                                        method="post"
                                        action="hapus.php"
                                        class="form-hapus">

                                        <input
                                            type="hidden"
                                            name="id"
                                            value="<?php echo (int) $anggota['id']; ?>">


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


    <?php if ($totalPages > 1): ?>

        <nav class="pagination">


            <?php if ($page > 1): ?>

                <a
                    href="list.php?page=<?php echo $page - 1; ?><?php echo $keyword !== '' ? '&q=' . urlencode($keyword) : ''; ?>">

                    &laquo; Sebelumnya

                </a>

            <?php endif; ?>


            <?php for (
                $i = 1;
                $i <= $totalPages;
                $i++
            ): ?>

                <a
                    href="list.php?page=<?php echo $i; ?><?php echo $keyword !== '' ? '&q=' . urlencode($keyword) : ''; ?>"
                    class="<?php echo $i === $page ? 'active' : ''; ?>">

                    <?php echo $i; ?>

                </a>

            <?php endfor; ?>


            <?php if ($page < $totalPages): ?>

                <a
                    href="list.php?page=<?php echo $page + 1; ?><?php echo $keyword !== '' ? '&q=' . urlencode($keyword) : ''; ?>">

                    Berikutnya &raquo;

                </a>

            <?php endif; ?>


        </nav>

    <?php endif; ?>


</section>


<?php include __DIR__ . '/../includes/footer.php'; ?>
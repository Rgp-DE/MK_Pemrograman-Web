<?php

require __DIR__ . '/../includes/auth.php';

$page_title = "Daftar Anggota";

include __DIR__ . '/../includes/header.php';

require __DIR__ . '/../includes/koneksi.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);


/*
|--------------------------------------------------------------------------
| Pagination
|--------------------------------------------------------------------------
*/

$perPage = 5;

$page = max(
    1,
    (int) ($_GET['page'] ?? 1)
);

$offset = ($page - 1) * $perPage;


/*
|--------------------------------------------------------------------------
| Pencarian
|--------------------------------------------------------------------------
*/

$keyword = trim($_GET['q'] ?? '');


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
    (int) ceil($totalRows / $perPage)
);


/*
|--------------------------------------------------------------------------
| Jika page melebihi jumlah halaman
|--------------------------------------------------------------------------
*/

if ($page > $totalPages) {
    $page = $totalPages;
}

?>

<section>

    <h2>Daftar Anggota</h2>


    <?php if ($flash): ?>

        <p class="flash flash-<?php echo htmlspecialchars($flash['type']); ?>">

            <?php
            echo htmlspecialchars($flash['pesan']);
            ?>

        </p>

    <?php endif; ?>


    <div class="search-box">

        <form
            method="get"
            action="list.php">

            <span>

                <label for="search-input">
                    Cari Nama Anggota
                </label>

                <br>

                <input
                    type="text"
                    id="search-input"
                    name="q"
                    value="<?php echo htmlspecialchars($keyword); ?>"
                    placeholder="Ketik nama anggota...">

            </span>


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


    <div class="table-responsive">

        <table>

            <thead>

                <tr>

                    <th>No. Anggota</th>

                    <th>Nama</th>

                    <th>Alamat</th>

                    <th>No. HP</th>

                    <th>Aksi</th>

                </tr>

            </thead>


            <tbody>

                <?php if (empty($daftarAnggota)): ?>

                    <tr>

                        <td colspan="5">

                            Tidak ada data anggota yang cocok.

                        </td>

                    </tr>

                <?php else: ?>

                    <?php foreach ($daftarAnggota as $anggota): ?>

                        <tr>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $anggota['no_anggota']
                                );
                                ?>
                            </td>


                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $anggota['nama']
                                );
                                ?>
                            </td>


                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $anggota['alamat'] ?? ''
                                );
                                ?>
                            </td>


                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $anggota['no_hp'] ?? ''
                                );
                                ?>
                            </td>


                            <td>

                                <a
                                    href="edit.php?id=<?php echo (int) $anggota['id']; ?>"
                                    class="btn-edit">
                                    Edit
                                </a>


                                <form
                                    class="form-hapus"
                                    method="post"
                                    action="hapus.php">

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

                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php endif; ?>

            </tbody>

        </table>

    </div>


    <?php if ($totalPages > 1): ?>

        <nav class="pagination">

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>

                <a
                    href="list.php?page=<?php echo $i; ?><?php echo $keyword !== '' ? '&q=' . urlencode($keyword) : ''; ?>"
                    class="<?php echo $i === $page ? 'active' : ''; ?>">

                    <?php echo $i; ?>

                </a>

            <?php endfor; ?>

        </nav>

    <?php endif; ?>

</section>


<?php include __DIR__ . '/../includes/footer.php'; ?>
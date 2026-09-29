<?php

$page_title = "Daftar Anggota";

require_once __DIR__ . '/../includes/koneksi.php';

include __DIR__ . '/../includes/header.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$keyword = trim($_GET['keyword'] ?? '');

$totalAnggota = $pdo
    ->query("SELECT COUNT(*) FROM anggota")
    ->fetchColumn();

if ($keyword !== '') {
    $stmt = $pdo->prepare("
        SELECT
            id,
            no_anggota,
            nama,
            alamat,
            no_hp,
            email
        FROM anggota
        WHERE nama ILIKE :keyword
        ORDER BY id DESC
    ");

    $stmt->execute([
        'keyword' => '%' . $keyword . '%'
    ]);

    $daftarAnggota = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $daftarAnggota = $pdo
        ->query("
            SELECT
                id,
                no_anggota,
                nama,
                alamat,
                no_hp,
                email
            FROM anggota
            ORDER BY id DESC
        ")
        ->fetchAll(PDO::FETCH_ASSOC);
}
?>

<section>

    <h2>Daftar Anggota</h2>

    <?php if ($flash): ?>

        <p class="flash flash-<?php echo htmlspecialchars($flash['type']); ?>">
            <?php echo htmlspecialchars($flash['pesan']); ?>
        </p>

    <?php endif; ?>

    <p id="table-counter" class="table-counter">
        Menampilkan <?php echo count($daftarAnggota); ?>
        dari <?php echo $totalAnggota; ?> anggota
    </p>

    <div class="search-box">

        <form method="get" action="list.php">

            <label for="search-input">
                Cari Nama Anggota
            </label>

            <input
                type="text"
                id="search-input"
                name="keyword"
                data-server-search="true"
                value="<?php echo htmlspecialchars($keyword); ?>"
                placeholder="Ketik nama anggota...">

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
                    <th>Email</th>
                    <th>Aksi</th>
                </tr>

            </thead>

            <tbody>

                <?php if (empty($daftarAnggota)): ?>

                    <tr>

                        <td colspan="6">

                            <?php if ($keyword !== ''): ?>

                                Anggota dengan nama
                                "<?php echo htmlspecialchars($keyword); ?>"
                                tidak ditemukan.

                            <?php else: ?>

                                Belum ada data anggota.
                                Silakan tambah lewat menu
                                "Tambah Anggota".

                            <?php endif; ?>

                        </td>

                    </tr>

                <?php else: ?>

                    <?php foreach ($daftarAnggota as $anggota): ?>

                        <tr>

                            <td>
                                <?php echo htmlspecialchars(
                                    $anggota['no_anggota'] ?? ''
                                ); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars(
                                    $anggota['nama'] ?? ''
                                ); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars(
                                    $anggota['alamat'] ?? ''
                                ); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars(
                                    $anggota['no_hp'] ?? ''
                                ); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars(
                                    $anggota['email'] ?? ''
                                ); ?>
                            </td>

                            <td>

                                <a
                                    href="edit.php?id=<?php echo (int) $anggota['id']; ?>"
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

</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
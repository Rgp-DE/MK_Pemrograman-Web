<?php

$page_title = "Daftar Buku";

require_once __DIR__ . '/../includes/koneksi.php';

include __DIR__ . '/../includes/header.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$keyword = trim($_GET['keyword'] ?? '');

$totalBuku = $pdo
    ->query("SELECT COUNT(*) FROM buku")
    ->fetchColumn();

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
        WHERE judul ILIKE :keyword
        ORDER BY id DESC
    ");

    $stmt->execute([
        'keyword' => '%' . $keyword . '%'
    ]);

    $daftarBuku = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $daftarBuku = $pdo
        ->query("
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
        ")
        ->fetchAll(PDO::FETCH_ASSOC);
}
?>

<section>
    <h2>Daftar Buku</h2>

    <?php if ($flash): ?>
        <p class="flash flash-<?php echo htmlspecialchars($flash['type']); ?>">
            <?php echo htmlspecialchars($flash['pesan']); ?>
        </p>
    <?php endif; ?>

    <p id="table-counter" class="table-counter">
        Menampilkan <?php echo count($daftarBuku); ?>
        dari <?php echo $totalBuku; ?> buku
    </p>

    <div class="search-box">
        <form method="get" action="list.php">

            <label for="search-input">
                Cari Judul Buku
            </label>

            <input
                type="text"
                id="search-input"
                name="keyword"
                data-server-search="true"
                value="<?php echo htmlspecialchars($keyword); ?>"
                placeholder="Ketik judul buku...">

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
                    <th>Judul</th>
                    <th>Pengarang</th>
                    <th>Tahun</th>
                    <th>Stok</th>
                    <th>Kategori</th>
                    <th>Tanggal Ditambahkan</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>

                <?php if (empty($daftarBuku)): ?>

                    <tr>
                        <td colspan="7">
                            <?php if ($keyword !== ''): ?>

                                Buku dengan judul
                                "<?php echo htmlspecialchars($keyword); ?>"
                                tidak ditemukan.

                            <?php else: ?>

                                Belum ada data buku.
                                Silakan tambah lewat menu
                                "Tambah Buku".

                            <?php endif; ?>
                        </td>
                    </tr>

                <?php else: ?>

                    <?php foreach ($daftarBuku as $buku): ?>

                        <tr>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $buku['judul'] ?? ''
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $buku['pengarang'] ?? ''
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $buku['tahun'] ?? ''
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $buku['stok'] ?? ''
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $buku['kategori'] ?? ''
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $buku['tanggal_ditambahkan'] ?? ''
                                );
                                ?>
                            </td>

                            <td>
                                <button
                                    type="button"
                                    class="btn-edit">
                                    Edit
                                </button>

                                <button
                                    type="button"
                                    class="btn-detail">
                                    Detail
                                </button>

                                <button
                                    type="button"
                                    class="btn-hapus">
                                    Hapus
                                </button>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php endif; ?>

            </tbody>
        </table>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
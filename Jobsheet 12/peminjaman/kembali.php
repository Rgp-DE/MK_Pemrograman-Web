
<?php

require __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/koneksi.php';
require_once __DIR__ . '/../includes/helpers.php';

$page_title = "Pengembalian Buku";

$keyword = trim($_GET['keyword'] ?? '');

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

if ($keyword !== '') {
    $stmt = $pdo->prepare("
        SELECT
            p.id,
            p.tanggal_pinjam,
            b.judul,
            a.nama,
            a.no_anggota
        FROM peminjaman p
        JOIN buku b ON b.id = p.buku_id
        JOIN anggota a ON a.id = p.anggota_id
        WHERE
            p.status = 'dipinjam'
            AND (
                b.judul ILIKE :keyword
                OR a.nama ILIKE :keyword
                OR a.no_anggota ILIKE :keyword
            )
        ORDER BY p.tanggal_pinjam ASC, p.id ASC
    ");

    $stmt->execute([
        'keyword' => '%' . $keyword . '%'
    ]);

} else {
    $stmt = $pdo->query("
        SELECT
            p.id,
            p.tanggal_pinjam,
            b.judul,
            a.nama,
            a.no_anggota
        FROM peminjaman p
        JOIN buku b ON b.id = p.buku_id
        JOIN anggota a ON a.id = p.anggota_id
        WHERE p.status = 'dipinjam'
        ORDER BY p.tanggal_pinjam ASC, p.id ASC
    ");
}

$daftarPeminjaman = $stmt->fetchAll(PDO::FETCH_ASSOC);

include __DIR__ . '/../includes/header.php';

?>

<section>

    <h2>Daftar Pengembalian Buku</h2>

    <p>
        Berikut adalah transaksi peminjaman yang belum dikembalikan.
    </p>

    <?php if ($flash): ?>
        <p class="flash flash-<?php echo e($flash['type']); ?>">
            <?php echo e($flash['pesan']); ?>
        </p>
    <?php endif; ?>

    <div class="search-box">
        <form method="get" action="kembali.php">

            <label for="search-input">
                Cari Judul Buku atau Anggota
            </label>

            <input
                type="text"
                id="search-input"
                name="keyword"
                value="<?php echo e($keyword); ?>"
                placeholder="Ketik judul atau nama anggota...">

            <button type="submit">Cari</button>

            <?php if ($keyword !== ''): ?>
                <a href="kembali.php">Reset</a>
            <?php endif; ?>

        </form>
    </div>

    <p class="table-counter">
        Total transaksi aktif: <?php echo e(count($daftarPeminjaman)); ?>
    </p>

    <div class="table-responsive">

        <table>
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Nama Anggota</th>
                    <th>No. Anggota</th>
                    <th>Judul Buku</th>
                    <th>Tanggal Pinjam</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>

                <?php if (empty($daftarPeminjaman)): ?>

                    <tr>
                        <td colspan="6">
                            Tidak ada transaksi peminjaman aktif.
                        </td>
                    </tr>

                <?php else: ?>

                    <?php foreach ($daftarPeminjaman as $i => $peminjaman): ?>

                        <tr>
                            <td><?php echo e($i + 1); ?></td>

                            <td>
                                <?php echo e($peminjaman['nama']); ?>
                            </td>

                            <td>
                                <?php echo e($peminjaman['no_anggota']); ?>
                            </td>

                            <td>
                                <?php echo e($peminjaman['judul']); ?>
                            </td>

                            <td>
                                <?php
                                echo e(
                                    date(
                                        'd-m-Y',
                                        strtotime($peminjaman['tanggal_pinjam'])
                                    )
                                );
                                ?>
                            </td>

                            <td>
                                <form
                                    method="post"
                                    action="proses_kembali.php"
                                    class="form-hapus">

                                    <?php
                                    require_once __DIR__ . '/../includes/csrf.php';
                                    echo csrf_field();
                                    ?>

                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?php echo e($peminjaman['id']); ?>">

                                    <button
                                        type="submit"
                                        class="btn-edit">
                                        Kembalikan
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


<?php

require __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/koneksi.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/csrf.php';

$page_title = "Pengembalian Buku";

$keyword = trim($_GET['keyword'] ?? '');

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

/*
|--------------------------------------------------------------------------
| Ambil transaksi peminjaman aktif
|--------------------------------------------------------------------------
*/

$sqlDasar = "
    SELECT
        p.id,
        p.tanggal_pinjam,
        p.tanggal_jatuh_tempo,
        b.judul,
        a.nama,
        a.no_anggota,
        a.no_hp,
        (
            CURRENT_DATE > p.tanggal_jatuh_tempo
        ) AS terlambat
    FROM peminjaman p
    JOIN buku b
        ON b.id = p.buku_id
    JOIN anggota a
        ON a.id = p.anggota_id
    WHERE p.status = 'dipinjam'
";

if ($keyword !== '') {
    $stmt = $pdo->prepare(
        $sqlDasar . "
            AND (
                b.judul ILIKE :keyword
                OR a.nama ILIKE :keyword
                OR a.no_anggota ILIKE :keyword
                OR a.no_hp ILIKE :keyword
            )
            ORDER BY
                p.tanggal_jatuh_tempo ASC,
                p.id ASC
        "
    );

    $stmt->execute([
        'keyword' => '%' . $keyword . '%'
    ]);
} else {
    $stmt = $pdo->query(
        $sqlDasar . "
            ORDER BY
                p.tanggal_jatuh_tempo ASC,
                p.id ASC
        "
    );
}

$daftarPeminjaman = $stmt->fetchAll(
    PDO::FETCH_ASSOC
);

include __DIR__ . '/../includes/header.php';

?>

<section>

    <h2>Daftar Pengembalian Buku</h2>

    <p>
        Berikut adalah transaksi peminjaman yang belum dikembalikan.
        Perhatikan tanggal jatuh tempo untuk mengetahui keterlambatan.
    </p>

    <?php if ($flash): ?>

        <p class="flash flash-<?php echo e($flash['type']); ?>">
            <?php echo e($flash['pesan']); ?>
        </p>

    <?php endif; ?>

    <div class="search-box">

        <form method="get" action="kembali.php">

            <label for="search-input">
                Cari Judul Buku, Anggota, atau No. HP
            </label>

            <input
                type="text"
                id="search-input"
                name="keyword"
                value="<?php echo e($keyword); ?>"
                placeholder="Ketik judul, nama, atau no. HP...">

            <button type="submit">Cari</button>

            <?php if ($keyword !== ''): ?>
                <a href="kembali.php">Reset</a>
            <?php endif; ?>

        </form>

    </div>

    <p class="table-counter">
        Total transaksi aktif:
        <?php echo e(count($daftarPeminjaman)); ?>
    </p>

    <div class="table-responsive">

        <table>

            <thead>
                <tr>
                    <th>No.</th>
                    <th>Nama Anggota</th>
                    <th>No. Anggota</th>
                    <th>No. HP</th>
                    <th>Judul Buku</th>
                    <th>Tanggal Pinjam</th>
                    <th>Tanggal Jatuh Tempo</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>

                <?php if (empty($daftarPeminjaman)): ?>

                    <tr>
                        <td colspan="9">
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
                                <?php echo e($peminjaman['no_hp'] ?: '-'); ?>
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
                                <?php
                                echo e(
                                    date(
                                        'd-m-Y',
                                        strtotime($peminjaman['tanggal_jatuh_tempo'])
                                    )
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                $terlambat =
                                    in_array(
                                        $peminjaman['terlambat'],
                                        [true, 't', 1, '1'],
                                        true
                                    );
                                ?>

                                <?php if ($terlambat): ?>
                                    <span class="status-terlambat">
                                        Terlambat
                                    </span>
                                <?php else: ?>
                                    <span class="status-aman">
                                        Belum Terlambat
                                    </span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <form
                                    method="post"
                                    action="proses_kembali.php"
                                    class="form-hapus">

                                    <?php echo csrf_field(); ?>

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

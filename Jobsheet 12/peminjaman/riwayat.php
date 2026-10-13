
<?php

require __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/koneksi.php';
require_once __DIR__ . '/../includes/helpers.php';

$page_title = "Riwayat Peminjaman";

$keyword = trim($_GET['keyword'] ?? '');

/*
|--------------------------------------------------------------------------
| Ambil riwayat peminjaman
|--------------------------------------------------------------------------
*/

$sqlDasar = "
    SELECT
        p.id,
        p.tanggal_pinjam,
        p.tanggal_jatuh_tempo,
        p.tanggal_kembali,
        p.status,
        b.judul,
        a.nama,
        a.no_anggota,
        a.no_hp
    FROM peminjaman p
    JOIN buku b
        ON b.id = p.buku_id
    JOIN anggota a
        ON a.id = p.anggota_id
";

if ($keyword !== '') {
    $stmt = $pdo->prepare(
        $sqlDasar . "
            WHERE
                b.judul ILIKE :keyword
                OR a.nama ILIKE :keyword
                OR a.no_anggota ILIKE :keyword
                OR a.no_hp ILIKE :keyword
            ORDER BY p.id DESC
        "
    );

    $stmt->execute([
        'keyword' => '%' . $keyword . '%'
    ]);
} else {
    $stmt = $pdo->query(
        $sqlDasar . "
            ORDER BY p.id DESC
        "
    );
}

$riwayatPeminjaman = $stmt->fetchAll(
    PDO::FETCH_ASSOC
);

include __DIR__ . '/../includes/header.php';

?>

<section>

    <h2>Riwayat Peminjaman</h2>

    <p>
        Halaman ini menampilkan riwayat transaksi peminjaman buku
        beserta informasi anggota dan tanggal transaksi.
    </p>

    <div class="search-box">

        <form method="get" action="riwayat.php">

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
                <a href="riwayat.php">Reset</a>
            <?php endif; ?>

        </form>

    </div>

    <p class="table-counter">
        Total transaksi ditemukan:
        <?php echo e(count($riwayatPeminjaman)); ?>
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
                    <th>Tanggal Kembali</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>

                <?php if (empty($riwayatPeminjaman)): ?>

                    <tr>
                        <td colspan="9">
                            Tidak ada riwayat peminjaman yang ditemukan.
                        </td>
                    </tr>

                <?php else: ?>

                    <?php foreach ($riwayatPeminjaman as $i => $peminjaman): ?>

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
                                <?php if (!empty($peminjaman['tanggal_kembali'])): ?>
                                    <?php
                                    echo e(
                                        date(
                                            'd-m-Y',
                                            strtotime($peminjaman['tanggal_kembali'])
                                        )
                                    );
                                    ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php if ($peminjaman['status'] === 'dikembalikan'): ?>
                                    <span class="status-aman">
                                        Dikembalikan
                                    </span>
                                <?php else: ?>
                                    <span class="status-terlambat">
                                        Dipinjam
                                    </span>
                                <?php endif; ?>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php endif; ?>

            </tbody>

        </table>

    </div>

</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>

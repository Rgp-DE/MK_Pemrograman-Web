
<?php

require __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/koneksi.php';
require_once __DIR__ . '/../includes/helpers.php';

$page_title = "Riwayat Peminjaman";

$anggotaId = filter_input(
    INPUT_GET,
    'anggota_id',
    FILTER_VALIDATE_INT
);

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

/*
|--------------------------------------------------------------------------
| Ambil daftar anggota untuk dropdown
|--------------------------------------------------------------------------
*/

$daftarAnggota = $pdo->query("
    SELECT id, nama, no_anggota
    FROM anggota
    ORDER BY nama
")->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| Ambil riwayat jika anggota dipilih
|--------------------------------------------------------------------------
*/

$daftarRiwayat = [];

if ($anggotaId && $anggotaId > 0) {

    $stmt = $pdo->prepare("
        SELECT
            p.id,
            p.tanggal_pinjam,
            p.tanggal_kembali,
            p.status,
            b.judul,
            b.pengarang,
            a.nama,
            a.no_anggota
        FROM peminjaman p
        JOIN buku b
            ON b.id = p.buku_id
        JOIN anggota a
            ON a.id = p.anggota_id
        WHERE p.anggota_id = :anggota_id
        ORDER BY
            p.tanggal_pinjam DESC,
            p.id DESC
    ");

    $stmt->execute([
        'anggota_id' => $anggotaId
    ]);

    $daftarRiwayat = $stmt->fetchAll(
        PDO::FETCH_ASSOC
    );
}

include __DIR__ . '/../includes/header.php';

?>

<section>

    <h2>Riwayat Peminjaman</h2>

    <p>
        Pilih anggota untuk melihat seluruh riwayat peminjamannya.
    </p>

    <?php if ($flash): ?>

        <p class="flash flash-<?php echo e($flash['type']); ?>">
            <?php echo e($flash['pesan']); ?>
        </p>

    <?php endif; ?>

    <div class="search-box">

        <form method="get" action="riwayat.php">

            <label for="anggota_id">
                Pilih Anggota
            </label>

            <select
                id="anggota_id"
                name="anggota_id"
                required>

                <option value="">
                    -- Pilih Anggota --
                </option>

                <?php foreach ($daftarAnggota as $anggota): ?>

                    <option
                        value="<?php echo e($anggota['id']); ?>"
                        <?php
                        echo (
                            $anggotaId &&
                            (int) $anggota['id'] === $anggotaId
                        ) ? 'selected' : '';
                        ?>>

                        <?php echo e($anggota['nama']); ?>
                        (<?php echo e($anggota['no_anggota']); ?>)

                    </option>

                <?php endforeach; ?>

            </select>

            <button type="submit">
                Tampilkan Riwayat
            </button>

            <?php if ($anggotaId): ?>

                <a href="riwayat.php">
                    Reset
                </a>

            <?php endif; ?>

        </form>

    </div>

    <?php if ($anggotaId && $anggotaId > 0): ?>

        <p class="table-counter">
            Total riwayat:
            <?php echo e(count($daftarRiwayat)); ?> transaksi
        </p>

        <div class="table-responsive">

            <table>

                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Nama Anggota</th>
                        <th>Judul Buku</th>
                        <th>Pengarang</th>
                        <th>Tanggal Pinjam</th>
                        <th>Tanggal Kembali</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if (empty($daftarRiwayat)): ?>

                        <tr>
                            <td colspan="7">
                                Anggota ini belum memiliki riwayat peminjaman.
                            </td>
                        </tr>

                    <?php else: ?>

                        <?php foreach ($daftarRiwayat as $i => $item): ?>

                            <tr>

                                <td>
                                    <?php echo e($i + 1); ?>
                                </td>

                                <td>
                                    <?php echo e($item['nama']); ?>
                                </td>

                                <td>
                                    <?php echo e($item['judul']); ?>
                                </td>

                                <td>
                                    <?php echo e($item['pengarang']); ?>
                                </td>

                                <td>
                                    <?php
                                    echo e(
                                        date(
                                            'd-m-Y',
                                            strtotime($item['tanggal_pinjam'])
                                        )
                                    );
                                    ?>
                                </td>

                                <td>
                                    <?php if ($item['tanggal_kembali']): ?>

                                        <?php
                                        echo e(
                                            date(
                                                'd-m-Y',
                                                strtotime($item['tanggal_kembali'])
                                            )
                                        );
                                        ?>

                                    <?php else: ?>

                                        -

                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?php
                                    echo $item['status'] === 'dikembalikan'
                                        ? 'Selesai'
                                        : 'Dipinjam';
                                    ?>
                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    <?php endif; ?>

</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>

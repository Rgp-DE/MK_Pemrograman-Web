
<?php

require __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/koneksi.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/csrf.php';

$page_title = "Peminjaman Baru";

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$daftarAnggota = $pdo->query("
    SELECT id, nama, no_anggota
    FROM anggota
    ORDER BY nama
")->fetchAll(PDO::FETCH_ASSOC);

$daftarBukuTersedia = $pdo->query("
    SELECT id, judul, pengarang, stok
    FROM buku
    WHERE stok > 0
    ORDER BY judul
")->fetchAll(PDO::FETCH_ASSOC);

include __DIR__ . '/../includes/header.php';

?>

<section class="form-section">

    <h2>Peminjaman Buku Baru</h2>

    <p>
        Pilih anggota dan buku yang akan dipinjam.
    </p>

    <?php if ($flash): ?>
        <p class="flash flash-<?php echo e($flash['type']); ?>">
            <?php echo e($flash['pesan']); ?>
        </p>
    <?php endif; ?>

    <?php if (empty($daftarAnggota)): ?>

        <p class="flash flash-error">
            Belum ada data anggota. Tambahkan anggota terlebih dahulu.
        </p>

    <?php elseif (empty($daftarBukuTersedia)): ?>

        <p class="flash flash-error">
            Tidak ada buku dengan stok tersedia saat ini.
        </p>

    <?php else: ?>

        <form method="post" action="proses_tambah.php">

            <?php echo csrf_field(); ?>

            <div class="form-group">
                <label for="anggota_id">Anggota</label>

                <select
                    id="anggota_id"
                    name="anggota_id"
                    required>

                    <option value="">
                        -- Pilih Anggota --
                    </option>

                    <?php foreach ($daftarAnggota as $anggota): ?>
                        <option value="<?php echo e($anggota['id']); ?>">
                            <?php echo e($anggota['nama']); ?>
                            (<?php echo e($anggota['no_anggota']); ?>)
                        </option>
                    <?php endforeach; ?>

                </select>
            </div>

            <div class="form-group">
                <label for="buku_id">
                    Buku yang Dipinjam
                </label>

                <select
                    id="buku_id"
                    name="buku_id"
                    required>

                    <option value="">
                        -- Pilih Buku --
                    </option>

                    <?php foreach ($daftarBukuTersedia as $buku): ?>
                        <option value="<?php echo e($buku['id']); ?>">
                            <?php echo e($buku['judul']); ?>
                            — <?php echo e($buku['pengarang']); ?>
                            (Stok: <?php echo e($buku['stok']); ?>)
                        </option>
                    <?php endforeach; ?>

                </select>
            </div>

            <div class="form-actions">

                <button
                    type="submit"
                    class="btn-edit">
                    Simpan Peminjaman
                </button>

                <a
                    href="kembali.php"
                    class="btn-detail">
                    Lihat Pengembalian
                </a>

            </div>

        </form>

    <?php endif; ?>

</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>

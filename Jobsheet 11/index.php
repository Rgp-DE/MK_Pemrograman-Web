<?php

require __DIR__ . '/includes/auth.php';
require __DIR__ . '/includes/koneksi.php';

$page_title = "Beranda";

$totalBuku = (int) $pdo
    ->query("
        SELECT COUNT(*)
        FROM buku
    ")
    ->fetchColumn();

$totalAnggota = (int) $pdo
    ->query("
        SELECT COUNT(*)
        FROM anggota
    ")
    ->fetchColumn();

$totalBukuTerlambat = 0;

include __DIR__ . '/includes/header.php';

?>

<section>

    <h2>
        Dashboard
    </h2>

    <p>
        Selamat datang di
        <strong>
            SIMPUS-Mini
        </strong>.
    </p>

    <div class="statistik">

        <div class="card">

            <h3>
                Total Buku
            </h3>

            <p>
                <?php echo e($totalBuku); ?>
            </p>

        </div>

        <div class="card">

            <h3>
                Total Anggota
            </h3>

            <p>
                <?php echo e($totalAnggota); ?>
            </p>

        </div>

        <div class="card">

            <h3>
                Buku Terlambat
            </h3>

            <p>
                <?php echo e($totalBukuTerlambat); ?>
            </p>

        </div>

        <div class="card">

            <h3>
                Status Sistem
            </h3>

            <p>
                Aktif
            </p>

        </div>

    </div>

</section>

<?php

include __DIR__ . '/includes/footer.php';

?>
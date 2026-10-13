
<?php

/*
|--------------------------------------------------------------------------
| Autentikasi dan koneksi database
|--------------------------------------------------------------------------
*/

require __DIR__ . '/includes/auth.php';
require __DIR__ . '/includes/koneksi.php';

$page_title = "Beranda";

/*
|--------------------------------------------------------------------------
| Menghitung total buku
|--------------------------------------------------------------------------
*/

$totalBuku = (int) $pdo
    ->query("
        SELECT COUNT(*)
        FROM buku
    ")
    ->fetchColumn();

/*
|--------------------------------------------------------------------------
| Menghitung total anggota
|--------------------------------------------------------------------------
*/

$totalAnggota = (int) $pdo
    ->query("
        SELECT COUNT(*)
        FROM anggota
    ")
    ->fetchColumn();

/*
|--------------------------------------------------------------------------
| Menghitung transaksi peminjaman aktif
|--------------------------------------------------------------------------
*/

$totalDipinjam = (int) $pdo
    ->query("
        SELECT COUNT(*)
        FROM peminjaman
        WHERE status = 'dipinjam'
    ")
    ->fetchColumn();

/*
|--------------------------------------------------------------------------
| Nilai sementara untuk fitur buku terlambat
|--------------------------------------------------------------------------
|
| Tetap mempertahankan kartu dari Jobsheet sebelumnya.
| Perhitungan keterlambatan belum diimplementasikan
| pada skema inti Jobsheet 12.
|
*/

$totalBukuTerlambat = 0;

/*
|--------------------------------------------------------------------------
| Menampilkan header
|--------------------------------------------------------------------------
*/

include __DIR__ . '/includes/header.php';

?>

<section>

    <h2>
        Dashboard
    </h2>

    <p>
        Selamat datang di
        <strong>SIMPUS-Mini</strong>.
    </p>

    <p>
        Sistem Informasi Perpustakaan Mini untuk
        pengelolaan buku, anggota, dan transaksi peminjaman.
    </p>

    <div class="statistik">

        <!-- Total Buku -->

        <div class="card">

            <h3>
                Total Buku
            </h3>

            <p>
                <?php echo e($totalBuku); ?>
            </p>

        </div>

        <!-- Total Anggota -->

        <div class="card">

            <h3>
                Total Anggota
            </h3>

            <p>
                <?php echo e($totalAnggota); ?>
            </p>

        </div>

        <!-- Buku Terlambat -->

        <div class="card">

            <h3>
                Buku Terlambat
            </h3>

            <p>
                <?php echo e($totalBukuTerlambat); ?>
            </p>

        </div>

        <!-- Status Sistem -->

        <div class="card">

            <h3>
                Status Sistem
            </h3>

            <p>
                Aktif
            </p>

        </div>

        <!-- Jobsheet 12: Peminjaman Aktif -->

        <div class="card">

            <h3>
                Sedang Dipinjam
            </h3>

            <p>
                <?php echo e($totalDipinjam); ?>
            </p>

        </div>

    </div>

    <div class="form-actions">

        <a
            href="peminjaman/tambah.php"
            class="btn-edit">

            Peminjaman Baru

        </a>

        <a
            href="peminjaman/kembali.php"
            class="btn-detail">

            Pengembalian Buku

        </a>

        <a
            href="peminjaman/riwayat.php"
            class="btn-detail">

            Riwayat Peminjaman

        </a>

    </div>

</section>

<?php

include __DIR__ . '/includes/footer.php';

?>

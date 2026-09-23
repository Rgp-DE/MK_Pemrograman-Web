<?php

$page_title = "Beranda";

require_once __DIR__ . '/includes/koneksi.php';

include __DIR__ . '/includes/header.php';

/* ===== Ambil jumlah buku dari PostgreSQL ===== */
$totalBuku = $pdo
    ->query("SELECT COUNT(*) FROM buku")
    ->fetchColumn();

/* ===== Ambil jumlah anggota dari PostgreSQL ===== */
$totalAnggota = $pdo
    ->query("SELECT COUNT(*) FROM anggota")
    ->fetchColumn();

?>

<section>
    <h2>Selamat Datang di Sistem Perpustakaan Mini</h2>

    <p>
        Aplikasi sederhana untuk mengelola data buku dan anggota perpustakaan.
    </p>
</section>

<section class="statistik">

    <h2>Ringkasan</h2>

    <article>
        <h3>Total Buku</h3>
        <p><?php echo $totalBuku; ?></p>
    </article>

    <article>
        <h3>Total Anggota</h3>
        <p><?php echo $totalAnggota; ?></p>
    </article>

    <article>
        <h3>Sedang Dipinjam</h3>
        <p>3</p>
    </article>

    <article>
        <h3>Buku Terlambat</h3>
        <p>2</p>
    </article>

</section>

<section>
    <h2>Contoh Blok Kode</h2>

    <div class="code-responsive">

        <pre>&lt;div class="contoh-kode-panjang"&gt;
Ini adalah contoh blok kode yang memiliki baris sangat panjang untuk menguji tampilan responsive pada layar sempit agar isi tetap dapat dibaca tanpa merusak layout halaman utama.
&lt;/div&gt;</pre>

    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
<?php

$page_title = "Daftar Buku";

require_once __DIR__ . '/../includes/koneksi.php';

include __DIR__ . '/../includes/header.php';

/* ===== Ambil flash message ===== */
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

/* ===== Ambil data buku dari PostgreSQL ===== */
$daftarBuku = $pdo
    ->query("
        SELECT
            id,
            judul,
            pengarang,
            tahun,
            isbn,
            stok,
            kategori
        FROM buku
        ORDER BY id DESC
    ")
    ->fetchAll(PDO::FETCH_ASSOC);
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
        dari <?php echo count($daftarBuku); ?> buku
    </p>

    <div class="search-box">
        <label for="search-input">Cari Judul Buku</label>

        <input
            type="text"
            id="search-input"
            placeholder="Ketik judul buku...">
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
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>

                <?php if (empty($daftarBuku)): ?>

                    <tr>
                        <td colspan="6">
                            Belum ada data buku.
                            Silakan tambah lewat menu
                            "Tambah Buku".
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
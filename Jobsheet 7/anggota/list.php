<?php
$page_title = "Daftar Anggota";

include __DIR__ . '/../includes/header.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$daftarAnggota = $_SESSION['anggota'] ?? [];
?>

<section>

    <h2>Daftar Anggota</h2>

    <?php if ($flash): ?>

        <p class="flash flash-<?php echo htmlspecialchars($flash['type']); ?>">
            <?php echo htmlspecialchars($flash['pesan']); ?>
        </p>

    <?php endif; ?>

    <div class="search-box">

        <label for="search-input">
            Cari Nama Anggota
        </label>

        <input
            type="text"
            id="search-input"
            placeholder="Ketik nama anggota...">

    </div>

    <div class="table-responsive">

        <table>

            <thead>
                <tr>
                    <th>No. Anggota</th>
                    <th>Nama</th>
                    <th>Alamat</th>
                    <th>No. HP</th>
                    <th>Email</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>

                <?php if (empty($daftarAnggota)): ?>

                    <tr>
                        <td colspan="6">
                            Belum ada data anggota.
                            Silakan tambah lewat menu
                            "Tambah Anggota".
                        </td>
                    </tr>

                <?php else: ?>

                    <?php foreach ($daftarAnggota as $anggota): ?>

                        <tr>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $anggota['no_anggota'] ?? ''
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $anggota['nama'] ?? ''
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $anggota['alamat'] ?? ''
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $anggota['no_hp'] ?? ''
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $anggota['email'] ?? ''
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
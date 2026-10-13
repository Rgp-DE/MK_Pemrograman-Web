
<?php

require __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/koneksi.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/csrf.php';

/*
|--------------------------------------------------------------------------
| Pastikan request menggunakan POST
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: tambah.php');
    exit;
}

/*
|--------------------------------------------------------------------------
| Verifikasi CSRF
|--------------------------------------------------------------------------
*/

csrf_verify();

/*
|--------------------------------------------------------------------------
| Ambil dan validasi input
|--------------------------------------------------------------------------
*/

$anggotaId = filter_input(
    INPUT_POST,
    'anggota_id',
    FILTER_VALIDATE_INT
);

$bukuId = filter_input(
    INPUT_POST,
    'buku_id',
    FILTER_VALIDATE_INT
);

if (
    !$anggotaId ||
    $anggotaId < 1 ||
    !$bukuId ||
    $bukuId < 1
) {
    $_SESSION['flash'] = [
        'type' => 'error',
        'pesan' => 'Anggota atau buku yang dipilih tidak valid.'
    ];

    header('Location: tambah.php');
    exit;
}

/*
|--------------------------------------------------------------------------
| Proses transaksi peminjaman
|--------------------------------------------------------------------------
*/

try {
    $pdo->beginTransaction();

    /*
    | Kunci baris buku agar stok tidak digunakan
    | oleh transaksi lain secara bersamaan.
    */

    $stmtBuku = $pdo->prepare("
        SELECT id, stok
        FROM buku
        WHERE id = :id
        FOR UPDATE
    ");

    $stmtBuku->execute([
        'id' => $bukuId
    ]);

    $buku = $stmtBuku->fetch(PDO::FETCH_ASSOC);

    if (!$buku || (int) $buku['stok'] < 1) {
        throw new RuntimeException(
            'Stok buku tidak tersedia.'
        );
    }

    /*
    | Pastikan anggota benar-benar tersedia.
    */

    $stmtAnggota = $pdo->prepare("
        SELECT id
        FROM anggota
        WHERE id = :id
    ");

    $stmtAnggota->execute([
        'id' => $anggotaId
    ]);

    $anggota = $stmtAnggota->fetch(PDO::FETCH_ASSOC);

    if (!$anggota) {
        throw new RuntimeException(
            'Data anggota tidak ditemukan.'
        );
    }

    /*
    | Catat transaksi peminjaman.
    */

    $stmtInsert = $pdo->prepare("
        INSERT INTO peminjaman (
            buku_id,
            anggota_id,
            tanggal_pinjam,
            status
        )
        VALUES (
            :buku_id,
            :anggota_id,
            CURRENT_DATE,
            'dipinjam'
        )
    ");

    $stmtInsert->execute([
        'buku_id' => $bukuId,
        'anggota_id' => $anggotaId
    ]);

    /*
    | Kurangi stok buku sebanyak satu.
    */

    $stmtUpdate = $pdo->prepare("
        UPDATE buku
        SET stok = stok - 1
        WHERE id = :id
    ");

    $stmtUpdate->execute([
        'id' => $bukuId
    ]);

    /*
    | Simpan kedua perubahan secara bersamaan.
    */

    $pdo->commit();

    $_SESSION['flash'] = [
        'type' => 'success',
        'pesan' => 'Peminjaman berhasil disimpan. Stok buku telah dikurangi.'
    ];

} catch (Throwable $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    if ($e instanceof RuntimeException) {
        $_SESSION['flash'] = [
            'type' => 'error',
            'pesan' => $e->getMessage()
        ];
    } else {
        error_log(
            'Gagal memproses peminjaman: ' . $e->getMessage()
        );

        $_SESSION['flash'] = [
            'type' => 'error',
            'pesan' => 'Peminjaman gagal diproses. Silakan coba kembali.'
        ];
    }
}

header('Location: tambah.php');
exit;

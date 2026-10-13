
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
    header('Location: kembali.php');
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
| Validasi ID transaksi
|--------------------------------------------------------------------------
*/

$id = filter_input(
    INPUT_POST,
    'id',
    FILTER_VALIDATE_INT
);

if (!$id || $id < 1) {
    $_SESSION['flash'] = [
        'type' => 'error',
        'pesan' => 'ID peminjaman tidak valid.'
    ];

    header('Location: kembali.php');
    exit;
}

/*
|--------------------------------------------------------------------------
| Proses pengembalian dalam satu transaksi database
|--------------------------------------------------------------------------
*/

try {
    $pdo->beginTransaction();

    /*
    | Kunci baris peminjaman agar transaksi yang sama
    | tidak dikembalikan dua kali secara bersamaan.
    */

    $stmtPeminjaman = $pdo->prepare("
        SELECT id, buku_id, status
        FROM peminjaman
        WHERE id = :id
        FOR UPDATE
    ");

    $stmtPeminjaman->execute([
        'id' => $id
    ]);

    $peminjaman = $stmtPeminjaman->fetch(
        PDO::FETCH_ASSOC
    );

    if (!$peminjaman) {
        throw new RuntimeException(
            'Data peminjaman tidak ditemukan.'
        );
    }

    if ($peminjaman['status'] !== 'dipinjam') {
        throw new RuntimeException(
            'Buku ini sudah dikembalikan sebelumnya.'
        );
    }

    /*
    | Kunci baris buku sebelum menambah stok.
    */

    $stmtBuku = $pdo->prepare("
        SELECT id
        FROM buku
        WHERE id = :id
        FOR UPDATE
    ");

    $stmtBuku->execute([
        'id' => $peminjaman['buku_id']
    ]);

    $buku = $stmtBuku->fetch(
        PDO::FETCH_ASSOC
    );

    if (!$buku) {
        throw new RuntimeException(
            'Data buku tidak ditemukan.'
        );
    }

    /*
    | Ubah status dan tanggal pengembalian.
    */

    $stmtUpdatePeminjaman = $pdo->prepare("
        UPDATE peminjaman
        SET
            status = 'dikembalikan',
            tanggal_kembali = CURRENT_DATE
        WHERE
            id = :id
            AND status = 'dipinjam'
    ");

    $stmtUpdatePeminjaman->execute([
        'id' => $id
    ]);

    if ($stmtUpdatePeminjaman->rowCount() !== 1) {
        throw new RuntimeException(
            'Status peminjaman gagal diperbarui.'
        );
    }

    /*
    | Tambahkan stok buku satu.
    */

    $stmtUpdateBuku = $pdo->prepare("
        UPDATE buku
        SET stok = stok + 1
        WHERE id = :id
    ");

    $stmtUpdateBuku->execute([
        'id' => $peminjaman['buku_id']
    ]);

    if ($stmtUpdateBuku->rowCount() !== 1) {
        throw new RuntimeException(
            'Stok buku gagal diperbarui.'
        );
    }

    /*
    | Simpan semua perubahan.
    */

    $pdo->commit();

    $_SESSION['flash'] = [
        'type' => 'success',
        'pesan' => 'Buku berhasil dikembalikan dan stok telah diperbarui.'
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
            'Gagal memproses pengembalian: ' . $e->getMessage()
        );

        $_SESSION['flash'] = [
            'type' => 'error',
            'pesan' => 'Pengembalian gagal diproses. Silakan coba kembali.'
        ];
    }
}

header('Location: kembali.php');
exit;

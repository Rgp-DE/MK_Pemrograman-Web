
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
| Validasi input
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
    | Kunci baris anggota agar proses peminjaman
    | untuk anggota yang sama tidak berjalan bersamaan.
    */

    $stmtAnggota = $pdo->prepare("
        SELECT id
        FROM anggota
        WHERE id = :id
        FOR UPDATE
    ");

    $stmtAnggota->execute([
        'id' => $anggotaId
    ]);

    $anggota = $stmtAnggota->fetch(
        PDO::FETCH_ASSOC
    );

    if (!$anggota) {
        throw new RuntimeException(
            'Data anggota tidak ditemukan.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Validasi bisnis keterlambatan lebih dari 14 hari
    |--------------------------------------------------------------------------
    */

    $stmtTerlambat = $pdo->prepare("
        SELECT COUNT(*)
        FROM peminjaman
        WHERE
            anggota_id = :anggota_id
            AND status = 'dipinjam'
            AND CURRENT_DATE - tanggal_pinjam > 14
    ");

    $stmtTerlambat->execute([
        'anggota_id' => $anggotaId
    ]);

    $jumlahTerlambat = (int) $stmtTerlambat->fetchColumn();

    if ($jumlahTerlambat > 0) {
        throw new RuntimeException(
            'Peminjaman baru ditolak. Anggota memiliki buku yang terlambat dikembalikan lebih dari 14 hari. Silakan kembalikan buku tersebut terlebih dahulu.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Kunci dan periksa stok buku
    |--------------------------------------------------------------------------
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

    $buku = $stmtBuku->fetch(
        PDO::FETCH_ASSOC
    );

    if (!$buku || (int) $buku['stok'] < 1) {
        throw new RuntimeException(
            'Stok buku tidak tersedia.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Simpan peminjaman dan tanggal jatuh tempo
    |--------------------------------------------------------------------------
    */

    $stmtInsert = $pdo->prepare("
        INSERT INTO peminjaman (
            buku_id,
            anggota_id,
            tanggal_pinjam,
            tanggal_jatuh_tempo,
            status
        )
        VALUES (
            :buku_id,
            :anggota_id,
            CURRENT_DATE,
            (CURRENT_DATE + INTERVAL '14 days')::DATE,
            'dipinjam'
        )
    ");

    $stmtInsert->execute([
        'buku_id' => $bukuId,
        'anggota_id' => $anggotaId
    ]);

    /*
    |--------------------------------------------------------------------------
    | Kurangi stok buku satu
    |--------------------------------------------------------------------------
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
    |--------------------------------------------------------------------------
    | Simpan transaksi
    |--------------------------------------------------------------------------
    */

    $pdo->commit();

    $_SESSION['flash'] = [
        'type' => 'success',
        'pesan' => 'Peminjaman berhasil disimpan. Tanggal jatuh tempo ditetapkan 14 hari setelah tanggal pinjam.'
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

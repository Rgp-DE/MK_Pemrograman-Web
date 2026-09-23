<?php

require_once __DIR__ . '/includes/koneksi.php';

$fileJson = __DIR__ . '/data/buku.json';

if (!file_exists($fileJson)) {
    die("File data/buku.json tidak ditemukan.");
}

$json = file_get_contents($fileJson);
$dataBuku = json_decode($json, true);

if (!is_array($dataBuku)) {
    die("Data JSON tidak valid.");
}

$stmt = $pdo->prepare("
    INSERT INTO buku (
        judul,
        pengarang,
        tahun,
        isbn,
        stok,
        kategori
    )
    VALUES (
        :judul,
        :pengarang,
        :tahun,
        :isbn,
        :stok,
        :kategori
    )
");

$jumlahBerhasil = 0;

foreach ($dataBuku as $buku) {
    $stmt->execute([
        'judul' => $buku['judul'],
        'pengarang' => $buku['pengarang'],
        'tahun' => (int) $buku['tahun'],
        'isbn' => null,
        'stok' => (int) $buku['stok'],
        'kategori' => $buku['kategori']
    ]);

    $jumlahBerhasil++;
}

echo "Migrasi data buku berhasil.<br>";
echo "Jumlah data yang dimasukkan: " . $jumlahBerhasil;
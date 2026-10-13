
-- Jobsheet 12 - Modifikasi No. 2
-- Menambahkan tanggal jatuh tempo peminjaman

ALTER TABLE peminjaman
ADD COLUMN IF NOT EXISTS tanggal_jatuh_tempo DATE;

-- Mengisi tanggal jatuh tempo untuk data lama
UPDATE peminjaman
SET tanggal_jatuh_tempo =
    (tanggal_pinjam + INTERVAL '14 days')::DATE
WHERE tanggal_jatuh_tempo IS NULL;

-- Memastikan kolom wajib terisi
ALTER TABLE peminjaman
ALTER COLUMN tanggal_jatuh_tempo SET NOT NULL;

# Wireframe dan User Flow SIMPUS-Mini

## User Flow: Petugas Mencari Anggota dengan Tunggakan Lewat Jatuh Tempo

**Aktor:** Petugas

**Tujuan:** Menemukan anggota perpustakaan yang memiliki tunggakan dan telah melewati batas jatuh tempo.

### Alur Pengguna

```text
+---------------------------+
|       MULAI               |
+-------------+-------------+
              |
              v
+---------------------------+
| Petugas membuka sistem    |
| SIMPUS-Mini               |
+-------------+-------------+
              |
              v
+---------------------------+
| Petugas memilih menu      |
| data/peminjaman anggota   |
+-------------+-------------+
              |
              v
+---------------------------+
| Petugas membuka fitur     |
| pencarian / filter        |
+-------------+-------------+
              |
              v
+---------------------------+
| Pilih status:             |
| "Lewat Jatuh Tempo"       |
+-------------+-------------+
              |
              v
+---------------------------+
| Sistem menampilkan daftar |
| anggota yang memiliki     |
| tunggakan lewat jatuh     |
| tempo                     |
+-------------+-------------+
              |
              v
        +-----------+
        | Ada data? |
        +-----+-----+
          Ya | | Tidak
             | |
     +-------+ +----------------------+
     |                              |
     v                              v
+---------------------------+   +---------------------------+
| Petugas memilih salah     |   | Sistem menampilkan pesan  |
| satu anggota              |   | "Data tidak ditemukan"    |
+-------------+-------------+   +-------------+-------------+
              |                               |
              v                               |
+---------------------------+                 |
| Sistem menampilkan detail |                 |
| anggota dan tunggakan     |                 |
+-------------+-------------+                 |
              |                               |
              v                               |
+---------------------------+                 |
| Petugas memeriksa detail  |                 |
| jatuh tempo               |                 |
+-------------+-------------+                 |
              |                               |
              +---------------+---------------+
                              |
                              v
                    +-------------------+
                    |      SELESAI      |
                    +-------------------+
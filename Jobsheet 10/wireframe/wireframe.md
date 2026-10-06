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



## Edge Case Proses Peminjaman Buku

Edge case merupakan kondisi khusus yang mungkin jarang terjadi, tetapi tetap perlu dipertimbangkan agar sistem dapat memberikan respons yang tepat dan tidak menghasilkan data yang tidak konsisten.

### 1. Buku yang Sama Dipinjam Dua Kali oleh Anggota yang Sama

**Kondisi:**

Petugas mencoba meminjamkan buku yang sama kepada anggota yang sama dua kali berturut-turut, sementara peminjaman pertama masih aktif.

**Respons Sistem:**

Sistem melakukan pengecekan terhadap data peminjaman aktif sebelum transaksi baru disimpan.

Jika buku yang sama masih tercatat sedang dipinjam oleh anggota tersebut, sistem menolak transaksi kedua dan menampilkan pesan:

> "Buku ini masih tercatat sedang dipinjam oleh anggota tersebut."

**Alur:**

```text
Petugas memilih anggota
        |
        v
Petugas memilih buku
        |
        v
Sistem memeriksa peminjaman aktif
        |
        v
+-------------------------------+
| Buku yang sama masih dipinjam?|
+---------------+---------------+
        Ya      |      Tidak
                |
        +-------+-------+
        |               |
        v               v
Transaksi ditolak   Peminjaman
dan tampil pesan    dapat diproses
        |               |
        +-------+-------+
                |
                v
             Selesai


2. Stok Buku Sudah Habis

Kondisi:

Petugas mencoba memproses peminjaman buku yang memiliki stok 0.

Respons Sistem:

Sistem menolak transaksi dan memberikan informasi bahwa buku sedang tidak tersedia.

Stok buku = 0
     |
     v
Peminjaman ditolak
     |
     v
"Buku sedang tidak tersedia."
3. Anggota Masih Memiliki Tunggakan

Kondisi:

Anggota ingin meminjam buku baru, tetapi masih memiliki peminjaman yang sudah melewati tanggal jatuh tempo.

Respons Sistem:

Sistem memberikan peringatan kepada Petugas sebelum peminjaman baru diproses.

Petugas dapat diarahkan untuk menyelesaikan proses pengembalian atau tunggakan terlebih dahulu sesuai aturan perpustakaan.

4. Data Anggota Tidak Ditemukan

Kondisi:

Petugas memasukkan nomor anggota yang tidak terdaftar di dalam sistem.

Respons Sistem:

Sistem tidak melanjutkan proses peminjaman dan menampilkan pesan:

"Data anggota tidak ditemukan."

Petugas dapat memeriksa kembali nomor anggota atau menuju proses pendaftaran anggota apabila diperlukan.

5. Buku Tidak Ditemukan

Kondisi:

Petugas mencari buku menggunakan judul, kode, atau informasi tertentu tetapi buku tidak tersedia di database.

Respons Sistem:

Sistem menampilkan pesan:

"Data buku tidak ditemukan."

Petugas dapat melakukan pencarian ulang atau menambahkan data buku apabila buku tersebut belum terdaftar.


### Kenapa kasus pertama penting?

Kalau sistem nggak melakukan pengecekan, bisa terjadi:

```text
Anggota: A001
Buku: Laskar Pelangi

Peminjaman #001 → aktif
Peminjaman #002 → aktif ❌

Padahal transaksi kedua sebenarnya duplikasi.

Jadi kita ingin sistem berpikir:

Request peminjaman
        ↓
Cek transaksi aktif
        ↓
Sudah ada kombinasi anggota + buku?
        ↓
      YA
        ↓
Tolak duplikasi





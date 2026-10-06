# Security Checklist - Jobsheet 11

Dokumen ini digunakan untuk mencatat hasil pemeriksaan keamanan
pada aplikasi SIMPUS-Mini.

## 1. SQL Injection

### Status

[ ] Belum diuji

### Pemeriksaan

Memastikan query database menggunakan prepared statement PDO
dan parameter binding.

### Catatan

Query pada aplikasi diperiksa untuk memastikan input pengguna
tidak digabungkan secara langsung ke dalam string SQL.

### Bukti Pengujian

Belum ada.

---

## 2. Cross-Site Scripting (XSS)

### Status

[ ] Belum diuji

### Pemeriksaan

Memastikan output yang berasal dari database, session, GET,
atau sumber input pengguna di-escape menggunakan helper `e()`.

### Implementasi

Aplikasi menggunakan fungsi:

```php
function e($value)
{
    return htmlspecialchars(
        (string) ($value ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}

Catatan
Fungsi e() digunakan sebagai helper untuk melakukan escaping
output HTML dan membantu mencegah script berbahaya dijalankan
oleh browser.
Bukti Pengujian
Belum ada.
3. Cross-Site Request Forgery (CSRF)
Status
[ ] Belum diuji
Pemeriksaan
Memastikan setiap form POST yang mengubah data memiliki CSRF
token dan setiap proses POST melakukan verifikasi token.
Implementasi
Aplikasi menggunakan:
- csrf_token()
- csrf_field()
- csrf_verify()
Catatan
CSRF token disimpan pada session dan dikirim melalui hidden input
pada form. Token kemudian diverifikasi ketika request POST
diterima.
Form pencarian menggunakan method GET dan tidak diberikan
CSRF token karena hanya digunakan untuk membaca data dan tidak
mengubah state aplikasi.
Bukti Pengujian
Belum ada.
4. Validasi dan Sanitasi Input
Status
[ ] Belum diuji
Pemeriksaan
Memastikan input pengguna divalidasi sebelum diproses dan
disimpan ke database.
Catatan
Validasi input dilakukan pada proses server-side. Selain itu,
output yang ditampilkan kembali ke browser harus melalui proses
escaping untuk mencegah XSS.
Bukti Pengujian
Belum ada.
5. Session Fixation
Status
[ ] Belum diuji
Pemeriksaan
Memastikan session ID dibuat ulang setelah autentikasi berhasil.
Implementasi
Aplikasi menggunakan:
session_regenerate_id(true);

setelah login berhasil.
Catatan
Regenerasi session ID digunakan untuk mencegah penggunaan session
ID lama setelah proses autentikasi.
Bukti Pengujian
Belum ada.
6. Kebocoran Pesan Error PHP dan Detail Server
Status
[ ] Perlu diperbaiki
Pemeriksaan
Memastikan pesan error PHP atau database yang ditampilkan kepada
pengguna tidak membocorkan informasi sensitif seperti struktur
database, nama tabel, konfigurasi server, atau detail koneksi.
Audit
Bagian	Sebelum	Sesudah
Penanganan error koneksi database	Pesan error database ditampilkan langsung kepada pengguna menggunakan $e->getMessage().	Pesan kepada pengguna menggunakan pesan umum tanpa menampilkan detail internal database atau server.
Informasi sensitif pada pesan error	Berpotensi membocorkan detail PostgreSQL dan struktur koneksi ketika terjadi error.	Detail error internal tidak ditampilkan kepada pengguna dan sebaiknya dicatat pada log server untuk kebutuhan debugging.
Pengalaman pengguna	Pengguna dapat melihat pesan error teknis ketika koneksi database gagal.	Pengguna menerima pesan error yang lebih aman dan mudah dipahami tanpa detail teknis internal.


Catatan
Pada audit ditemukan bahwa file includes/koneksi.php masih
menggunakan $e->getMessage() pada pesan die() ketika koneksi
database gagal.
Contoh kode yang ditemukan:
die(
    "Koneksi database gagal: " .
    $e->getMessage()
);

Penggunaan pesan error mentah seperti ini berpotensi memberikan
informasi internal kepada pengguna. Informasi tersebut dapat
membantu pihak yang tidak berwenang memahami konfigurasi atau
struktur sistem.
Perbaikan yang disarankan adalah menampilkan pesan umum kepada
pengguna, sedangkan detail error disimpan pada log server.
Bukti Pengujian
Belum ada.
Kesimpulan
Security checklist ini digunakan sebagai dokumentasi pemeriksaan
keamanan aplikasi SIMPUS-Mini pada Jobsheet 11.
Status setiap pemeriksaan akan diperbarui setelah implementasi
dan pengujian fitur keamanan selesai dilakukan.
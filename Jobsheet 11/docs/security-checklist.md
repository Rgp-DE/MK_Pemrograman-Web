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
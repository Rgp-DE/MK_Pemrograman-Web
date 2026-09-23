// ===== Hamburger menu (JS-driven) =====
function initNavToggle() {
    const toggleBtn = document.getElementById("nav-toggle-btn");
    const nav = document.querySelector("header nav");

    if (!toggleBtn || !nav) return;

    toggleBtn.addEventListener("click", function () {
        nav.classList.toggle("nav-open");
    });
}


// ===== Update counter di atas tabel =====
function updateTableCounter(table) {
    const counter = document.getElementById("table-counter");

    if (!counter || !table) return;

    const rows = table.querySelectorAll("tbody tr");

    const visibleRows = Array.from(rows).filter(function (row) {
        return row.style.display !== "none";
    });

    const heading = document.querySelector("h2");

    let jenisData = "buku";

    if (
        heading &&
        heading.textContent.trim() === "Daftar Anggota"
    ) {
        jenisData = "anggota";
    }

    counter.textContent =
        "Menampilkan " +
        visibleRows.length +
        " dari " +
        rows.length +
        " " +
        jenisData;
}


// ===== Konfirmasi hapus (front-end only) =====
function initHapusConfirm() {
    document.addEventListener("click", function (e) {

        const btn = e.target.closest(".btn-hapus");

        if (!btn) return;

        const row = btn.closest("tr");

        const nama = row
            ? row.querySelector("td")?.textContent.trim()
            : "data ini";

        const yakin = confirm(
            'Yakin ingin menghapus "' + nama + '"?'
        );

        if (yakin && row) {
            const table = row.closest("table");

            row.remove();

            updateTableCounter(table);
        }
    });
}


// ===== Filter / pencarian tabel =====
function initTableFilter() {
    const input = document.getElementById("search-input");
    const table = document.querySelector(
        ".table-responsive table"
    );

    if (!input || !table) return;

    input.addEventListener("keyup", function () {

        const keyword = input.value
            .toLowerCase()
            .trim();

        const rows = table.querySelectorAll("tbody tr");

        const heading = document.querySelector("h2");

        let kolomPencarian = 0;

        /*
         * Daftar Buku:
         * Judul berada di kolom pertama.
         */
        if (
            heading &&
            heading.textContent.trim() === "Daftar Buku"
        ) {
            kolomPencarian = 0;
        }

        /*
         * Daftar Anggota:
         * Nama berada di kolom kedua.
         */
        if (
            heading &&
            heading.textContent.trim() === "Daftar Anggota"
        ) {
            kolomPencarian = 1;
        }

        rows.forEach(function (row) {

            const cells = row.querySelectorAll("td");

            if (!cells[kolomPencarian]) return;

            const teksPencarian =
                cells[kolomPencarian]
                    .textContent
                    .toLowerCase();

            row.style.display =
                teksPencarian.includes(keyword)
                    ? ""
                    : "none";
        });

        updateTableCounter(table);
    });

    updateTableCounter(table);
}


// ===== Menampilkan error validasi =====
function tampilkanError(input, pesan) {
    hapusError(input);

    const span = document.createElement("span");

    span.className = "error";

    span.textContent = pesan;

    input.insertAdjacentElement(
        "afterend",
        span
    );
}


// ===== Menghapus error validasi =====
function hapusError(input) {
    const next = input.nextElementSibling;

    if (
        next &&
        next.classList.contains("error")
    ) {
        next.remove();
    }
}


// ===== Validasi form client-side =====
function initValidasiForm() {

    const form = document.getElementById("form-tambah");

    if (!form) return;

    form.addEventListener("submit", function (e) {

        let valid = true;


        // ===== Field wajib =====
        const fieldWajib = [
            "judul",
            "nama",
            "pengarang",
            "no_anggota",
            "email"
        ];

        fieldWajib.forEach(function (namaField) {

            const input = form.querySelector(
                "[name='" + namaField + "']"
            );

            if (!input) return;

            if (input.value.trim() === "") {

                tampilkanError(
                    input,
                    "Field ini wajib diisi."
                );

                valid = false;

            } else {

                hapusError(input);
            }
        });


        // ===== Validasi Tahun =====
        const tahun = form.querySelector(
            "[name='tahun']"
        );

        if (tahun) {

            const nilai = parseInt(
                tahun.value,
                10
            );

            if (
                isNaN(nilai) ||
                nilai < 1900 ||
                nilai > 2026
            ) {

                tampilkanError(
                    tahun,
                    "Tahun harus di antara 1900-2026."
                );

                valid = false;

            } else {

                hapusError(tahun);
            }
        }


        // ===== Validasi Stok =====
        const stok = form.querySelector(
            "[name='stok']"
        );

        if (stok) {

            const nilai = parseInt(
                stok.value,
                10
            );

            if (
                isNaN(nilai) ||
                nilai < 0
            ) {

                tampilkanError(
                    stok,
                    "Stok tidak boleh negatif."
                );

                valid = false;

            } else {

                hapusError(stok);
            }
        }


        // ===== Validasi ISBN =====
        const isbn = form.querySelector(
            "[name='isbn']"
        );

        if (isbn) {

            const nilai = isbn.value.trim();

            if (
                nilai !== "" &&
                !/^[0-9-]+$/.test(nilai)
            ) {

                tampilkanError(
                    isbn,
                    "ISBN hanya boleh berisi angka dan tanda hubung (-)."
                );

                valid = false;

            } else {

                hapusError(isbn);
            }
        }


        // ===== Validasi Email =====
        const email = form.querySelector(
            "[name='email']"
        );

        if (email) {

            const nilai = email.value.trim();

            if (
                nilai !== "" &&
                !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(nilai)
            ) {

                tampilkanError(
                    email,
                    "Format email tidak valid."
                );

                valid = false;

            } else {

                hapusError(email);
            }
        }


        // ===== Hentikan submit jika tidak valid =====
        if (!valid) {
            e.preventDefault();
        }
    });
}


// ===== Jalankan semua fungsi saat halaman siap =====
document.addEventListener(
    "DOMContentLoaded",
    function () {

        initNavToggle();

        initHapusConfirm();

        initTableFilter();

        initValidasiForm();
    }
);
document.addEventListener("DOMContentLoaded", function () {
    initNavToggle();
    initHapusConfirm();
    initTableFilter();
    initValidasiForm();
});

/* =========================
   NAVBAR HAMBURGER
========================= */

function initNavToggle() {
    const toggleBtn = document.getElementById("nav-toggle-btn");
    const nav = document.querySelector("header nav");

    if (!toggleBtn || !nav) return;

    toggleBtn.addEventListener("click", function () {
        nav.classList.toggle("nav-open");

        const isOpen = nav.classList.contains("nav-open");

        toggleBtn.setAttribute(
            "aria-label",
            isOpen ? "Tutup menu" : "Buka menu"
        );
    });
}


/* =========================
   HAPUS DATA - CONFIRM
========================= */

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

            if (typeof updateTableCounter === "function") {
                updateTableCounter(table);
            }
        }
    });
}


/* =========================
   FILTER TABEL
========================= */

function initTableFilter() {
    const input = document.getElementById("search-input");
    const table = document.querySelector("table");

    if (!input || !table) return;

    /*
     * Pada Jobsheet 8 buku/list.php,
     * pencarian sudah dilakukan di server
     * menggunakan PostgreSQL ILIKE.
     *
     * Karena itu JavaScript tidak perlu
     * melakukan filter lagi.
     */
    if (input.dataset.serverSearch === "true") {
        return;
    }

    input.addEventListener("input", function () {
        const keyword = input.value
            .trim()
            .toLowerCase();

        const rows = table.querySelectorAll(
            "tbody tr"
        );

        rows.forEach(function (row) {
            const judul = row.querySelector("td");

            if (!judul) return;

            const teksJudul =
                judul.textContent.toLowerCase();

            row.style.display =
                teksJudul.includes(keyword)
                    ? ""
                    : "none";
        });

        updateTableCounter(table);
    });
}


/* =========================
   COUNTER TABEL
========================= */

function updateTableCounter(table) {
    const counter =
        document.getElementById("table-counter");

    if (!counter || !table) return;

    const rows = table.querySelectorAll(
        "tbody tr"
    );

    const visibleRows =
        Array.from(rows).filter(function (row) {
            return row.style.display !== "none";
        });

    /*
     * Untuk server-side search, jumlah hasil
     * sudah ditentukan oleh PHP.
     *
     * JavaScript tidak mengubah counter.
     */
    const searchInput =
        document.getElementById("search-input");

    if (
        searchInput &&
        searchInput.dataset.serverSearch === "true"
    ) {
        return;
    }

    const totalRows = rows.length;

    counter.textContent =
        "Menampilkan " +
        visibleRows.length +
        " dari " +
        totalRows +
        " buku";
}


/* =========================
   VALIDASI FORM
========================= */

function initValidasiForm() {
    const form =
        document.querySelector("form");

    if (!form) return;

    form.addEventListener("submit", function (e) {
        let valid = true;

        /*
         * Field wajib
         */
        const fieldWajib = [
            "judul",
            "nama",
            "pengarang"
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


        /*
         * Validasi tahun
         */
        const tahun = form.querySelector(
            "[name='tahun']"
        );

        if (tahun) {
            const nilaiTahun =
                parseInt(tahun.value, 10);

            if (
                tahun.value.trim() !== "" &&
                (
                    isNaN(nilaiTahun) ||
                    nilaiTahun < 1900 ||
                    nilaiTahun > 2026
                )
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


        /*
         * Validasi stok
         */
        const stok = form.querySelector(
            "[name='stok']"
        );

        if (stok) {
            const nilaiStok =
                parseInt(stok.value, 10);

            if (
                stok.value.trim() !== "" &&
                (
                    isNaN(nilaiStok) ||
                    nilaiStok < 0
                )
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


        /*
         * Validasi ISBN
         */
        const isbn = form.querySelector(
            "[name='isbn']"
        );

        if (isbn) {
            const nilai =
                isbn.value.trim();

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


        /*
         * Jika validasi gagal,
         * cegah form dikirim.
         */
        if (!valid) {
            e.preventDefault();
        }
    });
}


/* =========================
   TAMPILKAN ERROR
========================= */

function tampilkanError(input, pesan) {
    hapusError(input);

    input.classList.add("input-error");

    const error = document.createElement("small");

    error.className = "error-message";

    error.textContent = pesan;

    input.insertAdjacentElement(
        "afterend",
        error
    );
}


/* =========================
   HAPUS ERROR
========================= */

function hapusError(input) {
    input.classList.remove(
        "input-error"
    );

    const error =
        input.nextElementSibling;

    if (
        error &&
        error.classList.contains(
            "error-message"
        )
    ) {
        error.remove();
    }
}
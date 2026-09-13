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

    counter.textContent =
        "Menampilkan " +
        visibleRows.length +
        " dari " +
        rows.length +
        " buku";
}

// ===== Konfirmasi hapus (front-end only, belum ke server) =====
function initHapusConfirm() {
    document.querySelectorAll(".btn-hapus").forEach(function (btn) {
        btn.addEventListener("click", function () {
            const row = btn.closest("tr");
            const nama = row
                ? row.querySelector("td")?.textContent
                : "data ini";

            const yakin = confirm(
                "Yakin ingin menghapus \"" + nama + "\"?"
            );

            if (yakin && row) {
                const table = row.closest("table");

                row.remove();

                updateTableCounter(table);
            }
        });
    });
}

// ===== Filter/pencarian tabel berdasarkan kolom Judul =====
function initTableFilter() {
    const input = document.getElementById("search-input");
    const table = document.querySelector(".table-responsive table");
    if (!input || !table) return;

    input.addEventListener("keyup", function () {
        const keyword = input.value.toLowerCase();
        const rows = table.querySelectorAll("tbody tr");

        rows.forEach(function (row) {
            const judul = row.querySelector("td");

            if (!judul) return;

            const teksJudul = judul.textContent.toLowerCase();

            row.style.display = teksJudul.includes(keyword) ? "" : "none";
        });

        updateTableCounter(table);
    });

    updateTableCounter(table);
}

// ===== Validasi form (client-side) =====
function tampilkanError(input, pesan) {
    hapusError(input);

    const span = document.createElement("span");
    span.className = "error";
    span.textContent = pesan;

    input.insertAdjacentElement("afterend", span);
}

function hapusError(input) {
    const next = input.nextElementSibling;

    if (next && next.classList.contains("error")) {
        next.remove();
    }
}

function initValidasiForm() {
    const form = document.getElementById("form-tambah");
    if (!form) return;

    form.addEventListener("submit", function (e) {
        let valid = true;

        // Daftar field yang wajib diisi
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

        // Validasi tahun terbit
        const tahun = form.querySelector("[name='tahun']");

        if (tahun) {
            const nilai = parseInt(tahun.value, 10);

            if (isNaN(nilai) || nilai < 1900 || nilai > 2026) {
                tampilkanError(
                    tahun,
                    "Tahun harus di antara 1900-2026."
                );
                valid = false;
            } else {
                hapusError(tahun);
            }
        }

        // Validasi stok
        const stok = form.querySelector("[name='stok']");

        if (stok) {
            const nilai = parseInt(stok.value, 10);

            if (isNaN(nilai) || nilai < 0) {
                tampilkanError(
                    stok,
                    "Stok tidak boleh negatif."
                );
                valid = false;
            } else {
                hapusError(stok);
            }
        }

        // Validasi ISBN
        const isbn = form.querySelector("[name='isbn']");

        if (isbn) {
            const nilai = isbn.value.trim();

            if (nilai !== "" && !/^[0-9-]+$/.test(nilai)) {
                tampilkanError(
                    isbn,
                    "ISBN hanya boleh berisi angka dan tanda hubung (-)."
                );
                valid = false;
            } else {
                hapusError(isbn);
            }
        }

        if (!valid) {
            e.preventDefault();
        }
    });
}

document.addEventListener("DOMContentLoaded", function () {
    initNavToggle();
    initHapusConfirm();
    initTableFilter();
    initValidasiForm();
});
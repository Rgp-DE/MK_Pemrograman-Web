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
    const toggleBtn =
        document.getElementById("nav-toggle-btn");

    const nav =
        document.querySelector("header nav");

    if (!toggleBtn || !nav) return;

    toggleBtn.addEventListener("click", function () {
        nav.classList.toggle("nav-open");

        const isOpen =
            nav.classList.contains("nav-open");

        toggleBtn.setAttribute(
            "aria-label",
            isOpen
                ? "Tutup menu"
                : "Buka menu"
        );
    });
}


/* =========================
   HAPUS DATA - CONFIRM
========================= */

function initHapusConfirm() {
    document.addEventListener(
        "submit",
        function (e) {

            const form =
                e.target.closest(".form-hapus");

            if (!form) return;

            const row =
                form.closest("tr");

            let nama = "data ini";

            if (row) {

                const firstCell =
                    row.querySelector("td");

                if (firstCell) {
                    nama =
                        firstCell.textContent.trim();
                }
            }

            const yakin = confirm(
                'Yakin ingin menghapus "' +
                nama +
                '"?'
            );

            if (!yakin) {

                e.preventDefault();
            }
        }
    );
}


/* =========================
   FILTER TABEL
========================= */

function initTableFilter() {

    const input =
        document.getElementById("search-input");

    const table =
        document.querySelector("table");

    if (!input || !table) return;

    /*
     * Jika pencarian dilakukan oleh server,
     * JavaScript tidak melakukan filter
     * terhadap baris tabel.
     */
    if (
        input.dataset.serverSearch === "true"
    ) {
        return;
    }

    input.addEventListener(
        "input",
        function () {

            const keyword =
                input.value
                    .trim()
                    .toLowerCase();

            const rows =
                table.querySelectorAll(
                    "tbody tr"
                );

            rows.forEach(function (row) {

                const firstCell =
                    row.querySelector("td");

                if (!firstCell) return;

                const teks =
                    firstCell.textContent
                        .toLowerCase();

                row.style.display =
                    teks.includes(keyword)
                        ? ""
                        : "none";
            });

            updateTableCounter(table);
        }
    );
}


/* =========================
   COUNTER TABEL
========================= */

function updateTableCounter(table) {

    const counter =
        document.getElementById(
            "table-counter"
        );

    if (!counter || !table) return;

    const searchInput =
        document.getElementById(
            "search-input"
        );

    /*
     * Pada server-side search,
     * counter sudah dihitung oleh PHP.
     */
    if (
        searchInput &&
        searchInput.dataset.serverSearch === "true"
    ) {
        return;
    }

    const rows =
        table.querySelectorAll(
            "tbody tr"
        );

    const visibleRows =
        Array.from(rows).filter(
            function (row) {
                return (
                    row.style.display !==
                    "none"
                );
            }
        );

    counter.textContent =
        "Menampilkan " +
        visibleRows.length +
        " dari " +
        rows.length +
        " data";
}


/* =========================
   VALIDASI FORM
========================= */

function initValidasiForm() {

    const forms =
        document.querySelectorAll(
            "form"
        );

    if (!forms.length) return;

    forms.forEach(function (form) {

        /*
         * Jangan pasang validasi pada
         * form pencarian dan form hapus.
         */
        if (
            form.classList.contains(
                "form-hapus"
            )
        ) {
            return;
        }

        if (
            form.querySelector(
                "[name='judul']"
            ) ||
            form.querySelector(
                "[name='nama']"
            ) ||
            form.querySelector(
                "[name='pengarang']"
            )
        ) {

            form.addEventListener(
                "submit",
                function (e) {

                    let valid = true;


                    /*
                     * Field wajib
                     */
                    const fieldWajib = [
                        "judul",
                        "nama",
                        "pengarang"
                    ];

                    fieldWajib.forEach(
                        function (namaField) {

                            const input =
                                form.querySelector(
                                    "[name='" +
                                    namaField +
                                    "']"
                                );

                            if (!input) return;

                            if (
                                input.value
                                    .trim() === ""
                            ) {

                                tampilkanError(
                                    input,
                                    "Field ini wajib diisi."
                                );

                                valid = false;

                            } else {

                                hapusError(
                                    input
                                );
                            }
                        }
                    );


                    /*
                     * Validasi tahun
                     */
                    const tahun =
                        form.querySelector(
                            "[name='tahun']"
                        );

                    if (tahun) {

                        const nilaiTahun =
                            parseInt(
                                tahun.value,
                                10
                            );

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
                    const stok =
                        form.querySelector(
                            "[name='stok']"
                        );

                    if (stok) {

                        const nilaiStok =
                            parseInt(
                                stok.value,
                                10
                            );

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
                    const isbn =
                        form.querySelector(
                            "[name='isbn']"
                        );

                    if (isbn) {

                        const nilai =
                            isbn.value.trim();

                        if (
                            nilai !== "" &&
                            !/^[0-9-]+$/.test(
                                nilai
                            )
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
                     * Validasi No. Anggota
                     */
                    const noAnggota =
                        form.querySelector(
                            "[name='no_anggota']"
                        );

                    if (noAnggota) {

                        const nilai =
                            noAnggota.value.trim();

                        if (
                            nilai !== "" &&
                            !/^[A-Za-z0-9-]+$/.test(
                                nilai
                            )
                        ) {

                            tampilkanError(
                                noAnggota,
                                "No. Anggota hanya boleh berisi huruf, angka, dan tanda hubung (-)."
                            );

                            valid = false;

                        } else {

                            hapusError(
                                noAnggota
                            );
                        }
                    }


                    /*
                     * Validasi Email
                     */
                    const email =
                        form.querySelector(
                            "[name='email']"
                        );

                    if (email) {

                        const nilai =
                            email.value.trim();

                        if (
                            nilai !== "" &&
                            !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(
                                nilai
                            )
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


                    /*
                     * Validasi No. HP
                     */
                    const noHp =
                        form.querySelector(
                            "[name='no_hp']"
                        );

                    if (noHp) {

                        const nilai =
                            noHp.value.trim();

                        if (
                            nilai !== "" &&
                            !/^[0-9+\-\s]+$/.test(
                                nilai
                            )
                        ) {

                            tampilkanError(
                                noHp,
                                "No. HP hanya boleh berisi angka, spasi, tanda plus (+), dan tanda hubung (-)."
                            );

                            valid = false;

                        } else {

                            hapusError(noHp);
                        }
                    }


                    /*
                     * Jika validasi gagal,
                     * cegah form dikirim.
                     */
                    if (!valid) {
                        e.preventDefault();
                    }
                }
            );
        }
    });
}


/* =========================
   TAMPILKAN ERROR
========================= */

function tampilkanError(
    input,
    pesan
) {

    hapusError(input);

    input.classList.add(
        "input-error"
    );

    const error =
        document.createElement(
            "small"
        );

    error.className =
        "error-message";

    error.textContent =
        pesan;

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
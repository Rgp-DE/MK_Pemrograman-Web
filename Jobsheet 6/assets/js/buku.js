// Mengambil & menampilkan Daftar Buku secara asinkron dari data/buku.json
async function muatDaftarBuku() {
    const tbody = document.querySelector(".table-responsive table tbody");
    const loading = document.getElementById("loading-indicator");

    if (!tbody) return;

    if (loading) {
        loading.style.display = "block";
    }

    tbody.innerHTML = "";

    try {
        // simulasi delay jaringan agar loading indicator terlihat
        await new Promise((resolve) => setTimeout(resolve, 600));

        const res = await fetch("../data/buku.json");

        if (!res.ok) {
            throw new Error(
                "Gagal mengambil data (status " + res.status + ")"
            );
        }

        const daftarBuku = await res.json();

        daftarBuku.forEach(function (buku) {
            const tr = document.createElement("tr");

            tr.innerHTML =
                "<td>" + buku.judul + "</td>" +
                "<td>" + buku.pengarang + "</td>" +
                "<td>" + buku.tahun + "</td>" +
                "<td>" + buku.stok + "</td>" +
                "<td>" +
                "<button type=\"button\" class=\"btn-edit\">Edit</button> " +
                "<button type=\"button\" class=\"btn-detail\">Detail</button> " +
                "<button type=\"button\" class=\"btn-hapus\">Hapus</button>" +
                "</td>";

            tbody.appendChild(tr);
        });

        updateTableCounter(
            tbody.closest("table")
        );

    } catch (err) {
        tbody.innerHTML =
            "<tr><td colspan=\"5\">Gagal memuat data: " +
            err.message +
            "</td></tr>";

        updateTableCounter(
            tbody.closest("table")
        );

    } finally {
        if (loading) {
            loading.style.display = "none";
        }
    }
}


// Memperbarui jumlah data buku yang sedang ditampilkan
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


// Filter daftar buku berdasarkan judul
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

            row.style.display =
                teksJudul.includes(keyword)
                    ? ""
                    : "none";
        });

        updateTableCounter(table);
    });

    updateTableCounter(table);
}


document.addEventListener("DOMContentLoaded", function () {
    muatDaftarBuku();
    initTableFilter();
});
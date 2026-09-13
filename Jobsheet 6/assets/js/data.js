// Fungsi generik untuk mengambil dan menampilkan data dari file JSON
async function muatDataJSON(namaFile, daftarKunci) {
    const tbody = document.querySelector(".table-responsive table tbody");
    const loading = document.getElementById("loading-indicator");

    if (!tbody) return;

    if (loading) {
        loading.style.display = "block";
    }

    tbody.innerHTML = "";

    try {
        // Simulasi delay jaringan agar loading indicator terlihat
        await new Promise((resolve) => setTimeout(resolve, 3000));

        const res = await fetch("../data/" + namaFile);

        if (!res.ok) {
            throw new Error(
                "Gagal mengambil data (status " + res.status + ")"
            );
        }

        const daftarData = await res.json();

        daftarData.forEach(function (data) {
            const tr = document.createElement("tr");

            let isiKolom = "";

            daftarKunci.forEach(function (kunci) {
                isiKolom += "<td>" + data[kunci] + "</td>";
            });

            // Tombol aksi
            if (namaFile === "buku.json") {
                isiKolom +=
                    "<td>" +
                    "<button type=\"button\" class=\"btn-edit\">Edit</button> " +
                    "<button type=\"button\" class=\"btn-detail\">Detail</button> " +
                    "<button type=\"button\" class=\"btn-hapus\">Hapus</button>" +
                    "</td>";
            } else {
                isiKolom +=
                    "<td>" +
                    "<button type=\"button\" class=\"btn-edit\">Edit</button> " +
                    "<button type=\"button\" class=\"btn-hapus\">Hapus</button>" +
                    "</td>";
            }

            tr.innerHTML = isiKolom;

            tbody.appendChild(tr);
        });

        // Update counter khusus halaman Daftar Buku
        if (typeof updateTableCounter === "function") {
            updateTableCounter(tbody.closest("table"));
        }

    } catch (err) {
        tbody.innerHTML =
            "<tr><td colspan=\"6\">Gagal memuat data: " +
            err.message +
            "</td></tr>";

        if (typeof updateTableCounter === "function") {
            updateTableCounter(tbody.closest("table"));
        }

    } finally {
        if (loading) {
            loading.style.display = "none";
        }
    }
}


// Memuat data buku
function muatDaftarBuku() {
    return muatDataJSON(
        "buku.json",
        ["judul", "pengarang", "tahun", "stok", "kategori"]
    );
}


// Memuat data anggota
function muatDaftarAnggota() {
    return muatDataJSON(
        "anggota.json",
        ["no_anggota", "nama", "alamat", "no_hp"]
    );
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


// Tombol Muat Ulang
function initMuatUlang() {
    const button = document.getElementById("btn-muat-ulang");

    if (!button) return;

    button.addEventListener("click", function () {
        muatDaftarBuku();
    });
}


// Inisialisasi berdasarkan halaman
document.addEventListener("DOMContentLoaded", function () {

    if (document.getElementById("btn-muat-ulang")) {
        muatDaftarBuku();
        initTableFilter();
        initMuatUlang();
    }

    if (document.querySelector("h2")?.textContent === "Daftar Anggota") {
        muatDaftarAnggota();
    }

});
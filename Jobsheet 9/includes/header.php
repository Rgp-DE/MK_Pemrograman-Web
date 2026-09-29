<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($page_title)) {
    $page_title = "SIMPUS-Mini";
}

$currentDirectory = basename(dirname($_SERVER['PHP_SELF']));

if (
    $currentDirectory === "buku" ||
    $currentDirectory === "anggota"
) {
    $base = "../";
} else {
    $base = "";
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>SIMPUS-Mini | <?php echo $page_title; ?></title>

    <link
        rel="stylesheet"
        href="<?php echo $base; ?>assets/css/style.css">
</head>

<body>

    <header>
        <h1>SIMPUS-Mini</h1>

        <button
            type="button"
            id="nav-toggle-btn"
            class="nav-toggle-label"
            aria-label="Buka menu">
            &#9776;
        </button>

        <nav>
            <ul>
                <li>
                    <a href="<?php echo $base; ?>index.php">
                        Beranda
                    </a>
                </li>

                <li>
                    <a href="<?php echo $base; ?>buku/list.php">
                        Daftar Buku
                    </a>
                </li>

                <li>
                    <a href="<?php echo $base; ?>buku/tambah.php">
                        Tambah Buku
                    </a>
                </li>

                <li>
                    <a href="<?php echo $base; ?>anggota/list.php">
                        Daftar Anggota
                    </a>
                </li>

                <li>
                    <a href="<?php echo $base; ?>anggota/tambah.php">
                        Tambah Anggota
                    </a>
                </li>

                <li>
                    <a href="<?php echo $base; ?>login.php">
                        Login
                    </a>
                </li>
            </ul>
        </nav>
    </header>

    <main>
<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/csrf.php';

if (!isset($page_title)) {
    $page_title = "SIMPUS-Mini";
}

$rootPath = dirname(__DIR__);

$scriptDirectory = dirname(
    $_SERVER['SCRIPT_FILENAME']
);

$relativeDirectory = str_replace(
    '\\',
    '/',
    substr(
        $scriptDirectory,
        strlen($rootPath)
    )
);

$relativeDirectory = trim(
    $relativeDirectory,
    '/'
);

if ($relativeDirectory === '') {
    $base = '';
} else {
    $jumlahFolder = substr_count(
        $relativeDirectory,
        '/'
    ) + 1;

    $base = str_repeat(
        '../',
        $jumlahFolder
    );
}

$sudahLogin = isset(
    $_SESSION['user_id']
);

$namaUser = $_SESSION['nama'] ?? '';
$roleUser = $_SESSION['role'] ?? '';

?>
<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>
        SIMPUS-Mini |
        <?php echo e($page_title); ?>
    </title>

    <link
        rel="stylesheet"
        href="<?php echo e($base); ?>assets/css/style.css">

</head>

<body>

<header>

    <h1>
        SIMPUS-Mini
    </h1>

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

                <a
                    href="<?php echo e($base); ?>index.php">

                    Beranda

                </a>

            </li>

            <li>

                <a
                    href="<?php echo e($base); ?>buku/list.php">

                    Daftar Buku

                </a>

            </li>

            <?php if ($sudahLogin): ?>

                <li>

                    <a
                        href="<?php echo e($base); ?>buku/tambah.php">

                        Tambah Buku

                    </a>

                </li>

                <li>

                    <a
                        href="<?php echo e($base); ?>anggota/list.php">

                        Daftar Anggota

                    </a>

                </li>

                <li>

                    <a
                        href="<?php echo e($base); ?>anggota/tambah.php">

                        Tambah Anggota

                    </a>

                </li>

            <?php endif; ?>

        </ul>

    </nav>

    <div class="auth-status">

        <?php if ($sudahLogin): ?>

            <span>

                <?php echo e($namaUser); ?>

                <?php if ($roleUser !== ''): ?>

                    (
                    <?php echo e($roleUser); ?>
                    )

                <?php endif; ?>

            </span>

            <a
                href="<?php echo e($base); ?>auth/logout.php">

                Logout

            </a>

        <?php else: ?>

            <a
                href="<?php echo e($base); ?>auth/login.php">

                Login

            </a>

        <?php endif; ?>

    </div>

</header>

<main>
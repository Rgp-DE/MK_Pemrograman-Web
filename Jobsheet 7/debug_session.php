<?php

session_start();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Debug Session | SIMPUS-Mini</title>

    <link
        rel="stylesheet"
        href="assets/css/style.css">
</head>

<body>

    <main>
        <section>
            <h2>Debug Session</h2>

            <p>
                Halaman ini digunakan untuk melihat isi
                <code>$_SESSION</code> secara langsung.
            </p>

            <pre><?php print_r($_SESSION); ?></pre>
        </section>
    </main>

</body>

</html>
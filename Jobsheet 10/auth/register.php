<?php

session_start();

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$page_title = "Register";
$base = '../';

include __DIR__ . '/../includes/header.php';

?>

<section class="form-section">

    <h2>Register</h2>

    <p>
        Silakan buat akun untuk mengakses fitur pengelolaan data.
    </p>

    <?php if ($flash): ?>

        <div class="alert <?php echo htmlspecialchars($flash['type']); ?>">
            <?php echo htmlspecialchars($flash['pesan']); ?>
        </div>

    <?php endif; ?>

    <form
        action="proses_register.php"
        method="post"
        id="form-register">

        <div class="form-group">

            <label for="nama">
                Nama
            </label>

            <input
                type="text"
                id="nama"
                name="nama"
                required
                autocomplete="name">

        </div>

        <div class="form-group">

            <label for="username">
                Username
            </label>

            <input
                type="text"
                id="username"
                name="username"
                required
                autocomplete="username">

        </div>

        <div class="form-group">

            <label for="password">
                Password
            </label>

            <input
                type="password"
                id="password"
                name="password"
                required
                minlength="6"
                autocomplete="new-password">

            <small>
                Password minimal 6 karakter.
            </small>

        </div>

        <div class="form-actions">

            <button
                type="submit"
                class="btn-edit">
                Daftar
            </button>

            <a
                href="login.php"
                class="btn-detail">
                Sudah punya akun?
            </a>

        </div>

    </form>

</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
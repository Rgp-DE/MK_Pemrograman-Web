<?php

session_start();

$flash = $_SESSION['flash'] ?? null;

unset(
    $_SESSION['flash']
);

$page_title = "Login";

include __DIR__ . '/../includes/header.php';

?>

<section class="form-section">

    <h2>
        Login
    </h2>

    <p>
        Silakan login untuk mengakses fitur
        pengelolaan data.
    </p>

    <?php if ($flash): ?>

        <div
            class="alert <?php echo e($flash['type']); ?>">

            <?php echo e($flash['pesan']); ?>

        </div>

    <?php endif; ?>

    <form
        action="proses_login.php"
        method="post"
        id="form-login">

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
                autocomplete="current-password">

        </div>

        <div class="remember-me">

            <input
                type="checkbox"
                id="remember_me"
                name="remember_me"
                value="1">

            <label for="remember_me">
                Ingat Saya
            </label>

        </div>

        <div class="form-actions">

            <button
                type="submit"
                class="btn-edit">

                Login

            </button>

            <a
                href="register.php"
                class="btn-detail">

                Belum punya akun?

            </a>

        </div>

    </form>

</section>

<?php

include __DIR__ . '/../includes/footer.php';

?>
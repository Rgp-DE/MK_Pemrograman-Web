<?php
$page_title = "Login";

include __DIR__ . '/includes/header.php';
?>

<main class="login-main">

    <section class="login-section">

        <h2>Login</h2>

        <p class="login-description">
            Silakan masuk menggunakan akun SIMPUS-Mini.
        </p>

        <form class="login-form">

            <p>
                <label for="email">Email</label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="Masukkan email"
                    required>
            </p>

            <p>
                <label for="password">Password</label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Masukkan password"
                    required>
            </p>

            <p>
                <button type="submit">
                    Masuk
                </button>
            </p>

        </form>

        <p class="login-register">
            Belum memiliki akun?
            <a href="wireframe/registrasi-anggota.txt">
                Registrasi Anggota Baru
            </a>
        </p>

    </section>

</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
    </main>


    <footer>

        <p>
            &copy; 2026 SIMPUS-Mini &mdash; Jobsheet 11
        </p>

    </footer>


    <script
        src="<?php echo $base; ?>assets/js/app.js">
    </script>


    <?php if (!empty($extra_scripts)): ?>

        <?php foreach ($extra_scripts as $src): ?>

            <script
                src="<?php echo htmlspecialchars($src); ?>">
            </script>

        <?php endforeach; ?>

    <?php endif; ?>


</body>

</html>
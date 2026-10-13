<?php

function csrf_token()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (
        empty($_SESSION['csrf_token'])
    ) {
        $_SESSION['csrf_token'] =
            bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function csrf_field()
{
    return
        '<input type="hidden" name="csrf_token" value="' .
        e(csrf_token()) .
        '">';
}

function csrf_verify()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $token =
        $_POST['csrf_token'] ?? '';

    $sessionToken =
        $_SESSION['csrf_token'] ?? '';

    if (
        $token === '' ||
        $sessionToken === '' ||
        !hash_equals(
            $sessionToken,
            $token
        )
    ) {
        http_response_code(403);

        die(
            'Permintaan tidak valid. ' .
            'CSRF token tidak cocok.'
        );
    }
}
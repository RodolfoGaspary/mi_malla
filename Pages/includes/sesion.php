<?php
// Arranca la sesión una sola vez y exige usuario autenticado.
// Incluir al inicio de TODA página o endpoint que no sea público.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['username'])) {
    // Endpoints AJAX responden 401 en vez de redirigir a HTML.
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])
        && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        http_response_code(401);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['error' => 'No autenticado']);
        exit();
    }

    header('Location: login.php');
    exit();
}

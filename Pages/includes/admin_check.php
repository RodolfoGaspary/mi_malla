<?php

function isAdmin(): bool
{
    return isset($_SESSION['username'], $_SESSION['role'])
        && $_SESSION['role'] === 'admin';
}

/** Corta la ejecución si el usuario no es administrador. */
function requireAdmin(): void
{
    if (!isAdmin()) {
        header('Location: dashboard.php');
        exit();
    }
}

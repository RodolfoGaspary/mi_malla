<?php

function isAdmin() {
    // Check if the user is logged in and their role is "admin"
    if (isset($_SESSION['username']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        return true; // User is an admin
    }
    return false; // User is not an admin
}
?>

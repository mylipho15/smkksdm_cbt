<?php
/**
 * Entry Point Aplikasi Exam CBT
 */

require_once __DIR__ . '/config/config.php';

// Redirect ke login jika belum login, atau ke dashboard sesuai role
if (isLoggedIn()) {
    $role = $_SESSION['user_role'];
    switch ($role) {
        case 'admin':
            redirect(BASE_URL . '/admin/dashboard.php');
            break;
        case 'guru':
            redirect(BASE_URL . '/guru/dashboard.php');
            break;
        case 'siswa':
            redirect(BASE_URL . '/siswa/dashboard.php');
            break;
    }
} else {
    redirect(BASE_URL . '/login.php');
}
?>

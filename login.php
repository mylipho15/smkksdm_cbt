<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/models/Auth.php';

// Jika sudah login, redirect sesuai role
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
}

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Username dan password harus diisi';
    } else {
        $auth = new Auth();
        $result = $auth->login($username, $password);
        
        if ($result['success']) {
            // Redirect berdasarkan role
            switch ($result['role']) {
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
            $error = $result['message'];
        }
    }
}

// Tampilkan halaman login
$pageTitle = 'Login';
ob_start();
include __DIR__ . '/views/login.php';
$content = ob_get_clean();

include __DIR__ . '/views/layouts/main.php';
?>

<?php
require_once __DIR__ . '/../../config/config.php';

// Cek apakah user sudah login dan merupakan guru
if (!isLoggedIn() || !hasRole('guru')) {
    redirect(BASE_URL . '/login.php');
}

$auth = new Auth();
$user = $auth->user();

// Handle logout
if (isset($_GET['logout'])) {
    $auth->logout();
    redirect(BASE_URL . '/login.php');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle : 'Dashboard' ?> - Guru Exam CBT</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f6fa; }
        
        .navbar {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .navbar h1 { font-size: 22px; }
        
        .navbar-right { display: flex; align-items: center; gap: 20px; }
        
        .user-info { color: white; opacity: 0.9; }
        
        .btn-logout {
            background: rgba(255,255,255,0.2);
            color: white;
            padding: 8px 16px;
            border-radius: 5px;
            text-decoration: none;
            transition: background 0.3s;
        }
        
        .btn-logout:hover { background: rgba(255,255,255,0.3); }
        
        .container { max-width: 1200px; margin: 30px auto; padding: 0 20px; }
        
        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            transition: transform 0.2s;
        }
        
        .card:hover { transform: translateY(-5px); }
        
        .card-icon { font-size: 40px; margin-bottom: 15px; }
        .card-title { font-size: 16px; color: #666; margin-bottom: 10px; }
        .card-value { font-size: 32px; font-weight: bold; color: #333; }
        
        .card-blue { border-left: 4px solid #667eea; }
        .card-green { border-left: 4px solid #28a745; }
        .card-orange { border-left: 4px solid #fd7e14; }
        .card-red { border-left: 4px solid #dc3545; }
        
        .content-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            padding: 25px;
        }
        
        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .btn {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
            cursor: pointer;
            border: none;
        }
        
        .btn-primary { background: #667eea; color: white; }
        .btn-primary:hover { background: #5568d3; }
        
        .btn-success { background: #28a745; color: white; }
        .btn-success:hover { background: #218838; }
        
        .btn-warning { background: #ffc107; color: #333; }
        .btn-warning:hover { background: #e0a800; }
        
        .btn-danger { background: #dc3545; color: white; }
        .btn-danger:hover { background: #c82333; }
        
        table { width: 100%; border-collapse: collapse; }
        
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        th { background: #f8f9fa; font-weight: 600; color: #555; }
        
        tr:hover { background: #f8f9fa; }
        
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .badge-pg { background: #e3f2fd; color: #1976d2; }
        .badge-essay { background: #fff3e0; color: #f57c00; }
        
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 500; color: #333; }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        .form-group textarea { resize: vertical; min-height: 100px; }
        
        .alert {
            padding: 12px 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        
        .nav-menu {
            background: white;
            padding: 15px 25px;
            margin-bottom: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }
        
        .nav-menu a {
            display: inline-block;
            margin-right: 20px;
            color: #555;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 5px;
            transition: all 0.3s;
        }
        
        .nav-menu a:hover, .nav-menu a.active {
            background: #28a745;
            color: white;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <h1>📚 Dashboard Guru</h1>
        <div class="navbar-right">
            <div class="user-info">
                Halo, <?= htmlspecialchars($user['nama_lengkap']) ?>
            </div>
            <a href="?logout=1" class="btn-logout">Logout</a>
        </div>
    </nav>
    
    <div class="container">
        <?= $content ?>
    </div>
</body>
</html>

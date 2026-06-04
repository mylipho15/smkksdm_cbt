<?php
require_once __DIR__ . '/../config/config.php';

$pageTitle = 'Dashboard Admin';
ob_start();

$db = getDBConnection();

// Get statistics
$stmt = $db->query("SELECT COUNT(*) as total FROM users WHERE role = 'admin'");
$totalAdmin = $stmt->fetch()['total'];

$stmt = $db->query("SELECT COUNT(*) as total FROM users WHERE role = 'guru'");
$totalGuru = $stmt->fetch()['total'];

$stmt = $db->query("SELECT COUNT(*) as total FROM users WHERE role = 'siswa'");
$totalSiswa = $stmt->fetch()['total'];

$stmt = $db->query("SELECT COUNT(*) as total FROM ujian");
$totalUjian = $stmt->fetch()['total'];

$stmt = $db->query("SELECT COUNT(*) as total FROM bank_soal");
$totalSoal = $stmt->fetch()['total'];

$stmt = $db->query("SELECT COUNT(*) as total FROM mata_pelajaran");
$totalMapel = $stmt->fetch()['total'];
?>

<div class="nav-menu">
    <a href="dashboard.php" class="active">📊 Dashboard</a>
    <a href="users.php">👥 Manajemen User</a>
    <a href="mapel.php">📚 Mata Pelajaran</a>
    <a href="ujian-all.php">🎯 Semua Ujian</a>
    <a href="soal-all.php">📝 Bank Soal</a>
</div>

<div class="dashboard-cards">
    <div class="card card-blue">
        <div class="card-icon">👤</div>
        <div class="card-title">Total Admin</div>
        <div class="card-value"><?= $totalAdmin ?></div>
    </div>
    
    <div class="card card-green">
        <div class="card-icon">👨‍🏫</div>
        <div class="card-title">Total Guru</div>
        <div class="card-value"><?= $totalGuru ?></div>
    </div>
    
    <div class="card card-orange">
        <div class="card-icon">👨‍🎓</div>
        <div class="card-title">Total Siswa</div>
        <div class="card-value"><?= $totalSiswa ?></div>
    </div>
    
    <div class="card card-red">
        <div class="card-icon">🎯</div>
        <div class="card-title">Total Ujian</div>
        <div class="card-value"><?= $totalUjian ?></div>
    </div>
    
    <div class="card card-blue">
        <div class="card-icon">📝</div>
        <div class="card-title">Total Soal</div>
        <div class="card-value"><?= $totalSoal ?></div>
    </div>
    
    <div class="card card-green">
        <div class="card-icon">📚</div>
        <div class="card-title">Mata Pelajaran</div>
        <div class="card-value"><?= $totalMapel ?></div>
    </div>
</div>

<div class="content-card">
    <div class="content-header">
        <h2>👥 User Terbaru</h2>
        <a href="users.php" class="btn btn-primary">Lihat Semua</a>
    </div>
    
    <?php
    $stmt = $db->query("SELECT * FROM users ORDER BY created_at DESC LIMIT 5");
    $userList = $stmt->fetchAll();
    ?>
    
    <table>
        <thead>
            <tr>
                <th>Username</th>
                <th>Nama Lengkap</th>
                <th>Role</th>
                <th>Kelas</th>
                <th>Terdaftar</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($userList as $u): ?>
            <tr>
                <td><?= htmlspecialchars($u['username']) ?></td>
                <td><?= htmlspecialchars($u['nama_lengkap']) ?></td>
                <td><span class="badge role-<?= $u['role'] ?>"><?= ucfirst($u['role']) ?></span></td>
                <td><?= htmlspecialchars($u['kelas'] ?? '-') ?></td>
                <td><?= date('d/m/Y', strtotime($u['created_at'])) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../views/layouts/admin.php';
?>

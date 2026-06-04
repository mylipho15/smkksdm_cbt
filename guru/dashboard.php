<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Soal.php';

// Include layout (layout akan handle auth check)
$pageTitle = 'Dashboard';

ob_start();

$soalModel = new Soal();
$db = getDBConnection();

// Get statistics
$userId = $_SESSION['user_id'];

$stmt = $db->prepare("SELECT COUNT(*) as total FROM bank_soal WHERE guru_id = ?");
$stmt->execute([$userId]);
$totalSoal = $stmt->fetch()['total'];

$stmt = $db->prepare("SELECT COUNT(*) as total FROM ujian WHERE guru_id = ?");
$stmt->execute([$userId]);
$totalUjian = $stmt->fetch()['total'];

$stmt = $db->prepare("SELECT COUNT(DISTINCT siswa_id) as total FROM ujian_peserta up JOIN ujian u ON up.ujian_id = u.id WHERE u.guru_id = ?");
$stmt->execute([$userId]);
$totalSiswa = $stmt->fetch()['total'];

$stmt = $db->prepare("SELECT COUNT(*) as total FROM mata_pelajaran WHERE guru_id = ?");
$stmt->execute([$userId]);
$totalMapel = $stmt->fetch()['total'];
?>

<div class="nav-menu">
    <a href="dashboard.php" class="active">📊 Dashboard</a>
    <a href="soal.php">📝 Bank Soal</a>
    <a href="import-soal.php">📥 Import Soal</a>
    <a href="ujian.php">🎯 Ujian</a>
    <a href="nilai.php">📈 Nilai Siswa</a>
</div>

<div class="dashboard-cards">
    <div class="card card-blue">
        <div class="card-icon">📝</div>
        <div class="card-title">Total Soal</div>
        <div class="card-value"><?= $totalSoal ?></div>
    </div>
    
    <div class="card card-green">
        <div class="card-icon">🎯</div>
        <div class="card-title">Total Ujian</div>
        <div class="card-value"><?= $totalUjian ?></div>
    </div>
    
    <div class="card card-orange">
        <div class="card-icon">👨‍🎓</div>
        <div class="card-title">Siswa Terdaftar</div>
        <div class="card-value"><?= $totalSiswa ?></div>
    </div>
    
    <div class="card card-red">
        <div class="card-icon">📚</div>
        <div class="card-title">Mata Pelajaran</div>
        <div class="card-value"><?= $totalMapel ?></div>
    </div>
</div>

<div class="content-card">
    <div class="content-header">
        <h2>📋 Ujian Terbaru</h2>
        <a href="ujian.php" class="btn btn-primary">Lihat Semua</a>
    </div>
    
    <?php
    $stmt = $db->prepare("SELECT u.*, m.nama_mapel 
                          FROM ujian u 
                          JOIN mata_pelajaran m ON u.mapel_id = m.id 
                          WHERE u.guru_id = ? 
                          ORDER BY u.created_at DESC LIMIT 5");
    $stmt->execute([$userId]);
    $ujianList = $stmt->fetchAll();
    
    if (count($ujianList) > 0):
    ?>
    <table>
        <thead>
            <tr>
                <th>Kode Ujian</th>
                <th>Judul</th>
                <th>Mapel</th>
                <th>Tanggal</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($ujianList as $ujian): ?>
            <tr>
                <td><?= htmlspecialchars($ujian['kode_ujian']) ?></td>
                <td><?= htmlspecialchars($ujian['judul_ujian']) ?></td>
                <td><?= htmlspecialchars($ujian['nama_mapel']) ?></td>
                <td><?= date('d/m/Y H:i', strtotime($ujian['tanggal_mulai'])) ?></td>
                <td>
                    <?php
                    $statusClass = '';
                    $statusText = '';
                    switch($ujian['status']) {
                        case 'draft': $statusClass = 'badge-essay'; $statusText = 'Draft'; break;
                        case 'aktif': $statusClass = 'badge-pg'; $statusText = 'Aktif'; break;
                        case 'selesai': $statusClass = 'badge-essay'; $statusText = 'Selesai'; break;
                    }
                    ?>
                    <span class="badge <?= $statusClass ?>"><?= $statusText ?></span>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <p style="color: #999; text-align: center; padding: 30px;">Belum ada ujian</p>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../views/layouts/guru.php';
?>

<?php
require_once __DIR__ . '/../../config/config.php';

$pageTitle = 'Dashboard Siswa';
ob_start();

$db = getDBConnection();
$userId = $_SESSION['user_id'];

// Get statistics
$stmt = $db->prepare("SELECT COUNT(*) as total FROM ujian_peserta WHERE siswa_id = ?");
$stmt->execute([$userId]);
$totalUjianDiikuti = $stmt->fetch()['total'];

$stmt = $db->prepare("SELECT COUNT(*) as total FROM ujian_peserta WHERE siswa_id = ? AND status = 'selesai'");
$stmt->execute([$userId]);
$totalSelesai = $stmt->fetch()['total'];

$stmt = $db->prepare("SELECT AVG(nilai) as avg FROM ujian_peserta WHERE siswa_id = ? AND nilai IS NOT NULL");
$stmt->execute([$userId]);
$rataRata = $stmt->fetch()['avg'] ?? 0;
?>

<div class="nav-menu">
    <a href="dashboard.php" class="active">📊 Dashboard</a>
    <a href="ujian-saya.php">🎯 Ujian Saya</a>
    <a href="kerjakan-ujian.php">✏️ Kerjakan Ujian</a>
    <a href="nilai-saya.php">📈 Nilai Saya</a>
</div>

<div class="dashboard-cards">
    <div class="card card-blue">
        <div class="card-icon">🎯</div>
        <div class="card-title">Total Ujian Diikuti</div>
        <div class="card-value"><?= $totalUjianDiikuti ?></div>
    </div>
    
    <div class="card card-green">
        <div class="card-icon">✅</div>
        <div class="card-title">Ujian Selesai</div>
        <div class="card-value"><?= $totalSelesai ?></div>
    </div>
    
    <div class="card card-orange">
        <div class="card-icon">📊</div>
        <div class="card-title">Rata-rata Nilai</div>
        <div class="card-value"><?= number_format($rataRata, 1) ?></div>
    </div>
</div>

<div class="content-card">
    <div class="content-header">
        <h2>📋 Ujian yang Tersedia</h2>
        <a href="kerjakan-ujian.php" class="btn btn-primary">Lihat Semua Ujian</a>
    </div>
    
    <?php
    $now = date('Y-m-d H:i:s');
    $stmt = $db->prepare("
        SELECT u.*, m.nama_mapel, up.status as peserta_status
        FROM ujian u
        JOIN mata_pelajaran m ON u.mapel_id = m.id
        LEFT JOIN ujian_peserta up ON u.id = up.ujian_id AND up.siswa_id = ?
        WHERE u.status = 'aktif' 
          AND u.tanggal_mulai <= ?
          AND u.tanggal_selesai >= ?
        ORDER BY u.tanggal_selesai ASC
        LIMIT 5
    ");
    $stmt->execute([$userId, $now, $now]);
    $ujianList = $stmt->fetchAll();
    
    if (count($ujianList) > 0):
    ?>
    <table>
        <thead>
            <tr>
                <th>Judul Ujian</th>
                <th>Mapel</th>
                <th>Tanggal Mulai</th>
                <th>Tanggal Selesai</th>
                <th>Durasi</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($ujianList as $ujian): ?>
            <tr>
                <td><?= htmlspecialchars($ujian['judul_ujian']) ?></td>
                <td><?= htmlspecialchars($ujian['nama_mapel']) ?></td>
                <td><?= date('d/m/Y H:i', strtotime($ujian['tanggal_mulai'])) ?></td>
                <td><?= date('d/m/Y H:i', strtotime($ujian['tanggal_selesai'])) ?></td>
                <td><?= $ujian['durasi_menit'] ?> menit</td>
                <td>
                    <?php
                    $statusClass = 'badge-belum';
                    $statusText = 'Belum Mulai';
                    if ($ujian['peserta_status'] == 'sedang') {
                        $statusClass = 'badge-sedang';
                        $statusText = 'Sedang Dikerjakan';
                    } elseif ($ujian['peserta_status'] == 'selesai') {
                        $statusClass = 'badge-selesai';
                        $statusText = 'Selesai';
                    }
                    ?>
                    <span class="badge <?= $statusClass ?>"><?= $statusText ?></span>
                </td>
                <td>
                    <?php if ($ujian['peserta_status'] == 'sedang'): ?>
                    <a href="lanjut-ujian.php?id=<?= $ujian['id'] ?>" class="btn btn-warning" style="padding: 5px 10px; font-size: 12px;">Lanjutkan</a>
                    <?php elseif ($ujian['peserta_status'] != 'selesai'): ?>
                    <a href="mulai-ujian.php?id=<?= $ujian['id'] ?>" class="btn btn-success" style="padding: 5px 10px; font-size: 12px;">Mulai</a>
                    <?php else: ?>
                    <span style="color: #999; font-size: 13px;">Selesai</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <p style="color: #999; text-align: center; padding: 30px;">Tidak ada ujian yang tersedia saat ini.</p>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../views/layouts/siswa.php';
?>

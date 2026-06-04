<?php
require_once __DIR__ . '/../../config/config.php';

$pageTitle = 'Nilai Saya';
ob_start();

$db = getDBConnection();
$userId = $_SESSION['user_id'];

// Get semua nilai ujian siswa
$stmt = $db->prepare("
    SELECT u.*, m.nama_mapel, up.nilai, up.status, up.selesai_mengerjakan
    FROM ujian_peserta up
    JOIN ujian u ON up.ujian_id = u.id
    JOIN mata_pelajaran m ON u.mapel_id = m.id
    WHERE up.siswa_id = ? AND up.nilai IS NOT NULL
    ORDER BY up.selesai_mengerjakan DESC
");
$stmt->execute([$userId]);
$nilaiList = $stmt->fetchAll();

// Hitung rata-rata
$stmt = $db->prepare("SELECT AVG(nilai) as avg FROM ujian_peserta WHERE siswa_id = ? AND nilai IS NOT NULL");
$stmt->execute([$userId]);
$rataRata = $stmt->fetch()['avg'] ?? 0;
?>

<div class="nav-menu">
    <a href="dashboard.php">📊 Dashboard</a>
    <a href="ujian-saya.php">🎯 Ujian Saya</a>
    <a href="kerjakan-ujian.php">✏️ Kerjakan Ujian</a>
    <a href="nilai-saya.php" class="active">📈 Nilai Saya</a>
</div>

<div class="content-card">
    <div class="content-header">
        <h2>📈 Nilai Saya</h2>
    </div>
    
    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; text-align: center;">
        <h3 style="margin-bottom: 10px;">Rata-rata Nilai</h3>
        <div style="font-size: 48px; font-weight: bold;"><?= number_format($rataRata, 1) ?></div>
    </div>
    
    <?php if (count($nilaiList) > 0): ?>
    <table>
        <thead>
            <tr>
                <th>Judul Ujian</th>
                <th>Mapel</th>
                <th>Tanggal Selesai</th>
                <th>Status</th>
                <th>Nilai</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($nilaiList as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['judul_ujian']) ?></td>
                <td><?= htmlspecialchars($item['nama_mapel']) ?></td>
                <td><?= date('d/m/Y H:i', strtotime($item['selesai_mengerjakan'])) ?></td>
                <td><span class="badge badge-selesai">Selesai</span></td>
                <td>
                    <?php
                    $nilaiColor = '#dc3545';
                    if ($item['nilai'] >= 75) $nilaiColor = '#28a745';
                    elseif ($item['nilai'] >= 60) $nilaiColor = '#ffc107';
                    ?>
                    <strong style="color: <?= $nilaiColor ?>; font-size: 18px;"><?= number_format($item['nilai'], 1) ?></strong>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <p style="color: #999; text-align: center; padding: 30px;">Belum ada nilai yang tersedia.</p>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../views/layouts/siswa.php';
?>

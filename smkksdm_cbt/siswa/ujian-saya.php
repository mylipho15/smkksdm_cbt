<?php
require_once __DIR__ . '/../../config/config.php';

$pageTitle = 'Ujian Saya';
ob_start();

$db = getDBConnection();
$userId = $_SESSION['user_id'];

// Get ujian yang sudah diikuti siswa
$stmt = $db->prepare("
    SELECT u.*, m.nama_mapel, up.status as peserta_status, up.nilai, up.mulai_mengerjakan, up.selesai_mengerjakan
    FROM ujian_peserta up
    JOIN ujian u ON up.ujian_id = u.id
    JOIN mata_pelajaran m ON u.mapel_id = m.id
    WHERE up.siswa_id = ?
    ORDER BY up.created_at DESC
");
$stmt->execute([$userId]);
$ujianList = $stmt->fetchAll();
?>

<div class="nav-menu">
    <a href="dashboard.php">📊 Dashboard</a>
    <a href="ujian-saya.php" class="active">🎯 Ujian Saya</a>
    <a href="kerjakan-ujian.php">✏️ Kerjakan Ujian</a>
    <a href="nilai-saya.php">📈 Nilai Saya</a>
</div>

<div class="content-card">
    <div class="content-header">
        <h2>🎯 Riwayat Ujian Saya</h2>
    </div>
    
    <?php if (count($ujianList) > 0): ?>
    <table>
        <thead>
            <tr>
                <th>Judul Ujian</th>
                <th>Mapel</th>
                <th>Tanggal Mulai</th>
                <th>Tanggal Selesai</th>
                <th>Status</th>
                <th>Nilai</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($ujianList as $ujian): ?>
            <tr>
                <td><?= htmlspecialchars($ujian['judul_ujian']) ?></td>
                <td><?= htmlspecialchars($ujian['nama_mapel']) ?></td>
                <td><?= date('d/m/Y H:i', strtotime($ujian['tanggal_mulai'])) ?></td>
                <td><?= date('d/m/Y H:i', strtotime($ujian['tanggal_selesai'])) ?></td>
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
                    <?php if ($ujian['nilai'] !== null): ?>
                    <strong style="color: #28a745;"><?= number_format($ujian['nilai'], 1) ?></strong>
                    <?php else: ?>
                    -
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <p style="color: #999; text-align: center; padding: 30px;">Anda belum mengikuti ujian apapun.</p>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../views/layouts/siswa.php';
?>

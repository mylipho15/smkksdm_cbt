<?php
require_once __DIR__ . '/../../config/config.php';

$pageTitle = 'Kerjakan Ujian';
ob_start();

$db = getDBConnection();
$userId = $_SESSION['user_id'];
$now = date('Y-m-d H:i:s');

// Get semua ujian yang tersedia untuk siswa
$stmt = $db->prepare("
    SELECT u.*, m.nama_mapel, up.status as peserta_status, up.id as peserta_id
    FROM ujian u
    JOIN mata_pelajaran m ON u.mapel_id = m.id
    LEFT JOIN ujian_peserta up ON u.id = up.ujian_id AND up.siswa_id = ?
    WHERE u.status = 'aktif' 
      AND u.tanggal_mulai <= ?
      AND u.tanggal_selesai >= ?
    ORDER BY u.tanggal_selesai ASC
");
$stmt->execute([$userId, $now, $now]);
$ujianList = $stmt->fetchAll();
?>

<div class="nav-menu">
    <a href="dashboard.php">📊 Dashboard</a>
    <a href="ujian-saya.php">🎯 Ujian Saya</a>
    <a href="kerjakan-ujian.php" class="active">✏️ Kerjakan Ujian</a>
    <a href="nilai-saya.php">📈 Nilai Saya</a>
</div>

<div class="content-card">
    <div class="content-header">
        <h2>✏️ Daftar Ujian Tersedia</h2>
    </div>
    
    <?php if (count($ujianList) > 0): ?>
    <table>
        <thead>
            <tr>
                <th>Judul Ujian</th>
                <th>Mapel</th>
                <th>Tanggal Mulai</th>
                <th>Tanggal Selesai</th>
                <th>Durasi</th>
                <th>Jumlah Soal</th>
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
                <td><?= $ujian['jumlah_soal'] ?> soal</td>
                <td>
                    <?php
                    $statusClass = 'badge-belum';
                    $statusText = 'Belum Mulai';
                    $actionBtn = '';
                    
                    if ($ujian['peserta_status'] == 'sedang') {
                        $statusClass = 'badge-sedang';
                        $statusText = 'Sedang Dikerjakan';
                        $actionBtn = '<a href="lanjut-ujian.php?id=' . $ujian['peserta_id'] . '" class="btn btn-warning" style="padding: 5px 10px; font-size: 12px;">Lanjutkan</a>';
                    } elseif ($ujian['peserta_status'] == 'selesai') {
                        $statusClass = 'badge-selesai';
                        $statusText = 'Selesai';
                        $actionBtn = '<span style="color: #999; font-size: 13px;">Selesai</span>';
                    } else {
                        $actionBtn = '<a href="mulai-ujian.php?id=' . $ujian['id'] . '" class="btn btn-success" style="padding: 5px 10px; font-size: 12px;">Mulai</a>';
                    }
                    ?>
                    <span class="badge <?= $statusClass ?>"><?= $statusText ?></span>
                </td>
                <td>
                    <?= $actionBtn ?>
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

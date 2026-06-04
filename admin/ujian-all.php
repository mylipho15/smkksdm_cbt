<?php
require_once __DIR__ . '/../../config/config.php';

$pageTitle = 'Semua Ujian';
ob_start();

$db = getDBConnection();

$message = '';
$messageType = '';

// Handle delete ujian
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $stmt = $db->prepare("DELETE FROM ujian WHERE id = ?");
        $stmt->execute([$id]);
        $message = 'Ujian berhasil dihapus';
        $messageType = 'success';
    } catch (PDOException $e) {
        $message = 'Gagal menghapus ujian';
        $messageType = 'error';
    }
}

// Get filter
$filterStatus = $_GET['status'] ?? '';
$where = [];
$params = [];

if ($filterStatus) {
    $where[] = "status = ?";
    $params[] = $filterStatus;
}

$sql = "SELECT u.*, m.nama_mapel, gu.nama_lengkap as nama_guru
        FROM ujian u
        JOIN mata_pelajaran m ON u.mapel_id = m.id
        JOIN users gu ON u.guru_id = gu.id";

if (count($where) > 0) {
    $sql .= " WHERE " . implode(' AND ', $where);
}
$sql .= " ORDER BY u.created_at DESC";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$ujianList = $stmt->fetchAll();
?>

<div class="nav-menu">
    <a href="dashboard.php" class="active">📊 Dashboard</a>
    <a href="users.php">👥 Manajemen User</a>
    <a href="mapel.php">📚 Mata Pelajaran</a>
    <a href="ujian-all.php">🎯 Semua Ujian</a>
    <a href="soal-all.php">📝 Bank Soal</a>
</div>

<?php if ($message): ?>
<div class="alert alert-<?= $messageType ?>"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<div class="content-card">
    <div class="content-header">
        <h2>🎯 Semua Ujian</h2>
    </div>
    
    <form method="GET" style="margin-bottom: 20px;">
        <select name="status" onchange="this.form.submit()" style="padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
            <option value="">Semua Status</option>
            <option value="draft" <?= $filterStatus == 'draft' ? 'selected' : '' ?>>Draft</option>
            <option value="aktif" <?= $filterStatus == 'aktif' ? 'selected' : '' ?>>Aktif</option>
            <option value="selesai" <?= $filterStatus == 'selesai' ? 'selected' : '' ?>>Selesai</option>
        </select>
    </form>
    
    <?php if (count($ujianList) > 0): ?>
    <table>
        <thead>
            <tr>
                <th>Kode</th>
                <th>Judul Ujian</th>
                <th>Mapel</th>
                <th>Guru</th>
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
                <td><?= htmlspecialchars($ujian['kode_ujian']) ?></td>
                <td><?= htmlspecialchars($ujian['judul_ujian']) ?></td>
                <td><?= htmlspecialchars($ujian['nama_mapel']) ?></td>
                <td><?= htmlspecialchars($ujian['nama_guru']) ?></td>
                <td><?= date('d/m/Y H:i', strtotime($ujian['tanggal_mulai'])) ?></td>
                <td><?= date('d/m/Y H:i', strtotime($ujian['tanggal_selesai'])) ?></td>
                <td><?= $ujian['durasi_menit'] ?> menit</td>
                <td>
                    <?php
                    $statusClass = '';
                    $statusText = '';
                    switch($ujian['status']) {
                        case 'draft': $statusClass = 'badge-essay'; $statusText = 'Draft'; break;
                        case 'aktif': $statusClass = 'badge-pg'; $statusText = 'Aktif'; break;
                        case 'selesai': $statusClass = 'badge-selesai'; $statusText = 'Selesai'; break;
                    }
                    ?>
                    <span class="badge <?= $statusClass ?>"><?= $statusText ?></span>
                </td>
                <td>
                    <a href="?delete=<?= $ujian['id'] ?>" class="btn btn-danger" style="padding: 5px 10px; font-size: 12px;" onclick="return confirm('Yakin ingin menghapus ujian ini?')">Hapus</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <p style="color: #999; text-align: center; padding: 30px;">Belum ada ujian.</p>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../views/layouts/admin.php';
?>

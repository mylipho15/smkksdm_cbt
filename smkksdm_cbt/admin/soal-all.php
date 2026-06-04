<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Soal.php';

$pageTitle = 'Bank Soal Semua Mapel';
ob_start();

$soalModel = new Soal();
$db = getDBConnection();

$message = '';
$messageType = '';

// Handle delete soal
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($soalModel->delete($id)) {
        $message = 'Soal berhasil dihapus';
        $messageType = 'success';
    } else {
        $message = 'Gagal menghapus soal';
        $messageType = 'error';
    }
}

// Get filter
$filterMapel = $_GET['mapel'] ?? '';
$filterJenis = $_GET['jenis'] ?? '';

// Build query
$where = [];
$params = [];

if ($filterMapel) {
    $where[] = "b.mapel_id = ?";
    $params[] = $filterMapel;
}

if ($filterJenis) {
    $where[] = "b.jenis_soal = ?";
    $params[] = $filterJenis;
}

$sql = "SELECT b.*, m.nama_mapel, u.nama_lengkap as nama_guru
        FROM bank_soal b 
        JOIN mata_pelajaran m ON b.mapel_id = m.id 
        JOIN users u ON b.guru_id = u.id";

if (count($where) > 0) {
    $sql .= " WHERE " . implode(' AND ', $where);
}
$sql .= " ORDER BY b.created_at DESC";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$soalList = $stmt->fetchAll();

$mapelList = $soalModel->getMapel();
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
        <h2>📝 Bank Soal - Semua Mapel</h2>
    </div>
    
    <form method="GET" style="margin-bottom: 20px; display: flex; gap: 15px; align-items: flex-end;">
        <div class="form-group" style="margin-bottom: 0; flex: 1;">
            <label>Mata Pelajaran</label>
            <select name="mapel">
                <option value="">Semua Mapel</option>
                <?php foreach ($mapelList as $mapel): ?>
                <option value="<?= $mapel['id'] ?>" <?= $filterMapel == $mapel['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($mapel['nama_mapel']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group" style="margin-bottom: 0; width: 200px;">
            <label>Jenis Soal</label>
            <select name="jenis">
                <option value="">Semua Jenis</option>
                <option value="pilihan_ganda" <?= $filterJenis == 'pilihan_ganda' ? 'selected' : '' ?>>Pilihan Ganda</option>
                <option value="essay" <?= $filterJenis == 'essay' ? 'selected' : '' ?>>Essay</option>
            </select>
        </div>
        
        <button type="submit" class="btn btn-success">Filter</button>
        <a href="soal-all.php" class="btn btn-warning">Reset</a>
    </form>
    
    <?php if (count($soalList) > 0): ?>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Pertanyaan</th>
                <th>Mapel</th>
                <th>Guru</th>
                <th>Jenis</th>
                <th>Kunci</th>
                <th>Bobot</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            foreach ($soalList as $soal): 
            ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars(substr($soal['pertanyaan'], 0, 80)) ?>...</td>
                <td><?= htmlspecialchars($soal['nama_mapel']) ?></td>
                <td><?= htmlspecialchars($soal['nama_guru']) ?></td>
                <td>
                    <span class="badge badge-<?= $soal['jenis_soal'] == 'pilihan_ganda' ? 'pg' : 'essay' ?>">
                        <?= $soal['jenis_soal'] == 'pilihan_ganda' ? 'Pilihan Ganda' : 'Essay' ?>
                    </span>
                </td>
                <td><?= htmlspecialchars($soal['kunci_jawaban'] ?? '-') ?></td>
                <td><?= $soal['bobot_nilai'] ?></td>
                <td>
                    <a href="?delete=<?= $soal['id'] ?>" class="btn btn-danger" style="padding: 5px 10px; font-size: 12px;" onclick="return confirm('Yakin ingin menghapus soal ini?')">Hapus</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <p style="color: #999; text-align: center; padding: 30px;">Belum ada soal.</p>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../views/layouts/admin.php';
?>

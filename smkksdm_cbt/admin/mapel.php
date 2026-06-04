<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Soal.php';

$pageTitle = 'Mata Pelajaran';
ob_start();

$soalModel = new Soal();
$db = getDBConnection();

$message = '';
$messageType = '';

// Handle add mapel
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_mapel'])) {
    $namaMapel = sanitize($_POST['nama_mapel'] ?? '');
    $guruId = $_POST['guru_id'] ?? null;
    
    if (empty($namaMapel)) {
        $message = 'Nama mata pelajaran harus diisi';
        $messageType = 'error';
    } else {
        $kodeMapel = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $namaMapel), 0, 3)) . '-' . time();
        try {
            $stmt = $db->prepare("INSERT INTO mata_pelajaran (kode_mapel, nama_mapel, guru_id) VALUES (?, ?, ?)");
            $stmt->execute([$kodeMapel, $namaMapel, $guruId]);
            $message = 'Mata pelajaran berhasil ditambahkan';
            $messageType = 'success';
        } catch (PDOException $e) {
            $message = 'Gagal menambahkan mata pelajaran';
            $messageType = 'error';
        }
    }
}

// Handle delete mapel
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $stmt = $db->prepare("DELETE FROM mata_pelajaran WHERE id = ?");
        $stmt->execute([$id]);
        $message = 'Mata pelajaran berhasil dihapus';
        $messageType = 'success';
    } catch (PDOException $e) {
        $message = 'Gagal menghapus mata pelajaran (masih digunakan)';
        $messageType = 'error';
    }
}

$mapelList = $soalModel->getMapel();

// Get all guru for dropdown
$stmt = $db->query("SELECT id, nama_lengkap FROM users WHERE role = 'guru' ORDER BY nama_lengkap");
$guruList = $stmt->fetchAll();
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
        <h2>📚 Mata Pelajaran</h2>
        <button onclick="document.getElementById('addMapelModal').style.display='block'" class="btn btn-primary">+ Tambah Mapel</button>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Kode</th>
                <th>Nama Mapel</th>
                <th>Guru Penanggung Jawab</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($mapelList as $mapel): ?>
            <tr>
                <td><?= htmlspecialchars($mapel['kode_mapel']) ?></td>
                <td><?= htmlspecialchars($mapel['nama_mapel']) ?></td>
                <td>
                    <?php 
                    if ($mapel['guru_id']) {
                        $stmt = $db->prepare("SELECT nama_lengkap FROM users WHERE id = ?");
                        $stmt->execute([$mapel['guru_id']]);
                        $guru = $stmt->fetch();
                        echo htmlspecialchars($guru['nama_lengkap'] ?? '-');
                    } else {
                        echo '-';
                    }
                    ?>
                </td>
                <td>
                    <a href="?delete=<?= $mapel['id'] ?>" class="btn btn-danger" style="padding: 5px 10px; font-size: 12px;" onclick="return confirm('Yakin ingin menghapus mata pelajaran ini?')">Hapus</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modal Tambah Mapel -->
<div id="addMapelModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 30px; border-radius: 10px; width: 90%; max-width: 500px;">
        <h3 style="margin-bottom: 20px;">➕ Tambah Mata Pelajaran</h3>
        <form method="POST">
            <div class="form-group">
                <label>Nama Mata Pelajaran *</label>
                <input type="text" name="nama_mapel" required placeholder="Contoh: Matematika">
            </div>
            <div class="form-group">
                <label>Guru Penanggung Jawab</label>
                <select name="guru_id">
                    <option value="">Pilih Guru (Opsional)</option>
                    <?php foreach ($guruList as $guru): ?>
                    <option value="<?= $guru['id'] ?>"><?= htmlspecialchars($guru['nama_lengkap']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" name="add_mapel" class="btn btn-success">Simpan</button>
            <button type="button" onclick="document.getElementById('addMapelModal').style.display='none'" class="btn btn-warning">Batal</button>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../views/layouts/admin.php';
?>

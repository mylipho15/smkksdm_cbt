<?php
require_once __DIR__ . '/../../config/config.php';

$pageTitle = 'Manajemen User';
ob_start();

$db = getDBConnection();

$message = '';
$messageType = '';

// Handle delete user
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $db->prepare("DELETE FROM users WHERE id = ? AND role != 'admin'");
    if ($stmt->execute([$id])) {
        $message = 'User berhasil dihapus';
        $messageType = 'success';
    } else {
        $message = 'Gagal menghapus user';
        $messageType = 'error';
    }
}

// Handle add user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $username = sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $namaLengkap = sanitize($_POST['nama_lengkap'] ?? '');
    $role = $_POST['role'] ?? '';
    $kelas = sanitize($_POST['kelas'] ?? '');
    
    if (empty($username) || empty($password) || empty($namaLengkap) || empty($role)) {
        $message = 'Semua field wajib diisi';
        $messageType = 'error';
    } else {
        $hashedPassword = hashPassword($password);
        try {
            $stmt = $db->prepare("INSERT INTO users (username, password, nama_lengkap, role, kelas) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$username, $hashedPassword, $namaLengkap, $role, $kelas]);
            $message = 'User berhasil ditambahkan';
            $messageType = 'success';
        } catch (PDOException $e) {
            $message = 'Username sudah digunakan';
            $messageType = 'error';
        }
    }
}

// Get filter
$filterRole = $_GET['role'] ?? '';
$where = [];
$params = [];

if ($filterRole) {
    $where[] = "role = ?";
    $params[] = $filterRole;
}

$sql = "SELECT * FROM users";
if (count($where) > 0) {
    $sql .= " WHERE " . implode(' AND ', $where);
}
$sql .= " ORDER BY created_at DESC";

$stmt = $db->query($sql);
$userList = $stmt->fetchAll();
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
        <h2>👥 Manajemen User</h2>
        <button onclick="document.getElementById('addUserModal').style.display='block'" class="btn btn-primary">+ Tambah User</button>
    </div>
    
    <form method="GET" style="margin-bottom: 20px;">
        <select name="role" onchange="this.form.submit()" style="padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
            <option value="">Semua Role</option>
            <option value="admin" <?= $filterRole == 'admin' ? 'selected' : '' ?>>Admin</option>
            <option value="guru" <?= $filterRole == 'guru' ? 'selected' : '' ?>>Guru</option>
            <option value="siswa" <?= $filterRole == 'siswa' ? 'selected' : '' ?>>Siswa</option>
        </select>
    </form>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Nama Lengkap</th>
                <th>Role</th>
                <th>Kelas</th>
                <th>Terdaftar</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($userList as $u): ?>
            <tr>
                <td><?= $u['id'] ?></td>
                <td><?= htmlspecialchars($u['username']) ?></td>
                <td><?= htmlspecialchars($u['nama_lengkap']) ?></td>
                <td><span class="badge role-<?= $u['role'] ?>"><?= ucfirst($u['role']) ?></span></td>
                <td><?= htmlspecialchars($u['kelas'] ?? '-') ?></td>
                <td><?= date('d/m/Y H:i', strtotime($u['created_at'])) ?></td>
                <td>
                    <?php if ($u['role'] != 'admin'): ?>
                    <a href="?delete=<?= $u['id'] ?>" class="btn btn-danger" style="padding: 5px 10px; font-size: 12px;" onclick="return confirm('Yakin ingin menghapus user ini?')">Hapus</a>
                    <?php else: ?>
                    <span style="color: #999; font-size: 12px;">-</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modal Tambah User -->
<div id="addUserModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 30px; border-radius: 10px; width: 90%; max-width: 500px;">
        <h3 style="margin-bottom: 20px;">➕ Tambah User Baru</h3>
        <form method="POST">
            <div class="form-group">
                <label>Username *</label>
                <input type="text" name="username" required>
            </div>
            <div class="form-group">
                <label>Password *</label>
                <input type="password" name="password" required>
            </div>
            <div class="form-group">
                <label>Nama Lengkap *</label>
                <input type="text" name="nama_lengkap" required>
            </div>
            <div class="form-group">
                <label>Role *</label>
                <select name="role" required>
                    <option value="">Pilih Role</option>
                    <option value="admin">Admin</option>
                    <option value="guru">Guru</option>
                    <option value="siswa">Siswa</option>
                </select>
            </div>
            <div class="form-group">
                <label>Kelas (untuk siswa)</label>
                <input type="text" name="kelas" placeholder="Contoh: X-A">
            </div>
            <button type="submit" name="add_user" class="btn btn-success">Simpan</button>
            <button type="button" onclick="document.getElementById('addUserModal').style.display='none'" class="btn btn-warning">Batal</button>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../views/layouts/admin.php';
?>

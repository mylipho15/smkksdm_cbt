<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Soal.php';

$pageTitle = 'Tambah Soal';
ob_start();

$soalModel = new Soal();
$db = getDBConnection();
$userId = $_SESSION['user_id'];

$message = '';
$messageType = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jenisSoal = $_POST['jenis_soal'] ?? '';
    $mapelId = $_POST['mapel_id'] ?? '';
    $pertanyaan = $_POST['pertanyaan'] ?? '';
    $opsiA = $_POST['opsi_a'] ?? null;
    $opsiB = $_POST['opsi_b'] ?? null;
    $opsiC = $_POST['opsi_c'] ?? null;
    $opsiD = $_POST['opsi_d'] ?? null;
    $opsiE = $_POST['opsi_e'] ?? null;
    $kunciJawaban = $_POST['kunci_jawaban'] ?? null;
    $bobotNilai = $_POST['bobot_nilai'] ?? 1;
    
    if (empty($mapelId) || empty($pertanyaan)) {
        $message = 'Mapel dan pertanyaan harus diisi';
        $messageType = 'error';
    } elseif ($jenisSoal == 'pilihan_ganda' && empty($kunciJawaban)) {
        $message = 'Kunci jawaban harus diisi untuk soal pilihan ganda';
        $messageType = 'error';
    } else {
        $data = [
            'mapel_id' => $mapelId,
            'guru_id' => $userId,
            'jenis_soal' => $jenisSoal,
            'pertanyaan' => $pertanyaan,
            'opsi_a' => $jenisSoal == 'pilihan_ganda' ? $opsiA : null,
            'opsi_b' => $jenisSoal == 'pilihan_ganda' ? $opsiB : null,
            'opsi_c' => $jenisSoal == 'pilihan_ganda' ? $opsiC : null,
            'opsi_d' => $jenisSoal == 'pilihan_ganda' ? $opsiD : null,
            'opsi_e' => $jenisSoal == 'pilihan_ganda' ? $opsiE : null,
            'kunci_jawaban' => $kunciJawaban,
            'bobot_nilai' => $bobotNilai
        ];
        
        if ($soalModel->create($data)) {
            redirect(BASE_URL . '/guru/soal.php?success=1');
        } else {
            $message = 'Gagal menyimpan soal';
            $messageType = 'error';
        }
    }
}

$mapelList = $soalModel->getMapel($userId);
?>

<div class="nav-menu">
    <a href="dashboard.php">📊 Dashboard</a>
    <a href="soal.php" class="active">📝 Bank Soal</a>
    <a href="import-soal.php">📥 Import Soal</a>
    <a href="ujian.php">🎯 Ujian</a>
    <a href="nilai.php">📈 Nilai Siswa</a>
</div>

<?php if ($message): ?>
<div class="alert alert-<?= $messageType ?>"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<div class="content-card">
    <div class="content-header">
        <h2>➕ Tambah Soal Baru</h2>
        <a href="soal.php" class="btn btn-warning">Kembali</a>
    </div>
    
    <form method="POST">
        <div class="form-group">
            <label>Jenis Soal *</label>
            <select name="jenis_soal" id="jenis_soal" required onchange="toggleOpsi()">
                <option value="">Pilih Jenis Soal</option>
                <option value="pilihan_ganda">Pilihan Ganda</option>
                <option value="essay">Essay</option>
            </select>
        </div>
        
        <div class="form-group">
            <label>Mata Pelajaran *</label>
            <select name="mapel_id" required>
                <option value="">Pilih Mata Pelajaran</option>
                <?php foreach ($mapelList as $mapel): ?>
                <option value="<?= $mapel['id'] ?>"><?= htmlspecialchars($mapel['nama_mapel']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label>Pertanyaan *</label>
            <textarea name="pertanyaan" rows="4" required></textarea>
        </div>
        
        <div id="opsi_container" style="display: none;">
            <div class="form-group">
                <label>Opsi A</label>
                <input type="text" name="opsi_a" placeholder="Opsi A">
            </div>
            <div class="form-group">
                <label>Opsi B</label>
                <input type="text" name="opsi_b" placeholder="Opsi B">
            </div>
            <div class="form-group">
                <label>Opsi C</label>
                <input type="text" name="opsi_c" placeholder="Opsi C">
            </div>
            <div class="form-group">
                <label>Opsi D</label>
                <input type="text" name="opsi_d" placeholder="Opsi D">
            </div>
            <div class="form-group">
                <label>Opsi E</label>
                <input type="text" name="opsi_e" placeholder="Opsi E">
            </div>
            <div class="form-group">
                <label>Kunci Jawaban *</label>
                <select name="kunci_jawaban">
                    <option value="">Pilih Kunci Jawaban</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                    <option value="D">D</option>
                    <option value="E">E</option>
                </select>
            </div>
        </div>
        
        <div class="form-group">
            <label>Bobot Nilai</label>
            <input type="number" name="bobot_nilai" value="1" min="1" max="100">
        </div>
        
        <button type="submit" class="btn btn-success">💾 Simpan Soal</button>
        <a href="soal.php" class="btn btn-warning">Batal</a>
    </form>
</div>

<script>
function toggleOpsi() {
    const jenisSoal = document.getElementById('jenis_soal').value;
    const opsiContainer = document.getElementById('opsi_container');
    
    if (jenisSoal === 'pilihan_ganda') {
        opsiContainer.style.display = 'block';
    } else {
        opsiContainer.style.display = 'none';
    }
}
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../views/layouts/guru.php';
?>

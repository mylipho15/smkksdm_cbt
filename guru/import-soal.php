<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Soal.php';
require_once __DIR__ . '/../../imports/SoalImporter.php';

$pageTitle = 'Import Soal';
ob_start();

$soalModel = new Soal();
$db = getDBConnection();
$userId = $_SESSION['user_id'];

$message = '';
$messageType = '';
$previewData = null;

// Handle file upload dan preview
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['file'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        // Validasi ukuran file
        if ($file['size'] > MAX_FILE_SIZE) {
            $message = 'Ukuran file terlalu besar (max 5MB)';
            $messageType = 'error';
        } elseif (!in_array($ext, ['txt', 'docx'])) {
            $message = 'Format file harus TXT atau DOCX';
            $messageType = 'error';
        } else {
            // Simpan file temporary
            $tempPath = UPLOAD_DIR . 'temp_' . time() . '.' . $ext;
            
            if (move_uploaded_file($file['tmp_name'], $tempPath)) {
                // Parse file
                if ($ext === 'txt') {
                    $result = SoalImporter::importFromTXT($tempPath);
                } else {
                    $result = SoalImporter::importFromDOCX($tempPath);
                }
                
                if ($result['success']) {
                    $previewData = $result['data'];
                    $_SESSION['import_temp'] = [
                        'path' => $tempPath,
                        'ext' => $ext,
                        'data' => $result['data']
                    ];
                    $message = 'File berhasil diparsing. Ditemukan ' . count($previewData) . ' soal. Silakan review dan klik Import.';
                    $messageType = 'success';
                } else {
                    $message = $result['message'];
                    $messageType = 'error';
                }
            } else {
                $message = 'Gagal mengupload file';
                $messageType = 'error';
            }
        }
    } else {
        $message = 'Silakan pilih file untuk diimport';
        $messageType = 'error';
    }
}

// Handle confirm import
if (isset($_POST['confirm_import']) && isset($_SESSION['import_temp'])) {
    $tempData = $_SESSION['import_temp'];
    
    $result = $soalModel->importSoal($tempData['data'], $userId);
    
    // Cleanup temp file
    if (file_exists($tempData['path'])) {
        unlink($tempData['path']);
    }
    unset($_SESSION['import_temp']);
    
    if ($result['success'] > 0) {
        $message = "Berhasil import {$result['success']} soal";
        if ($result['failed'] > 0) {
            $message .= " ({$result['failed']} gagal)";
        }
        $messageType = 'success';
        $previewData = null;
    } else {
        $message = 'Gagal mengimport soal';
        $messageType = 'error';
    }
}

$mapelList = $soalModel->getMapel($userId);
?>

<div class="nav-menu">
    <a href="dashboard.php">📊 Dashboard</a>
    <a href="soal.php">📝 Bank Soal</a>
    <a href="import-soal.php" class="active">📥 Import Soal</a>
    <a href="ujian.php">🎯 Ujian</a>
    <a href="nilai.php">📈 Nilai Siswa</a>
</div>

<?php if ($message): ?>
<div class="alert alert-<?= $messageType ?>"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<div class="content-card">
    <div class="content-header">
        <h2>📥 Import Soal dari File</h2>
    </div>
    
    <div style="margin-bottom: 20px;">
        <p><strong>Format yang didukung:</strong> TXT, DOCX</p>
        <p><strong>Ukuran maksimal:</strong> 5MB</p>
        <p><a href="<?= BASE_URL ?>/imports/template.txt" target="_blank" class="btn btn-primary" style="padding: 8px 15px; font-size: 13px;">📄 Download Template</a></p>
    </div>
    
    <form method="POST" enctype="multipart/form-data" style="margin-bottom: 30px;">
        <div class="form-group">
            <label>Pilih File (TXT/DOCX)</label>
            <input type="file" name="file" accept=".txt,.docx" required>
        </div>
        <button type="submit" name="preview" class="btn btn-primary">Preview Soal</button>
    </form>
    
    <?php if ($previewData && count($previewData) > 0): ?>
    <hr style="margin: 30px 0; border: none; border-top: 1px solid #eee;">
    
    <h3 style="margin-bottom: 15px;">📋 Preview Soal (<?= count($previewData) ?> soal)</h3>
    
    <form method="POST">
        <div style="max-height: 400px; overflow-y: auto; margin-bottom: 20px;">
            <?php foreach ($previewData as $index => $soal): ?>
            <div style="background: #f8f9fa; padding: 15px; margin-bottom: 15px; border-radius: 5px; border-left: 4px solid <?= $soal['jenis_soal'] == 'pilihan_ganda' ? '#667eea' : '#28a745' ?>;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                    <strong>#<?= $index + 1 ?> - <?= htmlspecialchars($soal['mapel'] ?? 'Umum') ?></strong>
                    <span class="badge badge-<?= $soal['jenis_soal'] == 'pilihan_ganda' ? 'pg' : 'essay' ?>">
                        <?= $soal['jenis_soal'] == 'pilihan_ganda' ? 'Pilihan Ganda' : 'Essay' ?>
                    </span>
                </div>
                <p style="margin-bottom: 10px;"><strong>Pertanyaan:</strong> <?= nl2br(htmlspecialchars($soal['pertanyaan'])) ?></p>
                
                <?php if ($soal['jenis_soal'] == 'pilihan_ganda'): ?>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 10px;">
                    <?php foreach (['A', 'B', 'C', 'D', 'E'] as $opsi): ?>
                    <div style="font-size: 13px;">
                        <strong><?= $opsi ?>:</strong> <?= htmlspecialchars($soal['opsi_' . strtolower($opsi)] ?? '-') ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                
                <div style="font-size: 13px; color: #666;">
                    <strong>Kunci Jawaban:</strong> <?= htmlspecialchars($soal['kunci_jawaban'] ?? '-') ?> | 
                    <strong>Bobot:</strong> <?= $soal['bobot_nilai'] ?? 1 ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <button type="submit" name="confirm_import" class="btn btn-success">✓ Import Semua Soal</button>
        <a href="import-soal.php" class="btn btn-warning">Batal</a>
    </form>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../views/layouts/guru.php';
?>

<?php
require_once __DIR__ . '/../config/config.php';

class Soal {
    private $db;
    
    public function __construct() {
        $this->db = getDBConnection();
    }
    
    /**
     * Get semua soal berdasarkan mapel
     */
    public function getByMapel($mapelId) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM bank_soal WHERE mapel_id = ? ORDER BY created_at DESC");
            $stmt->execute([$mapelId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }
    
    /**
     * Get soal by ID
     */
    public function getById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM bank_soal WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            return null;
        }
    }
    
    /**
     * Create soal baru
     */
    public function create($data) {
        try {
            $sql = "INSERT INTO bank_soal (mapel_id, guru_id, jenis_soal, pertanyaan, opsi_a, opsi_b, opsi_c, opsi_d, opsi_e, kunci_jawaban, bobot_nilai) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $data['mapel_id'],
                $data['guru_id'],
                $data['jenis_soal'],
                $data['pertanyaan'],
                $data['opsi_a'] ?? null,
                $data['opsi_b'] ?? null,
                $data['opsi_c'] ?? null,
                $data['opsi_d'] ?? null,
                $data['opsi_e'] ?? null,
                $data['kunci_jawaban'] ?? null,
                $data['bobot_nilai'] ?? 1
            ]);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
     * Update soal
     */
    public function update($id, $data) {
        try {
            $sql = "UPDATE bank_soal SET 
                    mapel_id = ?, jenis_soal = ?, pertanyaan = ?, 
                    opsi_a = ?, opsi_b = ?, opsi_c = ?, opsi_d = ?, opsi_e = ?, 
                    kunci_jawaban = ?, bobot_nilai = ? 
                    WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $data['mapel_id'],
                $data['jenis_soal'],
                $data['pertanyaan'],
                $data['opsi_a'] ?? null,
                $data['opsi_b'] ?? null,
                $data['opsi_c'] ?? null,
                $data['opsi_d'] ?? null,
                $data['opsi_e'] ?? null,
                $data['kunci_jawaban'] ?? null,
                $data['bobot_nilai'] ?? 1,
                $id
            ]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
     * Delete soal
     */
    public function delete($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM bank_soal WHERE id = ?");
            $stmt->execute([$id]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
     * Import soal dari array
     */
    public function importSoal($soalArray, $guruId) {
        $success = 0;
        $failed = 0;
        
        foreach ($soalArray as $soal) {
            // Cari atau buat mapel
            $mapelId = $this->getOrCreateMapel($soal['mapel'], $guruId);
            
            if ($mapelId) {
                $data = [
                    'mapel_id' => $mapelId,
                    'guru_id' => $guruId,
                    'jenis_soal' => $soal['jenis_soal'],
                    'pertanyaan' => $soal['pertanyaan'],
                    'opsi_a' => $soal['opsi_a'] ?? null,
                    'opsi_b' => $soal['opsi_b'] ?? null,
                    'opsi_c' => $soal['opsi_c'] ?? null,
                    'opsi_d' => $soal['opsi_d'] ?? null,
                    'opsi_e' => $soal['opsi_e'] ?? null,
                    'kunci_jawaban' => $soal['kunci_jawaban'] ?? null,
                    'bobot_nilai' => $soal['bobot_nilai'] ?? 1
                ];
                
                if ($this->create($data)) {
                    $success++;
                } else {
                    $failed++;
                }
            } else {
                $failed++;
            }
        }
        
        return ['success' => $success, 'failed' => $failed];
    }
    
    /**
     * Get or create mata pelajaran
     */
    private function getOrCreateMapel($namaMapel, $guruId) {
        try {
            // Cek apakah mapel sudah ada
            $stmt = $this->db->prepare("SELECT id FROM mata_pelajaran WHERE nama_mapel = ? OR kode_mapel = ?");
            $kodeMapel = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $namaMapel), 0, 3)) . '-' . time();
            $stmt->execute([$namaMapel, $kodeMapel]);
            $mapel = $stmt->fetch();
            
            if ($mapel) {
                return $mapel['id'];
            }
            
            // Buat mapel baru
            $stmt = $this->db->prepare("INSERT INTO mata_pelajaran (kode_mapel, nama_mapel, guru_id) VALUES (?, ?, ?)");
            $stmt->execute([$kodeMapel, $namaMapel, $guruId]);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
     * Get semua mata pelajaran
     */
    public function getMapel($guruId = null) {
        try {
            if ($guruId) {
                $stmt = $this->db->prepare("SELECT * FROM mata_pelajaran WHERE guru_id = ? OR guru_id IS NULL ORDER BY nama_mapel");
                $stmt->execute([$guruId]);
            } else {
                $stmt = $this->db->query("SELECT * FROM mata_pelajaran ORDER BY nama_mapel");
            }
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }
}
?>

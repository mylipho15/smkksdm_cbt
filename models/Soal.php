<?php
require_once __DIR__ . '/../config/config.php';

class Soal {
    private $db;
    
    public function __construct() {
        $this->db = getDBConnection();
    }
    
    /**
     * Get all questions by subject
     */
    public function getByMapel($mapelId) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM questions WHERE subject_id = ? ORDER BY created_at DESC");
            $stmt->execute([$mapelId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }
    
    /**
     * Get question by ID
     */
    public function getById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM questions WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            return null;
        }
    }
    
    /**
     * Create new question
     */
    public function create($data) {
        try {
            $sql = "INSERT INTO questions (subject_id, teacher_id, question_type, question_text, option_a, option_b, option_c, option_d, option_e, answer_key, weight) 
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
     * Update question
     */
    public function update($id, $data) {
        try {
            $sql = "UPDATE questions SET 
                    subject_id = ?, question_type = ?, question_text = ?, 
                    option_a = ?, option_b = ?, option_c = ?, option_d = ?, option_e = ?, 
                    answer_key = ?, weight = ? 
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
     * Delete question
     */
    public function delete($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM questions WHERE id = ?");
            $stmt->execute([$id]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
     * Import questions from array
     */
    public function importSoal($soalArray, $guruId) {
        $success = 0;
        $failed = 0;
        
        foreach ($soalArray as $soal) {
            // Find or create subject
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
     * Get or create subject
     */
    private function getOrCreateMapel($namaMapel, $guruId) {
        try {
            // Check if subject exists
            $stmt = $this->db->prepare("SELECT id FROM subjects WHERE subject_name = ? OR subject_code = ?");
            $kodeMapel = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $namaMapel), 0, 3)) . '-' . time();
            $stmt->execute([$namaMapel, $kodeMapel]);
            $mapel = $stmt->fetch();
            
            if ($mapel) {
                return $mapel['id'];
            }
            
            // Create new subject
            $stmt = $this->db->prepare("INSERT INTO subjects (subject_code, subject_name, teacher_id) VALUES (?, ?, ?)");
            $stmt->execute([$kodeMapel, $namaMapel, $guruId]);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
     * Get all subjects
     */
    public function getMapel($guruId = null) {
        try {
            if ($guruId) {
                $stmt = $this->db->prepare("SELECT * FROM subjects WHERE teacher_id = ? OR teacher_id IS NULL ORDER BY subject_name");
                $stmt->execute([$guruId]);
            } else {
                $stmt = $this->db->query("SELECT * FROM subjects ORDER BY subject_name");
            }
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }
}
?>

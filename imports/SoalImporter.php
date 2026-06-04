<?php
/**
 * Parser untuk import soal dari file DOCX dan TXT
 */

class SoalImporter {
    
    /**
     * Import dari file TXT
     * Format TXT:
     * [JENIS_SOAL:PG|ESSAY]
     * [MAPEL:Nama Mapel]
     * [BOBOT:1]
     * PERTANYAAN: Isi pertanyaan di sini
     * A. Opsi A
     * B. Opsi B
     * C. Opsi C
     * D. Opsi D
     * E. Opsi E
     * KUNCI: A
     */
    public static function importFromTXT($filePath) {
        if (!file_exists($filePath)) {
            return ['success' => false, 'message' => 'File tidak ditemukan'];
        }
        
        $content = file_get_contents($filePath);
        $soalList = [];
        $currentSoal = [];
        $lines = explode("\n", $content);
        
        foreach ($lines as $line) {
            $line = trim($line);
            
            if (empty($line)) {
                if (!empty($currentSoal['pertanyaan'])) {
                    $soalList[] = $currentSoal;
                    $currentSoal = [];
                }
                continue;
            }
            
            // Parse metadata
            if (preg_match('/\[JENIS_SOAL:(PG|ESSAY|pilihan_ganda|essay)\]/i', $line, $matches)) {
                $jenis = strtoupper($matches[1]);
                $currentSoal['jenis_soal'] = ($jenis == 'PG') ? 'pilihan_ganda' : 'essay';
            } elseif (preg_match('/\[MAPEL:(.+?)\]/i', $line, $matches)) {
                $currentSoal['mapel'] = trim($matches[1]);
            } elseif (preg_match('/\[BOBOT:(\d+)\]/i', $line, $matches)) {
                $currentSoal['bobot_nilai'] = (int)$matches[1];
            } elseif (preg_match('/^KUNCI:\s*([A-E])/i', $line, $matches)) {
                $currentSoal['kunci_jawaban'] = strtoupper($matches[1]);
            } elseif (preg_match('/^PERTANYAAN:\s*(.+)/i', $line, $matches)) {
                $currentSoal['pertanyaan'] = trim($matches[1]);
            } elseif (preg_match('/^A\.\s*(.+)/i', $line, $matches)) {
                $currentSoal['opsi_a'] = trim($matches[1]);
            } elseif (preg_match('/^B\.\s*(.+)/i', $line, $matches)) {
                $currentSoal['opsi_b'] = trim($matches[1]);
            } elseif (preg_match('/^C\.\s*(.+)/i', $line, $matches)) {
                $currentSoal['opsi_c'] = trim($matches[1]);
            } elseif (preg_match('/^D\.\s*(.+)/i', $line, $matches)) {
                $currentSoal['opsi_d'] = trim($matches[1]);
            } elseif (preg_match('/^E\.\s*(.+)/i', $line, $matches)) {
                $currentSoal['opsi_e'] = trim($matches[1]);
            } elseif (!isset($currentSoal['pertanyaan']) && !preg_match('/^\[/',$line)) {
                $currentSoal['pertanyaan'] = $line;
            } elseif (isset($currentSoal['pertanyaan']) && !isset($currentSoal['opsi_a']) && !preg_match('/^[A-E]\./i', $line) && !preg_match('/^KUNCI:/i', $line)) {
                $currentSoal['pertanyaan'] .= ' ' . $line;
            }
        }
        
        // Add last soal if exists
        if (!empty($currentSoal['pertanyaan'])) {
            $soalList[] = $currentSoal;
        }
        
        return ['success' => true, 'data' => $soalList, 'count' => count($soalList)];
    }
    
    /**
     * Import dari file DOCX
     * Format sama dengan TXT, menggunakan PHPWord jika tersedia
     */
    public static function importFromDOCX($filePath) {
        if (!file_exists($filePath)) {
            return ['success' => false, 'message' => 'File tidak ditemukan'];
        }
        
        // Cek apakah PHPWord tersedia
        if (!class_exists('PhpOffice\PhpWord\IOFactory')) {
            // Fallback: baca sebagai text biasa
            return self::importFromTXT($filePath);
        }
        
        try {
            $phpWord = \PhpOffice\PhpWord\IOFactory::load($filePath);
            $text = '';
            
            foreach ($phpWord->getSections() as $section) {
                foreach ($section->getElements() as $element) {
                    if (method_exists($element, 'getText')) {
                        $text .= $element->getText() . "\n";
                    }
                }
            }
            
            // Simpan ke temporary file dan parse
            $tempFile = tempnam(sys_get_temp_dir(), 'soal_') . '.txt';
            file_put_contents($tempFile, $text);
            $result = self::importFromTXT($tempFile);
            unlink($tempFile);
            
            return $result;
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Gagal membaca file DOCX: ' . $e->getMessage()];
        }
    }
    
    /**
     * Generate template format import
     */
    public static function getTemplateFormat() {
        return "FORMAT IMPORT SOAL
====================

1. FORMAT TXT/DOCX:

[JENIS_SOAL:PG]
[MAPEL:Matematika]
[BOBOT:1]
PERTANYAAN: Berapakah hasil dari 5 + 3?
A. 5
B. 6
C. 7
D. 8
E. 9
KUNCI: D

[JENIS_SOAL:ESSAY]
[MAPEL:Bahasa Indonesia]
[BOBOT:2]
PERTANYAAN: Jelaskan pengertian dari puisi bebas!
KUNCI: Puisi bebas adalah puisi yang tidak terikat oleh aturan-aturan seperti rima, irama, dan jumlah baris.

2. KETERANGAN:
- JENIS_SOAL: PG untuk Pilihan Ganda, ESSAY untuk Essay
- MAPEL: Nama mata pelajaran
- BOBOT: Nilai/bobot soal (default: 1)
- Untuk PG, wajib ada opsi A sampai E
- KUNCI: Jawaban benar (A/B/C/D/E untuk PG, atau penjelasan singkat untuk essay)
- Pisahkan setiap soal dengan garis kosong

3. CONTOH FILE LENGKAP:
Lihat file template di folder imports/template.txt
";
    }
}
?>

<?php
/**
 * Parser for importing questions from DOCX and TXT files
 */

class SoalImporter {
    
    /**
     * Import from TXT file
     * TXT Format:
     * [QUESTION_TYPE:MC|ESSAY]
     * [SUBJECT:Subject Name]
     * [WEIGHT:1]
     * QUESTION: Question content here
     * A. Option A
     * B. Option B
     * C. Option C
     * D. Option D
     * E. Option E
     * ANSWER: A
     */
    public static function importFromTXT($filePath) {
        if (!file_exists($filePath)) {
            return ['success' => false, 'message' => 'File not found'];
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
            if (preg_match('/\[QUESTION_TYPE:(MC|ESSAY|pilihan_ganda|essay)\]/i', $line, $matches)) {
                $jenis = strtoupper($matches[1]);
                $currentSoal['jenis_soal'] = ($jenis == 'MC') ? 'pilihan_ganda' : 'essay';
            } elseif (preg_match('/\[SUBJECT:(.+?)\]/i', $line, $matches)) {
                $currentSoal['mapel'] = trim($matches[1]);
            } elseif (preg_match('/\[WEIGHT:(\d+)\]/i', $line, $matches)) {
                $currentSoal['bobot_nilai'] = (int)$matches[1];
            } elseif (preg_match('/^ANSWER:\s*([A-E])/i', $line, $matches)) {
                $currentSoal['kunci_jawaban'] = strtoupper($matches[1]);
            } elseif (preg_match('/^QUESTION:\s*(.+)/i', $line, $matches)) {
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
            } elseif (isset($currentSoal['pertanyaan']) && !isset($currentSoal['opsi_a']) && !preg_match('/^[A-E]\./i', $line) && !preg_match('/^ANSWER:/i', $line)) {
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
     * Import from DOCX file
     * Same format as TXT, using PHPWord if available
     */
    public static function importFromDOCX($filePath) {
        if (!file_exists($filePath)) {
            return ['success' => false, 'message' => 'File not found'];
        }
        
        // Check if PHPWord is available
        if (!class_exists('PhpOffice\PhpWord\IOFactory')) {
            // Fallback: read as plain text
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
            
            // Save to temporary file and parse
            $tempFile = tempnam(sys_get_temp_dir(), 'soal_') . '.txt';
            file_put_contents($tempFile, $text);
            $result = self::importFromTXT($tempFile);
            unlink($tempFile);
            
            return $result;
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Failed to read DOCX file: ' . $e->getMessage()];
        }
    }
    
    /**
     * Generate import format template
     */
    public static function getTemplateFormat() {
        return "QUESTION IMPORT FORMAT
====================

1. TXT/DOCX FORMAT:

[QUESTION_TYPE:MC]
[SUBJECT:Mathematics]
[WEIGHT:1]
QUESTION: What is the result of 5 + 3?
A. 5
B. 6
C. 7
D. 8
E. 9
ANSWER: D

[QUESTION_TYPE:ESSAY]
[SUBJECT:Indonesian Language]
[WEIGHT:2]
QUESTION: Explain the definition of free verse poetry!
ANSWER: Free verse poetry is poetry that is not bound by rules such as rhyme, rhythm, and number of lines.

2. DESCRIPTION:
- QUESTION_TYPE: MC for Multiple Choice, ESSAY for Essay
- SUBJECT: Subject name
- WEIGHT: Score/weight of the question (default: 1)
- For MC, options A through E are required
- ANSWER: Correct answer (A/B/C/D/E for MC, or brief explanation for essay)
- Separate each question with a blank line

3. COMPLETE EXAMPLE FILE:
See the template file in imports/template.txt
";
    }
}
?>

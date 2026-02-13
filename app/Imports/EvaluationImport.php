<?php

namespace App\Imports;

use App\Models\Evaluation;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class EvaluationImport implements ToModel, WithValidation
{
    protected $defaultCategory;
    protected $subCategory;

    public function __construct($defaultCategory = null, $subCategory = null)
    {
        $this->defaultCategory = $defaultCategory;
        $this->subCategory = $subCategory;
    }

    public function model(array $row)
    {
        // 1. SKIP HEADER ROW (Heuristic Detection)
        // Convert all row values to lowercase for checking
        $rowValues = array_map(fn($v) => strtolower(trim($v ?? '')), $row);
        
        // If row contains header-like keywords, skip it
        if (in_array('soal', $rowValues) || 
            in_array('question', $rowValues) || 
            in_array('pertanyaan', $rowValues) || 
            in_array('soal essay', $rowValues) ||
            in_array('soal esai', $rowValues) ||
            in_array('kunci', $rowValues) ||
            in_array('opsi a', $rowValues)) {
            return null;
        }

        // 2. SKIP EMPTY ROWS
        // Filter out empty values
        if (empty(array_filter($rowValues, fn($v) => $v !== ''))) {
            return null;
        }

        // 3. MAP COLUMNS BY INDEX (No Header Mode)
        // Heuristic: If column 0 is a small number (1, 2, 3...), then Question is likely at index 1.
        // Otherwise, Question is at index 0.
        
        $col0 = $row[0] ?? null;
        $col1 = $row[1] ?? null;
        
        $isNumbered = is_numeric($col0) && $col0 < 1000; // Assume question numbering < 1000
        
        if ($isNumbered && !empty($col1)) {
            // Layout: [No, Question, OptA, OptB, OptC, OptD, Answer, Type]
            $qIdx = 1;
        } else {
            // Layout: [Question, OptA, OptB, OptC, OptD, Answer, Type]
            $qIdx = 0;
        }

        // Fetch values based on calculated index
        $question = $row[$qIdx] ?? null;
        if (!$question) return null; // Skip if no question found

        $optionA = $row[$qIdx + 1] ?? null;
        $optionB = $row[$qIdx + 2] ?? null;
        $optionC = $row[$qIdx + 3] ?? null;
        $optionD = $row[$qIdx + 4] ?? null;
        $correctAnswer = $row[$qIdx + 5] ?? null; // Usually after options
        
        // Type might be at index 6 or 7, but let's rely on auto-detection primarily
        // Or check if user put type in a specific column? 
        // Let's check if there is an explicit type column at the end
        $rawType = $row[$qIdx + 6] ?? $row[$qIdx + 7] ?? null;

        // 4. DETERMINE TYPE
        if ($rawType) {
            $t = strtolower(trim($rawType));
            if (str_contains($t, 'pg') || str_contains($t, 'ganda') || str_contains($t, 'choice')) {
                $type = 'multiple_choice';
            } elseif (str_contains($t, 'esai') || str_contains($t, 'essay')) {
                $type = 'essay';
            } else {
                // Fallback to auto-detect
                $type = (!empty($optionA)) ? 'multiple_choice' : 'essay';
            }
        } else {
            // Auto-detect based on options
            // If option A is present, assume multiple choice, otherwise essay
            $type = (!empty($optionA)) ? 'multiple_choice' : 'essay';
        }

        // 5. NORMALIZE CATEGORY
        // Category usually passed via constructor, but check if row has it (unlikely in simple format)
        // If the user put category in the Excel, it would be complex to guess index. 
        // We will stick to constructor defaults unless we find a strong match.
        
        $allowedCategories = ['cover', 'case', 'inner', 'endplate'];
        if ($this->defaultCategory) {
            $category = strtolower($this->defaultCategory);
        } else {
            $category = 'cover'; // Default fallback
        }
        
        // Normalize correct answer
        if ($correctAnswer) {
            $correctAnswer = strtolower(trim($correctAnswer));
        }
        
        return new Evaluation([
            'type'           => $type,
            'category'       => $category,
            'sub_category'   => $this->subCategory,
            'question'       => $question,
            'option_a'       => $optionA,
            'option_b'       => $optionB,
            'option_c'       => $optionC,
            'option_d'       => $optionD,
            'correct_answer' => $correctAnswer,
        ]);
    }

    public function rules(): array
    {
        return [
            // Relaxed rules handled in model()
            // 'question' => 'required', 
        ];
    }
}

<?php

namespace App\Imports;

use App\Models\Evaluation;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class EvaluationImport implements ToModel, WithHeadingRow, WithValidation
{
    protected $defaultCategory;
    protected $subCategory;
    protected $forcedType;

    public function __construct($defaultCategory = null, $subCategory = null, $forcedType = null)
    {
        $this->defaultCategory = $defaultCategory;
        $this->subCategory = $subCategory;
        $this->forcedType = $forcedType;
    }

    public function model(array $row)
    {
        // Aliases for flexibility
        if ($this->forcedType && $this->forcedType !== 'auto') {
            $type = $this->forcedType;
        } else {
            $rawType = $row['type'] ?? $row['tipe'] ?? $row['jenis'] ?? 'multiple_choice';
            $type = strtolower(trim($rawType));
            
            if ($type == 'pg' || $type == 'pilihan ganda' || $type == 'multiple choice') {
                $type = 'multiple_choice';
            }
            if ($type == 'esai' || $type == 'essay') {
                $type = 'essay';
            }
        }

        // Normalize category (Main Category: Cover, Case, etc.)
        $allowedCategories = ['cover', 'case', 'inner', 'endplate'];
        $rowCategoryRaw = $row['category'] ?? $row['kategori'] ?? $row['bagian'] ?? null;
        $rowCategory = $rowCategoryRaw ? strtolower(trim($rowCategoryRaw)) : null;
        
        if ($rowCategory && in_array($rowCategory, $allowedCategories)) {
            $category = $rowCategory;
        } elseif ($this->defaultCategory) {
            $category = strtolower($this->defaultCategory);
        } else {
            $category = $rowCategory ?: 'cover';
        }

        // Question & Options aliases
        $question = $row['question'] ?? $row['pertanyaan'] ?? $row['soal'] ?? $row['tanya'] ?? null;
        if (!$question) return null; // Skip empty rows

        $optionA = $row['option_a'] ?? $row['opsi_a'] ?? $row['pilihan_a'] ?? $row['a'] ?? null;
        $optionB = $row['option_b'] ?? $row['opsi_b'] ?? $row['pilihan_b'] ?? $row['b'] ?? null;
        $optionC = $row['option_c'] ?? $row['opsi_c'] ?? $row['pilihan_c'] ?? $row['c'] ?? null;
        $optionD = $row['option_d'] ?? $row['opsi_d'] ?? $row['pilihan_d'] ?? $row['d'] ?? null;
        $correctAnswer = $row['correct_answer'] ?? $row['kunci'] ?? $row['jawaban'] ?? $row['benar'] ?? $row['kunci_jawaban'] ?? $row['jawaban_benar'] ?? null;
        
        // Normalize correct answer to lowercase and trim
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

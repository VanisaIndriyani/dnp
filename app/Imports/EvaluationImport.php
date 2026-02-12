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

    public function __construct($defaultCategory = null, $subCategory = null)
    {
        $this->defaultCategory = $defaultCategory;
        $this->subCategory = $subCategory;
    }

    public function model(array $row)
    {
        // Normalize type
        $type = strtolower(trim($row['type'] ?? 'multiple_choice'));
        if ($type == 'pg' || $type == 'pilihan ganda' || $type == 'multiple choice') {
            $type = 'multiple_choice';
        }
        if ($type == 'esai') {
            $type = 'essay';
        }

        // Normalize category
        $allowedCategories = ['cover', 'case', 'inner', 'endplate'];
        $rowCategory = isset($row['category']) ? strtolower(trim($row['category'])) : null;
        
        // Logic: 
        // 1. If row category is valid, use it.
        // 2. If row category is invalid/empty AND we have a default category (from context), use default.
        // 3. Otherwise fallback to 'cover' or keep the invalid one (which might not show up).
        
        if ($rowCategory && in_array($rowCategory, $allowedCategories)) {
            $category = $rowCategory;
        } elseif ($this->defaultCategory) {
            $category = strtolower($this->defaultCategory);
        } else {
            $category = $rowCategory ?: 'cover';
        }
        
        return new Evaluation([
            'type'           => $type,
            'category'       => $category,
            'sub_category'   => $this->subCategory,
            'question'       => $row['question'],
            'option_a'       => $row['option_a'] ?? null,
            'option_b'       => $row['option_b'] ?? null,
            'option_c'       => $row['option_c'] ?? null,
            'option_d'       => $row['option_d'] ?? null,
            'correct_answer' => $row['correct_answer'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'type' => 'required',
            // 'category' => 'required', // Made optional since we can fallback to context
            'question' => 'required',
            'correct_answer' => 'nullable', 
        ];
    }
}

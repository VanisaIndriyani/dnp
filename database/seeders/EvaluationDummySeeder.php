<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Evaluation;
use App\Models\EvaluationResult;
use App\Models\EvaluationAnswer;
use Illuminate\Support\Facades\Hash;

class EvaluationDummySeeder extends Seeder
{
    public function run()
    {
        // 1. Ensure we have some operators
        $divisions = ['cover', 'case', 'inner', 'endplate'];
        $operators = [];

        foreach ($divisions as $i => $div) {
            $user = User::firstOrCreate(
                ['email' => "operator_{$div}@example.com"],
                [
                    'name' => "Operator " . ucfirst($div),
                    'nik' => 'OP' . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                    'role' => 'operator',
                    'division' => $div,
                    'password' => Hash::make('password'),
                    'status' => 'active'
                ]
            );
            $operators[] = $user;
        }

        // 2. Ensure we have some questions
        $questions = [];
        $types = ['multiple_choice', 'essay'];
        
        foreach ($divisions as $div) {
            // MC Question
            $q1 = Evaluation::firstOrCreate(
                ['question' => "Pertanyaan PG untuk $div?"],
                [
                    'type' => 'multiple_choice',
                    'category' => $div,
                    'sub_category' => 'General',
                    'option_a' => 'A',
                    'option_b' => 'B',
                    'option_c' => 'C',
                    'option_d' => 'D',
                    'correct_answer' => 'a'
                ]
            );
            
            // Essay Question
            $q2 = Evaluation::firstOrCreate(
                ['question' => "Jelaskan proses $div?"],
                [
                    'type' => 'essay',
                    'category' => $div,
                    'sub_category' => 'Technical',
                    'option_a' => null,
                    'option_b' => null,
                    'option_c' => null,
                    'option_d' => null,
                    'correct_answer' => null
                ]
            );
            
            $questions[$div] = [$q1, $q2];
        }

        // 3. Create Evaluation Results (5 Data)
        
        // Data 1: Pending, Unpublished (Need Verification)
        $this->createResult($operators[0], $questions['cover'], 'pending', false, 50, 0, 50);

        // Data 2: Graded, Unpublished (Need Verification)
        $this->createResult($operators[1], $questions['case'], 'graded', false, 100, 80, 90);

        // Data 3: Graded, Published (Already in Results)
        $this->createResult($operators[2], $questions['inner'], 'graded', true, 100, 90, 95);

        // Data 4: Pending, Unpublished (Another one needing verification)
        $this->createResult($operators[3], $questions['endplate'], 'pending', false, 0, 0, 0);

        // Data 5: Graded, Published (Another one in Results)
        // Re-use operator 0 for history/another record if allowed, but uniqueness is by user_id usually.
        // Let's create a new operator for the 5th data
        $user5 = User::firstOrCreate(
            ['email' => "operator_extra@example.com"],
            [
                'name' => "Operator Extra",
                'nik' => 'OP999',
                'role' => 'operator',
                'division' => 'cover',
                'password' => Hash::make('password'),
                'status' => 'active'
            ]
        );
        $this->createResult($user5, $questions['cover'], 'graded', true, 100, 75, 88);
    }

    private function createResult($user, $questions, $status, $isPublished, $mcScore, $essayScore, $totalScore)
    {
        // Check if result exists to avoid duplicate error
        if (EvaluationResult::where('user_id', $user->id)->exists()) {
            // Delete old one for seed refresh purpose
            EvaluationResult::where('user_id', $user->id)->delete();
            EvaluationAnswer::where('user_id', $user->id)->delete();
        }

        $result = EvaluationResult::create([
            'user_id' => $user->id,
            'score' => $totalScore,
            'status' => $status,
            'is_published' => $isPublished
        ]);

        // Create Answers
        foreach ($questions as $q) {
            $score = 0;
            $answerText = "Jawaban dummy";
            
            if ($q->type == 'multiple_choice') {
                $score = $mcScore; // Simplified logic
                $answerText = $mcScore == 100 ? 'a' : 'b';
            } else {
                $score = $essayScore;
                $answerText = "Ini jawaban essay panjang untuk simulasi.";
            }

            EvaluationAnswer::create([
                'user_id' => $user->id,
                'evaluation_id' => $q->id,
                'answer' => $answerText,
                'score' => $score
            ]);
        }
    }
}

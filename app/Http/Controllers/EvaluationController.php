<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use App\Models\EvaluationResult;
use App\Models\EvaluationAnswer;
use Illuminate\Http\Request;
use App\Exports\EvaluationExport;
use App\Imports\EvaluationImport;
use Maatwebsite\Excel\Facades\Excel;

class EvaluationController extends Controller
{
    public function start()
    {
        // Check if user already took the test
        $existingResult = EvaluationResult::where('user_id', auth()->id())->first();
        if ($existingResult) {
            return redirect()->route(auth()->user()->role . '.evaluation.results')->with('info', 'Anda sudah mengerjakan evaluasi. Skor Anda: ' . $existingResult->score . ($existingResult->status == 'pending' ? ' (Menunggu Penilaian)' : ''));
        }

        $userDivision = auth()->user()->division;
        
        // Strict filtering: Only show questions for the specific division.
        // Questions with NULL category (old questions) will only be shown if user has NULL division (or we can hide them).
        // Based on user feedback, they expect ONLY questions for their division.
        if ($userDivision) {
             $questions = Evaluation::where('category', $userDivision)->get();
        } else {
             // Fallback for users without division (maybe admins testing, or old users)
             $questions = Evaluation::whereNull('category')->get();
        }

        if ($questions->isEmpty()) {
             return redirect()->route(auth()->user()->role . '.dashboard')->with('error', 'Belum ada soal evaluasi untuk bagian Anda (' . ucfirst($userDivision ?? 'Umum') . ').');
        }

        return view('operator.evaluation.start', compact('questions'));
    }

    public function submit(Request $request)
    {
        $request->validate([
            'answers' => 'required|array',
        ]);

        $userDivision = auth()->user()->division;
        
        if ($userDivision) {
             $questions = Evaluation::where('category', $userDivision)->get();
        } else {
             $questions = Evaluation::whereNull('category')->get();
        }

        if ($questions->isEmpty()) {
             return redirect()->back()->with('error', 'Tidak ada soal yang tersedia.');
        }

        $essayExists = false;

        // Create the result record first (initially score 0, will update)
        $result = EvaluationResult::create([
            'user_id' => auth()->id(),
            'score' => 0,
            'status' => 'graded' // Default, will change to pending if essay found
        ]);

        foreach ($questions as $question) {
            $userAnswerText = $request->answers[$question->id] ?? null;
            $scoreForQuestion = 0;

            if ($question->type == 'essay') {
                $essayExists = true;
                $scoreForQuestion = 0; // Pending grading
            } else {
                // Multiple Choice
                if ($userAnswerText == $question->correct_answer) {
                    $scoreForQuestion = 100;
                }
            }

            // Store answer for ALL questions (MC and Essay)
            EvaluationAnswer::create([
                'user_id' => auth()->id(),
                'evaluation_id' => $question->id,
                'answer' => $userAnswerText ?? '',
                'score' => $scoreForQuestion
            ]);
        }

        // Calculate Initial Score (Average of all items)
        // Note: Essay scores are 0, so they pull down the average until graded.
        // This is expected behavior for "Pending" status.
        // Or we could exclude essay from initial calculation?
        // But logic in recalculateAndSave uses average of ALL.
        // So let's stick to that for consistency.
        
        $totalQuestions = $questions->count();
        $totalScore = EvaluationAnswer::where('user_id', auth()->id())
                        ->whereIn('evaluation_id', $questions->pluck('id')) // Just in case old answers exist? No, we just created them.
                        ->sum('score');
                        
        // Wait, we just created them, but we can sum from loop variables to avoid DB query?
        // Actually, let's use the DB summation in recalculate logic or just sum here.
        // But wait, $totalQuestions > 0.
        
        // Let's use the helper method to calculate score to ensure consistency!
        // But we need $result object.
        
        $this->recalculateAndSave($result); // This will save score and set status to graded.
        
        // Now override status if essay exists
        if ($essayExists) {
            $result->status = 'pending';
            $result->save();
             return redirect()->route(auth()->user()->role . '.evaluation.results')->with('success', "Evaluasi selesai. Jawaban esai Anda telah disimpan dan menunggu penilaian.");
        }

        return redirect()->route(auth()->user()->role . '.evaluation.results')->with('success', "Evaluasi selesai. Nilai Anda: {$result->score}");
    }

    public function index(Request $request)
    {
        if (auth()->user()->role == 'super_admin') {
            // If category is selected, show questions for that category
            if ($request->has('category') && $request->category != '') {
                $category = $request->category;
                
                $queryMc = Evaluation::where('type', 'multiple_choice')->where('category', $category);
                $queryEssay = Evaluation::where('type', 'essay')->where('category', $category);

                $mcQuestions = $queryMc->latest()->paginate(10, ['*'], 'mc_page');
                $essayQuestions = $queryEssay->latest()->paginate(10, ['*'], 'essay_page');
                
                return view('super_admin.evaluation.index', compact('mcQuestions', 'essayQuestions', 'category'));
            }
            
            // Default: Show Category Dashboard
            $categories = ['cover', 'case', 'inner', 'endplate'];
            $stats = [];
            foreach ($categories as $cat) {
                $stats[$cat] = [
                    'total' => Evaluation::where('category', $cat)->count(),
                    'mc' => Evaluation::where('category', $cat)->where('type', 'multiple_choice')->count(),
                    'essay' => Evaluation::where('category', $cat)->where('type', 'essay')->count(),
                ];
            }

            return view('super_admin.evaluation.index', compact('stats'));
        } elseif (auth()->user()->role == 'admin') {
            $evaluations = Evaluation::latest()->paginate(10);
            return view('admin.evaluation.index', compact('evaluations'));
        }

        return redirect()->route(auth()->user()->role . '.dashboard');
    }

    public function create(Request $request)
    {
        $type = $request->query('type', 'multiple_choice');
        $category = $request->query('category');
        return view('super_admin.evaluation.create', compact('type', 'category'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'questions' => 'required|array',
            'questions.*.type' => 'required|in:multiple_choice,essay',
            'questions.*.category' => 'required|string',
            'questions.*.question' => 'required|string',
            // Options and correct_answer required only if type is multiple_choice
            'questions.*.option_a' => 'required_if:questions.*.type,multiple_choice',
            'questions.*.option_b' => 'required_if:questions.*.type,multiple_choice',
            'questions.*.option_c' => 'required_if:questions.*.type,multiple_choice',
            'questions.*.option_d' => 'required_if:questions.*.type,multiple_choice',
            'questions.*.correct_answer' => 'required_if:questions.*.type,multiple_choice',
        ]);

        foreach ($request->questions as $q) {
            Evaluation::create([
                'type' => $q['type'],
                'category' => $q['category'],
                'question' => $q['question'],
                'option_a' => $q['type'] == 'multiple_choice' ? $q['option_a'] : null,
                'option_b' => $q['type'] == 'multiple_choice' ? $q['option_b'] : null,
                'option_c' => $q['type'] == 'multiple_choice' ? $q['option_c'] : null,
                'option_d' => $q['type'] == 'multiple_choice' ? $q['option_d'] : null,
                'correct_answer' => $q['type'] == 'multiple_choice' ? $q['correct_answer'] : null,
            ]);
        }

        $redirectRoute = route(auth()->user()->role . '.evaluation.index');
        if ($request->has('questions') && !empty($request->questions)) {
            $firstCategory = $request->input('questions.0.category');
            if ($firstCategory) {
                $redirectRoute = route(auth()->user()->role . '.evaluation.index', ['category' => $firstCategory]);
            }
        }

        return redirect($redirectRoute)->with('success', 'Soal evaluasi berhasil ditambahkan.');
    }

    public function edit(Evaluation $evaluation)
    {
        return view('super_admin.evaluation.edit', compact('evaluation'));
    }

    public function update(Request $request, Evaluation $evaluation)
    {
        $request->validate([
            'type' => 'required|in:multiple_choice,essay',
            'category' => 'required|string',
            'question' => 'required|string',
            'option_a' => 'required_if:type,multiple_choice',
            'option_b' => 'required_if:type,multiple_choice',
            'option_c' => 'required_if:type,multiple_choice',
            'option_d' => 'required_if:type,multiple_choice',
            'correct_answer' => 'required_if:type,multiple_choice',
        ]);

        $evaluation->update([
            'type' => $request->type,
            'category' => $request->category,
            'question' => $request->question,
            'option_a' => $request->type == 'multiple_choice' ? $request->option_a : null,
            'option_b' => $request->type == 'multiple_choice' ? $request->option_b : null,
            'option_c' => $request->type == 'multiple_choice' ? $request->option_c : null,
            'option_d' => $request->type == 'multiple_choice' ? $request->option_d : null,
            'correct_answer' => $request->type == 'multiple_choice' ? $request->correct_answer : null,
        ]);

        return redirect()->route(auth()->user()->role . '.evaluation.index', ['category' => $evaluation->category])->with('success', 'Soal evaluasi berhasil diperbarui.');
    }

    public function destroy(Evaluation $evaluation)
    {
        $category = $evaluation->category;
        $evaluation->delete();
        return redirect()->route(auth()->user()->role . '.evaluation.index', ['category' => $category])->with('success', 'Soal evaluasi berhasil dihapus.');
    }

    public function destroyAll(Request $request)
    {
        $request->validate([
            'category' => 'required|string'
        ]);

        $category = $request->category;
        
        // Delete all questions in this category
        Evaluation::where('category', $category)->delete();
        
        return redirect()->route(auth()->user()->role . '.evaluation.index', ['category' => $category])
            ->with('success', 'Semua soal evaluasi bagian ' . ucfirst($category) . ' berhasil dihapus.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            Excel::import(new EvaluationImport($request->category), $request->file('file'));
            return redirect()->back()->with('success', 'Soal evaluasi berhasil diimport.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal import data: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        $template = [
            ['type', 'category', 'question', 'option_a', 'option_b', 'option_c', 'option_d', 'correct_answer'],
            ['multiple_choice', 'cover', 'Apa warna langit?', 'Merah', 'Biru', 'Hijau', 'Kuning', 'Biru'],
            ['essay', 'case', 'Jelaskan tentang...', '', '', '', '', ''],
        ];

        return Excel::download(new class($template) implements \Maatwebsite\Excel\Concerns\FromArray {
            protected $data;
            public function __construct(array $data) { $this->data = $data; }
            public function array(): array { return $this->data; }
        }, 'template_soal_evaluasi.xlsx');
    }

    public function destroyResult($id)
    {
        $result = EvaluationResult::findOrFail($id);
        // Delete associated answers? 
        // answers are hasMany in Evaluation, but here we are deleting the Result.
        // Wait, EvaluationAnswer has user_id and evaluation_id, but not linked to EvaluationResult directly (my mistake in design?)
        // Actually, EvaluationAnswer is linked to User and Evaluation.
        // If we reset the result, we should also delete the user's answers for those questions so they can retake it.
        
        EvaluationAnswer::where('user_id', $result->user_id)->delete();
        
        $result->delete();
        return redirect()->back()->with('success', 'Hasil evaluasi berhasil direset (dihapus). User dapat mengerjakan ulang.');
    }

    public function results(Request $request)
    {
        if (auth()->user()->role == 'super_admin') {
            // If division is selected, show results for that division
            if ($request->has('division') && $request->division != '') {
                $division = $request->division;
                $query = EvaluationResult::with('user')
                    ->whereHas('user', function ($q) use ($division) {
                        $q->where('division', $division);
                    });

                // Date Filtering
                if ($request->filled('start_date')) {
                    $query->whereDate('created_at', '>=', $request->start_date);
                }
                if ($request->filled('end_date')) {
                    $query->whereDate('created_at', '<=', $request->end_date);
                }

                $results = $query->latest()->paginate(10);
                
                return view('super_admin.evaluation.results', compact('results', 'division'));
            }

            // Default: Show Category Dashboard for Results
            $divisions = ['cover', 'case', 'inner', 'endplate'];
            $stats = [];
            foreach ($divisions as $div) {
                $query = EvaluationResult::whereHas('user', function ($q) use ($div) {
                    $q->where('division', $div);
                });
                
                $stats[$div] = [
                    'total' => (clone $query)->count(),
                    'pending' => (clone $query)->where('status', 'pending')->count(),
                    'graded' => (clone $query)->where('status', 'graded')->count(),
                ];
            }

            return view('super_admin.evaluation.results', compact('stats'));
        }

        $query = EvaluationResult::with('user');

        if (auth()->user()->role == 'operator') {
            $query->where('user_id', auth()->id());
        }

        $results = $query->latest()->paginate(10);

        if (auth()->user()->role == 'operator') {
            return view('operator.evaluation.results', compact('results'));
        }

        return view('admin.evaluation.results', compact('results'));
    }

    public function exportResults(Request $request)
    {
        $query = EvaluationResult::with('user');

        if (auth()->user()->role == 'operator') {
            $query->where('user_id', auth()->id());
        }

        if (auth()->user()->role == 'super_admin' && $request->has('division') && $request->division != '') {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('division', $request->division);
            });
        }

        $results = $query->latest()->get();
        
        $fileName = 'Laporan_Hasil_Evaluasi';
        if ($request->has('division') && $request->division != '') {
            $fileName .= '_' . ucfirst($request->division);
        }
        $fileName .= '_' . date('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(new EvaluationExport($results), $fileName);
    }

    // Grading Methods
    public function grade($id)
    {
        $result = EvaluationResult::with('user')->findOrFail($id);
        
        // Get answers for this user
        // We assume the user took all current questions or we just find answers that exist
        $answers = EvaluationAnswer::with('evaluation')
            ->where('user_id', $result->user_id)
            ->get();
            
        // Separate answers
        $mcAnswers = $answers->filter(function ($answer) {
            return $answer->evaluation->type === 'multiple_choice';
        });
        
        $essayAnswers = $answers->filter(function ($answer) {
            return $answer->evaluation->type === 'essay';
        });

        // Calculate Breakdown Scores
        $mcScore = $mcAnswers->count() > 0 ? round($mcAnswers->avg('score')) : 0;
        $essayScore = $essayAnswers->count() > 0 ? round($essayAnswers->avg('score')) : 0;
            
        return view('super_admin.evaluation.grade', compact('result', 'answers', 'mcAnswers', 'essayAnswers', 'mcScore', 'essayScore'));
    }

    public function storeGrade(Request $request, $id)
    {
        $result = EvaluationResult::findOrFail($id);
        
        $request->validate([
            'grades' => 'required|array',
            'grades.*' => 'required|numeric|min:0|max:100', // Score per question
        ]);
        
        // 1. Update Essay Scores
        foreach ($request->grades as $answerId => $scoreVal) {
            $answer = EvaluationAnswer::findOrFail($answerId);
            $answer->score = $scoreVal;
            $answer->save();
        }
        
        // 2. Recalculate Total Score from Scratch (All Answers)
        $this->recalculateAndSave($result);
        
        return redirect()->route(auth()->user()->role . '.evaluation.results')->with('success', 'Penilaian berhasil disimpan. Skor akhir telah diperbarui.');
    }

    private function recalculateAndSave(EvaluationResult $result)
    {
        // Get all answers for this user
        // Note: We need to make sure we only get answers relevant to the current "session" or latest attempt
        // Since we delete old answers on reset, getting all answers for user_id should be fine
        // BUT ideally we should filter by evaluation IDs that currently exist?
        // Let's just take all answers for the user, assuming one active attempt.
        
        $answers = EvaluationAnswer::where('user_id', $result->user_id)->get();
        
        if ($answers->count() > 0) {
            $totalScore = $answers->sum('score');
            $averageScore = $totalScore / $answers->count();
            
            $result->score = round($averageScore);
            $result->status = 'graded';
            $result->save();
        }
    }
}

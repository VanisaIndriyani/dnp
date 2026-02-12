<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use App\Models\EvaluationResult;
use App\Models\EvaluationAnswer;
use Illuminate\Http\Request;
use App\Exports\EvaluationExport;
use App\Imports\EvaluationImport;
use Maatwebsite\Excel\Facades\Excel;

use App\Models\Setting; // Import Setting Model
use Illuminate\Support\Str;

class EvaluationController extends Controller
{
    public function updatePassingGrade(Request $request)
    {
        if (auth()->user()->role !== 'super_admin') {
            abort(403);
        }

        $request->validate([
            'passing_grade' => 'required|numeric|min:0|max:100',
        ]);

        Setting::setValue('evaluation_passing_grade', $request->passing_grade);

        return redirect()->back()->with('success', 'Nilai minimal kelulusan (KKM) berhasil diperbarui.');
    }

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

        // Prevent duplicate submission
        if (EvaluationResult::where('user_id', auth()->id())->exists()) {
            return redirect()->route(auth()->user()->role . '.evaluation.results')->with('error', 'Anda sudah mengerjakan evaluasi.');
        }

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
                // Normalize both answers for comparison
                $normalizedUserAnswer = strtolower(trim($userAnswerText ?? ''));
                $normalizedCorrectAnswer = strtolower(trim($question->correct_answer ?? ''));
                
                if ($normalizedUserAnswer === $normalizedCorrectAnswer) {
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
                $subCategory = $request->sub_category;
                
                $queryMc = Evaluation::where('type', 'multiple_choice')->where('category', $category);
                $queryEssay = Evaluation::where('type', 'essay')->where('category', $category);

                if ($subCategory) {
                    $queryMc->where('sub_category', $subCategory);
                    $queryEssay->where('sub_category', $subCategory);
                }

                $mcQuestions = $queryMc->latest()->paginate(10, ['*'], 'mc_page');
                $essayQuestions = $queryEssay->latest()->paginate(10, ['*'], 'essay_page');
                
                // Get available sub categories for filter
                $availableSubCategories = Evaluation::where('category', $category)
                    ->whereNotNull('sub_category')
                    ->distinct()
                    ->pluck('sub_category')
                    ->sort()
                    ->values();

                // Standard Sub Categories (ensure these always appear in filter/modal even if no data yet)
                $standardSubCategories = collect(['General', 'Safety', 'Technical', 'Quality', 'SOP']);
                $availableSubCategories = $standardSubCategories->merge($availableSubCategories)->unique()->sort()->values();
                
                return view('super_admin.evaluation.index', compact('mcQuestions', 'essayQuestions', 'category', 'subCategory', 'availableSubCategories'));
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
            'questions.*.sub_category' => 'required|string',
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
                'sub_category' => $q['sub_category'],
                'question' => $q['question'],
                'option_a' => $q['type'] == 'multiple_choice' ? $q['option_a'] : null,
                'option_b' => $q['type'] == 'multiple_choice' ? $q['option_b'] : null,
                'option_c' => $q['type'] == 'multiple_choice' ? $q['option_c'] : null,
                'option_d' => $q['type'] == 'multiple_choice' ? $q['option_d'] : null,
                'correct_answer' => $q['type'] == 'multiple_choice' ? strtolower(trim($q['correct_answer'])) : null,
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
            'sub_category' => 'required|string',
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
            'sub_category' => $request->sub_category,
            'question' => $request->question,
            'option_a' => $request->type == 'multiple_choice' ? $request->option_a : null,
            'option_b' => $request->type == 'multiple_choice' ? $request->option_b : null,
            'option_c' => $request->type == 'multiple_choice' ? $request->option_c : null,
            'option_d' => $request->type == 'multiple_choice' ? $request->option_d : null,
            'correct_answer' => $request->type == 'multiple_choice' ? strtolower(trim($request->correct_answer)) : null,
        ]);

        return redirect()->route(auth()->user()->role . '.evaluation.index', [
            'category' => $evaluation->category,
            'tab' => $request->type == 'essay' ? 'essay' : 'mc'
        ])->with('success', 'Soal evaluasi berhasil diperbarui.');
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
            'file' => 'required|mimes:xlsx,xls,csv',
            'sub_category' => 'nullable|string'
        ]);

        try {
            Excel::import(new EvaluationImport($request->category, $request->sub_category), $request->file('file'));
            return redirect()->back()->with('success', 'Soal evaluasi berhasil diimport.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal import data: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        $template = [
            ['tipe', 'kategori', 'pertanyaan', 'opsi_a', 'opsi_b', 'opsi_c', 'opsi_d', 'kunci'],
            ['pilihan ganda', 'cover', 'Apa warna langit?', 'Merah', 'Biru', 'Hijau', 'Kuning', 'Biru'],
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
        
        // Get sub categories from answers before deleting
        $subCategories = EvaluationAnswer::where('user_id', $result->user_id)
            ->with(['evaluation' => function ($query) {
                $query->withTrashed();
            }])
            ->get()
            ->pluck('evaluation.sub_category')
            ->filter()
            ->unique()
            ->implode(', ');

        // Archive to History
        \App\Models\EvaluationHistory::create([
            'user_id' => $result->user_id,
            'score' => $result->score,
            'mc_score' => $result->mc_score,
            'essay_score' => $result->essay_score,
            'sub_categories' => $subCategories,
            'completed_at' => $result->created_at,
            'archived_at' => now(),
        ]);
        
        // Delete associated answers for this user
        EvaluationAnswer::where('user_id', $result->user_id)->delete();
        
        $result->delete();
        return redirect()->back()->with('success', 'Hasil evaluasi berhasil direset dan diarsipkan. User dapat mengerjakan ulang.');
    }

    public function results(Request $request)
    {
        $passingGrade = Setting::getValue('evaluation_passing_grade', 70);

        if (auth()->user()->role == 'super_admin' || auth()->user()->role == 'admin') {
            // Get Sub Categories for Filter
            $defaultCategories = ['General', 'Safety', 'Technical', 'Quality', 'SOP'];
            $existingCategories = Evaluation::withTrashed()->distinct()->pluck('sub_category')->filter()->toArray();
            $subCategories = array_values(array_unique(array_merge($defaultCategories, $existingCategories)));

            // 1. Calculate Stats (Always)
            $allDivisions = ['cover', 'case', 'inner', 'endplate'];
            $stats = [];
            foreach ($allDivisions as $div) {
                $statQuery = EvaluationResult::whereHas('user', function ($q) use ($div) {
                    $q->where('division', $div);
                });
                
                $stats[$div] = [
                    'total' => (clone $statQuery)->count(),
                    'pending' => (clone $statQuery)->where('status', 'pending')->count(),
                    'graded' => (clone $statQuery)->where('status', 'graded')->count(),
                ];
            }

            // 2. Prepare Results Query (Always)
            // If view_all is requested, ignore division filter
            $division = $request->has('view_all') ? null : $request->division;
            
            // --- Active Results Query ---
            $query = EvaluationResult::with('user')->where('is_published', true);
            
            if ($division) {
                $query->whereHas('user', function ($q) use ($division) {
                    $q->where('division', $division);
                });
            }

            // Date Filtering
            if ($request->filled('start_date')) {
                $query->whereDate('created_at', '>=', $request->start_date);
            }
            if ($request->filled('end_date')) {
                $query->whereDate('created_at', '<=', $request->end_date);
            }

            // NIK Filtering
            if ($request->filled('nik')) {
                $query->whereHas('user', function ($q) use ($request) {
                    $q->where('nik', 'like', '%' . $request->nik . '%');
                });
            }
            
            // Status Kelulusan Filtering
            if ($request->filled('status_kelulusan')) {
                if ($request->status_kelulusan == 'lulus') {
                    $query->where('score', '>=', $passingGrade);
                } elseif ($request->status_kelulusan == 'tidak_lulus') {
                    $query->where('score', '<', $passingGrade);
                }
            }

            // Sub Category Filtering
            if ($request->filled('sub_category')) {
                $query->whereHas('answers.evaluation', function ($q) use ($request) {
                    $q->where('sub_category', $request->sub_category);
                });
            }

            $results = $query->latest()->paginate(10);
            
            // --- History Results Query (Disabled as per request) ---
            $histories = collect();

            $viewName = auth()->user()->role == 'super_admin' 
                ? 'super_admin.evaluation.results' 
                : 'admin.evaluation.results';

            return view($viewName, compact('results', 'division', 'histories', 'passingGrade', 'stats', 'subCategories'));
        }

        $query = EvaluationResult::with('user');

        if (auth()->user()->role == 'operator') {
            // 1. Get Sub Categories for Filter
            $defaultCategories = ['General', 'Safety', 'Technical', 'Quality', 'SOP'];
            $existingCategories = Evaluation::withTrashed()->distinct()->pluck('sub_category')->filter()->toArray();
            $subCategories = array_values(array_unique(array_merge($defaultCategories, $existingCategories)));

            // 2. Active Results Query
            $activeQuery = EvaluationResult::with(['user', 'answers.evaluation'])
                ->where('user_id', auth()->id())
                ->where('is_published', true);

            // Filters for Active
            if ($request->filled('status_kelulusan')) {
                if ($request->status_kelulusan == 'lulus') {
                    $activeQuery->where('score', '>=', $passingGrade);
                } elseif ($request->status_kelulusan == 'tidak_lulus') {
                    $activeQuery->where('score', '<', $passingGrade);
                }
            }
            if ($request->filled('start_date')) {
                $activeQuery->whereDate('created_at', '>=', $request->start_date);
            }
            if ($request->filled('end_date')) {
                $activeQuery->whereDate('created_at', '<=', $request->end_date);
            }
            if ($request->filled('sub_category')) {
                $activeQuery->whereHas('answers.evaluation', function ($q) use ($request) {
                    $q->where('sub_category', $request->sub_category);
                });
            }

            $activeResults = $activeQuery->get()->map(function($item) {
                $item->type = 'active';
                $item->sort_date = $item->created_at;
                // Try to get category from answers
                $firstAnswer = $item->answers->first();
                $item->category_name = $firstAnswer && $firstAnswer->evaluation ? $firstAnswer->evaluation->sub_category : '-';
                return $item;
            });

            // 3. History Results Query
            $historyQuery = \App\Models\EvaluationHistory::with('user')->where('user_id', auth()->id());

            // Filters for History
            if ($request->filled('status_kelulusan')) {
                if ($request->status_kelulusan == 'lulus') {
                    $historyQuery->where('score', '>=', $passingGrade);
                } elseif ($request->status_kelulusan == 'tidak_lulus') {
                    $historyQuery->where('score', '<', $passingGrade);
                }
            }
            if ($request->filled('start_date')) {
                $historyQuery->whereDate('archived_at', '>=', $request->start_date);
            }
            if ($request->filled('end_date')) {
                $historyQuery->whereDate('archived_at', '<=', $request->end_date);
            }
            
            // Sub Category Filtering for History
            if ($request->filled('sub_category')) {
                $historyQuery->where('sub_categories', 'like', '%' . $request->sub_category . '%');
            }

            $historyResults = $historyQuery->get()->map(function($item) {
                $item->type = 'history';
                $item->status = 'archived';
                $item->sort_date = $item->archived_at;
                $item->category_name = $item->sub_categories ?? '-';
                return $item;
            });

            // 4. Merge and Sort
            // Both Active and History are now filtered by sub_category if present.
            
            $merged = $activeResults->concat($historyResults)->sortByDesc('sort_date')->values();

            // 5. Paginate
            $page = $request->get('page', 1);
            $perPage = 10;
            $results = new \Illuminate\Pagination\LengthAwarePaginator(
                $merged->forPage($page, $perPage),
                $merged->count(),
                $perPage,
                $page,
                ['path' => $request->url(), 'query' => $request->query()]
            );

            return view('operator.evaluation.results', compact('results', 'passingGrade', 'subCategories'));
        }

        $results = $query->latest()->paginate(10);
        
        return view('admin.evaluation.results', compact('results'));
    }

    public function exportResults(Request $request)
    {
        $exportType = $request->input('export_type', 'all'); // 'all', 'active', 'history'
        $passingGrade = Setting::getValue('evaluation_passing_grade', 70);

        $activeResults = collect();
        $historyResults = collect();

        // 1. Fetch Active Results
        if ($exportType == 'all' || $exportType == 'active') {
            $query = EvaluationResult::with('user');

            if (auth()->user()->role == 'operator') {
                $query->where('user_id', auth()->id());
            }

            if (auth()->user()->role == 'super_admin') {
                // If view_all is requested, ignore division filter
                if (!$request->has('view_all') && $request->has('division') && $request->division != '') {
                    $query->whereHas('user', function ($q) use ($request) {
                        $q->where('division', $request->division);
                    });
                }
            }

            // Date Filtering
            if ($request->filled('start_date')) {
                $query->whereDate('created_at', '>=', $request->start_date);
            }
            if ($request->filled('end_date')) {
                $query->whereDate('created_at', '<=', $request->end_date);
            }

            if ($request->filled('nik')) {
                $query->whereHas('user', function ($q) use ($request) {
                    $q->where('nik', 'like', '%' . $request->nik . '%');
                });
            }

            // Status Kelulusan Filtering
            if ($request->filled('status_kelulusan')) {
                if ($request->status_kelulusan == 'lulus') {
                    $query->where('score', '>=', $passingGrade);
                } elseif ($request->status_kelulusan == 'tidak_lulus') {
                    $query->where('score', '<', $passingGrade);
                }
            }

            // Sub Category Filtering
            if ($request->filled('sub_category')) {
                $query->whereHas('answers.evaluation', function ($q) use ($request) {
                    $q->where('sub_category', $request->sub_category);
                });
            }

            $activeResults = $query->latest()->get();
        }
        
        // 2. Fetch Histories
        if ($exportType == 'all' || $exportType == 'history') {
            $historyQuery = \App\Models\EvaluationHistory::with('user');
            
            if (auth()->user()->role == 'operator') {
                 $historyQuery->where('user_id', auth()->id());
            }
            
            if (auth()->user()->role == 'super_admin') {
                // If view_all is requested, ignore division filter
                if (!$request->has('view_all') && $request->has('division') && $request->division != '') {
                    $historyQuery->whereHas('user', function ($q) use ($request) {
                        $q->where('division', $request->division);
                    });
                }
            }
            
            // Date Filtering (History uses completed_at)
            if ($request->filled('start_date')) {
                $historyQuery->whereDate('completed_at', '>=', $request->start_date);
            }
            if ($request->filled('end_date')) {
                $historyQuery->whereDate('completed_at', '<=', $request->end_date);
            }

            if ($request->filled('nik')) {
                $historyQuery->whereHas('user', function ($q) use ($request) {
                    $q->where('nik', 'like', '%' . $request->nik . '%');
                });
            }

            // Status Kelulusan Filtering
            if ($request->filled('status_kelulusan')) {
                if ($request->status_kelulusan == 'lulus') {
                    $historyQuery->where('score', '>=', $passingGrade);
                } elseif ($request->status_kelulusan == 'tidak_lulus') {
                    $historyQuery->where('score', '<', $passingGrade);
                }
            }

            // Sub Category Filtering for History
            if ($request->filled('sub_category')) {
                $historyQuery->where('sub_categories', 'like', '%' . $request->sub_category . '%');
            }

            $historyResults = $historyQuery->latest('archived_at')->get();
        }
        
        // Merge collections
        $merged = $activeResults->concat($historyResults);

        $fileName = 'Laporan_Hasil_Evaluasi';
        if (!$request->has('view_all') && $request->has('division') && $request->division != '') {
            $fileName .= '_' . ucfirst($request->division);
        } else {
            $fileName .= '_Semua_Bagian';
        }
        
        if ($exportType == 'active') {
            $fileName .= '_Active_Only';
        } elseif ($exportType == 'history') {
            $fileName .= '_Reset_History';
        }

        if ($request->filled('status_kelulusan')) {
            $fileName .= '_' . ucfirst($request->status_kelulusan);
        }

        if ($request->filled('sub_category')) {
            $fileName .= '_' . ucfirst(Str::slug($request->sub_category));
        }

        $fileName .= '_' . date('Y-m-d_H-i-s') . '.xlsx';

        // Pass passing grade to export class
        return Excel::download(new EvaluationExport($merged, $passingGrade), $fileName);
    }

    public function verification(Request $request)
    {
        if (auth()->user()->role !== 'super_admin') {
            abort(403);
        }

        $passingGrade = Setting::getValue('evaluation_passing_grade', 70);
        $defaultCategories = ['General', 'Safety', 'Technical', 'Quality', 'SOP'];
        $existingCategories = Evaluation::withTrashed()->distinct()->pluck('sub_category')->filter()->toArray();
        $subCategories = array_values(array_unique(array_merge($defaultCategories, $existingCategories)));

        // 1. Calculate Stats for Verification (Unpublished)
        $allDivisions = ['cover', 'case', 'inner', 'endplate'];
        $stats = [];
        foreach ($allDivisions as $div) {
            $statQuery = EvaluationResult::whereHas('user', function ($q) use ($div) {
                $q->where('division', $div);
            })->where('is_published', false);
            
            $stats[$div] = [
                'total' => (clone $statQuery)->count(),
                'pending' => (clone $statQuery)->where('status', 'pending')->count(),
                'graded' => (clone $statQuery)->where('status', 'graded')->count(),
            ];
        }

        // 2. Prepare Results Query
        $division = $request->has('view_all') ? null : $request->division;
        
        $query = EvaluationResult::with('user')->where('is_published', false);
        
        if ($division) {
            $query->whereHas('user', function ($q) use ($division) {
                $q->where('division', $division);
            });
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        if ($request->filled('nik')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('nik', 'like', '%' . $request->nik . '%');
            });
        }
        
        if ($request->filled('status_kelulusan')) {
            if ($request->status_kelulusan == 'lulus') {
                $query->where('score', '>=', $passingGrade);
            } elseif ($request->status_kelulusan == 'tidak_lulus') {
                $query->where('score', '<', $passingGrade);
            }
        }

        if ($request->filled('sub_category')) {
            $query->whereHas('answers.evaluation', function ($q) use ($request) {
                $q->where('sub_category', $request->sub_category);
            });
        }

        $results = $query->latest()->paginate(10);
        
        return view('super_admin.evaluation.verification', compact('results', 'division', 'passingGrade', 'stats', 'subCategories'));
    }

    public function publishAll(Request $request)
    {
        if (auth()->user()->role !== 'super_admin') {
            abort(403);
        }
        
        $query = EvaluationResult::where('is_published', false);
        
        if ($request->filled('division')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('division', $request->division);
            });
        }
        
        $count = $query->update(['is_published' => true]);
        
        return redirect()->route('super_admin.evaluation.results', $request->all())->with('success', "$count data evaluasi berhasil dipublish dan ditampilkan di daftar utama.");
    }

    // Grading Methods
    public function grade($id)
    {
        $result = EvaluationResult::with('user')->findOrFail($id);
        
        // Get answers for this user
        // We assume the user took all current questions or we just find answers that exist
        $answers = EvaluationAnswer::with(['evaluation' => function ($q) {
                $q->withTrashed();
            }])
            ->where('user_id', $result->user_id)
            ->get();
            
        // Separate answers
        $mcAnswers = $answers->filter(function ($answer) {
            return $answer->evaluation && $answer->evaluation->type === 'multiple_choice';
        });
        
        $essayAnswers = $answers->filter(function ($answer) {
            return $answer->evaluation && $answer->evaluation->type === 'essay';
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
        
        if (!$result->is_published) {
             return redirect()->route('super_admin.evaluation.verification', ['division' => $result->user->division])->with('success', 'Penilaian berhasil disimpan. Data masih dalam status verifikasi.');
        }
        
        return redirect()->route(auth()->user()->role . '.evaluation.results', ['division' => $result->user->division])->with('success', 'Penilaian berhasil disimpan. Skor akhir telah diperbarui.');
    }

    private function recalculateAndSave(EvaluationResult $result)
    {
        // Get all answers for this user
        $answers = EvaluationAnswer::where('user_id', $result->user_id)
            ->whereHas('evaluation', function($q) {
                $q->withTrashed();
            })
            ->get();
        
        if ($answers->count() > 0) {
            $totalScore = $answers->sum('score');
            $averageScore = $totalScore / $answers->count();
            
            $result->score = round($averageScore);
            $result->status = 'graded';
            $result->save();
        }
    }
}

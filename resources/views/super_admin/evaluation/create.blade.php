@extends('layouts.super_admin')

@section('title', 'Tambah Soal ' . ($type == 'essay' ? 'Essay' : 'Pilihan Ganda') . ($category ? ' - ' . ucfirst($category) : ''))

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h2 class="mb-0">Tambah Soal {{ $type == 'essay' ? 'Essay' : 'Pilihan Ganda' }} <span class="text-primary">{{ $category ? '(' . ucfirst($category) . ')' : '' }}</span></h2>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('super_admin.evaluation.store') }}" method="POST" id="evaluation-form">
            @csrf
            
            <div id="questions-container">
                <!-- Questions will be added here -->
            </div>

            <div class="d-flex justify-content-between mt-4">
                <button type="button" class="btn btn-outline-danger" id="add-question-btn">
                    <i class="fas fa-plus me-2"></i>Tambah Soal
                </button>
                <div>
                    <a href="{{ route('super_admin.evaluation.index', $category ? ['category' => $category] : []) }}" class="btn btn-secondary me-2">Batal</a>
                    <button type="submit" class="btn btn-danger" style="background-color: var(--primary-color);">Simpan Semua Soal</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('questions-container');
        const addBtn = document.getElementById('add-question-btn');
        const questionType = "{{ $type }}";
        const defaultCategory = "{{ $category ?? '' }}";
        let questionCount = 0;

        function addQuestion() {
            const index = questionCount++;
            let optionsHtml = '';
            
            if (questionType === 'multiple_choice') {
                optionsHtml = `
                    <div class="multiple-choice-options">
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label class="form-label small text-muted">Opsi A</label>
                                <input type="text" class="form-control form-control-sm" name="questions[${index}][option_a]" required>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label small text-muted">Opsi B</label>
                                <input type="text" class="form-control form-control-sm" name="questions[${index}][option_b]" required>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label small text-muted">Opsi C</label>
                                <input type="text" class="form-control form-control-sm" name="questions[${index}][option_c]" required>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label small text-muted">Opsi D</label>
                                <input type="text" class="form-control form-control-sm" name="questions[${index}][option_d]" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kunci Jawaban</label>
                            <select class="form-select" name="questions[${index}][correct_answer]" required>
                                <option value="">Pilih Jawaban Benar</option>
                                <option value="a">A</option>
                                <option value="b">B</option>
                                <option value="c">C</option>
                                <option value="d">D</option>
                            </select>
                        </div>
                    </div>
                `;
            }

            const html = `
                <div class="card mb-3 border question-card" id="question-${index}">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold">Soal #${index + 1} (${questionType === 'essay' ? 'Essay' : 'Pilihan Ganda'})</h6>
                        <button type="button" class="btn btn-sm btn-danger remove-question">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    <div class="card-body">
                        <input type="hidden" name="questions[${index}][type]" value="${questionType}">
                        <input type="hidden" name="questions[${index}][category]" value="${defaultCategory}">
                        
                        <div class="mb-3">
                            <label class="form-label">Kategori Soal</label>
                            <select class="form-select" name="questions[${index}][sub_category]" required>
                                <option value="" selected disabled>Pilih Kategori</option>
                                <option value="General">General</option>
                                <option value="Safety">Safety</option>
                                <option value="Technical">Technical</option>
                                <option value="Quality">Quality</option>
                                <option value="SOP">SOP</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Pertanyaan</label>
                            <textarea class="form-control" name="questions[${index}][question]" rows="3" required placeholder="Tuliskan pertanyaan disini..."></textarea>
                        </div>

                        ${optionsHtml}
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
        }

        // Add initial question
        addQuestion();

        addBtn.addEventListener('click', addQuestion);

        container.addEventListener('click', function(e) {
            // Remove question
            if (e.target.closest('.remove-question')) {
                const card = e.target.closest('.question-card');
                // Don't allow removing the last question if it's the only one? 
                // Currently allows removing all, which might be fine, but usually we want at least one.
                // But let's keep it simple as before.
                card.remove();
            }
        });
    });
</script>
@endsection

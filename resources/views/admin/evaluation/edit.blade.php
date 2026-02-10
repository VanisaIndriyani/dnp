@extends('layouts.admin')

@section('title', 'Edit Soal Evaluasi')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h2 class="mb-0">Edit Soal Evaluasi</h2>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.evaluation.update', $evaluation) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="question" class="form-label">Pertanyaan</label>
                <textarea class="form-control @error('question') is-invalid @enderror" id="question" name="question" rows="3" required>{{ old('question', $evaluation->question) }}</textarea>
                @error('question')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="option_a" class="form-label">Opsi A</label>
                    <input type="text" class="form-control @error('option_a') is-invalid @enderror" id="option_a" name="option_a" value="{{ old('option_a', $evaluation->option_a) }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="option_b" class="form-label">Opsi B</label>
                    <input type="text" class="form-control @error('option_b') is-invalid @enderror" id="option_b" name="option_b" value="{{ old('option_b', $evaluation->option_b) }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="option_c" class="form-label">Opsi C</label>
                    <input type="text" class="form-control @error('option_c') is-invalid @enderror" id="option_c" name="option_c" value="{{ old('option_c', $evaluation->option_c) }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="option_d" class="form-label">Opsi D</label>
                    <input type="text" class="form-control @error('option_d') is-invalid @enderror" id="option_d" name="option_d" value="{{ old('option_d', $evaluation->option_d) }}" required>
                </div>
            </div>

            <div class="mb-3">
                <label for="correct_answer" class="form-label">Kunci Jawaban</label>
                <select class="form-select @error('correct_answer') is-invalid @enderror" id="correct_answer" name="correct_answer" required>
                    <option value="">Pilih Kunci Jawaban</option>
                    <option value="a" {{ old('correct_answer', $evaluation->correct_answer) == 'a' ? 'selected' : '' }}>A</option>
                    <option value="b" {{ old('correct_answer', $evaluation->correct_answer) == 'b' ? 'selected' : '' }}>B</option>
                    <option value="c" {{ old('correct_answer', $evaluation->correct_answer) == 'c' ? 'selected' : '' }}>C</option>
                    <option value="d" {{ old('correct_answer', $evaluation->correct_answer) == 'd' ? 'selected' : '' }}>D</option>
                </select>
                @error('correct_answer')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-end mt-4">
                <a href="{{ route('admin.evaluation.index') }}" class="btn btn-secondary me-2">Batal</a>
                <button type="submit" class="btn btn-danger" style="background-color: var(--primary-color);">Update Soal</button>
            </div>
        </form>
    </div>
</div>
@endsection

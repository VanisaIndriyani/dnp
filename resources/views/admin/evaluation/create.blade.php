@extends('layouts.admin')

@section('title', 'Tambah Soal Evaluasi')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h2 class="mb-0 fw-bold text-dark">Tambah Soal Evaluasi</h2>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header text-white" style="background-color: var(--primary-color);">
        <h5 class="mb-0 fw-bold"><i class="fas fa-plus-circle me-2"></i>Formulir Soal Baru</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.evaluation.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="question" class="form-label fw-bold">Pertanyaan</label>
                <textarea class="form-control @error('question') is-invalid @enderror" id="question" name="question" rows="3" required placeholder="Tuliskan pertanyaan evaluasi disini...">{{ old('question') }}</textarea>
                @error('question')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="option_a" class="form-label fw-bold">Opsi A</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-secondary fw-bold">A</span>
                        <input type="text" class="form-control @error('option_a') is-invalid @enderror" id="option_a" name="option_a" value="{{ old('option_a') }}" required placeholder="Pilihan jawaban A">
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="option_b" class="form-label fw-bold">Opsi B</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-secondary fw-bold">B</span>
                        <input type="text" class="form-control @error('option_b') is-invalid @enderror" id="option_b" name="option_b" value="{{ old('option_b') }}" required placeholder="Pilihan jawaban B">
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="option_c" class="form-label fw-bold">Opsi C</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-secondary fw-bold">C</span>
                        <input type="text" class="form-control @error('option_c') is-invalid @enderror" id="option_c" name="option_c" value="{{ old('option_c') }}" required placeholder="Pilihan jawaban C">
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="option_d" class="form-label fw-bold">Opsi D</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-secondary fw-bold">D</span>
                        <input type="text" class="form-control @error('option_d') is-invalid @enderror" id="option_d" name="option_d" value="{{ old('option_d') }}" required placeholder="Pilihan jawaban D">
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="correct_answer" class="form-label fw-bold">Kunci Jawaban</label>
                <select class="form-select @error('correct_answer') is-invalid @enderror" id="correct_answer" name="correct_answer" required>
                    <option value="">Pilih Kunci Jawaban</option>
                    <option value="a" {{ old('correct_answer') == 'a' ? 'selected' : '' }}>A</option>
                    <option value="b" {{ old('correct_answer') == 'b' ? 'selected' : '' }}>B</option>
                    <option value="c" {{ old('correct_answer') == 'c' ? 'selected' : '' }}>C</option>
                    <option value="d" {{ old('correct_answer') == 'd' ? 'selected' : '' }}>D</option>
                </select>
                @error('correct_answer')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-end mt-4 gap-2">
                <a href="{{ route('admin.evaluation.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-1"></i> Batal
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Simpan Soal
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

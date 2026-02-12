@extends('layouts.super_admin')

@section('title', 'Edit Soal Evaluasi (' . ucfirst($evaluation->category) . ')')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h2 class="mb-0">Edit Soal Evaluasi <span class="text-primary">({{ ucfirst($evaluation->category) }})</span></h2>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('super_admin.evaluation.update', $evaluation) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Tipe Soal</label>
                <div class="d-flex gap-3">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="type" id="type_mc" value="multiple_choice" {{ old('type', $evaluation->type) == 'multiple_choice' ? 'checked' : '' }}>
                        <label class="form-check-label" for="type_mc">Pilihan Ganda</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="type" id="type_essay" value="essay" {{ old('type', $evaluation->type) == 'essay' ? 'checked' : '' }}>
                        <label class="form-check-label" for="type_essay">Essay</label>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <input type="hidden" name="category" value="{{ $evaluation->category }}">
            </div>

            <div class="mb-3">
                <label class="form-label">Kategori Soal</label>
                <select class="form-select" name="sub_category" required>
                    <option value="" disabled>Pilih Kategori</option>
                    <option value="General" {{ old('sub_category', $evaluation->sub_category) == 'General' ? 'selected' : '' }}>General</option>
                    <option value="Safety" {{ old('sub_category', $evaluation->sub_category) == 'Safety' ? 'selected' : '' }}>Safety</option>
                    <option value="Technical" {{ old('sub_category', $evaluation->sub_category) == 'Technical' ? 'selected' : '' }}>Technical</option>
                    <option value="Quality" {{ old('sub_category', $evaluation->sub_category) == 'Quality' ? 'selected' : '' }}>Quality</option>
                    <option value="SOP" {{ old('sub_category', $evaluation->sub_category) == 'SOP' ? 'selected' : '' }}>SOP</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="question" class="form-label">Pertanyaan</label>
                <textarea class="form-control @error('question') is-invalid @enderror" id="question" name="question" rows="3" required>{{ old('question', $evaluation->question) }}</textarea>
                @error('question')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Options for Multiple Choice -->
            <div id="mc-options" style="{{ old('type', $evaluation->type) == 'essay' ? 'display: none;' : '' }}">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="option_a" class="form-label">Opsi A</label>
                        <input type="text" class="form-control mc-input" id="option_a" name="option_a" value="{{ old('option_a', $evaluation->option_a) }}" {{ old('type', $evaluation->type) != 'essay' ? 'required' : '' }}>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="option_b" class="form-label">Opsi B</label>
                        <input type="text" class="form-control mc-input" id="option_b" name="option_b" value="{{ old('option_b', $evaluation->option_b) }}" {{ old('type', $evaluation->type) != 'essay' ? 'required' : '' }}>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="option_c" class="form-label">Opsi C</label>
                        <input type="text" class="form-control mc-input" id="option_c" name="option_c" value="{{ old('option_c', $evaluation->option_c) }}" {{ old('type', $evaluation->type) != 'essay' ? 'required' : '' }}>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="option_d" class="form-label">Opsi D</label>
                        <input type="text" class="form-control mc-input" id="option_d" name="option_d" value="{{ old('option_d', $evaluation->option_d) }}" {{ old('type', $evaluation->type) != 'essay' ? 'required' : '' }}>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Kunci Jawaban</label>
                    <select class="form-select mc-input" id="correct_answer" name="correct_answer" {{ old('type', $evaluation->type) != 'essay' ? 'required' : '' }}>
                        <option value="">Pilih Kunci Jawaban</option>
                        <option value="a" {{ old('correct_answer', $evaluation->correct_answer) == 'a' ? 'selected' : '' }}>A</option>
                        <option value="b" {{ old('correct_answer', $evaluation->correct_answer) == 'b' ? 'selected' : '' }}>B</option>
                        <option value="c" {{ old('correct_answer', $evaluation->correct_answer) == 'c' ? 'selected' : '' }}>C</option>
                        <option value="d" {{ old('correct_answer', $evaluation->correct_answer) == 'd' ? 'selected' : '' }}>D</option>
                    </select>
                </div>
            </div>

            <div class="d-flex justify-content-end mt-4">
                <a href="{{ route('super_admin.evaluation.index', ['category' => $evaluation->category]) }}" class="btn btn-secondary me-2">Batal</a>
                <button type="submit" class="btn btn-danger" style="background-color: var(--primary-color);">Update Soal</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const mcOptions = document.getElementById('mc-options');
        const mcInputs = document.querySelectorAll('.mc-input');
        const typeRadios = document.querySelectorAll('input[name="type"]');

        typeRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'essay') {
                    mcOptions.style.display = 'none';
                    mcInputs.forEach(input => input.removeAttribute('required'));
                } else {
                    mcOptions.style.display = 'block';
                    mcInputs.forEach(input => input.setAttribute('required', 'required'));
                }
            });
        });
    });
</script>
@endsection

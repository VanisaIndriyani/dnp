@extends('layouts.super_admin')

@section('title', 'Penilaian Essay')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Penilaian Jawaban Evaluasi</h1>
            <p class="mb-0 text-muted">Review dan berikan nilai untuk jawaban essay operator.</p>
        </div>
        <a href="{{ route('super_admin.evaluation.results') }}" class="btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50 me-2"></i>Kembali ke Hasil
        </a>
    </div>

    <div class="row">
        <!-- Sidebar Info (Sticky on large screens) -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm sticky-top" style="top: 20px; z-index: 1;">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h6 class="m-0 fw-bold text-primary"><i class="fas fa-user-circle me-2"></i>Informasi Peserta</h6>
                </div>
                <div class="card-body pt-0">
                    <div class="mb-3">
                        <label class="small text-muted text-uppercase fw-bold">Nama Operator</label>
                        <h5 class="fw-bold text-dark">{{ $result->user->name }}</h5>
                    </div>
                    <div class="mb-3">
                        <label class="small text-muted text-uppercase fw-bold">NIK</label>
                        <p class="h6">{{ $result->user->nik }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="small text-muted text-uppercase fw-bold">Divisi</label>
                        <p class="h6">{{ $result->user->division ?? '-' }}</p>
                    </div>
                    <hr>
                    
                    <!-- Score Breakdown -->
                    <div class="mb-3">
                        <label class="small text-muted text-uppercase fw-bold mb-2">Rincian Nilai</label>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Pilihan Ganda</span>
                            <span class="badge bg-success bg-opacity-10 text-success border border-success">{{ $mcScore }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Essay</span>
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary">{{ $essayScore }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center border-top pt-2 mt-2">
                            <span class="fw-bold text-dark">Total Akhir</span>
                            <span class="badge bg-info fs-6">{{ $result->score }}</span>
                        </div>
                    </div>

                    <div class="mt-3">
                        <div class="alert alert-warning small mb-0">
                            <i class="fas fa-info-circle me-1"></i> Skor Essay dan Total akan diperbarui setelah Anda menyimpan.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Questions List -->
        <div class="col-lg-8">
            <form action="{{ route('super_admin.evaluation.storeGrade', $result->id) }}" method="POST" id="gradingForm">
                @csrf
                
                <!-- Section 1: Multiple Choice -->
                @if($mcAnswers->count() > 0)
                <div class="mb-4">
                    <h5 class="fw-bold text-gray-800 mb-3 border-start border-5 border-success ps-3">
                        Bagian I: Pilihan Ganda 
                        <span class="badge bg-success ms-2">{{ $mcScore }}</span>
                    </h5>
                    
                    @foreach($mcAnswers as $index => $answer)
                        @php
                            $isCorrect = $answer->score == 100;
                            $colorClass = $isCorrect ? 'success' : 'danger';
                            $textClass = 'text-' . $colorClass;
                            $icon = $isCorrect ? 'fa-check-circle' : 'fa-times-circle';
                        @endphp

                        <div class="card border-0 shadow-sm mb-3 overflow-hidden">
                            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom-0">
                                <span class="fw-bold {{ $textClass }}"><i class="fas {{ $icon }} me-2"></i>Soal #{{ $loop->iteration }}</span>
                                <span class="badge bg-light text-dark border">Max: 100</span>
                            </div>
                            <div class="card-body p-4 pt-0">
                                <h5 class="text-dark mb-3" style="line-height: 1.6;">{{ $answer->evaluation->question }}</h5>
                                <div class="p-3 rounded bg-white border border-{{ $colorClass }} bg-opacity-10">
                                    <p class="mb-0 text-dark">{{ $answer->answer ?: '-' }}</p>
                                </div>
                                <div class="mt-2 text-end">
                                    @if(!$isCorrect)
                                        <small class="text-danger fw-bold">Kunci: {{ strtoupper($answer->evaluation->correct_answer) }}</small>
                                    @endif
                                    <input type="hidden" name="grades[{{ $answer->id }}]" value="{{ $answer->score }}">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @endif

                <!-- Section 2: Essay -->
                @if($essayAnswers->count() > 0)
                <div class="mb-4">
                    <h5 class="fw-bold text-gray-800 mb-3 border-start border-5 border-primary ps-3">
                        Bagian II: Essay
                        <span class="badge bg-primary ms-2">{{ $essayScore }}</span>
                    </h5>

                    @foreach($essayAnswers as $index => $answer)
                        <div class="card border-0 shadow-sm mb-4 overflow-hidden border-top border-5 border-primary">
                            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom-0">
                                <span class="fw-bold text-primary"><i class="fas fa-pen-fancy me-2"></i>Soal Essay #{{ $loop->iteration }}</span>
                                <span class="badge bg-light text-dark border">Max: 100</span>
                            </div>

                            <div class="card-body p-4 pt-0">
                                <!-- Question -->
                                <div class="mb-4">
                                    <label class="small text-muted fw-bold mb-2">PERTANYAAN:</label>
                                    <h5 class="text-dark" style="line-height: 1.6;">{{ $answer->evaluation->question }}</h5>
                                </div>

                                <!-- User Answer -->
                                <div class="mb-4">
                                    <label class="small text-muted fw-bold mb-2">JAWABAN USER:</label>
                                    <div class="p-3 rounded bg-white border border-primary">
                                        <p class="mb-0 text-dark" style="white-space: pre-wrap;">{{ $answer->answer ?: '-' }}</p>
                                    </div>
                                </div>

                                <!-- Grading Section -->
                                <div class="grading-section bg-light p-3 rounded border">
                                    <label for="grade_{{ $answer->id }}" class="form-label fw-bold text-primary mb-2">
                                        <i class="fas fa-star me-1"></i> Berikan Nilai (0-100)
                                    </label>
                                    <div class="row g-3 align-items-center">
                                        <div class="col-sm-4">
                                            <div class="input-group">
                                                <input type="number" class="form-control form-control-lg border-primary fw-bold text-center" 
                                                       id="grade_{{ $answer->id }}" 
                                                       name="grades[{{ $answer->id }}]" 
                                                       value="{{ $answer->score }}" 
                                                       min="0" max="100" required>
                                                <span class="input-group-text">/ 100</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="btn-group w-100" role="group">
                                                <button type="button" class="btn btn-outline-danger btn-sm" onclick="setScore({{ $answer->id }}, 0)">0 (Salah)</button>
                                                <button type="button" class="btn btn-outline-warning btn-sm" onclick="setScore({{ $answer->id }}, 50)">50 (Setengah)</button>
                                                <button type="button" class="btn btn-outline-success btn-sm" onclick="setScore({{ $answer->id }}, 100)">100 (Sempurna)</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @endif

                <!-- Submit Button -->
                <div class="card border-0 shadow-sm mb-5">
                    <div class="card-body p-4">
                        <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold py-3 shadow-sm">
                            <i class="fas fa-save me-2"></i>Simpan Semua Penilaian
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function setScore(id, score) {
        document.getElementById('grade_' + id).value = score;
    }
</script>
@endsection

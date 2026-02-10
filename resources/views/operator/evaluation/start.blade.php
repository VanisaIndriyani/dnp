@extends('layouts.operator')

@section('title', 'Mulai Evaluasi')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    .option-card {
        border: 2px solid #e9ecef;
        border-radius: 10px;
        padding: 15px 20px;
        margin-bottom: 15px;
        cursor: pointer;
        transition: all 0.2s ease;
        position: relative;
        display: flex;
        align-items: center;
    }
    .option-card:hover {
        border-color: #C62828;
        background-color: #fff5f5;
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    }
    .option-card.selected {
        border-color: #C62828 !important;
        background-color: #ffebee !important;
        font-weight: 600;
    }
    .option-card .form-check-input {
        margin-right: 15px;
        margin-top: 0;
        transform: scale(1.2);
        border-color: #C62828;
    }
    .option-card .form-check-input:checked {
        background-color: #C62828 !important;
        border-color: #C62828 !important;
    }
    .option-card .form-check-label {
        cursor: pointer;
        width: 100%;
        margin-bottom: 0;
    }
    .progress-container {
        height: 10px;
        background-color: #e9ecef;
        border-radius: 5px;
        margin-bottom: 30px;
        overflow: hidden;
    }
    .progress-fill {
        height: 100%;
        background-color: #C62828;
        transition: width 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .question-text {
        font-size: 1.3rem;
        font-weight: 600;
        color: #2c3e50;
        line-height: 1.6;
        margin-bottom: 25px;
    }
    .step-badge {
        background-color: #ffebee;
        color: #C62828;
        padding: 5px 15px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.9rem;
        margin-bottom: 15px;
        display: inline-block;
    }
    .fade-in {
        animation: fadeIn 0.5s ease-in-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .hover-scale {
        transition: transform 0.2s;
    }
    .hover-scale:hover {
        transform: scale(1.05);
    }
</style>

<div class="row justify-content-center">
    <div class="col-md-10 col-lg-8">
        
        <!-- Start Screen -->
        <div id="start-screen" class="card text-center border-0 shadow-sm" style="border-radius: 15px;">
            <div class="card-body py-5">
                <div class="mb-4">
                    <div class="bg-danger text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                        <i class="fas fa-file-alt fa-3x"></i>
                    </div>
                </div>
                <h2 class="mb-3 fw-bold text-dark">Evaluasi Operator</h2>
                <p class="text-muted fs-5 mb-4">Bagian: <span class="badge bg-danger rounded-pill">{{ ucfirst(auth()->user()->division ?? 'Umum') }}</span></p>
                <p class="text-muted mb-4 fs-5">
                    Tes ini terdiri dari <strong class="text-danger">{{ $questions->count() }} soal</strong>.<br>
                    Silakan baca peraturan di bawah ini dengan seksama sebelum memulai.
                </p>
                
                <div class="alert alert-warning text-start d-inline-block p-4" style="border-left: 5px solid #ffc107; border-radius: 4px;">
                    <h5 class="alert-heading fw-bold"><i class="fas fa-exclamation-triangle me-2"></i>Peraturan Penting:</h5>
                    <ul class="mb-0 ps-3">
                        <li class="mb-2">Dilarang membuka tab lain atau aplikasi lain.</li>
                        <li class="mb-2">Dilarang keluar dari mode layar penuh (fullscreen).</li>
                        <li class="mb-2">Sistem akan memberikan peringatan jika terjadi pelanggaran.</li>
                        <li>Jika peringatan mencapai <strong>3 kali</strong>, jawaban akan <strong>dikumpulkan otomatis</strong>.</li>
                    </ul>
                </div>

                <div class="mt-5">
                    <button id="btn-start-exam" class="btn btn-danger btn-lg px-5 py-3 rounded-pill shadow hover-scale">
                        <i class="fas fa-play me-2"></i> Mulai Ujian Sekarang
                    </button>
                </div>
            </div>
        </div>

        <!-- Exam Container (Hidden initially) -->
        <div id="exam-container" class="card d-none border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
            <div class="card-header bg-white text-danger d-flex justify-content-between align-items-center py-3 border-bottom">
                <h5 class="mb-0 fw-bold"><i class="fas fa-pen-alt me-2"></i>Soal Evaluasi</h5>
                <div>
                    <span class="badge bg-danger text-white py-2 px-3 rounded-pill shadow-sm">
                        <i class="fas fa-exclamation-circle me-1"></i> Peringatan: <span id="warning-count">0</span>/3
                    </span>
                </div>
            </div>
            <div class="card-body p-4 p-md-5">
                @if($questions->count() > 0)
                    <div class="mb-2 d-flex justify-content-between text-muted small">
                        <span>Progress</span>
                        <span id="progress-text">0%</span>
                    </div>
                    <div class="progress-container">
                        <div class="progress-fill" id="progress-bar" style="width: 0%"></div>
                    </div>

                    <form id="exam-form" action="{{ route('operator.evaluation.submit') }}" method="POST">
                        @csrf
                        @foreach($questions as $index => $question)
                            <div class="question-step fade-in {{ $index === 0 ? '' : 'd-none' }}" id="step-{{ $index }}" data-index="{{ $index }}">
                                <div class="step-badge">Soal {{ $index + 1 }} dari {{ $questions->count() }}</div>
                                <h4 class="question-text">{{ $question->question }}</h4>
                                
                                <div class="options-container">
                                    @if($question->type == 'essay')
                                        <div class="mb-3">
                                            <textarea class="form-control" name="answers[{{ $question->id }}]" rows="5" placeholder="Tulis jawaban Anda di sini..." required></textarea>
                                        </div>
                                    @else
                                        <div class="option-card" onclick="selectOption(this)">
                                            <input class="form-check-input" type="radio" name="answers[{{ $question->id }}]" id="q{{ $question->id }}_a" value="a" required>
                                            <label class="form-check-label" for="q{{ $question->id }}_a">A. {{ $question->option_a }}</label>
                                        </div>
                                        <div class="option-card" onclick="selectOption(this)">
                                            <input class="form-check-input" type="radio" name="answers[{{ $question->id }}]" id="q{{ $question->id }}_b" value="b" required>
                                            <label class="form-check-label" for="q{{ $question->id }}_b">B. {{ $question->option_b }}</label>
                                        </div>
                                        <div class="option-card" onclick="selectOption(this)">
                                            <input class="form-check-input" type="radio" name="answers[{{ $question->id }}]" id="q{{ $question->id }}_c" value="c" required>
                                            <label class="form-check-label" for="q{{ $question->id }}_c">C. {{ $question->option_c }}</label>
                                        </div>
                                        <div class="option-card" onclick="selectOption(this)">
                                            <input class="form-check-input" type="radio" name="answers[{{ $question->id }}]" id="q{{ $question->id }}_d" value="d" required>
                                            <label class="form-check-label" for="q{{ $question->id }}_d">D. {{ $question->option_d }}</label>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                        
                        <div class="mt-5 d-flex justify-content-between">
                            <button type="button" id="btn-prev" class="btn btn-outline-secondary d-none">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </button>
                            <button type="button" id="btn-next" class="btn btn-danger">
                                Selanjutnya <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                            <button type="submit" id="btn-submit" class="btn btn-danger d-none">
                                <i class="fas fa-check me-2"></i>Kirim Jawaban
                            </button>
                        </div>
                    </form>
                @else
                    <div class="text-center py-5">
                        <h4 class="text-muted">Belum ada soal evaluasi yang tersedia.</h4>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const startScreen = document.getElementById('start-screen');
        const examContainer = document.getElementById('exam-container');
        const btnStart = document.getElementById('btn-start-exam');
        const examForm = document.getElementById('exam-form');
        const warningCountSpan = document.getElementById('warning-count');
        
        let warningCount = 0;
        const maxWarnings = 3;
        let isExamActive = false;

        // Make selectOption available globally
        window.selectOption = function(card) {
            // Find all options in the same container
            const container = card.closest('.options-container');
            const allOptions = container.querySelectorAll('.option-card');
            
            // Remove selected class from all
            allOptions.forEach(opt => opt.classList.remove('selected'));
            
            // Add selected class to clicked one
            card.classList.add('selected');
            
            // Check the radio input
            const radio = card.querySelector('input[type="radio"]');
            radio.checked = true;
        };

        // Function to Enter Fullscreen
        function openFullscreen() {
            const elem = document.documentElement;
            if (elem.requestFullscreen) {
                elem.requestFullscreen();
            } else if (elem.webkitRequestFullscreen) { /* Safari */
                elem.webkitRequestFullscreen();
            } else if (elem.msRequestFullscreen) { /* IE11 */
                elem.msRequestFullscreen();
            }
        }

        // Start Exam
        btnStart.addEventListener('click', function() {
            startScreen.classList.add('d-none');
            examContainer.classList.remove('d-none');
            isExamActive = true;
            openFullscreen();
        });

        // Wizard Navigation
        let currentStep = 0;
        const totalSteps = {{ $questions->count() }};
        const steps = document.querySelectorAll('.question-step');
        const btnPrev = document.getElementById('btn-prev');
        const btnNext = document.getElementById('btn-next');
        const btnSubmit = document.getElementById('btn-submit');
        const progressBar = document.getElementById('progress-bar');

        if (btnNext && btnPrev && btnSubmit) {
            function updateProgress(index) {
                const percent = ((index + 1) / totalSteps) * 100;
                if (progressBar) {
                    progressBar.style.width = percent + '%';
                }
                const progressText = document.getElementById('progress-text');
                if (progressText) {
                    progressText.innerText = Math.round(percent) + '%';
                }
            }

            function showStep(index) {
                steps.forEach((step, i) => {
                    if (i === index) {
                        step.classList.remove('d-none');
                    } else {
                        step.classList.add('d-none');
                    }
                });

                // Update buttons
                if (index === 0) {
                    btnPrev.classList.add('d-none');
                } else {
                    btnPrev.classList.remove('d-none');
                }

                if (index === totalSteps - 1) {
                    btnNext.classList.add('d-none');
                    btnSubmit.classList.remove('d-none');
                } else {
                    btnNext.classList.remove('d-none');
                    btnSubmit.classList.add('d-none');
                }

                updateProgress(index);
            }

            // Initialize progress
            updateProgress(0);

            btnPrev.addEventListener('click', function() {
                if (currentStep > 0) {
                    currentStep--;
                    showStep(currentStep);
                }
            });

            btnNext.addEventListener('click', function() {
                // Validate current step
                const currentStepEl = steps[currentStep];
                
                // Check Radio buttons
                const radios = currentStepEl.querySelectorAll('input[type="radio"]');
                let answered = false;
                
                if (radios.length > 0) {
                    radios.forEach(input => {
                        if (input.checked) answered = true;
                    });
                } else {
                    // Check Textarea for Essay
                    const textarea = currentStepEl.querySelector('textarea');
                    if (textarea && textarea.value.trim() !== '') {
                        answered = true;
                    }
                }

                if (!answered) {
                    alert('Silakan isi jawaban terlebih dahulu sebelum lanjut ke soal berikutnya.');
                    return;
                }

                if (currentStep < totalSteps - 1) {
                    currentStep++;
                    showStep(currentStep);
                }
            });
        }

        // Anti-Cheat: Visibility Change (Tab Switch)
        document.addEventListener("visibilitychange", function() {
            if (isExamActive && document.hidden) {
                handleViolation("Anda terdeteksi meninggalkan halaman ujian (pindah tab/minimize)!");
            }
        });

        // Anti-Cheat: Window Blur (Focus Loss - e.g. clicking other app)
        window.addEventListener("blur", function() {
            if (isExamActive) {
                // Small delay to prevent false positives during interaction
                setTimeout(() => {
                    if (document.activeElement.tagName === "IFRAME") return; // Ignore iframe clicks
                    handleViolation("Anda terdeteksi membuka aplikasi lain!");
                }, 500);
            }
        });

        // Anti-Cheat: Fullscreen Exit
        document.addEventListener('fullscreenchange', (event) => {
            if (isExamActive && !document.fullscreenElement) {
                handleViolation("Anda keluar dari mode layar penuh!");
            }
        });

        // Handle Violation
        function handleViolation(message) {
            if (!isExamActive) return;

            warningCount++;
            warningCountSpan.innerText = warningCount;
            
            alert(`PERINGATAN ${warningCount}/${maxWarnings}\n\n${message}\n\nJangan ulangi atau ujian akan dikumpulkan otomatis!`);

            // Re-enter fullscreen if exited
            if (!document.fullscreenElement) {
                openFullscreen();
            }

            if (warningCount >= maxWarnings) {
                autoSubmit();
            }
        }

        // Auto Submit
        function autoSubmit() {
            isExamActive = false;
            alert("Batas peringatan terlampaui. Ujian Anda akan dikumpulkan otomatis.");
            examForm.submit();
        }

        // Disable Context Menu (Right Click)
        document.addEventListener('contextmenu', event => event.preventDefault());

        // Disable Copy/Cut/Paste
        document.addEventListener('copy', (e) => { e.preventDefault(); });
        document.addEventListener('cut', (e) => { e.preventDefault(); });
        document.addEventListener('paste', (e) => { e.preventDefault(); });

        // Submit Confirmation with SweetAlert2
        btnSubmit.addEventListener('click', function(e) {
            e.preventDefault(); // Prevent default form submission
            
            Swal.fire({
                title: 'Kumpulkan Jawaban?',
                text: "Pastikan Anda sudah yakin dengan semua jawaban Anda.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Kumpulkan!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    isExamActive = false; // Matikan sistem anti-cheat saat mengirim

                    // Show loading state
                    Swal.fire({
                        title: 'Sedang Mengirim...',
                        text: 'Mohon tunggu sebentar.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    examForm.submit();
                }
            });
        });
    });
</script>
@endsection


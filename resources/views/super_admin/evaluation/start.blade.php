@extends('layouts.super_admin')

@section('title', 'Mulai Evaluasi')

@section('content')
<div class="row">
    <div class="col-12">
        
        <!-- Start Screen -->
        <div id="start-screen" class="card text-center py-5">
            <div class="card-body">
                <i class="fas fa-exclamation-triangle fa-4x text-warning mb-3"></i>
                <h3 class="mb-3">Peraturan Evaluasi</h3>
                <ul class="text-start d-inline-block">
                    <li>Dilarang membuka tab lain atau aplikasi lain.</li>
                    <li>Dilarang keluar dari mode layar penuh (fullscreen).</li>
                    <li>Jika Anda melanggar, sistem akan memberikan peringatan.</li>
                    <li>Jika peringatan mencapai 3 kali, jawaban akan <strong>dikumpulkan otomatis</strong>.</li>
                </ul>
                <div class="mt-4">
                    <button id="btn-start-exam" class="btn btn-danger btn-lg px-5">
                        <i class="fas fa-play me-2"></i> Mulai Ujian
                    </button>
                </div>
            </div>
        </div>

        <!-- Exam Container (Hidden initially) -->
        <div id="exam-container" class="card d-none">
            <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Soal Evaluasi</h4>
                <div>
                    <span class="badge bg-warning text-dark me-2">Peringatan: <span id="warning-count">0</span>/3</span>
                    <span id="timer" class="badge bg-light text-danger"></span>
                </div>
            </div>
            <div class="card-body">
                @if($questions->count() > 0)
                    <form id="exam-form" action="{{ route('super_admin.evaluation.submit') }}" method="POST">
                        @csrf
                        @foreach($questions as $index => $question)
                            <div class="mb-4 border-bottom pb-3">
                                <h5 class="mb-3">{{ $index + 1 }}. {{ $question->question }}</h5>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="answers[{{ $question->id }}]" id="q{{ $question->id }}_a" value="a" required>
                                    <label class="form-check-label" for="q{{ $question->id }}_a">A. {{ $question->option_a }}</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="answers[{{ $question->id }}]" id="q{{ $question->id }}_b" value="b" required>
                                    <label class="form-check-label" for="q{{ $question->id }}_b">B. {{ $question->option_b }}</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="answers[{{ $question->id }}]" id="q{{ $question->id }}_c" value="c" required>
                                    <label class="form-check-label" for="q{{ $question->id }}_c">C. {{ $question->option_c }}</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="answers[{{ $question->id }}]" id="q{{ $question->id }}_d" value="d" required>
                                    <label class="form-check-label" for="q{{ $question->id }}_d">D. {{ $question->option_d }}</label>
                                </div>
                            </div>
                        @endforeach
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-danger btn-lg px-5" onclick="return confirm('Apakah Anda yakin ingin mengumpulkan jawaban?')">Kirim Jawaban</button>
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
    });
</script>
@endsection


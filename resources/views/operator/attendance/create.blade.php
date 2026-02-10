@extends('layouts.operator')

@section('title', 'Isi Absensi')

@section('content')
<div class="container-fluid py-3">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            <!-- Main Card -->
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0 text-center">
                    <h5 class="fw-bold text-dark mb-1">Absensi Harian</h5>
                    <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3">{{ $division }}</span>
                </div>

                <div class="card-body px-4 py-3">
                    
                    <!-- Clock Compact -->
                    <div class="text-center mb-4 mt-2">
                        <div class="bg-light rounded-3 py-2 px-3 d-inline-block">
                            <h2 class="fw-bold text-dark mb-0 font-monospace" id="clock">
                                {{ \Carbon\Carbon::now()->format('H:i:s') }}
                            </h2>
                            <small class="text-muted">{{ \Carbon\Carbon::now()->translatedFormat('l, d M Y') }}</small>
                        </div>
                    </div>

                    <!-- Alerts -->
                    @if(session('success'))
                        <div class="alert alert-success py-2 px-3 small d-flex align-items-center mb-3 rounded-3" role="alert">
                            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                        </div>
                    @endif

                    @if(session('info'))
                        <div class="alert alert-info py-2 px-3 small d-flex align-items-center mb-3 rounded-3" role="alert">
                            <i class="fas fa-info-circle me-2"></i> {{ session('info') }}
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger py-2 px-3 small d-flex align-items-center mb-3 rounded-3" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                        </div>
                    @endif

                    <!-- User Action Area -->
                    @forelse($users as $user)
                        <div class="text-center mb-4">
                            <!-- Small Avatar -->
                            <div class="avatar-circle bg-danger text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2 shadow-sm" style="width: 45px; height: 45px; font-size: 18px;">
                                {{ strtoupper(substr($user->name, 0, 2)) }}
                            </div>
                            <h6 class="fw-bold text-dark mb-0">{{ $user->name }}</h6>
                            <small class="text-secondary font-monospace">{{ $user->nik }}</small>
                        </div>

                        <!-- Status Badge -->
                         @if($user->today_attendance)
                            <div class="text-center mb-3">
                                @if($user->today_attendance->time_out)
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2">
                                        <i class="fas fa-check-double me-1"></i> Selesai
                                    </span>
                                @elseif($user->today_attendance->status == 'alpha')
                                    <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-2">
                                        <i class="fas fa-times me-1"></i> Tidak Hadir
                                    </span>
                                @else
                                    <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-3 py-2">
                                        <i class="fas fa-clock me-1"></i> Masuk: {{ \Carbon\Carbon::parse($user->today_attendance->time_in)->format('H:i') }}
                                    </span>
                                @endif
                            </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="d-grid gap-2">
                            @if(!$user->today_attendance)
                                <div class="row g-2">
                                    <div class="col-8">
                                         <form action="{{ route('operator.attendance.store') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="nik" value="{{ $user->nik }}">
                                            <input type="hidden" name="division" value="{{ $division }}">
                                            <input type="hidden" name="action" value="present">
                                            <button type="submit" class="btn btn-success w-100 py-2 fw-bold shadow-sm">
                                                <i class="fas fa-fingerprint me-1"></i> HADIR
                                            </button>
                                        </form>
                                    </div>
                                    <div class="col-4">
                                         <form action="{{ route('operator.attendance.store') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="nik" value="{{ $user->nik }}">
                                            <input type="hidden" name="division" value="{{ $division }}">
                                            <input type="hidden" name="action" value="absent">
                                            <button type="submit" class="btn btn-outline-danger w-100 py-2 fw-bold shadow-sm">
                                                ABSEN
                                            </button>
                                        </form>
                                    </div>
                                </div>

                            @elseif(!$user->today_attendance->time_out && $user->today_attendance->status != 'alpha')
                                <form action="{{ route('operator.attendance.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="nik" value="{{ $user->nik }}">
                                    <input type="hidden" name="division" value="{{ $division }}">
                                    <button type="submit" class="btn btn-warning w-100 py-2 fw-bold text-white shadow-sm">
                                        <i class="fas fa-sign-out-alt me-1"></i> ABSEN PULANG
                                    </button>
                                </form>
                            @else
                                <button class="btn btn-secondary w-100 py-2" disabled>
                                    <i class="fas fa-check-circle me-1"></i> Selesai Hari Ini
                                </button>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-3">
                            <small class="text-muted">Data tidak ditemukan.</small>
                        </div>
                    @endforelse

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    setInterval(function() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('id-ID', { hour12: false });
        if(document.getElementById('clock')) {
            document.getElementById('clock').innerText = timeString;
        }
    }, 1000);
</script>
@endsection

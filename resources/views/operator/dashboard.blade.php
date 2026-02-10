@extends('layouts.operator')

@section('title', 'Dashboard')

@section('content')
<!-- Welcome Banner -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-white border-0 shadow-sm p-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h2 class="mb-1 fw-bold text-dark">Dashboard Operator</h2>
                    <p class="text-muted mb-0">Halo, <span class="text-danger fw-bold">{{ auth()->user()->name }}</span>! Selamat bekerja dan tetap semangat.</p>
                </div>
                <div class="d-none d-md-block">
                    <span class="badge bg-danger p-2 px-3 rounded-pill">
                        <i class="fas fa-calendar-alt me-2"></i> {{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Card 1: Status Absensi Hari Ini -->
    <div class="col-md-6 col-lg-4">
        <div class="stats-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stats-title">Absensi Hari Ini</div>
                    <div class="stats-value">
                        @php
                            $attendance = \App\Models\Attendance::where('user_id', auth()->id())
                                        ->whereDate('date', \Carbon\Carbon::today())
                                        ->first();
                        @endphp
                        @if($attendance)
                            <span class="text-success" style="font-size: 0.6em;"><i class="fas fa-check-circle me-1"></i> Hadir</span>
                        @else
                            <span class="text-danger" style="font-size: 0.6em;"><i class="fas fa-times-circle me-1"></i> Tidak Hadir</span>
                        @endif
                    </div>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
            <div class="mt-3 text-muted small">
                @if($attendance)
                    <div><i class="fas fa-check text-success me-1"></i> Hadir: {{ \Carbon\Carbon::parse($attendance->time_in)->format('H:i') }} WIB</div>
                @else
                    <i class="fas fa-exclamation-triangle text-warning me-1"></i> Segera lakukan absensi masuk
                @endif
            </div>
        </div>
    </div>

    <!-- Card 2: Nilai Evaluasi -->
    <div class="col-md-6 col-lg-4">
        <div class="stats-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stats-title">Nilai Evaluasi Terakhir</div>
                    <div class="stats-value">
                        @php
                            $lastResult = \App\Models\EvaluationResult::where('user_id', auth()->id())
                                        ->latest()
                                        ->first();
                        @endphp
                        {{ $lastResult ? $lastResult->score : '-' }}
                    </div>
                </div>
                <div class="stats-icon info">
                    <i class="fas fa-star"></i>
                </div>
            </div>
            <div class="mt-3 text-muted small">
                @if($lastResult)
                    <i class="fas fa-check text-success me-1"></i> Diselesaikan pada {{ $lastResult->created_at->format('d M Y') }}
                @else
                    <i class="fas fa-info-circle text-info me-1"></i> Belum ada data evaluasi
                @endif
            </div>
        </div>
    </div>

    <!-- Card 3: Materi Tersedia -->
    <div class="col-md-6 col-lg-4">
        <div class="stats-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stats-title">Materi Tersedia</div>
                    <div class="stats-value">{{ \App\Models\Material::count() }}</div>
                </div>
                <div class="stats-icon warning">
                    <i class="fas fa-book"></i>
                </div>
            </div>
            <div class="mt-3 text-muted small">
                <a href="{{ route('operator.materials.index') }}" class="text-decoration-none text-muted">
                    <i class="fas fa-external-link-alt me-1"></i> Lihat materi pembelajaran
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                <h5 class="mb-0 fw-bold"><i class="fas fa-bullhorn text-danger me-2"></i>Pengumuman & Informasi</h5>
            </div>
            <div class="card-body px-4 pb-4">
                <div class="alert alert-info border-0 shadow-sm d-flex align-items-center" role="alert" style="background-color: #E1F5FE; color: #0277BD;">
                    <i class="fas fa-info-circle fa-2x me-3"></i>
                    <div>
                        <h5 class="alert-heading fw-bold mb-1">Penting!</h5>
                        <p class="mb-0">Jangan lupa untuk melakukan <strong>absensi</strong> saat tiba di lokasi kerja. Selalu patuhi protokol keselamatan kerja (K3) di setiap aktivitas.</p>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="p-3 border rounded-3 bg-light h-100">
                            <h6 class="fw-bold mb-3"><i class="fas fa-history text-secondary me-2"></i>Riwayat Absensi Terakhir</h6>
                            <ul class="list-group list-group-flush bg-transparent">
                                @php
                                    $recentAttendance = \App\Models\Attendance::where('user_id', auth()->id())
                                                        ->latest()
                                                        ->take(3)
                                                        ->get();
                                @endphp
                                @forelse($recentAttendance as $log)
                                    <li class="list-group-item bg-transparent px-0 py-2 d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="d-block small fw-bold">{{ \Carbon\Carbon::parse($log->date)->format('d M Y') }}</span>
                                            <span class="d-block small text-muted">
                                                @if($log->status == 'present' || $log->status == 'late') Hadir
                                                @elseif($log->status == 'sick') Sakit
                                                @elseif($log->status == 'permission') Izin
                                                @else Tidak Hadir @endif
                                            </span>
                                        </div>
                                        <div class="text-end">
                                            @if($log->time_in)
                                                <span class="badge bg-success rounded-pill" title="Masuk">{{ \Carbon\Carbon::parse($log->time_in)->format('H:i') }}</span>
                                            @endif
                                            @if(!$log->time_in)
                                                <span class="badge bg-secondary rounded-pill">-</span>
                                            @endif
                                        </div>
                                    </li>
                                @empty
                                    <li class="list-group-item bg-transparent text-center text-muted small">Belum ada riwayat absensi.</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

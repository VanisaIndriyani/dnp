@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<!-- Welcome Banner -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-white border-0 shadow-sm p-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h2 class="mb-1 fw-bold text-dark">Dashboard Admin</h2>
                    <p class="text-muted mb-0">Selamat datang kembali, <span class="text-danger fw-bold">{{ auth()->user()->name }}</span>! Pantau aktivitas operator hari ini.</p>
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

<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <!-- Card 1: Total Operator -->
    <div class="col-md-6 col-lg-3">
        <div class="stats-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stats-title">Total Operator</div>
                    <div class="stats-value">{{ \App\Models\User::where('role', 'operator')->count() }}</div>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-users-cog"></i>
                </div>
            </div>
            <div class="mt-3 text-muted small">
                <i class="fas fa-user-tag text-primary me-1"></i> Staff Operasional
            </div>
        </div>
    </div>

    <!-- Card 2: Hadir Hari Ini (Operator Only) -->
    <div class="col-md-6 col-lg-4">
        <div class="stats-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stats-title">Hadir Hari Ini</div>
                    <div class="stats-value">
                        {{ \App\Models\Attendance::whereDate('date', \Carbon\Carbon::today())
                            ->where('status', 'present')
                            ->whereHas('user', function($q) {
                                $q->where('role', 'operator');
                            })->count() }}
                    </div>
                </div>
                <div class="stats-icon success">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
            <div class="mt-3 text-muted small">
                <i class="fas fa-clock text-success me-1"></i> Operator Masuk
            </div>
        </div>
    </div>

    <!-- Card 4: Tidak Hadir (Operator Only) -->
    <div class="col-md-6 col-lg-4">
        <div class="stats-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stats-title">Tidak Hadir</div>
                    <div class="stats-value">
                        @php
                            $totalOps = \App\Models\User::where('role', 'operator')->count();
                            $presentOps = \App\Models\Attendance::whereDate('date', \Carbon\Carbon::today())
                                ->where('status', 'present')
                                ->whereHas('user', function($q) {
                                    $q->where('role', 'operator');
                                })->count();
                        @endphp
                        {{ max(0, $totalOps - $presentOps) }}
                    </div>
                </div>
                <div class="stats-icon danger">
                    <i class="fas fa-user-times"></i>
                </div>
            </div>
            <div class="mt-3 text-muted small">
                <i class="fas fa-times-circle text-danger me-1"></i> Operator Belum Absen
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-chart-line text-danger me-2"></i> Aktivitas Absensi Terbaru</h5>
                    <a href="{{ route('admin.attendance.index') }}" class="btn btn-sm btn-outline-danger">Lihat Semua</a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Nama</th>
                                <th>Bagian</th>
                                <th>Jam Masuk</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $recentActivities = \App\Models\Attendance::with('user')
                                    ->whereHas('user', function($q) {
                                        $q->where('role', 'operator');
                                    })
                                    ->whereDate('date', \Carbon\Carbon::today())
                                    ->latest()
                                    ->take(5)
                                    ->get();
                            @endphp
                            
                            @forelse($recentActivities as $attendance)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-3">
                                            @if($attendance->user->photo)
                                                <img src="{{ asset('storage/' . $attendance->user->photo) }}" alt="{{ $attendance->user->name }}" class="rounded-circle w-100 h-100" style="object-fit: cover;">
                                            @else
                                                @php
                                                    $colors = ['text-primary', 'text-success', 'text-danger', 'text-warning', 'text-info', 'text-dark', 'text-secondary'];
                                                    $bgColors = ['bg-primary-subtle', 'bg-success-subtle', 'bg-danger-subtle', 'bg-warning-subtle', 'bg-info-subtle', 'bg-dark-subtle', 'bg-secondary-subtle'];
                                                    $index = $attendance->user->id % count($colors);
                                                    $colorClass = $colors[$index];
                                                    $bgClass = $bgColors[$index];
                                                @endphp
                                                <span class="avatar-title rounded-circle {{ $bgClass }} {{ $colorClass }}">
                                                    {{ substr($attendance->user->name, 0, 1) }}
                                                </span>
                                            @endif
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $attendance->user->name }}</h6>
                                            <small class="text-muted">{{ $attendance->user->nik }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        {{ ucfirst($attendance->user->division) }}
                                    </span>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($attendance->time_in)->format('H:i') }}</td>
                                <td>
                                    @php
                                        $isPresent = ($attendance->status == 'present' || $attendance->status == 'late');
                                        $statusClass = $isPresent ? 'bg-success' : 'bg-danger';
                                        $statusIcon = $isPresent ? 'fa-check-circle' : 'fa-times-circle';
                                        $statusLabel = $isPresent ? 'Hadir' : 'Tidak Hadir';
                                    @endphp
                                    <span class="badge {{ $statusClass }}"><i class="fas {{ $statusIcon }} me-1"></i> {{ $statusLabel }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <img src="https://cdn-icons-png.flaticon.com/512/7486/7486754.png" width="64" class="mb-3 opacity-50" alt="Empty">
                                    <p class="mb-0">Belum ada aktivitas absensi hari ini.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

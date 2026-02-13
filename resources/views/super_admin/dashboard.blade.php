@extends('layouts.super_admin')

@section('title', 'Dashboard')

@section('content')
<!-- Welcome Banner -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-white border-0 shadow-sm p-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h2 class="mb-1 fw-bold text-dark">Dashboard Overview</h2>
                    <p class="text-muted mb-0">Selamat datang kembali, <span class="text-danger fw-bold">Super Admin</span>! Berikut adalah ringkasan hari ini.</p>
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
                <i class="fas fa-arrow-up me-1" style="color: var(--primary-color);"></i> Data terupdate
            </div>
        </div>
    </div>

    <!-- Card 2: Total Admin -->
    <div class="col-md-6 col-lg-3">
        <div class="stats-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stats-title">Total Admin</div>
                    <div class="stats-value">{{ \App\Models\User::where('role', 'admin')->count() }}</div>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-user-shield"></i>
                </div>
            </div>
            <div class="mt-3 text-muted small">
                <i class="fas fa-user-check me-1" style="color: var(--primary-color);"></i> Aktif memantau
            </div>
        </div>
    </div>

    <!-- Card 3: Hadir Hari Ini -->
    <div class="col-md-6 col-lg-3">
        <div class="stats-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stats-title">Hadir Hari Ini</div>
                    <div class="stats-value">{{ \App\Models\Attendance::whereDate('date', \Carbon\Carbon::today())->count() }}</div>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
            <div class="mt-3 text-muted small">
                <i class="fas fa-clock me-1" style="color: var(--primary-color);"></i> Realtime update
            </div>
        </div>
    </div>

    <!-- Card 4: Tidak Hadir -->
    <div class="col-md-6 col-lg-3">
        <div class="stats-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stats-title">Tidak Hadir</div>
                    <div class="stats-value">
                        @php
                            $totalUsers = \App\Models\User::where('role', '!=', 'super_admin')->count();
                            $attended = \App\Models\Attendance::whereDate('date', \Carbon\Carbon::today())->count();
                        @endphp
                        {{ max(0, $totalUsers - $attended) }}
                    </div>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-user-times"></i>
                </div>
            </div>
            <div class="mt-3 text-muted small">
                <i class="fas fa-exclamation-circle me-1" style="color: var(--primary-color);"></i> Belum Absen
            </div>
        </div>
    </div>
</div>

<style>
    .stats-card {
        background: white;
        border: 1px solid rgba(0,0,0,0.05);
        border-radius: 12px;
        padding: 1.5rem;
        height: 100%;
        transition: all 0.3s ease;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(198, 40, 40, 0.15);
        border: 1px solid var(--primary-color);
    }
    .stats-title {
        color: #6c757d;
        font-size: 0.875rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .stats-value {
        color: #212529;
        font-size: 1.75rem;
        font-weight: 700;
        line-height: 1.2;
    }
    .stats-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        background-color: rgba(198, 40, 40, 0.1);
        color: var(--primary-color);
    }
</style>

<!-- Recent Attendance Table -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center bg-white py-3">
                <h5 class="mb-0 fw-bold"><i class="fas fa-history me-2 text-danger"></i>Absensi Terbaru</h5>
                <a href="{{ route('super_admin.attendance.index') }}" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                    Lihat Semua <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Nama Karyawan</th>
                                <th>Role</th>
                                <th>Jam Masuk</th>
                                <th>Status Kehadiran</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(\App\Models\Attendance::with('user')->latest()->take(5)->get() as $attendance)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        @if($attendance->user->photo)
                                            <img src="{{ asset('storage/' . $attendance->user->photo) }}" alt="{{ $attendance->user->name }}" class="rounded-circle me-3 shadow-sm" style="width: 40px; height: 40px; object-fit: cover;">
                                        @else
                                            @php
                                                $colors = ['text-primary', 'text-success', 'text-danger', 'text-warning', 'text-info', 'text-dark', 'text-secondary'];
                                                $bgColors = ['bg-primary-subtle', 'bg-success-subtle', 'bg-danger-subtle', 'bg-warning-subtle', 'bg-info-subtle', 'bg-dark-subtle', 'bg-secondary-subtle'];
                                                $index = $attendance->user->id % count($colors);
                                                $colorClass = $colors[$index];
                                                $bgClass = $bgColors[$index];
                                            @endphp
                                            <div class="avatar-initial rounded-circle {{ $bgClass }} {{ $colorClass }} me-3 d-flex justify-content-center align-items-center shadow-sm" style="width: 40px; height: 40px; font-weight: bold;">
                                                {{ strtoupper(substr($attendance->user->name ?? 'U', 0, 1)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <div class="fw-bold text-dark">{{ $attendance->user->name ?? 'Unknown' }}</div>
                                            <small class="text-muted"><i class="fas fa-id-card me-1"></i>{{ $attendance->user->nik ?? '-' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($attendance->user->role == 'admin')
                                        <span class="badge bg-primary">Admin</span>
                                    @elseif($attendance->user->role == 'operator')
                                        <span class="badge bg-secondary">Operator</span>
                                    @else
                                        <span class="badge bg-dark">{{ $attendance->user->role }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="fw-bold text-dark">{{ \Carbon\Carbon::parse($attendance->time_in)->format('H:i') }}</span>
                                    <small class="text-muted d-block">WIB</small>
                                </td>
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
                                    <img src="https://img.icons8.com/ios/100/cccccc/nothing-found.png" alt="No Data" style="width: 60px; opacity: 0.5;" class="mb-3">
                                    <p class="mb-0">Belum ada data absensi hari ini.</p>
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

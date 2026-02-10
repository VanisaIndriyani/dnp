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
                <i class="fas fa-arrow-up text-success me-1"></i> Data terupdate
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
                <div class="stats-icon info">
                    <i class="fas fa-user-shield"></i>
                </div>
            </div>
            <div class="mt-3 text-muted small">
                <i class="fas fa-user-check text-info me-1"></i> Aktif memantau
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
                <div class="stats-icon success">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
            <div class="mt-3 text-muted small">
                <i class="fas fa-clock text-success me-1"></i> Realtime update
            </div>
        </div>
    </div>

    <!-- Card 4: Belum Absen -->
    <div class="col-md-6 col-lg-3">
        <div class="stats-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stats-title">Belum Absen</div>
                    <div class="stats-value">
                        @php
                            $totalUsers = \App\Models\User::where('role', '!=', 'super_admin')->count();
                            $attended = \App\Models\Attendance::whereDate('date', \Carbon\Carbon::today())->count();
                        @endphp
                        {{ max(0, $totalUsers - $attended) }}
                    </div>
                </div>
                <div class="stats-icon warning">
                    <i class="fas fa-user-clock"></i>
                </div>
            </div>
            <div class="mt-3 text-muted small">
                <i class="fas fa-exclamation-circle text-warning me-1"></i> Perlu diingatkan
            </div>
        </div>
    </div>
</div>

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
                                <th>Jam Pulang</th>
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
                                    @if($attendance->time_out)
                                        <span class="fw-bold text-dark">{{ \Carbon\Carbon::parse($attendance->time_out)->format('H:i') }}</span>
                                        <small class="text-muted d-block">WIB</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($attendance->time_out)
                                        <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> Selesai</span>
                                    @else
                                        <span class="badge bg-warning text-dark"><i class="fas fa-spinner fa-spin me-1"></i> Bekerja</span>
                                    @endif
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

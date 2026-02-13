@extends('layouts.super_admin')

@section('title', 'Data Absensi')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h2 class="mb-0 fw-bold">Data Absensi</h2>
    </div>
</div>

<!-- Filter Section -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <form action="{{ route('super_admin.attendance.index') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label for="date" class="form-label fw-bold text-secondary small text-uppercase">Tanggal</label>
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted"><i class="fas fa-calendar"></i></span>
                    <input type="date" class="form-control" id="date" name="date" value="{{ request('date') }}" onchange="this.form.submit()">
                </div>
            </div>
            <div class="col-md-3">
                <label for="division" class="form-label fw-bold text-secondary small text-uppercase">Bagian</label>
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted"><i class="fas fa-layer-group"></i></span>
                    <select class="form-select" id="division" name="division" onchange="this.form.submit()">
                        <option value="">Semua Bagian</option>
                        <option value="case" {{ request('division') == 'case' ? 'selected' : '' }}>Case</option>
                        <option value="cover" {{ request('division') == 'cover' ? 'selected' : '' }}>Cover</option>
                        <option value="inner" {{ request('division') == 'inner' ? 'selected' : '' }}>Inner</option>
                        <option value="endplate" {{ request('division') == 'endplate' ? 'selected' : '' }}>Endplate</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <label for="status" class="form-label fw-bold text-secondary small text-uppercase">Status</label>
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted"><i class="fas fa-filter"></i></span>
                    <select class="form-select" id="status" name="status" onchange="this.form.submit()">
                        <option value="" {{ !request('status') ? 'selected' : '' }}>Semua Status</option>
                        <option value="hadir" {{ request('status') == 'hadir' ? 'selected' : '' }}>Hadir</option>
                        <option value="tidak_hadir" {{ request('status') == 'tidak_hadir' ? 'selected' : '' }}>Tidak Hadir (Belum Absen)</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="d-grid">
                    <a href="{{ route('super_admin.attendance.export', request()->query()) }}" class="btn btn-success">
                        <i class="fas fa-file-excel me-2"></i>Export Excel
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Data Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header text-white" style="background-color: var(--primary-color);">
        <h5 class="mb-0 fw-bold"><i class="fas fa-list me-2"></i>Daftar Absensi</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 text-secondary small text-uppercase fw-bold">Tanggal</th>
                        <th class="py-3 text-secondary small text-uppercase fw-bold">Nama Karyawan</th>
                        <th class="py-3 text-secondary small text-uppercase fw-bold">NIK</th>
                        <th class="py-3 text-secondary small text-uppercase fw-bold">Bagian</th>
                        <th class="py-3 text-secondary small text-uppercase fw-bold">Jam Masuk</th>
                        <th class="py-3 text-secondary small text-uppercase fw-bold">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $item)
                        @php
                            // Unified Logic: $item is always a User object with attached 'attendance_record'
                            $user = $item;
                            $attendance = $user->attendance_record ?? null;
                            $date = $user->target_date ?? \Carbon\Carbon::today()->toDateString();
                            
                            if ($attendance) {
                                $timeIn = $attendance->time_in;
                                $timeOut = $attendance->time_out;
                                $status = $attendance->status;
                                $attendanceId = $attendance->id;
                                $isApproved = $attendance->is_approved;
                                $isMissing = false;
                            } else {
                                $timeIn = null;
                                $timeOut = null;
                                $status = 'tidak_hadir';
                                $attendanceId = null;
                                $isApproved = false;
                                $isMissing = true;
                            }
                        @endphp
                        <tr>
                            <td class="ps-4 fw-medium text-secondary">
                                {{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($user->photo && Storage::disk('public')->exists($user->photo))
                                        <img src="{{ asset('storage/' . $user->photo) }}" alt="Photo" class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                    @else
                                        @php
                                            $initials = strtoupper(substr($user->name, 0, 2));
                                            $colors = ['#FF5733', '#33FF57', '#3357FF', '#FF33A1', '#A133FF', '#33FFF5', '#F5FF33', '#FF8C33'];
                                            $bgColor = $colors[ord($initials[0]) % count($colors)];
                                        @endphp
                                        <div class="rounded-circle me-2 d-flex align-items-center justify-content-center text-white fw-bold" 
                                             style="width: 40px; height: 40px; background-color: {{ $bgColor }}; font-size: 14px;">
                                            {{ $initials }}
                                        </div>
                                    @endif
                                    <div>
                                        <div class="fw-bold">{{ $user->name }}</div>
                                        <div class="small text-muted">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-muted">{{ $user->nik }}</td>
                            <td>
                                @if($user->division)
                                    <span class="badge bg-light text-dark border">
                                        {{ ucfirst($user->division) }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="fw-medium">
                                @if($timeIn)
                                    <span class="badge bg-success">{{ \Carbon\Carbon::parse($timeIn)->format('H:i') }}</span>
                                @else
                                    <span class="badge bg-secondary">-</span>
                                @endif
                            </td>
                            <td>
                                @if($isMissing)
                                    <span class="badge rounded-pill px-2 py-1" style="background-color: #dc3545; color: white; font-size: 11px;">
                                        <i class="fas fa-times-circle me-1"></i> Tidak Hadir
                                    </span>
                                @else
                                    @php
                                        // Simplify Status Logic: Only 'Hadir' (Green) or 'Tidak Hadir' (Red)
                                        $isPresent = ($status == 'present' || $status == 'late');
                                        
                                        $bgColor = $isPresent ? '#198754' : '#dc3545'; // Green : Red
                                        $statusIcon = $isPresent ? 'fa-check-circle' : 'fa-times-circle';
                                        $statusLabel = $isPresent ? 'Hadir' : 'Tidak Hadir';
                                    @endphp
                                    <div class="d-flex flex-column gap-1">
                                        <span class="badge rounded-pill px-2 py-1" style="background-color: {{ $bgColor }}; color: white; font-size: 11px;">
                                            <i class="fas {{ $statusIcon }} me-1"></i> {{ $statusLabel }}
                                        </span>
                                        @if($isApproved)
                                            <span class="badge bg-light rounded-pill mt-1" style="font-size: 10px; color: var(--primary-color); border: 1px solid var(--primary-color);">
                                                <i class="fas fa-check-double me-1"></i> Disetujui
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center justify-content-center opacity-50">
                                    <i class="fas fa-calendar-times fa-4x mb-3 text-muted"></i>
                                    <h5 class="text-muted">Tidak ada data absensi ditemukan.</h5>
                                    <p class="small text-muted mb-0">Coba ubah filter tanggal atau bagian.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-4 py-3 border-top bg-light">
            {{ $attendances->withQueryString()->links() }}
        </div>
    </div>
</div>

@endsection

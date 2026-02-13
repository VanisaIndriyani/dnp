@extends('layouts.admin')

@section('title', 'Data Absensi')

@section('content')
<!-- Header & Filters -->
<div class="row mb-4 align-items-center">
    <div class="col-12 col-xl-auto mb-3 mb-xl-0">
        <h2 class="mb-1 fw-bold text-dark">Data Absensi</h2>
        <p class="text-muted mb-0">Pantau kehadiran dan aktivitas harian operator.</p>
    </div>
    <div class="col-12 col-xl-auto ms-auto">
        <div class="d-flex flex-wrap gap-2 justify-content-start justify-content-xl-end align-items-center">
            {{-- Action Buttons --}}
            <a href="{{ route('admin.attendance.createManual') }}" class="btn btn-primary btn-sm shadow-sm" style="background-color: var(--primary-color); border-color: var(--primary-color);">
                <i class="fas fa-plus me-1"></i> Input Manual
            </a>
            <a href="{{ route('admin.attendance.export', request()->query()) }}" class="btn btn-success btn-sm shadow-sm">
                <i class="fas fa-file-excel me-1"></i> Export
            </a>

            <div class="vr mx-1 d-none d-xl-block bg-secondary opacity-25"></div>

            {{-- Filters --}}
            <form action="{{ route('admin.attendance.index') }}" method="GET" class="d-flex flex-wrap gap-2 align-items-center">
                <select name="division" class="form-select form-select-sm bg-white border-secondary border-opacity-25 rounded-3 shadow-sm" style="width: auto; cursor: pointer;" onchange="this.form.submit()">
                    <option value="">Semua Bagian</option>
                    <option value="case" {{ request('division') == 'case' ? 'selected' : '' }}>Case</option>
                    <option value="cover" {{ request('division') == 'cover' ? 'selected' : '' }}>Cover</option>
                    <option value="inner" {{ request('division') == 'inner' ? 'selected' : '' }}>Inner</option>
                    <option value="endplate" {{ request('division') == 'endplate' ? 'selected' : '' }}>Endplate</option>
                </select>

                <select name="status" class="form-select form-select-sm bg-white border-secondary border-opacity-25 rounded-3 shadow-sm" style="width: auto; cursor: pointer;" onchange="this.form.submit()">
                    <option value="hadir" {{ request('status') == 'hadir' || !request('status') ? 'selected' : '' }}>Hadir</option>
                    <option value="tidak_hadir" {{ request('status') == 'tidak_hadir' ? 'selected' : '' }}>Tidak Hadir</option>
                </select>

                <div class="d-flex align-items-center bg-white rounded-3 border border-secondary border-opacity-25 shadow-sm px-2 py-1">
                    <i class="fas fa-calendar text-muted small me-2"></i>
                    <input type="date" name="date" class="form-control form-control-sm border-0 bg-transparent p-0 text-secondary fw-bold" style="width: 110px; font-size: 0.8rem;" value="{{ request('date') }}" onchange="this.form.submit()">
                </div>

                @if(request()->anyFilled(['division', 'date']) || (request('status') && request('status') != 'hadir'))
                    <a href="{{ route('admin.attendance.index') }}" class="btn btn-light btn-sm rounded-circle border shadow-sm d-flex align-items-center justify-content-center text-danger" style="width: 32px; height: 32px;" title="Reset Filter">
                        <i class="fas fa-times fa-xs"></i>
                    </a>
                @endif
            </form>
        </div>
    </div>
</div>

<!-- Data Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header text-white" style="background-color: #C62828;">
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
                        @can('admin')
                        <th class="py-3 text-secondary small text-uppercase fw-bold text-end pe-4">Aksi</th>
                        @endcan
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $item)
                        @php
                            $isMissing = $item instanceof \App\Models\User;
                            $user = $isMissing ? $item : $item->user;
                            $date = $isMissing ? $item->missing_date : $item->date;
                            $timeIn = $isMissing ? null : $item->time_in;
                            $timeOut = $isMissing ? null : $item->time_out;
                            $status = $isMissing ? 'tidak_hadir' : $item->status;
                            $attendanceId = $isMissing ? null : $item->id;
                            $isApproved = $isMissing ? false : $item->is_approved;
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
                                        @endphp
                                        <div class="rounded-circle me-2 d-flex align-items-center justify-content-center text-white fw-bold" 
                                             style="width: 40px; height: 40px; background-color: var(--primary-color); font-size: 14px;">
                                            {{ $initials }}
                                        </div>
                                    @endif
                                    <div>
                                        <div class="fw-bold">{{ $user->name }}</div>
                                        <div class="small text-muted">{{ $user->division }}</div>
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
                                    <span class="badge text-white" style="background-color: var(--primary-color);">{{ \Carbon\Carbon::parse($timeIn)->format('H:i') }}</span>
                                @else
                                    <span class="badge bg-light text-secondary border">-</span>
                                @endif
                            </td>
                            <td>
                                @if($isMissing)
                                    <span class="badge bg-danger text-white border border-danger rounded-pill px-3 py-2">
                                        <i class="fas fa-times-circle me-1"></i> Tidak Hadir
                                    </span>
                                @else
                                    @php
                                        // Simplify Status Logic: Only 'Hadir' (Green) or 'Tidak Hadir' (Red)
                                        $isPresent = ($status == 'present' || $status == 'late');
                                        
                                        $statusClass = $isPresent ? 'bg-success text-white' : 'bg-danger text-white';
                                        $statusIcon = $isPresent ? 'fa-check-circle' : 'fa-times-circle';
                                        $statusLabel = $isPresent ? 'Hadir' : 'Tidak Hadir';
                                    @endphp
                                    <div class="d-flex flex-column gap-1">
                                        <span class="badge {{ $statusClass }} border border-{{ $isPresent ? 'success' : 'danger' }} rounded-pill px-3 py-2">
                                            <i class="fas {{ $statusIcon }} me-1"></i> {{ $statusLabel }}
                                        </span>
                                        @if($isApproved)
                                            <span class="badge bg-light rounded-pill mt-1" style="font-size: 0.7rem; color: var(--primary-color); border: 1px solid var(--primary-color);">
                                                <i class="fas fa-check-double me-1"></i> Disetujui
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </td>
                            @can('admin')
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-1">
                                    @if($isMissing)
                                        <a href="{{ route('admin.attendance.createManual', ['user_id' => $user->id, 'date' => $date]) }}" class="btn btn-sm btn-outline-danger px-3 shadow-sm rounded-3" title="Edit Absensi" style="border-color: var(--primary-color); color: var(--primary-color);">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @else
                                        <a href="{{ route('admin.attendance.edit', $attendanceId) }}" class="btn btn-sm btn-outline-danger px-3 shadow-sm rounded-3" title="Edit Absensi" style="border-color: var(--primary-color); color: var(--primary-color);">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        @if(!$isApproved)
                                            <form action="{{ route('admin.attendance.approve', $attendanceId) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-danger px-3 shadow-sm rounded-3" title="Setujui Absensi" style="background-color: var(--primary-color); border-color: var(--primary-color);">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        @endif
                                    @endif
                                </div>
                            </td>
                            @endcan
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

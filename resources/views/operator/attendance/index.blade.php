@extends('layouts.operator')

@section('title', 'Data Absensi')

@section('content')
<div class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <h2 class="mb-0">Data Absensi</h2>
        <a href="{{ route('operator.attendance.export', request()->query()) }}" class="btn btn-success">
            <i class="fas fa-file-excel me-2"></i>Export Excel
        </a>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('operator.attendance.index') }}" method="GET" class="row g-3">
            <div class="col-md-5">
                <label for="date" class="form-label">Tanggal</label>
                <input type="date" class="form-control" id="date" name="date" value="{{ request('date') }}">
            </div>
            <div class="col-md-5">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">Semua Status</option>
                    <option value="present" {{ request('status') == 'present' ? 'selected' : '' }}>Hadir</option>
                    <option value="alpha" {{ request('status') == 'alpha' ? 'selected' : '' }}>Tidak Hadir</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-danger w-100" style="background-color: var(--primary-color);">Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Tanggal</th>
                        <th>Jam Masuk</th>
                        <th>Jam Keluar</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $attendance)
                        <tr>
                            <td class="ps-4">{{ \Carbon\Carbon::parse($attendance->date)->format('d/m/Y') }}</td>
                            <td>{{ $attendance->time_in ? \Carbon\Carbon::parse($attendance->time_in)->format('H:i') : '-' }}</td>
                            <td>{{ $attendance->time_out ? \Carbon\Carbon::parse($attendance->time_out)->format('H:i') : '-' }}</td>
                            <td>
                                @php
                                    $statusColor = 'secondary';
                                    if ($attendance->status == 'present' || $attendance->status == 'late') $statusColor = 'success';
                                    elseif ($attendance->status == 'sick') $statusColor = 'info';
                                    elseif ($attendance->status == 'alpha') $statusColor = 'danger';
                                    elseif ($attendance->status == 'permission') $statusColor = 'primary';
                                    
                                    $statusLabel = ucfirst($attendance->status);
                                    if ($attendance->status == 'present' || $attendance->status == 'late') $statusLabel = 'Hadir';
                                    elseif ($attendance->status == 'alpha') $statusLabel = 'Tidak Hadir';
                                @endphp
                                <span class="badge bg-{{ $statusColor }}">{{ $statusLabel }}</span>
                                @if($attendance->is_approved)
                                    <span class="badge bg-success ms-1"><i class="fas fa-check-circle"></i> Approved</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4">Tidak ada data absensi ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">
            {{ $attendances->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection


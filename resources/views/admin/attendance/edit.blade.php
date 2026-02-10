@extends('layouts.admin')

@section('title', 'Edit Data Absensi')

@section('content')
<div class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <h2 class="mb-0 fw-bold">Edit Data Absensi</h2>
        <a href="{{ route('admin.attendance.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <form action="{{ route('admin.attendance.update', $attendance->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label class="form-label fw-bold">Operator</label>
                <input type="text" class="form-control" value="{{ $attendance->user->name }} ({{ $attendance->user->nik }})" disabled readonly>
            </div>

            <div class="row">
                <!-- Date -->
                <div class="col-md-6 mb-4">
                    <label for="date" class="form-label fw-bold">Tanggal</label>
                    <input type="date" class="form-control" id="date" name="date" value="{{ $attendance->date }}" required>
                </div>

                <!-- Status -->
                <div class="col-md-6 mb-4">
                    <label for="status" class="form-label fw-bold">Status Kehadiran</label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="present" {{ $attendance->status == 'present' || $attendance->status == 'late' ? 'selected' : '' }}>Hadir (Present)</option>
                        <option value="sick" {{ $attendance->status == 'sick' ? 'selected' : '' }}>Sakit (Sick)</option>
                        <option value="permission" {{ $attendance->status == 'permission' ? 'selected' : '' }}>Izin (Permission)</option>
                        <option value="alpha" {{ $attendance->status == 'alpha' ? 'selected' : '' }}>Alpha (Absent)</option>
                    </select>
                </div>
            </div>

            <div class="row">
                <!-- Time In -->
                <div class="col-md-6 mb-4">
                    <label for="time_in" class="form-label fw-bold">Jam Masuk</label>
                    <input type="time" class="form-control" id="time_in" name="time_in" 
                        value="{{ $attendance->time_in ? \Carbon\Carbon::parse($attendance->time_in)->format('H:i') : '' }}">
                    <div class="form-text">Kosongkan jika tidak hadir.</div>
                </div>

                <!-- Time Out -->
                <div class="col-md-6 mb-4">
                    <label for="time_out" class="form-label fw-bold">Jam Keluar</label>
                    <input type="time" class="form-control" id="time_out" name="time_out" 
                        value="{{ $attendance->time_out ? \Carbon\Carbon::parse($attendance->time_out)->format('H:i') : '' }}">
                    <div class="form-text">Biarkan kosong jika belum pulang atau tidak hadir.</div>
                </div>
            </div>

            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-3">
                <button type="submit" class="btn btn-warning px-5 text-white" style="background-color: #ffc107; border: none;">
                    <i class="fas fa-save me-2"></i>Update Absensi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

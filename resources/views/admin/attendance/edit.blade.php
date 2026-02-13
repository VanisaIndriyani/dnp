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

            <input type="hidden" name="date" value="{{ $attendance->date }}">
            
            <div class="mb-4">
                <label for="time_in" class="form-label fw-bold">Jam Masuk</label>
                <input type="time" class="form-control" id="time_in" name="time_in" 
                       value="{{ $attendance->time_in ? \Carbon\Carbon::parse($attendance->time_in)->format('H:i') : '' }}">
                <div class="form-text">Format 24 jam. Kosongkan jika tidak ada jam masuk.</div>
            </div>

            <div class="mb-4">
                <label for="status" class="form-label fw-bold">Status Kehadiran</label>
                <select class="form-select" id="status" name="status" required>
                    <option value="present" {{ ($attendance->status == 'present' || $attendance->status == 'late') ? 'selected' : '' }}>Hadir</option>
                    <option value="alpha" {{ ($attendance->status != 'present' && $attendance->status != 'late') ? 'selected' : '' }}>Tidak Hadir</option>
                </select>
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

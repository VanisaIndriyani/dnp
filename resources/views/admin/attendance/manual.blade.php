@extends('layouts.admin')

@section('title', 'Input Absensi Manual')

@section('content')
<div class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <h2 class="mb-0 fw-bold">Input Absensi Manual</h2>
        <a href="{{ route('admin.attendance.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form action="{{ route('admin.attendance.storeManual') }}" method="POST">
            @csrf
            
            <!-- Division Selection -->
            <div class="mb-4">
                <label for="division_filter" class="form-label fw-bold">Pilih Divisi</label>
                <select class="form-select" id="division_filter">
                    <option value="" selected>-- Semua Divisi --</option>
                    <option value="case">Case</option>
                    <option value="cover">Cover</option>
                    <option value="inner">Inner</option>
                    <option value="endplate">Endplate</option>
                </select>
            </div>

            <!-- User Selection -->
            <div class="mb-4">
                <label for="user_id" class="form-label fw-bold">Pilih Operator</label>
                <select class="form-select" id="user_id" name="user_id" required>
                    <option value="" selected disabled>-- Pilih Operator --</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" data-division="{{ $user->division }}" {{ (isset($selectedUser) && $selectedUser == $user->id) ? 'selected' : '' }}>{{ $user->name }} ({{ $user->nik }}) - {{ ucfirst($user->division) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="row">
                <!-- Date -->
                <div class="col-md-6 mb-4">
                    <label for="date" class="form-label fw-bold">Tanggal</label>
                    <input type="date" class="form-control" id="date" name="date" value="{{ isset($selectedDate) ? $selectedDate : date('Y-m-d') }}" required>
                </div>

                <!-- Status -->
                <div class="col-md-6 mb-4">
                    <label for="status" class="form-label fw-bold">Status Kehadiran</label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="present" selected>Hadir</option>
                        <option value="alpha">Tidak Hadir</option>
                    </select>
                </div>
            </div>

            <div class="row">
                <!-- Time In -->
                <div class="col-md-12 mb-4">
                    <label for="time_in" class="form-label fw-bold">Jam Masuk</label>
                    <input type="time" class="form-control" id="time_in" name="time_in" value="08:00">
                    <div class="form-text">Format 24 jam (Contoh: 08:00). Kosongkan jika Tidak Hadir.</div>
                </div>
            </div>

            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-3">
                <button type="submit" class="btn btn-danger px-5" style="background-color: var(--primary-color);">
                    <i class="fas fa-save me-2"></i>Simpan Data Absensi
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const divisionSelect = document.getElementById('division_filter');
        const userSelect = document.getElementById('user_id');
        const statusSelect = document.getElementById('status');
        const timeInInput = document.getElementById('time_in');
        
        // Store all original options (except the placeholder)
        const allOptions = Array.from(userSelect.querySelectorAll('option:not([value=""])'));

        function toggleTimeInputs() {
            if (statusSelect.value === 'alpha') {
                timeInInput.value = '';
                timeInInput.disabled = true;
            } else {
                timeInInput.disabled = false;
                if (!timeInInput.value) timeInInput.value = '08:00';
            }
        }

        statusSelect.addEventListener('change', toggleTimeInputs);
        
        // Initialize state
        toggleTimeInputs();

        divisionSelect.addEventListener('change', function() {
            const selectedDivision = this.value;
            
            // Reset user selection to default
            userSelect.innerHTML = '<option value="" selected disabled>-- Pilih Operator --</option>';
            
            allOptions.forEach(option => {
                const userDivision = option.getAttribute('data-division');
                
                // If no division selected or matches the user's division, append it
                if (selectedDivision === "" || userDivision === selectedDivision) {
                    userSelect.appendChild(option);
                }
            });
        });
    });
</script>
@endsection

@extends('layouts.super_admin')

@section('title', 'Laporan')

@section('content')
<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1 fw-bold text-dark">Laporan & Rekapitulasi</h2>
        <p class="text-muted mb-0">Unduh dan kelola laporan aktivitas sistem secara menyeluruh.</p>
    </div>
</div>

<div class="row g-4">
    <!-- Laporan Absensi -->
    <div class="col-md-6">
        <div class="card h-100 border-0 shadow-sm hover-lift overflow-hidden">
            <div class="card-body p-4 position-relative">
                <div class="d-flex align-items-center mb-4">
                    <div class="icon-square bg-danger-subtle text-danger rounded-3 p-3 me-3">
                        <i class="fas fa-calendar-check fa-2x"></i>
                    </div>
                    <div>
                        <h4 class="card-title fw-bold mb-1">Laporan Absensi</h4>
                        <span class="badge bg-light text-muted border">Data Kehadiran</span>
                    </div>
                </div>
                
                <p class="card-text text-muted mb-4">
                    Akses rekapitulasi kehadiran karyawan secara lengkap. Filter data berdasarkan periode waktu dan divisi, serta unduh laporan dalam format Excel.
                </p>
                
                <div class="d-grid gap-2">
                    <a href="{{ route('super_admin.attendance.export') }}" class="btn btn-success py-2 fw-medium">
                        <i class="fas fa-file-excel me-2"></i>Download Excel
                    </a>
                    <a href="{{ route('super_admin.attendance.index') }}" class="btn btn-outline-secondary py-2">
                        <i class="fas fa-eye me-2"></i>Lihat Data
                    </a>
                </div>
                
                <!-- Decorative background icon -->
                <i class="fas fa-calendar-check position-absolute text-danger opacity-10" style="font-size: 8rem; right: -20px; bottom: -20px; transform: rotate(-15deg);"></i>
            </div>
        </div>
    </div>

    <!-- Laporan Evaluasi -->
    <div class="col-md-6">
        <div class="card h-100 border-0 shadow-sm hover-lift overflow-hidden">
            <div class="card-body p-4 position-relative">
                <div class="d-flex align-items-center mb-4">
                    <div class="icon-square bg-info-subtle text-info rounded-3 p-3 me-3">
                        <i class="fas fa-poll fa-2x"></i>
                    </div>
                    <div>
                        <h4 class="card-title fw-bold mb-1">Laporan Evaluasi</h4>
                        <span class="badge bg-light text-muted border">Hasil Penilaian</span>
                    </div>
                </div>
                
                <p class="card-text text-muted mb-4">
                    Tinjau performa karyawan melalui hasil evaluasi. Analisis skor dan unduh rekapitulasi nilai untuk penilaian kinerja.
                </p>
                
                <div class="d-grid gap-2">
                    <a href="{{ route('super_admin.evaluation.results.export') }}" class="btn btn-success py-2 fw-medium">
                        <i class="fas fa-file-excel me-2"></i>Download Excel
                    </a>
                    <a href="{{ route('super_admin.evaluation.results') }}" class="btn btn-outline-secondary py-2">
                        <i class="fas fa-list-alt me-2"></i>Lihat Nilai
                    </a>
                </div>

                <!-- Decorative background icon -->
                <i class="fas fa-poll position-absolute text-info opacity-10" style="font-size: 8rem; right: -20px; bottom: -20px; transform: rotate(-15deg);"></i>
            </div>
        </div>
    </div>
</div>
@endsection


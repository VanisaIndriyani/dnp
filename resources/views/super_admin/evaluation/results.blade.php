@extends('layouts.super_admin')

@section('title', 'Hasil Evaluasi')

@section('content')

@if(isset($stats))
    {{-- CATEGORY DASHBOARD VIEW --}}
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-2">Hasil Nilai Evaluasi</h2>
                <p class="text-muted">Pilih bagian untuk melihat hasil evaluasi operator.</p>
            </div>
            <div>
                <div class="btn-group">
                    <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-file-excel me-2"></i>Export Excel
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('super_admin.evaluation.results.export') }}">Export Semua Data</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><h6 class="dropdown-header">Export Per Bagian</h6></li>
                        <li><a class="dropdown-item" href="{{ route('super_admin.evaluation.results.export', ['division' => 'cover']) }}">Bagian Cover</a></li>
                        <li><a class="dropdown-item" href="{{ route('super_admin.evaluation.results.export', ['division' => 'case']) }}">Bagian Case</a></li>
                        <li><a class="dropdown-item" href="{{ route('super_admin.evaluation.results.export', ['division' => 'inner']) }}">Bagian Inner</a></li>
                        <li><a class="dropdown-item" href="{{ route('super_admin.evaluation.results.export', ['division' => 'endplate']) }}">Bagian Endplate</a></li>
                    </ul>
                </div>
                <a href="{{ route('super_admin.evaluation.index') }}" class="btn btn-secondary ms-2">
                    <i class="fas fa-arrow-left me-2"></i>Kembali ke Soal
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        @foreach($stats as $div => $stat)
        <div class="col-md-6 col-lg-3">
            <a href="{{ route('super_admin.evaluation.results', ['division' => $div]) }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100 hover-card">
                    <div class="card-body text-center p-4">
                        <div class="avatar-lg mx-auto mb-3 bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class="fas fa-user-graduate fa-2x text-primary"></i>
                        </div>
                        <h4 class="card-title text-dark mb-1">{{ ucfirst($div) }}</h4>
                        <p class="text-muted small mb-3">Hasil Evaluasi Bagian {{ ucfirst($div) }}</p>
                        
                        <div class="d-flex justify-content-center gap-3">
                            <div class="text-center">
                                <h5 class="mb-0 fw-bold text-dark">{{ $stat['total'] }}</h5>
                                <small class="text-muted" style="font-size: 0.75rem;">Total</small>
                            </div>
                            <div class="vr"></div>
                            <div class="text-center">
                                <h5 class="mb-0 fw-bold text-warning">{{ $stat['pending'] }}</h5>
                                <small class="text-muted" style="font-size: 0.75rem;">Pending</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0 pb-3">
                        <span class="btn btn-outline-primary btn-sm w-100 rounded-pill">Lihat Hasil</span>
                    </div>
                </div>
            </a>
        </div>
        @endforeach
    </div>

    <style>
        .hover-card {
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .hover-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
        }
    </style>

@else
    {{-- RESULTS LIST VIEW --}}
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <a href="{{ route('super_admin.evaluation.results') }}" class="text-decoration-none text-muted mb-2 d-inline-block">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Bagian
                </a>
                <h2 class="mb-0">Hasil Nilai: <span class="text-primary">{{ isset($division) ? ucfirst($division) : 'Semua Bagian' }}</span></h2>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <form action="{{ route('super_admin.evaluation.results') }}" method="GET" class="d-flex gap-2">
                    <input type="hidden" name="division" value="{{ $division }}">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="fas fa-calendar-alt text-muted"></i></span>
                        <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}" title="Tanggal Mulai">
                        <span class="input-group-text bg-light border-start-0 border-end-0">s/d</span>
                        <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}" title="Tanggal Selesai">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter"></i>
                        </button>
                    </div>
                    @if(request('start_date') || request('end_date'))
                        <a href="{{ route('super_admin.evaluation.results', ['division' => $division]) }}" class="btn btn-outline-secondary" title="Reset Filter">
                            <i class="fas fa-undo"></i>
                        </a>
                    @endif
                </form>

                <div class="btn-group">
                    <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-file-excel me-2"></i>Export Excel
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('super_admin.evaluation.results.export', isset($division) ? ['division' => $division] : []) }}">Export Data {{ isset($division) ? ucfirst($division) : 'Semua' }}</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="{{ route('super_admin.evaluation.results.export') }}">Export Semua Data</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">No</th>
                            <th>Nama User</th>
                            <th>NIK</th>
                            <th>Bagian</th>
                            <th class="text-center">Nilai PG</th>
                            <th class="text-center">Nilai Essay</th>
                            <th class="text-center">Total Akhir</th>
                            <th>Status</th>
                            <th>Tanggal Evaluasi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($results as $key => $result)
                            <tr>
                                <td class="ps-4">{{ $results->firstItem() + $key }}</td>
                                <td>{{ $result->user->name }}</td>
                                <td>{{ $result->user->nik }}</td>
                                <td>{{ ucfirst($result->user->division ?? '-') }}</td>
                                <td class="text-center">
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success">
                                        {{ $result->mc_score }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary">
                                        {{ $result->essay_score }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $result->score >= 70 ? 'success' : 'danger' }} fs-6">
                                        {{ $result->score }}
                                    </span>
                                </td>
                                <td>
                                    @if($result->status == 'pending')
                                        <span class="badge bg-warning text-dark">Menunggu Penilaian</span>
                                    @else
                                        <span class="badge bg-success">Selesai Dinilai</span>
                                    @endif
                                </td>
                                <td>{{ $result->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('super_admin.evaluation.grade', $result->id) }}" class="btn btn-sm btn-primary mb-1">
                                        <i class="fas fa-edit me-1"></i> {{ $result->status == 'pending' ? 'Nilai Sekarang' : 'Edit Nilai' }}
                                    </a>
                                    <form action="{{ route('super_admin.evaluation.results.destroy', $result->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin mereset hasil evaluasi ini? User harus mengerjakan ulang.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger mb-1">
                                            <i class="fas fa-redo-alt me-1"></i> Reset
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-4">Belum ada hasil evaluasi untuk bagian ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-3">
                {{ $results->appends(request()->all())->links() }}
            </div>
        </div>
    </div>
@endif

@endsection

@extends('layouts.operator')

@section('title', 'Hasil Evaluasi')

@section('content')

{{-- PAGE HEADER --}}
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h2 class="mb-1 fw-bold text-dark">Hasil Nilai Evaluasi</h2>
        <p class="text-muted mb-0">Lihat riwayat dan hasil evaluasi Anda.</p>
    </div>
</div>

@if(session('info'))
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <i class="fas fa-info-circle me-2"></i>{{ session('info') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- MAIN CONTENT --}}
<div class="card shadow-sm border-0 mb-5" style="border-top: 3px solid #C62828;">
    {{-- TOOLBAR HEADER --}}
    <div class="card-header bg-white py-3 px-4 border-bottom">
        <div class="row g-3 align-items-center justify-content-between">
            
            {{-- Left: Title --}}
            <div class="col-12 col-xl-auto">
                <div class="d-flex align-items-center gap-2">
                    <div class="p-2 rounded" style="background-color: #ffebee; color: #C62828;">
                        <i class="fas fa-table"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold text-dark">Daftar Nilai</h5>
                        <small class="text-muted">Total {{ $results->total() }} hasil evaluasi</small>
                    </div>
                </div>
            </div>
            
            {{-- Right: Filters --}}
            <div class="col-12 col-xl-auto">
                <div class="d-flex flex-wrap gap-2 align-items-center justify-content-xl-end">
                    <form action="{{ route('operator.evaluation.results') }}" method="GET" class="w-100">
                        <div class="d-flex flex-wrap gap-3 align-items-end">
                            
                            {{-- Status --}}
                            {{-- REMOVED FILTERS AS REQUESTED --}}
                            
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">No</th>
                        <th class="py-3 text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Operator</th>
                        <th class="py-3 text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Bagian</th>
                        <th class="py-3 text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Kategori Soal</th>
                        <th class="py-3 text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                        <th class="py-3 text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($results as $key => $result)
                        <tr>
                            <td class="ps-4">
                                <span class="text-secondary text-xs font-weight-bold">{{ $results->firstItem() + $key }}</span>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <h6 class="mb-0 text-sm fw-bold text-dark">{{ $result->user->name }}</h6>
                                    <span class="text-xs text-secondary">{{ $result->user->nik }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary px-2 py-1">
                                    {{ ucfirst($result->user->division ?? '-') }}
                                </span>
                            </td>
                            <td>
                                <span class="text-dark text-sm fw-bold">{{ $result->category_name }}</span>
                            </td>
                            <td class="text-center">
                                @if($result->type == 'active')
                                    @if($result->status == 'pending')
                                        <span class="badge bg-warning text-dark border border-warning">
                                            <i class="fas fa-spinner fa-spin me-1 small"></i>Menunggu
                                        </span>
                                    @else
                                        <span class="badge bg-light text-success border border-success">
                                            <i class="fas fa-check-double me-1 small"></i>Selesai
                                        </span>
                                    @endif
                                @else
                                    <span class="badge bg-light text-secondary border border-secondary">
                                        <i class="fas fa-archive me-1"></i>Arsip
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    @if($result->type == 'history')
                                        <span class="text-xs text-muted mb-1"><i class="fas fa-history me-1"></i>Reset:</span>
                                    @endif
                                    <span class="text-secondary text-xs font-weight-bold">
                                        <i class="far fa-calendar me-1"></i>{{ $result->sort_date->format('d/m/y') }}
                                    </span>
                                    <span class="text-secondary text-xs">
                                        <i class="far fa-clock me-1"></i>{{ $result->sort_date->format('H:i') }}
                                    </span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center justify-content-center">
                                    <div class="rounded-circle p-4 mb-3" style="background-color: #ffebee;">
                                        <i class="fas fa-clipboard-list fa-3x" style="color: #C62828;"></i>
                                    </div>
                                    <h6 class="text-muted fw-bold">Belum ada data evaluasi</h6>
                                    <p class="text-muted small mb-0">Hasil evaluasi Anda akan muncul di sini.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-top">
            {{ $results->links() }}
        </div>
    </div>
</div>

<style>
    .text-xxs { font-size: 0.7rem; }
    .text-xs { font-size: 0.8rem; }
    .opacity-7 { opacity: 0.7; }
</style>
@endsection
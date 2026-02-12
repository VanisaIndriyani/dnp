@extends('layouts.super_admin')

@section('title', 'Verifikasi Nilai Evaluasi')

@section('content')

{{-- PAGE HEADER --}}
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h2 class="mb-1 fw-bold text-dark">Verifikasi Nilai Evaluasi</h2>
        <p class="text-muted mb-0">Review dan publikasikan hasil evaluasi operator sebelum ditampilkan di daftar utama.</p>
    </div>
    
    <div class="d-flex gap-2">
        <a href="{{ route('super_admin.evaluation.results') }}" class="btn btn-danger shadow-sm text-white fw-bold">
            <i class="fas fa-file-invoice me-2"></i> Laporan Nilai
        </a>
    </div>
</div>

{{-- STATS DASHBOARD --}}
@php
    $cardStyles = [
        'cover' => ['label' => 'Cover'],
        'case' => ['label' => 'Case'],
        'inner' => ['label' => 'Inner'],
        'endplate' => ['label' => 'Endplate'],
    ];
@endphp

<div class="row g-4 mb-5">
    @foreach($stats as $div => $stat)
    @php 
        $style = $cardStyles[$div] ?? ['label' => ucfirst($div)];
        $isActive = $division == $div;
    @endphp
    <div class="col-md-6 col-lg-3">
        <a href="{{ route('super_admin.evaluation.verification', ['division' => $div]) }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100 hover-card {{ $isActive ? 'active-card' : '' }} overflow-hidden position-relative">
                <div class="card-body p-4 position-relative">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar-md rounded-3 d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background-color: rgba(220, 53, 69, 0.1);">
                            <i class="fas fa-clipboard-check fa-lg text-danger"></i>
                        </div>
                        <div>
                            <h6 class="card-subtitle text-muted small text-uppercase fw-bold mb-1">Bagian</h6>
                            <h5 class="card-title text-dark mb-0 fw-bold">{{ $style['label'] }}</h5>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <span class="display-6 fw-bold text-dark">{{ $stat['total'] }}</span>
                            <span class="text-muted small ms-1">Belum Publish</span>
                        </div>
                        <div class="text-end">
                            @if($stat['pending'] > 0)
                                <span class="badge bg-warning text-dark rounded-pill mb-1">
                                    <i class="fas fa-clock me-1"></i>{{ $stat['pending'] }} Belum Dinilai
                                </span>
                            @else
                                <span class="badge bg-success rounded-pill mb-1">
                                    <i class="fas fa-check me-1"></i>Siap Publish
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                @if($isActive)
                <div class="card-footer border-0 py-2 text-center" style="background-color: rgba(220, 53, 69, 0.1);">
                    <small class="fw-bold text-danger">Sedang Ditampilkan <i class="fas fa-chevron-right ms-1"></i></small>
                </div>
                @endif
            </div>
        </a>
    </div>
    @endforeach
</div>

<style>
    .hover-card { transition: all 0.3s ease; top: 0; }
    .hover-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; border: 1px solid #dc3545 !important; }
    .active-card { border: 2px solid #dc3545 !important; }
</style>

{{-- MAIN CONTENT --}}
<div class="card shadow-sm border-0 mb-5">
    {{-- TOOLBAR HEADER --}}
    <div class="card-header bg-white py-3 px-4 border-bottom">
        <div class="row g-3 align-items-center justify-content-between">
            
            {{-- Left: Title & Active Filter Indicator --}}
            <div class="col-12 col-xl-auto">
                <div class="d-flex align-items-center gap-2">
                    <div class="p-2 rounded" style="background-color: rgba(220, 53, 69, 0.1); color: #dc3545;">
                        <i class="fas fa-list-ul"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold text-dark">Data Belum Dipublikasi</h5>
                        <small class="text-muted">
                            @if(isset($division) && !request('view_all'))
                                Menampilkan data <span class="fw-bold text-dark">Bagian {{ ucfirst($division) }}</span>
                            @else
                                Menampilkan <span class="fw-bold text-dark">Semua Bagian</span>
                            @endif
                        </small>
                    </div>
                </div>
            </div>
            
            {{-- Right: Filters & Actions --}}
            <div class="col-12 col-xl-auto">
                <div class="d-flex flex-wrap gap-2 align-items-center justify-content-xl-end">
                    <form action="{{ route('super_admin.evaluation.verification') }}" method="GET" class="w-100">
                        <div class="d-flex flex-wrap gap-3 align-items-end justify-content-end">
                            
                            {{-- Division --}}
                            <div style="min-width: 140px;">
                                <label class="small fw-bold text-muted mb-1"><i class="fas fa-building me-1 text-danger"></i> Bagian</label>
                                <select name="division" class="form-select form-select-sm shadow-sm rounded-3" onchange="this.form.submit()">
                                    <option value="">Semua Bagian</option>
                                    <option value="cover" {{ request('division') == 'cover' ? 'selected' : '' }}>Cover</option>
                                    <option value="case" {{ request('division') == 'case' ? 'selected' : '' }}>Case</option>
                                    <option value="inner" {{ request('division') == 'inner' ? 'selected' : '' }}>Inner</option>
                                    <option value="endplate" {{ request('division') == 'endplate' ? 'selected' : '' }}>Endplate</option>
                                </select>
                            </div>

                            {{-- Publish Button --}}
                            @if($results->count() > 0)
                            <div class="pb-0 ms-2">
                                <button type="button" class="btn btn-danger shadow-sm rounded-3 fw-bold px-4 py-2 d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#publishModal">
                                    <i class="fas fa-save"></i> Simpan Semua Nilai
                                </button>
                            </div>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- TABLE CONTENT --}}
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-nowrap align-middle mb-0">
                <thead class="bg-light text-muted text-uppercase small fw-bold">
                    <tr>
                        <th class="ps-4 py-3" width="5%">No</th>
                        <th class="py-3" width="20%">Operator</th>
                        <th class="py-3" width="10%">Bagian</th>
                        <th class="py-3" width="15%">Kategori Soal</th>
                        <th class="text-center py-3" width="10%">Nilai PG</th>
                        <th class="text-center py-3" width="10%">Nilai Essay</th>
                        <th class="text-center py-3" width="10%">Total</th>
                        <th class="text-center py-3" width="10%">Status</th>
                        <th class="text-end pe-4 py-3" width="10%">Aksi</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($results as $key => $result)
                        <tr>
                            <td class="ps-4 fw-bold text-muted">{{ $results->firstItem() + $key }}</td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold text-dark">{{ $result->user->name }}</span>
                                    <small class="text-muted">{{ $result->user->nik }}</small>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ ucfirst($result->user->division ?? '-') }}</span>
                            </td>
                            <td>
                                <span class="text-secondary small">{{ $result->sub_categories ?: '-' }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success bg-opacity-10 text-success border border-success">
                                    {{ $result->mc_score }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-opacity-10 border" style="background-color: rgba(198, 40, 40, 0.1); color: var(--primary-color); border-color: var(--primary-color) !important;">
                                    {{ $result->essay_score }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="fw-bold fs-6 {{ $result->score >= $passingGrade ? 'text-success' : 'text-danger' }}">
                                    {{ $result->score }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($result->status == 'pending')
                                    <span class="badge bg-warning text-dark border border-warning">
                                        <i class="fas fa-spinner fa-spin me-1 small"></i>Menunggu Penilaian
                                    </span>
                                @else
                                    <span class="badge bg-success text-white">
                                        <i class="fas fa-check me-1 small"></i>Sudah Dinilai
                                    </span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    <a href="{{ route('super_admin.evaluation.grade', $result->id) }}" class="btn btn-sm btn-outline-danger" title="{{ $result->status == 'pending' ? 'Beri Nilai' : 'Edit Nilai' }}">
                                        <i class="fas {{ $result->status == 'pending' ? 'fa-pen-alt' : 'fa-edit' }}"></i> {{ $result->status == 'pending' ? 'Nilai' : 'Edit' }}
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center justify-content-center opacity-50">
                                    <i class="fas fa-check-circle fa-4x mb-3 text-success"></i>
                                    <h5 class="text-muted">Semua data sudah dipublikasikan</h5>
                                    <p class="small text-muted mb-0">Tidak ada data baru yang perlu diverifikasi.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- PAGINATION --}}
        <div class="px-4 py-3 border-top">
            {{ $results->appends(request()->query())->links() }}
        </div>
    </div>
</div>

<!-- Publish Modal -->
<div class="modal fade" id="publishModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Konfirmasi Simpan Nilai</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menyimpan dan mempublikasikan <strong>{{ $results->total() }}</strong> data nilai evaluasi ini?</p>
                <p class="text-muted small mb-0">Data yang disimpan akan muncul di halaman utama Hasil Nilai Evaluasi dan dapat dilihat oleh operator.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <form action="{{ route('super_admin.evaluation.publishAll') }}" method="POST">
                    @csrf
                    @if(request('division'))
                        <input type="hidden" name="division" value="{{ request('division') }}">
                    @endif
                    <button type="submit" class="btn btn-danger fw-bold">Ya, Simpan Semua</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
@extends('layouts.super_admin')

@section('title', 'Hasil Evaluasi')

@section('content')

{{-- PAGE HEADER --}}
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h2 class="mb-1 fw-bold text-dark">Hasil Nilai Evaluasi</h2>
        <p class="text-muted mb-0">Monitor dan kelola hasil evaluasi operator per bagian.</p>
    </div>
    
    {{-- KKM WIDGET --}}
    <div class="bg-white px-4 py-2 rounded-3 shadow-sm border d-flex align-items-center gap-3">
        <div class="d-flex flex-column">
            <span class="text-muted small text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 1px;">Passing Grade (KKM)</span>
            <div class="d-flex align-items-center gap-2">
                <span class="h4 mb-0 fw-bold text-primary">{{ $passingGrade }}</span>
                <span class="badge bg-light text-primary border rounded-pill px-2 py-1" style="font-size: 0.7rem;">Poin</span>
            </div>
        </div>
        <div class="vr mx-2 bg-secondary opacity-25" style="height: 30px;"></div>
        <button type="button" class="btn btn-outline-primary btn-sm rounded-circle shadow-sm p-0 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" data-bs-toggle="modal" data-bs-target="#updateKKMModal" title="Ubah KKM">
            <i class="fas fa-pen fa-xs"></i>
        </button>
    </div>
</div>

{{-- STATS DASHBOARD --}}
@php
    $cardStyles = [
        'cover' => ['color' => 'primary', 'icon' => 'fa-book-open', 'label' => 'Cover'],
        'case' => ['color' => 'info', 'icon' => 'fa-box', 'label' => 'Case'],
        'inner' => ['color' => 'success', 'icon' => 'fa-layer-group', 'label' => 'Inner'],
        'endplate' => ['color' => 'warning', 'icon' => 'fa-clipboard-check', 'label' => 'Endplate'],
    ];
@endphp

<div class="row g-4 mb-5">
    @foreach($stats as $div => $stat)
    @php 
        $style = $cardStyles[$div] ?? ['color' => 'secondary', 'icon' => 'fa-user', 'label' => ucfirst($div)];
        $isActive = $division == $div;
    @endphp
    <div class="col-md-6 col-lg-3">
        <a href="{{ route('super_admin.evaluation.results', ['division' => $div]) }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100 hover-card {{ $isActive ? 'ring-2 ring-' . $style['color'] : '' }} overflow-hidden position-relative">
                {{-- Decorative Background Icon --}}
                <div class="position-absolute end-0 bottom-0 opacity-10 me-n3 mb-n3 text-{{ $style['color'] }}" style="transform: rotate(-15deg);">
                    <i class="fas {{ $style['icon'] }}" style="font-size: 5rem;"></i>
                </div>

                <div class="card-body p-4 position-relative">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar-md bg-{{ $style['color'] }} bg-opacity-10 rounded-3 d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                            <i class="fas {{ $style['icon'] }} fa-lg text-{{ $style['color'] }}"></i>
                        </div>
                        <div>
                            <h6 class="card-subtitle text-muted small text-uppercase fw-bold mb-1">Bagian</h6>
                            <h5 class="card-title text-dark mb-0 fw-bold">{{ $style['label'] }}</h5>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <span class="display-6 fw-bold text-dark">{{ $stat['total'] }}</span>
                            <span class="text-muted small ms-1">Total</span>
                        </div>
                        <div class="text-end">
                            @if($stat['pending'] > 0)
                                <span class="badge bg-warning text-dark rounded-pill mb-1">
                                    <i class="fas fa-clock me-1"></i>{{ $stat['pending'] }} Pending
                                </span>
                            @else
                                <span class="badge bg-success bg-opacity-10 text-success border border-success rounded-pill mb-1">
                                    <i class="fas fa-check me-1"></i>All Graded
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                @if($isActive)
                <div class="card-footer bg-{{ $style['color'] }} bg-opacity-10 border-0 py-2 text-center">
                    <small class="text-{{ $style['color'] }} fw-bold">Sedang Ditampilkan <i class="fas fa-chevron-right ms-1"></i></small>
                </div>
                @endif
            </div>
        </a>
    </div>
    @endforeach
</div>

<style>
    .hover-card { transition: all 0.3s ease; top: 0; }
    .hover-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important; }
    .ring-2 { border: 2px solid transparent; }
    .ring-primary { border-color: var(--bs-primary) !important; }
    .ring-info { border-color: var(--bs-info) !important; }
    .ring-success { border-color: var(--bs-success) !important; }
    .ring-warning { border-color: var(--bs-warning) !important; }
    .opacity-10 { opacity: 0.1; }
</style>

{{-- MAIN CONTENT --}}
<div class="card shadow-sm border-0 mb-5">
    {{-- TOOLBAR HEADER --}}
    <div class="card-header bg-white py-3 px-4 border-bottom">
        <div class="row g-3 align-items-center justify-content-between">
            
            {{-- Left: Title & Active Filter Indicator --}}
            <div class="col-12 col-xl-auto">
                <div class="d-flex align-items-center gap-2">
                    <div class="bg-primary bg-opacity-10 p-2 rounded text-primary">
                        <i class="fas fa-table"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold text-dark">Daftar Nilai</h5>
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
                    <form action="{{ route('super_admin.evaluation.results') }}" method="GET" class="w-100">
                        <div class="d-flex flex-wrap gap-3 align-items-end">
                            
                            {{-- Search --}}
                            <div class="flex-grow-1" style="min-width: 200px;">
                                <label class="small fw-bold text-muted mb-1"><i class="fas fa-search me-1 text-primary"></i> Pencarian</label>
                                <div class="input-group input-group-sm shadow-sm rounded-3">
                                    <span class="input-group-text bg-white border-end-0 ps-3"><i class="fas fa-search text-muted"></i></span>
                                    <input type="text" name="nik" class="form-control border-start-0 ps-2" placeholder="Cari NIK..." value="{{ request('nik') }}">
                                </div>
                            </div>

                            {{-- Division --}}
                            <div style="min-width: 140px;">
                                <label class="small fw-bold text-muted mb-1"><i class="fas fa-building me-1 text-info"></i> Bagian</label>
                                <select name="division" class="form-select form-select-sm shadow-sm rounded-3" onchange="this.form.submit()">
                                    <option value="">Semua Bagian</option>
                                    <option value="cover" {{ request('division') == 'cover' ? 'selected' : '' }}>Cover</option>
                                    <option value="case" {{ request('division') == 'case' ? 'selected' : '' }}>Case</option>
                                    <option value="inner" {{ request('division') == 'inner' ? 'selected' : '' }}>Inner</option>
                                    <option value="endplate" {{ request('division') == 'endplate' ? 'selected' : '' }}>Endplate</option>
                                </select>
                            </div>
                            
                            {{-- Status --}}
                            <div style="min-width: 140px;">
                                <label class="small fw-bold text-muted mb-1"><i class="fas fa-check-circle me-1 text-success"></i> Status</label>
                                <select name="status_kelulusan" class="form-select form-select-sm shadow-sm rounded-3" onchange="this.form.submit()">
                                    <option value="">Semua Status</option>
                                    <option value="lulus" {{ request('status_kelulusan') == 'lulus' ? 'selected' : '' }}>Lulus</option>
                                    <option value="tidak_lulus" {{ request('status_kelulusan') == 'tidak_lulus' ? 'selected' : '' }}>Tidak Lulus</option>
                                </select>
                            </div>

                            {{-- Category --}}
                            <div style="min-width: 140px;">
                                <label class="small fw-bold text-muted mb-1"><i class="fas fa-tags me-1 text-warning"></i> Kategori</label>
                                <select name="sub_category" class="form-select form-select-sm shadow-sm rounded-3" onchange="this.form.submit()">
                                    <option value="">Semua Kategori</option>
                                    @foreach($subCategories as $cat)
                                        <option value="{{ $cat }}" {{ request('sub_category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Date Range --}}
                            <div style="min-width: 220px;">
                                <label class="small fw-bold text-muted mb-1"><i class="fas fa-calendar-alt me-1 text-secondary"></i> Rentang Tanggal</label>
                                <div class="input-group input-group-sm shadow-sm rounded-3">
                                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}" onchange="this.form.submit()">
                                    <span class="input-group-text bg-white border-start-0 border-end-0"><i class="fas fa-arrow-right fa-xs text-muted"></i></span>
                                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}" onchange="this.form.submit()">
                                </div>
                            </div>

                            {{-- View All --}}
                            <div class="pb-1">
                                <div class="form-check form-switch mb-0" title="Tampilkan semua data tanpa filter bagian">
                                    <input class="form-check-input" type="checkbox" name="view_all" value="1" id="viewAllCheck" {{ request('view_all') ? 'checked' : '' }} style="cursor: pointer;" onchange="this.form.submit()">
                                    <label class="form-check-label small fw-bold text-muted" for="viewAllCheck" style="cursor: pointer;">All</label>
                                </div>
                            </div>

                            <div class="vr mx-1 d-none d-xl-block bg-secondary opacity-25 py-3 align-self-center"></div>

                            {{-- Export Button --}}
                            <div class="ms-auto pb-0">
                                <a href="{{ route('super_admin.evaluation.results.export', array_merge(request()->query(), ['export_type' => 'all'])) }}" class="btn btn-success btn-sm shadow-sm rounded-3 fw-bold px-3 py-2 d-flex align-items-center gap-2">
                                    <i class="fas fa-file-excel"></i> Export Excel
                                </a>
                            </div>
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
                        <th class="py-3" width="15%">Operator</th>
                        <th class="py-3" width="8%">Bagian</th>
                        <th class="py-3" width="12%">Kategori Soal</th>
                        <th class="text-center py-3" width="7%">Nilai PG</th>
                        <th class="text-center py-3" width="7%">Nilai Essay</th>
                        <th class="text-center py-3" width="7%">Total</th>
                        <th class="text-center py-3" width="8%">Status</th>
                        <th class="text-center py-3" width="8%">Penilaian</th>
                        <th class="py-3" width="10%">Tanggal</th>
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
                                <span class="badge bg-success bg-opacity-10 text-success border border-success" title="Nilai PG">
                                    {{ $result->mc_score }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary" title="Nilai Essay">
                                    {{ $result->essay_score }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="fw-bold fs-6 {{ $result->score >= $passingGrade ? 'text-success' : 'text-danger' }}">
                                    {{ $result->score }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($result->score >= $passingGrade)
                                    <span class="badge bg-success rounded-pill px-3">LULUS</span>
                                @else
                                    <span class="badge bg-danger rounded-pill px-3">TIDAK</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($result->status == 'pending')
                                    <span class="badge bg-warning text-dark border border-warning">
                                        <i class="fas fa-spinner fa-spin me-1 small"></i>Menunggu
                                    </span>
                                @else
                                    <span class="badge bg-light text-success border border-success">
                                        <i class="fas fa-check-double me-1 small"></i>Selesai
                                    </span>
                                @endif
                            </td>
                            <td class="text-muted small">
                                <i class="far fa-calendar me-1"></i>{{ $result->created_at->format('d/m/y') }}<br>
                                <i class="far fa-clock me-1"></i>{{ $result->created_at->format('H:i') }}
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    <a href="{{ route('super_admin.evaluation.grade', $result->id) }}" class="btn btn-sm btn-outline-primary" title="{{ $result->status == 'pending' ? 'Beri Nilai' : 'Edit Nilai' }}">
                                        <i class="fas {{ $result->status == 'pending' ? 'fa-pen-alt' : 'fa-edit' }}"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger" title="Reset Evaluasi" onclick="confirmReset('{{ route('super_admin.evaluation.results.destroy', $result->id) }}')">
                                        <i class="fas fa-redo-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        @if(!isset($histories) || $histories->count() == 0)
                        <tr>
                            <td colspan="11" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center justify-content-center opacity-50">
                                    <i class="fas fa-clipboard-list fa-4x mb-3 text-secondary"></i>
                                    <h5 class="text-muted">Tidak ada data hasil evaluasi</h5>
                                    <p class="small text-muted mb-0">Coba ubah filter pencarian Anda.</p>
                                </div>
                            </td>
                        </tr>
                        @endif
                    @endforelse

                    {{-- MERGED HISTORY DATA --}}
                    @if(isset($histories) && $histories->count() > 0)
                        @php $startNo = $results->lastItem() ?? 0; @endphp
                        @foreach($histories as $history)
                        <tr class="bg-light bg-opacity-25">
                            <td class="ps-4 fw-bold text-muted">{{ $startNo + $loop->iteration }}</td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold text-secondary">{{ $history->user->name }}</span>
                                    <small class="text-muted">{{ $history->user->nik }}</small>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-secondary border">{{ ucfirst($history->user->division ?? '-') }}</span>
                            </td>
                            <td>
                                <span class="text-muted small">-</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary" title="Nilai PG">
                                    {{ $history->mc_score }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary" title="Nilai Essay">
                                    {{ $history->essay_score }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="fw-bold fs-6 {{ $history->score >= $passingGrade ? 'text-success' : 'text-danger' }} opacity-75">
                                    {{ $history->score }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($history->score >= $passingGrade)
                                    <span class="badge bg-success bg-opacity-75 rounded-pill px-3">LULUS</span>
                                @else
                                    <span class="badge bg-danger bg-opacity-75 rounded-pill px-3">TIDAK</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-secondary border border-secondary">
                                    <i class="fas fa-archive me-1 small"></i>Arsip
                                </span>
                            </td>
                            <td class="text-muted small">
                                <span class="d-block text-secondary"><i class="fas fa-history me-1"></i>Reset:</span>
                                {{ $history->archived_at->format('d/m/y') }}
                            </td>
                            <td class="text-end pe-4">
                                <button type="button" class="btn btn-sm btn-light text-muted border-0" disabled title="Data telah direset">
                                    <i class="fas fa-check-circle me-1"></i>Selesai
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-top bg-light bg-opacity-50">
            {{ $results->appends(request()->all())->links() }}
        </div>
    </div>
</div>



{{-- MODALS & SCRIPTS --}}
<div class="modal fade" id="updateKKMModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-bottom-0 pb-0">
                <h6 class="modal-title fw-bold">Update KKM</h6>
                <button type="button" class="btn-close small" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('super_admin.evaluation.update_passing_grade') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="text-center mb-4 mt-2">
                        <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle mx-auto d-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                            <span class="h4 mb-0 fw-bold text-primary">{{ $passingGrade }}</span>
                        </div>
                        <small class="text-muted">Nilai minimal saat ini</small>
                    </div>
                    
                    <div class="form-floating mb-1">
                        <input type="number" name="passing_grade" class="form-control" id="kkmInput" value="{{ $passingGrade }}" min="0" max="100" required>
                        <label for="kkmInput">Nilai Baru (0-100)</label>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<form id="destroy-form" action="" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-check View All when Date is selected
        const dateInputs = document.querySelectorAll('input[name="start_date"], input[name="end_date"]');
        const viewAllCheck = document.getElementById('viewAllCheck');
        
        dateInputs.forEach(input => {
            input.addEventListener('change', function() {
                if (this.value && !viewAllCheck.checked) {
                    viewAllCheck.checked = true;
                }
            });
        });
    });

    function confirmReset(url) {
        Swal.fire({ 
            title: 'Reset Evaluasi?',
            text: "Hasil evaluasi ini akan diarsipkan dan Operator harus mengerjakan ulang ujian. Tindakan ini tidak dapat dibatalkan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Reset Data!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('destroy-form');
                form.action = url;
                form.submit();
            }
        })
    }
</script>
@endsection
@extends('layouts.admin')

@section('title', 'Hasil Evaluasi')

@section('content')

{{-- PAGE HEADER --}}
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h2 class="mb-1 fw-bold text-dark">Hasil Nilai Evaluasi</h2>
        <p class="text-muted mb-0">Monitor hasil evaluasi operator.</p>
    </div>
    
    {{-- KKM WIDGET (Read Only) --}}
    <div class="bg-white px-4 py-2 rounded-3 shadow-sm border d-flex align-items-center gap-3">
        <div class="d-flex flex-column">
            <span class="text-muted small text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 1px;">Passing Grade (KKM)</span>
            <div class="d-flex align-items-center gap-2">
                <span class="h4 mb-0 fw-bold" style="color: var(--primary-color);">{{ $passingGrade }}</span>
                <span class="badge bg-light border rounded-pill px-2 py-1" style="color: var(--primary-color); font-size: 0.7rem;">Poin</span>
            </div>
        </div>
    </div>
</div>

{{-- STATS DASHBOARD --}}
@php
    $cardStyles = [
        'cover' => ['icon' => 'fa-book-open', 'label' => 'Cover'],
        'case' => ['icon' => 'fa-box', 'label' => 'Case'],
        'inner' => ['icon' => 'fa-layer-group', 'label' => 'Inner'],
        'endplate' => ['icon' => 'fa-clipboard-check', 'label' => 'Endplate'],
    ];
@endphp

<div class="row g-4 mb-5">
    @foreach($stats as $div => $stat)
    @php 
        $style = $cardStyles[$div] ?? ['icon' => 'fa-user', 'label' => ucfirst($div)];
        $isActive = $division == $div;
    @endphp
    <div class="col-md-6 col-lg-3">
        <a href="{{ route('admin.evaluation.results', ['division' => $div]) }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100 hover-card {{ $isActive ? 'ring-2 ring-primary' : '' }} overflow-hidden position-relative">
                {{-- Decorative Background Icon --}}
                <div class="position-absolute end-0 bottom-0 opacity-10 me-n3 mb-n3" style="color: var(--primary-color); transform: rotate(-15deg);">
                    <i class="fas {{ $style['icon'] }}" style="font-size: 5rem;"></i>
                </div>

                <div class="card-body p-4 position-relative">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar-md bg-opacity-10 rounded-3 d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background-color: rgba(198, 40, 40, 0.1);">
                            <i class="fas {{ $style['icon'] }} fa-lg" style="color: var(--primary-color);"></i>
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
                <div class="card-footer bg-opacity-10 border-0 py-2 text-center" style="background-color: rgba(198, 40, 40, 0.1);">
                    <small class="fw-bold" style="color: var(--primary-color);">Sedang Ditampilkan <i class="fas fa-chevron-right ms-1"></i></small>
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
    .ring-primary { border-color: var(--primary-color) !important; }
    .opacity-10 { opacity: 0.1; }
</style>

{{-- MAIN CONTENT --}}
<div class="card shadow-sm border-0 mb-5">
    
    {{-- TABLE CONTENT --}}
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-nowrap align-middle mb-0">
                <thead class="bg-light text-muted text-uppercase small fw-bold">
                    <tr>
                        <th class="ps-4 py-3" width="5%">No</th>
                        <th class="py-3" width="20%">Operator</th>
                        <th class="py-3" width="10%">Bagian</th>
                        <th class="py-3" width="12%">Kategori Soal</th>
                        <th class="text-center py-3" width="8%">Nilai PG</th>
                        <th class="text-center py-3" width="8%">Nilai Essay</th>
                        <th class="text-center py-3" width="8%">Total</th>
                        <th class="text-center py-3" width="10%">Status</th>
                        <th class="text-center py-3" width="10%">Penilaian</th>
                        <th class="text-end pe-4 py-3" width="9%">Tanggal</th>
                        <th class="text-center py-3" width="10%">Aksi</th>
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
                                <span class="badge bg-opacity-10 text-danger border border-danger" title="Nilai Essay" style="background-color: rgba(198, 40, 40, 0.1); color: var(--primary-color) !important; border-color: var(--primary-color) !important;">
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
                                        <i class="fas fa-check me-1 small"></i>Selesai
                                    </span>
                                @endif
                            </td>
                            <td class="text-end pe-4 text-muted small">
                                <i class="far fa-calendar me-1"></i>{{ $result->created_at->format('d/m/y') }}<br>
                                <i class="far fa-clock me-1"></i>{{ $result->created_at->format('H:i') }}
                            </td>
                            <td class="text-center">
                                {{-- Read Only for Admin --}}
                                <span class="text-muted">-</span>
                            </td>
                        </tr>
                    @empty
                        @if(!isset($histories) || $histories->count() == 0)
                        <tr>
                            <td colspan="11" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center justify-content-center opacity-50">
                                    <i class="fas fa-clipboard-list fa-4x mb-3 text-secondary"></i>
                                    <h5 class="text-muted">Tidak ada data hasil evaluasi</h5>
                                </div>
                            </td>
                        </tr>
                        @endif
                    @endforelse

                    {{-- History Section (Integrated) --}}
                    @if(isset($histories) && $histories->count() > 0)
                        @foreach($histories as $history)
                        <tr>
                            <td class="ps-4 fw-bold text-muted">{{ $results->total() + $loop->iteration }}</td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold text-muted">{{ $history->user->name }}</span>
                                    <small class="text-muted">{{ $history->user->nik }}</small>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-muted border">{{ ucfirst($history->user->division ?? '-') }}</span>
                            </td>
                            <td class="text-muted small">{{ $history->sub_categories ?? '-' }}</td>
                            <td class="text-center text-muted">{{ $history->mc_score }}</td>
                            <td class="text-center text-muted">{{ $history->essay_score }}</td>
                            <td class="text-center">
                                <span class="fw-bold fs-6 text-muted">
                                    {{ $history->score }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($history->score >= $passingGrade)
                                    <span class="badge bg-success bg-opacity-50 rounded-pill px-3">LULUS</span>
                                @else
                                    <span class="badge bg-danger bg-opacity-50 rounded-pill px-3">TIDAK</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-white text-secondary border">
                                    <i class="fas fa-trash me-1 small"></i>Arsip
                                </span>
                            </td>
                            <td class="text-end pe-4 text-muted small">
                                <span class="fw-bold text-secondary">Reset:</span><br>
                                {{ $history->archived_at->format('d/m/y') }}
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-muted">
                                    <i class="fas fa-check-circle me-1"></i>Selesai
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        <div class="px-4 py-3 border-top">
            {{ $results->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection

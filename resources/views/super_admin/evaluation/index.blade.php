@extends('layouts.super_admin')

@section('title', 'Manajemen Evaluasi')

@section('content')

@if(isset($stats))
    {{-- CATEGORY DASHBOARD VIEW --}}
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="mb-2">Bank Soal Evaluasi</h2>
            <p class="text-muted">Pilih bagian untuk mengelola soal evaluasi.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        @foreach($stats as $cat => $stat)
        <div class="col-md-6 col-lg-3">
            <a href="{{ route('super_admin.evaluation.index', ['category' => $cat]) }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100 hover-card">
                    <div class="card-body text-center p-4">
                        <div class="avatar-lg mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; background-color: rgba(198, 40, 40, 0.1);">
                            <i class="fas fa-layer-group fa-2x" style="color: var(--primary-color);"></i>
                        </div>
                        <h4 class="card-title text-dark mb-1">{{ ucfirst($cat) }}</h4>
                        <p class="text-muted small mb-3">Kelola soal untuk bagian {{ ucfirst($cat) }}</p>
                        
                        <div class="d-flex justify-content-center gap-3">
                            <div class="text-center">
                                <h5 class="mb-0 fw-bold text-success">{{ $stat['mc'] }}</h5>
                                <small class="text-muted" style="font-size: 0.75rem;">PG</small>
                            </div>
                            <div class="vr"></div>
                            <div class="text-center">
                                <h5 class="mb-0 fw-bold text-warning">{{ $stat['essay'] }}</h5>
                                <small class="text-muted" style="font-size: 0.75rem;">Essay</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0 pb-3">
                        <span class="btn btn-sm w-100 rounded-pill" style="color: var(--primary-color); border-color: var(--primary-color); background-color: white;">Lihat Soal</span>
                    </div>
                </div>
            </a>
        </div>
        @endforeach
        
        <!-- Optional: General/Umum Category if needed, currently filtering out NULLs in controller for strict mode, 
             but if we want to support 'Umum' questions we can add a card here manually -->
    </div>

    <style>
        .hover-card {
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .hover-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(198, 40, 40, 0.15) !important;
            border: 1px solid var(--primary-color) !important;
        }
    </style>

@else
    {{-- QUESTION LIST VIEW (FILTERED BY CATEGORY) --}}
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <a href="{{ route('super_admin.evaluation.index') }}" class="text-decoration-none text-muted mb-2 d-inline-block">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Bagian
                </a>
                <h2 class="mb-0">Soal Evaluasi: <span class="text-primary">{{ ucfirst($category) }}</span></h2>
            </div>
            <div>
                @can('super_admin')
                
                {{-- Filter Sub Category --}}
                <form action="{{ route('super_admin.evaluation.index') }}" method="GET" class="d-inline-block me-2">
                    <input type="hidden" name="category" value="{{ $category }}">
                    <div class="input-group shadow-sm rounded-pill overflow-hidden border" style="background: white;">
                        <span class="input-group-text bg-transparent border-0 pe-0 ps-3" style="color: var(--primary-color);">
                            <i class="fas fa-filter fa-sm"></i>
                        </span>
                        <select name="sub_category" class="form-select border-0 shadow-none ps-2 fw-bold text-dark" onchange="this.form.submit()" style="min-width: 160px; cursor: pointer; background-color: transparent; font-size: 0.9rem;">
                            <option value="" class="text-muted fw-normal">Semua Kategori</option>
                            @foreach($availableSubCategories as $sub)
                                <option value="{{ $sub }}" {{ isset($subCategory) && $subCategory == $sub ? 'selected' : '' }} class="fw-bold">{{ $sub }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>

                <form id="deleteAllForm" action="{{ route('super_admin.evaluation.destroyAll') }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="category" value="{{ $category }}">
                    <button type="button" class="btn btn-outline-danger me-2" onclick="confirmDeleteAll('{{ ucfirst($category) }}')">
                        <i class="fas fa-trash-alt me-2"></i>Hapus Semua
                    </button>
                </form>

                <button type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#importModal">
                    <i class="fas fa-file-excel me-2"></i>Import Excel
                </button>

                <div class="btn-group">
                    <a href="{{ route('super_admin.evaluation.create', ['type' => 'multiple_choice', 'category' => $category]) }}" class="btn btn-danger" style="background-color: var(--primary-color);">
                        <i class="fas fa-plus me-2"></i>Tambah Pilihan Ganda
                    </a>
                    <a href="{{ route('super_admin.evaluation.create', ['type' => 'essay', 'category' => $category]) }}" class="btn btn-warning text-white">
                        <i class="fas fa-plus me-2"></i>Tambah Essay
                    </a>
                </div>
                @endcan
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            
            @php
                $activeTab = request('tab') == 'essay' ? 'essay' : 'mc';
            @endphp

            <!-- Tabs -->
            <ul class="nav nav-tabs mb-3" id="evaluationTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab == 'mc' ? 'active' : '' }}" id="mc-tab" data-bs-toggle="tab" data-bs-target="#mc" type="button" role="tab" aria-controls="mc" aria-selected="{{ $activeTab == 'mc' ? 'true' : 'false' }}">
                        <i class="fas fa-list-ul me-2"></i>Pilihan Ganda
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab == 'essay' ? 'active' : '' }}" id="essay-tab" data-bs-toggle="tab" data-bs-target="#essay" type="button" role="tab" aria-controls="essay" aria-selected="{{ $activeTab == 'essay' ? 'true' : 'false' }}">
                        <i class="fas fa-align-left me-2"></i>Essay
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="evaluationTabsContent">
                <!-- Multiple Choice Tab -->
                <div class="tab-pane fade {{ $activeTab == 'mc' ? 'show active' : '' }}" id="mc" role="tabpanel" aria-labelledby="mc-tab">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4" style="width: 5%;">#</th>
                                    <th style="width: 15%;">Kategori</th>
                                    {{-- Removed 'Bagian' column since we are in a specific category view --}}
                                    <th style="width: 30%;">Pertanyaan</th>
                                    <th style="width: 10%;">Kunci</th>
                                    <th style="width: 25%;">Opsi Jawaban</th>
                                    <th class="text-end pe-4" style="width: 15%;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($mcQuestions as $key => $evaluation)
                                    <tr>
                                        <td class="ps-4">{{ $mcQuestions->firstItem() + $key }}</td>
                                        <td><span class="badge bg-info text-dark">{{ $evaluation->sub_category ?? '-' }}</span></td>
                                        <td>{{ Str::limit($evaluation->question, 100) }}</td>
                                        <td><span class="badge bg-success">{{ strtoupper($evaluation->correct_answer) }}</span></td>
                                        <td>
                                            <small>
                                                A: {{ $evaluation->option_a }}<br>
                                                B: {{ $evaluation->option_b }}<br>
                                                C: {{ $evaluation->option_c }}<br>
                                                D: {{ $evaluation->option_d }}
                                            </small>
                                        </td>
                                        <td class="text-end pe-4">
                                            @can('super_admin')
                                            <a href="{{ route('super_admin.evaluation.edit', $evaluation) }}" class="btn btn-sm btn-warning text-white me-1">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('super_admin.evaluation.destroy', $evaluation) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus soal ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            @else
                                            <span class="text-muted"><i class="fas fa-lock"></i></span>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4">Belum ada soal pilihan ganda untuk bagian {{ ucfirst($category) }}.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="p-3">
                        {{ $mcQuestions->appends(['essay_page' => $essayQuestions->currentPage(), 'category' => request('category'), 'tab' => 'mc'])->links() }}
                    </div>
                </div>

                <!-- Essay Tab -->
                <div class="tab-pane fade {{ $activeTab == 'essay' ? 'show active' : '' }}" id="essay" role="tabpanel" aria-labelledby="essay-tab">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4" style="width: 5%;">#</th>
                                    <th style="width: 15%;">Kategori</th>
                                    <th style="width: 55%;">Pertanyaan</th>
                                    <th class="text-end pe-4" style="width: 25%;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($essayQuestions as $key => $evaluation)
                                    <tr>
                                        <td class="ps-4">{{ $essayQuestions->firstItem() + $key }}</td>
                                        <td><span class="badge bg-info text-dark">{{ $evaluation->sub_category ?? '-' }}</span></td>
                                        <td>{{ Str::limit($evaluation->question, 150) }}</td>
                                        <td class="text-end pe-4">
                                            @can('super_admin')
                                            <a href="{{ route('super_admin.evaluation.edit', $evaluation) }}" class="btn btn-sm btn-warning text-white me-1">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('super_admin.evaluation.destroy', $evaluation) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus soal ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            @else
                                            <span class="text-muted"><i class="fas fa-lock"></i></span>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4">Belum ada soal essay untuk bagian {{ ucfirst($category) }}.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="p-3">
                        {{ $essayQuestions->appends(['mc_page' => $mcQuestions->currentPage(), 'category' => request('category'), 'tab' => 'essay'])->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Import Modal -->
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('super_admin.evaluation.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="category" value="{{ $category ?? '' }}">
                    <div class="modal-header">
                        <h5 class="modal-title" id="importModalLabel">Import Soal Evaluasi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="sub_category" value="{{ $subCategory ?? '' }}">
                        <div class="mb-3">
                            <label for="file" class="form-label">Pilih File Excel (.xlsx, .xls, .csv)</label>
                            <input type="file" class="form-control" id="file" name="file" required accept=".xlsx, .xls, .csv">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmDeleteAll(category) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Anda akan menghapus SEMUA soal untuk bagian " + category + ". Tindakan ini tidak dapat dibatalkan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus Semua!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('deleteAllForm').submit();
            }
        })
    }
</script>
@endsection
@extends('layouts.admin')

@section('title', 'Manajemen Evaluasi')

@section('content')
<!-- Header -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
    <div>
        <h2 class="mb-1 fw-bold text-dark">Bank Soal Evaluasi</h2>
        <p class="text-muted mb-0">Kelola daftar pertanyaan untuk evaluasi karyawan.</p>
    </div>
    <div class="mt-3 mt-md-0 d-flex gap-2">
        <a href="{{ route('admin.evaluation.results') }}" class="btn btn-outline-primary shadow-sm">
            <i class="fas fa-poll me-2"></i>Lihat Hasil Nilai
        </a>
        <a href="{{ route('admin.evaluation.create') }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus me-2"></i>Tambah Soal
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 border-start border-success border-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="fas fa-check-circle me-2 fs-5"></i>
            <div>{{ session('success') }}</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-header text-white" style="background-color: var(--primary-color);">
        <h5 class="mb-0 fw-bold"><i class="fas fa-list me-2"></i>Daftar Soal</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 text-secondary small text-uppercase fw-bold" style="width: 5%;">#</th>
                        <th class="py-3 text-secondary small text-uppercase fw-bold" style="width: 40%;">Pertanyaan</th>
                        <th class="py-3 text-secondary small text-uppercase fw-bold" style="width: 10%;">Kunci</th>
                        <th class="py-3 text-secondary small text-uppercase fw-bold" style="width: 30%;">Opsi Jawaban</th>
                        <th class="text-end pe-4 py-3 text-secondary small text-uppercase fw-bold" style="width: 15%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($evaluations as $key => $evaluation)
                        <tr>
                            <td class="ps-4 text-muted fw-bold">{{ $evaluations->firstItem() + $key }}</td>
                            <td>
                                <div class="fw-medium text-dark">{{ Str::limit($evaluation->question, 100) }}</div>
                                <div class="small text-muted mt-1">Dibuat: {{ $evaluation->created_at->format('d M Y') }}</div>
                            </td>
                            <td>
                                <span class="badge bg-light text-primary border border-primary rounded-pill fs-6 px-3">
                                    {{ strtoupper($evaluation->correct_answer) }}
                                </span>
                            </td>
                            <td>
                                <ul class="list-unstyled mb-0 small text-muted">
                                    <li class="{{ $evaluation->correct_answer == 'a' ? 'text-primary fw-bold' : '' }}">
                                        <span class="fw-bold me-1">A.</span> {{ $evaluation->option_a }}
                                        @if($evaluation->correct_answer == 'a') <i class="fas fa-check ms-1"></i> @endif
                                    </li>
                                    <li class="{{ $evaluation->correct_answer == 'b' ? 'text-primary fw-bold' : '' }}">
                                        <span class="fw-bold me-1">B.</span> {{ $evaluation->option_b }}
                                        @if($evaluation->correct_answer == 'b') <i class="fas fa-check ms-1"></i> @endif
                                    </li>
                                    <li class="{{ $evaluation->correct_answer == 'c' ? 'text-primary fw-bold' : '' }}">
                                        <span class="fw-bold me-1">C.</span> {{ $evaluation->option_c }}
                                        @if($evaluation->correct_answer == 'c') <i class="fas fa-check ms-1"></i> @endif
                                    </li>
                                    <li class="{{ $evaluation->correct_answer == 'd' ? 'text-primary fw-bold' : '' }}">
                                        <span class="fw-bold me-1">D.</span> {{ $evaluation->option_d }}
                                        @if($evaluation->correct_answer == 'd') <i class="fas fa-check ms-1"></i> @endif
                                    </li>
                                </ul>
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.evaluation.edit', $evaluation) }}" class="btn btn-sm btn-outline-primary shadow-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.evaluation.destroy', $evaluation) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus soal ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger shadow-sm" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center justify-content-center opacity-50">
                                    <i class="fas fa-clipboard-question fa-4x mb-3 text-muted"></i>
                                    <h5 class="text-muted">Belum ada soal evaluasi.</h5>
                                    @can('super_admin')
                                    <p class="small text-muted mb-0">Klik tombol "Tambah Soal" untuk memulai.</p>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-top bg-light">
            {{ $evaluations->links() }}
        </div>
    </div>
</div>
@endsection

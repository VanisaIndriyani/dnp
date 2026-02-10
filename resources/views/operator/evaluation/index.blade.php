@extends('layouts.operator')

@section('title', 'Manajemen Evaluasi')

@section('content')
<div class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <h2 class="mb-0">Bank Soal Evaluasi</h2>
        <div>
            <a href="{{ route('operator.evaluation.results') }}" class="btn btn-info text-white me-2">
                <i class="fas fa-poll me-2"></i>Lihat Hasil Nilai
            </a>
            @can('super_admin')
            <a href="{{ route('super_admin.evaluation.create') }}" class="btn btn-danger" style="background-color: var(--primary-color);">
                <i class="fas fa-plus me-2"></i>Tambah Soal
            </a>
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

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4" style="width: 5%;">#</th>
                        <th style="width: 40%;">Pertanyaan</th>
                        <th style="width: 10%;">Kunci</th>
                        <th style="width: 30%;">Opsi Jawaban</th>
                        <th class="text-end pe-4" style="width: 15%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($evaluations as $key => $evaluation)
                        <tr>
                            <td class="ps-4">{{ $evaluations->firstItem() + $key }}</td>
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
                            <td colspan="5" class="text-center py-4">Belum ada soal evaluasi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">
            {{ $evaluations->links() }}
        </div>
    </div>
</div>
@endsection


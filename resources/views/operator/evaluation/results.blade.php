@extends('layouts.operator')

@section('title', 'Hasil Evaluasi')

@section('content')
<div class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <h2 class="mb-0">Hasil Nilai Evaluasi</h2>
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
                        <th>Nilai</th>
                        <th>Tanggal Evaluasi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($results as $key => $result)
                        <tr>
                            <td class="ps-4">{{ $results->firstItem() + $key }}</td>
                            <td>{{ $result->user->name }}</td>
                            <td>{{ $result->user->nik }}</td>
                            <td>{{ ucfirst($result->user->division ?? '-') }}</td>
                            <td>
                                <span class="badge bg-{{ $result->score >= 70 ? 'success' : 'danger' }} fs-6">
                                    {{ $result->score }}
                                </span>
                            </td>
                            <td>{{ $result->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">Belum ada hasil evaluasi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">
            {{ $results->links() }}
        </div>
    </div>
</div>
@endsection


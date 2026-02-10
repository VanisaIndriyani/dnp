@extends('layouts.operator')

@section('title', 'Materi')

@section('content')
<div class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <h2 class="mb-0">Materi Pembelajaran</h2>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row">
    @forelse($materials as $material)
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            @if($material->type)
                                <span class="badge bg-info">{{ $material->type }}</span>
                            @endif
                        </div>
                        @can('super_admin')
                        <form action="{{ route('super_admin.materials.destroy', $material) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus materi ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm text-danger p-0" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                        @endcan
                    </div>
                    <h5 class="card-title">{{ $material->title }}</h5>
                    <p class="card-text text-muted small">
                        <i class="fas fa-clock me-1"></i> {{ $material->created_at->format('d M Y') }}
                    </p>
                </div>
                <div class="card-footer bg-white border-top-0">
                    <a href="{{ asset('storage/' . $material->file_path) }}" target="_blank" class="btn btn-outline-danger w-100">
                        <i class="fas fa-file-pdf me-2"></i>Download PDF
                    </a>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Belum ada materi yang diupload.</p>
                </div>
            </div>
        </div>
    @endforelse
</div>

<div class="mt-3">
    {{ $materials->links() }}
</div>
@endsection

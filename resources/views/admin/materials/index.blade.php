@extends('layouts.admin')

@section('title', 'Materi')

@section('content')

@if(isset($stats))
    {{-- DASHBOARD VIEW --}}
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="mb-2">Bank Materi Pembelajaran</h2>
            <p class="text-muted">Pilih bagian untuk melihat materi.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        @foreach($stats as $cat => $count)
        <div class="col-md-6 col-lg-3">
            <a href="{{ route('admin.materials.index', ['category' => $cat]) }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100 hover-card">
                    <div class="card-body text-center p-4">
                        <div class="avatar-lg mx-auto mb-3 bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class="fas fa-book-open fa-2x text-primary"></i>
                        </div>
                        <h4 class="card-title text-dark mb-1">{{ ucfirst($cat) }}</h4>
                        <p class="text-muted small mb-3">Lihat materi untuk bagian {{ ucfirst($cat) }}</p>
                        
                        <div class="d-flex justify-content-center gap-3">
                            <div class="text-center">
                                <h5 class="mb-0 fw-bold text-success">{{ $count }}</h5>
                                <small class="text-muted" style="font-size: 0.75rem;">Total File</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0 pb-3">
                        <span class="btn btn-outline-primary btn-sm w-100 rounded-pill">Lihat Materi</span>
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
    {{-- LIST VIEW --}}
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <a href="{{ route('admin.materials.index') }}" class="text-decoration-none text-muted mb-2 d-inline-block">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Bagian
                </a>
                <h2 class="mb-0">Materi: <span class="text-primary">{{ ucfirst($category) }}</span></h2>
            </div>
            {{-- Upload button removed as requested --}}
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
                            {{-- Delete button only for super_admin --}}
                            @can('super_admin')
                            <form action="{{ route('admin.materials.destroy', $material) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus materi ini?')">
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

@endif
@endsection

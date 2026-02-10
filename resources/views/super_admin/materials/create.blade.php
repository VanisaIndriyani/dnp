@extends('layouts.super_admin')

@section('title', 'Upload Materi')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h2 class="mb-0">Upload Materi Baru {{ isset($category) ? '- ' . ucfirst($category) : '' }}</h2>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('super_admin.materials.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label for="title" class="form-label">Judul Materi</label>
                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="category" class="form-label">Kategori</label>
                @if(isset($category) && $category != '')
                    <input type="hidden" name="category" value="{{ $category }}">
                    <input type="text" class="form-control" value="{{ ucfirst($category) }}" disabled readonly>
                @else
                    <select class="form-select @error('category') is-invalid @enderror" id="category" name="category" required>
                        <option value="">Pilih Kategori</option>
                        <option value="cover" {{ old('category') == 'cover' ? 'selected' : '' }}>Cover</option>
                        <option value="case" {{ old('category') == 'case' ? 'selected' : '' }}>Case</option>
                        <option value="inner" {{ old('category') == 'inner' ? 'selected' : '' }}>Inner</option>
                        <option value="endplate" {{ old('category') == 'endplate' ? 'selected' : '' }}>Endplate</option>
                    </select>
                    @error('category')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                @endif
            </div>

            <div class="mb-3">
                <label for="file" class="form-label">File PDF</label>
                <input type="file" class="form-control @error('file') is-invalid @enderror" id="file" name="file" accept=".pdf" required>
                <div class="form-text">Maksimal ukuran file 10MB. Format: PDF.</div>
                @error('file')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-end mt-4">
                @if(isset($category) && $category != '')
                    <a href="{{ route('super_admin.materials.index', ['category' => $category]) }}" class="btn btn-secondary me-2">Batal</a>
                @else
                    <a href="{{ route('super_admin.materials.index') }}" class="btn btn-secondary me-2">Batal</a>
                @endif
                <button type="submit" class="btn btn-danger" style="background-color: var(--primary-color);">Upload</button>
            </div>
        </form>
    </div>
</div>
@endsection


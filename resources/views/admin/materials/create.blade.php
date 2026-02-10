@extends('layouts.admin')

@section('title', 'Upload Materi')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h2 class="mb-0">Upload Materi Baru</h2>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.materials.store') }}" method="POST" enctype="multipart/form-data">
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
                <select class="form-select @error('category') is-invalid @enderror" id="category" name="category" required>
                    <option value="">Pilih Kategori</option>
                    <option value="General" {{ old('category') == 'General' ? 'selected' : '' }}>General</option>
                    <option value="Safety" {{ old('category') == 'Safety' ? 'selected' : '' }}>Safety</option>
                    <option value="Quality" {{ old('category') == 'Quality' ? 'selected' : '' }}>Quality</option>
                    <option value="Technical" {{ old('category') == 'Technical' ? 'selected' : '' }}>Technical</option>
                    <option value="SOP" {{ old('category') == 'SOP' ? 'selected' : '' }}>SOP</option>
                </select>
                @error('category')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
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
                <a href="{{ route('admin.materials.index') }}" class="btn btn-secondary me-2">Batal</a>
                <button type="submit" class="btn btn-danger" style="background-color: var(--primary-color);">Upload</button>
            </div>
        </form>
    </div>
</div>
@endsection
